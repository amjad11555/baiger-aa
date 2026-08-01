<?php
/**
 * Static page.
 *
 * @package baigr-blog
 */

get_header();
?>

<div class="container single-wrap">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="entry-header">
				<h1><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-featured"><?php the_post_thumbnail( 'full' ); ?></figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<div class="page-links">صفحات: ', 'after' => '</div>' ) );
				?>
			</div>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
