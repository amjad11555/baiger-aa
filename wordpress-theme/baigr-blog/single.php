<?php
/**
 * Single post template.
 *
 * @package baigr-blog
 */

get_header();
?>

<div class="container single-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<header class="entry-header">
				<div class="post-meta" style="justify-content:center"><?php baigr_blog_posted_on(); ?></div>
				<h1><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-featured">
					<?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array(
					'before' => '<div class="page-links">صفحات: ',
					'after'  => '</div>',
				) );
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<div class="tags"><?php the_tags( '', '' ); ?></div>
			<?php endif; ?>

			<div class="brand-cta">
				<h3>تبي تحوّل هذا الكلام إلى نموّ حقيقي؟</h3>
				<p>في BAIGR نبني لك تسويقاً مدعوماً بالذكاء الاصطناعي — إعلانات، علامة، موقع، وأتمتة — مصمّماً لسوقك.</p>
				<a class="btn" href="https://baigr.com/">تعرّف على خدمات BAIGR ←</a>
			</div>

			<?php
			// Related posts (same category).
			$cats = wp_get_post_categories( get_the_ID() );
			if ( $cats ) {
				$related = new WP_Query( array(
					'category__in'        => $cats,
					'post__not_in'        => array( get_the_ID() ),
					'posts_per_page'      => 3,
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => true,
				) );
				if ( $related->have_posts() ) : ?>
					<section class="related">
						<h3>مقالات ذات صلة</h3>
						<div class="post-grid">
							<?php while ( $related->have_posts() ) : $related->the_post(); ?>
								<article class="post-card">
									<?php if ( has_post_thumbnail() ) : ?>
										<a class="thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
									<?php endif; ?>
									<div class="body">
										<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
										<a class="read-more" href="<?php the_permalink(); ?>">اقرأ ←</a>
									</div>
								</article>
							<?php endwhile; ?>
						</div>
					</section>
					<?php
				endif;
				wp_reset_postdata();
			}
			?>

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
