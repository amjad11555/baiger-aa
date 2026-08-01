<?php
/**
 * Single post.
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
				<div class="post-meta"><?php baigr_blog_posted_on(); ?><?php if ( has_category() ) { echo '<span aria-hidden="true">•</span>'; the_category( '، ' ); } ?></div>
				<h1><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-featured">
					<?php the_post_thumbnail( 'full', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<div class="page-links">صفحات: ', 'after' => '</div>' ) );
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<div class="tags reveal"><?php the_tags( '', '' ); ?></div>
			<?php endif; ?>

			<aside class="brand-cta reveal">
				<p class="eyebrow"><span class="eyebrow__line"></span>BAIGR</p>
				<h3>تبي تحوّل هذا الكلام إلى نموّ حقيقي؟</h3>
				<p>نبني لك تسويقاً مدعوماً بالذكاء الاصطناعي — إعلانات، علامة، موقع، وأتمتة — مصمّماً لسوقك. لا وعود فارغة، بل نتائج تُقاس.</p>
				<a class="btn btn--solid btn--lg magnetic" href="<?php echo esc_url( BAIGR_SITE ); ?>/">تعرّف على خدمات BAIGR ←</a>
			</aside>

			<?php
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
								<article class="post-card reveal">
									<a class="thumb <?php echo has_post_thumbnail() ? '' : 'thumb--empty'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
										<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } else { echo '<span></span>'; } ?>
									</a>
									<div class="body">
										<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
										<a class="read-more" href="<?php the_permalink(); ?>">اقرأ <span aria-hidden="true">←</span></a>
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
