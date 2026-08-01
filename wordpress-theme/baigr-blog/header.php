<?php
/**
 * Header.
 *
 * @package baigr-blog
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<script>document.documentElement.className+=' js';</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#content">تخطَّ إلى المحتوى</a>
<div class="scroll-progress" id="scroll-progress"></div>

<header class="navbar" id="navbar">
	<div class="bgr-container navbar__inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="navbar__logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="navbar__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="logo-word"><?php bloginfo( 'name' ); ?><span class="dot">.</span></span>
			</a>
		<?php endif; ?>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_class'     => 'navbar__links',
				'menu_id'        => 'primary-menu',
				'container'      => false,
				'depth'          => 1,
			) );
		} else {
			baigr_blog_fallback_menu();
		}
		?>

		<div class="navbar__actions">
			<a class="btn btn--solid navbar__cta magnetic" href="<?php echo esc_url( BAIGR_SITE ); ?>/">تواصل معنا</a>
			<button class="burger" id="burger" aria-label="القائمة" aria-expanded="false" aria-controls="menu-overlay">
				<span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div class="menu-overlay" id="menu-overlay">
	<nav>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">المدوّنة</a>
		<a href="<?php echo esc_url( BAIGR_SITE ); ?>/">الموقع الرئيسي</a>
		<a href="<?php echo esc_url( BAIGR_SITE ); ?>/">خدماتنا</a>
		<a href="mailto:team@baigr.com">تواصل معنا</a>
	</nav>
</div>

<main id="content" class="site-content">
