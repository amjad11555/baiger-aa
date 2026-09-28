<?php
/**
 * الصفحة الرئيسية.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="sh-main sh-home">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/brandbar' );
	get_template_part( 'template-parts/home/categories' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/products' );
	}
	get_template_part( 'template-parts/home/segments' );
	get_template_part( 'template-parts/home/process' );
	get_template_part( 'template-parts/home/why' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/offers' );
	}
	get_template_part( 'template-parts/home/export-band' );
	get_template_part( 'template-parts/home/sourcing' );
	shami_render_faqs( 'home', 'أسئلة تجار التجزئة قبل فتح الحساب' );
	get_template_part( 'template-parts/home/seo' );
	?>
</main>
<?php
get_footer();
