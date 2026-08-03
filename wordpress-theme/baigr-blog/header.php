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

		<?php baigr_blog_primary_nav( 'navbar__links', 'primary-menu' ); ?>

		<div class="navbar__actions">
			<a class="btn btn--solid navbar__cta magnetic" href="<?php echo esc_url( BAIGR_SITE ); ?>/">تواصل معنا</a>
			<button class="burger" id="burger" aria-label="القائمة" aria-expanded="false" aria-controls="menu-overlay">
				<span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div class="menu-overlay" id="menu-overlay">
	<nav class="menu-overlay__nav" aria-label="القائمة">
		<?php baigr_blog_primary_nav( 'menu-overlay__links', 'mobile-menu' ); ?>
	</nav>

	<form role="search" method="get" class="menu-overlay__search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.2-3.2"></path></svg>
		<input type="search" name="s" placeholder="ابحث في المدوّنة…" value="<?php echo esc_attr( get_search_query() ); ?>" aria-label="ابحث في المدوّنة" />
		<button type="submit">بحث</button>
	</form>

	<?php
	$overlay_cats = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true, 'number' => 10 ) );
	if ( ! empty( $overlay_cats ) ) :
		?>
		<div class="menu-overlay__cats">
			<h4>التصنيفات</h4>
			<div class="blog-cats">
				<?php foreach ( $overlay_cats as $ocat ) : ?>
					<a class="cat-chip" href="<?php echo esc_url( get_category_link( $ocat->term_id ) ); ?>"><?php echo esc_html( $ocat->name ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>

<main id="content" class="site-content">
