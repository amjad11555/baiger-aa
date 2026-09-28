<?php
/**
 * الترويسة.
 *
 * على الجوال: صف الشعار يختفي عند التمرير ويبقى شريط البحث والسلة ظاهرين دائماً.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_has_wc  = class_exists( 'WooCommerce' );
$mr_cats    = maria_categories();
$mr_brands  = maria_all_brands();
$mr_current = '';
if ( $mr_has_wc && is_product_category() ) {
	$mr_current = get_queried_object()->slug;
} elseif ( $mr_has_wc && is_product() ) {
	$mr_current = maria_product_main_cat( get_the_ID() );
}
$mr_count = $mr_has_wc && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
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
<a class="mr-skip" href="#main">تخطَّ إلى المحتوى</a>

<div class="mr-topbar">
	<div class="mr-container mr-topbar__row">
		<p class="mr-topbar__text"><?php maria_the_icon( 'truck', '', 16 ); ?><span><?php echo esc_html( maria_opt( 'announcement' ) ); ?></span></p>
		<nav class="mr-topbar__links" aria-label="روابط سريعة">
			<a href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر</a>
			<a href="<?php echo esc_url( maria_page_url( 'export' ) ); ?>"><?php maria_the_icon( 'globe', '', 15 ); ?> الجملة الدولية</a>
			<?php if ( maria_opt( 'phone' ) ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', maria_opt( 'phone' ) ) ); ?>" dir="ltr"><?php maria_the_icon( 'phone', '', 15 ); ?> <?php echo esc_html( maria_opt( 'phone' ) ); ?></a>
			<?php endif; ?>
		</nav>
	</div>
</div>

<header class="mr-header" id="mr-header">
	<div class="mr-container mr-header__row">
		<button type="button" class="mr-icon-btn mr-header__menu" data-mr-open="mr-drawer" aria-controls="mr-drawer" aria-expanded="false" aria-label="فتح القائمة">
			<?php maria_the_icon( 'menu', '', 24 ); ?>
		</button>

		<div class="mr-header__logo"><?php maria_logo(); ?></div>

		<div class="mr-header__search"><?php get_search_form(); ?></div>

		<div class="mr-header__actions">
			<?php if ( maria_wa_number() ) : ?>
				<a class="mr-icon-btn mr-header__wa" href="<?php echo esc_url( maria_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener" aria-label="تواصل عبر واتساب"><?php maria_the_icon( 'whatsapp', '', 22 ); ?></a>
			<?php endif; ?>
			<?php if ( $mr_has_wc ) : ?>
				<a class="mr-icon-btn mr-header__account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="حسابي وطلباتي"><?php maria_the_icon( 'user', '', 22 ); ?></a>
				<button type="button" class="mr-cart-btn" data-mr-open="mr-cart-drawer" aria-controls="mr-cart-drawer" aria-expanded="false" aria-label="سلة الطلب">
					<span class="mr-cart-btn__icon"><?php maria_the_icon( 'cart', '', 22 ); ?><span class="mr-cart-count" data-count="<?php echo (int) $mr_count; ?>"><?php echo (int) $mr_count; ?></span></span>
					<span class="mr-cart-btn__label"><small>سلة الطلب</small><span class="mr-cart-total"><?php echo wp_kses_post( WC()->cart ? WC()->cart->get_cart_subtotal() : '' ); ?></span></span>
				</button>
			<?php endif; ?>
		</div>
	</div>
</header>

<nav class="mr-nav" aria-label="أقسام المتجر">
	<div class="mr-container mr-nav__row">
		<ul class="mr-nav__list">
			<?php foreach ( $mr_cats as $mr_slug => $mr_cat ) : ?>
				<li>
					<a class="mr-nav__link<?php echo 'offers' === $mr_slug ? ' mr-nav__link--offers' : ''; ?><?php echo $mr_current === $mr_slug ? ' is-current' : ''; ?>" href="<?php echo esc_url( maria_cat_url( $mr_slug ) ); ?>"<?php echo $mr_current === $mr_slug ? ' aria-current="page"' : ''; ?>>
						<?php maria_the_icon( $mr_cat['icon'], '', 18 ); ?><span><?php echo esc_html( $mr_cat['name'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
			<li class="mr-nav__more">
				<button type="button" class="mr-nav__link" aria-expanded="false" aria-controls="mr-nav-brands" data-mr-toggle="mr-nav-brands"><?php maria_the_icon( 'store', '', 18 ); ?><span>الشركات</span><?php maria_the_icon( 'chevron-down', 'mr-nav__caret', 16 ); ?></button>
				<div class="mr-nav__panel" id="mr-nav-brands" hidden>
					<?php foreach ( $mr_brands as $mr_slug => $mr_b ) : ?>
						<?php if ( $mr_b['count'] ) : ?>
							<a href="<?php echo esc_url( $mr_b['url'] ); ?>" style="--c:<?php echo esc_attr( $mr_b['c1'] ); ?>"><span class="mr-nav__word" lang="tr"><?php echo esc_html( $mr_b['latin'] ); ?></span><span><?php echo esc_html( $mr_b['ar'] ); ?><small><?php echo esc_html( maria_n_items( $mr_b['count'] ) ); ?></small></span></a>
						<?php endif; ?>
					<?php endforeach; ?>
					<a class="mr-nav__all" href="<?php echo esc_url( maria_page_url( 'brands' ) ); ?>">كل الشركات <?php maria_the_icon( 'arrow-left', '', 16 ); ?></a>
				</div>
			</li>
		</ul>
		<a class="mr-nav__quick" href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>"><?php maria_the_icon( 'list', '', 18 ); ?> قائمة الطلب السريع</a>
	</div>
</nav>

<div class="mr-drawer mr-drawer--start" id="mr-drawer" role="dialog" aria-modal="true" aria-label="القائمة" aria-hidden="true">
	<div class="mr-drawer__head">
		<?php maria_logo(); ?>
		<button type="button" class="mr-icon-btn" data-mr-close aria-label="إغلاق القائمة"><?php maria_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="mr-drawer__body">
		<a class="mr-btn mr-btn--primary mr-btn--block" href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>"><?php maria_the_icon( 'list', '', 20 ); ?> قائمة الطلب السريع</a>
		<p class="mr-drawer__label">الأقسام</p>
		<ul class="mr-drawer__cats">
			<?php foreach ( $mr_cats as $mr_slug => $mr_cat ) : ?>
				<li><a href="<?php echo esc_url( maria_cat_url( $mr_slug ) ); ?>" style="--c:<?php echo esc_attr( $mr_cat['color'] ); ?>;--t:<?php echo esc_attr( $mr_cat['tint'] ); ?>"><span class="mr-drawer__ico"><?php maria_the_icon( $mr_cat['icon'], '', 22 ); ?></span><span><?php echo esc_html( $mr_cat['name'] ); ?></span><small><?php echo esc_html( maria_n_items( maria_cat_count( $mr_slug ) ) ); ?></small></a></li>
			<?php endforeach; ?>
		</ul>
		<p class="mr-drawer__label">الشركات</p>
		<ul class="mr-drawer__brands">
			<?php foreach ( $mr_brands as $mr_slug => $mr_b ) : ?>
				<?php if ( $mr_b['count'] ) : ?>
					<li><a href="<?php echo esc_url( $mr_b['url'] ); ?>" style="--c:<?php echo esc_attr( $mr_b['c1'] ); ?>"><span lang="tr"><?php echo esc_html( $mr_b['latin'] ); ?></span><?php echo esc_html( $mr_b['ar'] ); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<p class="mr-drawer__label">روابط مهمة</p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'mr-drawer__menu',
				'depth'          => 1,
				'fallback_cb'    => 'maria_fallback_menu',
			)
		);
		?>
		<?php if ( maria_wa_number() ) : ?>
			<a class="mr-btn mr-btn--wa mr-btn--block" href="<?php echo esc_url( maria_wa_link( 'مرحباً، أرغب بالطلب' ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 20 ); ?> اطلب عبر واتساب</a>
		<?php endif; ?>
	</div>
</div>

<?php if ( $mr_has_wc ) : ?>
<div class="mr-drawer mr-drawer--end mr-cart-drawer" id="mr-cart-drawer" role="dialog" aria-modal="true" aria-label="سلة الطلب" aria-hidden="true">
	<div class="mr-drawer__head">
		<strong class="mr-drawer__title"><?php maria_the_icon( 'cart', '', 22 ); ?> سلة الطلب <span class="mr-cart-count" data-count="<?php echo (int) $mr_count; ?>"><?php echo (int) $mr_count; ?></span></strong>
		<button type="button" class="mr-icon-btn" data-mr-close aria-label="إغلاق السلة"><?php maria_the_icon( 'close', '', 22 ); ?></button>
	</div>
	<div class="mr-drawer__body">
		<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	</div>
</div>
<?php endif; ?>
<div class="mr-overlay" data-mr-close hidden></div>
