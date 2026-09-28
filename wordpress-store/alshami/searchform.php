<?php
/**
 * نموذج البحث الذكي (منتجات + شركات + أقسام مع إضافة فورية للسلة).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_uid = wp_unique_id( 'sh-search-' );
?>
<form role="search" method="get" class="sh-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-sh-search>
	<label class="screen-reader-text" for="<?php echo esc_attr( $sh_uid ); ?>">ابحث عن منتج أو شركة أو قسم</label>
	<input type="search" id="<?php echo esc_attr( $sh_uid ); ?>" class="sh-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="ابحث عن منتج، شركة أو قسم…" autocomplete="off" enterkeyhint="search" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="<?php echo esc_attr( $sh_uid ); ?>-list">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="button" class="sh-search__clear" aria-label="مسح البحث" hidden><?php shami_the_icon( 'close', '', 18 ); ?></button>
	<button type="submit" class="sh-search__btn" aria-label="بحث"><?php shami_the_icon( 'search', 'sh-search__icon', 24 ); ?></button>
	<div class="sh-search__results" id="<?php echo esc_attr( $sh_uid ); ?>-list" hidden></div>
</form>
