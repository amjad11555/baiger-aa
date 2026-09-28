<?php
/**
 * الواجهة الرئيسية: لماذا نحن.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_items = array(
	array( 'tag', 'أسعار جملة حقيقية', 'سعر الكرتونة واضح أمامك قبل الطلب، مع عروض أسبوعية على الأصناف الأكثر طلباً.' ),
	array( 'box', 'من كرتونة واحدة', 'نوّع رفّك بلا مخاطرة: اطلب كرتونة واحدة من كل صنف تريد تجربته.' ),
	array( 'truck', 'توصيل سريع', sprintf( 'نوصل الطلب إلى باب محلّك في %s وباقي الولايات التركية.', maria_opt( 'city' ) ) ),
	array( 'wallet', 'الدفع عند الاستلام', 'ادفع نقداً عند وصول البضاعة، ولا حاجة لأي بطاقة بنكية.' ),
	array( 'shield', 'منتجات أصلية بصلاحية حديثة', 'نختار دفعات إنتاج حديثة تناسب مدة العرض على الرف.' ),
	array( 'whatsapp', 'دعم مباشر عبر واتساب', 'اسأل، عدّل طلبك، أو اطلب منتجاً غير متوفر برسالة واحدة.' ),
);
?>
<section class="mr-section mr-why" aria-labelledby="mr-why-title">
	<div class="mr-container">
		<div class="mr-section__head">
			<span class="mr-kicker"><?php maria_the_icon( 'sparkle', '', 16 ); ?> لماذا يختارنا أصحاب البقالات؟</span>
			<h2 class="mr-section__title" id="mr-why-title">شريك رفّك من أول كرتونة</h2>
		</div>
		<ul class="mr-why__grid">
			<?php foreach ( $mr_items as $mr_it ) : ?>
				<li class="mr-why__item">
					<span class="mr-why__icon"><?php maria_the_icon( $mr_it[0], '', 26 ); ?></span>
					<h3><?php echo esc_html( $mr_it[1] ); ?></h3>
					<p><?php echo esc_html( $mr_it[2] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
