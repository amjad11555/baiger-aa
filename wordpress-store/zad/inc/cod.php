<?php
/**
 * الدفع عند الاستلام: طريقة السداد للمندوب (نقداً / بطاقة / تحويل) وإبرازه في المتجر.
 *
 * يعتمد على بوابة «الدفع عند الاستلام» المدمجة في ووكومرس (cod): يضيف تحتها في صفحة الدفع
 * اختيار طريقة السداد، ويحفظه مع الطلب، ويعرضه في صفحة الشكر والبريد ولوحة الطلبات ورسالة واتساب.
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
 * طرق السداد المتاحة عند الاستلام.
 *
 * @return array مفتاح => [الاسم، الشرح].
 */
function zad_cod_methods() {
	$methods = array(
		'cash'     => array( 'نقداً', 'تدفع للمندوب عند تسليم الكراتين' ),
		'card'     => array( 'بطاقة بنكية', 'جهاز POS مع المندوب' ),
		'transfer' => array( 'تحويل بنكي (Havale/EFT)', 'تحوّل المبلغ عند الاستلام' ),
	);
	if ( ! zad_opt( 'cod_card' ) ) {
		unset( $methods['card'] );
	}
	return $methods;
}

/**
 * وصف مختصر لطرق السداد: «نقداً أو بالبطاقة».
 *
 * @return string
 */
function zad_cod_short() {
	return zad_opt( 'cod_card' ) ? 'نقداً أو بالبطاقة' : 'نقداً';
}

/**
 * اسم طريقة السداد المحفوظة مع الطلب.
 *
 * @param WC_Order $order الطلب.
 * @return string
 */
function zad_cod_method_label( $order ) {
	if ( ! $order || 'cod' !== $order->get_payment_method() ) {
		return '';
	}
	$key = (string) $order->get_meta( '_zad_cod_method' );
	$all = array(
		'cash'     => 'نقداً',
		'card'     => 'بطاقة بنكية (POS)',
		'transfer' => 'تحويل بنكي (Havale/EFT)',
	);
	return isset( $all[ $key ] ) ? $all[ $key ] : '';
}

/**
 * اختيار طريقة السداد داخل صندوق «الدفع عند الاستلام» في صفحة الدفع.
 *
 * يُلحق بوصف البوابة لأن ووكومرس يعرض الوصف داخل صندوقها ويُظهره فقط عند اختيارها.
 * المعرّفات ثابتة ليحافظ سكربت الدفع في ووكومرس على الاختيار بعد تحديث الملخّص.
 *
 * @param string $description وصف البوابة.
 * @param string $gateway_id  معرّف البوابة.
 * @return string
 */
