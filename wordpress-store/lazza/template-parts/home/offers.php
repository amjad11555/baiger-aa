<?php
/**
 * الواجهة الرئيسية: عروض الأسبوع.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_ids = array_values( array_filter( array_map( 'intval', wc_get_product_ids_on_sale() ) ) );
if ( ! $lz_ids ) {
	return;
}
?>
<section class="lz-section lz-offers" aria-labelledby="lz-offers-title">
	<div class="lz-container">
		<div class="lz-offers__head">
			<div>
				<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'fire', '', 16 ); ?> عروض الأسبوع</span>
				<h2 class="lz-section__title" id="lz-offers-title">خصومات حقيقية على سعر الكرتونة</h2>
			</div>
			<div class="lz-countdown" data-lz-countdown="<?php echo esc_attr( lazza_offer_end_iso() ); ?>" aria-label="الوقت المتبقي على انتهاء العروض">
				<span><b data-u="d">0</b><small>يوم</small></span>
				<span><b data-u="h">00</b><small>ساعة</small></span>
				<span><b data-u="m">00</b><small>دقيقة</small></span>
				<span><b data-u="s">00</b><small>ثانية</small></span>
			</div>
			<a class="lz-btn lz-btn--gold" href="<?php echo esc_url( lazza_cat_url( 'offers' ) ); ?>">كل العروض <?php lazza_the_icon( 'arrow-left', '', 18 ); ?></a>
		</div>
		<?php
		lazza_product_grid(
			array(
				'include' => $lz_ids,
				'limit'   => 8,
				'orderby' => 'menu_order',
				'order'   => 'ASC',
			),
			'lz-grid--scroll'
		);
		?>
	</div>
</section>
