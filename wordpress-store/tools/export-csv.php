<?php
/**
 * يولّد ملف CSV متوافق مع مستورد منتجات ووكومرس من كتالوج القالب.
 *
 * الاستخدام (داخل موقع ووردبريس فيه قالب لذّة مفعّل):
 *   wp eval-file wordpress-store/tools/export-csv.php wordpress-store/products-ar.csv
 *
 * بعد تعديل الأسعار في Excel / Google Sheets:
 *   المنتجات ← استيراد ← اختر الملف ← فعّل «تحديث المنتجات الموجودة» (المطابقة بالـ SKU).
 *
 * @package Lazza
 */

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'lazza_seed_description' ) ) {
	fwrite( STDERR, "شغّل الملف عبر wp eval-file مع تفعيل قالب لذّة.\n" );
	return;
}

$out  = isset( $args[0] ) ? $args[0] : 'products-ar.csv';
$rows = include LAZZA_DIR . '/inc/data/catalog.php';
$cats = lazza_categories();

$fh = fopen( $out, 'w' );
fwrite( $fh, "\xEF\xBB\xBF" ); // BOM ليعرض Excel الحروف العربية بشكل صحيح.

$header = array( 'Type', 'SKU', 'Name', 'Published', 'Is featured?', 'Visibility in catalog', 'Short description', 'Description', 'Sale price', 'Regular price', 'Categories', 'In stock?', 'Meta: _lazza_brand', 'Meta: _lazza_line', 'Meta: _lazza_tr', 'Meta: _lazza_flavor', 'Meta: _lazza_pack', 'Meta: _lazza_units', 'Meta: _lazza_bundle' );
fputcsv( $fh, $header );

foreach ( $rows as $row ) {
	$r = array_combine( array( 'sku', 'brand', 'cat', 'line', 'name', 'tr', 'flavor', 'pack', 'units', 'price', 'sale', 'best', 'desc' ), $row );

	$categories = array( $cats[ $r['cat'] ]['name'] );
	if ( ( $r['sale'] || 'offers' === $r['cat'] ) && 'offers' !== $r['cat'] ) {
		$categories[] = $cats['offers']['name'];
	}

	fputcsv(
		$fh,
		array(
			'simple',
			$r['sku'],
			$r['name'],
			1,
			$r['best'] ? 1 : 0,
			'visible',
			'<p>' . $r['desc'] . '. التعبئة: ' . $r['pack'] . '. متوفر بالجملة للبقالات بسعر الكرتونة مع توصيل سريع.</p>',
			lazza_seed_description( $r ),
			$r['sale'] ? $r['sale'] : '',
			$r['price'],
			implode( ', ', $categories ),
			1,
			$r['brand'],
			$r['line'],
			$r['tr'],
			$r['flavor'],
			$r['pack'],
			(int) $r['units'],
			'offers' === $r['cat'] ? 1 : '',
		)
	);
}
fclose( $fh );

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::success( sprintf( 'تم إنشاء %s (%d منتجاً).', $out, count( $rows ) ) );
}
