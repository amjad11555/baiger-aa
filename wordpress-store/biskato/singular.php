<?php
/**
 * الصفحات والمقالات المفردة.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="zd-main">
	<?php
	while ( have_posts() ) :
		the_post();
		$zd_is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
		$zd_hero       = $zd_is_wc_page ? '' : zad_page_image( get_the_ID() );
		?>
		<?php if ( $zd_hero ) : ?>
			<section class="zd-page-hero">
				<div class="zd-page-hero__media"><?php echo zad_img( $zd_hero, '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<div class="zd-container zd-page-hero__inner">
					<?php zad_breadcrumbs(); ?>
					<p class="zd-eyebrow zd-eyebrow--light"><?php echo esc_html( zad_page_image( get_the_ID(), 'eyebrow' ) ); ?></p>
					<h1 class="zd-page-hero__title"><?php the_title(); ?></h1>
				</div>
			</section>
		<?php endif; ?>
		<div class="zd-container<?php echo $zd_is_wc_page ? '' : ' zd-container--narrow'; ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'zd-article' ); ?>>
				<?php if ( ! $zd_hero ) : ?>
					<header class="zd-page-head<?php echo $zd_is_wc_page ? ' zd-page-head--wc' : ''; ?>">
						<?php zad_breadcrumbs(); ?>
						<h1 class="zd-page-title"><?php the_title(); ?></h1>
						<?php if ( $zd_is_wc_page && function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) : ?>
							<ol class="zd-progress" aria-label="مراحل الطلب">
								<li class="is-done">الطلبية</li>
								<li class="is-current">بيانات التسليم</li>
								<li>التأكيد</li>
							</ol>
						<?php endif; ?>
					</header>
				<?php endif; ?>
				<div class="zd-prose zd-entry">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
