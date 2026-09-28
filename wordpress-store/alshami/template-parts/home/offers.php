<?php
/**
 * الواجهة الرئيسية: عروض الجملة الحالية.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_sale_ids = array_values( array_filter( array_map( 'intval', wc_get_product_ids_on_sale() ) ) );
if ( ! $sh_sale_ids ) {
	return;
}
?>
<section class="sh-section sh-section--sand sh-offers" aria-labelledby="sh-offers-title">
	<div class="sh-container">
		<div class="sh-head">
			<div class="sh-head__text">
				<p class="sh-eyebrow">عروض الجملة</p>
				<h2 class="sh-head__title" id="sh-offers-title">أسعار خاصة على كميات محدودة</h2>
				<p class="sh-head__sub">خصومات على سعر الكرتونة لأصناف مختارة، تتجدد أسبوعياً وتسري حتى نفاد الكمية المخصصة للعرض.</p>
			</div>
			<a class="sh-link" href="<?php echo esc_url( shami_cat_url( 'offers' ) ); ?>">كل العروض</a>
		</div>
		<?php
		shami_product_grid(
			array(
				'include' => $sh_sale_ids,
				'limit'   => 4,
				'orderby' => 'menu_order',
				'order'   => 'ASC',
			),
			'sh-grid--home'
		);
		?>
	</div>
</section>
