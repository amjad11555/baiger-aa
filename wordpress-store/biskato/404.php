<?php
/**
 * الصفحة غير موجودة.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="zd-main">
	<div class="zd-container zd-container--narrow">
		<div class="zd-empty zd-404">
			<div class="zd-404__num" aria-hidden="true">4<span><?php echo zad_logo_mark( 'zd-404__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>4</div>
			<h1>هذه الصفحة غير موجودة</h1>
			<p>ربما تغيّر الرابط أو لم يعد الصنف متوفراً. ابحث عن الصنف بالاسم، أو افتح قائمة أسعار الجملة.</p>
			<?php get_search_form(); ?>
			<div class="zd-empty__actions">
				<a class="zd-btn zd-btn--primary" href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a>
				<a class="zd-btn zd-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
