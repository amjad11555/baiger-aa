<?php
/**
 * الواجهة الرئيسية: البطل.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_title = (string) lazza_opt( 'hero_title' );
$lz_parts = preg_split( '/…|\.\.\./u', $lz_title, 2 );
$lz_total = lazza_products_total();

$lz_float = array();
if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
	foreach ( array( 'ETI-BRW-INT', 'ULK-CRZ-SWT', 'ULK-BSK-COC', 'BON-DUB-CHO' ) as $lz_sku ) {
		$lz_id = wc_get_product_id_by_sku( $lz_sku );
		if ( $lz_id ) {
			$lz_float[] = wc_get_product( $lz_id );
		}
	}
	if ( count( $lz_float ) < 4 ) {
		$lz_float = wc_get_products(
			array(
				'status'   => 'publish',
				'featured' => true,
				'limit'    => 4,
			)
		);
	}
}
?>
<section class="lz-hero" aria-labelledby="lz-hero-title">
	<div class="lz-hero__bg" aria-hidden="true">
		<span class="lz-hero__rays"><?php echo lazza_sun_svg( 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lz-hero__dots"></span>
	</div>
	<div class="lz-container lz-hero__grid">
		<div class="lz-hero__content">
			<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'store', '', 18 ); ?> <?php echo esc_html( lazza_opt( 'hero_kicker' ) ); ?></span>
			<h1 class="lz-hero__title" id="lz-hero-title">
				<?php if ( count( $lz_parts ) > 1 ) : ?>
					<?php echo esc_html( trim( $lz_parts[0] ) ); ?> <span class="lz-hl"><?php echo esc_html( trim( $lz_parts[1] ) ); ?></span>
				<?php else : ?>
					<?php echo esc_html( $lz_title ); ?>
				<?php endif; ?>
			</h1>
			<p class="lz-hero__text"><?php echo esc_html( lazza_opt( 'hero_text' ) ); ?></p>
			<div class="lz-hero__cta">
				<a class="lz-btn lz-btn--gold lz-btn--lg" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"><?php lazza_the_icon( 'bolt', '', 20 ); ?> ابدأ الطلب السريع</a>
				<a class="lz-btn lz-btn--glass lz-btn--lg" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">تصفّح كل المنتجات <?php lazza_the_icon( 'arrow-left', '', 18 ); ?></a>
			</div>
			<ul class="lz-hero__trust">
				<li><?php lazza_the_icon( 'truck', '', 20 ); ?> توصيل سريع لمحلّك</li>
				<li><?php lazza_the_icon( 'wallet', '', 20 ); ?> الدفع عند الاستلام</li>
				<li><?php lazza_the_icon( 'box', '', 20 ); ?> من كرتونة واحدة</li>
			</ul>
		</div>
		<div class="lz-hero__visual" aria-hidden="true">
			<div class="lz-hero__plate"></div>
			<?php foreach ( array_slice( $lz_float, 0, 4 ) as $lz_i => $lz_p ) : ?>
				<div class="lz-float lz-float--<?php echo (int) $lz_i + 1; ?>"><?php echo $lz_p->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endforeach; ?>
			<div class="lz-hero__badge">
				<strong><?php echo esc_html( $lz_total ? '+' . $lz_total : '+130' ); ?></strong>
				<span>صنف من إيتي وأولكر وبونوتشي</span>
			</div>
		</div>
	</div>
	<svg class="lz-wave" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M0 40c120 30 240 45 360 38S600 30 720 22s240-10 360 6 240 40 360 34V90H0z"/></svg>
</section>
