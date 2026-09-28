<?php
/**
 * الواجهة الرئيسية: الشركات.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_brands = array_filter(
	maria_all_brands(),
	static function ( $b ) {
		return $b['count'] > 0;
	}
);
if ( ! $mr_brands ) {
	return;
}
?>
<section class="mr-section mr-brands" aria-labelledby="mr-brands-title">
	<div class="mr-container">
		<div class="mr-section__head mr-section__head--row">
			<div>
				<span class="mr-kicker">منتجات أصلية</span>
				<h2 class="mr-section__title" id="mr-brands-title">علامات يعرفها زبائنك ويطلبونها</h2>
			</div>
			<a class="mr-link" href="<?php echo esc_url( maria_page_url( 'brands' ) ); ?>">كل الشركات <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
		</div>
		<div class="mr-brands__grid mr-scroller">
			<?php foreach ( $mr_brands as $mr_slug => $mr_b ) : ?>
				<?php get_template_part( 'template-parts/brand-card', null, array( 'slug' => $mr_slug, 'brand' => $mr_b ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
