<?php
/**
 * الواجهة الرئيسية: شريط التصدير بصورة الميناء.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sh-band sh-band--export" aria-labelledby="sh-export-title">
	<div class="sh-band__media"><?php echo shami_img( 'seg-export', '', array( 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<div class="sh-container sh-band__inner">
		<div class="sh-band__content">
			<p class="sh-eyebrow sh-eyebrow--light">التصدير والجملة الدولية</p>
			<h2 class="sh-band__title" id="sh-export-title">حاوية مختلطة من إيتي وأولكر وبونوتشي، تصل إلى سوقك</h2>
			<ul class="sh-band__list">
				<li>حاوية 20 أو 40 قدماً، أو طبليات مختلطة من عدة علامات</li>
				<li>شهادات المنشأ والحلال والفاتورة التجارية وقائمة التعبئة</li>
				<li>شروط شحن مرنة <bdi dir="ltr">EXW · FOB · CIF</bdi></li>
			</ul>
			<a class="sh-btn sh-btn--accent sh-btn--lg" href="<?php echo esc_url( shami_page_url( 'export' ) ); ?>">اطلب عرض سعر للتصدير</a>
		</div>
	</div>
</section>
