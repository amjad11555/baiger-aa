<?php
/**
 * الواجهة الرئيسية: مزايا الطلب من ماريا (قائمة بطاقات بأيقونات ملوّنة).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_items = array(
	array( 'truck', '#EB1C24', 'توصيل إلى باب محلّك', sprintf( 'نوصل طلبك في %s خلال يوم أو يومين من تأكيده، وإلى باقي الولايات حسب الموقع والكمية.', maria_opt( 'city' ) ) ),
	array( 'wallet', '#FF7B19', 'الدفع عند الاستلام', 'ادفع نقداً عند وصول البضاعة، أو بتحويل بنكي للعملاء الدائمين. لا تحتاج إلى بطاقة بنكية.' ),
	array( 'box', '#8C51FF', 'ابدأ من كرتونة واحدة', 'جرّب أصنافاً جديدة دون مخاطرة، واطلب كرتونة واحدة فقط من كل صنف.' ),
	array( 'shield', '#006C78', 'منتجات أصلية بصلاحية حديثة', 'كل المنتجات أصلية من إيتي وأولكر وبونوتشي، ونختار دفعات حديثة تناسب مدة العرض على الرف.' ),
	array( 'whatsapp', '#1E7B3C', 'فريق يرد عليك', 'اسأل، أو عدّل طلبك، أو اطلب صنفاً جديداً برسالة واتساب خلال ساعات العمل.' ),
);
?>
<section class="mr-section mr-section--white mr-why" aria-labelledby="mr-why-title">
	<div class="mr-container">
		<h2 class="screen-reader-text" id="mr-why-title">لماذا تطلب من ماريا؟</h2>
		<ul class="mr-feats">
			<?php foreach ( $mr_items as $mr_it ) : ?>
				<li class="mr-feat" style="--c:<?php echo esc_attr( $mr_it[1] ); ?>">
					<span class="mr-feat__icon" aria-hidden="true"><?php maria_the_icon( $mr_it[0], '', 34 ); ?></span>
					<span class="mr-feat__text">
						<strong class="mr-feat__title"><?php echo esc_html( $mr_it[2] ); ?></strong>
						<span class="mr-feat__desc"><?php echo esc_html( $mr_it[3] ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
