<?php
/**
 * الصفحة غير موجودة.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="lz-main">
	<div class="lz-container lz-container--narrow">
		<div class="lz-empty lz-404">
			<div class="lz-404__num" aria-hidden="true">4<span><?php echo lazza_sun_svg( 12 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>4</div>
			<h1>الصفحة غير موجودة</h1>
			<p>ربما تغيّر الرابط أو نفدت الكمية. ابحث عن المنتج الذي تريده أو ابدأ من الطلب السريع.</p>
			<?php get_search_form(); ?>
			<div class="lz-empty__actions">
				<a class="lz-btn lz-btn--primary" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>">الطلب السريع</a>
				<a class="lz-btn lz-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
