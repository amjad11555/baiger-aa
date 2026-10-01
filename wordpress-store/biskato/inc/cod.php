<?php
/**
 * الدفع حسب منطقة التوصيل:
 * - داخل إسطنبول: نقداً عند الاستلام فقط (بوابة cod).
 * - خارج إسطنبول أو خارج تركيا: تحويل بنكي إلى الحساب الرسمي فقط (بوابة bacs).
 *
 * المنطقة تُعرف من «الدولة» و«الولاية» في صفحة الدفع (إسطنبول = TR34)، ويتبدّل خيار الدفع فوراً
 * عند تغييرهما. أي بوابة أخرى (بطاقات، شيك…) تُخفى حتى لو فُعّلت من الإعدادات.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * هل بوابة الدفع عند الاستلام مفعّلة؟
 *
 * @return bool
 */
function zad_cod_enabled() {
	$settings = get_option( 'woocommerce_cod_settings', array() );
	return is_array( $settings ) && isset( $settings['enabled'] ) && 'yes' === $settings['enabled'];
}

/**
 * منطقة التوصيل للطلبية الحالية: istanbul | turkey | abroad.
 *
 * @return string
 */
function zad_checkout_region() {
	$country = 'TR';
	$state   = '';
	$city    = '';
	if ( function_exists( 'WC' ) && WC()->customer ) {
		$country = (string) WC()->customer->get_billing_country();
		$state   = (string) WC()->customer->get_billing_state();
		$city    = (string) WC()->customer->get_billing_city();
	} elseif ( is_user_logged_in() ) {
		$country = (string) get_user_meta( get_current_user_id(), 'billing_country', true );
		$state   = (string) get_user_meta( get_current_user_id(), 'billing_state', true );
		$city    = (string) get_user_meta( get_current_user_id(), 'billing_city', true );
	}
	return zad_region_of( $country, $state, $city );
}

/**
 * المنطقة من الدولة والولاية والمدينة.
 *
 * @param string $country الدولة.
 * @param string $state   الولاية.
 * @param string $city    المدينة.
 * @return string istanbul|turkey|abroad
 */
function zad_region_of( $country, $state, $city = '' ) {
	$country = $country ? strtoupper( $country ) : 'TR';
	if ( 'TR' !== $country ) {
		return 'abroad';
	}
	if ( 'TR34' === $state ) {
		return 'istanbul';
	}
	if ( '' === $state && preg_match( '/إسطنبول|اسطنبول|istanbul|İstanbul/iu', $city ) ) {
		return 'istanbul';
	}
	return '' === $state ? 'istanbul' : 'turkey';
}

/**
 * منطقة طلب محفوظ.
 *
 * @param WC_Order $order الطلب.
 * @return string
 */
function zad_order_region( $order ) {
	return zad_region_of( $order->get_billing_country(), $order->get_billing_state(), $order->get_billing_city() );
}

/**
 * نص قاعدة الدفع للعرض في الموقع.
 *
 * @return string
 */
function zad_payment_rule_text() {
	return 'داخل إسطنبول: نقداً عند الاستلام · خارجها: تحويل بنكي';
}

/**
 * وصف مختصر لطريقة السداد عند الاستلام.
 *
 * @return string
 */
function zad_cod_short() {
	return 'نقداً';
}

/**
 * طريقة السداد المحفوظة مع الطلب.
 *
 * @param WC_Order $order الطلب.
 * @return string
 */
function zad_cod_method_label( $order ) {
	if ( ! $order ) {
		return '';
	}
	if ( 'bacs' === $order->get_payment_method() ) {
		return 'تحويل بنكي';
	}
	return 'cod' === $order->get_payment_method() ? 'نقداً' : '';
}

/**
 * سطر توضيحي داخل صندوقي الدفع في صفحة الدفع.
 *
 * @param string $description وصف البوابة.
 * @param string $gateway_id  معرّف البوابة.
 * @return string
 */
