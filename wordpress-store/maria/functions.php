<?php
/**
 * Maria (ماريا) — قالب متجر جملة الحلويات والتسالي.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

define( 'MARIA_VERSION', '1.0.0' );
define( 'MARIA_DIR', get_template_directory() );
define( 'MARIA_URI', get_template_directory_uri() );

require MARIA_DIR . '/inc/helpers.php';
require MARIA_DIR . '/inc/icons.php';
require MARIA_DIR . '/inc/setup.php';
require MARIA_DIR . '/inc/customizer.php';
require MARIA_DIR . '/inc/product-art.php';
require MARIA_DIR . '/inc/seo.php';
require MARIA_DIR . '/inc/search.php';
require MARIA_DIR . '/inc/requests.php';
require MARIA_DIR . '/inc/seeder.php';

if ( class_exists( 'WooCommerce' ) ) {
	require MARIA_DIR . '/inc/woocommerce.php';
	require MARIA_DIR . '/inc/quick-order.php';
	require MARIA_DIR . '/inc/i18n-fallback.php';
	require MARIA_DIR . '/inc/image-import.php';
}
