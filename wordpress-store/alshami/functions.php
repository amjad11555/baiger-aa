<?php
/**
 * Al-Shami (الشامي) — قالب متجر جملة الحلويات والتسالي.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

define( 'SHAMI_VERSION', '2.0.0' );
define( 'SHAMI_DIR', get_template_directory() );
define( 'SHAMI_URI', get_template_directory_uri() );

require SHAMI_DIR . '/inc/helpers.php';
require SHAMI_DIR . '/inc/icons.php';
require SHAMI_DIR . '/inc/setup.php';
require SHAMI_DIR . '/inc/customizer.php';
require SHAMI_DIR . '/inc/product-art.php';
require SHAMI_DIR . '/inc/seo.php';
require SHAMI_DIR . '/inc/search.php';
require SHAMI_DIR . '/inc/requests.php';
require SHAMI_DIR . '/inc/seeder.php';
require SHAMI_DIR . '/inc/site-images.php';

if ( class_exists( 'WooCommerce' ) ) {
	require SHAMI_DIR . '/inc/woocommerce.php';
	require SHAMI_DIR . '/inc/quick-order.php';
	require SHAMI_DIR . '/inc/i18n-fallback.php';
	require SHAMI_DIR . '/inc/image-import.php';
}
