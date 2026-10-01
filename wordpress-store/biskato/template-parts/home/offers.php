<?php
/**
 * الواجهة الرئيسية: عروض الجملة الحالية (شبكة بعنوان في الوسط).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_sale_ids = array_values( array_filter( array_map( 'intval', wc_get_product_ids_on_sale() ) ) );
if ( ! $zd_sale_ids ) {
	return;
}
?>
<section class="zd-section zd-offers" aria-labelledby="zd-offers-title">
	<div class="zd-container">
		<?php zad_section_head( 'عروض الجملة', 'خصم دائم على أصناف إيتي وأولكر، وعروض الأسبوع', 'zd-offers-title' ); ?>
		<?php
		zad_product_grid(
			array(
				'include' => $zd_sale_ids,
				'limit'   => 8,
				'orderby' => 'menu_order',
				'order'   => 'ASC',
			),
			'zd-grid--home'
		);
		?>
		<p class="zd-center"><a class="zd-btn zd-btn--outline" href="<?php echo esc_url( zad_cat_url( 'offers' ) ); ?>">كل العروض</a></p>
	</div>
</section>
