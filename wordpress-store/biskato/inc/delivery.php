<?php
/**
 * التوصيل فقط: المتجر للطلب أونلاين، والطلبية تصل إلى باب المحل أو تُرتَّب على الرف.
 *
 * - لا استلام من المستودع: تُلغى طرق الاستلام (local_pickup و pickup_location) من ووكومرس،
 *   وتُحذف من مناطق الشحن الموجودة مرة واحدة عند التحديث.
 * - صفحة الدفع: اختيار «مكان التسليم» (باب المحل / الرف)، يُحفظ مع الطلب ويظهر في صفحة
 *   الشكر والبريد وحسابي ولوحة الطلبات ورسالة واتساب، ويُتذكَّر للطلبية التالية.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * خيارات مكان التسليم.
 *
 * @return array مفتاح => [الاسم، الشرح].
 */
function zad_drop_options() {
	return array(
		'door'  => array( 'عند باب المحل', 'يسلّمك المندوب الكراتين عند باب محلك.' ),
		'shelf' => array( 'ترتيبها على الرف', 'يُدخل المندوب الكراتين ويرصّ الأصناف على رفوفك.' ),
	);
}

/**
 * اسم مكان التسليم المحفوظ مع الطلب.
 *
 * @param WC_Order $order الطلب.
 * @return string
 */
function zad_drop_label( $order ) {
	$key = $order ? (string) $order->get_meta( '_zad_drop' ) : '';
	$all = zad_drop_options();
	return isset( $all[ $key ] ) ? $all[ $key ][0] : '';
}

/**
 * اسم طريقة التوصيل الافتراضية.
 *
 * @return string
 */
function zad_delivery_title() {
	return 'توصيل مجاني إلى محلك';
}

/* -------------------------------------------------------------------------
 * لا استلام من المستودع
 * ---------------------------------------------------------------------- */

/**
 * إزالة طرق الاستلام من قائمة طرق الشحن في ووكومرس.
 *
 * @param array $methods طرق الشحن.
 * @return array
 */
function zad_no_pickup_methods( $methods ) {
	unset( $methods['local_pickup'], $methods['pickup_location'] );
	return $methods;
}
add_filter( 'woocommerce_shipping_methods', 'zad_no_pickup_methods', 99 );

/**
 * حماية إضافية: لا يظهر أي سعر استلام في السلة أو الدفع.
 *
 * @param array $rates أسعار الشحن.
 * @return array
 */
function zad_no_pickup_rates( $rates ) {
	foreach ( $rates as $id => $rate ) {
		if ( in_array( $rate->get_method_id(), array( 'local_pickup', 'pickup_location' ), true ) ) {
			unset( $rates[ $id ] );
		}
	}
	return $rates;
}
add_filter( 'woocommerce_package_rates', 'zad_no_pickup_rates', 99 );

/**
 * ترحيل لمرة واحدة: حذف طرق الاستلام من مناطق الشحن، وتوحيد اسم التوصيل،
 * وإيقاف «حاسبة الشحن» في السلة (العنوان محفوظ في حساب المحل).
 */
function zad_delivery_migrate() {
	if ( get_option( 'zad_migrated_v6' ) || ! class_exists( 'WC_Shipping_Zones' ) ) {
		return;
	}
	$store = WC_Data_Store::load( 'shipping-zone' );
	$ids   = array_merge( array( 0 ), array_map( 'intval', wp_list_pluck( WC_Shipping_Zones::get_zones(), 'id' ) ) );
	foreach ( $ids as $zone_id ) {
		$zone = new WC_Shipping_Zone( $zone_id );
		foreach ( (array) $store->get_methods( $zone_id, false ) as $raw ) {
			if ( in_array( $raw->method_id, array( 'local_pickup', 'pickup_location' ), true ) ) {
				$zone->delete_shipping_method( (int) $raw->instance_id );
				delete_option( 'woocommerce_' . $raw->method_id . '_' . (int) $raw->instance_id . '_settings' );
			} elseif ( 'free_shipping' === $raw->method_id ) {
				$key      = 'woocommerce_free_shipping_' . (int) $raw->instance_id . '_settings';
				$settings = (array) get_option( $key, array() );
				$title    = isset( $settings['title'] ) ? (string) $settings['title'] : '';
				if ( '' === $title || in_array( $title, array( 'توريد إلى المتجر', 'توصيل للبقالة', 'Free shipping' ), true ) ) {
					$settings['title'] = zad_delivery_title();
					update_option( $key, $settings );
				}
			}
		}
	}
	$pickup = get_option( 'woocommerce_pickup_location_settings' );
	if ( is_array( $pickup ) && isset( $pickup['enabled'] ) ) {
		$pickup['enabled'] = 'no';
		update_option( 'woocommerce_pickup_location_settings', $pickup );
	}
	update_option( 'woocommerce_enable_shipping_calc', 'no' );
	WC_Cache_Helper::get_transient_version( 'shipping', true );
	update_option( 'zad_migrated_v6', 1, true );
}
add_action( 'init', 'zad_delivery_migrate', 25 );

/**
 * في السلة: لا «التوصيل إلى İstanbul · تغيير العنوان» (العنوان يُؤكَّد عند الإتمام).
 */
add_filter( 'woocommerce_shipping_show_shipping_calculator', '__return_false' );

/**
 * التوصيل المجاني لإسطنبول فقط: لباقي الولايات التركية يتغير الاسم إلى شحن نؤكد تكلفته قبل الإرسال.
 *
 * @param WC_Shipping_Rate[] $rates   الأسعار.
 * @param array              $package الطرد.
 * @return WC_Shipping_Rate[]
 */
