<?php
/**
 * بطاقة المنتج بأسلوب Kalles.
 *
 * الصورة بخلفية فاتحة مع الشارات، وعند المرور: زر الحفظ، و«عرض سريع»، وزر «أضف إلى الطلبية»
 * الذي يتحول إلى عدّاد كراتين. تحت الصورة: العلامة، والاسم، والسعر، وسعر البيع وهامش الربح، وتعبئة الكرتونة.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Zad\WooCommerce
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$zd_cart_map = zad_cart_qty_cached();
$zd_info     = zad_product_info( $product->get_id() );
$zd_brands   = zad_all_brands();
$zd_brand    = isset( $zd_brands[ $zd_info['brand'] ] ) ? $zd_brands[ $zd_info['brand'] ] : null;
$zd_qty      = isset( $zd_cart_map[ $product->get_id() ] ) ? (int) $zd_cart_map[ $product->get_id() ] : 0;
$zd_heading  = ( ( is_shop() || is_product_taxonomy() || is_search() ) && ! wc_get_loop_prop( 'name' ) ) ? 'h2' : 'h3';
$zd_link     = get_permalink( $product->get_id() );
$zd_name     = $product->get_name();
$zd_desc     = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
$zd_desc     = wp_trim_words( wp_strip_all_tags( (string) $zd_desc ), 30, '…' );
?>
<li <?php wc_product_class( 'zd-card', $product ); ?> data-zd-desc="<?php echo esc_attr( $zd_desc ); ?>">
	<div class="zd-card__media">
		<a class="zd-card__img" href="<?php echo esc_url( $zd_link ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
		<?php echo zad_card_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="zd-card__tools">
			<button type="button" class="zd-card__tool" data-zd-wish="<?php echo (int) $product->get_id(); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( 'احفظ ' . $zd_name ); ?>"><?php zad_the_icon( 'heart', '', 18 ); ?></button>
			<button type="button" class="zd-card__tool zd-card__tool--qv" data-zd-qv aria-label="<?php echo esc_attr( 'عرض سريع: ' . $zd_name ); ?>"><?php zad_the_icon( 'eye', '', 18 ); ?></button>
		</div>
		<div class="zd-card__actions">
			<button type="button" class="zd-card__qv" data-zd-qv>عرض سريع</button>
			<?php echo zad_cart_control( $product, $zd_qty ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
	<div class="zd-card__body">
		<?php if ( $zd_brand ) : ?>
			<a class="zd-card__brand" href="<?php echo esc_url( $zd_brand['url'] ); ?>"><?php echo esc_html( $zd_brand['ar'] ); ?> <span lang="tr"><?php echo esc_html( $zd_brand['latin'] ); ?></span></a>
		<?php endif; ?>
		<<?php echo esc_attr( $zd_heading ); ?> class="zd-card__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $zd_link ); ?>"><?php echo esc_html( $zd_name ); ?></a></<?php echo esc_attr( $zd_heading ); ?>>
		<div class="zd-card__price">
			<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<?php echo wp_kses_post( zad_unit_price_html( $product ) ); ?>
		</div>
		<?php echo zad_profit_line_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- مبالغ wc_price وأرقام صحيحة فقط. ?>
		<?php if ( $zd_info['pack'] ) : ?>
			<p class="zd-card__spec">الكرتونة: <bdi><?php echo esc_html( $zd_info['pack'] ); ?></bdi></p>
		<?php endif; ?>
	</div>
</li>
