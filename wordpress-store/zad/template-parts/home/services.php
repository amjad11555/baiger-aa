<?php
/**
 * الواجهة الرئيسية: صف الخدمات بأسلوب Kalles (أربعة أعمدة نصية بلا أيقونات).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_items = array(
	array( '24–48 ساعة', 'توصيل مجاني إلى محلك', 'إلى باب المحل أو مرتّبة على الرف، داخل ' . zad_opt( 'city' ) . ' وباقي الولايات وفق جدول التوزيع.' ),
	array( 'عند الاستلام', 'دفع مرن', 'نقداً عند الاستلام أو بالتحويل للحسابات الدائمة.' ),
	array( 'كرتونة → حاوية', 'أي كمية', 'اطلب من كرتونة واحدة لكل صنف حتى حاوية كاملة.' ),
	array( 'فاتورة نظامية', 'أصناف أصلية', 'دفعات إنتاج حديثة ومراجعة الصلاحية قبل كل شحنة.' ),
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
