<?php
/**
 * Template Name: الطلب السريع للبقاليات
 *
 * قائمة واحدة بكل المنتجات: بحث فوري، تصفية حسب القسم والعلامة،
 * عدّاد كراتين لكل صنف متزامن مع السلة، وشريط إجمالي ثابت مع إرسال عبر واتساب.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();

$lz_groups = function_exists( 'lazza_quick_order_groups' ) ? lazza_quick_order_groups() : array();
$lz_brands = lazza_brands();
$lz_total  = 0;
foreach ( $lz_groups as $lz_g ) {
	$lz_total += count( $lz_g['rows'] );
}
$lz_cat_colors = lazza_categories();
?>
<main id="main" class="lz-main lz-qo" data-lz-qo>
	<section class="lz-qo-hero">
		<div class="lz-container">
			<?php lazza_breadcrumbs(); ?>
			<div class="lz-qo-hero__row">
				<div>
					<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'bolt', '', 16 ); ?> <?php echo esc_html( lazza_n_items( $lz_total ) ); ?> في قائمة واحدة</span>
					<h1 class="lz-qo-hero__title"><?php the_title(); ?></h1>
					<div class="lz-qo-hero__intro">
						<?php
						while ( have_posts() ) :
							the_post();
							the_content();
						endwhile;
						?>
					</div>
				</div>
				<ol class="lz-qo-steps" aria-label="طريقة الطلب">
					<li><span>1</span> ابحث أو اختر القسم</li>
					<li><span>2</span> اضغط <b>+</b> لعدد الكراتين</li>
					<li><span>3</span> أتمم الطلب أو أرسله واتساب</li>
				</ol>
			</div>
		</div>
	</section>

	<?php if ( ! $lz_groups ) : ?>
		<div class="lz-container"><div class="lz-empty"><h2>لا توجد منتجات بعد</h2><p>أضف المنتجات من «المظهر ← إعداد متجر لذّة».</p></div></div>
	<?php else : ?>

	<div class="lz-qo-bar" data-lz-qo-bar>
		<div class="lz-container lz-qo-bar__row">
			<label class="lz-qo-search">
				<?php lazza_the_icon( 'search', '', 20 ); ?>
				<span class="screen-reader-text">ابحث في قائمة الطلب</span>
				<input type="search" data-lz-qo-search placeholder="ابحث بالاسم العربي أو التركي: بسكريم، Popkek…" autocomplete="off">
			</label>
			<div class="lz-qo-filters" role="group" aria-label="تصفية القائمة">
				<button type="button" class="lz-qo-filter is-active" data-filter="all" aria-pressed="true">الكل</button>
				<?php foreach ( $lz_groups as $lz_slug => $lz_g ) : ?>
					<button type="button" class="lz-qo-filter" data-filter="cat:<?php echo esc_attr( $lz_slug ); ?>" aria-pressed="false" style="--c:<?php echo esc_attr( $lz_cat_colors[ $lz_slug ]['color'] ); ?>"><?php lazza_the_icon( $lz_g['icon'], '', 16 ); ?> <?php echo esc_html( $lz_g['name'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="lz-qo-filter lz-qo-filter--sale" data-filter="sale" aria-pressed="false"><?php lazza_the_icon( 'fire', '', 16 ); ?> عليها عرض</button>
				<button type="button" class="lz-qo-filter lz-qo-filter--selected" data-filter="selected" aria-pressed="false"><?php lazza_the_icon( 'check', '', 16 ); ?> المحدد <span class="lz-qo-filter__count" data-lz-qo-lines-badge>0</span></button>
			</div>
			<div class="lz-qo-brandfilter" role="group" aria-label="تصفية حسب العلامة">
				<button type="button" class="lz-qo-brand is-active" data-brand="all" aria-pressed="true">كل العلامات</button>
				<?php foreach ( $lz_brands as $lz_s => $lz_b ) : ?>
					<button type="button" class="lz-qo-brand" data-brand="<?php echo esc_attr( $lz_s ); ?>" aria-pressed="false" style="--c:<?php echo esc_attr( $lz_b['c1'] ); ?>"><?php echo esc_html( $lz_b['ar'] ); ?></button>
				<?php endforeach; ?>
				<button type="button" class="lz-qo-print" data-lz-print aria-label="طباعة قائمة الأسعار"><?php lazza_the_icon( 'print', '', 18 ); ?> <span>طباعة القائمة</span></button>
			</div>
		</div>
	</div>

	<div class="lz-container lz-qo-list">
		<div class="lz-qo-printhead" aria-hidden="true">
			<strong><?php bloginfo( 'name' ); ?> — قائمة أسعار الجملة</strong>
			<span><?php echo esc_html( wp_date( 'Y-m-d' ) ); ?><?php echo lazza_wa_number() ? ' • واتساب: +' . esc_html( lazza_wa_number() ) : ''; ?></span>
		</div>
		<?php foreach ( $lz_groups as $lz_slug => $lz_g ) : ?>
			<section class="lz-qo-group" id="qo-<?php echo esc_attr( $lz_slug ); ?>" data-group="<?php echo esc_attr( $lz_slug ); ?>" style="--c:<?php echo esc_attr( $lz_cat_colors[ $lz_slug ]['color'] ); ?>;--t:<?php echo esc_attr( $lz_cat_colors[ $lz_slug ]['tint'] ); ?>">
				<h2 class="lz-qo-group__title"><span class="lz-qo-group__icon"><?php lazza_the_icon( $lz_g['icon'], '', 22 ); ?></span><?php echo esc_html( $lz_g['name'] ); ?> <small><?php echo esc_html( lazza_n_items( count( $lz_g['rows'] ) ) ); ?></small></h2>
				<ul class="lz-qo-rows">
					<?php
					foreach ( $lz_g['rows'] as $lz_r ) {
						echo lazza_qo_row_html( $lz_r, $lz_slug ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</ul>
			</section>
		<?php endforeach; ?>

		<div class="lz-empty lz-qo-empty" data-lz-qo-empty hidden>
			<div class="lz-empty__icon"><?php lazza_the_icon( 'search', '', 34 ); ?></div>
			<h2>لا توجد نتائج مطابقة</h2>
			<p>لم تجد المنتج؟ أخبرنا باسمه ونؤمّنه لبقالتك.</p>
			<a class="lz-btn lz-btn--primary" href="<?php echo esc_url( lazza_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر</a>
		</div>

		<aside class="lz-qo-missing">
			<?php lazza_the_icon( 'sparkle', '', 22 ); ?>
			<p><strong>تحتاج منتجاً غير موجود في القائمة؟</strong> مشروبات، مواد غذائية، منظفات أو أي علامة أخرى — <a href="<?php echo esc_url( lazza_page_url( 'special_request' ) ); ?>">اطلبه من هنا</a> ونؤمّنه لك.</p>
		</aside>
	</div>

	<div class="lz-qo-summary" data-lz-qo-summary>
		<div class="lz-container lz-qo-summary__row">
			<div class="lz-qo-summary__stats" aria-live="polite">
				<span class="lz-qo-summary__stat"><b data-lz-qo-lines>0</b> صنف</span>
				<span class="lz-qo-summary__stat"><b data-lz-qo-cartons>0</b> كرتونة</span>
				<?php if ( lazza_show_prices() ) : ?>
					<span class="lz-qo-summary__total"><small>الإجمالي التقديري</small><b data-lz-qo-total>0</b></span>
				<?php endif; ?>
			</div>
			<div class="lz-qo-summary__actions">
				<button type="button" class="lz-icon-btn lz-qo-summary__clear" data-lz-qo-clear aria-label="تفريغ الكميات"><?php lazza_the_icon( 'trash', '', 20 ); ?></button>
				<?php if ( lazza_wa_number() ) : ?>
					<a class="lz-btn lz-btn--wa" href="<?php echo esc_url( lazza_wa_link() ); ?>" target="_blank" rel="noopener" data-lz-qo-wa><?php lazza_the_icon( 'whatsapp', '', 20 ); ?> <span>أرسل واتساب</span></a>
				<?php endif; ?>
				<a class="lz-btn lz-btn--gold" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-lz-qo-checkout><?php lazza_the_icon( 'check', '', 20 ); ?> <span>إتمام الطلب</span></a>
			</div>
		</div>
	</div>
	<?php endif; ?>
</main>
<?php
get_footer();
