<?php
/**
 * BAIGR Blog — theme functions, assets, and built-in SEO/Schema.
 *
 * @package baigr-blog
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'BAIGR_SITE', 'https://baigr.com' );

/* ============================================================
   1. Theme setup
   ============================================================ */
function baigr_blog_setup() {
	load_theme_textdomain( 'baigr-blog', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 60, 'width' => 220, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	set_post_thumbnail_size( 1200, 750, true );
	register_nav_menus( array(
		'primary' => __( 'القائمة الرئيسية', 'baigr-blog' ),
		'footer'  => __( 'قائمة التذييل', 'baigr-blog' ),
	) );
}
add_action( 'after_setup_theme', 'baigr_blog_setup' );

function baigr_blog_content_width() { $GLOBALS['content_width'] = 820; }
add_action( 'after_setup_theme', 'baigr_blog_content_width', 0 );

/* ============================================================
   2. Assets — Cairo + Lenis (smooth scroll) + theme JS/CSS
   ============================================================ */
function baigr_blog_assets() {
	// Only the weights the theme actually uses — smaller font payload.
	wp_enqueue_style( 'baigr-cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap', array(), null );
	wp_enqueue_style( 'baigr-blog-style', get_stylesheet_uri(), array( 'baigr-cairo' ), wp_get_theme()->get( 'Version' ) );

	// Lightweight, dependency-free motion layer (native scroll — no smooth-scroll library).
	wp_enqueue_script( 'baigr-blog-js', get_template_directory_uri() . '/assets/theme.js', array(), wp_get_theme()->get( 'Version' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'baigr_blog_assets' );

function baigr_blog_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => 'https://fonts.googleapis.com' );
		$hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'baigr_blog_resource_hints', 10, 2 );

/* ============================================================
   3. Excerpt + fallback menu + meta helpers
   ============================================================ */
function baigr_blog_excerpt_length() { return 26; }
add_filter( 'excerpt_length', 'baigr_blog_excerpt_length' );
function baigr_blog_excerpt_more() { return '…'; }
add_filter( 'excerpt_more', 'baigr_blog_excerpt_more' );

function baigr_blog_fallback_menu() {
	echo '<ul id="primary-menu" class="navbar__links">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">المدوّنة</a></li>';
	echo '<li><a href="' . esc_url( BAIGR_SITE ) . '/">الموقع الرئيسي</a></li>';
	echo '</ul>';
}

function baigr_blog_posted_on() {
	printf( '<span class="posted-on">%s</span>', esc_html( get_the_date() ) );
}

/** True when a dedicated SEO plugin is active — we then stand down to avoid duplicate tags. */
function baigr_blog_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/** Best available description for the current view. */
function baigr_blog_description() {
	if ( is_singular() ) {
		$post = get_queried_object();
		$d = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		return trim( mb_substr( preg_replace( '/\s+/', ' ', $d ), 0, 160 ) );
	}
	return get_bloginfo( 'description' );
}

/** Best available share image. */
function baigr_blog_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if ( $img ) { return $img[0]; }
	}
	if ( has_custom_logo() ) {
		$img = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
		if ( $img ) { return $img[0]; }
	}
	return BAIGR_SITE . '/images/logo.png';
}

function baigr_blog_publisher_logo() {
	if ( has_custom_logo() ) {
		$img = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
		if ( $img ) { return $img[0]; }
	}
	return BAIGR_SITE . '/images/logo.png';
}

/* ============================================================
   4. SEO meta tags (description, Open Graph, Twitter, robots)
   Note: if you later install Yoast/RankMath, disable one of the two
   to avoid duplicate tags (this theme already covers the basics).
   ============================================================ */
function baigr_blog_meta_tags() {
	if ( baigr_blog_seo_plugin_active() ) { return; }
	$desc  = baigr_blog_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	$img   = baigr_blog_image();
	$type  = is_singular( 'post' ) ? 'article' : 'website';

	echo "\n<!-- BAIGR SEO -->\n";
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
	}
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	if ( $desc ) { echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n"; }
	echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
	echo '<meta property="og:locale" content="ar_AR" />' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $img ) . '" />' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
	if ( $desc ) { echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />' . "\n"; }
	echo '<meta name="twitter:image" content="' . esc_url( $img ) . '" />' . "\n";

	if ( is_singular( 'post' ) ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '" />' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'baigr_blog_meta_tags', 5 );

/* ============================================================
   5. JSON-LD schema (Organization + WebSite on home;
      BlogPosting + BreadcrumbList on single posts)
   ============================================================ */
function baigr_blog_schema() {
	if ( baigr_blog_seo_plugin_active() ) { return; }
	$org = array(
		'@type' => 'Organization',
		'@id'   => BAIGR_SITE . '/#org',
		'name'  => get_bloginfo( 'name' ),
		'url'   => BAIGR_SITE . '/',
		'logo'  => array( '@type' => 'ImageObject', 'url' => baigr_blog_publisher_logo() ),
		'email' => 'team@baigr.com',
	);

	$graph = array();

	if ( is_singular( 'post' ) ) {
		$graph[] = array(
			'@type'            => 'BlogPosting',
			'headline'         => get_the_title(),
			'description'      => baigr_blog_description(),
			'image'            => baigr_blog_image(),
			'inLanguage'       => 'ar',
			'datePublished'    => get_the_date( 'c' ),
			'dateModified'     => get_the_modified_date( 'c' ),
			'author'           => $org,
			'publisher'        => $org,
			'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink() ),
		);
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'الرئيسية', 'item' => BAIGR_SITE . '/' ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'المدوّنة', 'item' => home_url( '/' ) ),
				array( '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title(), 'item' => get_permalink() ),
			),
		);
	} elseif ( is_home() || is_front_page() ) {
		$graph[] = $org;
		$graph[] = array(
			'@type' => 'WebSite',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
			'inLanguage' => 'ar',
			'publisher'  => array( '@id' => BAIGR_SITE . '/#org' ),
		);
	}

	if ( empty( $graph ) ) { return; }

	$data = array( '@context' => 'https://schema.org', '@graph' => $graph );
	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'baigr_blog_schema', 6 );
