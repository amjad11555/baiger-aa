<?php
/**
 * رأس صفحات الأرشيف + شرائح الفلترة حسب القسم والشركة.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_cats    = maria_categories();
$mr_brands  = maria_all_brands();
$mr_filters = maria_archive_filters();
$mr_cat     = '';
$mr_brand   = '';
$mr_icon    = 'grid';
$mr_color   = '#0F5C63';
$mr_tint    = '#EAF5F3';
$mr_desc    = '';
$mr_scope   = array();
global $wp_query;
$mr_count = (int) $wp_query->found_posts;

if ( is_search() ) {
	$mr_title = sprintf( 'نتائج البحث عن «%s»', get_search_query() );
	$mr_icon  = 'search';
	$mr_ids   = maria_search_ids( get_search_query() );
	$mr_scope = array( 'ids' => array_flip( $mr_ids ) );
} elseif ( is_product_category() ) {
	$mr_term  = get_queried_object();
	$mr_cat   = $mr_term->slug;
	$mr_title = 'offers' === $mr_cat ? 'عروض الجملة' : $mr_term->name . ' بالجملة';
	$mr_desc  = $mr_term->description;
	if ( isset( $mr_cats[ $mr_cat ] ) ) {
		$mr_icon  = $mr_cats[ $mr_cat ]['icon'];
		$mr_color = $mr_cats[ $mr_cat ]['color'];
		$mr_tint  = $mr_cats[ $mr_cat ]['tint'];
	}
	$mr_scope = array( 'cat' => $mr_cat );
} elseif ( is_tax( 'product_brand' ) ) {
	$mr_term  = get_queried_object();
	$mr_brand = $mr_term->slug;
	$mr_title = 'منتجات ' . $mr_term->name . ' بالجملة';
	$mr_desc  = $mr_term->description ? $mr_term->description : ( isset( $mr_brands[ $mr_brand ] ) ? $mr_brands[ $mr_brand ]['about'] : '' );
	if ( isset( $mr_brands[ $mr_brand ] ) ) {
		$mr_color = $mr_brands[ $mr_brand ]['c1'];
	}
	$mr_scope = array( 'brand' => $mr_brand );
} elseif ( is_product_taxonomy() ) {
	$mr_term  = get_queried_object();
	$mr_title = $mr_term->name;
	$mr_desc  = $mr_term->description;
} else {
	$mr_title = 'كل المنتجات بالجملة';
	$mr_desc  = 'كيك وبسكويت وشيبسات وتسالي تركية بأسعار الكرتونة للبقالات والماركت. استخدم الشرائح لتصفية النتائج حسب القسم أو الشركة.';
}

// الرابط الأساسي للصفحة الحالية دون الفلاتر.
$mr_base = remove_query_arg( array( 'company', 'section', 'paged', 'product-page' ) );
$mr_base = preg_replace( '#/page/\d+/?#', '/', $mr_base );

$mr_show_cats   = ! $mr_cat;
$mr_show_brands = ! $mr_brand;
$mr_cat_counts  = $mr_show_cats ? maria_facet_counts( 'cat', $mr_scope + ( $mr_filters['company'] ? array( 'brand' => $mr_filters['company'] ) : array() ) ) : array();
$mr_br_counts   = $mr_show_brands ? maria_facet_counts( 'brand', $mr_scope + ( $mr_filters['section'] ? array( 'cat' => $mr_filters['section'] ) : array() ) ) : array();
?>
<header class="mr-archive-hero" style="--c:<?php echo esc_attr( $mr_color ); ?>;--t:<?php echo esc_attr( $mr_tint ); ?>">
	<span class="mr-archive-hero__icon" aria-hidden="true">
		<?php if ( $mr_brand && isset( $mr_brands[ $mr_brand ] ) ) : ?>
			<span class="mr-archive-hero__word" lang="tr"><?php echo esc_html( $mr_brands[ $mr_brand ]['latin'] ); ?></span>
		<?php else : ?>
			<?php maria_the_icon( $mr_icon, '', 30 ); ?>
		<?php endif; ?>
	</span>
	<div class="mr-archive-hero__text">
		<h1 class="mr-archive-hero__title"><?php echo esc_html( $mr_title ); ?></h1>
		<p class="mr-archive-hero__meta"><?php echo esc_html( maria_n_items( $mr_count ) ); ?><?php echo $mr_desc ? ' · ' . esc_html( wp_trim_words( wp_strip_all_tags( $mr_desc ), 26, '…' ) ) : ''; ?></p>
	</div>
	<a class="mr-btn mr-btn--ghost mr-btn--sm mr-archive-hero__quick" href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>"><?php maria_the_icon( 'list', '', 18 ); ?> قائمة الطلب السريع</a>
</header>

<div class="mr-filters">
	<?php if ( $mr_show_cats && $mr_cat_counts ) : ?>
		<nav class="mr-filters__row" aria-label="تصفية حسب القسم">
			<span class="mr-filters__label">القسم</span>
			<div class="mr-filters__chips mr-scroller">
				<a class="mr-chip<?php echo ! $mr_filters['section'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $mr_filters['company'] ? add_query_arg( 'company', $mr_filters['company'], $mr_base ) : $mr_base ); ?>">الكل</a>
				<?php foreach ( $mr_cats as $mr_s => $mr_c ) : ?>
					<?php if ( ! empty( $mr_cat_counts[ $mr_s ] ) ) : ?>
						<?php $mr_on = $mr_filters['section'] === $mr_s; ?>
						<a class="mr-chip<?php echo $mr_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'section' => $mr_on ? '' : $mr_s, 'company' => $mr_filters['company'] ) ), $mr_base ) ); ?>"<?php echo $mr_on ? ' aria-current="true"' : ''; ?>><?php maria_the_icon( $mr_c['icon'], '', 16 ); ?> <?php echo esc_html( $mr_c['name'] ); ?> <span class="mr-chip__n"><?php echo (int) $mr_cat_counts[ $mr_s ]; ?></span></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>
	<?php if ( $mr_show_brands && $mr_br_counts ) : ?>
		<nav class="mr-filters__row" aria-label="تصفية حسب الشركة">
			<span class="mr-filters__label">الشركة</span>
			<div class="mr-filters__chips mr-scroller">
				<a class="mr-chip<?php echo ! $mr_filters['company'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $mr_filters['section'] ? add_query_arg( 'section', $mr_filters['section'], $mr_base ) : $mr_base ); ?>">الكل</a>
				<?php foreach ( $mr_brands as $mr_s => $mr_b ) : ?>
					<?php if ( ! empty( $mr_br_counts[ $mr_s ] ) ) : ?>
						<?php $mr_on = $mr_filters['company'] === $mr_s; ?>
						<a class="mr-chip mr-chip--brand<?php echo $mr_on ? ' is-active' : ''; ?>" style="--c:<?php echo esc_attr( $mr_b['c1'] ); ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'company' => $mr_on ? '' : $mr_s, 'section' => $mr_filters['section'] ) ), $mr_base ) ); ?>"<?php echo $mr_on ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $mr_b['ar'] ); ?> <span class="mr-chip__n"><?php echo (int) $mr_br_counts[ $mr_s ]; ?></span></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>
</div>
