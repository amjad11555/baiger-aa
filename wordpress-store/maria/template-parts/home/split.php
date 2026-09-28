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
			<h2>لم تجد ما تبحث عنه؟ <span>نؤمّنه لك</span></h2>
			<p>اكتب لنا اسم المنتج والكمية، حتى لو لم يكن في المتجر، ونعود إليك بالسعر والتوفر. نريدك أن تطلب كل احتياجات رفّك من مكان واحد.</p>
			<ul class="mr-checklist">
				<li>أي علامة تركية أو مستوردة</li>
				<li>رد سريع بالسعر والتوفر</li>
				<li>متابعة شخصية عبر واتساب</li>
			</ul>
			<a class="mr-btn mr-btn--accent mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر <?php maria_the_icon( 'arrow-left', '', 18 ); ?></a>
		</article>
		<article class="mr-split__card mr-split__card--export">
			<span class="mr-split__icon"><?php maria_the_icon( 'globe', '', 34 ); ?></span>
			<h2>تستورد من تركيا؟ <span>نشحن إليك</span></h2>
			<p>للمستوردين والموزعين وسلاسل السوبرماركت: طبليات مختلطة أو حاويات 20 و40 قدماً من إيتي وأولكر وبونوتشي، مع مستندات التخليص كاملة.</p>
			<ul class="mr-checklist">
				<li>حاوية واحدة من عدة علامات</li>
				<li>شهادات المنشأ والحلال</li>
				<li><span dir="ltr">EXW · FOB · CIF</span></li>
			</ul>
			<a class="mr-btn mr-btn--primary mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'export' ) ); ?>">اطلب عرض سعر للتصدير <?php maria_the_icon( 'arrow-left', '', 18 ); ?></a>
		</article>
	</div>
</section>
