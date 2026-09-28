<?php
/**
 * Template Name: الجملة الدولية والتصدير
 *
 * صفحة طلبات الجملة خارج تركيا: عرض الخدمة + التشكيلة + خطوات التصدير + نموذج طلب عرض سعر.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sh_cats  = shami_categories();
$sh_total = shami_products_total();
$sh_ex    = array(
	'cake'     => 'كيك مغلّف فردياً وعائلي: براوني، بوب كيك، دان كيك، رول كيك، كيك دبي.',
	'biscuits' => 'بسكويت وويفر وكوكيز: بسكريم، شوكوبرنس، هانيملر، جين، ويفر أولكر.',
	'chips'    => 'شيبس ذرة وكراكرز: تشيريزا، كراكس، غونغ بوبس، سمكات باليك.',
	'snacks'   => 'شوكولاتة وألواح وحلوى: ألبيني، مترو، كارام، جانغا، شوكولاتة دبي.',
);
?>
<main id="main" class="sh-main sh-export">
	<section class="sh-page-hero sh-page-hero--tall">
		<div class="sh-page-hero__media"><?php echo shami_img( 'seg-export', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="sh-container sh-page-hero__inner">
			<?php shami_breadcrumbs(); ?>
			<p class="sh-eyebrow sh-eyebrow--light">للمستوردين والموزعين وسلاسل السوبرماركت</p>
			<h1 class="sh-page-hero__title">تصدير الحلويات التركية بالجملة، بحاوية تناسب سوقك</h1>
			<p class="sh-page-hero__en" lang="en" dir="ltr">Turkish Biscuits, Cakes, Snacks &amp; Chocolate — Wholesale Export · Eti · Ülker · Bonucci</p>
			<div class="sh-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
			<div class="sh-page-hero__actions">
				<a class="sh-btn sh-btn--accent sh-btn--lg" href="#sh-form">اطلب عرض سعر</a>
				<?php if ( shami_wa_number() ) : ?>
					<a class="sh-btn sh-btn--outline-light sh-btn--lg" href="<?php echo esc_url( shami_wa_link( 'Hello, I am interested in wholesale export. / مرحباً، أرغب بطلب جملة للتصدير.' ) ); ?>" target="_blank" rel="noopener">واتساب قسم التصدير</a>
				<?php endif; ?>
			</div>
			<dl class="sh-page-hero__facts">
				<div><dt>أصناف جاهزة للتصدير</dt><dd><bdi><?php echo esc_html( $sh_total ? $sh_total . '+' : '130+' ); ?></bdi></dd></div>
				<div><dt>حجم الشحنة</dt><dd><bdi dir="ltr">20ft · 40ft</bdi></dd></div>
				<div><dt>شروط التسليم</dt><dd><bdi dir="ltr">EXW · FOB · CIF</bdi></dd></div>
			</dl>
		</div>
	</section>

	<section class="sh-section" aria-labelledby="sh-ex-cats">
		<div class="sh-container">
			<div class="sh-head">
				<div class="sh-head__text">
					<p class="sh-eyebrow">ماذا نصدّر؟</p>
					<h2 class="sh-head__title" id="sh-ex-cats">تشكيلة كاملة لرفوف متاجرك في حاوية واحدة</h2>
				</div>
			</div>
			<ul class="sh-excats">
				<?php foreach ( $sh_ex as $sh_slug => $sh_text ) : ?>
					<li class="sh-excat">
						<div class="sh-excat__media"><?php echo shami_img( $sh_cats[ $sh_slug ]['image'], '', array( 'sizes' => '(min-width: 1024px) 25vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<h3><?php echo esc_html( $sh_cats[ $sh_slug ]['title'] ); ?></h3>
						<p><?php echo esc_html( $sh_text ); ?></p>
						<a class="sh-link" href="<?php echo esc_url( shami_cat_url( $sh_slug ) ); ?>"><?php echo esc_html( shami_n_items( shami_cat_count( $sh_slug ) ) ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="sh-section sh-section--paper sh-export-steps" aria-labelledby="sh-ex-steps">
		<div class="sh-container">
			<div class="sh-head">
				<div class="sh-head__text">
					<p class="sh-eyebrow">من الطلب إلى الميناء</p>
					<h2 class="sh-head__title" id="sh-ex-steps">خمس مراحل واضحة لكل شحنة</h2>
				</div>
			</div>
			<ol class="sh-timeline">
				<li><span>01</span><h3>طلب عرض السعر</h3><p>ترسل الأصناف والكميات وبلد الوصول عبر النموذج أو واتساب.</p></li>
				<li><span>02</span><h3>عرض سعر مفصّل</h3><p>نرسل الأسعار وتوزيع الحاوية ومدة التجهيز، غالباً خلال يوم عمل.</p></li>
				<li><span>03</span><h3>التأكيد والدفعة</h3><p>تأكيد الطلب وتحويل الدفعة المقدمة، مع إمكانية إرسال عينات للطلبات الكبيرة.</p></li>
				<li><span>04</span><h3>التجهيز والتحميل</h3><p>نجمع الأصناف من دفعات إنتاج حديثة ونرصّها على طبليات، ونرسل صور التحميل.</p></li>
				<li><span>05</span><h3>الشحن والمستندات</h3><p>الفاتورة وقائمة التعبئة وشهادة المنشأ والشهادات الصحية والحلال حسب الحاجة.</p></li>
			</ol>
		</div>
	</section>

	<section class="sh-section" aria-labelledby="sh-ex-ship">
		<div class="sh-container">
			<div class="sh-head">
				<div class="sh-head__text">
					<p class="sh-eyebrow">حجم الشحنة</p>
					<h2 class="sh-head__title" id="sh-ex-ship">اختر الحجم المناسب لمرحلة دخولك السوق</h2>
				</div>
			</div>
			<div class="sh-ship">
				<article class="sh-ship__card">
					<p class="sh-ship__size">طبليات</p>
					<h3>طبليات مختلطة</h3>
					<p>للطلبات التجريبية واختبار السوق: عدة أصناف على طبليات قليلة، بشحن بري أو ضمن حاوية مشتركة.</p>
				</article>
				<article class="sh-ship__card sh-ship__card--featured">
					<span class="sh-ship__tag">الأكثر طلباً</span>
					<p class="sh-ship__size"><bdi dir="ltr">20ft</bdi></p>
					<h3>حاوية 20 قدم</h3>
					<p>حاوية مختلطة من علامات وأقسام متعددة، الخيار الأوفر للموزعين ومتاجر الجملة.</p>
				</article>
				<article class="sh-ship__card">
					<p class="sh-ship__size"><bdi dir="ltr">40ft</bdi></p>
					<h3>حاوية 40 قدم</h3>
					<p>للمستوردين وسلاسل السوبرماركت: أفضل سعر للوحدة وأقل تكلفة شحن لكل كرتونة.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="sh-section sh-section--sand sh-export-form" id="sh-form" aria-labelledby="sh-ex-form">
		<div class="sh-container sh-req__grid">
			<div class="sh-form-card">
				<div class="sh-form-card__head">
					<h2 id="sh-ex-form">طلب عرض سعر للتصدير</h2>
					<p>املأ النموذج ونرسل لك عرض سعر مفصّلاً مع قائمة الأصناف المتاحة وتوزيع الحاوية.</p>
				</div>
				<?php if ( ! shami_form_feedback( 'export' ) ) : ?>
					<form class="sh-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-sh-form>
						<?php echo shami_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<input type="hidden" name="sh_type" value="export">
						<fieldset class="sh-form__section">
							<legend><span>01</span> بيانات الشركة</legend>
							<div class="sh-form__grid">
								<?php
								shami_form_field( 'export', 'company', array( 'autocomplete' => 'organization' ) );
								shami_form_field( 'export', 'name', array( 'autocomplete' => 'name' ) );
								shami_form_field( 'export', 'country' );
								shami_form_field( 'export', 'city', array( 'placeholder' => 'مثال: جدة / ميناء جدة الإسلامي' ) );
								shami_form_field( 'export', 'phone', array( 'placeholder' => '+966 5x xxx xxxx', 'autocomplete' => 'tel' ) );
								shami_form_field( 'export', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
								shami_form_field( 'export', 'business' );
								?>
							</div>
						</fieldset>
						<fieldset class="sh-form__section">
							<legend><span>02</span> تفاصيل الطلب</legend>
							<div class="sh-form__grid">
								<?php
								shami_form_field( 'export', 'interests' );
								shami_form_field( 'export', 'volume' );
								shami_form_field( 'export', 'incoterm' );
								shami_form_field( 'export', 'docs' );
								shami_form_field( 'export', 'products', array( 'placeholder' => 'مثال: بسكريم 200 كرتونة، براوني 150 كرتونة، تشيريزا 100 كرتونة…' ) );
								shami_form_field( 'export', 'notes' );
								?>
							</div>
						</fieldset>
						<button type="submit" class="sh-btn sh-btn--primary sh-btn--lg sh-btn--block">أرسل طلب عرض السعر</button>
						<p class="sh-form__privacy">بياناتك سرية وتُستخدم فقط لإعداد عرض السعر.</p>
					</form>
				<?php endif; ?>
			</div>
			<aside class="sh-req__aside">
				<div class="sh-aside-card">
					<h2>المستندات التي نجهزها</h2>
					<ul class="sh-checklist">
						<li>الفاتورة التجارية <span lang="en">(Commercial Invoice)</span></li>
						<li>قائمة التعبئة <span lang="en">(Packing List)</span></li>
						<li>شهادة المنشأ <span lang="en">(Certificate of Origin)</span></li>
						<li>الشهادات الصحية وشهادات الحلال حسب المصنّع</li>
						<li>بوليصة الشحن <span lang="en">(B/L)</span> أو <span lang="en">CMR</span> للشحن البري</li>
					</ul>
				</div>
				<div class="sh-aside-card">
					<h2>أسواق نستقبل طلباتها</h2>
					<ul class="sh-tags">
						<li>دول الخليج العربي</li><li>العراق</li><li>الأردن وفلسطين</li><li>لبنان وسوريا</li><li>ليبيا وشمال أفريقيا</li><li>أوروبا</li><li>آسيا الوسطى والقوقاز</li><li>أفريقيا</li>
					</ul>
				</div>
			</aside>
		</div>
	</section>

	<?php shami_render_faqs( 'export', 'أسئلة المستوردين الشائعة' ); ?>
</main>
<?php
get_footer();
