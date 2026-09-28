<?php
/**
 * الترويسة: شريط علوي للمعلومات التجارية، ترويسة بالشعار والبحث والطلبية، وقائمة الأقسام.
 *
 * على الجوال: صف الشعار والطلبية ثابت، والبحث تحته، والقائمة في درج جانبي.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_has_wc  = class_exists( 'WooCommerce' );
$sh_cats    = shami_categories();
$sh_brands  = shami_all_brands();
$sh_current = '';
if ( $sh_has_wc && is_product_category() ) {
	$sh_current = get_queried_object()->slug;
} elseif ( $sh_has_wc && is_product() ) {
	$sh_current = shami_product_main_cat( get_the_ID() );
}
$sh_count = $sh_has_wc && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$sh_phone = (string) shami_opt( 'phone' );
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
<a class="sh-skip" href="#main">تخطَّ إلى المحتوى</a>

<div class="sh-topbar">
	<div class="sh-container sh-topbar__row">
		<p class="sh-topbar__text"><?php echo esc_html( shami_opt( 'announcement' ) ); ?></p>
		<nav class="sh-topbar__links" aria-label="روابط سريعة">
			<a href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">طلب توريد خاص</a>
			<a href="<?php echo esc_url( shami_page_url( 'export' ) ); ?>">التصدير</a>
			<?php if ( $sh_phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $sh_phone ) ); ?>"><span class="sh-topbar__label">المبيعات</span> <bdi dir="ltr"><?php echo esc_html( $sh_phone ); ?></bdi></a>
			<?php endif; ?>
		</nav>
	</div>
</div>

<header class="sh-header" id="sh-header">
	<div class="sh-container sh-header__row">
		<button type="button" class="sh-icon-btn sh-header__menu" data-sh-open="sh-drawer" aria-controls="sh-drawer" aria-expanded="false" aria-label="فتح القائمة">
			<?php shami_the_icon( 'menu', '', 24 ); ?>
		</button>

		<div class="sh-header__logo"><?php shami_logo(); ?></div>

		<div class="sh-header__search"><?php get_search_form(); ?></div>

		<div class="sh-header__actions">
			<?php if ( $sh_has_wc ) : ?>
				<a class="sh-header__link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">حسابي</a>
				<button type="button" class="sh-cart-btn" data-sh-open="sh-cart-drawer" aria-controls="sh-cart-drawer" aria-expanded="false" aria-label="الطلبية">
					<span class="sh-cart-btn__icon"><?php shami_the_icon( 'cart', '', 22 ); ?><span class="sh-cart-count" data-count="<?php echo (int) $sh_count; ?>"><?php echo (int) $sh_count; ?></span></span>
					<span class="sh-cart-btn__label"><small>الطلبية</small><span class="sh-cart-total"><?php echo wp_kses_post( WC()->cart ? WC()->cart->get_cart_subtotal() : '' ); ?></span></span>
				</button>
			<?php endif; ?>
		</div>
	</div>
</header>

<nav class="sh-nav" aria-label="أقسام المتجر">
	<div class="sh-container sh-nav__row">
		<ul class="sh-nav__list">
			<?php foreach ( $sh_cats as $sh_slug => $sh_cat ) : ?>
				<li>
					<a class="sh-nav__link<?php echo 'offers' === $sh_slug ? ' sh-nav__link--offers' : ''; ?><?php echo $sh_current === $sh_slug ? ' is-current' : ''; ?>" href="<?php echo esc_url( shami_cat_url( $sh_slug ) ); ?>"<?php echo $sh_current === $sh_slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $sh_cat['title'] ); ?></a>
				</li>
			<?php endforeach; ?>
			<li class="sh-nav__more">
				<button type="button" class="sh-nav__link" aria-expanded="false" aria-controls="sh-nav-brands" data-sh-toggle="sh-nav-brands">الشركات<span class="sh-nav__caret" aria-hidden="true"></span></button>
				<div class="sh-nav__panel" id="sh-nav-brands" hidden>
					<?php foreach ( $sh_brands as $sh_slug => $sh_b ) : ?>
						<?php if ( $sh_b['count'] ) : ?>
							<a href="<?php echo esc_url( $sh_b['url'] ); ?>"><span class="sh-nav__word" lang="tr"><?php echo esc_html( $sh_b['latin'] ); ?></span><span><?php echo esc_html( $sh_b['ar'] ); ?><small><?php echo esc_html( shami_n_items( $sh_b['count'] ) ); ?></small></span></a>
						<?php endif; ?>
					<?php endforeach; ?>
					<a class="sh-nav__all" href="<?php echo esc_url( shami_page_url( 'brands' ) ); ?>">كل الشركات</a>
				</div>
			</li>
			<li><a class="sh-nav__link" href="<?php echo esc_url( shami_page_url( 'export' ) ); ?>">التصدير</a></li>
		</ul>
		<a class="sh-btn sh-btn--accent sh-btn--sm sh-nav__quick" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">قائمة الأسعار</a>
	</div>
</nav>

<div class="sh-drawer sh-drawer--start" id="sh-drawer" role="dialog" aria-modal="true" aria-label="القائمة" aria-hidden="true">
	<div class="sh-drawer__head">
		<?php shami_logo(); ?>
		<button type="button" class="sh-icon-btn" data-sh-close aria-label="إغلاق القائمة"><?php shami_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="sh-drawer__body">
		<a class="sh-btn sh-btn--primary sh-btn--block" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a>
		<p class="sh-drawer__label">الأقسام</p>
		<ul class="sh-drawer__cats">
			<?php foreach ( $sh_cats as $sh_slug => $sh_cat ) : ?>
				<li><a href="<?php echo esc_url( shami_cat_url( $sh_slug ) ); ?>"><span class="sh-drawer__thumb"><?php echo shami_img( $sh_cat['image'], '', array( 'sizes' => '56px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo esc_html( $sh_cat['title'] ); ?></span><small><?php echo esc_html( shami_n_items( shami_cat_count( $sh_slug ) ) ); ?></small></a></li>
			<?php endforeach; ?>
		</ul>
		<p class="sh-drawer__label">الشركات</p>
		<ul class="sh-drawer__brands">
			<?php foreach ( $sh_brands as $sh_slug => $sh_b ) : ?>
				<?php if ( $sh_b['count'] ) : ?>
					<li><a href="<?php echo esc_url( $sh_b['url'] ); ?>"><span lang="tr"><?php echo esc_html( $sh_b['latin'] ); ?></span><?php echo esc_html( $sh_b['ar'] ); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<p class="sh-drawer__label">الشركة</p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'sh-drawer__menu',
				'depth'          => 1,
				'fallback_cb'    => 'shami_fallback_menu',
			)
		);
		?>
		<?php if ( shami_wa_number() ) : ?>
			<a class="sh-btn sh-btn--wa sh-btn--block" href="<?php echo esc_url( shami_wa_link( 'مرحباً، أرغب بالتواصل مع قسم المبيعات في ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener">تحدث مع المبيعات عبر واتساب</a>
		<?php endif; ?>
	</div>
</div>

<?php if ( $sh_has_wc ) : ?>
<div class="sh-drawer sh-drawer--end sh-cart-drawer" id="sh-cart-drawer" role="dialog" aria-modal="true" aria-label="الطلبية" aria-hidden="true">
	<div class="sh-drawer__head">
		<strong class="sh-drawer__title">الطلبية <span class="sh-cart-count" data-count="<?php echo (int) $sh_count; ?>"><?php echo (int) $sh_count; ?></span></strong>
		<button type="button" class="sh-icon-btn" data-sh-close aria-label="إغلاق الطلبية"><?php shami_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="sh-drawer__body">
		<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	</div>
</div>
<?php endif; ?>
<div class="sh-overlay" data-sh-close hidden></div>
