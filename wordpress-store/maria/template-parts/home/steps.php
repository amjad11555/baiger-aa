<?php
/**
 * الواجهة الرئيسية: خطوات الطلب + مشاركة الرابط.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_share = sprintf( 'حلويات تركية أصلية بالجملة للبقالات من %1$s: كيك وبسكويت وشيبس وتسالي بسعر الكرتونة 👇 %2$s', get_bloginfo( 'name' ), home_url( '/' ) );
?>
<section class="mr-section mr-steps" aria-labelledby="mr-steps-title">
	<div class="mr-container">
		<div class="mr-section__head mr-section__head--center">
			<?php echo maria_ornaments_row(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<h2 class="mr-section__title" id="mr-steps-title">طلبك جاهز في ثلاث خطوات</h2>
			<p class="mr-section__sub">صمّمنا الطلب ليكون أسهل من رسالة واتساب، من الجوال أو الكمبيوتر.</p>
		</div>
		<ol class="mr-steps__list">
			<li>
				<span class="mr-steps__num">1</span>
				<span class="mr-steps__icon"><?php maria_the_icon( 'list', '', 30 ); ?></span>
				<h3>ابحث عن أصنافك</h3>
				<p>اكتب اسم المنتج أو الشركة بالعربي أو التركي، أو افتح قائمة الطلب السريع بكل الأصناف.</p>
			</li>
			<li>
				<span class="mr-steps__num">2</span>
				<span class="mr-steps__icon"><?php maria_the_icon( 'box', '', 30 ); ?></span>
				<h3>حدّد عدد الكراتين</h3>
				<p>اضغط + بجانب كل صنف. نحفظ الكميات في سلتك ونحسب الإجمالي أمامك فوراً.</p>
			</li>
			<li>
				<span class="mr-steps__num">3</span>
				<span class="mr-steps__icon"><?php maria_the_icon( 'send', '', 30 ); ?></span>
				<h3>أرسل الطلب</h3>
				<p>اكتب اسمك وعنوانك فقط، والدفع عند الاستلام. أو أرسل الطلب برسالة واتساب جاهزة.</p>
			</li>
		</ol>
		<div class="mr-share">
			<div class="mr-share__text">
				<strong><?php maria_the_icon( 'share', '', 20 ); ?> احفظ رابط ماريا على جوالك</strong>
				<span>رابط واحد تطلب منه احتياجات رفّك في أي وقت، وشاركه مع أصحاب المحلات.</span>
			</div>
			<div class="mr-share__actions">
				<input class="mr-share__url" type="text" value="<?php echo esc_attr( home_url( '/' ) ); ?>" readonly dir="ltr" aria-label="رابط المتجر">
				<button type="button" class="mr-btn mr-btn--ghost" data-mr-copy="<?php echo esc_attr( home_url( '/' ) ); ?>"><?php maria_the_icon( 'copy', '', 18 ); ?> نسخ</button>
				<a class="mr-btn mr-btn--wa" href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( $mr_share ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 18 ); ?> مشاركة</a>
			</div>
		</div>
	</div>
</section>
