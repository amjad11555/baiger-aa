<?php
/**
 * Template Name: طلب منتج غير متوفر
 *
 * يطلب فيه صاحب البقالة أي منتج غير موجود في المتجر لنؤمّنه له.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();
$mr_opts = maria_request_options();
?>
<main id="main" class="mr-main mr-request-page">
	<section class="mr-page-hero mr-page-hero--request">
		<div class="mr-container">
			<?php maria_breadcrumbs(); ?>
			<span class="mr-kicker">كل احتياجات بقالتك من مكان واحد</span>
			<h1 class="mr-page-hero__title">لم تجد المنتج؟ <span class="mr-hl">نؤمّنه لبقالتك</span></h1>
			<div class="mr-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
			<ul class="mr-hero__trust">
				<li><?php maria_the_icon( 'clock', '', 20 ); ?> رد خلال ساعات</li>
				<li><?php maria_the_icon( 'tag', '', 20 ); ?> بسعر الجملة</li>
				<li><?php maria_the_icon( 'whatsapp', '', 20 ); ?> متابعة عبر واتساب</li>
			</ul>
		</div>
	</section>

	<div class="mr-container mr-req__grid">
		<div class="mr-form-card" id="mr-form">
			<?php if ( ! maria_form_feedback( 'special' ) ) : ?>
				<form class="mr-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" data-mr-form>
					<?php echo maria_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="mr_type" value="special">

					<fieldset class="mr-form__section">
						<legend><span>1</span> بيانات البقالة</legend>
						<div class="mr-form__grid">
							<?php
							maria_form_field( 'special', 'name', array( 'autocomplete' => 'name' ) );
							maria_form_field( 'special', 'shop', array( 'placeholder' => 'مثال: ماركت النور', 'autocomplete' => 'organization' ) );
							maria_form_field( 'special', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
							maria_form_field( 'special', 'city', array( 'placeholder' => 'مثال: إسطنبول – أسنيورت' ) );
							?>
						</div>
					</fieldset>

					<fieldset class="mr-form__section">
						<legend><span>2</span> المنتجات التي تحتاجها</legend>
						<p class="mr-form__hint">اكتب اسم المنتج كما تعرفه، بالعربي أو التركي. يمكنك إضافة حتى 30 منتجاً في طلب واحد.</p>
						<div class="mr-items" data-mr-items>
							<div class="mr-items__head" aria-hidden="true"><span>المنتج</span><span>العلامة</span><span>الكمية</span><span>الوحدة</span><span></span></div>
							<?php for ( $mr_i = 0; $mr_i < 3; $mr_i++ ) : ?>
								<div class="mr-item" data-mr-item>
									<input type="text" name="mr_item_name[]" placeholder="<?php echo 0 === $mr_i ? 'مثال: عصير كابي برتقال 1 لتر' : 'منتج آخر (اختياري)'; ?>" aria-label="اسم المنتج"<?php echo 0 === $mr_i ? ' required aria-required="true"' : ''; ?>>
									<input type="text" name="mr_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
									<input type="number" name="mr_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
									<select name="mr_item_unit[]" aria-label="الوحدة">
										<?php foreach ( $mr_opts['unit'] as $mr_u ) : ?>
											<option value="<?php echo esc_attr( $mr_u ); ?>"><?php echo esc_html( $mr_u ); ?></option>
										<?php endforeach; ?>
									</select>
									<button type="button" class="mr-icon-btn mr-item__remove" data-mr-remove-item aria-label="حذف السطر"><?php maria_the_icon( 'close', '', 18 ); ?></button>
								</div>
							<?php endfor; ?>
						</div>
						<button type="button" class="mr-btn mr-btn--ghost mr-btn--sm" data-mr-add-item><?php maria_the_icon( 'plus', '', 18 ); ?> أضف منتجاً آخر</button>
					</fieldset>

					<fieldset class="mr-form__section">
						<legend><span>3</span> تفاصيل إضافية</legend>
						<div class="mr-form__grid">
							<?php maria_form_field( 'special', 'urgency' ); ?>
							<div class="mr-field">
								<label for="mr-special-photo">صورة المنتج (اختياري)</label>
								<label class="mr-drop" for="mr-special-photo">
									<?php maria_the_icon( 'upload', '', 24 ); ?>
									<span data-mr-file-label>اضغط لإرفاق صورة (JPG / PNG حتى 5MB)</span>
									<input type="file" id="mr-special-photo" name="mr_photo" accept="image/jpeg,image/png,image/webp" data-mr-file>
								</label>
							</div>
							<?php maria_form_field( 'special', 'notes', array( 'placeholder' => 'مثال: أحتاجه أسبوعياً، أو أريد بديلاً مشابهاً إن لم يتوفر' ) ); ?>
						</div>
					</fieldset>

					<button type="submit" class="mr-btn mr-btn--primary mr-btn--lg mr-btn--block"><?php maria_the_icon( 'send', '', 20 ); ?> أرسل الطلب</button>
					<p class="mr-form__privacy"><?php maria_the_icon( 'shield', '', 16 ); ?> نستخدم بياناتك فقط للتواصل بخصوص طلبك.</p>
				</form>
				<template data-mr-item-tpl>
					<div class="mr-item" data-mr-item>
						<input type="text" name="mr_item_name[]" placeholder="اسم المنتج" aria-label="اسم المنتج">
						<input type="text" name="mr_item_brand[]" placeholder="العلامة" aria-label="العلامة التجارية">
						<input type="number" name="mr_item_qty[]" min="1" max="99999" inputmode="numeric" placeholder="1" aria-label="الكمية">
						<select name="mr_item_unit[]" aria-label="الوحدة">
							<?php foreach ( $mr_opts['unit'] as $mr_u ) : ?>
								<option value="<?php echo esc_attr( $mr_u ); ?>"><?php echo esc_html( $mr_u ); ?></option>
							<?php endforeach; ?>
						</select>
						<button type="button" class="mr-icon-btn mr-item__remove" data-mr-remove-item aria-label="حذف السطر"><?php maria_the_icon( 'close', '', 18 ); ?></button>
					</div>
				</template>
			<?php endif; ?>
		</div>

		<aside class="mr-req__aside">
			<div class="mr-aside-card">
				<h2>كيف نؤمّن طلبك؟</h2>
				<ol class="mr-mini-steps">
					<li><b>ترسل الطلب</b> باسم المنتج والكمية.</li>
					<li><b>نبحث لك</b> لدى المصانع والموردين.</li>
					<li><b>نرد عليك</b> بالسعر والتوفر عبر واتساب.</li>
					<li><b>نوصله</b> مع طلبك القادم أو فوراً.</li>
				</ol>
			</div>
			<div class="mr-aside-card">
				<h2>أمثلة يطلبها أصحاب البقالات</h2>
				<ul class="mr-tags">
					<li>مشروبات وعصائر</li><li>شاي وقهوة</li><li>معلبات ومواد غذائية</li><li>منظفات ومستلزمات</li><li>علامات عربية ومستوردة</li><li>أصناف موسمية ورمضانية</li><li>حلويات بالوزن</li><li>علكة وسكاكر</li>
				</ul>
			</div>
			<?php if ( maria_wa_number() ) : ?>
				<div class="mr-aside-card mr-aside-card--wa">
					<h2>تفضّل واتساب؟</h2>
					<p>صوّر المنتج وأرسل الصورة إلينا مباشرة، ونرد عليك بالسعر.</p>
					<a class="mr-btn mr-btn--wa mr-btn--block" href="<?php echo esc_url( maria_wa_link( 'مرحباً، أبحث عن منتج غير موجود في المتجر:' ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 20 ); ?> أرسل صورة المنتج</a>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</main>
<?php
get_footer();
