<?php
/**
 * بطاقة علامة تجارية (صفحة العلامات).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_slug = isset( $args['slug'] ) ? $args['slug'] : '';
$zd_b    = isset( $args['brand'] ) ? $args['brand'] : null;
if ( ! $zd_b ) {
	return;
}
$zd_cats   = zad_categories();
$zd_facets = function_exists( 'zad_facet_counts' ) ? zad_facet_counts( 'cat', array( 'brand' => $zd_slug ) ) : array();
?>
<article class="zd-brand-card">
	<a class="zd-brand-card__link" href="<?php echo esc_url( $zd_b['url'] ); ?>">
		<span class="zd-brand-card__logo" lang="tr"><?php echo esc_html( $zd_b['latin'] ); ?></span>
		<span class="zd-brand-card__name"><?php echo esc_html( $zd_b['ar'] ); ?> <small><?php echo esc_html( zad_n_items( $zd_b['count'] ) ); ?></small></span>
	</a>
	<?php if ( ! empty( $zd_b['about'] ) ) : ?>
		<p class="zd-brand-card__about"><?php echo esc_html( $zd_b['about'] ); ?></p>
	<?php endif; ?>
	<?php if ( $zd_facets ) : ?>
		<ul class="zd-brand-card__cats">
			<?php foreach ( $zd_cats as $zd_c => $zd_cat ) : ?>
				<?php if ( ! empty( $zd_facets[ $zd_c ] ) && 'offers' !== $zd_c ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'section', $zd_c, $zd_b['url'] ) ); ?>"><?php echo esc_html( $zd_cat['title'] ); ?> <span><?php echo (int) $zd_facets[ $zd_c ]; ?></span></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</article>
