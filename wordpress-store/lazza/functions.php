<?php
/**
 * Lazza (لذّة) — قالب متجر جملة الحلويات والتسالي.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

define( 'LAZZA_VERSION', '1.0.0' );
define( 'LAZZA_DIR', get_template_directory() );
define( 'LAZZA_URI', get_template_directory_uri() );

require LAZZA_DIR . '/inc/helpers.php';
require LAZZA_DIR . '/inc/icons.php';
require LAZZA_DIR . '/inc/setup.php';
require LAZZA_DIR . '/inc/customizer.php';
require LAZZA_DIR . '/inc/product-art.php';
require LAZZA_DIR . '/inc/seo.php';
require LAZZA_DIR . '/inc/requests.php';
require LAZZA_DIR . '/inc/seeder.php';

if ( class_exists( 'WooCommerce' ) ) {
	require LAZZA_DIR . '/inc/woocommerce.php';
	require LAZZA_DIR . '/inc/quick-order.php';
	require LAZZA_DIR . '/inc/i18n-fallback.php';
	require LAZZA_DIR . '/inc/image-import.php';
}
