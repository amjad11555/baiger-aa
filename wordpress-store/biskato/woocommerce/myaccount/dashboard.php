<?php
/**
 * لوحة «حسابي»: بطاقة المحل، وآخر طلبية مع «اطلبها مجدداً»، واختصارات الطلب.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Zad\WooCommerce
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

zad_account_dashboard();

/**
 * لتوافق الإضافات التي تضيف محتوى إلى لوحة الحساب.
 */
do_action( 'woocommerce_account_dashboard' );
