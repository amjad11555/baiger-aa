<?php
/**
 * Header template.
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
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="bar">
		<?php if ( has_custom_logo() ) : ?>
			<div class="site-brand"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="dot"></span><?php bloginfo( 'name' ); ?>
			</a>
		<?php endif; ?>

		<button class="menu-toggle" aria-label="القائمة" aria-expanded="false">☰</button>

		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_class'     => 'nav-menu',
				'menu_id'        => 'primary-menu',
				'container'      => false,
			) );
		} else {
			baigr_blog_fallback_menu();
		}
		?>
	</div>
</header>

<script>
(function(){
	var t=document.querySelector('.menu-toggle'),m=document.getElementById('primary-menu');
	if(t&&m){t.addEventListener('click',function(){var o=m.classList.toggle('open');t.setAttribute('aria-expanded',o);});}
})();
</script>

<main id="content" class="site-content">
