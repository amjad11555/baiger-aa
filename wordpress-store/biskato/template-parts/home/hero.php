<?php
/**
 * الواجهة الرئيسية: قسم افتتاحي واحد ثابت (بلا شرائح متحركة، أسرع وأوضح).
 *
 * النص والأزرار في جهة، والصورة في الجهة الأخرى مع شارات خصم إيتي وأولكر.
 * الأزرار تتبدل حسب حالة الزائر: زائر جديد ← افتح حساباً، زبون مسجّل ← قائمة الأسعار والعروض.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_custom = (string) zad_opt( 'hero_image' );
$zd_gate   = zad_price_gate();
$zd_count  = (int) wp_count_posts( 'product' )->publish;
$zd_count  = $zd_count >= 20 ? (int) floor( $zd_count / 10 ) * 10 : $zd_count;
$zd_acc    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
$zd_shop   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

if ( 'login' === $zd_gate ) {
	$zd_primary = array( 'افتح حساب جملة مجاناً', add_query_arg( 'tab', 'register', $zd_acc ) );
	$zd_second  = array( 'تصفّح الأصناف', $zd_shop );
} else {
	$zd_primary = array( 'قائمة أسعار الجملة', zad_page_url( 'quick_order' ) );
	$zd_second  = array( 'عروض إيتي وأولكر', zad_cat_url( 'offers' ) );
}
$zd_badges = array();
foreach ( array( 'eti', 'ulker' ) as $zd_b ) {
	$zd_d = function_exists( 'zad_brand_discount' ) ? zad_brand_discount( $zd_b ) : 0;
	if ( $zd_d > 0 ) {
		$zd_all      = zad_brands();
		$zd_badges[] = array( $zd_all[ $zd_b ]['ar'], ( 0 + $zd_d ) . '%' );
	}
}
?>
<section class="zd-hero5" aria-labelledby="zd-hero-title">
	<div class="zd-container zd-hero5__grid">
		<div class="zd-hero5__copy">
			<p class="zd-hero5__kicker"><span class="zd-hero5__dot" aria-hidden="true"></span><?php echo esc_html( zad_opt( 'hero_kicker' ) ); ?></p>
			<h1 class="zd-hero5__title" id="zd-hero-title"><?php echo esc_html( zad_opt( 'hero_title' ) ); ?></h1>
			<p class="zd-hero5__text"><?php echo esc_html( zad_opt( 'hero_text' ) ); ?></p>
			<div class="zd-hero5__actions">
				<a class="zd-btn zd-btn--dark zd-btn--lg" href="<?php echo esc_url( $zd_primary[1] ); ?>"><?php echo esc_html( $zd_primary[0] ); ?></a>
				<a class="zd-btn zd-btn--outline zd-btn--lg" href="<?php echo esc_url( $zd_second[1] ); ?>"><?php echo esc_html( $zd_second[0] ); ?></a>
			</div>
			<ul class="zd-hero5__trust">
				<li><?php zad_the_icon( 'box', '', 20 ); ?><span><b><?php echo esc_html( '+' . $zd_count ); ?></b> صنفاً بالجملة</span></li>
				<li><?php zad_the_icon( 'truck', '', 20 ); ?><span>توصيل مجاني <b>داخل إسطنبول</b></span></li>
				<li><?php zad_the_icon( 'wallet', '', 20 ); ?><span>نقداً عند الاستلام <b>في إسطنبول</b></span></li>
			</ul>
		</div>
		<div class="zd-hero5__media">
			<?php if ( $zd_custom ) : ?>
				<img src="<?php echo esc_url( $zd_custom ); ?>" alt="" width="1600" height="1200" fetchpriority="high" decoding="async">
			<?php else : ?>
				<?php
				echo zad_img( // phpcs:ignore WordPress.Security.EscapeOutput
					'hero-v5',
					'كراتين جملة من الكيك والبسكويت والشيبس والسكاكر',
					array(
						'sizes'         => '(min-width: 1024px) 50vw, 100vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			<?php endif; ?>
			<?php foreach ( $zd_badges as $zd_i => $zd_bd ) : ?>
				<span class="zd-hero5__badge zd-hero5__badge--<?php echo (int) $zd_i; ?>"><small>خصم دائم</small><b><?php echo esc_html( $zd_bd[0] ); ?> <span><?php echo esc_html( $zd_bd[1] ); ?></span></b></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>
