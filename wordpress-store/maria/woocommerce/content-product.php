<?php
/**
 * بطاقة المنتج في الشبكات (نسخة القالب).
 *
 * صورة بيضاء مع زري المفضلة والعرض السريع، ثم الشركة والاسم والسعر قبل/بعد،
 * وشارات الخصم، وزر «أضف إلى السلة» الذي يتحول إلى عدّاد كراتين.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Maria\WooCommerce
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$mr_cart_map = maria_cart_qty_cached();
$mr_info     = maria_product_info( $product->get_id() );
$mr_brands   = maria_all_brands();
$mr_brand    = isset( $mr_brands[ $mr_info['brand'] ] ) ? $mr_brands[ $mr_info['brand'] ] : null;
$mr_qty      = isset( $mr_cart_map[ $product->get_id() ] ) ? (int) $mr_cart_map[ $product->get_id() ] : 0;
$mr_heading  = ( ( is_shop() || is_product_taxonomy() || is_search() ) && ! wc_get_loop_prop( 'name' ) ) ? 'h2' : 'h3';
$mr_link     = get_permalink( $product->get_id() );
$mr_name     = $product->get_name();
?>
<li <?php wc_product_class( 'mr-card', $product ); ?>>
	<div class="mr-card__media">
		<a class="mr-card__img" href="<?php echo esc_url( $mr_link ); ?>" tabindex="-1" aria-hidden="true"><?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php echo maria_card_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="mr-card__acts">
			<button type="button" class="mr-card__act mr-fav" data-mr-fav="<?php echo (int) $product->get_id(); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( 'أضف ' . $mr_name . ' إلى المفضلة' ); ?>"><?php maria_the_icon( 'heart', '', 22 ); ?></button>
			<a class="mr-card__act" href="<?php echo esc_url( $mr_link ); ?>" aria-label="<?php echo esc_attr( 'تفاصيل ' . $mr_name ); ?>"><?php maria_the_icon( 'eye', '', 22 ); ?></a>
		</div>
	</div>
	<div class="mr-card__body">
		<?php if ( $mr_brand ) : ?>
			<a class="mr-card__brand" href="<?php echo esc_url( $mr_brand['url'] ); ?>"><?php echo esc_html( $mr_brand['ar'] ); ?></a>
		<?php endif; ?>
		<<?php echo esc_attr( $mr_heading ); ?> class="mr-card__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $mr_link ); ?>"><?php echo esc_html( $mr_name ); ?></a></<?php echo esc_attr( $mr_heading ); ?>>
		<?php if ( $mr_info['pack'] ) : ?>
			<span class="mr-card__pack"><?php echo esc_html( $mr_info['pack'] ); ?></span>
		<?php endif; ?>
		<div class="mr-card__price">
			<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<?php echo wp_kses_post( maria_unit_price_html( $product ) ); ?>
		</div>
		<?php echo maria_card_tags( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo maria_cart_control( $product, $mr_qty ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
</li>
