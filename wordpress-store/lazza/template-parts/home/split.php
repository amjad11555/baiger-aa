<?php
/**
 * الواجهة الرئيسية: طلب منتج غير متوفر + الجملة الدولية.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="lz-section lz-split" aria-label="خدمات إضافية">
	<div class="lz-container lz-split__grid">
		<article class="lz-split__card lz-split__card--request lz-reveal">
			<span class="lz-split__icon"><?php lazza_the_icon( 'search', '', 34 ); ?></span>
			<h2>ما لقيت المنتج؟ <span>نأمّنه لك</span></h2>
			<p>أرسل لنا اسم أي منتج تحتاجه بقالتك — حتى لو لم يكن في المتجر — ونوفره لك بسعر الجملة. هدفنا أن تطلب كل احتياجات رفّك من مكان واحد.</p>
			<ul class="lz-checklist lz-checklist--light">
				<li>أي علامة تركية أو مستوردة</li>
				<li>رد سريع بالتوفر والسعر</li>
				<li>متابعة الطلب عبر واتساب</li>
			</ul>
			<a class="lz-btn lz-btn--gold lz-btn--lg" href="<?php echo esc_url( lazza_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر <?php lazza_the_icon( 'arrow-left', '', 18 ); ?></a>
		</article>
		<article class="lz-split__card lz-split__card--export lz-reveal">
			<span class="lz-split__icon"><?php lazza_the_icon( 'globe', '', 34 ); ?></span>
			<h2>جملة خارج تركيا؟ <span>نصدّر إليك</span></h2>
			<p>للمستوردين والموزعين وسلاسل السوبرماركت: حاويات 20 و40 قدم أو طبليات مختلطة من إيتي وأولكر وبونوتشي، مع المستندات اللازمة للتخليص.</p>
			<ul class="lz-checklist">
				<li>حاوية مختلطة من عدة علامات</li>
				<li>شهادات المنشأ والحلال</li>
				<li>EXW / FOB / CIF</li>
			</ul>
			<a class="lz-btn lz-btn--primary lz-btn--lg" href="<?php echo esc_url( lazza_page_url( 'export' ) ); ?>">اطلب عرض سعر للتصدير <?php lazza_the_icon( 'arrow-left', '', 18 ); ?></a>
		</article>
	</div>
</section>
