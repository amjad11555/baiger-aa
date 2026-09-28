<?php
/**
 * الواجهة الرئيسية: طلب منتج غير متوفر + الجملة الدولية.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mr-section mr-split" aria-label="خدمات إضافية">
	<div class="mr-container mr-split__grid">
		<article class="mr-split__card mr-split__card--request">
			<span class="mr-split__icon"><?php maria_the_icon( 'search', '', 34 ); ?></span>
			<h2>ما لقيت المنتج؟ <span>نأمّنه لك</span></h2>
			<p>أرسل لنا اسم أي منتج تحتاجه بقالتك — حتى لو لم يكن في المتجر — ونوفره لك بسعر الجملة. هدفنا أن تطلب كل احتياجات رفّك من مكان واحد.</p>
			<ul class="mr-checklist">
				<li>أي علامة تركية أو مستوردة</li>
				<li>رد سريع بالتوفر والسعر</li>
				<li>متابعة الطلب عبر واتساب</li>
			</ul>
			<a class="mr-btn mr-btn--accent mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر <?php maria_the_icon( 'arrow-left', '', 18 ); ?></a>
		</article>
		<article class="mr-split__card mr-split__card--export">
			<span class="mr-split__icon"><?php maria_the_icon( 'globe', '', 34 ); ?></span>
			<h2>جملة خارج تركيا؟ <span>نصدّر إليك</span></h2>
			<p>للمستوردين والموزعين وسلاسل السوبرماركت: حاويات 20 و40 قدم أو طبليات مختلطة من إيتي وأولكر وبونوتشي، مع المستندات اللازمة للتخليص.</p>
			<ul class="mr-checklist">
				<li>حاوية مختلطة من عدة علامات</li>
				<li>شهادات المنشأ والحلال</li>
				<li>EXW / FOB / CIF</li>
			</ul>
			<a class="mr-btn mr-btn--primary mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'export' ) ); ?>">اطلب عرض سعر للتصدير <?php maria_the_icon( 'arrow-left', '', 18 ); ?></a>
		</article>
	</div>
</section>
