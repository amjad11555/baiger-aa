<?php
/**
 * الواجهة الرئيسية: التوصيل في إسطنبول (صورة + مناطق التوصيل + الدفع حسب المنطقة)،
 * مع رابطين للتصدير وطلب صنف غير موجود.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_districts = array();
foreach ( zad_districts() as $zd_slug => $zd_d ) {
	if ( 'other' !== $zd_slug ) {
		$zd_districts[] = $zd_d;
	}
}
?>
<section class="zd-section zd-deliver" aria-labelledby="zd-deliver-title">
	<div class="zd-container zd-deliver__grid">
		<div class="zd-deliver__media"><?php echo zad_img( 'banner-delivery', 'مندوب بسكاتو يسلّم كراتين الجملة عند باب بقالة في إسطنبول', array( 'sizes' => '(min-width: 1024px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="zd-deliver__copy">
			<p class="zd-deliver__kicker">التوصيل والدفع</p>
			<h2 class="zd-deliver__title" id="zd-deliver-title">نوصل طلبيتك إلى باب محلك في كل مناطق إسطنبول</h2>
			<ul class="zd-deliver__points">
				<li><?php zad_the_icon( 'truck', '', 20 ); ?><span><b>داخل إسطنبول:</b> توصيل مجاني خلال 24 إلى 48 ساعة، والدفع نقداً عند الاستلام.</span></li>
				<li><?php zad_the_icon( 'globe', '', 20 ); ?><span><b>خارج إسطنبول وخارج تركيا:</b> شحن إلى ولايتك أو بلدك، والدفع بتحويل بنكي إلى حسابنا الرسمي.</span></li>
				<li><?php zad_the_icon( 'box', '', 20 ); ?><span>نسلّم عند الباب أو نرتّب الكراتين على رفوفك، كما تختار عند تأكيد الطلبية.</span></li>
			</ul>
			<p class="zd-deliver__areas-title">نوصل إلى:</p>
			<ul class="zd-deliver__areas">
				<?php foreach ( $zd_districts as $zd_d ) : ?>
					<li><?php echo esc_html( $zd_d[0] ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p class="zd-deliver__links">
				<a class="zd-link" href="<?php echo esc_url( zad_page_url( 'export' ) ); ?>">تصدير بالحاويات خارج تركيا</a>
				<a class="zd-link" href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">صنف غير موجود؟ نؤمّنه لك</a>
			</p>
		</div>
	</div>
</section>
