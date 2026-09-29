<?php
/**
 * رأس صفحات الأرشيف + شرائح الفلترة حسب القسم والشركة.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_cats    = zad_categories();
$zd_brands  = zad_all_brands();
$zd_filters = zad_archive_filters();
$zd_cat     = '';
$zd_brand   = '';
$zd_image   = 'flatlay';
$zd_eyebrow = 'التشكيلة';
$zd_desc    = '';
$zd_scope   = array();
global $wp_query;
$zd_count = (int) $wp_query->found_posts;

if ( is_search() ) {
	$zd_title   = sprintf( 'نتائج البحث عن «%s»', get_search_query() );
	$zd_eyebrow = 'البحث';
	$zd_image   = 'step-pick';
	$zd_ids     = zad_search_ids( get_search_query() );
	$zd_scope   = array( 'ids' => array_flip( $zd_ids ) );
} elseif ( is_product_category() ) {
	$zd_term  = get_queried_object();
	$zd_cat   = $zd_term->slug;
	$zd_title = isset( $zd_cats[ $zd_cat ] ) ? $zd_cats[ $zd_cat ]['title'] . ' بالجملة' : $zd_term->name . ' بالجملة';
	if ( 'offers' === $zd_cat ) {
		$zd_title = 'عروض الجملة';
	}
	$zd_desc = $zd_term->description;
	if ( isset( $zd_cats[ $zd_cat ] ) ) {
		$zd_image = $zd_cats[ $zd_cat ]['image'];
	}
	$zd_scope = array( 'cat' => $zd_cat );
} elseif ( is_tax( 'product_brand' ) ) {
	$zd_term    = get_queried_object();
	$zd_brand   = $zd_term->slug;
	$zd_eyebrow = 'العلامات';
	$zd_title   = 'منتجات ' . $zd_term->name . ' بالجملة';
	$zd_desc    = $zd_term->description ? $zd_term->description : ( isset( $zd_brands[ $zd_brand ] ) ? $zd_brands[ $zd_brand ]['about'] : '' );
	$zd_image   = ! empty( $zd_brands[ $zd_brand ]['hero'] ) ? $zd_brands[ $zd_brand ]['hero'] : 'seg-supermarket';
	$zd_scope   = array( 'brand' => $zd_brand );
} elseif ( is_product_taxonomy() ) {
	$zd_term  = get_queried_object();
	$zd_title = $zd_term->name;
	$zd_desc  = $zd_term->description;
} elseif ( function_exists( 'zad_is_new_view' ) && zad_is_new_view() ) {
	$zd_title   = 'الأصناف الجديدة';
	$zd_eyebrow = 'وصل حديثاً';
	$zd_desc    = sprintf( 'كل ما وصل إلى التشكيلة خلال آخر %d يوماً، من الأحدث. تابع هذه الصفحة أو جرس الإشعارات لتعرف بالأصناف الجديدة فور وصولها.', zad_new_days() );
	$zd_scope   = array( 'ids' => array_flip( zad_new_product_ids( 500 ) ) );
} else {
	$zd_title = 'كل الأصناف بأسعار الجملة';
	$zd_desc  = 'الكيك والبسكويت والشيبس والشوكولاتة التركية بسعر الكرتونة لتجار التجزئة. صفِّ الأصناف حسب القسم أو العلامة، وأضف الكميات مباشرة إلى الطلبية.';
}

// الرابط الأساسي للصفحة الحالية دون الفلاتر.
$zd_base = remove_query_arg( array( 'company', 'section', 'paged', 'product-page' ) );
$zd_base = preg_replace( '#/page/\d+/?#', '/', $zd_base );

$zd_show_cats   = ! $zd_cat;
$zd_show_brands = ! $zd_brand;
$zd_cat_counts  = $zd_show_cats ? zad_facet_counts( 'cat', $zd_scope + ( $zd_filters['company'] ? array( 'brand' => $zd_filters['company'] ) : array() ) ) : array();
$zd_br_counts   = $zd_show_brands ? zad_facet_counts( 'brand', $zd_scope + ( $zd_filters['section'] ? array( 'cat' => $zd_filters['section'] ) : array() ) ) : array();
$zd_active = ( $zd_filters['section'] ? 1 : 0 ) + ( $zd_filters['company'] ? 1 : 0 );
?>
<header class="zd-archive-hero<?php echo ( $zd_brand && ! empty( $zd_brands[ $zd_brand ]['hero'] ) ) ? ' zd-archive-hero--brand' : ''; ?>">
	<div class="zd-archive-hero__media"><?php echo zad_img( $zd_image, '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<div class="zd-container zd-archive-hero__text">
		<h1 class="zd-archive-hero__title">
			<?php if ( $zd_brand && isset( $zd_brands[ $zd_brand ] ) ) : ?>
				<?php $zd_logo = zad_brand_logo( $zd_brands[ $zd_brand ], 48 ); ?>
				<?php if ( $zd_logo ) : ?>
					<span class="zd-archive-hero__logo"><?php echo $zd_logo; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php else : ?>
					<span class="zd-archive-hero__word" lang="tr"><?php echo esc_html( $zd_brands[ $zd_brand ]['latin'] ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
			<?php echo esc_html( $zd_title ); ?>
		</h1>
		<?php zad_breadcrumbs(); ?>
		<?php if ( $zd_desc ) : ?>
			<p class="zd-archive-hero__meta"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $zd_desc ), 26, '…' ) ); ?></p>
		<?php endif; ?>
	</div>
</header>

<div class="zd-drawer zd-drawer--start zd-filter-drawer" id="zd-filter-drawer" role="dialog" aria-modal="true" aria-label="تصفية الأصناف" aria-hidden="true">
	<div class="zd-drawer__head">
		<strong class="zd-drawer__title">تصفية الأصناف</strong>
		<button type="button" class="zd-icon-btn" data-zd-close aria-label="إغلاق التصفية"><?php zad_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="zd-drawer__body zd-filters">
		<?php if ( $zd_show_cats && $zd_cat_counts ) : ?>
			<nav class="zd-filters__group" aria-label="تصفية حسب القسم">
				<p class="zd-filters__label">القسم</p>
				<ul class="zd-filters__list">
					<li><a class="<?php echo ! $zd_filters['section'] ? 'is-active' : ''; ?>" href="<?php echo esc_url( $zd_filters['company'] ? add_query_arg( 'company', $zd_filters['company'], $zd_base ) : $zd_base ); ?>">الكل</a></li>
					<?php foreach ( $zd_cats as $zd_s => $zd_c ) : ?>
						<?php if ( ! empty( $zd_cat_counts[ $zd_s ] ) ) : ?>
							<?php $zd_on = $zd_filters['section'] === $zd_s; ?>
							<li><a class="<?php echo $zd_on ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'section' => $zd_on ? '' : $zd_s, 'company' => $zd_filters['company'] ) ), $zd_base ) ); ?>"<?php echo $zd_on ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $zd_c['title'] ); ?> <span><?php echo (int) $zd_cat_counts[ $zd_s ]; ?></span></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
		<?php if ( $zd_show_brands && $zd_br_counts ) : ?>
			<nav class="zd-filters__group" aria-label="تصفية حسب العلامة">
				<p class="zd-filters__label">العلامة</p>
				<ul class="zd-filters__list">
					<li><a class="<?php echo ! $zd_filters['company'] ? 'is-active' : ''; ?>" href="<?php echo esc_url( $zd_filters['section'] ? add_query_arg( 'section', $zd_filters['section'], $zd_base ) : $zd_base ); ?>">الكل</a></li>
					<?php foreach ( $zd_brands as $zd_s => $zd_b ) : ?>
						<?php if ( ! empty( $zd_br_counts[ $zd_s ] ) ) : ?>
							<?php $zd_on = $zd_filters['company'] === $zd_s; ?>
							<li><a class="<?php echo $zd_on ? 'is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'company' => $zd_on ? '' : $zd_s, 'section' => $zd_filters['section'] ) ), $zd_base ) ); ?>"<?php echo $zd_on ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $zd_b['ar'] ); ?> <span><?php echo (int) $zd_br_counts[ $zd_s ]; ?></span></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
		<?php if ( $zd_active ) : ?>
			<a class="zd-btn zd-btn--outline zd-btn--block" href="<?php echo esc_url( $zd_base ); ?>">مسح التصفية</a>
		<?php endif; ?>
		<a class="zd-btn zd-btn--dark zd-btn--block" href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">قائمة الأسعار الكاملة</a>
	</div>
</div>
