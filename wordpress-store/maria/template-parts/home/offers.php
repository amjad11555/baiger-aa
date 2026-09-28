<?php
/**
 * الواجهة الرئيسية: عروض الأسبوع.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_ids = array_values( array_filter( array_map( 'intval', wc_get_product_ids_on_sale() ) ) );
if ( ! $mr_ids ) {
	return;
}
?>
<section class="mr-section mr-offers" aria-labelledby="mr-offers-title">
	<div class="mr-container">
		<div class="mr-offers__box">
			<div class="mr-offers__head">
				<div>
					<span class="mr-kicker--accent"><?php maria_the_icon( 'percent', '', 16 ); ?> عروض الأسبوع</span>
					<h2 class="mr-section__title" id="mr-offers-title">خصومات على سعر الكرتونة</h2>
				</div>
				<div class="mr-countdown" data-mr-countdown="<?php echo esc_attr( maria_offer_end_iso() ); ?>" aria-label="الوقت المتبقي على انتهاء العروض">
					<span><b data-u="d">0</b><small>يوم</small></span>
					<span><b data-u="h">00</b><small>ساعة</small></span>
					<span><b data-u="m">00</b><small>دقيقة</small></span>
					<span><b data-u="s">00</b><small>ثانية</small></span>
				</div>
				<a class="mr-link" href="<?php echo esc_url( maria_cat_url( 'offers' ) ); ?>">كل العروض <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
			</div>
			<?php
			maria_product_grid(
				array(
					'include' => $mr_ids,
					'limit'   => 8,
					'orderby' => 'menu_order',
					'order'   => 'ASC',
				),
				'mr-grid--rail'
			);
			?>
		</div>
	</div>
</section>
