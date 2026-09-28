<?php
/**
 * الواجهة الرئيسية: الواجهة الأولى بصورة المستودع وعرض القيمة وأرقام مختصرة.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_total   = shami_products_total();
$sh_rounded = $sh_total >= 20 ? ( floor( $sh_total / 10 ) * 10 ) . '+' : (string) $sh_total;
$sh_brands = count( array_filter( wp_list_pluck( shami_all_brands(), 'count' ) ) );
$sh_custom = (string) shami_opt( 'hero_image' );
?>
<section class="sh-hero" aria-labelledby="sh-hero-title">
	<div class="sh-hero__media">
		<?php if ( $sh_custom ) : ?>
			<img src="<?php echo esc_url( $sh_custom ); ?>" alt="" fetchpriority="high" decoding="async">
		<?php else : ?>
			<picture>
				<source media="(max-width: 767px)" srcset="<?php echo esc_url( shami_img_url( 'hero-m', true ) ); ?> 550w, <?php echo esc_url( shami_img_url( 'hero-m' ) ); ?> 1100w" sizes="100vw">
				<?php
				echo shami_img( // phpcs:ignore WordPress.Security.EscapeOutput
					'hero',
					'',
					array(
						'sizes'         => '100vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			</picture>
		<?php endif; ?>
	</div>
	<div class="sh-container sh-hero__inner">
		<div class="sh-hero__content">
			<p class="sh-eyebrow sh-eyebrow--light"><?php echo esc_html( shami_opt( 'hero_kicker' ) ); ?></p>
			<h1 class="sh-hero__title" id="sh-hero-title"><?php echo esc_html( shami_opt( 'hero_title' ) ); ?></h1>
			<p class="sh-hero__text"><?php echo esc_html( shami_opt( 'hero_text' ) ); ?></p>
			<div class="sh-hero__actions">
				<a class="sh-btn sh-btn--accent sh-btn--lg" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">تصفّح قائمة الأسعار</a>
				<?php if ( shami_wa_number() ) : ?>
					<a class="sh-btn sh-btn--outline-light sh-btn--lg" href="<?php echo esc_url( shami_wa_link( 'مرحباً، أرغب بفتح حساب جملة لدى ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener">افتح حساب جملة</a>
				<?php else : ?>
					<a class="sh-btn sh-btn--outline-light sh-btn--lg" href="<?php echo esc_url( shami_page_url( 'contact' ) ); ?>">افتح حساب جملة</a>
				<?php endif; ?>
			</div>
		</div>
		<dl class="sh-hero__stats">
			<div><dt>صنف جاهز للتوريد</dt><dd><bdi><?php echo esc_html( $sh_rounded ); ?></bdi></dd></div>
			<div><dt>علامات تركية رائدة</dt><dd><?php echo esc_html( max( 1, $sh_brands ) ); ?></dd></div>
			<div><dt>للتوريد داخل إسطنبول</dt><dd><bdi>24–48</bdi> <small>ساعة</small></dd></div>
			<div><dt>حجم الطلبية</dt><dd><small>من</small> كرتونة <small>إلى</small> حاوية</dd></div>
		</dl>
	</div>
</section>
