<?php
/**
 * Template Name: الجملة الدولية والتصدير
 *
 * صفحة طلبات الجملة خارج تركيا: عرض الخدمة + خطوات التصدير + نموذج طلب عرض سعر.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();

$lz_cats  = lazza_categories();
$lz_total = lazza_products_total();
$lz_ex    = array(
	'cake'     => array( 'ETI-BRW-INT', 'كيك مغلف فردياً وعائلي: براوني، بوب كيك، دان كيك، رول كيك، كيك دبي.' ),
	'biscuits' => array( 'ULK-BSK-COC', 'بسكويت وويفر وكوكيز: بسكريم، شوكوبرنس، هانيملر، جين، ويفر أولكر.' ),
	'chips'    => array( 'ULK-CRZ-SWT', 'شيبس ذرة وكراكرز: تشيريزا، كراكس، غونغ بوبس، سمكات باليك.' ),
	'snacks'   => array( 'BON-DUB-CHO', 'شوكولاتة وألواح وحلوى: ألبيني، مترو، كارام، جانغا، شوكولاتة دبي.' ),
);
?>
<main id="main" class="lz-main lz-export">
	<section class="lz-page-hero lz-page-hero--export">
		<div class="lz-container lz-export-hero">
			<div>
				<?php lazza_breadcrumbs(); ?>
				<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'globe', '', 16 ); ?> للمستوردين والموزعين وسلاسل السوبرماركت</span>
				<h1 class="lz-page-hero__title">تصدير الحلويات التركية <span class="lz-hl">بالجملة إلى بلدك</span></h1>
				<p class="lz-page-hero__en" lang="en" dir="ltr">Turkish Biscuits, Cakes, Chips &amp; Chocolate — Wholesale Export · Eti · Ülker · Bonucci</p>
				<div class="lz-page-hero__intro">
					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>
				</div>
				<div class="lz-hero__cta">
					<a class="lz-btn lz-btn--gold lz-btn--lg" href="#lz-form"><?php lazza_the_icon( 'file', '', 20 ); ?> اطلب عرض سعر</a>
					<?php if ( lazza_wa_number() ) : ?>
						<a class="lz-btn lz-btn--glass lz-btn--lg" href="<?php echo esc_url( lazza_wa_link( 'Hello, I am interested in wholesale export. / مرحباً، أرغب بطلب جملة للتصدير.' ) ); ?>" target="_blank" rel="noopener"><?php lazza_the_icon( 'whatsapp', '', 20 ); ?> واتساب التصدير</a>
					<?php endif; ?>
				</div>
			</div>
			<ul class="lz-stats">
				<li><b><?php echo esc_html( $lz_total ? '+' . $lz_total : '+130' ); ?></b><span>صنف جاهز للتصدير</span></li>
				<li><b>3</b><span>علامات تركية رئيسية</span></li>
				<li><b>20ft · 40ft</b><span>حاويات كاملة أو مختلطة</span></li>
				<li><b>EXW · FOB · CIF</b><span>شروط تسليم مرنة</span></li>
			</ul>
		</div>
	</section>

	<section class="lz-section" aria-labelledby="lz-ex-cats">
		<div class="lz-container">
			<div class="lz-section__head">
				<span class="lz-kicker"><?php lazza_the_icon( 'box', '', 16 ); ?> ماذا نصدّر؟</span>
				<h2 class="lz-section__title" id="lz-ex-cats">تشكيلة كاملة لرفوف متاجرك</h2>
			</div>
			<div class="lz-excats">
				<?php foreach ( $lz_ex as $lz_slug => $lz_e ) : ?>
					<?php
					$lz_pid = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( $lz_e[0] ) : 0;
					?>
					<article class="lz-excat lz-reveal" style="--c:<?php echo esc_attr( $lz_cats[ $lz_slug ]['color'] ); ?>;--t:<?php echo esc_attr( $lz_cats[ $lz_slug ]['tint'] ); ?>">
						<?php if ( $lz_pid ) : ?>
							<span class="lz-excat__art" aria-hidden="true"><?php echo wc_get_product( $lz_pid )->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
						<h3><?php lazza_the_icon( $lz_cats[ $lz_slug ]['icon'], '', 22 ); ?> <?php echo esc_html( $lz_cats[ $lz_slug ]['name'] ); ?></h3>
						<p><?php echo esc_html( $lz_e[1] ); ?></p>
						<a href="<?php echo esc_url( lazza_cat_url( $lz_slug ) ); ?>">شاهد <?php echo esc_html( lazza_n_items( lazza_cat_count( $lz_slug ) ) ); ?> <?php lazza_the_icon( 'arrow-left', '', 16 ); ?></a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="lz-section lz-export-steps" aria-labelledby="lz-ex-steps">
		<div class="lz-container">
			<div class="lz-section__head">
				<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'ship', '', 16 ); ?> من الطلب إلى الميناء</span>
				<h2 class="lz-section__title" id="lz-ex-steps">كيف تتم عملية التصدير؟</h2>
			</div>
			<ol class="lz-timeline">
				<li class="lz-reveal"><span>1</span><h3>طلب عرض السعر</h3><p>ترسل لنا الأصناف والكميات وبلد الوصول عبر النموذج أو واتساب.</p></li>
				<li class="lz-reveal"><span>2</span><h3>عرض سعر مفصّل</h3><p>نرسل قائمة الأسعار وتوزيع الحاوية ومدة التجهيز خلال يوم عمل غالباً.</p></li>
				<li class="lz-reveal"><span>3</span><h3>التأكيد والدفعة</h3><p>تأكيد الطلب وتحويل الدفعة المقدمة، ويمكن إرسال عينات للطلبات الكبيرة.</p></li>
				<li class="lz-reveal"><span>4</span><h3>التجهيز والتحميل</h3><p>نجمع الأصناف من دفعات إنتاج حديثة ونرصّها على طبليات مع صور للتحميل.</p></li>
				<li class="lz-reveal"><span>5</span><h3>الشحن والمستندات</h3><p>الفاتورة، قائمة التعبئة، شهادة المنشأ والشهادات الصحية والحلال حسب الحاجة.</p></li>
			</ol>
		</div>
	</section>

	<section class="lz-section" aria-labelledby="lz-ex-ship">
		<div class="lz-container">
			<div class="lz-section__head">
				<span class="lz-kicker"><?php lazza_the_icon( 'truck', '', 16 ); ?> خيارات الشحن</span>
				<h2 class="lz-section__title" id="lz-ex-ship">اختر حجم الطلب المناسب لسوقك</h2>
			</div>
			<div class="lz-ship">
				<article class="lz-ship__card lz-reveal">
					<span class="lz-ship__icon"><?php lazza_the_icon( 'box', '', 30 ); ?></span>
					<h3>طبليات مختلطة</h3>
					<p>للطلبات التجريبية ودخول السوق: عدة أصناف على طبليات قليلة، شحن بري أو ضمن حاوية مشتركة.</p>
				</article>
				<article class="lz-ship__card lz-ship__card--featured lz-reveal">
					<span class="lz-ship__tag">الأكثر طلباً</span>
					<span class="lz-ship__icon"><?php lazza_the_icon( 'ship', '', 30 ); ?></span>
					<h3>حاوية 20 قدم</h3>
					<p>حاوية مختلطة من علامات وأقسام متعددة — الخيار الأوفر للموزعين ومتاجر الجملة.</p>
				</article>
				<article class="lz-ship__card lz-reveal">
					<span class="lz-ship__icon"><?php lazza_the_icon( 'globe', '', 30 ); ?></span>
					<h3>حاوية 40 قدم</h3>
					<p>للمستوردين وسلاسل السوبرماركت: أفضل سعر للوحدة وتكلفة شحن أقل لكل كرتونة.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="lz-section lz-export-form" id="lz-form" aria-labelledby="lz-ex-form">
		<div class="lz-container lz-req__grid">
			<div class="lz-form-card">
				<div class="lz-form-card__head">
					<h2 id="lz-ex-form">طلب عرض سعر للتصدير</h2>
					<p>املأ النموذج وسنرسل لك عرض سعر مفصّلاً مع قائمة المنتجات المتاحة.</p>
				</div>
				<?php if ( ! lazza_form_feedback( 'export' ) ) : ?>
					<form class="lz-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-lz-form>
						<?php echo lazza_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<input type="hidden" name="lz_type" value="export">
						<fieldset class="lz-form__section">
							<legend><span>1</span> بيانات الشركة</legend>
							<div class="lz-form__grid">
								<?php
								lazza_form_field( 'export', 'company', array( 'autocomplete' => 'organization' ) );
								lazza_form_field( 'export', 'name', array( 'autocomplete' => 'name' ) );
								lazza_form_field( 'export', 'country' );
								lazza_form_field( 'export', 'city', array( 'placeholder' => 'مثال: جدة / ميناء جدة الإسلامي' ) );
								lazza_form_field( 'export', 'phone', array( 'placeholder' => '+966 5x xxx xxxx', 'autocomplete' => 'tel' ) );
								lazza_form_field( 'export', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
								lazza_form_field( 'export', 'business' );
								?>
							</div>
						</fieldset>
						<fieldset class="lz-form__section">
							<legend><span>2</span> تفاصيل الطلب</legend>
							<div class="lz-form__grid">
								<?php
								lazza_form_field( 'export', 'interests' );
								lazza_form_field( 'export', 'volume' );
								lazza_form_field( 'export', 'incoterm' );
								lazza_form_field( 'export', 'docs' );
								lazza_form_field( 'export', 'products', array( 'placeholder' => 'مثال: بسكريم 200 كرتونة، براوني 150 كرتونة، تشيريزا 100 كرتونة…' ) );
								lazza_form_field( 'export', 'notes' );
								?>
							</div>
						</fieldset>
						<button type="submit" class="lz-btn lz-btn--primary lz-btn--lg lz-btn--block"><?php lazza_the_icon( 'send', '', 20 ); ?> أرسل طلب عرض السعر</button>
						<p class="lz-form__privacy"><?php lazza_the_icon( 'shield', '', 16 ); ?> بياناتك سرية وتُستخدم فقط لإعداد عرض السعر.</p>
					</form>
				<?php endif; ?>
			</div>
			<aside class="lz-req__aside">
				<div class="lz-aside-card">
					<h2>المستندات التي نجهزها</h2>
					<ul class="lz-checklist">
						<li>الفاتورة التجارية (Commercial Invoice)</li>
						<li>قائمة التعبئة (Packing List)</li>
						<li>شهادة المنشأ (Certificate of Origin)</li>
						<li>الشهادات الصحية وشهادات الحلال حسب المصنّع</li>
						<li>بوليصة الشحن (B/L) أو CMR للشحن البري</li>
					</ul>
				</div>
				<div class="lz-aside-card">
					<h2>أسواق نستقبل طلباتها</h2>
					<ul class="lz-tags">
						<li>دول الخليج العربي</li><li>العراق</li><li>الأردن وفلسطين</li><li>لبنان وسوريا</li><li>ليبيا وشمال أفريقيا</li><li>أوروبا</li><li>آسيا الوسطى والقوقاز</li><li>أفريقيا</li>
					</ul>
				</div>
			</aside>
		</div>
	</section>

	<?php lazza_render_faqs( 'export', 'أسئلة المستوردين الشائعة' ); ?>
</main>
<?php
get_footer();
