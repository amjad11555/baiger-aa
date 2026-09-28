<?php
/**
 * الواجهة الرئيسية: لافتتان إعلانيتان (طلب منتج غير متوفر + التصدير).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="mr-section mr-section--white mr-promos" aria-label="خدمات إضافية">
	<div class="mr-container mr-promos__grid">
		<a class="mr-promo mr-promo--purple" href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">
			<span class="mr-promo__icon" aria-hidden="true"><?php maria_the_icon( 'search', '', 40 ); ?></span>
			<span class="mr-promo__text">
				<strong class="mr-promo__title">لم تجد ما تبحث عنه؟</strong>
				<span class="mr-promo__sub">اكتب لنا اسم المنتج والكمية، ونؤمّنه لبقالتك بسعر الجملة من أي علامة تركية أو مستوردة.</span>
				<span class="mr-promo__cta">اطلب منتجاً غير متوفر <?php maria_the_icon( 'arrow-left', '', 18 ); ?></span>
			</span>
		</a>
		<a class="mr-promo mr-promo--teal" href="<?php echo esc_url( maria_page_url( 'export' ) ); ?>">
			<span class="mr-promo__icon" aria-hidden="true"><?php maria_the_icon( 'globe', '', 40 ); ?></span>
			<span class="mr-promo__text">
				<strong class="mr-promo__title">تستورد من تركيا؟ نشحن إليك</strong>
				<span class="mr-promo__sub">طبليات مختلطة أو حاويات 20 و40 قدماً من إيتي وأولكر وبونوتشي، مع مستندات التخليص كاملة.</span>
				<span class="mr-promo__cta">اطلب عرض سعر للتصدير <?php maria_the_icon( 'arrow-left', '', 18 ); ?></span>
			</span>
		</a>
	</div>
</section>
