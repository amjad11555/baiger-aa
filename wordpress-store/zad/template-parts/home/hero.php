<?php
/**
 * الواجهة الرئيسية: عارض شرائح بعرض الشاشة بأسلوب Kalles (ثلاث شرائح بتلاشٍ ناعم ونقاط وأسهم).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_custom = (string) zad_opt( 'hero_image' );
$zd_slides = array(
	array(
		'image'   => 'hero',
		'mobile'  => 'hero-m',
		'kicker'  => zad_opt( 'hero_kicker' ),
		'title'   => zad_opt( 'hero_title' ),
		'text'    => zad_opt( 'hero_text' ),
		'primary' => array( 'تصفّح قائمة الأسعار', zad_page_url( 'quick_order' ) ),
		'second'  => array( 'افتح حساب جملة', zad_wa_number() ? zad_wa_link( 'مرحباً، أرغب بفتح حساب جملة لدى ' . get_bloginfo( 'name' ) ) : zad_page_url( 'contact' ) ),
	),
	array(
		'image'   => 'seg-export',
		'mobile'  => '',
		'kicker'  => 'للمستوردين خارج تركيا',
		'title'   => 'حاوية كاملة من أشهر العلامات التركية',
		'text'    => 'طبليات مختلطة أو حاويات 20 و40 قدماً إلى أسواقك، مع شهادات المنشأ والحلال ومستندات التخليص.',
		'primary' => array( 'اطلب عرض سعر للتصدير', zad_page_url( 'export' ) ),
		'second'  => array(),
	),
	array(
		'image'   => 'flatlay',
		'mobile'  => '',
		'kicker'  => 'عروض الجملة',
		'title'   => 'أسعار خاصة على كميات محدودة',
		'text'    => 'خصومات على سعر الكرتونة لأصناف مختارة، تتجدد أسبوعياً حتى نفاد الكمية.',
		'primary' => array( 'تسوّق العروض', zad_cat_url( 'offers' ) ),
		'second'  => array(),
	),
);
?>
<section class="zd-hero" aria-roledescription="carousel" aria-label="عروض <?php bloginfo( 'name' ); ?>" data-zd-slider data-autoplay="6500">
	<div class="zd-hero__track">
		<?php foreach ( $zd_slides as $zd_i => $zd_s ) : ?>
			<div class="zd-slide<?php echo 0 === $zd_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $zd_i + 1 ) . ' من ' . count( $zd_slides ) ); ?>"<?php echo 0 === $zd_i ? '' : ' aria-hidden="true"'; ?>>
				<div class="zd-slide__media">
					<?php if ( 0 === $zd_i && $zd_custom ) : ?>
						<img src="<?php echo esc_url( $zd_custom ); ?>" alt="" fetchpriority="high" decoding="async">
					<?php else : ?>
						<picture>
							<?php if ( $zd_s['mobile'] ) : ?>
								<source media="(max-width: 767px)" srcset="<?php echo esc_url( zad_img_url( $zd_s['mobile'], true ) ); ?> 550w, <?php echo esc_url( zad_img_url( $zd_s['mobile'] ) ); ?> 1100w" sizes="100vw">
							<?php endif; ?>
							<?php
							echo zad_img( // phpcs:ignore WordPress.Security.EscapeOutput
								$zd_s['image'],
								'',
								array(
									'sizes'         => '100vw',
									'loading'       => 0 === $zd_i ? 'eager' : 'lazy',
									'fetchpriority' => 0 === $zd_i ? 'high' : '',
								)
							);
							?>
						</picture>
					<?php endif; ?>
				</div>
				<div class="zd-container zd-slide__inner">
					<div class="zd-slide__content">
						<p class="zd-slide__kicker"><?php echo esc_html( $zd_s['kicker'] ); ?></p>
						<?php if ( 0 === $zd_i ) : ?>
							<h1 class="zd-slide__title"><?php echo esc_html( $zd_s['title'] ); ?></h1>
						<?php else : ?>
							<h2 class="zd-slide__title"><?php echo esc_html( $zd_s['title'] ); ?></h2>
						<?php endif; ?>
						<p class="zd-slide__text"><?php echo esc_html( $zd_s['text'] ); ?></p>
						<div class="zd-slide__actions">
							<a class="zd-btn zd-btn--dark zd-btn--lg" href="<?php echo esc_url( $zd_s['primary'][1] ); ?>"<?php echo 0 === $zd_i ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $zd_s['primary'][0] ); ?></a>
							<?php if ( $zd_s['second'] ) : ?>
								<a class="zd-btn zd-btn--outline zd-btn--lg" href="<?php echo esc_url( $zd_s['second'][1] ); ?>"<?php echo 0 === strpos( $zd_s['second'][1], 'https://wa.me' ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $zd_s['second'][0] ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<button type="button" class="zd-hero__arrow zd-hero__arrow--prev" data-zd-slide="prev" aria-label="الشريحة السابقة"></button>
	<button type="button" class="zd-hero__arrow zd-hero__arrow--next" data-zd-slide="next" aria-label="الشريحة التالية"></button>
	<div class="zd-hero__dots" role="tablist" aria-label="اختر شريحة">
		<?php foreach ( $zd_slides as $zd_i => $zd_s ) : ?>
			<button type="button" class="zd-hero__dot<?php echo 0 === $zd_i ? ' is-active' : ''; ?>" data-zd-slide="<?php echo (int) $zd_i; ?>" aria-label="<?php echo esc_attr( 'الشريحة ' . ( $zd_i + 1 ) ); ?>"<?php echo 0 === $zd_i ? ' aria-current="true"' : ''; ?>></button>
		<?php endforeach; ?>
	</div>
</section>
