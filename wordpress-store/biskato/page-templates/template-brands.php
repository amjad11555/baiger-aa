<?php
/**
 * Template Name: العلامات التجارية
 *
 * دليل العلامات في التشكيلة مع عدد الأصناف وأقسام كل علامة.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();
$zd_brands = array_filter(
	zad_all_brands(),
	static function ( $b ) {
		return $b['count'] > 0;
	}
);
?>
<main id="main" class="zd-main zd-brands-page">
	<section class="zd-page-hero">
		<div class="zd-page-hero__media"><?php echo zad_img( 'seg-supermarket', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="zd-container zd-page-hero__inner">
			<?php zad_breadcrumbs(); ?>
			<p class="zd-eyebrow zd-eyebrow--light"><?php echo esc_html( count( $zd_brands ) ); ?> علامات · <?php echo esc_html( zad_n_items( zad_products_total() ) ); ?></p>
			<h1 class="zd-page-hero__title"><?php the_title(); ?></h1>
			<div class="zd-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>
	<div class="zd-container">
		<div class="zd-brands__grid zd-brands__grid--page">
			<?php foreach ( $zd_brands as $zd_slug => $zd_b ) : ?>
				<?php get_template_part( 'template-parts/brand-card', null, array( 'slug' => $zd_slug, 'brand' => $zd_b ) ); ?>
			<?php endforeach; ?>
			<article class="zd-brand-card zd-brand-card--more">
				<a class="zd-brand-card__link" href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">
					<span class="zd-brand-card__logo">+</span>
					<span class="zd-brand-card__name">علامة أخرى؟</span>
				</a>
				<p class="zd-brand-card__about">تحتاج أصناف علامة غير موجودة هنا؟ أرسل اسمها، ويؤمّنها فريق المشتريات بسعر الجملة.</p>
				<a class="zd-link" href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">أرسل طلب توريد</a>
			</article>
		</div>
	</div>
</main>
<?php
get_footer();
