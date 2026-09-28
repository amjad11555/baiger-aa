<?php
/**
 * Template Name: الشركات والعلامات التجارية
 *
 * دليل كل الشركات مع عدد المنتجات وأقسام كل شركة.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();
$mr_brands = array_filter(
	maria_all_brands(),
	static function ( $b ) {
		return $b['count'] > 0;
	}
);
?>
<main id="main" class="mr-main mr-brands-page">
	<section class="mr-page-hero">
		<div class="mr-container">
			<?php maria_breadcrumbs(); ?>
			<span class="mr-kicker"><?php maria_the_icon( 'store', '', 16 ); ?> <?php echo esc_html( count( $mr_brands ) ); ?> شركات · <?php echo esc_html( maria_n_items( maria_products_total() ) ); ?></span>
			<h1 class="mr-page-hero__title"><?php the_title(); ?></h1>
			<div class="mr-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>
	<div class="mr-container">
		<div class="mr-brands__grid mr-brands__grid--page">
			<?php foreach ( $mr_brands as $mr_slug => $mr_b ) : ?>
				<?php get_template_part( 'template-parts/brand-card', null, array( 'slug' => $mr_slug, 'brand' => $mr_b ) ); ?>
			<?php endforeach; ?>
			<article class="mr-brand-card mr-brand-card--more">
				<a class="mr-brand-card__link" href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">
					<span class="mr-brand-card__logo"><?php maria_the_icon( 'plus', '', 28 ); ?></span>
					<span class="mr-brand-card__name">شركة أخرى؟</span>
				</a>
				<p class="mr-brand-card__about">تحتاج منتجات من شركة غير موجودة هنا؟ أرسل لنا اسمها ونؤمّن منتجاتها لبقالتك بسعر الجملة.</p>
				<a class="mr-link" href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلب الآن <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
			</article>
		</div>
	</div>
</main>
<?php
get_footer();