function zad_cod_description( $description, $gateway_id ) {
	if ( ( is_admin() && ! wp_doing_ajax() ) || ! function_exists( 'is_checkout' ) || ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return $description;
	}
	if ( 'cod' === $gateway_id ) {
		return $description . '<p class="zd-cod-cash">' . zad_icon( 'wallet', '', 18 ) . '<span>الدفع <b>نقداً فقط</b> للمندوب عند الاستلام، داخل إسطنبول.</span></p>';
	}
	if ( 'bacs' === $gateway_id ) {
		return $description . '<p class="zd-cod-cash">' . zad_icon( 'shield', '', 18 ) . '<span>للطلبات <b>خارج إسطنبول أو خارج تركيا</b>: حوّل المبلغ إلى حسابنا البنكي الرسمي، وتظهر بيانات الحساب بعد تأكيد الطلبية.</span></p>';
	}
	return $description;
}
add_filter( 'woocommerce_gateway_description', 'zad_cod_description', 20, 2 );

/**
 * البوابات المتاحة حسب المنطقة.
 *
 * @param array $gateways البوابات المتاحة.
 * @return array
 */
function zad_payment_by_region( $gateways ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $gateways;
	}
	$want = 'istanbul' === zad_checkout_region() ? 'cod' : 'bacs';
	return isset( $gateways[ $want ] ) ? array( $want => $gateways[ $want ] ) : array();
}
add_filter( 'woocommerce_available_payment_gateways', 'zad_payment_by_region', 20 );

/**
 * تنبيه واضح إن لم تتوفر طريقة دفع للمنطقة (مثلاً التحويل البنكي غير مفعّل بعد).
 *
 * @param string $html النص الافتراضي.
 * @return string
 */
function zad_no_gateway_text( $html ) {
	return 'istanbul' === zad_checkout_region()
		? 'الدفع عند الاستلام غير مفعّل حالياً. تواصل معنا عبر واتساب لإتمام طلبيتك.'
		: 'الطلبات خارج إسطنبول تُدفع بتحويل بنكي، وهذه الطريقة غير مفعّلة حالياً. تواصل معنا عبر واتساب لإتمام طلبيتك.';
}
add_filter( 'woocommerce_no_available_payment_methods_message', 'zad_no_gateway_text' );

/**
 * تحديث صفحة الدفع فور تغيير الولاية (لا تفعل ووكومرس ذلك لحقل الولاية دائماً).
 *
 * @param array $fields الحقول.
 * @return array
 */
function zad_state_refresh( $fields ) {
	foreach ( array( 'billing_state', 'billing_country' ) as $key ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$fields['billing'][ $key ]['class'][] = 'update_totals_on_change';
		}
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'zad_state_refresh', 30 );

/**
 * حفظ طريقة السداد والمنطقة مع الطلب.
 *
 * @param WC_Order $order الطلب.
 */
function zad_cod_save_method( $order ) {
	$order->update_meta_data( '_zad_region', zad_order_region( $order ) );
	if ( 'cod' === $order->get_payment_method() ) {
		$order->update_meta_data( '_zad_cod_method', 'cash' );
	}
}
add_action( 'woocommerce_checkout_create_order', 'zad_cod_save_method', 30 );

/**
 * سطر «السداد» في ملخّص الطلب (صفحة الشكر، البريد، حسابي).
 *
 * @param array    $rows  الأسطر.
 * @param WC_Order $order الطلب.
 * @return array
 */
function zad_cod_totals_row( $rows, $order ) {
	if ( 'cod' !== $order->get_payment_method() ) {
		return $rows;
	}
	$out = array();
	foreach ( $rows as $key => $row ) {
		$out[ $key ] = $row;
		if ( 'payment_method' === $key ) {
			$out['zad_cod_method'] = array(
				'label' => 'السداد للمندوب:',
				'value' => 'نقداً',
			);
		}
	}
	return $out;
}
add_filter( 'woocommerce_get_order_item_totals', 'zad_cod_totals_row', 10, 2 );

