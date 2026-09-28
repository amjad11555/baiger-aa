<?php
/**
 * الصفحات والمقالات المفردة.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="lz-main">
	<div class="lz-container<?php echo ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ? '' : ' lz-container--narrow'; ?>">
		<?php
		while ( have_posts() ) :
			the_post();
			$lz_is_wc_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'lz-article' ); ?>>
				<header class="lz-page-head<?php echo $lz_is_wc_page ? ' lz-page-head--wc' : ''; ?>">
					<?php lazza_breadcrumbs(); ?>
					<h1 class="lz-page-title"><?php the_title(); ?></h1>
					<?php if ( $lz_is_wc_page && function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) : ?>
						<ol class="lz-progress" aria-label="مراحل الطلب">
							<li class="is-done"><?php lazza_the_icon( 'check', '', 16 ); ?> السلة</li>
							<li class="is-current">بيانات التوصيل</li>
							<li>تأكيد الطلب</li>
						</ol>
					<?php endif; ?>
				</header>
				<div class="lz-prose lz-entry">
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
