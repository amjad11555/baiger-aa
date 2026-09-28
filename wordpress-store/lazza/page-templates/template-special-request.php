<?php
/**
 * Template Name: طلب منتج غير متوفر
 *
 * يطلب فيه صاحب البقالة أي منتج غير موجود في المتجر لنؤمّنه له.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();
$lz_opts = lazza_request_options();
?>
<main id="main" class="lz-main lz-req">
	<section class="lz-page-hero lz-page-hero--request">
		<div class="lz-container">
			<?php lazza_breadcrumbs(); ?>
			<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'sparkle', '', 16 ); ?> كل احتياجات بقالتك من مكان واحد</span>
			<h1 class="lz-page-hero__title">ما لقيت المنتج؟ <span class="lz-hl">نأمّنه لبقالتك</span></h1>
			<div class="lz-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
			<ul class="lz-hero__trust">
				<li><?php lazza_the_icon( 'clock', '', 20 ); ?> رد خلال ساعات</li>
				<li><?php lazza_the_icon( 'tag', '', 20 ); ?> بسعر الجملة</li>
				<li><?php lazza_the_icon( 'whatsapp', '', 20 ); ?> متابعة عبر واتساب</li>
			</ul>
		</div>
	</section>

	<div class="lz-container lz-req__grid">
		<div class="lz-form-card" id="lz-form">
			<?php if ( ! lazza_form_feedback( 'special' ) ) : ?>
				<form class="lz-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" data-lz-form>
					<?php echo lazza_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="lz_type" value="special">

					<fieldset class="lz-form__section">
						<legend><span>1</span> بيانات البقالة</legend>
						<div class="lz-form__grid">
							<?php
							lazza_form_field( 'special', 'name', array( 'autocomplete' => 'name' ) );
							lazza_form_field( 'special', 'shop', array( 'placeholder' => 'مثال: ماركت النور', 'autocomplete' => 'organization' ) );
							lazza_form_field( 'special', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
							lazza_form_field( 'special', 'city', array( 'placeholder' => 'مثال: إسطنبول – أسنيورت' ) );
							?>
						</div>
					</fieldset>

					<fieldset class="lz-form__section">
						<legend><span>2</span> المنتجات التي تحتاجها</legend>
						<p class="lz-form__hint">اكتب اسم المنتج كما تعرفه (بالعربي أو التركي)، ويمكنك إضافة حتى 30 منتجاً.</p>
						<div class="lz-items" data-lz-items>
							<div class="lz-items__head" aria-hidden="true"><span>المنتج</span><span>العلامة</span><span>الكمية</span><span>الوحدة</span><span></span></div>
							<?php for ( $lz_i = 0; $lz_i < 3; $lz_i++ ) : ?>
								<div class="lz-item" data-lz-item>
									<input type="text" name="lz_item_name[]" placeholder="<?php echo 0 === $lz_i ? 'مثال: عصير كابي برتقال 1 لتر' : 'منتج آخر (اختياري)'; ?>" aria-label="اسم المنتج"<?php echo 0 === $lz_i ? ' required aria-required="true"' : ''; ?>>
									<input type="text" name="lz_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
									<input type="number" name="lz_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
									<select name="lz_item_unit[]" aria-label="الوحدة">
										<?php foreach ( $lz_opts['unit'] as $lz_u ) : ?>
											<option value="<?php echo esc_attr( $lz_u ); ?>"><?php echo esc_html( $lz_u ); ?></option>
										<?php endforeach; ?>
									</select>
									<button type="button" class="lz-icon-btn lz-item__remove" data-lz-remove-item aria-label="حذف السطر"><?php lazza_the_icon( 'close', '', 18 ); ?></button>
								</div>
							<?php endfor; ?>
						</div>
						<button type="button" class="lz-btn lz-btn--ghost lz-btn--sm" data-lz-add-item><?php lazza_the_icon( 'plus', '', 18 ); ?> أضف منتجاً آخر</button>
					</fieldset>

					<fieldset class="lz-form__section">
						<legend><span>3</span> تفاصيل إضافية</legend>
						<div class="lz-form__grid">
							<?php lazza_form_field( 'special', 'urgency' ); ?>
							<div class="lz-field">
								<label for="lz-special-photo">صورة المنتج (اختياري)</label>
								<label class="lz-drop" for="lz-special-photo">
									<?php lazza_the_icon( 'upload', '', 24 ); ?>
									<span data-lz-file-label>اضغط لإرفاق صورة (JPG / PNG حتى 5MB)</span>
									<input type="file" id="lz-special-photo" name="lz_photo" accept="image/jpeg,image/png,image/webp" data-lz-file>
								</label>
							</div>
							<?php lazza_form_field( 'special', 'notes', array( 'placeholder' => 'مثال: أحتاجه أسبوعياً، أو أريد بديلاً مشابهاً إن لم يتوفر' ) ); ?>
						</div>
					</fieldset>

					<button type="submit" class="lz-btn lz-btn--primary lz-btn--lg lz-btn--block"><?php lazza_the_icon( 'send', '', 20 ); ?> أرسل الطلب</button>
					<p class="lz-form__privacy"><?php lazza_the_icon( 'shield', '', 16 ); ?> نستخدم بياناتك فقط للتواصل بخصوص طلبك.</p>
				</form>
				<template data-lz-item-tpl>
					<div class="lz-item" data-lz-item>
						<input type="text" name="lz_item_name[]" placeholder="اسم المنتج" aria-label="اسم المنتج">
						<input type="text" name="lz_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
						<input type="number" name="lz_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
						<select name="lz_item_unit[]" aria-label="الوحدة">
							<?php foreach ( $lz_opts['unit'] as $lz_u ) : ?>
								<option value="<?php echo esc_attr( $lz_u ); ?>"><?php echo esc_html( $lz_u ); ?></option>
							<?php endforeach; ?>
						</select>
						<button type="button" class="lz-icon-btn lz-item__remove" data-lz-remove-item aria-label="حذف السطر"><?php lazza_the_icon( 'close', '', 18 ); ?></button>
					</div>
				</template>
			<?php endif; ?>
		</div>

		<aside class="lz-req__aside">
			<div class="lz-aside-card">
				<h2>كيف نؤمّن طلبك؟</h2>
				<ol class="lz-mini-steps">
					<li><b>ترسل الطلب</b> باسم المنتج والكمية.</li>
					<li><b>نبحث لك</b> لدى المصانع والموردين.</li>
					<li><b>نرد عليك</b> بالتوفر والسعر عبر واتساب.</li>
					<li><b>نوصله</b> مع طلبك القادم أو فوراً.</li>
				</ol>
			</div>
			<div class="lz-aside-card">
				<h2>أمثلة يطلبها أصحاب البقالات</h2>
				<ul class="lz-tags">
					<li>مشروبات وعصائر</li><li>شاي وقهوة</li><li>معلبات ومواد غذائية</li><li>منظفات ومستلزمات</li><li>علامات عربية ومستوردة</li><li>أصناف موسمية ورمضانية</li><li>حلويات بالوزن</li><li>علكة وسكاكر</li>
				</ul>
			</div>
			<?php if ( lazza_wa_number() ) : ?>
				<div class="lz-aside-card lz-aside-card--wa">
					<h2>تفضّل واتساب؟</h2>
					<p>صوّر المنتج وأرسله لنا مباشرة، وسنرد عليك بالسعر.</p>
					<a class="lz-btn lz-btn--wa lz-btn--block" href="<?php echo esc_url( lazza_wa_link( 'مرحباً، أبحث عن منتج غير موجود في المتجر:' ) ); ?>" target="_blank" rel="noopener"><?php lazza_the_icon( 'whatsapp', '', 20 ); ?> أرسل صورة المنتج</a>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</main>
<?php
get_footer();
