<?php
/**
 * نموذج البحث (مع اقتراحات فورية).
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_uid = wp_unique_id( 'lz-search-' );
?>
<form role="search" method="get" class="lz-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-lz-search>
	<label class="screen-reader-text" for="<?php echo esc_attr( $lz_uid ); ?>">ابحث عن منتج</label>
	<?php lazza_the_icon( 'search', 'lz-search__icon', 20 ); ?>
	<input type="search" id="<?php echo esc_attr( $lz_uid ); ?>" class="lz-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="ابحث: بسكريم، براوني، شيبس، Popkek…" autocomplete="off" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $lz_uid ); ?>-list">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="lz-search__btn">بحث</button>
	<div class="lz-search__results" id="<?php echo esc_attr( $lz_uid ); ?>-list" role="listbox" hidden></div>
</form>
