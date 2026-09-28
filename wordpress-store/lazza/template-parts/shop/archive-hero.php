<?php
/**
 * رأس صفحات الأرشيف في المتجر.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_cats   = lazza_categories();
$lz_brands = lazza_brands();
$lz_slug   = '';
$lz_color  = '#CE0006';
$lz_tint   = '#FFE8E3';
$lz_icon   = 'grid';
$lz_desc   = '';
$lz_count  = 0;
$lz_brand  = '';

if ( is_search() ) {
	$lz_title = sprintf( 'نتائج البحث عن «%s»', get_search_query() );
	$lz_icon  = 'search';
	global $wp_query;
	$lz_count = (int) $wp_query->found_posts;
} elseif ( is_product_category() ) {
	$lz_term  = get_queried_object();
	$lz_slug  = $lz_term->slug;
	$lz_title = 'offers' === $lz_slug ? 'عروض الجملة' : $lz_term->name . ' بالجملة';
	$lz_desc  = $lz_term->description;
	$lz_count = (int) $lz_term->count;
	if ( isset( $lz_cats[ $lz_slug ] ) ) {
		$lz_color = $lz_cats[ $lz_slug ]['color'];
		$lz_tint  = $lz_cats[ $lz_slug ]['tint'];
		$lz_icon  = $lz_cats[ $lz_slug ]['icon'];
	}
} elseif ( is_tax( 'product_brand' ) ) {
	$lz_term  = get_queried_object();
	$lz_brand = $lz_term->slug;
	$lz_title = 'منتجات ' . $lz_term->name . ' بالجملة';
	$lz_desc  = $lz_term->description;
	$lz_count = (int) $lz_term->count;
	if ( isset( $lz_brands[ $lz_brand ] ) ) {
		$lz_color = $lz_brands[ $lz_brand ]['c1'];
		$lz_tint  = '#FFF6E6';
		$lz_icon  = 'shield';
	}
} elseif ( is_product_taxonomy() ) {
	$lz_term  = get_queried_object();
	$lz_title = $lz_term->name;
	$lz_desc  = $lz_term->description;
	$lz_count = (int) $lz_term->count;
} else {
	$lz_title = 'كل المنتجات بالجملة';
	$lz_desc  = 'كيك وبسكويت وشيبسات وتسالي تركية من إيتي وأولكر وبونوتشي بأسعار الكرتونة للبقالات والماركت.';
	$lz_count = lazza_products_total();
}
?>
<header class="lz-archive-hero" style="--c:<?php echo esc_attr( $lz_color ); ?>;--t:<?php echo esc_attr( $lz_tint ); ?>">
	<div class="lz-archive-hero__text">
		<h1 class="lz-archive-hero__title"><?php echo esc_html( $lz_title ); ?></h1>
		<?php if ( $lz_desc ) : ?>
			<p class="lz-archive-hero__desc"><?php echo esc_html( wp_strip_all_tags( $lz_desc ) ); ?></p>
		<?php endif; ?>
		<div class="lz-archive-hero__meta">
			<span class="lz-pill"><?php lazza_the_icon( 'box', '', 16 ); ?> <?php echo esc_html( lazza_n_items( $lz_count ) ); ?></span>
			<a class="lz-pill lz-pill--gold" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"><?php lazza_the_icon( 'bolt', '', 16 ); ?> اطلب الكل من الطلب السريع</a>
		</div>
	</div>
	<span class="lz-archive-hero__icon" aria-hidden="true"><?php lazza_the_icon( $lz_icon, '', 64 ); ?></span>
</header>

<nav class="lz-chips" aria-label="تصفح الأقسام">
	<a class="lz-chip<?php echo ( ! $lz_slug && ! $lz_brand && ! is_search() ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php lazza_the_icon( 'grid', '', 16 ); ?> الكل</a>
	<?php foreach ( $lz_cats as $lz_s => $lz_c ) : ?>
		<a class="lz-chip<?php echo $lz_slug === $lz_s ? ' is-active' : ''; ?>" href="<?php echo esc_url( lazza_cat_url( $lz_s ) ); ?>" style="--c:<?php echo esc_attr( $lz_c['color'] ); ?>"<?php echo $lz_slug === $lz_s ? ' aria-current="page"' : ''; ?>><?php lazza_the_icon( $lz_c['icon'], '', 16 ); ?> <?php echo esc_html( $lz_c['name'] ); ?></a>
	<?php endforeach; ?>
	<?php if ( taxonomy_exists( 'product_brand' ) ) : ?>
		<span class="lz-chips__sep" aria-hidden="true"></span>
		<?php foreach ( $lz_brands as $lz_s => $lz_b ) : ?>
			<?php $lz_l = get_term_link( $lz_s, 'product_brand' ); ?>
			<?php if ( ! is_wp_error( $lz_l ) ) : ?>
				<a class="lz-chip lz-chip--brand<?php echo $lz_brand === $lz_s ? ' is-active' : ''; ?>" href="<?php echo esc_url( $lz_l ); ?>" style="--c:<?php echo esc_attr( $lz_b['c1'] ); ?>"><?php echo esc_html( $lz_b['ar'] ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</nav>
