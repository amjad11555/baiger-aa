<?php
/**
 * الصفحة الرئيسية.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="zd-main zd-home">
	<?php
	// الترتيب من الأهم لتاجر يطلب اليوم: الأقسام، ثم العروض، ثم الأعلى دوراناً والجديد، ثم العلامات والتوصيل.
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/services' );
	zad_cached_part( 'template-parts/home/categories' );
	if ( class_exists( 'WooCommerce' ) ) {
		zad_cached_part( 'template-parts/home/offers' );
		zad_cached_part( 'template-parts/home/products' );
		zad_cached_part( 'template-parts/home/new-arrivals' );
	}
	zad_cached_part( 'template-parts/home/brandbar' );
	zad_cached_part( 'template-parts/home/delivery' );
	zad_cached_part( 'template-parts/home/process' );
	zad_render_faqs( 'home', 'أسئلة تجار التجزئة قبل فتح الحساب' );
	get_template_part( 'template-parts/home/seo' );
	?>
</main>
<?php
get_footer();
