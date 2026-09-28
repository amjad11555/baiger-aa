<?php
/**
 * الواجهة الرئيسية: لماذا نحن.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_items = array(
	array( 'tag', 'أسعار جملة حقيقية', 'سعر الكرتونة واضح أمامك قبل الطلب، مع عروض أسبوعية على الأصناف الأكثر طلباً.' ),
	array( 'box', 'من كرتونة واحدة', 'نوّع رفّك بلا مخاطرة: اطلب كرتونة واحدة من كل صنف تريد تجربته.' ),
	array( 'truck', 'توصيل سريع', sprintf( 'نوصل الطلب إلى باب محلّك في %s وباقي الولايات التركية.', lazza_opt( 'city' ) ) ),
	array( 'wallet', 'الدفع عند الاستلام', 'ادفع نقداً عند وصول البضاعة، ولا حاجة لأي بطاقة بنكية.' ),
	array( 'shield', 'منتجات أصلية بصلاحية حديثة', 'نختار دفعات إنتاج حديثة تناسب مدة العرض على الرف.' ),
	array( 'whatsapp', 'دعم مباشر عبر واتساب', 'اسأل، عدّل طلبك، أو اطلب منتجاً غير متوفر برسالة واحدة.' ),
);
?>
<section class="lz-section lz-why" aria-labelledby="lz-why-title">
	<div class="lz-container">
		<div class="lz-section__head">
			<span class="lz-kicker"><?php lazza_the_icon( 'sparkle', '', 16 ); ?> لماذا يختارنا أصحاب البقالات؟</span>
			<h2 class="lz-section__title" id="lz-why-title">شريك رفّك من أول كرتونة</h2>
		</div>
		<ul class="lz-why__grid">
			<?php foreach ( $lz_items as $lz_it ) : ?>
				<li class="lz-why__item lz-reveal">
					<span class="lz-why__icon"><?php lazza_the_icon( $lz_it[0], '', 26 ); ?></span>
					<h3><?php echo esc_html( $lz_it[1] ); ?></h3>
					<p><?php echo esc_html( $lz_it[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
