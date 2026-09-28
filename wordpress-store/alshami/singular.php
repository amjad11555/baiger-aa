<?php
/**
 * الصفحات والمقالات المفردة.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="sh-main">
	<?php
	while ( have_posts() ) :
		the_post();
		$sh_is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
		$sh_hero       = $sh_is_wc_page ? '' : shami_page_image( get_the_ID() );
		?>
		<?php if ( $sh_hero ) : ?>
			<section class="sh-page-hero">
				<div class="sh-page-hero__media"><?php echo shami_img( $sh_hero, '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<div class="sh-container sh-page-hero__inner">
					<?php shami_breadcrumbs(); ?>
					<p class="sh-eyebrow sh-eyebrow--light"><?php echo esc_html( shami_page_image( get_the_ID(), 'eyebrow' ) ); ?></p>
					<h1 class="sh-page-hero__title"><?php the_title(); ?></h1>
				</div>
			</section>
		<?php endif; ?>
		<div class="sh-container<?php echo $sh_is_wc_page ? '' : ' sh-container--narrow'; ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'sh-article' ); ?>>
				<?php if ( ! $sh_hero ) : ?>
					<header class="sh-page-head<?php echo $sh_is_wc_page ? ' sh-page-head--wc' : ''; ?>">
						<?php shami_breadcrumbs(); ?>
						<h1 class="sh-page-title"><?php the_title(); ?></h1>
						<?php if ( $sh_is_wc_page && function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) : ?>
							<ol class="sh-progress" aria-label="مراحل الطلب">
								<li class="is-done">الطلبية</li>
								<li class="is-current">بيانات التسليم</li>
								<li>التأكيد</li>
							</ol>
						<?php endif; ?>
					</header>
				<?php endif; ?>
				<div class="sh-prose sh-entry">
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
