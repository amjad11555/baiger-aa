<?php
/**
 * Blog listing / archive / search.
 *
 * @package baigr-blog
 */

get_header();

$is_main_first = ( is_home() || is_front_page() ) && ! is_paged() && ! is_search();
?>

<section class="page-hero aurora">
	<canvas id="hero-field" aria-hidden="true"></canvas>
	<div class="floor"></div>
	<div class="bgr-container">
		<div class="inner">
			<p class="eyebrow"><span class="eyebrow__line"></span>
				<?php
				if ( is_search() ) { echo 'بحث'; }
				elseif ( is_category() || is_tag() || is_archive() ) { echo 'أرشيف'; }
				else { echo 'مدوّنة BAIGR'; }
				?>
			</p>
			<h1>
				<?php
				if ( is_search() ) { echo 'نتائج: ' . esc_html( get_search_query() ); }
				elseif ( is_archive() ) { echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); }
				elseif ( is_home() && ! is_front_page() && get_option( 'page_for_posts' ) ) { echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ); }
				else { echo 'رؤى في التسويق <span class="hero-accent">والنموّ</span>'; }
				?>
			</h1>
			<p class="lead">مقالات عملية — لا نظريات — في التسويق والإعلانات والنموّ بالذكاء الاصطناعي، مكتوبة لسوقك الخليجي.</p>
			<?php if ( ! is_search() && ! is_archive() ) : ?>
				<div class="page-hero__meta">
					<span>استراتيجية</span><span>إعلانات أداء</span><span>ذكاء اصطناعي</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="bgr-container">
	<?php baigr_blog_filter_bar(); ?>

	<?php if ( have_posts() ) : ?>

		<?php
		$i = 0;
		while ( have_posts() ) :
			the_post();
			$i++;

			// First post on the main blog page → featured card.
			if ( $i === 1 && $is_main_first ) :
				?>
				<article <?php post_class( 'post-featured reveal' ); ?>>
					<a class="post-featured__media <?php echo has_post_thumbnail() ? '' : 'post-featured__media--empty'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php
						if ( has_category() ) { echo '<span class="chip">' . esc_html( get_the_category()[0]->name ) . '</span>'; }
						if ( has_post_thumbnail() ) { the_post_thumbnail( 'full' ); } else { echo '<i></i>'; }
						?>
					</a>
					<div class="post-featured__body">
						<div class="post-meta"><span class="chip">مقال مميّز</span><?php baigr_blog_posted_on(); ?></div>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34, '…' ) ); ?></p>
						<a class="read-more" href="<?php the_permalink(); ?>">اقرأ المقال <span aria-hidden="true">←</span></a>
					</div>
				</article>
				<div class="post-grid">
				<?php
				continue;
			endif;

			// Open the grid for non-featured layouts.
			if ( $i === 1 || ( $i === 2 && $is_main_first ) ) {
				if ( ! $is_main_first ) { echo '<div class="post-grid">'; }
			}
			?>
			<article <?php post_class( 'post-card reveal' ); ?>>
				<a class="thumb <?php echo has_post_thumbnail() ? '' : 'thumb--empty'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php
					if ( has_category() ) { echo '<span class="chip">' . esc_html( get_the_category()[0]->name ) . '</span>'; }
					if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } else { echo '<span></span>'; }
					?>
				</a>
				<div class="body">
					<div class="post-meta"><?php baigr_blog_posted_on(); ?></div>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p class="excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '…' ) ); ?></p>
					<a class="read-more" href="<?php the_permalink(); ?>">اقرأ المقال <span aria-hidden="true">←</span></a>
				</div>
			</article>
			<?php
		endwhile;
		echo '</div>'; // .post-grid
		?>

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
