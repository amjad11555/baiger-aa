<?php
/**
 * الواجهة الرئيسية: خطوات الطلب + مشاركة الرابط.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_share = sprintf( 'اطلب احتياجات بقالتك من الكيك والبسكويت والشيبس والتسالي بالجملة من %1$s 👇 %2$s', get_bloginfo( 'name' ), home_url( '/' ) );
?>
<section class="mr-section mr-steps" aria-labelledby="mr-steps-title">
	<div class="mr-container">
		<div class="mr-section__head">
			<span class="mr-kicker"><?php maria_the_icon( 'bolt', '', 16 ); ?> أسهل من رسالة واتساب</span>
			<h2 class="mr-section__title" id="mr-steps-title">اطلب لبقالتك في 3 خطوات</h2>
		</div>
		<ol class="mr-steps__list">
			<li>
				<span class="mr-steps__num">1</span>
				<span class="mr-steps__icon"><?php maria_the_icon( 'list', '', 30 ); ?></span>
				<h3>ابحث أو اختر القسم</h3>
				<p>ابحث باسم المنتج أو الشركة بالعربي أو التركي، أو افتح قائمة الطلب السريع بكل الأصناف.</p>
			</li>
			<li>
				<span class="mr-steps__num">2</span>
				<span class="mr-steps__icon"><?php maria_the_icon( 'box', '', 30 ); ?></span>
				<h3>حدّد عدد الكراتين</h3>
				<p>اضغط + لكل صنف تريده، وتُحفظ الكميات في سلتك تلقائياً مع حساب الإجمالي فوراً.</p>
			</li>
			<li>
				<span class="mr-steps__num">3</span>
				<span class="mr-steps__icon"><?php maria_the_icon( 'send', '', 30 ); ?></span>
				<h3>أرسل الطلب</h3>
				<p>اضغط «أكمل الطلب» واكتب اسمك وعنوانك فقط — الدفع عند الاستلام، أو أرسله واتساب برسالة جاهزة.</p>
			</li>
		</ol>
		<div class="mr-share">
			<div class="mr-share__text">
				<strong><?php maria_the_icon( 'share', '', 20 ); ?> احفظ رابط المتجر أو شاركه مع أصحاب البقالات</strong>
				<span>رابط واحد يكفي لطلب كل احتياجات الرف في أي وقت.</span>
			</div>
			<div class="mr-share__actions">
				<input class="mr-share__url" type="text" value="<?php echo esc_attr( home_url( '/' ) ); ?>" readonly dir="ltr" aria-label="رابط المتجر">
				<button type="button" class="mr-btn mr-btn--ghost" data-mr-copy="<?php echo esc_attr( home_url( '/' ) ); ?>"><?php maria_the_icon( 'copy', '', 18 ); ?> نسخ</button>
				<a class="mr-btn mr-btn--wa" href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( $mr_share ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 18 ); ?> مشاركة</a>
			</div>
		</div>
	</div>
</section>
