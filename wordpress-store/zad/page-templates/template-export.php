<?php
/**
 * Template Name: الجملة الدولية والتصدير
 *
 * صفحة طلبات الجملة خارج تركيا: عرض الخدمة + التشكيلة + خطوات التصدير + نموذج طلب عرض سعر.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();

$zd_cats  = zad_categories();
$zd_total = zad_products_total();
$zd_ex    = array(
	'cake'     => 'كيك مغلّف فردياً وعائلي: براوني، بوب كيك، دان كيك، رول كيك، كيك دبي.',
	'biscuits' => 'بسكويت وويفر وكوكيز: بسكريم، شوكوبرنس، هانيملر، جين، ويفر أولكر.',
	'chips'    => 'شيبس ذرة وكراكرز: تشيريزا، كراكس، غونغ بوبس، سمكات باليك.',
	'snacks'   => 'شوكولاتة وألواح وحلوى: ألبيني، مترو، كارام، جانغا، شوكولاتة دبي.',
);
?>
<main id="main" class="zd-main zd-export">
	<section class="zd-page-hero zd-page-hero--tall">
		<div class="zd-page-hero__media"><?php echo zad_img( 'seg-export', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="zd-container zd-page-hero__inner">
			<?php zad_breadcrumbs(); ?>
			<p class="zd-eyebrow zd-eyebrow--light">للمستوردين والموزعين وسلاسل السوبرماركت</p>
			<h1 class="zd-page-hero__title">تصدير الحلويات التركية بالجملة، بحاوية تناسب سوقك</h1>
			<p class="zd-page-hero__en" lang="en" dir="ltr">Turkish Biscuits, Cakes, Snacks &amp; Chocolate — Wholesale Export · Eti · Ülker · Bonucci</p>
			<div class="zd-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
			<div class="zd-page-hero__actions">
				<a class="zd-btn zd-btn--accent zd-btn--lg" href="#zd-form">اطلب عرض سعر</a>
				<?php if ( zad_wa_number() ) : ?>
					<a class="zd-btn zd-btn--outline-light zd-btn--lg" href="<?php echo esc_url( zad_wa_link( 'Hello, I am interested in wholesale export. / مرحباً، أرغب بطلب جملة للتصدير.' ) ); ?>" target="_blank" rel="noopener">واتساب قسم التصدير</a>
				<?php endif; ?>
			</div>
			<dl class="zd-page-hero__facts">
				<div><dt>أصناف جاهزة للتصدير</dt><dd><bdi><?php echo esc_html( $zd_total ? $zd_total . '+' : '130+' ); ?></bdi></dd></div>
				<div><dt>حجم الشحنة</dt><dd><bdi dir="ltr">20ft · 40ft</bdi></dd></div>
				<div><dt>شروط التسليم</dt><dd><bdi dir="ltr">EXW · FOB · CIF</bdi></dd></div>
			</dl>
		</div>
	</section>

	<section class="zd-section" aria-labelledby="zd-ex-cats">
		<div class="zd-container">
			<div class="zd-head">
				<div class="zd-head__text">
					<p class="zd-eyebrow">ماذا نصدّر؟</p>
					<h2 class="zd-head__title" id="zd-ex-cats">تشكيلة كاملة لرفوف متاجرك في حاوية واحدة</h2>
				</div>
			</div>
			<ul class="zd-excats">
				<?php foreach ( $zd_ex as $zd_slug => $zd_text ) : ?>
					<li class="zd-excat">
						<div class="zd-excat__media"><?php echo zad_img( $zd_cats[ $zd_slug ]['image'], '', array( 'sizes' => '(min-width: 1024px) 25vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<h3><?php echo esc_html( $zd_cats[ $zd_slug ]['title'] ); ?></h3>
						<p><?php echo esc_html( $zd_text ); ?></p>
						<a class="zd-link" href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>"><?php echo esc_html( zad_n_items( zad_cat_count( $zd_slug ) ) ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="zd-section zd-section--paper zd-export-steps" aria-labelledby="zd-ex-steps">
		<div class="zd-container">
			<div class="zd-head">
				<div class="zd-head__text">
					<p class="zd-eyebrow">من الطلب إلى الميناء</p>
					<h2 class="zd-head__title" id="zd-ex-steps">خمس مراحل واضحة لكل شحنة</h2>
				</div>
			</div>
			<ol class="zd-timeline">
				<li><span>01</span><h3>طلب عرض السعر</h3><p>ترسل الأصناف والكميات وبلد الوصول عبر النموذج أو واتساب.</p></li>
				<li><span>02</span><h3>عرض سعر مفصّل</h3><p>نرسل الأسعار وتوزيع الحاوية ومدة التجهيز، غالباً خلال يوم عمل.</p></li>
				<li><span>03</span><h3>التأكيد والدفعة</h3><p>تأكيد الطلب وتحويل الدفعة المقدمة، مع إمكانية إرسال عينات للطلبات الكبيرة.</p></li>
				<li><span>04</span><h3>التجهيز والتحميل</h3><p>نجمع الأصناف من دفعات إنتاج حديثة ونرصّها على طبليات، ونرسل صور التحميل.</p></li>
				<li><span>05</span><h3>الشحن والمستندات</h3><p>الفاتورة وقائمة التعبئة وشهادة المنشأ والشهادات الصحية والحلال حسب الحاجة.</p></li>
			</ol>
		</div>
	</section>

	<section class="zd-section" aria-labelledby="zd-ex-ship">
		<div class="zd-container">
			<div class="zd-head">
				<div class="zd-head__text">
					<p class="zd-eyebrow">حجم الشحنة</p>
					<h2 class="zd-head__title" id="zd-ex-ship">اختر الحجم المناسب لمرحلة دخولك السوق</h2>
				</div>
			</div>
			<div class="zd-ship">
				<article class="zd-ship__card">
					<p class="zd-ship__size">طبليات</p>
					<h3>طبليات مختلطة</h3>
					<p>للطلبات التجريبية واختبار السوق: عدة أصناف على طبليات قليلة، بشحن بري أو ضمن حاوية مشتركة.</p>
				</article>
				<article class="zd-ship__card zd-ship__card--featured">
					<span class="zd-ship__tag">الأكثر طلباً</span>
					<p class="zd-ship__size"><bdi dir="ltr">20ft</bdi></p>
					<h3>حاوية 20 قدم</h3>
					<p>حاوية مختلطة من علامات وأقسام متعددة، الخيار الأوفر للموزعين ومتاجر الجملة.</p>
				</article>
				<article class="zd-ship__card">
					<p class="zd-ship__size"><bdi dir="ltr">40ft</bdi></p>
					<h3>حاوية 40 قدم</h3>
					<p>للمستوردين وسلاسل السوبرماركت: أفضل سعر للوحدة وأقل تكلفة شحن لكل كرتونة.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="zd-section zd-section--sand zd-export-form" id="zd-form" aria-labelledby="zd-ex-form">
		<div class="zd-container zd-req__grid">
			<div class="zd-form-card">
				<div class="zd-form-card__head">
					<h2 id="zd-ex-form">طلب عرض سعر للتصدير</h2>
					<p>املأ النموذج ونرسل لك عرض سعر مفصّلاً مع قائمة الأصناف المتاحة وتوزيع الحاوية.</p>
				</div>
				<?php if ( ! zad_form_feedback( 'export' ) ) : ?>
					<form class="zd-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-zd-form>
						<?php echo zad_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<input type="hidden" name="zd_type" value="export">
						<fieldset class="zd-form__section">
							<legend><span>01</span> بيانات الشركة</legend>
							<div class="zd-form__grid">
								<?php
								zad_form_field( 'export', 'company', array( 'autocomplete' => 'organization' ) );
								zad_form_field( 'export', 'name', array( 'autocomplete' => 'name' ) );
								zad_form_field( 'export', 'country' );
								zad_form_field( 'export', 'city', array( 'placeholder' => 'مثال: جدة / ميناء جدة الإسلامي' ) );
								zad_form_field( 'export', 'phone', array( 'placeholder' => '+966 5x xxx xxxx', 'autocomplete' => 'tel' ) );
								zad_form_field( 'export', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
								zad_form_field( 'export', 'business' );
								?>
							</div>
						</fieldset>
						<fieldset class="zd-form__section">
							<legend><span>02</span> تفاصيل الطلب</legend>
							<div class="zd-form__grid">
								<?php
								zad_form_field( 'export', 'interests' );
								zad_form_field( 'export', 'volume' );
								zad_form_field( 'export', 'incoterm' );
								zad_form_field( 'export', 'docs' );
								zad_form_field( 'export', 'products', array( 'placeholder' => 'مثال: بسكريم 200 كرتونة، براوني 150 كرتونة، تشيريزا 100 كرتونة…' ) );
								zad_form_field( 'export', 'notes' );
								?>
							</div>
						</fieldset>
						<button type="submit" class="zd-btn zd-btn--primary zd-btn--lg zd-btn--block">أرسل طلب عرض السعر</button>
						<p class="zd-form__privacy">بياناتك سرية وتُستخدم فقط لإعداد عرض السعر.</p>
					</form>
				<?php endif; ?>
			</div>
			<aside class="zd-req__aside">
				<div class="zd-aside-card">
					<h2>المستندات التي نجهزها</h2>
					<ul class="zd-checklist">
						<li>الفاتورة التجارية <span lang="en">(Commercial Invoice)</span></li>
						<li>قائمة التعبئة <span lang="en">(Packing List)</span></li>
						<li>شهادة المنشأ <span lang="en">(Certificate of Origin)</span></li>
						<li>الشهادات الصحية وشهادات الحلال حسب المصنّع</li>
						<li>بوليصة الشحن <span lang="en">(B/L)</span> أو <span lang="en">CMR</span> للشحن البري</li>
					</ul>
				</div>
				<div class="zd-aside-card">
					<h2>أسواق نستقبل طلباتها</h2>
					<ul class="zd-tags">
						<li>دول الخليج العربي</li><li>العراق</li><li>الأردن وفلسطين</li><li>لبنان وسوريا</li><li>ليبيا وشمال أفريقيا</li><li>أوروبا</li><li>آسيا الوسطى والقوقاز</li><li>أفريقيا</li>
					</ul>
				</div>
			</aside>
		</div>
	</section>

	<?php zad_render_faqs( 'export', 'أسئلة المستوردين الشائعة' ); ?>
</main>
<?php
get_footer();
