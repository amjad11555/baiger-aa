<?php
/**
 * القالب العام (المدونة، الأرشيف، نتائج البحث العامة).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="sh-main">
	<div class="sh-container">
		<header class="sh-page-head">
			<h1 class="sh-page-title">
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
			<div class="sh-posts">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'sh-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="sh-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium_large' ); ?></a>
						<?php endif; ?>
						<h2 class="sh-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="sh-post-card__excerpt"><?php the_excerpt(); ?></div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="sh-empty">
				<h2>لا توجد نتائج</h2>
				<p>جرّب البحث بكلمة أخرى أو تصفّح أقسام المتجر.</p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
