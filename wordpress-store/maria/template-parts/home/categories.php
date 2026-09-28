<?php
/**
 * الواجهة الرئيسية: الأقسام والشركات كمربعات ملوّنة بصور المنتجات.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_tiles = array();
foreach ( maria_categories() as $mr_slug => $mr_cat ) {
	$mr_tiles[] = array(
		'url'   => maria_cat_url( $mr_slug ),
		'color' => $mr_cat['color'],
		'name'  => $mr_cat['name'],
		'count' => maria_n_items( maria_cat_count( $mr_slug ) ),
		'query' => array( 'category' => array( $mr_slug ) ),
	);
}
$mr_extra = array( '#518B50', '#F6B90A', '#8C51FF', '#006C78' );
$mr_i     = 0;
foreach ( maria_all_brands() as $mr_slug => $mr_b ) {
	if ( ! $mr_b['count'] ) {
		continue;
	}
	$mr_tiles[] = array(
		'url'   => $mr_b['url'],
		'color' => $mr_extra[ $mr_i % count( $mr_extra ) ],
		'name'  => $mr_b['ar'],
		'latin' => $mr_b['latin'],
		'count' => maria_n_items( $mr_b['count'] ),
		'query' => taxonomy_exists( 'product_brand' ) ? array( 'tax_query' => array( array( 'taxonomy' => 'product_brand', 'field' => 'slug', 'terms' => $mr_slug ) ) ) : array(),
	);
	++$mr_i;
}
?>
<section class="mr-section mr-section--white mr-tiles-sec" aria-labelledby="mr-cats-title">
	<div class="mr-container">
		<div class="mr-section__head mr-section__head--row">
			<h2 class="mr-section__title" id="mr-cats-title">الأقسام الشائعة</h2>
			<a class="mr-link" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">جميع المنتجات <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
		</div>
		<div class="mr-tiles mr-scroller">
			<?php foreach ( $mr_tiles as $mr_t ) : ?>
				<?php
				$mr_p = array();
				if ( function_exists( 'wc_get_products' ) && $mr_t['query'] ) {
					$mr_p = wc_get_products(
						array_merge(
							array(
								'status'     => 'publish',
								'visibility' => 'catalog',
								'limit'      => 1,
								'orderby'    => 'menu_order',
								'order'      => 'ASC',
							),
							$mr_t['query']
						)
					);
				}
				?>
				<a class="mr-tile" href="<?php echo esc_url( $mr_t['url'] ); ?>" style="--c:<?php echo esc_attr( $mr_t['color'] ); ?>">
					<span class="mr-tile__art" aria-hidden="true">
						<?php if ( ! empty( $mr_t['latin'] ) ) : ?>
							<span class="mr-tile__word" lang="tr"><?php echo esc_html( $mr_t['latin'] ); ?></span>
						<?php endif; ?>
						<?php if ( $mr_p ) : ?>
							<?php echo $mr_p[0]->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>
					</span>
					<span class="mr-tile__name"><?php echo esc_html( $mr_t['name'] ); ?></span>
					<span class="mr-tile__count"><?php echo esc_html( $mr_t['count'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
