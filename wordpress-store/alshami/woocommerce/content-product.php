<?php
/**
 * بطاقة المنتج في الشبكات (نسخة القالب).
 *
 * صورة العبوة مع شارات مختصرة، ثم العلامة والاسم ومواصفات الكرتونة،
 * وسعر الكرتونة وسعر القطعة، وزر «أضف إلى الطلبية» الذي يتحول إلى عدّاد كراتين.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package AlShami\WooCommerce
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$sh_cart_map = shami_cart_qty_cached();
$sh_info     = shami_product_info( $product->get_id() );
$sh_brands   = shami_all_brands();
$sh_brand    = isset( $sh_brands[ $sh_info['brand'] ] ) ? $sh_brands[ $sh_info['brand'] ] : null;
$sh_qty      = isset( $sh_cart_map[ $product->get_id() ] ) ? (int) $sh_cart_map[ $product->get_id() ] : 0;
$sh_heading  = ( ( is_shop() || is_product_taxonomy() || is_search() ) && ! wc_get_loop_prop( 'name' ) ) ? 'h2' : 'h3';
$sh_link     = get_permalink( $product->get_id() );
?>
<li <?php wc_product_class( 'sh-card', $product ); ?>>
	<a class="sh-card__media" href="<?php echo esc_url( $sh_link ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo shami_card_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
	<div class="sh-card__body">
		<?php if ( $sh_brand ) : ?>
			<a class="sh-card__brand" href="<?php echo esc_url( $sh_brand['url'] ); ?>"><?php echo esc_html( $sh_brand['ar'] ); ?> <span lang="tr"><?php echo esc_html( $sh_brand['latin'] ); ?></span></a>
		<?php endif; ?>
		<<?php echo esc_attr( $sh_heading ); ?> class="sh-card__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $sh_link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></<?php echo esc_attr( $sh_heading ); ?>>
		<?php if ( $sh_info['pack'] ) : ?>
			<p class="sh-card__spec">الكرتونة: <bdi><?php echo esc_html( $sh_info['pack'] ); ?></bdi></p>
		<?php endif; ?>
		<div class="sh-card__foot">
			<div class="sh-card__price">
				<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				<?php echo wp_kses_post( shami_unit_price_html( $product ) ); ?>
			</div>
			<?php echo shami_cart_control( $product, $sh_qty ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</li>