/**
 * طريقة السداد ومنطقة التوصيل في صفحة الطلب بلوحة التحكم.
 *
 * @param WC_Order $order الطلب.
 */
function zad_cod_admin_row( $order ) {
	$labels = array(
		'istanbul' => 'داخل إسطنبول',
		'turkey'   => 'خارج إسطنبول (تركيا)',
		'abroad'   => 'خارج تركيا',
	);
	$region = zad_order_region( $order );
	printf( '<p><strong>المنطقة:</strong> %1$s — <strong>السداد:</strong> %2$s</p>', esc_html( $labels[ $region ] ), esc_html( zad_cod_method_label( $order ) ) );
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'zad_cod_admin_row' );

/**
 * تنبيه الدفع تحت صندوق الشراء في صفحة المنتج.
 */
function zad_cod_single_note() {
	printf(
		'<p class="zd-cod-note">%1$s<span><b>داخل إسطنبول: نقداً عند الاستلام</b> بلا دفع مسبق. خارج إسطنبول وخارج تركيا: تحويل بنكي إلى حسابنا الرسمي.</span></p>',
		zad_icon( 'wallet', '', 20 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}
add_action( 'woocommerce_single_product_summary', 'zad_cod_single_note', 18 );

/**
 * سطر مختصر في درج الطلبية وصفحة السلة.
 */
function zad_cod_cart_note() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	printf(
		'<p class="zd-cod-line">%1$s<span>%2$s</span></p>',
		zad_icon( 'wallet', '', 16 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( zad_payment_rule_text() )
	);
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'zad_cod_cart_note' );
add_action( 'woocommerce_proceed_to_checkout', 'zad_cod_cart_note', 25 );

/**
 * تنبيه للمدير: التحويل البنكي مفعّل لكن بلا رقم IBAN.
 */
function zad_bacs_notice() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$accounts = get_option( 'woocommerce_bacs_accounts', array() );
	if ( ! empty( $accounts ) && ! empty( $accounts[0]['iban'] ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning" dir="rtl"><p><strong>بسكاتو:</strong> أضف بيانات حسابك البنكي (IBAN) للطلبات خارج إسطنبول من <a href="%s">ووكومرس ← الإعدادات ← المدفوعات ← تحويل بنكي مباشر</a>.</p></div>',
		esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=bacs' ) )
	);
}
add_action( 'admin_notices', 'zad_bacs_notice' );

/**
 * إعداد بوابتي الدفع (عند الإعداد الكامل ومرة واحدة عند الترقية).
 */
function zad_setup_gateways() {
	$cod = (array) get_option( 'woocommerce_cod_settings', array() );
	update_option(
		'woocommerce_cod_settings',
		array_merge(
			$cod,
			array(
				'enabled'            => 'yes',
				'title'              => 'نقداً عند الاستلام (داخل إسطنبول)',
				'description'        => 'لا دفع مسبق: تدفع للمندوب حين تصل الطلبية إلى محلك.',
				'instructions'       => 'سنتواصل معك عبر واتساب لتأكيد الطلب وموعد التوصيل، والدفع نقداً للمندوب.',
				'enable_for_methods' => array(),
				'enable_for_virtual' => 'yes',
			)
		)
	);
	$bacs = (array) get_option( 'woocommerce_bacs_settings', array() );
	update_option(
		'woocommerce_bacs_settings',
		array_merge(
			$bacs,
			array(
				'enabled'      => 'yes',
				'title'        => 'تحويل بنكي (خارج إسطنبول وخارج تركيا)',
				'description'  => 'حوّل المبلغ إلى حسابنا البنكي الرسمي، واكتب رقم الطلبية في وصف التحويل.',
				'instructions' => 'حوّل المبلغ إلى الحساب أدناه واكتب رقم الطلبية في وصف التحويل، ثم أرسل صورة الإيصال على واتساب. نجهّز طلبيتك ونشحنها فور وصول التحويل.',
			)
		)
	);
}
