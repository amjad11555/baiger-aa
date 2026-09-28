<?php
/**
 * Template Name: الجملة الدولية والتصدير
 *
 * صفحة طلبات الجملة خارج تركيا: عرض الخدمة + خطوات التصدير + نموذج طلب عرض سعر.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mr_cats  = maria_categories();
$mr_total = maria_products_total();
$mr_ex    = array(
	'cake'     => array( 'ETI-BRW-INT', 'كيك مغلف فردياً وعائلي: براوني، بوب كيك، دان كيك، رول كيك، كيك دبي.' ),
	'biscuits' => array( 'ULK-BSK-COC', 'بسكويت وويفر وكوكيز: بسكريم، شوكوبرنس، هانيملر، جين، ويفر أولكر.' ),
	'chips'    => array( 'ULK-CRZ-CKT', 'شيبس ذرة وكراكرز: تشيريزا، كراكس، غونغ بوبس، سمكات باليك.' ),
	'snacks'   => array( 'BON-DUB-CHO', 'شوكولاتة وألواح وحلوى: ألبيني، مترو، كارام، جانغا، شوكولاتة دبي.' ),
);
?>
<main id="main" class="mr-main mr-export">
	<section class="mr-page-hero mr-page-hero--export">
		<div class="mr-container mr-export-hero">
			<div>
				<?php maria_breadcrumbs(); ?>
				<span class="mr-kicker"><?php maria_the_icon( 'globe', '', 16 ); ?> للمستوردين والموزعين وسلاسل السوبرماركت</span>
				<h1 class="mr-page-hero__title">تصدير الحلويات التركية <span class="mr-hl">بالجملة إلى بلدك</span></h1>
				<p class="mr-page-hero__en" lang="en" dir="ltr">Turkish Biscuits, Cakes, Chips &amp; Chocolate — Wholesale Export · Eti · Ülker · Bonucci</p>
				<div class="mr-page-hero__intro">
					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>
				</div>
				<div class="mr-hero__cta">
					<a class="mr-btn mr-btn--accent mr-btn--lg" href="#mr-form"><?php maria_the_icon( 'file', '', 20 ); ?> اطلب عرض سعر</a>
					<?php if ( maria_wa_number() ) : ?>
						<a class="mr-btn mr-btn--ghost mr-btn--lg" href="<?php echo esc_url( maria_wa_link( 'Hello, I am interested in wholesale export. / مرحباً، أرغب بطلب جملة للتصدير.' ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 20 ); ?> واتساب التصدير</a>
					<?php endif; ?>
				</div>
			</div>
			<ul class="mr-stats">
				<li><b><?php echo esc_html( $mr_total ? '+' . $mr_total : '+130' ); ?></b><span>صنف جاهز للتصدير</span></li>
				<li><b>3</b><span>علامات تركية رئيسية</span></li>
				<li><b>20ft · 40ft</b><span>حاويات كاملة أو مختلطة</span></li>
				<li><b>EXW · FOB · CIF</b><span>شروط تسليم مرنة</span></li>
			</ul>
		</div>
	</section>

	<section class="mr-section" aria-labelledby="mr-ex-cats">
		<div class="mr-container">
			<div class="mr-section__head">
				<span class="mr-kicker"><?php maria_the_icon( 'box', '', 16 ); ?> ماذا نصدّر؟</span>
				<h2 class="mr-section__title" id="mr-ex-cats">تشكيلة كاملة لرفوف متاجرك</h2>
			</div>
			<div class="mr-excats">
				<?php foreach ( $mr_ex as $mr_slug => $mr_e ) : ?>
					<?php
					$mr_pid = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( $mr_e[0] ) : 0;
					?>
					<article class="mr-excat" style="--c:<?php echo esc_attr( $mr_cats[ $mr_slug ]['color'] ); ?>;--t:<?php echo esc_attr( $mr_cats[ $mr_slug ]['tint'] ); ?>">
						<?php if ( $mr_pid ) : ?>
							<span class="mr-excat__art" aria-hidden="true"><?php echo wc_get_product( $mr_pid )->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
						<h3><?php maria_the_icon( $mr_cats[ $mr_slug ]['icon'], '', 22 ); ?> <?php echo esc_html( $mr_cats[ $mr_slug ]['name'] ); ?></h3>
						<p><?php echo esc_html( $mr_e[1] ); ?></p>
						<a href="<?php echo esc_url( maria_cat_url( $mr_slug ) ); ?>">شاهد <?php echo esc_html( maria_n_items( maria_cat_count( $mr_slug ) ) ); ?> <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="mr-section mr-export-steps" aria-labelledby="mr-ex-steps">
		<div class="mr-container">
			<div class="mr-section__head">
				<span class="mr-kicker"><?php maria_the_icon( 'ship', '', 16 ); ?> من الطلب إلى الميناء</span>
				<h2 class="mr-section__title" id="mr-ex-steps">كيف تتم عملية التصدير؟</h2>
			</div>
			<ol class="mr-timeline">
				<li><span>1</span><h3>طلب عرض السعر</h3><p>ترسل لنا الأصناف والكميات وبلد الوصول عبر النموذج أو واتساب.</p></li>
				<li><span>2</span><h3>عرض سعر مفصّل</h3><p>نرسل قائمة الأسعار وتوزيع الحاوية ومدة التجهيز خلال يوم عمل غالباً.</p></li>
				<li><span>3</span><h3>التأكيد والدفعة</h3><p>تأكيد الطلب وتحويل الدفعة المقدمة، ويمكن إرسال عينات للطلبات الكبيرة.</p></li>
				<li><span>4</span><h3>التجهيز والتحميل</h3><p>نجمع الأصناف من دفعات إنتاج حديثة ونرصّها على طبليات مع صور للتحميل.</p></li>
				<li><span>5</span><h3>الشحن والمستندات</h3><p>الفاتورة، قائمة التعبئة، شهادة المنشأ والشهادات الصحية والحلال حسب الحاجة.</p></li>
			</ol>
		</div>
	</section>

	<section class="mr-section" aria-labelledby="mr-ex-ship">
		<div class="mr-container">
			<div class="mr-section__head">
				<span class="mr-kicker"><?php maria_the_icon( 'truck', '', 16 ); ?> خيارات الشحن</span>
				<h2 class="mr-section__title" id="mr-ex-ship">اختر حجم الطلب المناسب لسوقك</h2>
			</div>
			<div class="mr-ship">
				<article class="mr-ship__card">
					<span class="mr-ship__icon"><?php maria_the_icon( 'box', '', 30 ); ?></span>
					<h3>طبليات مختلطة</h3>
					<p>للطلبات التجريبية ودخول السوق: عدة أصناف على طبليات قليلة، شحن بري أو ضمن حاوية مشتركة.</p>
				</article>
				<article class="mr-ship__card mr-ship__card--featured">
					<span class="mr-ship__tag">الأكثر طلباً</span>
					<span class="mr-ship__icon"><?php maria_the_icon( 'ship', '', 30 ); ?></span>
					<h3>حاوية 20 قدم</h3>
					<p>حاوية مختلطة من علامات وأقسام متعددة — الخيار الأوفر للموزعين ومتاجر الجملة.</p>
				</article>
				<article class="mr-ship__card">
					<span class="mr-ship__icon"><?php maria_the_icon( 'globe', '', 30 ); ?></span>
					<h3>حاوية 40 قدم</h3>
					<p>للمستوردين وسلاسل السوبرماركت: أفضل سعر للوحدة وتكلفة شحن أقل لكل كرتونة.</p>
				</article>
			</div>
		</div>
	</section>

	<section class="mr-section mr-export-form" id="mr-form" aria-labelledby="mr-ex-form">
		<div class="mr-container mr-req__grid">
			<div class="mr-form-card">
				<div class="mr-form-card__head">
					<h2 id="mr-ex-form">طلب عرض سعر للتصدير</h2>
					<p>املأ النموذج وسنرسل لك عرض سعر مفصّلاً مع قائمة المنتجات المتاحة.</p>
				</div>
				<?php if ( ! maria_form_feedback( 'export' ) ) : ?>
					<form class="mr-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-mr-form>
						<?php echo maria_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<input type="hidden" name="mr_type" value="export">
						<fieldset class="mr-form__section">
							<legend><span>1</span> بيانات الشركة</legend>
							<div class="mr-form__grid">
								<?php
								maria_form_field( 'export', 'company', array( 'autocomplete' => 'organization' ) );
								maria_form_field( 'export', 'name', array( 'autocomplete' => 'name' ) );
								maria_form_field( 'export', 'country' );
								maria_form_field( 'export', 'city', array( 'placeholder' => 'مثال: جدة / ميناء جدة الإسلامي' ) );
								maria_form_field( 'export', 'phone', array( 'placeholder' => '+966 5x xxx xxxx', 'autocomplete' => 'tel' ) );
								maria_form_field( 'export', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
								maria_form_field( 'export', 'business' );
								?>
							</div>
						</fieldset>
						<fieldset class="mr-form__section">
							<legend><span>2</span> تفاصيل الطلب</legend>
							<div class="mr-form__grid">
								<?php
								maria_form_field( 'export', 'interests' );
								maria_form_field( 'export', 'volume' );
								maria_form_field( 'export', 'incoterm' );
								maria_form_field( 'export', 'docs' );
								maria_form_field( 'export', 'products', array( 'placeholder' => 'مثال: بسكريم 200 كرتونة، براوني 150 كرتونة، تشيريزا 100 كرتونة…' ) );
								maria_form_field( 'export', 'notes' );
								?>
							</div>
						</fieldset>
						<button type="submit" class="mr-btn mr-btn--primary mr-btn--lg mr-btn--block"><?php maria_the_icon( 'send', '', 20 ); ?> أرسل طلب عرض السعر</button>
						<p class="mr-form__privacy"><?php maria_the_icon( 'shield', '', 16 ); ?> بياناتك سرية وتُستخدم فقط لإعداد عرض السعر.</p>
					</form>
				<?php endif; ?>
			</div>
			<aside class="mr-req__aside">
				<div class="mr-aside-card">
					<h2>المستندات التي نجهزها</h2>
					<ul class="mr-checklist">
						<li>الفاتورة التجارية (Commercial Invoice)</li>
						<li>قائمة التعبئة (Packing List)</li>
						<li>شهادة المنشأ (Certificate of Origin)</li>
						<li>الشهادات الصحية وشهادات الحلال حسب المصنّع</li>
						<li>بوليصة الشحن (B/L) أو CMR للشحن البري</li>
					</ul>
				</div>
				<div class="mr-aside-card">
					<h2>أسواق نستقبل طلباتها</h2>
					<ul class="mr-tags">
						<li>دول الخليج العربي</li><li>العراق</li><li>الأردن وفلسطين</li><li>لبنان وسوريا</li><li>ليبيا وشمال أفريقيا</li><li>أوروبا</li><li>آسيا الوسطى والقوقاز</li><li>أفريقيا</li>
					</ul>
				</div>
			</aside>
		</div>
	</section>

	<?php maria_render_faqs( 'export', 'أسئلة المستوردين الشائعة' ); ?>
</main>
<?php
get_footer();
