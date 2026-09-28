<?php
/**
 * الواجهة الرئيسية: لماذا الشامي (صورة كبيرة + أربع نقاط مرقّمة).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_points = array(
	array( 'أسعار جملة معلنة', 'سعر الكرتونة وسعر القطعة ظاهران لكل صنف قبل الطلب، فتحسب هامشك بدقة ولا تفاجئك الفاتورة.' ),
	array( 'استمرارية في التوريد', 'مخزون دائم من الأصناف الأعلى دوراناً، حتى لا يفرغ رفّك في مواسم الطلب المرتفع.' ),
	array( 'صلاحيات حديثة ومراجَعة', 'نختار دفعات إنتاج حديثة، ونراجع تواريخ الصلاحية قبل شحن كل طلبية.' ),
	array( 'متابعة شخصية لحسابك', 'مسؤول مبيعات يعرف متجرك، يقترح الأصناف المناسبة ويتابع طلبياتك وعروضك.' ),
);
?>
<section class="sh-section sh-why" aria-labelledby="sh-why-title">
	<div class="sh-container sh-why__grid">
		<div class="sh-why__media">
			<?php echo shami_img( 'quality', '', array( 'sizes' => '(min-width: 1024px) 45vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="sh-why__content">
			<p class="sh-eyebrow">لماذا الشامي</p>
			<h2 class="sh-head__title" id="sh-why-title">شريك توريد يُعتمد عليه، لا مجرد بائع</h2>
			<ol class="sh-why__list">
				<?php foreach ( $sh_points as $sh_i => $sh_p ) : ?>
					<li>
						<span class="sh-why__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $sh_i + 1 ) ); ?></span>
						<div>
							<h3><?php echo esc_html( $sh_p[0] ); ?></h3>
							<p><?php echo esc_html( $sh_p[1] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>
