<?php
/**
 * Main blog listing template.
 *
 * @package baigr-blog
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1><?php single_post_title(); ?></h1>
			<p>مقالات ورؤى في التسويق والنموّ بالذكاء الاصطناعي.</p>
		<?php elseif ( is_archive() ) : ?>
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<p>', '</p>' ); ?>
		<?php elseif ( is_search() ) : ?>
			<h1>نتائج البحث عن: <?php echo esc_html( get_search_query() ); ?></h1>
		<?php else : ?>
			<h1>مدوّنة <?php bloginfo( 'name' ); ?></h1>
			<p>مقالات ورؤى في التسويق والنموّ بالذكاء الاصطناعي.</p>
		<?php endif; ?>
	</div>
</section>

<div class="container-wide">
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="thumb" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
							<?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
						</a>
					<?php endif; ?>
					<div class="body">
						<div class="post-meta"><?php baigr_blog_posted_on(); ?></div>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26, '…' ) ); ?></p>
						<a class="read-more" href="<?php the_permalink(); ?>">اقرأ المقال ←</a>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<div class="pagination">
			<?php
			echo paginate_links( array(
				'prev_text' => '→ السابق',
				'next_text' => 'التالي ←',
			) );
			?>
		</div>

	<?php else : ?>
		<div class="container" style="padding:60px 20px;text-align:center">
			<h2>لا توجد مقالات بعد</h2>
			<p>ترقّبوا أول مقالاتنا قريباً.</p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
