<?php
/**
 * الترويسة بأسلوب Kalles: شريط إعلان أسود، ثم ترويسة بيضاء لاصقة
 * (الشعار في البداية، القائمة في الوسط مع قائمة كبيرة للتشكيلة، والأيقونات في النهاية).
 * على الجوال: زر القائمة، الشعار في الوسط، والطلبية؛ وشريط تنقل سفلي في التذييل.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_has_wc  = class_exists( 'WooCommerce' );
$zd_cats    = zad_categories();
$zd_brands  = zad_all_brands();
$zd_current = '';
if ( $zd_has_wc && is_product_category() ) {
	$zd_current = get_queried_object()->slug;
} elseif ( $zd_has_wc && is_product() ) {
	$zd_current = zad_product_main_cat( get_the_ID() );
}
$zd_count   = $zd_has_wc && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$zd_account = $zd_has_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$zd_shop    = $zd_has_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="format-detection" content="telephone=no">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="zd-skip" href="#main">تخطَّ إلى المحتوى</a>

<?php
$zd_bar = function_exists( 'zad_announce_bar' ) ? zad_announce_bar() : ( zad_opt( 'announcement' ) ? array( zad_opt( 'announcement' ), zad_page_url( 'quick_order' ), 'افتح قائمة الأسعار' ) : null );
?>
<?php if ( $zd_bar ) : ?>
<div class="zd-announce" role="region" aria-label="إعلان" data-zd-announce>
	<div class="zd-container zd-announce__row">
		<p class="zd-announce__text"><?php echo esc_html( $zd_bar[0] ); ?> <a href="<?php echo esc_url( $zd_bar[1] ); ?>"><?php echo esc_html( $zd_bar[2] ); ?></a></p>
		<button type="button" class="zd-announce__close" data-zd-announce-close aria-label="إغلاق الإعلان"><?php zad_the_icon( 'close', '', 16 ); ?></button>
	</div>
</div>
<?php endif; ?>

<header class="zd-header" id="zd-header">
	<div class="zd-container zd-header__row">
		<div class="zd-header__start">
			<button type="button" class="zd-icon-btn zd-header__menu" data-zd-open="zd-drawer" aria-controls="zd-drawer" aria-expanded="false" aria-label="فتح القائمة"><?php zad_the_icon( 'menu', '', 24 ); ?></button>
			<div class="zd-header__logo"><?php zad_logo( 'light', false, true ); ?></div>
		</div>

		<nav class="zd-nav" aria-label="القائمة الرئيسية">
			<ul class="zd-nav__list">
				<li class="zd-nav__item zd-nav__item--mega">
					<a class="zd-nav__link<?php echo $zd_current ? ' is-current' : ''; ?>" href="<?php echo esc_url( $zd_shop ); ?>" aria-haspopup="true">التشكيلة<span class="zd-nav__caret" aria-hidden="true"></span></a>
					<div class="zd-mega">
						<div class="zd-container zd-mega__grid">
							<ul class="zd-mega__cats">
								<?php foreach ( $zd_cats as $zd_slug => $zd_cat ) : ?>
									<li>
										<a class="zd-mega__cat" href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>">
											<span class="zd-mega__img"><?php echo zad_img( $zd_cat['image'], '', array( 'sizes' => '200px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
											<span class="zd-mega__name"><?php echo esc_html( $zd_cat['title'] ); ?></span>
											<small><?php echo esc_html( zad_n_items( zad_cat_count( $zd_slug ) ) ); ?></small>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
							<div class="zd-mega__side">
								<p class="zd-mega__title">العلامات</p>
								<ul class="zd-mega__links">
									<?php foreach ( $zd_brands as $zd_b ) : ?>
										<?php if ( $zd_b['count'] ) : ?>
											<li><a href="<?php echo esc_url( $zd_b['url'] ); ?>"><?php echo esc_html( $zd_b['ar'] ); ?> <span lang="tr"><?php echo esc_html( $zd_b['latin'] ); ?></span></a></li>
										<?php endif; ?>
									<?php endforeach; ?>
									<li><a href="<?php echo esc_url( zad_page_url( 'brands' ) ); ?>">كل العلامات</a></li>
								</ul>
								<p class="zd-mega__title">للتجار</p>
								<ul class="zd-mega__links">
									<li><a href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a></li>
									<li><a href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">طلب توريد خاص</a></li>
									<li><a href="<?php echo esc_url( zad_page_url( 'delivery' ) ); ?>">التوريد والدفع</a></li>
								</ul>
							</div>
						</div>
					</div>
				</li>
				<li class="zd-nav__item"><a class="zd-nav__link" href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">قائمة الأسعار</a></li>
				<li class="zd-nav__item"><a class="zd-nav__link<?php echo 'offers' === $zd_current ? ' is-current' : ''; ?>" href="<?php echo esc_url( zad_cat_url( 'offers' ) ); ?>">العروض<span class="zd-nav__label zd-nav__label--sale">Sale</span></a></li>
				<li class="zd-nav__item zd-nav__item--drop">
					<a class="zd-nav__link" href="<?php echo esc_url( zad_page_url( 'brands' ) ); ?>" aria-haspopup="true">العلامات<span class="zd-nav__caret" aria-hidden="true"></span></a>
					<ul class="zd-dropmenu">
						<?php foreach ( $zd_brands as $zd_b ) : ?>
							<?php if ( $zd_b['count'] ) : ?>
								<li><a href="<?php echo esc_url( $zd_b['url'] ); ?>"><?php echo esc_html( $zd_b['ar'] ); ?><small><?php echo esc_html( zad_n_items( $zd_b['count'] ) ); ?></small></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</li>
				<li class="zd-nav__item"><a class="zd-nav__link" href="<?php echo esc_url( zad_page_url( 'export' ) ); ?>">التصدير<span class="zd-nav__label">جديد</span></a></li>
				<li class="zd-nav__item"><a class="zd-nav__link" href="<?php echo esc_url( zad_page_url( 'about' ) ); ?>">عن بسكاتو</a></li>
			</ul>
		</nav>

		<div class="zd-header__icons">
			<button type="button" class="zd-icon-btn" data-zd-open="zd-search-panel" aria-controls="zd-search-panel" aria-expanded="false" aria-label="البحث"><?php zad_the_icon( 'search', '', 22 ); ?></button>
			<a class="zd-icon-btn zd-header__account" href="<?php echo esc_url( $zd_account ); ?>" aria-label="حسابي"><?php zad_the_icon( 'user', '', 22 ); ?></a>
			<button type="button" class="zd-icon-btn zd-header__wish" data-zd-open="zd-wish-drawer" aria-controls="zd-wish-drawer" aria-expanded="false" aria-label="الأصناف المحفوظة"><?php zad_the_icon( 'heart', '', 22 ); ?><span class="zd-bubble" data-zd-wish-count hidden>0</span></button>
			<?php if ( $zd_has_wc && function_exists( 'zad_notif_button' ) ) : ?>
				<?php zad_notif_button(); ?>
			<?php endif; ?>
			<?php if ( $zd_has_wc ) : ?>
				<button type="button" class="zd-icon-btn zd-header__cart" data-zd-open="zd-cart-drawer" aria-controls="zd-cart-drawer" aria-expanded="false" aria-label="الطلبية"><?php zad_the_icon( 'bag', '', 22 ); ?><span class="zd-bubble zd-cart-count" data-count="<?php echo (int) $zd_count; ?>"><?php echo (int) $zd_count; ?></span></button>
			<?php endif; ?>
		</div>
	</div>
</header>

<div class="zd-drawer zd-drawer--top zd-search-panel" id="zd-search-panel" role="dialog" aria-modal="true" aria-label="البحث" aria-hidden="true">
	<div class="zd-container zd-search-panel__inner">
		<div class="zd-search-panel__head">
			<p class="zd-search-panel__title">ابحث في تشكيلة <?php bloginfo( 'name' ); ?></p>
			<button type="button" class="zd-icon-btn" data-zd-close aria-label="إغلاق البحث"><?php zad_the_icon( 'close', '', 22 ); ?></button>
		</div>
		<?php get_search_form(); ?>
	</div>
</div>

<div class="zd-drawer zd-drawer--start zd-menu-drawer" id="zd-drawer" role="dialog" aria-modal="true" aria-label="القائمة" aria-hidden="true">
	<div class="zd-drawer__tabs" role="tablist">
		<button type="button" class="zd-drawer__tab is-active" data-zd-dtab="zd-dt-menu" aria-selected="true">القائمة</button>
		<button type="button" class="zd-drawer__tab" data-zd-dtab="zd-dt-cats" aria-selected="false">الأقسام</button>
		<button type="button" class="zd-icon-btn zd-drawer__x" data-zd-close aria-label="إغلاق القائمة"><?php zad_the_icon( 'close', '', 20 ); ?></button>
	</div>
	<div class="zd-drawer__body">
		<div class="zd-drawer__pane" id="zd-dt-menu">
			<ul class="zd-mlist">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a></li>
				<li><a href="<?php echo esc_url( $zd_shop ); ?>">كل الأصناف</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a></li>
				<li><a href="<?php echo esc_url( zad_cat_url( 'offers' ) ); ?>">العروض <span class="zd-nav__label zd-nav__label--sale">Sale</span></a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'brands' ) ); ?>">العلامات</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'export' ) ); ?>">التصدير والجملة الدولية</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">طلب توريد خاص</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'about' ) ); ?>">عن بسكاتو</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'contact' ) ); ?>">تواصل مع المبيعات</a></li>
			</ul>
			<div class="zd-drawer__foot">
				<a class="zd-drawer__line" href="<?php echo esc_url( $zd_account ); ?>">حسابي</a>
				<?php if ( zad_opt( 'phone' ) ) : ?>
					<a class="zd-drawer__line" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', (string) zad_opt( 'phone' ) ) ); ?>">المبيعات: <bdi dir="ltr"><?php echo esc_html( zad_opt( 'phone' ) ); ?></bdi></a>
				<?php endif; ?>
				<?php if ( zad_wa_number() ) : ?>
					<a class="zd-btn zd-btn--wa zd-btn--block" href="<?php echo esc_url( zad_wa_link( 'مرحباً، أرغب بالتواصل مع قسم المبيعات في ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener">تحدث مع المبيعات عبر واتساب</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="zd-drawer__pane" id="zd-dt-cats" hidden>
			<ul class="zd-drawer__cats">
				<?php foreach ( $zd_cats as $zd_slug => $zd_cat ) : ?>
					<li><a href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>"><span class="zd-drawer__thumb"><?php echo zad_img( $zd_cat['image'], '', array( 'sizes' => '56px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo esc_html( $zd_cat['title'] ); ?></span><small><?php echo esc_html( zad_n_items( zad_cat_count( $zd_slug ) ) ); ?></small></a></li>
				<?php endforeach; ?>
			</ul>
			<p class="zd-drawer__label">العلامات</p>
			<ul class="zd-drawer__brands">
				<?php foreach ( $zd_brands as $zd_b ) : ?>
					<?php if ( $zd_b['count'] ) : ?>
						<li><a href="<?php echo esc_url( $zd_b['url'] ); ?>"><span lang="tr"><?php echo esc_html( $zd_b['latin'] ); ?></span><?php echo esc_html( $zd_b['ar'] ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<div class="zd-drawer zd-drawer--end zd-wish-drawer" id="zd-wish-drawer" role="dialog" aria-modal="true" aria-label="الأصناف المحفوظة" aria-hidden="true">
	<div class="zd-drawer__head">
		<strong class="zd-drawer__title">الأصناف المحفوظة</strong>
		<button type="button" class="zd-icon-btn" data-zd-close aria-label="إغلاق"><?php zad_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="zd-drawer__body" data-zd-wish-list></div>
</div>

<?php if ( $zd_has_wc ) : ?>
<div class="zd-drawer zd-drawer--end zd-cart-drawer" id="zd-cart-drawer" role="dialog" aria-modal="true" aria-label="الطلبية" aria-hidden="true">
	<div class="zd-drawer__head">
		<strong class="zd-drawer__title">الطلبية <span class="zd-cart-count" data-count="<?php echo (int) $zd_count; ?>"><?php echo (int) $zd_count; ?></span></strong>
		<button type="button" class="zd-icon-btn" data-zd-close aria-label="إغلاق الطلبية"><?php zad_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="zd-drawer__body">
		<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	</div>
</div>
<?php endif; ?>

<div class="zd-modal" id="zd-qv" role="dialog" aria-modal="true" aria-label="عرض سريع" hidden>
	<div class="zd-modal__box">
		<button type="button" class="zd-icon-btn zd-modal__close" data-zd-qv-close aria-label="إغلاق"><?php zad_the_icon( 'close', '', 22 ); ?></button>
		<div class="zd-modal__body" data-zd-qv-body></div>
	</div>
</div>
<div class="zd-overlay" data-zd-close hidden></div>
