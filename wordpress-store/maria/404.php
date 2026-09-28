<?php
/**
 * الصفحة غير موجودة.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="mr-main">
	<div class="mr-container mr-container--narrow">
		<div class="mr-empty mr-404">
			<div class="mr-404__num" aria-hidden="true">4<span><?php echo maria_logo_mark( 'mr-404__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>4</div>
			<h1>الصفحة غير موجودة</h1>
			<p>ربما تغيّر الرابط أو نفدت الكمية. ابحث عن المنتج أو الشركة التي تريدها، أو ابدأ من قائمة الطلب السريع.</p>
			<?php get_search_form(); ?>
			<div class="mr-empty__actions">
				<a class="mr-btn mr-btn--primary" href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>">الطلب السريع</a>
				<a class="mr-btn mr-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
