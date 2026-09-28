<?php
/**
 * بطاقة علامة تجارية (صفحة العلامات).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_slug = isset( $args['slug'] ) ? $args['slug'] : '';
$sh_b    = isset( $args['brand'] ) ? $args['brand'] : null;
if ( ! $sh_b ) {
	return;
}
$sh_cats   = shami_categories();
$sh_facets = function_exists( 'shami_facet_counts' ) ? shami_facet_counts( 'cat', array( 'brand' => $sh_slug ) ) : array();
?>
<article class="sh-brand-card">
	<a class="sh-brand-card__link" href="<?php echo esc_url( $sh_b['url'] ); ?>">
		<span class="sh-brand-card__logo" lang="tr"><?php echo esc_html( $sh_b['latin'] ); ?></span>
		<span class="sh-brand-card__name"><?php echo esc_html( $sh_b['ar'] ); ?> <small><?php echo esc_html( shami_n_items( $sh_b['count'] ) ); ?></small></span>
	</a>
	<?php if ( ! empty( $sh_b['about'] ) ) : ?>
		<p class="sh-brand-card__about"><?php echo esc_html( $sh_b['about'] ); ?></p>
	<?php endif; ?>
	<?php if ( $sh_facets ) : ?>
		<ul class="sh-brand-card__cats">
			<?php foreach ( $sh_cats as $sh_c => $sh_cat ) : ?>
				<?php if ( ! empty( $sh_facets[ $sh_c ] ) && 'offers' !== $sh_c ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( 'section', $sh_c, $sh_b['url'] ) ); ?>"><?php echo esc_html( $sh_cat['title'] ); ?> <span><?php echo (int) $sh_facets[ $sh_c ]; ?></span></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</article>
