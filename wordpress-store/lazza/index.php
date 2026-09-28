<?php
/**
 * القالب العام (المدونة، الأرشيف، نتائج البحث العامة).
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="lz-main">
	<div class="lz-container">
		<header class="lz-page-head">
			<h1 class="lz-page-title">
				<?php
				if ( is_search() ) {
					printf( 'نتائج البحث عن: «%s»', esc_html( get_search_query() ) );
				} elseif ( is_archive() ) {
					echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
				} elseif ( is_home() && ! is_front_page() ) {
					single_post_title();
				} else {
					bloginfo( 'name' );
				}
				?>
			</h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="lz-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'lz-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="lz-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium_large' ); ?></a>
						<?php endif; ?>
						<h2 class="lz-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="lz-post-card__excerpt"><?php the_excerpt(); ?></div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="lz-empty">
				<h2>لا توجد نتائج</h2>
				<p>جرّب البحث بكلمة أخرى أو تصفّح أقسام المتجر.</p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
