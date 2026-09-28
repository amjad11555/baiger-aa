<?php
/**
 * بطاقة المنتج في الشبكات (نسخة القالب).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Lazza\WooCommerce
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$lz_cart_map = lazza_cart_qty_cached();
$lz_info    = lazza_product_info( $product->get_id() );
$lz_brands  = lazza_brands();
$lz_brand   = isset( $lz_brands[ $lz_info['brand'] ] ) ? $lz_brands[ $lz_info['brand'] ] : null;
$lz_qty     = isset( $lz_cart_map[ $product->get_id() ] ) ? (int) $lz_cart_map[ $product->get_id() ] : 0;
$lz_heading = ( ( is_shop() || is_product_taxonomy() || is_search() ) && ! wc_get_loop_prop( 'name' ) ) ? 'h2' : 'h3';
$lz_link    = get_permalink( $product->get_id() );
?>
<li <?php wc_product_class( 'lz-card', $product ); ?>>
	<a class="lz-card__media" href="<?php echo esc_url( $lz_link ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo lazza_card_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
	<div class="lz-card__body">
		<div class="lz-card__meta">
			<?php if ( $lz_brand ) : ?>
				<span class="lz-chip lz-chip--brand lz-chip--xs" style="--c:<?php echo esc_attr( $lz_brand['c1'] ); ?>"><?php echo esc_html( $lz_brand['ar'] ); ?></span>
			<?php endif; ?>
			<?php if ( $lz_info['pack'] ) : ?>
				<span class="lz-card__pack"><?php lazza_the_icon( 'box', '', 14 ); ?> <?php echo esc_html( $lz_info['pack'] ); ?></span>
			<?php endif; ?>
		</div>
		<<?php echo esc_attr( $lz_heading ); ?> class="lz-card__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $lz_link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></<?php echo esc_attr( $lz_heading ); ?>>
		<div class="lz-card__foot">
			<div class="lz-card__price">
				<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				<?php echo wp_kses_post( lazza_unit_price_html( $product ) ); ?>
			</div>
			<?php echo lazza_cart_control( $product, $lz_qty ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</li>
