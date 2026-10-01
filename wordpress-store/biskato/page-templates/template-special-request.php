<?php
/**
 * Template Name: طلب توريد خاص
 *
 * يطلب فيه التاجر أي صنف غير موجود في القائمة ليؤمّنه فريق المشتريات من المصدر.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();
$zd_opts = zad_request_options();
?>
<main id="main" class="zd-main zd-request-page">
	<section class="zd-page-hero">
		<div class="zd-page-hero__media"><?php echo zad_img( 'sourcing', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="zd-container zd-page-hero__inner">
			<?php zad_breadcrumbs(); ?>
			<p class="zd-eyebrow zd-eyebrow--light">طلب توريد خاص</p>
			<h1 class="zd-page-hero__title">صنف غير موجود في القائمة؟ نؤمّنه لك من المصدر</h1>
			<div class="zd-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
			<dl class="zd-page-hero__facts">
				<div><dt>الرد</dt><dd>خلال يوم عمل</dd></div>
				<div><dt>السعر</dt><dd>بسعر الجملة</dd></div>
				<div><dt>المتابعة</dt><dd>عبر واتساب أو الهاتف</dd></div>
			</dl>
		</div>
	</section>

	<div class="zd-container zd-req__grid">
		<div class="zd-form-card" id="zd-form">
			<?php if ( ! zad_form_feedback( 'special' ) ) : ?>
				<form class="zd-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" data-zd-form>
					<?php echo zad_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="zd_type" value="special">

					<fieldset class="zd-form__section">
						<legend><span>01</span> بيانات المتجر</legend>
						<div class="zd-form__grid">
							<?php
							zad_form_field( 'special', 'name', array( 'autocomplete' => 'name' ) );
							zad_form_field( 'special', 'shop', array( 'placeholder' => 'مثال: ماركت النور', 'autocomplete' => 'organization' ) );
							zad_form_field( 'special', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
							zad_form_field( 'special', 'city', array( 'placeholder' => 'مثال: إسطنبول – أسنيورت' ) );
							?>
						</div>
					</fieldset>

					<fieldset class="zd-form__section">
						<legend><span>02</span> الأصناف المطلوبة</legend>
						<p class="zd-form__hint">اكتب اسم الصنف كما تعرفه، بالعربية أو التركية. يمكنك إضافة حتى 30 صنفاً في طلب واحد.</p>
						<div class="zd-items" data-zd-items>
							<div class="zd-items__head" aria-hidden="true"><span>الصنف</span><span>العلامة</span><span>الكمية</span><span>الوحدة</span><span></span></div>
							<?php for ( $zd_i = 0; $zd_i < 3; $zd_i++ ) : ?>
								<div class="zd-item" data-zd-item>
									<input type="text" name="zd_item_name[]" placeholder="<?php echo 0 === $zd_i ? 'مثال: عصير كابي برتقال 1 لتر' : 'صنف آخر (اختياري)'; ?>" aria-label="اسم الصنف"<?php echo 0 === $zd_i ? ' required aria-required="true"' : ''; ?>>
									<input type="text" name="zd_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
									<input type="number" name="zd_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
									<select name="zd_item_unit[]" aria-label="الوحدة">
										<?php foreach ( $zd_opts['unit'] as $zd_u ) : ?>
											<option value="<?php echo esc_attr( $zd_u ); ?>"><?php echo esc_html( $zd_u ); ?></option>
										<?php endforeach; ?>
									</select>
									<button type="button" class="zd-icon-btn zd-item__remove" data-zd-remove-item aria-label="حذف السطر"><?php zad_the_icon( 'close', '', 18 ); ?></button>
								</div>
							<?php endfor; ?>
						</div>
						<button type="button" class="zd-btn zd-btn--ghost zd-btn--sm" data-zd-add-item>+ أضف صنفاً آخر</button>
					</fieldset>

					<fieldset class="zd-form__section">
						<legend><span>03</span> تفاصيل إضافية</legend>
						<div class="zd-form__grid">
							<?php zad_form_field( 'special', 'urgency' ); ?>
							<div class="zd-field">
								<label for="zd-special-photo">صورة الصنف (اختياري)</label>
								<label class="zd-drop" for="zd-special-photo">
									<span data-zd-file-label>اضغط لإرفاق صورة (JPG / PNG حتى 5MB)</span>
									<input type="file" id="zd-special-photo" name="zd_photo" accept="image/jpeg,image/png,image/webp" data-zd-file>
								</label>
							</div>
							<?php zad_form_field( 'special', 'notes', array( 'placeholder' => 'مثال: الكمية شهرية ثابتة، أو أقبل بديلاً مشابهاً إن لم يتوفر' ) ); ?>
						</div>
					</fieldset>

					<button type="submit" class="zd-btn zd-btn--primary zd-btn--lg zd-btn--block">أرسل طلب التوريد</button>
					<p class="zd-form__privacy">نستخدم بياناتك فقط للتواصل بخصوص هذا الطلب.</p>
				</form>
				<template data-zd-item-tpl>
					<div class="zd-item" data-zd-item>
						<input type="text" name="zd_item_name[]" placeholder="اسم الصنف" aria-label="اسم الصنف">
						<input type="text" name="zd_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
						<input type="number" name="zd_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
						<select name="zd_item_unit[]" aria-label="الوحدة">
							<?php foreach ( $zd_opts['unit'] as $zd_u ) : ?>
								<option value="<?php echo esc_attr( $zd_u ); ?>"><?php echo esc_html( $zd_u ); ?></option>
							<?php endforeach; ?>
						</select>
						<button type="button" class="zd-icon-btn zd-item__remove" data-zd-remove-item aria-label="حذف السطر"><?php zad_the_icon( 'close', '', 18 ); ?></button>
					</div>
				</template>
			<?php endif; ?>
		</div>

		<aside class="zd-req__aside">
			<div class="zd-aside-card">
				<h2>كيف نؤمّن طلبك؟</h2>
				<ol class="zd-mini-steps">
					<li><b>ترسل الطلب</b> باسم الصنف والكمية المطلوبة.</li>
					<li><b>نبحث لك</b> لدى المصانع والوكلاء والمستوردين.</li>
					<li><b>نرد عليك</b> بالسعر والتوفر وموعد التوريد.</li>
					<li><b>نورّده</b> مع طلبيتك القادمة أو بشحنة مستقلة.</li>
				</ol>
			</div>
			<div class="zd-aside-card">
				<h2>أصناف يطلبها التجار عادةً</h2>
				<ul class="zd-tags">
					<li>مشروبات وعصائر</li><li>شاي وقهوة</li><li>معلبات ومواد غذائية</li><li>منظفات ومستلزمات</li><li>علامات عربية ومستوردة</li><li>أصناف موسمية ورمضانية</li><li>حلويات بالوزن</li><li>علكة وسكاكر</li>
				</ul>
			</div>
			<?php if ( zad_wa_number() ) : ?>
				<div class="zd-aside-card zd-aside-card--wa">
					<h2>تفضّل واتساب؟</h2>
					<p>صوّر الصنف أو عبوته وأرسل الصورة مباشرة إلى فريق المشتريات.</p>
					<a class="zd-btn zd-btn--wa zd-btn--block" href="<?php echo esc_url( zad_wa_link( 'مرحباً، أبحث عن صنف غير موجود في قائمة الأسعار:' ) ); ?>" target="_blank" rel="noopener">أرسل صورة الصنف</a>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</main>
<?php
get_footer();
