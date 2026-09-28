<?php
/**
 * الواجهة الرئيسية: عروض الأسبوع (لافتة صفراء بعدّاد تنازلي + شريط المنتجات).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_ids = array_values( array_filter( array_map( 'intval', wc_get_product_ids_on_sale() ) ) );
if ( ! $mr_ids ) {
	return;
}
?>
<section class="mr-section mr-flash-sec" aria-labelledby="mr-offers-title">
	<div class="mr-container">
		<a class="mr-flash" href="<?php echo esc_url( maria_cat_url( 'offers' ) ); ?>">
			<h2 class="mr-flash__title" id="mr-offers-title">عروض الأسبوع</h2>
			<span class="mr-flash__sub">خصومات على سعر الكرتونة، تنتهي خلال</span>
			<span class="mr-countdown" data-mr-countdown="<?php echo esc_attr( maria_offer_end_iso() ); ?>" role="timer" aria-label="الوقت المتبقي على انتهاء العروض">
				<span><b data-u="d">0</b><small>يوم</small></span>
				<span><b data-u="h">00</b><small>ساعة</small></span>
				<span><b data-u="m">00</b><small>دقيقة</small></span>
				<span><b data-u="s">00</b><small>ثانية</small></span>
			</span>
		</a>
		<?php
		maria_product_rail(
			array(
				'include' => $mr_ids,
				'limit'   => 12,
				'orderby' => 'menu_order',
				'order'   => 'ASC',
			)
		);
		?>
		<p class="mr-center"><a class="mr-btn mr-btn--ghost" href="<?php echo esc_url( maria_cat_url( 'offers' ) ); ?>">جميع العروض</a></p>
	</div>
</section>
