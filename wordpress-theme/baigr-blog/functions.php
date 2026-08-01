<?php
/**
 * BAIGR Blog — theme functions.
 *
 * @package baigr-blog
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'baigr_blog_setup' ) ) {
	function baigr_blog_setup() {
		load_theme_textdomain( 'baigr-blog', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo', array(
			'height'      => 60,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
		) );
		add_theme_support( 'html5', array(
			'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
		) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );

		register_nav_menus( array(
			'primary' => __( 'القائمة الرئيسية', 'baigr-blog' ),
			'footer'  => __( 'قائمة التذييل', 'baigr-blog' ),
		) );
	}
}
add_action( 'after_setup_theme', 'baigr_blog_setup' );

/**
 * Content width.
 */
function baigr_blog_content_width() {
	$GLOBALS['content_width'] = 820;
}
add_action( 'after_setup_theme', 'baigr_blog_content_width', 0 );

/**
 * Enqueue styles — Cairo font + the theme stylesheet.
 */
function baigr_blog_assets() {
	// Cairo (Google Fonts is reachable; falls back to system Arabic fonts).
	wp_enqueue_style(
		'baigr-cairo',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'baigr-blog-style', get_stylesheet_uri(), array( 'baigr-cairo' ), wp_get_theme()->get( 'Version' ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'baigr_blog_assets' );

/**
 * Preconnect to Google Fonts for speed.
 */
function baigr_blog_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => 'https://fonts.googleapis.com' );
		$hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'baigr_blog_resource_hints', 10, 2 );

/**
 * Excerpt tweaks.
 */
function baigr_blog_excerpt_length( $length ) { return 26; }
add_filter( 'excerpt_length', 'baigr_blog_excerpt_length' );

function baigr_blog_excerpt_more( $more ) { return '…'; }
add_filter( 'excerpt_more', 'baigr_blog_excerpt_more' );

/**
 * Fallback menu when none is assigned — links to the main site.
 */
function baigr_blog_fallback_menu() {
	echo '<ul id="primary-menu" class="nav-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">المدوّنة</a></li>';
	echo '<li><a href="https://baigr.com/">الموقع الرئيسي</a></li>';
	echo '<li><a class="nav-cta" href="https://baigr.com/">تواصل معنا</a></li>';
	echo '</ul>';
}

/**
 * Posted-on date helper.
 */
function baigr_blog_posted_on() {
	printf(
		'<span class="posted-on">📅 %s</span>',
		esc_html( get_the_date() )
	);
}
