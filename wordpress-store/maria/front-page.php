<?php
/**
 * الصفحة الرئيسية.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="mr-main mr-home">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/categories' );
	get_template_part( 'template-parts/home/brands' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/bestsellers' );
		get_template_part( 'template-parts/home/offers' );
	}
	get_template_part( 'template-parts/home/steps' );
	get_template_part( 'template-parts/home/split' );
	get_template_part( 'template-parts/home/why' );
	maria_render_faqs( 'home', 'أسئلة يطرحها أصحاب البقالات' );
	get_template_part( 'template-parts/home/seo' );
	?>
</main>
<?php
get_footer();
