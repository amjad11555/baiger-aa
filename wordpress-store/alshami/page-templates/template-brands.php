<?php
/**
 * Template Name: العلامات التجارية
 *
 * دليل العلامات في التشكيلة مع عدد الأصناف وأقسام كل علامة.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
$sh_brands = array_filter(
	shami_all_brands(),
	static function ( $b ) {
		return $b['count'] > 0;
	}
);
?>
<main id="main" class="sh-main sh-brands-page">
	<section class="sh-page-hero">
		<div class="sh-page-hero__media"><?php echo shami_img( 'seg-supermarket', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="sh-container sh-page-hero__inner">
			<?php shami_breadcrumbs(); ?>
			<p class="sh-eyebrow sh-eyebrow--light"><?php echo esc_html( count( $sh_brands ) ); ?> علامات · <?php echo esc_html( shami_n_items( shami_products_total() ) ); ?></p>
			<h1 class="sh-page-hero__title"><?php the_title(); ?></h1>
			<div class="sh-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>
	<div class="sh-container">
		<div class="sh-brands__grid sh-brands__grid--page">
			<?php foreach ( $sh_brands as $sh_slug => $sh_b ) : ?>
				<?php get_template_part( 'template-parts/brand-card', null, array( 'slug' => $sh_slug, 'brand' => $sh_b ) ); ?>
			<?php endforeach; ?>
			<article class="sh-brand-card sh-brand-card--more">
				<a class="sh-brand-card__link" href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">
					<span class="sh-brand-card__logo">+</span>
					<span class="sh-brand-card__name">علامة أخرى؟</span>
				</a>
				<p class="sh-brand-card__about">تحتاج أصناف علامة غير موجودة هنا؟ أرسل اسمها، ويؤمّنها فريق المشتريات بسعر الجملة.</p>
				<a class="sh-link" href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">أرسل طلب توريد</a>
			</article>
		</div>
	</div>
</main>
<?php
get_footer();
