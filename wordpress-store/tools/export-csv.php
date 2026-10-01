<?php
/**
 * يولّد ملف CSV متوافق مع مستورد منتجات ووكومرس من كتالوج القالب.
 *
 * الاستخدام (داخل موقع ووردبريس فيه قالب بسكاتو مفعّل):
 *   wp eval-file wordpress-store/tools/export-csv.php wordpress-store/products-ar.csv
 *
 * بعد تعديل الأسعار في Excel / Google Sheets:
 *   المنتجات ← استيراد ← اختر الملف ← فعّل «تحديث المنتجات الموجودة» (المطابقة بالـ SKU).
 *
 * @package Zad
 */

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'zad_seed_description' ) ) {
	fwrite( STDERR, "شغّل الملف عبر wp eval-file مع تفعيل قالب بسكاتو.\n" );
	return;
}

$out  = isset( $args[0] ) ? $args[0] : 'products-ar.csv';
$rows = zad_catalog_rows();
$cats = zad_categories();

$fh = fopen( $out, 'w' );
fwrite( $fh, "\xEF\xBB\xBF" ); // BOM ليعرض Excel الحروف العربية بشكل صحيح.

$header = array( 'Type', 'SKU', 'Name', 'Published', 'Is featured?', 'Visibility in catalog', 'Short description', 'Description', 'Sale price', 'Regular price', 'Categories', 'In stock?', 'Brands', 'Meta: _zad_brand', 'Meta: _zad_line', 'Meta: _zad_tr', 'Meta: _zad_flavor', 'Meta: _zad_pack', 'Meta: _zad_units', 'Meta: _zad_unit' );
fputcsv( $fh, $header, ',', '"', '\\' );

foreach ( $rows as $r ) {
	// سعر العرض = خصم العلامة الدائم من الإعدادات (إيتي وأولكر).
	$sale       = zad_discounted_price( $r['price'], $r['brand'] );
	$categories = array( isset( $cats[ $r['cat'] ] ) ? $cats[ $r['cat'] ]['name'] : $r['cat'] );
	if ( '' !== $sale ) {
		$categories[] = $cats['offers']['name'];
	}
	$brand = isset( zad_brands()[ $r['brand'] ] ) ? zad_brands()[ $r['brand'] ]['ar'] : '';

	fputcsv(
		$fh,
		array(
			'simple',
			$r['sku'],
			$r['name'],
			1,
			$r['best'] ? 1 : 0,
			'visible',
			'<p>' . $r['desc'] . '.' . ( $r['pack'] ? ' التعبئة: ' . $r['pack'] . '.' : '' ) . '</p>',
			zad_seed_description( $r ),
			$sale,
			$r['price'],
			implode( ', ', $categories ),
			1,
			$brand,
			$r['brand'],
			$r['line'],
			$r['tr'],
			$r['flavor'],
			$r['pack'],
			(int) $r['units'],
			$r['unit'] ? $r['unit'] : 'علبة',
		),
		',',
		'"',
		'\\'
	);
}
fclose( $fh );

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::success( sprintf( 'تم إنشاء %s (%d منتجاً).', $out, count( $rows ) ) );
}
