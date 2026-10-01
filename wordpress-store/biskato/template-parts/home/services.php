<?php
/**
 * الواجهة الرئيسية: صف الخدمات بأسلوب Kalles (أربعة أعمدة نصية بلا أيقونات).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_disc  = function_exists( 'zad_brand_discount' ) ? array( zad_brand_discount( 'eti' ), zad_brand_discount( 'ulker' ) ) : array( 0, 0 );
$zd_items = array(
	array( '24–48 ساعة', 'توصيل مجاني داخل إسطنبول', 'إلى باب المحل أو مرتّبة على الرف. وللولايات الأخرى وخارج تركيا شحن حسب الاتفاق.' ),
	array( 'نقداً', 'الدفع عند الاستلام', 'داخل إسطنبول تدفع للمندوب حين تصل الطلبية، وخارجها بتحويل بنكي.' ),
	zad_min_cartons() > 0
		? array( zad_min_cartons() . ' كرتونة', 'طلبية مشكّلة', 'الحد الأدنى ' . zad_min_cartons() . ' كرتونة من أي أصناف تختارها، حتى حاوية كاملة.' )
		: array( 'كرتونة → حاوية', 'أي كمية', 'اطلب من كرتونة واحدة لكل صنف حتى حاوية كاملة.' ),
	( $zd_disc[0] > 0 || $zd_disc[1] > 0 )
		? array( 'خصم دائم', 'إيتي وأولكر', trim( ( $zd_disc[0] > 0 ? 'خصم ' . ( 0 + $zd_disc[0] ) . '% على كل أصناف إيتي' : '' ) . ( $zd_disc[0] > 0 && $zd_disc[1] > 0 ? '، و' : '' ) . ( $zd_disc[1] > 0 ? ( $zd_disc[0] > 0 ? '' : 'خصم ' ) . ( 0 + $zd_disc[1] ) . '% على أولكر' : '' ) ) . '.' )
		: array( 'فاتورة نظامية', 'أصناف أصلية', 'دفعات إنتاج حديثة ومراجعة الصلاحية قبل كل شحنة.' ),
);
?>
<section class="zd-services" aria-label="مزايا التعامل مع <?php bloginfo( 'name' ); ?>">
	<div class="zd-container">
		<ul class="zd-services__grid">
			<?php foreach ( $zd_items as $zd_it ) : ?>
				<li class="zd-service">
					<p class="zd-service__big"><?php echo esc_html( $zd_it[0] ); ?></p>
					<p class="zd-service__title"><?php echo esc_html( $zd_it[1] ); ?></p>
					<p class="zd-service__text"><?php echo esc_html( $zd_it[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
