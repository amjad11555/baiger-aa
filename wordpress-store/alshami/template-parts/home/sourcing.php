<?php
/**
 * الواجهة الرئيسية: طلب توريد خاص (صنف غير موجود في القائمة).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sh-section sh-sourcing" aria-labelledby="sh-sourcing-title">
	<div class="sh-container sh-split">
		<div class="sh-split__content">
			<p class="sh-eyebrow">طلب توريد خاص</p>
			<h2 class="sh-head__title" id="sh-sourcing-title">صنف غير موجود في القائمة؟ نؤمّنه لك من المصدر</h2>
			<p>أرسل اسم الصنف والكمية التي تحتاجها، سواء كان من علامة تركية أو مستوردة. يتواصل معك فريق المشتريات بالسعر والتوفر وموعد التوريد، لتطلب كل احتياجات متجرك من مورّد واحد.</p>
			<dl class="sh-facts">
				<div><dt>الرد</dt><dd>خلال يوم عمل</dd></div>
				<div><dt>الحد الأدنى</dt><dd>حسب الصنف والمصدر</dd></div>
			</dl>
			<a class="sh-btn sh-btn--primary sh-btn--lg" href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">أرسل طلب توريد</a>
		</div>
		<div class="sh-split__media"><?php echo shami_img( 'sourcing', '', array( 'sizes' => '(min-width: 1024px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	</div>
</section>
