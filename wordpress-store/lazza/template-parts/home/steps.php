<?php
/**
 * الواجهة الرئيسية: خطوات الطلب + مشاركة الرابط.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_share = sprintf( 'اطلب احتياجات بقالتك من الكيك والبسكويت والشيبس والتسالي بالجملة من %1$s 👇 %2$s', get_bloginfo( 'name' ), home_url( '/' ) );
?>
<section class="lz-section lz-steps" aria-labelledby="lz-steps-title">
	<div class="lz-container">
		<div class="lz-section__head">
			<span class="lz-kicker"><?php lazza_the_icon( 'bolt', '', 16 ); ?> أسهل من رسالة واتساب</span>
			<h2 class="lz-section__title" id="lz-steps-title">اطلب لبقالتك في 3 خطوات</h2>
		</div>
		<ol class="lz-steps__list">
			<li class="lz-reveal">
				<span class="lz-steps__num">1</span>
				<span class="lz-steps__icon"><?php lazza_the_icon( 'list', '', 30 ); ?></span>
				<h3>افتح قائمة الطلب السريع</h3>
				<p>كل المنتجات في صفحة واحدة مقسّمة حسب الأقسام، مع بحث فوري بالاسم العربي أو التركي.</p>
			</li>
			<li class="lz-reveal">
				<span class="lz-steps__num">2</span>
				<span class="lz-steps__icon"><?php lazza_the_icon( 'box', '', 30 ); ?></span>
				<h3>حدّد عدد الكراتين</h3>
				<p>اضغط + لكل صنف تريده، وتُحفظ الكميات في سلتك تلقائياً مع حساب الإجمالي فوراً.</p>
			</li>
			<li class="lz-reveal">
				<span class="lz-steps__num">3</span>
				<span class="lz-steps__icon"><?php lazza_the_icon( 'send', '', 30 ); ?></span>
				<h3>أرسل الطلب</h3>
				<p>أكمل الطلب بالدفع عند الاستلام، أو أرسله لنا عبر واتساب برسالة جاهزة بكل الأصناف.</p>
			</li>
		</ol>
		<div class="lz-share lz-reveal">
			<div class="lz-share__text">
				<strong><?php lazza_the_icon( 'share', '', 20 ); ?> احفظ رابط المتجر أو شاركه مع أصحاب البقالات</strong>
				<span>رابط واحد يكفي لطلب كل احتياجات الرف في أي وقت.</span>
			</div>
			<div class="lz-share__actions">
				<input class="lz-share__url" type="text" value="<?php echo esc_attr( home_url( '/' ) ); ?>" readonly dir="ltr" aria-label="رابط المتجر">
				<button type="button" class="lz-btn lz-btn--ghost" data-lz-copy="<?php echo esc_attr( home_url( '/' ) ); ?>"><?php lazza_the_icon( 'copy', '', 18 ); ?> نسخ</button>
				<a class="lz-btn lz-btn--wa" href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( $lz_share ) ); ?>" target="_blank" rel="noopener"><?php lazza_the_icon( 'whatsapp', '', 18 ); ?> مشاركة</a>
			</div>
		</div>
	</div>
</section>
