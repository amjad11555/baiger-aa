<?php
/**
 * الواجهة الرئيسية: لافتتان متجاورتان بأسلوب Kalles (التصدير + طلب التوريد الخاص).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_banners = array(
	array( 'seg-export', 'للمستوردين والموزعين', 'تصدير بالحاويات', 'حاويات 20 و40 قدماً وطبليات مختلطة، مع مستندات التخليص كاملة.', 'اطلب عرض سعر', zad_page_url( 'export' ) ),
	array( 'sourcing', 'صنف غير موجود في القائمة؟', 'نؤمّنه لك من المصدر', 'أرسل اسم الصنف والكمية، ونعود إليك بالسعر وموعد التوريد.', 'طلب توريد خاص', zad_page_url( 'special_request' ) ),
);
?>
<section class="zd-section zd-section--tight zd-banners" aria-label="خدمات الجملة">
	<div class="zd-container zd-banners__grid">
		<?php foreach ( $zd_banners as $zd_b ) : ?>
			<a class="zd-banner" href="<?php echo esc_url( $zd_b[5] ); ?>">
				<span class="zd-banner__media"><?php echo zad_img( $zd_b[0], '', array( 'sizes' => '(min-width: 1024px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="zd-banner__content">
					<span class="zd-banner__kicker"><?php echo esc_html( $zd_b[1] ); ?></span>
					<span class="zd-banner__title"><?php echo esc_html( $zd_b[2] ); ?></span>
					<span class="zd-banner__text"><?php echo esc_html( $zd_b[3] ); ?></span>
					<span class="zd-banner__cta"><?php echo esc_html( $zd_b[4] ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
