<?php
/**
 * Template Name: الطلب السريع للبقاليات
 *
 * قائمة واحدة بكل المنتجات: بحث فوري، تصفية حسب القسم والعلامة،
 * عدّاد كراتين لكل صنف متزامن مع السلة، وشريط إجمالي ثابت مع إرسال عبر واتساب.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mr_groups = function_exists( 'maria_quick_order_groups' ) ? maria_quick_order_groups() : array();
$mr_brands = maria_brands();
$mr_total  = 0;
foreach ( $mr_groups as $mr_g ) {
	$mr_total += count( $mr_g['rows'] );
}
$mr_cat_colors = maria_categories();
?>
<main id="main" class="mr-main mr-qo" data-mr-qo>
	<section class="mr-qo-hero">
		<div class="mr-container">
			<?php maria_breadcrumbs(); ?>
			<div class="mr-qo-hero__row">
				<div>
					<h1 class="mr-qo-hero__title"><?php the_title(); ?></h1>
					<p class="mr-qo-hero__intro"><?php echo esc_html( maria_n_items( $mr_total ) ); ?> في قائمة واحدة. ابحث، واضغط <b>+</b> لتحديد عدد الكراتين، ثم أكمل الطلب.</p>
				</div>
				<ol class="mr-qo-steps" aria-label="طريقة الطلب">
					<li><span>1</span> ابحث أو اختر القسم</li>
					<li><span>2</span> حدّد عدد الكراتين</li>
					<li><span>3</span> أكمل الطلب أو أرسله عبر واتساب</li>
				</ol>
			</div>
		</div>
	</section>

	<?php if ( ! $mr_groups ) : ?>
		<div class="mr-container"><div class="mr-empty"><h2>لا توجد منتجات بعد</h2><p>أضف المنتجات من «المظهر ← إعداد متجر ماريا».</p></div></div>
	<?php else : ?>

	<div class="mr-qo-bar" data-mr-qo-bar>
		<div class="mr-container mr-qo-bar__row">
			<label class="mr-qo-search">
				<?php maria_the_icon( 'search', '', 20 ); ?>
				<span class="screen-reader-text">ابحث في قائمة الطلب</span>
				<input type="search" data-mr-qo-search placeholder="ابحث في القائمة: بسكريم، Popkek، أولكر…" autocomplete="off">
			</label>
			<div class="mr-qo-chips">
			<div class="mr-qo-filters" role="group" aria-label="تصفية القائمة">
				<button type="button" class="mr-qo-filter is-active" data-filter="all" aria-pressed="true">الكل</button>
				<?php foreach ( $mr_groups as $mr_slug => $mr_g ) : ?>
					<button type="button" class="mr-qo-filter" data-filter="cat:<?php echo esc_attr( $mr_slug ); ?>" aria-pressed="false"><?php maria_the_icon( $mr_g['icon'], '', 16 ); ?> <?php echo esc_html( $mr_g['name'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="mr-qo-filter mr-qo-filter--sale" data-filter="sale" aria-pressed="false"><?php maria_the_icon( 'percent', '', 16 ); ?> عليها عرض</button>
				<button type="button" class="mr-qo-filter mr-qo-filter--selected" data-filter="selected" aria-pressed="false"><?php maria_the_icon( 'check', '', 16 ); ?> المحدد <span class="mr-qo-filter__count" data-mr-qo-lines-badge>0</span></button>
				<button type="button" class="mr-qo-filter mr-qo-filter--favs" data-filter="favs" aria-pressed="false"><?php maria_the_icon( 'heart', '', 16 ); ?> المفضلة <span class="mr-qo-filter__count" data-mr-fav-count hidden>0</span></button>
			</div>
			<div class="mr-qo-brandfilter" role="group" aria-label="تصفية حسب العلامة">
				<button type="button" class="mr-qo-brand is-active" data-brand="all" aria-pressed="true">كل الشركات</button>
				<?php foreach ( $mr_brands as $mr_s => $mr_b ) : ?>
					<button type="button" class="mr-qo-brand" data-brand="<?php echo esc_attr( $mr_s ); ?>" aria-pressed="false" style="--c:<?php echo esc_attr( $mr_b['c1'] ); ?>"><?php echo esc_html( $mr_b['ar'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="mr-qo-print" data-mr-print aria-label="طباعة قائمة الأسعار"><?php maria_the_icon( 'print', '', 18 ); ?> <span>طباعة القائمة</span></button>
			</div>
			</div>
		</div>
	</div>

	<div class="mr-container mr-qo-list">
		<div class="mr-qo-printhead" aria-hidden="true">
			<strong><?php bloginfo( 'name' ); ?> — قائمة أسعار الجملة</strong>
			<span><?php echo esc_html( wp_date( 'Y-m-d' ) ); ?><?php echo maria_wa_number() ? ' • واتساب: +' . esc_html( maria_wa_number() ) : ''; ?></span>
		</div>
		<?php foreach ( $mr_groups as $mr_slug => $mr_g ) : ?>
			<section class="mr-qo-group" id="qo-<?php echo esc_attr( $mr_slug ); ?>" data-group="<?php echo esc_attr( $mr_slug ); ?>" style="--c:<?php echo esc_attr( $mr_cat_colors[ $mr_slug ]['color'] ); ?>;--t:<?php echo esc_attr( $mr_cat_colors[ $mr_slug ]['tint'] ); ?>">
				<h2 class="mr-qo-group__title"><span class="mr-qo-group__icon"><?php maria_the_icon( $mr_g['icon'], '', 22 ); ?></span><?php echo esc_html( $mr_g['name'] ); ?> <small><?php echo esc_html( maria_n_items( count( $mr_g['rows'] ) ) ); ?></small></h2>
				<ul class="mr-qo-rows">
					<?php
					foreach ( $mr_g['rows'] as $mr_r ) {
						echo maria_qo_row_html( $mr_r, $mr_slug ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</ul>
			</section>
		<?php endforeach; ?>

		<div class="mr-empty mr-qo-empty" data-mr-qo-empty hidden>
			<div class="mr-empty__icon"><?php maria_the_icon( 'search', '', 34 ); ?></div>
			<h2>لا توجد نتائج مطابقة</h2>
			<p>لم تجد المنتج؟ أخبرنا باسمه ونؤمّنه لبقالتك.</p>
			<a class="mr-btn mr-btn--primary" href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر</a>
		</div>

		<aside class="mr-qo-missing">
			<?php maria_the_icon( 'sparkle', '', 22 ); ?>
			<p><strong>تحتاج صنفاً غير موجود في القائمة؟</strong> مشروبات أو مواد غذائية أو منظفات أو أي علامة أخرى، <a href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلبه من هنا</a> ونؤمّنه لك.</p>
		</aside>
	</div>

	<div class="mr-qo-summary" data-mr-qo-summary>
		<div class="mr-container mr-qo-summary__row">
			<div class="mr-qo-summary__stats" aria-live="polite">
				<span class="mr-qo-summary__stat"><b data-mr-qo-lines>0</b> صنف</span>
				<span class="mr-qo-summary__stat"><b data-mr-qo-cartons>0</b> كرتونة</span>
				<?php if ( maria_show_prices() ) : ?>
					<span class="mr-qo-summary__total"><small>الإجمالي التقديري</small><b data-mr-qo-total>0</b></span>
				<?php endif; ?>
			</div>
			<div class="mr-qo-summary__actions">
				<button type="button" class="mr-icon-btn mr-qo-summary__clear" data-mr-qo-clear aria-label="تفريغ الكميات"><?php maria_the_icon( 'trash', '', 20 ); ?></button>
				<?php if ( maria_wa_number() ) : ?>
					<a class="mr-btn mr-btn--wa" href="<?php echo esc_url( maria_wa_link() ); ?>" target="_blank" rel="noopener" data-mr-qo-wa><?php maria_the_icon( 'whatsapp', '', 20 ); ?> <span>أرسل عبر واتساب</span></a>
				<?php endif; ?>
				<a class="mr-btn mr-btn--primary" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-mr-qo-checkout><span>أكمل الطلب</span> <?php maria_the_icon( 'arrow-left', '', 20 ); ?></a>
			</div>
		</div>
	</div>
	<?php endif; ?>
</main>
<?php
get_footer();
