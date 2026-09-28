<?php
/**
 * الواجهة الرئيسية: لماذا نحن.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_items = array(
	array( 'tag', 'سعر جملة واضح', 'ترى سعر الكرتونة وسعر القطعة قبل أن تطلب، دون مفاجآت.' ),
	array( 'box', 'ابدأ بكرتونة واحدة', 'جرّب صنفاً جديداً دون مخاطرة، ونوّع رفّك كما تشاء.' ),
	array( 'truck', 'توصيل إلى محلّك', sprintf( 'نوصل طلبك في %s خلال يوم أو يومين، وإلى باقي الولايات حسب الموقع.', maria_opt( 'city' ) ) ),
	array( 'wallet', 'الدفع عند الاستلام', 'ادفع نقداً عند وصول البضاعة، دون الحاجة إلى بطاقة بنكية.' ),
	array( 'shield', 'أصلي وصلاحيته حديثة', 'نختار دفعات إنتاج حديثة تناسب مدة العرض على الرف.' ),
	array( 'whatsapp', 'فريق يرد عليك', 'اسأل، عدّل طلبك، أو اطلب صنفاً جديداً برسالة واتساب.' ),
);
?>
<section class="mr-section mr-why" aria-labelledby="mr-why-title">
	<div class="mr-container">
		<div class="mr-section__head">
			<span class="mr-kicker">لماذا ماريا؟</span>
			<h2 class="mr-section__title" id="mr-why-title">شريك يفهم عمل البقالة</h2>
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
