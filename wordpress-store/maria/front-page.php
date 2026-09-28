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
	get_template_part( 'template-parts/home/banners' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/offers' );
	}
	get_template_part( 'template-parts/home/categories' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/picks' );
	}
	get_template_part( 'template-parts/home/split' );
	if ( class_exists( 'WooCommerce' ) ) {
		get_template_part( 'template-parts/home/bestsellers' );
	}
	get_template_part( 'template-parts/home/why' );
	get_template_part( 'template-parts/home/share' );
	maria_render_faqs( 'home', 'أسئلة يطرحها أصحاب البقالات قبل الطلب' );
	get_template_part( 'template-parts/home/seo' );
	?>
</main>
<?php
get_footer();
