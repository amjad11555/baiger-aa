<?php
/**
 * رأس صفحات الأرشيف + شرائح الفلترة حسب القسم والشركة.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_cats    = shami_categories();
$sh_brands  = shami_all_brands();
$sh_filters = shami_archive_filters();
$sh_cat     = '';
$sh_brand   = '';
$sh_image   = 'flatlay';
$sh_eyebrow = 'التشكيلة';
$sh_desc    = '';
$sh_scope   = array();
global $wp_query;
$sh_count = (int) $wp_query->found_posts;

if ( is_search() ) {
	$sh_title   = sprintf( 'نتائج البحث عن «%s»', get_search_query() );
	$sh_eyebrow = 'البحث';
	$sh_image   = 'step-pick';
	$sh_ids     = shami_search_ids( get_search_query() );
	$sh_scope   = array( 'ids' => array_flip( $sh_ids ) );
} elseif ( is_product_category() ) {
	$sh_term  = get_queried_object();
	$sh_cat   = $sh_term->slug;
	$sh_title = isset( $sh_cats[ $sh_cat ] ) ? $sh_cats[ $sh_cat ]['title'] . ' بالجملة' : $sh_term->name . ' بالجملة';
	if ( 'offers' === $sh_cat ) {
		$sh_title = 'عروض الجملة';
	}
	$sh_desc = $sh_term->description;
	if ( isset( $sh_cats[ $sh_cat ] ) ) {
		$sh_image = $sh_cats[ $sh_cat ]['image'];
	}
	$sh_scope = array( 'cat' => $sh_cat );
} elseif ( is_tax( 'product_brand' ) ) {
	$sh_term    = get_queried_object();
	$sh_brand   = $sh_term->slug;
	$sh_eyebrow = 'العلامات';
	$sh_title   = 'منتجات ' . $sh_term->name . ' بالجملة';
	$sh_desc    = $sh_term->description ? $sh_term->description : ( isset( $sh_brands[ $sh_brand ] ) ? $sh_brands[ $sh_brand ]['about'] : '' );
	$sh_image   = 'seg-supermarket';
	$sh_scope   = array( 'brand' => $sh_brand );
} elseif ( is_product_taxonomy() ) {
	$sh_term  = get_queried_object();
	$sh_title = $sh_term->name;
	$sh_desc  = $sh_term->description;
} else {
	$sh_title = 'كل الأصناف بأسعار الجملة';
	$sh_desc  = 'الكيك والبسكويت والشيبس والشوكولاتة التركية بسعر الكرتونة لتجار التجزئة. صفِّ الأصناف حسب القسم أو العلامة، وأضف الكميات مباشرة إلى الطلبية.';
}

// الرابط الأساسي للصفحة الحالية دون الفلاتر.
$sh_base = remove_query_arg( array( 'company', 'section', 'paged', 'product-page' ) );
$sh_base = preg_replace( '#/page/\d+/?#', '/', $sh_base );

$sh_show_cats   = ! $sh_cat;
$sh_show_brands = ! $sh_brand;
$sh_cat_counts  = $sh_show_cats ? shami_facet_counts( 'cat', $sh_scope + ( $sh_filters['company'] ? array( 'brand' => $sh_filters['company'] ) : array() ) ) : array();
$sh_br_counts   = $sh_show_brands ? shami_facet_counts( 'brand', $sh_scope + ( $sh_filters['section'] ? array( 'cat' => $sh_filters['section'] ) : array() ) ) : array();
?>
<header class="sh-archive-hero">
	<div class="sh-archive-hero__media"><?php echo shami_img( $sh_image, '', array( 'sizes' => '(min-width: 1200px) 1200px, 100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<div class="sh-archive-hero__text">
		<p class="sh-eyebrow sh-eyebrow--light"><?php echo esc_html( $sh_eyebrow ); ?> · <?php echo esc_html( shami_n_items( $sh_count ) ); ?></p>
		<h1 class="sh-archive-hero__title">
			<?php if ( $sh_brand && isset( $sh_brands[ $sh_brand ] ) ) : ?>
				<span class="sh-archive-hero__word" lang="tr"><?php echo esc_html( $sh_brands[ $sh_brand ]['latin'] ); ?></span>
			<?php endif; ?>
			<?php echo esc_html( $sh_title ); ?>
		</h1>
		<?php if ( $sh_desc ) : ?>
			<p class="sh-archive-hero__meta"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $sh_desc ), 30, '…' ) ); ?></p>
		<?php endif; ?>
	</div>
	<a class="sh-btn sh-btn--accent sh-btn--sm sh-archive-hero__quick" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">قائمة الأسعار</a>
</header>

<div class="sh-filters">
	<?php if ( $sh_show_cats && $sh_cat_counts ) : ?>
		<nav class="sh-filters__row" aria-label="تصفية حسب القسم">
			<span class="sh-filters__label">القسم</span>
			<div class="sh-filters__chips sh-scroller">
				<a class="sh-chip<?php echo ! $sh_filters['section'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $sh_filters['company'] ? add_query_arg( 'company', $sh_filters['company'], $sh_base ) : $sh_base ); ?>">الكل</a>
				<?php foreach ( $sh_cats as $sh_s => $sh_c ) : ?>
					<?php if ( ! empty( $sh_cat_counts[ $sh_s ] ) ) : ?>
						<?php $sh_on = $sh_filters['section'] === $sh_s; ?>
						<a class="sh-chip<?php echo $sh_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'section' => $sh_on ? '' : $sh_s, 'company' => $sh_filters['company'] ) ), $sh_base ) ); ?>"<?php echo $sh_on ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $sh_c['title'] ); ?> <span class="sh-chip__n"><?php echo (int) $sh_cat_counts[ $sh_s ]; ?></span></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>
	<?php if ( $sh_show_brands && $sh_br_counts ) : ?>
		<nav class="sh-filters__row" aria-label="تصفية حسب العلامة">
			<span class="sh-filters__label">العلامة</span>
			<div class="sh-filters__chips sh-scroller">
				<a class="sh-chip<?php echo ! $sh_filters['company'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $sh_filters['section'] ? add_query_arg( 'section', $sh_filters['section'], $sh_base ) : $sh_base ); ?>">الكل</a>
				<?php foreach ( $sh_brands as $sh_s => $sh_b ) : ?>
					<?php if ( ! empty( $sh_br_counts[ $sh_s ] ) ) : ?>
						<?php $sh_on = $sh_filters['company'] === $sh_s; ?>
						<a class="sh-chip sh-chip--brand<?php echo $sh_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'company' => $sh_on ? '' : $sh_s, 'section' => $sh_filters['section'] ) ), $sh_base ) ); ?>"<?php echo $sh_on ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $sh_b['ar'] ); ?> <span class="sh-chip__n"><?php echo (int) $sh_br_counts[ $sh_s ]; ?></span></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>
</div>
