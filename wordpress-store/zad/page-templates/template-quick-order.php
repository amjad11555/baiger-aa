<?php
/**
 * Template Name: قائمة أسعار الجملة
 *
 * قائمة واحدة بكل الأصناف: بحث فوري، تصفية حسب القسم والعلامة،
 * عدّاد كراتين لكل صنف متزامن مع الطلبية، وشريط إجمالي ثابت مع إرسال عبر واتساب.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();

$zd_groups = function_exists( 'zad_quick_order_groups' ) ? zad_quick_order_groups() : array();
$zd_brands = zad_brands();
$zd_total  = 0;
foreach ( $zd_groups as $zd_g ) {
	$zd_total += count( $zd_g['rows'] );
}
?>
<main id="main" class="zd-main zd-qo" data-zd-qo>
	<section class="zd-qo-hero">
		<div class="zd-container">
			<?php zad_breadcrumbs(); ?>
			<div class="zd-qo-hero__row">
				<div>
					<p class="zd-eyebrow">محدّثة بتاريخ <?php echo esc_html( zad_ar_date() ); ?></p>
					<h1 class="zd-qo-hero__title"><?php the_title(); ?></h1>
					<p class="zd-qo-hero__intro"><?php echo esc_html( zad_n_items( $zd_total ) ); ?> بسعر الكرتونة وسعر القطعة. ابحث عن الصنف، وحدد عدد الكراتين، ثم أتمّ الطلبية أو أرسلها إلى قسم المبيعات عبر واتساب.</p>
				</div>
				<ol class="zd-qo-steps" aria-label="طريقة الطلب">
					<li><span>01</span> ابحث أو اختر القسم</li>
					<li><span>02</span> حدّد عدد الكراتين</li>
					<li><span>03</span> أتمّ الطلبية أو أرسلها عبر واتساب</li>
				</ol>
			</div>
		</div>
	</section>

	<?php if ( ! $zd_groups ) : ?>
		<div class="zd-container"><div class="zd-empty"><h2>لا توجد أصناف بعد</h2><p>أضف الأصناف من «المظهر ← إعداد متجر زاد».</p></div></div>
	<?php else : ?>

	<div class="zd-qo-bar" data-zd-qo-bar>
		<div class="zd-container zd-qo-bar__row">
			<label class="zd-qo-search">
				<?php zad_the_icon( 'search', '', 20 ); ?>
				<span class="screen-reader-text">ابحث في قائمة الأسعار</span>
				<input type="search" data-zd-qo-search placeholder="ابحث بالاسم العربي أو التركي: بسكريم، Popkek، أولكر…" autocomplete="off">
			</label>
			<div class="zd-qo-chips">
			<div class="zd-qo-filters" role="group" aria-label="تصفية القائمة">
				<button type="button" class="zd-qo-filter is-active" data-filter="all" aria-pressed="true">الكل</button>
				<?php foreach ( $zd_groups as $zd_slug => $zd_g ) : ?>
					<button type="button" class="zd-qo-filter" data-filter="cat:<?php echo esc_attr( $zd_slug ); ?>" aria-pressed="false"><?php echo esc_html( $zd_g['name'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="zd-qo-filter zd-qo-filter--sale" data-filter="sale" aria-pressed="false">عليها عرض</button>
				<button type="button" class="zd-qo-filter zd-qo-filter--selected" data-filter="selected" aria-pressed="false">المحدد <span class="zd-qo-filter__count" data-zd-qo-lines-badge>0</span></button>
			</div>
			<div class="zd-qo-brandfilter" role="group" aria-label="تصفية حسب العلامة">
				<button type="button" class="zd-qo-brand is-active" data-brand="all" aria-pressed="true">كل العلامات</button>
				<?php foreach ( $zd_brands as $zd_s => $zd_b ) : ?>
					<button type="button" class="zd-qo-brand" data-brand="<?php echo esc_attr( $zd_s ); ?>" aria-pressed="false"><?php echo esc_html( $zd_b['ar'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="zd-qo-print" data-zd-print>طباعة القائمة</button>
			</div>
			</div>
		</div>
	</div>

	<div class="zd-container zd-qo-list">
		<div class="zd-qo-printhead" aria-hidden="true">
			<strong><?php bloginfo( 'name' ); ?> للتجارة — قائمة أسعار الجملة</strong>
			<span><?php echo esc_html( wp_date( 'Y-m-d' ) ); ?><?php echo zad_wa_number() ? ' • المبيعات: +' . esc_html( zad_wa_number() ) : ''; ?></span>
		</div>
		<?php foreach ( $zd_groups as $zd_slug => $zd_g ) : ?>
			<section class="zd-qo-group" id="qo-<?php echo esc_attr( $zd_slug ); ?>" data-group="<?php echo esc_attr( $zd_slug ); ?>">
				<h2 class="zd-qo-group__title"><span class="zd-qo-group__thumb"><?php echo zad_img( $zd_g['image'], '', array( 'sizes' => '48px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $zd_g['name'] ); ?> <small><?php echo esc_html( zad_n_items( count( $zd_g['rows'] ) ) ); ?></small></h2>
				<ul class="zd-qo-rows">
					<?php
					foreach ( $zd_g['rows'] as $zd_r ) {
						echo zad_qo_row_html( $zd_r, $zd_slug ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</ul>
			</section>
		<?php endforeach; ?>

		<div class="zd-empty zd-qo-empty" data-zd-qo-empty hidden>
			<h2>لا توجد نتائج مطابقة</h2>
			<p>جرّب كلمة أقصر أو اسم العلامة بالتركية، أو أرسل اسم الصنف ونؤمّنه لك من المصدر.</p>
			<a class="zd-btn zd-btn--primary" href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">أرسل طلب توريد خاص</a>
		</div>

		<aside class="zd-qo-missing">
			<p><strong>تحتاج صنفاً غير موجود في القائمة؟</strong> مشروبات أو مواد غذائية أو أي علامة أخرى، <a href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">أرسل طلب توريد خاص</a> ويتواصل معك فريق المشتريات بالسعر والتوفر.</p>
		</aside>
	</div>

	<div class="zd-qo-summary" data-zd-qo-summary>
		<div class="zd-container zd-qo-summary__row">
			<div class="zd-qo-summary__stats" aria-live="polite">
				<span class="zd-qo-summary__stat"><b data-zd-qo-lines>0</b> صنف</span>
				<span class="zd-qo-summary__stat"><b data-zd-qo-cartons>0</b> كرتونة</span>
				<?php if ( zad_show_prices() ) : ?>
					<span class="zd-qo-summary__total"><small>الإجمالي التقديري</small><b data-zd-qo-total>0</b></span>
				<?php endif; ?>
			</div>
			<div class="zd-qo-summary__actions">
				<button type="button" class="zd-qo-summary__clear" data-zd-qo-clear>تفريغ</button>
				<?php if ( zad_wa_number() ) : ?>
					<a class="zd-btn zd-btn--wa" href="<?php echo esc_url( zad_wa_link() ); ?>" target="_blank" rel="noopener" data-zd-qo-wa><span class="zd-qo-wa__long">أرسل عبر واتساب</span><span class="zd-qo-wa__short">واتساب</span></a>
				<?php endif; ?>
				<a class="zd-btn zd-btn--accent" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-zd-qo-checkout>إتمام الطلبية</a>
			</div>
		</div>
	</div>
	<?php endif; ?>
</main>
<?php
get_footer();
