<?php
/**
 * ZAD (زاد) — قالب متجر جملة الحلويات والتسالي.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

define( 'ZAD_VERSION', '3.6.0' );
define( 'ZAD_DIR', get_template_directory() );
define( 'ZAD_URI', get_template_directory_uri() );

require ZAD_DIR . '/inc/helpers.php';
require ZAD_DIR . '/inc/icons.php';
require ZAD_DIR . '/inc/setup.php';
require ZAD_DIR . '/inc/customizer.php';
require ZAD_DIR . '/inc/product-art.php';
require ZAD_DIR . '/inc/seo.php';
require ZAD_DIR . '/inc/search.php';
require ZAD_DIR . '/inc/requests.php';
require ZAD_DIR . '/inc/seeder.php';
require ZAD_DIR . '/inc/site-images.php';

if ( class_exists( 'WooCommerce' ) ) {
	require ZAD_DIR . '/inc/woocommerce.php';
	require ZAD_DIR . '/inc/quick-order.php';
	require ZAD_DIR . '/inc/profit.php';
	require ZAD_DIR . '/inc/cod.php';
	require ZAD_DIR . '/inc/customers.php';
	require ZAD_DIR . '/inc/engage.php';
	if ( is_admin() ) {
		require ZAD_DIR . '/inc/customers-admin.php';
		require ZAD_DIR . '/inc/engage-admin.php';
	}
	require ZAD_DIR . '/inc/i18n-fallback.php';
	require ZAD_DIR . '/inc/image-import.php';
}
