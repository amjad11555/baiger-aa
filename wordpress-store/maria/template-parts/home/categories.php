<?php
/**
 * الواجهة الرئيسية: الأقسام.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mr-section mr-cats" aria-labelledby="mr-cats-title">
	<div class="mr-container">
		<div class="mr-section__head mr-section__head--row">
			<div>
				<span class="mr-kicker"><?php maria_the_icon( 'grid', '', 16 ); ?> الأقسام</span>
				<h2 class="mr-section__title" id="mr-cats-title">تسوّق حسب القسم</h2>
			</div>
			<a class="mr-link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">كل المنتجات <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
		</div>
		<div class="mr-cats__grid mr-scroller">
			<?php foreach ( maria_categories() as $mr_slug => $mr_cat ) : ?>
				<a class="mr-cat-tile" href="<?php echo esc_url( maria_cat_url( $mr_slug ) ); ?>" style="--c:<?php echo esc_attr( $mr_cat['color'] ); ?>;--t:<?php echo esc_attr( $mr_cat['tint'] ); ?>">
					<span class="mr-cat-tile__icon"><?php maria_the_icon( $mr_cat['icon'], '', 28 ); ?></span>
					<span class="mr-cat-tile__name"><?php echo esc_html( $mr_cat['name'] ); ?></span>
					<span class="mr-cat-tile__line"><?php echo esc_html( $mr_cat['line'] ); ?></span>
					<span class="mr-cat-tile__count"><?php echo esc_html( maria_n_items( maria_cat_count( $mr_slug ) ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
