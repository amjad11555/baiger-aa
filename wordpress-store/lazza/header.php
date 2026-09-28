<?php
/**
 * الترويسة.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_has_wc  = class_exists( 'WooCommerce' );
$lz_cats    = lazza_categories();
$lz_current = '';
if ( $lz_has_wc && is_product_category() ) {
	$lz_current = get_queried_object()->slug;
} elseif ( $lz_has_wc && is_product() ) {
	$lz_current = lazza_product_main_cat( get_the_ID() );
}
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
<a class="lz-skip" href="#main">تخطَّ إلى المحتوى</a>

<?php if ( lazza_opt( 'announcement' ) ) : ?>
	<div class="lz-topbar">
		<div class="lz-container lz-topbar__row">
			<p class="lz-topbar__text"><?php lazza_the_icon( 'truck', '', 18 ); ?><span><?php echo esc_html( lazza_opt( 'announcement' ) ); ?></span></p>
			<div class="lz-topbar__links">
				<?php if ( lazza_opt( 'phone' ) ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', lazza_opt( 'phone' ) ) ); ?>" dir="ltr"><?php lazza_the_icon( 'phone', '', 15 ); ?> <?php echo esc_html( lazza_opt( 'phone' ) ); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( lazza_page_url( 'export' ) ); ?>"><?php lazza_the_icon( 'globe', '', 15 ); ?> الجملة خارج تركيا</a>
			</div>
		</div>
	</div>
<?php endif; ?>

<header class="lz-header" id="lz-header">
	<div class="lz-header__main">
		<div class="lz-container lz-header__row">
			<button type="button" class="lz-icon-btn lz-header__menu" data-lz-open="lz-drawer" aria-controls="lz-drawer" aria-expanded="false" aria-label="فتح القائمة">
				<?php lazza_the_icon( 'menu' ); ?>
			</button>

			<div class="lz-header__logo"><?php lazza_logo(); ?></div>

			<?php get_search_form(); ?>

			<div class="lz-header__actions">
				<a class="lz-btn lz-btn--gold lz-header__quick" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"><?php lazza_the_icon( 'bolt', '', 18 ); ?><span>الطلب السريع</span></a>
				<?php if ( lazza_wa_number() ) : ?>
					<a class="lz-icon-btn lz-header__wa" href="<?php echo esc_url( lazza_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener" aria-label="تواصل عبر واتساب"><?php lazza_the_icon( 'whatsapp' ); ?></a>
				<?php endif; ?>
				<?php if ( $lz_has_wc ) : ?>
					<a class="lz-icon-btn lz-header__account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" aria-label="حسابي"><?php lazza_the_icon( 'user' ); ?></a>
					<button type="button" class="lz-cart-btn" data-lz-open="lz-cart-drawer" aria-controls="lz-cart-drawer" aria-expanded="false" aria-label="سلة الطلب">
						<span class="lz-cart-btn__icon"><?php lazza_the_icon( 'cart' ); ?><span class="lz-cart-count" data-count="<?php echo (int) WC()->cart->get_cart_contents_count(); ?>"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></span></span>
						<span class="lz-cart-btn__label"><small>سلة الطلب</small><span class="lz-cart-total"><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span></span>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<nav class="lz-catnav" aria-label="أقسام المتجر">
		<div class="lz-container lz-catnav__row">
			<ul class="lz-catnav__cats">
				<?php foreach ( $lz_cats as $lz_slug => $lz_cat ) : ?>
					<li>
						<a class="lz-catnav__link lz-catnav__link--<?php echo esc_attr( $lz_slug ); ?><?php echo $lz_current === $lz_slug ? ' is-current' : ''; ?>" href="<?php echo esc_url( lazza_cat_url( $lz_slug ) ); ?>" style="--c:<?php echo esc_attr( $lz_cat['color'] ); ?>"<?php echo $lz_current === $lz_slug ? ' aria-current="page"' : ''; ?>>
							<?php lazza_the_icon( $lz_cat['icon'], '', 20 ); ?><span><?php echo esc_html( $lz_cat['name'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'lz-menu',
					'depth'          => 1,
					'fallback_cb'    => 'lazza_fallback_menu',
				)
			);
			?>
		</div>
	</nav>
</header>

<div class="lz-drawer lz-drawer--start" id="lz-drawer" role="dialog" aria-modal="true" aria-label="القائمة" aria-hidden="true">
	<div class="lz-drawer__head">
		<?php lazza_logo(); ?>
		<button type="button" class="lz-icon-btn" data-lz-close aria-label="إغلاق القائمة"><?php lazza_the_icon( 'close' ); ?></button>
	</div>
	<div class="lz-drawer__body">
		<a class="lz-btn lz-btn--gold lz-btn--block" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"><?php lazza_the_icon( 'bolt', '', 18 ); ?> الطلب السريع</a>
		<p class="lz-drawer__label">الأقسام</p>
		<ul class="lz-drawer__cats">
			<?php foreach ( $lz_cats as $lz_slug => $lz_cat ) : ?>
				<li><a href="<?php echo esc_url( lazza_cat_url( $lz_slug ) ); ?>" style="--c:<?php echo esc_attr( $lz_cat['color'] ); ?>;--t:<?php echo esc_attr( $lz_cat['tint'] ); ?>"><span class="lz-drawer__ico"><?php lazza_the_icon( $lz_cat['icon'], '', 22 ); ?></span><?php echo esc_html( $lz_cat['name'] ); ?><small><?php echo esc_html( lazza_n_items( lazza_cat_count( $lz_slug ) ) ); ?></small></a></li>
			<?php endforeach; ?>
		</ul>
		<p class="lz-drawer__label">روابط مهمة</p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'lz-drawer__menu',
				'depth'          => 1,
				'fallback_cb'    => 'lazza_fallback_menu',
			)
		);
		?>
		<?php if ( lazza_wa_number() ) : ?>
			<a class="lz-btn lz-btn--wa lz-btn--block" href="<?php echo esc_url( lazza_wa_link( 'مرحباً، أرغب بالطلب' ) ); ?>" target="_blank" rel="noopener"><?php lazza_the_icon( 'whatsapp', '', 20 ); ?> اطلب عبر واتساب</a>
		<?php endif; ?>
	</div>
</div>

<?php if ( $lz_has_wc ) : ?>
<div class="lz-drawer lz-drawer--end lz-cart-drawer" id="lz-cart-drawer" role="dialog" aria-modal="true" aria-label="سلة الطلب" aria-hidden="true">
	<div class="lz-drawer__head">
		<strong class="lz-drawer__title"><?php lazza_the_icon( 'cart', '', 22 ); ?> سلة الطلب <span class="lz-cart-count" data-count="<?php echo (int) WC()->cart->get_cart_contents_count(); ?>"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></span></strong>
		<button type="button" class="lz-icon-btn" data-lz-close aria-label="إغلاق السلة"><?php lazza_the_icon( 'close' ); ?></button>
	</div>
	<div class="lz-drawer__body">
		<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	</div>
	<div class="lz-drawer__foot">
		<a class="lz-link-more" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"><?php lazza_the_icon( 'bolt', '', 16 ); ?> أكمل من صفحة الطلب السريع</a>
	</div>
</div>
<?php endif; ?>
<div class="lz-overlay" data-lz-close hidden></div>
