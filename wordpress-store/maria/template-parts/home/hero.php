<?php
/**
 * الواجهة الرئيسية: البطل + لوحة «اطلب فوراً» بالأصناف الأكثر طلباً.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_title  = (string) maria_opt( 'hero_title' );
$mr_parts  = preg_split( '/…|\.\.\./u', $mr_title, 2 );
$mr_total  = maria_products_total();
$mr_brandn = count(
	array_filter(
		maria_all_brands(),
		static function ( $b ) {
			return $b['count'] > 0;
		}
	)
);

$mr_quick = array();
if ( function_exists( 'wc_get_products' ) ) {
	$mr_quick = wc_get_products(
		array(
			'status'     => 'publish',
			'featured'   => true,
			'visibility' => 'catalog',
			'limit'      => 4,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
		)
	);
}
$mr_map = maria_cart_qty_cached();
?>
<section class="mr-hero" aria-labelledby="mr-hero-title">
	<div class="mr-container mr-hero__grid">
		<div class="mr-hero__content">
			<span class="mr-kicker"><?php maria_the_icon( 'store', '', 16 ); ?> <?php echo esc_html( maria_opt( 'hero_kicker' ) ); ?></span>
			<h1 class="mr-hero__title" id="mr-hero-title">
				<?php if ( count( $mr_parts ) > 1 ) : ?>
					<?php echo esc_html( trim( $mr_parts[0] ) ); ?> <span class="mr-hl"><?php echo esc_html( trim( $mr_parts[1] ) ); ?></span>
				<?php else : ?>
					<?php echo esc_html( $mr_title ); ?>
				<?php endif; ?>
			</h1>
			<p class="mr-hero__text"><?php echo esc_html( maria_opt( 'hero_text' ) ); ?></p>
			<div class="mr-hero__cta">
				<a class="mr-btn mr-btn--primary mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>"><?php maria_the_icon( 'list', '', 20 ); ?> ابدأ الطلب الآن</a>
				<a class="mr-btn mr-btn--ghost mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'brands' ) ); ?>">تسوّق حسب الشركة</a>
			</div>
			<dl class="mr-hero__stats">
				<div><dt>صنف جاهز للطلب</dt><dd><?php echo esc_html( $mr_total ? $mr_total : '130' ); ?>+</dd></div>
				<div><dt>شركات موثوقة</dt><dd><?php echo esc_html( max( 1, $mr_brandn ) ); ?></dd></div>
				<div><dt>أقل كمية للطلب</dt><dd>كرتونة</dd></div>
			</dl>
		</div>

		<?php if ( $mr_quick ) : ?>
		<aside class="mr-hero__panel" aria-labelledby="mr-quick-title">
			<div class="mr-hero__panel-head">
				<h2 id="mr-quick-title"><?php maria_the_icon( 'fire', '', 18 ); ?> الأكثر طلباً الآن</h2>
				<a href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>">كل الأصناف <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
			</div>
			<ul class="mr-mini-list">
				<?php foreach ( $mr_quick as $mr_p ) : ?>
					<?php $mr_info = maria_product_info( $mr_p->get_id() ); ?>
					<li class="mr-mini">
						<a class="mr-mini__art" href="<?php echo esc_url( $mr_p->get_permalink() ); ?>" tabindex="-1" aria-hidden="true"><?php echo $mr_p->get_image( 'woocommerce_gallery_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<div class="mr-mini__info">
							<a class="mr-mini__name" href="<?php echo esc_url( $mr_p->get_permalink() ); ?>"><?php echo esc_html( $mr_p->get_name() ); ?></a>
							<span class="mr-mini__meta"><?php echo esc_html( $mr_info['pack'] ); ?><?php if ( maria_show_prices() ) : ?> · <b><?php echo esc_html( maria_money_plain( wc_get_price_to_display( $mr_p ) ) ); ?></b><?php endif; ?></span>
						</div>
						<?php echo maria_cart_control( $mr_p, isset( $mr_map[ $mr_p->get_id() ] ) ? (int) $mr_map[ $mr_p->get_id() ] : 0, 'row' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<ul class="mr-hero__trust">
				<li><?php maria_the_icon( 'truck', '', 18 ); ?> توصيل لباب المحل</li>
				<li><?php maria_the_icon( 'wallet', '', 18 ); ?> الدفع عند الاستلام</li>
				<li><?php maria_the_icon( 'shield', '', 18 ); ?> منتجات أصلية</li>
			</ul>
		</aside>
		<?php endif; ?>
	</div>
</section>
