<?php
/**
 * Template Name: طلب توريد خاص
 *
 * يطلب فيه التاجر أي صنف غير موجود في القائمة ليؤمّنه فريق المشتريات من المصدر.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
$sh_opts = shami_request_options();
?>
<main id="main" class="sh-main sh-request-page">
	<section class="sh-page-hero">
		<div class="sh-page-hero__media"><?php echo shami_img( 'sourcing', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="sh-container sh-page-hero__inner">
			<?php shami_breadcrumbs(); ?>
			<p class="sh-eyebrow sh-eyebrow--light">طلب توريد خاص</p>
			<h1 class="sh-page-hero__title">صنف غير موجود في القائمة؟ نؤمّنه لك من المصدر</h1>
			<div class="sh-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
			<dl class="sh-page-hero__facts">
				<div><dt>الرد</dt><dd>خلال يوم عمل</dd></div>
				<div><dt>السعر</dt><dd>بسعر الجملة</dd></div>
				<div><dt>المتابعة</dt><dd>عبر واتساب أو الهاتف</dd></div>
			</dl>
		</div>
	</section>

	<div class="sh-container sh-req__grid">
		<div class="sh-form-card" id="sh-form">
			<?php if ( ! shami_form_feedback( 'special' ) ) : ?>
				<form class="sh-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" data-sh-form>
					<?php echo shami_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="sh_type" value="special">

					<fieldset class="sh-form__section">
						<legend><span>01</span> بيانات المتجر</legend>
						<div class="sh-form__grid">
							<?php
							shami_form_field( 'special', 'name', array( 'autocomplete' => 'name' ) );
							shami_form_field( 'special', 'shop', array( 'placeholder' => 'مثال: ماركت النور', 'autocomplete' => 'organization' ) );
							shami_form_field( 'special', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
							shami_form_field( 'special', 'city', array( 'placeholder' => 'مثال: إسطنبول – أسنيورت' ) );
							?>
						</div>
					</fieldset>

					<fieldset class="sh-form__section">
						<legend><span>02</span> الأصناف المطلوبة</legend>
						<p class="sh-form__hint">اكتب اسم الصنف كما تعرفه، بالعربية أو التركية. يمكنك إضافة حتى 30 صنفاً في طلب واحد.</p>
						<div class="sh-items" data-sh-items>
							<div class="sh-items__head" aria-hidden="true"><span>الصنف</span><span>العلامة</span><span>الكمية</span><span>الوحدة</span><span></span></div>
							<?php for ( $sh_i = 0; $sh_i < 3; $sh_i++ ) : ?>
								<div class="sh-item" data-sh-item>
									<input type="text" name="sh_item_name[]" placeholder="<?php echo 0 === $sh_i ? 'مثال: عصير كابي برتقال 1 لتر' : 'صنف آخر (اختياري)'; ?>" aria-label="اسم الصنف"<?php echo 0 === $sh_i ? ' required aria-required="true"' : ''; ?>>
									<input type="text" name="sh_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
									<input type="number" name="sh_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
									<select name="sh_item_unit[]" aria-label="الوحدة">
										<?php foreach ( $sh_opts['unit'] as $sh_u ) : ?>
											<option value="<?php echo esc_attr( $sh_u ); ?>"><?php echo esc_html( $sh_u ); ?></option>
										<?php endforeach; ?>
									</select>
									<button type="button" class="sh-icon-btn sh-item__remove" data-sh-remove-item aria-label="حذف السطر"><?php shami_the_icon( 'close', '', 18 ); ?></button>
								</div>
							<?php endfor; ?>
						</div>
						<button type="button" class="sh-btn sh-btn--ghost sh-btn--sm" data-sh-add-item>+ أضف صنفاً آخر</button>
					</fieldset>

					<fieldset class="sh-form__section">
						<legend><span>03</span> تفاصيل إضافية</legend>
						<div class="sh-form__grid">
							<?php shami_form_field( 'special', 'urgency' ); ?>
							<div class="sh-field">
								<label for="sh-special-photo">صورة الصنف (اختياري)</label>
								<label class="sh-drop" for="sh-special-photo">
									<span data-sh-file-label>اضغط لإرفاق صورة (JPG / PNG حتى 5MB)</span>
									<input type="file" id="sh-special-photo" name="sh_photo" accept="image/jpeg,image/png,image/webp" data-sh-file>
								</label>
							</div>
							<?php shami_form_field( 'special', 'notes', array( 'placeholder' => 'مثال: الكمية شهرية ثابتة، أو أقبل بديلاً مشابهاً إن لم يتوفر' ) ); ?>
						</div>
					</fieldset>

					<button type="submit" class="sh-btn sh-btn--primary sh-btn--lg sh-btn--block">أرسل طلب التوريد</button>
					<p class="sh-form__privacy">نستخدم بياناتك فقط للتواصل بخصوص هذا الطلب.</p>
				</form>
				<template data-sh-item-tpl>
					<div class="sh-item" data-sh-item>
						<input type="text" name="sh_item_name[]" placeholder="اسم الصنف" aria-label="اسم الصنف">
						<input type="text" name="sh_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
						<input type="number" name="sh_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
						<select name="sh_item_unit[]" aria-label="الوحدة">
							<?php foreach ( $sh_opts['unit'] as $sh_u ) : ?>
								<option value="<?php echo esc_attr( $sh_u ); ?>"><?php echo esc_html( $sh_u ); ?></option>
							<?php endforeach; ?>
						</select>
						<button type="button" class="sh-icon-btn sh-item__remove" data-sh-remove-item aria-label="حذف السطر"><?php shami_the_icon( 'close', '', 18 ); ?></button>
					</div>
				</template>
			<?php endif; ?>
		</div>

		<aside class="sh-req__aside">
			<div class="sh-aside-card">
				<h2>كيف نؤمّن طلبك؟</h2>
				<ol class="sh-mini-steps">
					<li><b>ترسل الطلب</b> باسم الصنف والكمية المطلوبة.</li>
					<li><b>نبحث لك</b> لدى المصانع والوكلاء والمستوردين.</li>
					<li><b>نرد عليك</b> بالسعر والتوفر وموعد التوريد.</li>
					<li><b>نورّده</b> مع طلبيتك القادمة أو بشحنة مستقلة.</li>
				</ol>
			</div>
			<div class="sh-aside-card">
				<h2>أصناف يطلبها التجار عادةً</h2>
				<ul class="sh-tags">
					<li>مشروبات وعصائر</li><li>شاي وقهوة</li><li>معلبات ومواد غذائية</li><li>منظفات ومستلزمات</li><li>علامات عربية ومستوردة</li><li>أصناف موسمية ورمضانية</li><li>حلويات بالوزن</li><li>علكة وسكاكر</li>
				</ul>
			</div>
			<?php if ( shami_wa_number() ) : ?>
				<div class="sh-aside-card sh-aside-card--wa">
					<h2>تفضّل واتساب؟</h2>
					<p>صوّر الصنف أو عبوته وأرسل الصورة مباشرة إلى فريق المشتريات.</p>
					<a class="sh-btn sh-btn--wa sh-btn--block" href="<?php echo esc_url( shami_wa_link( 'مرحباً، أبحث عن صنف غير موجود في قائمة الأسعار:' ) ); ?>" target="_blank" rel="noopener">أرسل صورة الصنف</a>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</main>
<?php
get_footer();
