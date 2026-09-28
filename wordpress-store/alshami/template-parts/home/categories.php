<?php
/**
 * الواجهة الرئيسية: أقسام التشكيلة ببطاقات صور.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sh-section sh-cats" aria-labelledby="sh-cats-title">
	<div class="sh-container">
		<div class="sh-head">
			<div class="sh-head__text">
				<p class="sh-eyebrow">التشكيلة</p>
				<h2 class="sh-head__title" id="sh-cats-title">كل ما يحتاجه رف الحلويات، من مورّد واحد</h2>
				<p class="sh-head__sub">خمسة أقسام تغطي طلب زبائنك اليومي، بأسعار الكرتونة وتوفر مستمر.</p>
			</div>
			<a class="sh-link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">كل الأصناف</a>
		</div>
		<ul class="sh-cats__grid sh-scroller">
			<?php foreach ( shami_categories() as $sh_slug => $sh_cat ) : ?>
				<li>
					<a class="sh-cat" href="<?php echo esc_url( shami_cat_url( $sh_slug ) ); ?>">
						<span class="sh-cat__media"><?php echo shami_img( $sh_cat['image'], '', array( 'sizes' => '(min-width: 1200px) 240px, (min-width: 768px) 30vw, 60vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="sh-cat__body">
							<span class="sh-cat__count"><?php echo esc_html( shami_n_items( shami_cat_count( $sh_slug ) ) ); ?></span>
							<span class="sh-cat__name"><?php echo esc_html( $sh_cat['title'] ); ?></span>
							<span class="sh-cat__line"><?php echo esc_html( $sh_cat['line'] ); ?></span>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
