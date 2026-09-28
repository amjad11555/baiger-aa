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
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/services' );
	get_template_part( 'template-parts/home/categories' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/products' );
	}
	get_template_part( 'template-parts/home/banners' );
	get_template_part( 'template-parts/home/segments' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/offers' );
	}
	get_template_part( 'template-parts/home/process' );
	get_template_part( 'template-parts/home/brandbar' );
	zad_render_faqs( 'home', 'أسئلة تجار التجزئة قبل فتح الحساب' );
	get_template_part( 'template-parts/home/seo' );
	?>
</main>
<?php
get_footer();
