<?php
/**
 * الصفحات والمقالات المفردة.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="mr-main">
	<div class="mr-container<?php echo ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ? '' : ' mr-container--narrow'; ?>">
		<?php
		while ( have_posts() ) :
			the_post();
			$mr_is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'mr-article' ); ?>>
				<header class="mr-page-head<?php echo $mr_is_wc_page ? ' mr-page-head--wc' : ''; ?>">
					<?php maria_breadcrumbs(); ?>
					<h1 class="mr-page-title"><?php the_title(); ?></h1>
					<?php if ( $mr_is_wc_page && function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) : ?>
						<ol class="mr-progress" aria-label="مراحل الطلب">
							<li class="is-done"><?php maria_the_icon( 'check', '', 16 ); ?> السلة</li>
							<li class="is-current">بيانات التوصيل</li>
							<li>تأكيد الطلب</li>
						</ol>
					<?php endif; ?>
				</header>
				<div class="mr-prose mr-entry">
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
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
