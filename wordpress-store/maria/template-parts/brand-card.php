<?php
/**
 * بطاقة شركة (الرئيسية وصفحة الشركات).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_slug = isset( $args['slug'] ) ? $args['slug'] : '';
$mr_b    = isset( $args['brand'] ) ? $args['brand'] : null;
if ( ! $mr_b ) {
	return;
}
$mr_cats   = maria_categories();
$mr_facets = function_exists( 'maria_facet_counts' ) ? maria_facet_counts( 'cat', array( 'brand' => $mr_slug ) ) : array();
?>
<article class="mr-brand-card" style="--c:<?php echo esc_attr( $mr_b['c1'] ); ?>">
	<a class="mr-brand-card__link" href="<?php echo esc_url( $mr_b['url'] ); ?>">
		<span class="mr-brand-card__logo" lang="tr"><?php echo esc_html( $mr_b['latin'] ); ?></span>
		<span class="mr-brand-card__name"><?php echo esc_html( $mr_b['ar'] ); ?> <small><?php echo esc_html( maria_n_items( $mr_b['count'] ) ); ?></small></span>
	</a>
	<?php if ( ! empty( $mr_b['about'] ) ) : ?>
		<p class="mr-brand-card__about"><?php echo esc_html( $mr_b['about'] ); ?></p>
	<?php endif; ?>
	<?php if ( $mr_facets ) : ?>
		<ul class="mr-brand-card__cats">
			<?php foreach ( $mr_cats as $mr_c => $mr_cat ) : ?>
				<?php if ( ! empty( $mr_facets[ $mr_c ] ) && 'offers' !== $mr_c ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'section', $mr_c, $mr_b['url'] ) ); ?>"><?php echo esc_html( $mr_cat['name'] ); ?> <span><?php echo (int) $mr_facets[ $mr_c ]; ?></span></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</article>
