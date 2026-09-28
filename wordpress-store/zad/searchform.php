<?php
/**
 * نموذج البحث الذكي (منتجات + شركات + أقسام مع إضافة فورية للسلة).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_uid = wp_unique_id( 'zd-search-' );
?>
<form role="search" method="get" class="zd-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-zd-search>
	<label class="screen-reader-text" for="<?php echo esc_attr( $zd_uid ); ?>">ابحث عن منتج أو شركة أو قسم</label>
	<input type="search" id="<?php echo esc_attr( $zd_uid ); ?>" class="zd-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="ابحث عن منتج، شركة أو قسم…" autocomplete="off" enterkeyhint="search" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $zd_uid ); ?>-list">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="button" class="zd-search__clear" aria-label="مسح البحث" hidden><?php zad_the_icon( 'close', '', 18 ); ?></button>
	<button type="submit" class="zd-search__btn" aria-label="بحث"><?php zad_the_icon( 'search', 'zd-search__icon', 24 ); ?></button>
	<div class="zd-search__results" id="<?php echo esc_attr( $zd_uid ); ?>-list" hidden></div>
</form>
