<?php
/**
 * الصفحة غير موجودة.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="sh-main">
	<div class="sh-container sh-container--narrow">
		<div class="sh-empty sh-404">
			<div class="sh-404__num" aria-hidden="true">4<span><?php echo shami_logo_mark( 'sh-404__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>4</div>
			<h1>هذه الصفحة غير موجودة</h1>
			<p>ربما تغيّر الرابط أو لم يعد الصنف متوفراً. ابحث عن الصنف بالاسم، أو افتح قائمة أسعار الجملة.</p>
			<?php get_search_form(); ?>
			<div class="sh-empty__actions">
				<a class="sh-btn sh-btn--primary" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a>
				<a class="sh-btn sh-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
