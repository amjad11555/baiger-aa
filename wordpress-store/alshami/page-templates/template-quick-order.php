<?php
/**
 * Template Name: قائمة أسعار الجملة
 *
 * قائمة واحدة بكل الأصناف: بحث فوري، تصفية حسب القسم والعلامة،
 * عدّاد كراتين لكل صنف متزامن مع الطلبية، وشريط إجمالي ثابت مع إرسال عبر واتساب.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();

$sh_groups = function_exists( 'shami_quick_order_groups' ) ? shami_quick_order_groups() : array();
$sh_brands = shami_brands();
$sh_total  = 0;
foreach ( $sh_groups as $sh_g ) {
	$sh_total += count( $sh_g['rows'] );
}
?>
<main id="main" class="sh-main sh-qo" data-sh-qo>
	<section class="sh-qo-hero">
		<div class="sh-container">
			<?php shami_breadcrumbs(); ?>
			<div class="sh-qo-hero__row">
				<div>
					<p class="sh-eyebrow">محدّثة بتاريخ <?php echo esc_html( shami_ar_date() ); ?></p>
					<h1 class="sh-qo-hero__title"><?php the_title(); ?></h1>
					<p class="sh-qo-hero__intro"><?php echo esc_html( shami_n_items( $sh_total ) ); ?> بسعر الكرتونة وسعر القطعة. ابحث عن الصنف، وحدد عدد الكراتين، ثم أتمّ الطلبية أو أرسلها إلى قسم المبيعات عبر واتساب.</p>
				</div>
				<ol class="sh-qo-steps" aria-label="طريقة الطلب">
					<li><span>01</span> ابحث أو اختر القسم</li>
					<li><span>02</span> حدّد عدد الكراتين</li>
					<li><span>03</span> أتمّ الطلبية أو أرسلها عبر واتساب</li>
				</ol>
			</div>
		</div>
	</section>

	<?php if ( ! $sh_groups ) : ?>
		<div class="sh-container"><div class="sh-empty"><h2>لا توجد أصناف بعد</h2><p>أضف الأصناف من «المظهر ← إعداد متجر الشامي».</p></div></div>
	<?php else : ?>

	<div class="sh-qo-bar" data-sh-qo-bar>
		<div class="sh-container sh-qo-bar__row">
			<label class="sh-qo-search">
				<?php shami_the_icon( 'search', '', 20 ); ?>
				<span class="screen-reader-text">ابحث في قائمة الأسعار</span>
				<input type="search" data-sh-qo-search placeholder="ابحث بالاسم العربي أو التركي: بسكريم، Popkek، أولكر…" autocomplete="off">
			</label>
			<div class="sh-qo-chips">
			<div class="sh-qo-filters" role="group" aria-label="تصفية القائمة">
				<button type="button" class="sh-qo-filter is-active" data-filter="all" aria-pressed="true">الكل</button>
				<?php foreach ( $sh_groups as $sh_slug => $sh_g ) : ?>
					<button type="button" class="sh-qo-filter" data-filter="cat:<?php echo esc_attr( $sh_slug ); ?>" aria-pressed="false"><?php echo esc_html( $sh_g['name'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="sh-qo-filter sh-qo-filter--sale" data-filter="sale" aria-pressed="false">عليها عرض</button>
				<button type="button" class="sh-qo-filter sh-qo-filter--selected" data-filter="selected" aria-pressed="false">المحدد <span class="sh-qo-filter__count" data-sh-qo-lines-badge>0</span></button>
			</div>
			<div class="sh-qo-brandfilter" role="group" aria-label="تصفية حسب العلامة">
				<button type="button" class="sh-qo-brand is-active" data-brand="all" aria-pressed="true">كل العلامات</button>
				<?php foreach ( $sh_brands as $sh_s => $sh_b ) : ?>
					<button type="button" class="sh-qo-brand" data-brand="<?php echo esc_attr( $sh_s ); ?>" aria-pressed="false"><?php echo esc_html( $sh_b['ar'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="sh-qo-print" data-sh-print>طباعة القائمة</button>
			</div>
			</div>
		</div>
	</div>

	<div class="sh-container sh-qo-list">
		<div class="sh-qo-printhead" aria-hidden="true">
			<strong><?php bloginfo( 'name' ); ?> للتجارة — قائمة أسعار الجملة</strong>
			<span><?php echo esc_html( wp_date( 'Y-m-d' ) ); ?><?php echo shami_wa_number() ? ' • المبيعات: +' . esc_html( shami_wa_number() ) : ''; ?></span>
		</div>
		<?php foreach ( $sh_groups as $sh_slug => $sh_g ) : ?>
			<section class="sh-qo-group" id="qo-<?php echo esc_attr( $sh_slug ); ?>" data-group="<?php echo esc_attr( $sh_slug ); ?>">
				<h2 class="sh-qo-group__title"><span class="sh-qo-group__thumb"><?php echo shami_img( $sh_g['image'], '', array( 'sizes' => '48px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $sh_g['name'] ); ?> <small><?php echo esc_html( shami_n_items( count( $sh_g['rows'] ) ) ); ?></small></h2>
				<ul class="sh-qo-rows">
					<?php
					foreach ( $sh_g['rows'] as $sh_r ) {
						echo shami_qo_row_html( $sh_r, $sh_slug ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</ul>
			</section>
		<?php endforeach; ?>

		<div class="sh-empty sh-qo-empty" data-sh-qo-empty hidden>
			<h2>لا توجد نتائج مطابقة</h2>
			<p>جرّب كلمة أقصر أو اسم العلامة بالتركية، أو أرسل اسم الصنف ونؤمّنه لك من المصدر.</p>
			<a class="sh-btn sh-btn--primary" href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">أرسل طلب توريد خاص</a>
		</div>

		<aside class="sh-qo-missing">
			<p><strong>تحتاج صنفاً غير موجود في القائمة؟</strong> مشروبات أو مواد غذائية أو أي علامة أخرى، <a href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">أرسل طلب توريد خاص</a> ويتواصل معك فريق المشتريات بالسعر والتوفر.</p>
		</aside>
	</div>

	<div class="sh-qo-summary" data-sh-qo-summary>
		<div class="sh-container sh-qo-summary__row">
			<div class="sh-qo-summary__stats" aria-live="polite">
				<span class="sh-qo-summary__stat"><b data-sh-qo-lines>0</b> صنف</span>
				<span class="sh-qo-summary__stat"><b data-sh-qo-cartons>0</b> كرتونة</span>
				<?php if ( shami_show_prices() ) : ?>
					<span class="sh-qo-summary__total"><small>الإجمالي التقديري</small><b data-sh-qo-total>0</b></span>
				<?php endif; ?>
			</div>
			<div class="sh-qo-summary__actions">
				<button type="button" class="sh-qo-summary__clear" data-sh-qo-clear>تفريغ</button>
				<?php if ( shami_wa_number() ) : ?>
					<a class="sh-btn sh-btn--wa" href="<?php echo esc_url( shami_wa_link() ); ?>" target="_blank" rel="noopener" data-sh-qo-wa><span class="sh-qo-wa__long">أرسل عبر واتساب</span><span class="sh-qo-wa__short">واتساب</span></a>
				<?php endif; ?>
				<a class="sh-btn sh-btn--accent" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-sh-qo-checkout>إتمام الطلبية</a>
			</div>
		</div>
	</div>
	<?php endif; ?>
</main>
<?php
get_footer();
