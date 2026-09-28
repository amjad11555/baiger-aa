<?php
/**
 * نموذج البحث الذكي (منتجات + شركات + أقسام مع إضافة فورية للسلة).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_uid = wp_unique_id( 'mr-search-' );
?>
<form role="search" method="get" class="mr-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-mr-search>
	<label class="screen-reader-text" for="<?php echo esc_attr( $mr_uid ); ?>">ابحث عن منتج أو شركة أو قسم</label>
	<input type="search" id="<?php echo esc_attr( $mr_uid ); ?>" class="mr-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="ابحث عن منتج، شركة أو قسم…" autocomplete="off" enterkeyhint="search" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $mr_uid ); ?>-list">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="button" class="mr-search__clear" aria-label="مسح البحث" hidden><?php maria_the_icon( 'close', '', 18 ); ?></button>
	<button type="submit" class="mr-search__btn" aria-label="بحث"><?php maria_the_icon( 'search', 'mr-search__icon', 24 ); ?></button>
	<div class="mr-search__results" id="<?php echo esc_attr( $mr_uid ); ?>-list" hidden></div>
</form>