function zad_cod_description( $description, $gateway_id ) {
	if ( 'cod' !== $gateway_id || ( is_admin() && ! wp_doing_ajax() ) || ! is_checkout() || is_wc_endpoint_url( 'order-pay' ) ) {
		return $description;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- قراءة الاختيار السابق فقط لإعادة عرضه.
	$posted = isset( $_POST['post_data'] ) ? wp_parse_args( wp_unslash( $_POST['post_data'] ) ) : array();
	$chosen = isset( $posted['zad_cod_method'] ) ? sanitize_key( $posted['zad_cod_method'] ) : 'cash';

	$html = '<fieldset class="zd-cod-pick"><legend>كيف ستدفع للمندوب؟</legend>';
	foreach ( zad_cod_methods() as $key => $m ) {
		$html .= sprintf(
			'<label class="zd-cod-pick__opt" for="zad_cod_%1$s"><input type="radio" id="zad_cod_%1$s" name="zad_cod_method" value="%1$s"%2$s><span><b>%3$s</b><small>%4$s</small></span></label>',
			esc_attr( $key ),
			checked( $chosen, $key, false ),
			esc_html( $m[0] ),
			esc_html( $m[1] )
		);
	}
	$html .= '</fieldset>';
	return $description . $html;
}
add_filter( 'woocommerce_gateway_description', 'zad_cod_description', 20, 2 );

/**
 * الدفع عند الاستلام أولاً في قائمة طرق الدفع (يُختار تلقائياً).
 *
 * @param array $gateways البوابات المتاحة.
 * @return array
 */
function zad_cod_first( $gateways ) {
	if ( isset( $gateways['cod'] ) ) {
		$gateways = array( 'cod' => $gateways['cod'] ) + $gateways;
	}
	return $gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'zad_cod_first', 20 );

/**
 * حفظ طريقة السداد مع الطلب.
 *
 * @param WC_Order $order الطلب.
 * @param array    $data  بيانات النموذج.
 */
function zad_cod_save_method( $order, $data ) {
	if ( empty( $data['payment_method'] ) || 'cod' !== $data['payment_method'] ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- ووكومرس تحقق من nonce الدفع قبل إنشاء الطلب.
	$method = isset( $_POST['zad_cod_method'] ) ? sanitize_key( wp_unslash( $_POST['zad_cod_method'] ) ) : 'cash';
	if ( ! array_key_exists( $method, zad_cod_methods() ) ) {
		$method = 'cash';
	}
	$order->update_meta_data( '_zad_cod_method', $method );
}
add_action( 'woocommerce_checkout_create_order', 'zad_cod_save_method', 10, 2 );

/**
 * سطر «السداد للمندوب» في ملخّص الطلب (صفحة الشكر، البريد، حسابي).
 *
 * @param array    $rows  الأسطر.
 * @param WC_Order $order الطلب.
 * @return array
 */
function zad_cod_totals_row( $rows, $order ) {
	$label = zad_cod_method_label( $order );
	if ( ! $label ) {
		return $rows;
	}
	$out = array();
	foreach ( $rows as $key => $row ) {
		$out[ $key ] = $row;
		if ( 'payment_method' === $key ) {
			$out['zad_cod_method'] = array(
				'label' => 'السداد للمندوب:',
				'value' => esc_html( $label ),
			);
		}
	}
	if ( ! isset( $out['zad_cod_method'] ) ) {
		$out['zad_cod_method'] = array(
			'label' => 'السداد للمندوب:',
			'value' => esc_html( $label ),
		);
	}
	return $out;
}
add_filter( 'woocommerce_get_order_item_totals', 'zad_cod_totals_row', 10, 2 );

/**
 * طريقة السداد في صفحة الطلب بلوحة التحكم (تحت عنوان الفوترة).
 *
 * @param WC_Order $order الطلب.
 */
function zad_cod_admin_row( $order ) {
	$label = zad_cod_method_label( $order );
	if ( $label ) {
		printf( '<p><strong>السداد للمندوب:</strong> %s</p>', esc_html( $label ) );
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'zad_cod_admin_row' );

/**
 * تنبيه «الدفع عند الاستلام» تحت صندوق الشراء في صفحة المنتج.
 */
function zad_cod_single_note() {
	if ( ! zad_cod_enabled() ) {
		return;
	}
	printf(
		'<p class="zd-cod-note">%1$s<span><b>الدفع عند الاستلام</b> %2$s — لا دفع مسبق، تدفع حين تصل الكراتين إلى محلك.</span></p>',
		zad_icon( 'wallet', '', 20 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( zad_cod_short() )
	);
}
add_action( 'woocommerce_single_product_summary', 'zad_cod_single_note', 32 );

/**
 * سطر مختصر في درج الطلبية وصفحة السلة.
 */
function zad_cod_cart_note() {
	if ( ! zad_cod_enabled() || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	printf(
		'<p class="zd-cod-line">%1$s<span>الدفع عند الاستلام · %2$s</span></p>',
		zad_icon( 'wallet', '', 16 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( zad_cod_short() )
	);
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'zad_cod_cart_note' );
add_action( 'woocommerce_proceed_to_checkout', 'zad_cod_cart_note', 25 );