function zad_region_rate_labels( $rates, $package ) {
	$d      = isset( $package['destination'] ) ? (array) $package['destination'] : array();
	$region = function_exists( 'zad_region_of' ) ? zad_region_of( isset( $d['country'] ) ? $d['country'] : '', isset( $d['state'] ) ? $d['state'] : '', isset( $d['city'] ) ? $d['city'] : '' ) : 'istanbul';
	if ( 'turkey' !== $region ) {
		return $rates;
	}
	foreach ( $rates as $rate ) {
		if ( 'free_shipping' === $rate->get_method_id() ) {
			$rate->set_label( 'شحن إلى ولايتك (نؤكد الموعد والتكلفة قبل الإرسال)' );
		}
	}
	return $rates;
}
add_filter( 'woocommerce_package_rates', 'zad_region_rate_labels', 100, 2 );

/**
 * سطر توضيحي تحت طريقة التوصيل في السلة والدفع.
 *
 * @param WC_Shipping_Rate $rate السعر.
 */
function zad_delivery_rate_note( $rate ) {
	if ( 'free_shipping' !== $rate->get_method_id() && 'flat_rate' !== $rate->get_method_id() ) {
		return;
	}
	echo '<small class="zd-ship-note">إلى باب المحل أو على الرف، تختار عند تأكيد الطلبية.</small>';
}
add_action( 'woocommerce_after_shipping_rate', 'zad_delivery_rate_note' );

/* -------------------------------------------------------------------------
 * مكان التسليم في صفحة الدفع
 * ---------------------------------------------------------------------- */

/**
 * الاختيار الافتراضي: آخر اختيار للعميل، وإلا باب المحل.
 *
 * @return string
 */
function zad_drop_default() {
	$saved = is_user_logged_in() ? (string) get_user_meta( get_current_user_id(), 'zad_drop', true ) : '';
	return array_key_exists( $saved, zad_drop_options() ) ? $saved : 'door';
}

/**
 * عرض الاختيار بعد حقول العنوان (خارج ملخّص الطلب، فلا يُعاد رسمه عند التحديث).
 */
function zad_drop_field() {
	$chosen = zad_drop_default();
	echo '<fieldset class="zd-drop-pick"><legend>أين نسلّمك الطلبية؟</legend><div class="zd-drop-pick__opts">';
	foreach ( zad_drop_options() as $key => $opt ) {
		printf(
			'<label class="zd-drop-pick__opt" for="zad_drop_%1$s"><input type="radio" id="zad_drop_%1$s" name="zad_drop" value="%1$s"%2$s>%3$s<span><b>%4$s</b><small>%5$s</small></span></label>',
			esc_attr( $key ),
			checked( $chosen, $key, false ),
			zad_icon( 'shelf' === $key ? 'box' : 'store', '', 22 ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $opt[0] ),
			esc_html( $opt[1] )
		);
	}
	echo '</div><p class="zd-drop-pick__note">التوصيل مجاني، ولا يوجد استلام من المستودع.</p></fieldset>';
}
add_action( 'woocommerce_after_checkout_billing_form', 'zad_drop_field' );

/**
 * حفظ مكان التسليم مع الطلب، وتذكّره لحساب العميل.
 *
 * @param WC_Order $order الطلب.
 */
function zad_drop_save( $order ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- ووكومرس تحقق من nonce الدفع قبل إنشاء الطلب.
	$drop = isset( $_POST['zad_drop'] ) ? sanitize_key( wp_unslash( $_POST['zad_drop'] ) ) : '';
	if ( ! array_key_exists( $drop, zad_drop_options() ) ) {
		$drop = 'door';
	}
	$order->update_meta_data( '_zad_drop', $drop );
	if ( $order->get_customer_id() ) {
		update_user_meta( $order->get_customer_id(), 'zad_drop', $drop );
	}
}
add_action( 'woocommerce_checkout_create_order', 'zad_drop_save', 10, 1 );

/**
 * سطر «التسليم» في ملخّص الطلب (صفحة الشكر، البريد، حسابي).
 *
 * @param array    $rows  الأسطر.
 * @param WC_Order $order الطلب.
 * @return array
 */
function zad_drop_totals_row( $rows, $order ) {
	$label = zad_drop_label( $order );
	if ( ! $label ) {
		return $rows;
	}
	$row = array(
		'label' => 'التسليم:',
		'value' => esc_html( $label ),
	);
	$out = array();
	foreach ( $rows as $key => $r ) {
		$out[ $key ] = $r;
		if ( 'shipping' === $key ) {
			$out['zad_drop'] = $row;
		}
	}
	if ( ! isset( $out['zad_drop'] ) ) {
		$out = array_slice( $out, 0, 1, true ) + array( 'zad_drop' => $row ) + array_slice( $out, 1, null, true );
	}
	return $out;
}
add_filter( 'woocommerce_get_order_item_totals', 'zad_drop_totals_row', 9, 2 );

/**
 * مكان التسليم في صفحة الطلب بلوحة التحكم.
 *
 * @param WC_Order $order الطلب.
 */
function zad_drop_admin_row( $order ) {
	$label = zad_drop_label( $order );
	if ( $label ) {
		printf( '<p><strong>التسليم:</strong> %s</p>', esc_html( $label ) );
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'zad_drop_admin_row', 5 );

/**
 * عنوان مخفي قبل حقول الدفع ليبقى تسلسل العناوين سليماً لقارئات الشاشة (h1 ثم h2 ثم h3 ووكومرس).
 */
function zad_checkout_heading() {
	echo '<h2 class="screen-reader-text">بيانات المحل والتوصيل</h2>';
}
add_action( 'woocommerce_checkout_before_customer_details', 'zad_checkout_heading', 1 );
