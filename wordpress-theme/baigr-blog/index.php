<?php
/**
 * Blog listing / archive / search.
 *
 * @package baigr-blog
 */

get_header();
?>

<section class="page-hero aurora">
	<canvas id="hero-field" aria-hidden="true"></canvas>
	<div class="floor"></div>
	<div class="container inner">
		<p class="eyebrow eyebrow--center"><span class="eyebrow__line"></span>
			<?php
			if ( is_search() ) { echo 'بحث'; }
			elseif ( is_category() || is_tag() || is_archive() ) { echo 'أرشيف'; }
			else { echo 'مدوّنة BAIGR'; }
			?>
		</p>
		<h1 class="wr" data-wr="load">
			<?php
			if ( is_search() ) { echo 'نتائج: ' . esc_html( get_search_query() ); }
			elseif ( is_archive() ) { echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); }
			elseif ( is_home() && ! is_front_page() && get_option( 'page_for_posts' ) ) { echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ); }
			else { echo 'رؤى في التسويق والنموّ'; }
			?>
		</h1>
		<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : 'مقالات عملية في التسويق والنموّ بالذكاء الاصطناعي — من فريق BAIGR.' ); ?></p>
	</div>
</section>

<div class="container">
	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			$i = 0;
			while ( have_posts() ) :
				the_post();
				$i++;
				?>
				<article <?php post_class( 'post-card reveal' ); ?> data-delay="<?php echo esc_attr( ( $i % 3 ) * 0.08 ); ?>">
					<a class="thumb <?php echo has_post_thumbnail() ? '' : 'thumb--empty'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } else { echo '<span></span>'; } ?>
					</a>
					<div class="body">
						<div class="post-meta"><?php baigr_blog_posted_on(); ?></div>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
						<a class="read-more" href="<?php the_permalink(); ?>">اقرأ المقال <span aria-hidden="true">←</span></a>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<div class="pagination">
			<?php echo paginate_links( array( 'prev_text' => '→ السابق', 'next_text' => 'التالي ←' ) ); ?>
		</div>

	<?php else : ?>
		<div style="padding:5rem 0;text-align:center">
			<h2 class="h2">لا توجد مقالات بعد</h2>
			<p style="color:var(--mist);margin-top:1rem">ترقّبوا أول مقالاتنا قريباً.</p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
