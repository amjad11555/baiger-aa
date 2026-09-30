<?php
/**
 * حسابات الزبائن (البقالات والمحلات).
 *
 * التسجيل: الاسم، واسم المحل، ورقم الواتساب، والمنطقة، والعنوان، وموقع المحل على الخريطة (اختياري)، وكلمة مرور.
 * الدخول: برقم الواتساب (بأي صيغة) أو بالبريد. رقم الواتساب هو اسم المستخدم، والبريد اختياري.
 * الطلب: يُطلب الحساب قبل إتمام الطلبية (قابل للإلغاء من الإعدادات)، وتُنسخ المنطقة والموقع إلى الطلب.
 *
 * البيانات تُحفظ في حقول ووكومرس المعتادة (billing_*) مع ثلاثة حقول إضافية:
 * zad_wa (الرقم أرقاماً فقط)، zad_district (المنطقة)، zad_lat و zad_lng (الموقع).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * البيانات الأساسية
 * ---------------------------------------------------------------------- */

/**
 * مناطق إسطنبول التسع والثلاثون + «خارج إسطنبول».
 *
 * @return array slug => [الاسم العربي، الاسم التركي، خط العرض، خط الطول].
 */
function zad_districts() {
	static $list = null;
	if ( null !== $list ) {
		return $list;
	}
	$list = array(
		'fatih'         => array( 'الفاتح', 'Fatih', 41.0186, 28.9497 ),
		'esenyurt'      => array( 'إسنيورت', 'Esenyurt', 41.0343, 28.6801 ),
		'basaksehir'    => array( 'باشاك شهير', 'Başakşehir', 41.0931, 28.8020 ),
		'bagcilar'      => array( 'باغجلار', 'Bağcılar', 41.0390, 28.8567 ),
		'sultangazi'    => array( 'سلطان غازي', 'Sultangazi', 41.1066, 28.8672 ),
		'kucukcekmece'  => array( 'كوتشوك تشكمجة', 'Küçükçekmece', 41.0000, 28.7800 ),
		'esenler'       => array( 'إسنلر', 'Esenler', 41.0435, 28.8760 ),
		'avcilar'       => array( 'أفجلار', 'Avcılar', 40.9796, 28.7214 ),
		'zeytinburnu'   => array( 'زيتون بورنو', 'Zeytinburnu', 40.9943, 28.9044 ),
		'bahcelievler'  => array( 'بهتشلي إيفلر', 'Bahçelievler', 41.0009, 28.8625 ),
		'gaziosmanpasa' => array( 'غازي عثمان باشا', 'Gaziosmanpaşa', 41.0637, 28.9124 ),
		'bayrampasa'    => array( 'بيرم باشا', 'Bayrampaşa', 41.0461, 28.9110 ),
		'gungoren'      => array( 'غونغورن', 'Güngören', 41.0223, 28.8727 ),
		'eyupsultan'    => array( 'أيوب سلطان', 'Eyüpsultan', 41.0479, 28.9340 ),
		'beyoglu'       => array( 'بي أوغلو', 'Beyoğlu', 41.0370, 28.9770 ),
		'sisli'         => array( 'شيشلي', 'Şişli', 41.0602, 28.9877 ),
		'kagithane'     => array( 'كاغتهانة', 'Kağıthane', 41.0807, 28.9737 ),
		'besiktas'      => array( 'بشكتاش', 'Beşiktaş', 41.0428, 29.0075 ),
		'sariyer'       => array( 'صاريير', 'Sarıyer', 41.1669, 29.0572 ),
		'bakirkoy'      => array( 'باكركوي', 'Bakırköy', 40.9800, 28.8720 ),
		'beylikduzu'    => array( 'بيليك دوزو', 'Beylikdüzü', 40.9822, 28.6400 ),
		'buyukcekmece'  => array( 'بويوك تشكمجة', 'Büyükçekmece', 41.0210, 28.5850 ),
		'arnavutkoy'    => array( 'أرناؤوط كوي', 'Arnavutköy', 41.1840, 28.7400 ),
		'catalca'       => array( 'تشاتالجا', 'Çatalca', 41.1430, 28.4610 ),
		'silivri'       => array( 'سيليفري', 'Silivri', 41.0740, 28.2460 ),
		'uskudar'       => array( 'أسكودار', 'Üsküdar', 41.0230, 29.0150 ),
		'kadikoy'       => array( 'كاديكوي', 'Kadıköy', 40.9900, 29.0290 ),
		'umraniye'      => array( 'عمرانية', 'Ümraniye', 41.0160, 29.1240 ),
		'atasehir'      => array( 'أتا شهير', 'Ataşehir', 40.9840, 29.1070 ),
		'maltepe'       => array( 'مالتبة', 'Maltepe', 40.9350, 29.1550 ),
		'kartal'        => array( 'كارتال', 'Kartal', 40.8900, 29.1900 ),
		'pendik'        => array( 'بنديك', 'Pendik', 40.8770, 29.2350 ),
		'tuzla'         => array( 'توزلا', 'Tuzla', 40.8160, 29.3000 ),
		'sultanbeyli'   => array( 'سلطان بيلي', 'Sultanbeyli', 40.9670, 29.2620 ),
		'sancaktepe'    => array( 'سنجق تبة', 'Sancaktepe', 41.0020, 29.2300 ),
		'cekmekoy'      => array( 'تشكمكوي', 'Çekmeköy', 41.0350, 29.1800 ),
		'beykoz'        => array( 'بيكوز', 'Beykoz', 41.1340, 29.0930 ),
		'sile'          => array( 'شيلة', 'Şile', 41.1760, 29.6130 ),
		'adalar'        => array( 'جزر الأميرات', 'Adalar', 40.8760, 29.0910 ),
		'other'         => array( 'خارج إسطنبول', '', 39.0, 35.0 ),
	);
	return $list;
}

/**
 * اسم المنطقة للعرض.
 *
 * @param string $slug المعرّف.
 * @param bool   $with_tr مع الاسم التركي.
 * @return string
 */
function zad_district_label( $slug, $with_tr = false ) {
	$all = zad_districts();
	if ( ! isset( $all[ $slug ] ) ) {
		return '';
	}
	return $with_tr && $all[ $slug ][1] ? $all[ $slug ][0] . ' · ' . $all[ $slug ][1] : $all[ $slug ][0];
}

/**
 * توحيد رقم الواتساب: أرقام فقط بالصيغة الدولية (905xxxxxxxxx).
 *
 * يقبل: 0532 123 45 67 · 532 123 45 67 · +90 532… · 0090 532… · أرقام دولية أخرى بين 10 و15 رقماً.
 *
 * @param string $raw الرقم كما كُتب.
 * @return string '' إن لم يكن رقماً صالحاً.
 */
function zad_normalize_phone( $raw ) {
	$raw = strtr( (string) $raw, array_combine( array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ), range( 0, 9 ) ) );
	$d   = preg_replace( '/\D+/', '', $raw );
	if ( 0 === strpos( $d, '00' ) ) {
		$d = substr( $d, 2 );
	}
	if ( 11 === strlen( $d ) && '0' === $d[0] ) {
		$d = '90' . substr( $d, 1 );
	} elseif ( 10 === strlen( $d ) && '5' === $d[0] ) {
		$d = '90' . $d;
	}
	if ( strlen( $d ) < 10 || strlen( $d ) > 15 ) {
		return '';
	}
	// الأرقام التركية: جوال فقط (يبدأ بـ 5 بعد 90) لأنه رقم واتساب.
	if ( 0 === strpos( $d, '90' ) && ( 12 !== strlen( $d ) || '5' !== $d[2] ) ) {
		return '';
	}
	return $d;
}

/**
 * عرض الرقم بشكل مقروء: +90 532 123 45 67.
 *
 * @param string $digits الرقم أرقاماً.
 * @return string
 */
function zad_format_phone( $digits ) {
	$digits = preg_replace( '/\D+/', '', (string) $digits );
	if ( 12 === strlen( $digits ) && 0 === strpos( $digits, '90' ) ) {
		return sprintf( '+90 %s %s %s %s', substr( $digits, 2, 3 ), substr( $digits, 5, 3 ), substr( $digits, 8, 2 ), substr( $digits, 10, 2 ) );
	}
	return $digits ? '+' . $digits : '';
}

/**
 * البحث عن زبون برقم الواتساب.
 *
 * @param string $digits الرقم الموحّد.
 * @param int    $exclude رقم مستخدم يُستثنى (عند تعديل الحساب).
 * @return int رقم المستخدم أو 0.
 */
function zad_find_customer_by_phone( $digits, $exclude = 0 ) {
	if ( ! $digits ) {
		return 0;
	}
	$ids = get_users(
		array(
			'meta_key'   => 'zad_wa', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => $digits, // phpcs:ignore WordPress.DB.SlowDBQuery
			'exclude'    => $exclude ? array( $exclude ) : array(),
			'fields'     => 'ID',
			'number'     => 1,
		)
	);
	if ( $ids ) {
		return (int) $ids[0];
	}
	$user = get_user_by( 'login', $digits );
	return ( $user && (int) $user->ID !== (int) $exclude ) ? (int) $user->ID : 0;
}

/**
 * بيانات زبون مجمّعة للعرض.
 *
 * @param int $user_id رقم المستخدم.
 * @return array
 */
function zad_customer_profile( $user_id ) {
	$wa  = (string) get_user_meta( $user_id, 'zad_wa', true );
	$lat = get_user_meta( $user_id, 'zad_lat', true );
	$lng = get_user_meta( $user_id, 'zad_lng', true );
	if ( ! $wa ) {
		$wa = zad_normalize_phone( get_user_meta( $user_id, 'billing_phone', true ) );
	}
	$district = (string) get_user_meta( $user_id, 'zad_district', true );
	return array(
		'id'       => (int) $user_id,
		'name'     => (string) get_user_meta( $user_id, 'first_name', true ),
		'shop'     => (string) get_user_meta( $user_id, 'billing_company', true ),
		'wa'       => $wa,
		'district' => $district,
		'city'     => (string) get_user_meta( $user_id, 'billing_city', true ),
		'address'  => (string) get_user_meta( $user_id, 'billing_address_1', true ),
		'lat'      => '' !== $lat ? (float) $lat : null,
		'lng'      => '' !== $lng ? (float) $lng : null,
	);
}

/**
 * رابط الموقع على خرائط Google.
 *
 * @param float $lat خط العرض.
 * @param float $lng خط الطول.
 * @return string
 */
function zad_map_link( $lat, $lng ) {
	if ( null === $lat || null === $lng || ( ! $lat && ! $lng ) ) {
		return '';
	}
	return 'https://www.google.com/maps?q=' . rawurlencode( round( (float) $lat, 6 ) . ',' . round( (float) $lng, 6 ) );
}

/**
 * رابط واتساب مباشر لرقم زبون.
 *
 * @param string $digits الرقم.
 * @param string $text   نص اختياري.
 * @return string
 */
function zad_customer_wa_link( $digits, $text = '' ) {
	$digits = preg_replace( '/\D+/', '', (string) $digits );
	if ( ! $digits ) {
		return '';
	}
	return 'https://wa.me/' . $digits . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/**
 * مسح بيانات لوحة «الزبائن» المخزّنة عند تغيّر الزبائن أو الطلبات.
 */
function zad_customers_flush() {
	delete_transient( 'zad_customers_dataset' );
}
foreach ( array( 'user_register', 'profile_update', 'deleted_user', 'woocommerce_new_order', 'woocommerce_order_status_changed', 'woocommerce_save_account_details' ) as $zad_hook ) {
	add_action( $zad_hook, 'zad_customers_flush' );
}
unset( $zad_hook );

/* -------------------------------------------------------------------------
 * قراءة الحقول والتحقق منها (مشتركة بين التسجيل وتعديل الحساب ولوحة التحكم)
 * ---------------------------------------------------------------------- */

/**
 * قراءة حقول المحل من النموذج.
 *
 * @param string $name_key اسم حقل الاسم في النموذج.
 * @return array
 */
function zad_customer_posted( $name_key = 'zad_name' ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- يتحقق المستدعي من nonce قبل الحفظ.
	$get = static function ( $key ) {
		return isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
	};
	$v   = array(
		'name'     => $get( $name_key ),
		'shop'     => $get( 'zad_shop' ),
		'wa_raw'   => $get( 'zad_wa' ),
		'district' => sanitize_key( $get( 'zad_district' ) ),
		'city'     => $get( 'zad_city' ),
		'address'  => $get( 'zad_address' ),
		'lat'      => $get( 'zad_lat' ),
		'lng'      => $get( 'zad_lng' ),
	);
	// phpcs:enable
	$v['wa'] = zad_normalize_phone( $v['wa_raw'] );
	return $v;
}

/**
 * التحقق من حقول المحل.
 *
 * @param array $v       القيم.
 * @param int   $user_id الحساب الحالي (عند التعديل).
 * @return array رسائل الأخطاء (مفتاح الحقل => الرسالة).
 */
function zad_customer_validate( $v, $user_id = 0 ) {
	$errors    = array();
	$districts = zad_districts();
	if ( mb_strlen( $v['name'] ) < 2 ) {
		$errors['name'] = 'اكتب اسمك.';
	}
	if ( mb_strlen( $v['shop'] ) < 2 ) {
		$errors['shop'] = 'اكتب اسم المحل.';
	}
	if ( ! $v['wa'] ) {
		$errors['wa'] = 'اكتب رقم واتساب صحيحاً، مثل 0532 123 45 67.';
	} elseif ( zad_find_customer_by_phone( $v['wa'], $user_id ) ) {
		$errors['wa'] = $user_id
			? 'رقم الواتساب هذا مسجّل لحساب آخر.'
			: 'رقم الواتساب هذا مسجّل مسبقاً. سجّل الدخول به، أو راسلنا إن نسيت كلمة المرور.';
	}
	if ( ! isset( $districts[ $v['district'] ] ) ) {
		$errors['district'] = 'اختر منطقة المحل.';
	} elseif ( 'other' === $v['district'] && mb_strlen( $v['city'] ) < 2 ) {
		$errors['city'] = 'اكتب المدينة.';
	}
	if ( mb_strlen( $v['address'] ) < 5 ) {
		$errors['address'] = 'اكتب عنوان المحل بالتفصيل (الحي والشارع ورقم المحل).';
	}
	// الموقع على الخريطة اختياري (يسرّع وصول المندوب)، لكن إن أُرسل فيجب أن يكون داخل تركيا.
	if ( '' !== $v['lat'] || '' !== $v['lng'] ) {
		$lat = is_numeric( $v['lat'] ) ? (float) $v['lat'] : null;
		$lng = is_numeric( $v['lng'] ) ? (float) $v['lng'] : null;
		if ( null === $lat || null === $lng || $lat < 35 || $lat > 43 || $lng < 25 || $lng > 45.5 ) {
			$errors['location'] = 'الموقع المحدّد خارج تركيا. المس مكان المحل على الخريطة أو اتركه فارغاً.';
		}
	}
	return $errors;
}

/**
 * حفظ حقول المحل في حساب الزبون.
 *
 * @param int   $user_id رقم المستخدم.
 * @param array $v       القيم بعد التحقق.
 */
function zad_customer_save( $user_id, $v ) {
	$city = 'other' === $v['district'] ? $v['city'] : 'إسطنبول – ' . zad_district_label( $v['district'] );
	$meta = array(
		'first_name'         => $v['name'],
		'billing_first_name' => $v['name'],
		'billing_company'    => $v['shop'],
		'billing_phone'      => zad_format_phone( $v['wa'] ),
		'billing_city'       => $city,
		'billing_address_1'  => $v['address'],
		'billing_country'    => 'TR',
		'zad_wa'             => $v['wa'],
		'zad_district'       => $v['district'],
	);
	if ( is_numeric( $v['lat'] ) && is_numeric( $v['lng'] ) ) {
		$meta['zad_lat'] = number_format( (float) $v['lat'], 6, '.', '' );
		$meta['zad_lng'] = number_format( (float) $v['lng'], 6, '.', '' );
	}
	foreach ( $meta as $key => $value ) {
		update_user_meta( $user_id, $key, $value );
	}
}

/**
 * وجهة ما بعد الدخول أو التسجيل (مثلاً العودة إلى إتمام الطلبية).
 *
 * @return string
 */
function zad_account_next_url() {
	// phpcs:ignore WordPress.Security.NonceVerification
	$next = isset( $_REQUEST['zad_next'] ) ? sanitize_key( wp_unslash( $_REQUEST['zad_next'] ) ) : '';
	if ( 'checkout' === $next && function_exists( 'wc_get_checkout_url' ) ) {
		return wc_get_checkout_url();
	}
	return wc_get_page_permalink( 'myaccount' );
}

/* -------------------------------------------------------------------------
 * التسجيل
 * ---------------------------------------------------------------------- */

/**
 * معالجة نموذج «حساب جديد».
 */
function zad_process_registration() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( empty( $_POST['zad_register'] ) || is_user_logged_in() ) {
		return;
	}
	$nonce = isset( $_POST['zad-register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['zad-register-nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'zad-register' ) ) {
		wc_add_notice( 'انتهت صلاحية الصفحة. أعد تعبئة النموذج من فضلك.', 'error' );
		return;
	}
	// فخ الروبوتات.
	if ( ! empty( $_POST['zd_website'] ) ) {
		return;
	}
	// حد المعدل: 5 حسابات في الساعة لكل عنوان.
	$rl_key = 'zd_reg_' . md5( zad_client_ip() );
	$hits   = (int) get_transient( $rl_key );
	if ( $hits >= 5 ) {
		wc_add_notice( 'أنشأت عدة حسابات خلال وقت قصير. حاول بعد ساعة أو راسلنا عبر واتساب.', 'error' );
		return;
	}

	$v        = zad_customer_posted( 'zad_name' );
	$errors   = zad_customer_validate( $v );
	$password = isset( $_POST['zad_password'] ) && is_string( $_POST['zad_password'] ) ? (string) wp_unslash( $_POST['zad_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$email    = isset( $_POST['zad_email'] ) ? sanitize_email( wp_unslash( $_POST['zad_email'] ) ) : '';
	$raw_mail = isset( $_POST['zad_email'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['zad_email'] ) ) ) : '';
	// phpcs:enable
	if ( strlen( $password ) < 6 ) {
		$errors['password'] = 'اختر كلمة مرور من 6 أحرف أو أرقام على الأقل.';
	}
	if ( '' !== $raw_mail && ! is_email( $email ) ) {
		$errors['email'] = 'البريد الإلكتروني غير صحيح (أو اتركه فارغاً).';
	} elseif ( $email && email_exists( $email ) ) {
		$errors['email'] = 'هذا البريد مسجّل لحساب آخر. سجّل الدخول به أو اتركه فارغاً.';
	}
	if ( $errors ) {
		foreach ( $errors as $key => $msg ) {
			wc_add_notice( $msg, 'error', array( 'id' => 'zad_' . $key ) );
		}
		return;
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => $v['wa'],
			'user_pass'    => $password,
			'user_email'   => $email,
			'first_name'   => $v['name'],
			'display_name' => $v['shop'],
			'nickname'     => $v['shop'],
			'role'         => 'customer',
		)
	);
	if ( is_wp_error( $user_id ) ) {
		wc_add_notice( 'تعذّر إنشاء الحساب: ' . $user_id->get_error_message(), 'error' );
		return;
	}
	set_transient( $rl_key, $hits + 1, HOUR_IN_SECONDS );
	zad_customer_save( $user_id, $v );
	if ( $email ) {
		update_user_meta( $user_id, 'billing_email', $email );
	}

	/** لتوافق الإضافات ولرسالة «حساب جديد» من ووكومرس (تُرسل فقط إن وُجد بريد). */
	do_action(
		'woocommerce_created_customer',
		$user_id,
		array(
			'user_login' => $v['wa'],
			'user_email' => $email,
			'role'       => 'customer',
		),
		false
	);
	zad_notify_new_customer( $user_id );

	wc_set_customer_auth_cookie( $user_id );
	wc_add_notice( sprintf( 'أهلاً بك يا %s! تم إنشاء حساب «%s». يمكنك الآن الطلب بأسعار الجملة وتتبع طلبياتك.', $v['name'], $v['shop'] ), 'success' );
	wp_safe_redirect( zad_account_next_url() );
	exit;
}
add_action( 'wp_loaded', 'zad_process_registration', 20 );

/**
 * إشعار بريدي لقسم المبيعات بزبون جديد.
 *
 * @param int $user_id رقم المستخدم.
 */
function zad_notify_new_customer( $user_id ) {
	$p  = zad_customer_profile( $user_id );
	$to = zad_opt( 'email' ) ? zad_opt( 'email' ) : get_option( 'admin_email' );
	if ( ! $to ) {
		return;
	}
	$lines = array(
		'زبون جديد سجّل في الموقع:',
		'',
		'المحل: ' . $p['shop'],
		'الاسم: ' . $p['name'],
		'واتساب: ' . zad_format_phone( $p['wa'] ) . ' — ' . zad_customer_wa_link( $p['wa'] ),
		'المنطقة: ' . ( 'other' === $p['district'] ? $p['city'] : zad_district_label( $p['district'], true ) ),
		'العنوان: ' . $p['address'],
		'الموقع: ' . zad_map_link( $p['lat'], $p['lng'] ),
		'',
		'كل الزبائن: ' . admin_url( 'admin.php?page=zad-customers' ),
	);
	wp_mail( $to, 'زبون جديد: ' . $p['shop'], implode( "\n", $lines ) );
}

/* -------------------------------------------------------------------------
 * الدخول برقم الواتساب
 * ---------------------------------------------------------------------- */

/**
 * تحويل رقم الواتساب المكتوب بأي صيغة إلى اسم المستخدم.
 *
 * @param array $creds بيانات الدخول.
 * @return array
 */
function zad_login_by_phone( $creds ) {
	$login = isset( $creds['user_login'] ) ? (string) $creds['user_login'] : '';
	if ( $login && ! is_email( $login ) && preg_match( '/^[\s\d+\-()٠-٩]+$/u', $login ) ) {
		$user_id = zad_find_customer_by_phone( zad_normalize_phone( $login ) );
		if ( $user_id ) {
			$user                = get_userdata( $user_id );
			$creds['user_login'] = $user->user_login;
		}
	}
	return $creds;
}
add_filter( 'woocommerce_login_credentials', 'zad_login_by_phone' );

/**
 * رسالة خطأ دخول واضحة بالعربية في نموذج المتجر.
 *
 * @param WP_User|WP_Error|null $user النتيجة.
 * @return WP_User|WP_Error|null
 */
function zad_login_error_message( $user ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- ووكومرس تحقق من nonce الدخول.
	if ( is_wp_error( $user ) && isset( $_POST['login'], $_POST['woocommerce-login-nonce'] ) ) {
		return new WP_Error( 'zad_login', 'رقم الواتساب أو كلمة المرور غير صحيحة. تأكد من الرقم وأعد المحاولة.' );
	}
	return $user;
}
add_filter( 'authenticate', 'zad_login_error_message', 99 );

/* -------------------------------------------------------------------------
 * الطلب للزبائن المسجلين، والأسعار للأعضاء (اختياري)
 * ---------------------------------------------------------------------- */

/**
 * الزائر غير المسجل يُحوَّل من صفحة الدفع إلى التسجيل، وتبقى طلبيته محفوظة.
 */
function zad_checkout_gate() {
	if ( ! zad_opt( 'require_account' ) || is_user_logged_in() || ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}
	if ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	wc_add_notice( 'طلبيتك محفوظة. لإتمامها أنشئ حسابك مرة واحدة (دقيقة واحدة)، أو ادخل برقم الواتساب إن كان لديك حساب.', 'notice' );
	wp_safe_redirect( add_query_arg( 'zad_next', 'checkout', wc_get_page_permalink( 'myaccount' ) ) );
	exit;
}
add_action( 'template_redirect', 'zad_checkout_gate', 5 );

/**
 * إعدادات ووكومرس المتوافقة مع «الطلب للمسجلين فقط».
 *
 * @param mixed $value القيمة.
 * @return mixed
 */
function zad_guest_checkout_option( $value ) {
	return zad_opt( 'require_account' ) ? 'no' : $value;
}
add_filter( 'pre_option_woocommerce_enable_guest_checkout', 'zad_guest_checkout_option' );
add_filter( 'pre_option_woocommerce_enable_signup_and_login_from_checkout', 'zad_guest_checkout_option' );

/**
 * نسخ منطقة الزبون وموقعه إلى الطلب (للتوصيل وللتقارير).
 *
 * @param WC_Order $order الطلب.
 */
function zad_order_copy_location( $order ) {
	$user_id = $order->get_customer_id();
	if ( ! $user_id ) {
		return;
	}
	$p = zad_customer_profile( $user_id );
	if ( $p['district'] ) {
		$order->update_meta_data( '_zad_district', $p['district'] );
	}
	if ( null !== $p['lat'] ) {
		$order->update_meta_data( '_zad_lat', $p['lat'] );
		$order->update_meta_data( '_zad_lng', $p['lng'] );
	}
}
add_action( 'woocommerce_checkout_create_order', 'zad_order_copy_location', 20 );

/**
 * صفحة الطلب في لوحة التحكم: واتساب الزبون وموقع محله.
 *
 * @param WC_Order $order الطلب.
 */
function zad_order_admin_customer( $order ) {
	$lat = $order->get_meta( '_zad_lat' );
	$lng = $order->get_meta( '_zad_lng' );
	$wa  = zad_normalize_phone( $order->get_billing_phone() );
	$out = array();
	if ( $wa ) {
		$out[] = sprintf( '<a href="%s" target="_blank" rel="noopener">واتساب الزبون</a>', esc_url( zad_customer_wa_link( $wa, 'مرحباً، بخصوص طلبيتك رقم #' . $order->get_order_number() ) ) );
	}
	if ( '' !== $lat && '' !== $lng ) {
		$out[] = sprintf( '<a href="%s" target="_blank" rel="noopener">موقع المحل على الخريطة</a>', esc_url( zad_map_link( (float) $lat, (float) $lng ) ) );
	}
	$district = zad_district_label( (string) $order->get_meta( '_zad_district' ), true );
	if ( $district ) {
		$out[] = 'المنطقة: ' . esc_html( $district );
	}
	if ( $out ) {
		echo '<p class="zd-order-links">' . implode( ' · ', $out ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'zad_order_admin_customer', 5 );

/* -------------------------------------------------------------------------
 * صفحة «حسابي»
 * ---------------------------------------------------------------------- */

/**
 * قائمة الحساب: ما يحتاجه صاحب المحل فقط.
 *
 * @param array $items العناصر.
 * @return array
 */
function zad_account_menu( $items ) {
	$out = array(
		'dashboard'    => 'حسابي',
		'orders'       => 'طلبياتي',
		'edit-account' => 'بيانات المحل',
	);
	if ( isset( $items['customer-logout'] ) ) {
		$out['customer-logout'] = 'تسجيل الخروج';
	}
	return $out;
}
add_filter( 'woocommerce_account_menu_items', 'zad_account_menu', 20 );

/**
 * الحقول المطلوبة في «بيانات المحل»: الاسم فقط من حقول ووكومرس، والباقي نتحقق منه نحن.
 *
 * @return array
 */
function zad_account_required_fields() {
	return array( 'account_first_name' => 'الاسم' );
}
add_filter( 'woocommerce_save_account_details_required_fields', 'zad_account_required_fields' );

/**
 * التحقق من حقول المحل عند حفظ «بيانات المحل».
 *
 * @param WP_Error $errors الأخطاء.
 * @param stdClass $user   المستخدم.
 */
function zad_account_details_errors( $errors, $user ) {
	$v = zad_customer_posted( 'account_first_name' );
	foreach ( zad_customer_validate( $v, (int) $user->ID ) as $key => $msg ) {
		$errors->add( 'zad_' . $key, $msg );
	}
	// اسم العرض = اسم المحل (بدل الرقم).
	if ( $v['shop'] ) {
		$user->display_name = $v['shop'];
	}
}
add_action( 'woocommerce_save_account_details_errors', 'zad_account_details_errors', 10, 2 );

/**
 * حفظ حقول المحل بعد نجاح التحقق.
 *
 * @param int $user_id رقم المستخدم.
 */
function zad_account_details_save( $user_id ) {
	zad_customer_save( $user_id, zad_customer_posted( 'account_first_name' ) );
}
add_action( 'woocommerce_save_account_details', 'zad_account_details_save' );

/**
 * «اطلب مجدداً» متاح للطلبيات قيد التجهيز والمكتملة.
 *
 * @param array $statuses الحالات.
 * @return array
 */
function zad_order_again_statuses( $statuses ) {
	return array_unique( array_merge( (array) $statuses, array( 'completed', 'processing', 'on-hold' ) ) );
}
add_filter( 'woocommerce_valid_order_statuses_for_order_again', 'zad_order_again_statuses' );

/**
 * سكربت الخريطة ونماذج الحساب (صفحة «حسابي» فقط).
 */
function zad_account_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}
	if ( is_user_logged_in() && ! is_wc_endpoint_url( 'edit-account' ) ) {
		return;
	}
	$ver = ZAD_VERSION;
	wp_enqueue_style( 'zad-leaflet', ZAD_URI . '/assets/vendor/leaflet/leaflet.css', array(), '1.9.4' );
	wp_enqueue_script( 'zad-leaflet', ZAD_URI . '/assets/vendor/leaflet/leaflet.js', array(), '1.9.4', true );
	wp_enqueue_script( 'zad-account', ZAD_URI . '/assets/js/account.js', array( 'zad-leaflet' ), $ver, true );
	$districts = array();
	foreach ( zad_districts() as $slug => $d ) {
		$districts[ $slug ] = array( $d[2], $d[3] );
	}
	wp_localize_script(
		'zad-account',
		'ZAD_ACCOUNT',
		array(
			'districts' => $districts,
			'tiles'     => apply_filters( 'zad_map_tiles', 'https://tile.openstreetmap.org/{z}/{x}/{y}.png' ),
			'attr'      => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'zad_account_assets', 30 );

/**
 * حقول المحل (تُستخدم في التسجيل وفي «بيانات المحل»).
 *
 * @param array  $v        القيم الحالية.
 * @param string $name_key اسم حقل الاسم.
 * @param string $prefix   بادئة المعرّفات.
 */
function zad_customer_fields_html( $v, $name_key, $prefix ) {
	$v = wp_parse_args(
		$v,
		array(
			'name'     => '',
			'shop'     => '',
			'wa_raw'   => '',
			'district' => '',
			'city'     => '',
			'address'  => '',
			'lat'      => '',
			'lng'      => '',
		)
	);
	$id   = static function ( $k ) use ( $prefix ) {
		return esc_attr( $prefix . '-' . $k );
	};
	$star = ' <span class="zd-req" aria-hidden="true">*</span>';
	?>
	<div class="zd-form__grid">
		<div class="zd-field">
			<label for="<?php echo $id( 'name' ); // phpcs:ignore ?>">اسمك<?php echo $star; // phpcs:ignore ?></label>
			<input type="text" id="<?php echo $id( 'name' ); // phpcs:ignore ?>" name="<?php echo esc_attr( $name_key ); ?>" value="<?php echo esc_attr( $v['name'] ); ?>" autocomplete="name" required aria-required="true" placeholder="مثال: أحمد الحلبي">
		</div>
		<div class="zd-field">
			<label for="<?php echo $id( 'shop' ); // phpcs:ignore ?>">اسم المحل<?php echo $star; // phpcs:ignore ?></label>
			<input type="text" id="<?php echo $id( 'shop' ); // phpcs:ignore ?>" name="zad_shop" value="<?php echo esc_attr( $v['shop'] ); ?>" autocomplete="organization" required aria-required="true" placeholder="مثال: ماركت النور">
		</div>
		<div class="zd-field zd-field--wide">
			<label for="<?php echo $id( 'wa' ); // phpcs:ignore ?>">رقم الواتساب<?php echo $star; // phpcs:ignore ?></label>
			<input type="tel" id="<?php echo $id( 'wa' ); // phpcs:ignore ?>" name="zad_wa" value="<?php echo esc_attr( $v['wa_raw'] ); ?>" autocomplete="tel" inputmode="tel" dir="ltr" required aria-required="true" placeholder="05xx xxx xx xx" aria-describedby="<?php echo $id( 'wa-hint' ); // phpcs:ignore ?>">
			<small class="zd-field__hint" id="<?php echo $id( 'wa-hint' ); // phpcs:ignore ?>">تدخل به إلى حسابك، ونتواصل معك عليه لتأكيد الطلبيات.</small>
		</div>
		<div class="zd-field">
			<label for="<?php echo $id( 'district' ); // phpcs:ignore ?>">المنطقة<?php echo $star; // phpcs:ignore ?></label>
			<select id="<?php echo $id( 'district' ); // phpcs:ignore ?>" name="zad_district" required aria-required="true" data-zd-district>
				<option value="">— اختر منطقة المحل —</option>
				<?php foreach ( zad_districts() as $slug => $d ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $v['district'], $slug ); ?>><?php echo esc_html( $d[1] ? $d[0] . ' · ' . $d[1] : $d[0] ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="zd-field" data-zd-city<?php echo 'other' === $v['district'] ? '' : ' hidden'; ?>>
			<label for="<?php echo $id( 'city' ); // phpcs:ignore ?>">المدينة<?php echo $star; // phpcs:ignore ?></label>
			<input type="text" id="<?php echo $id( 'city' ); // phpcs:ignore ?>" name="zad_city" value="<?php echo esc_attr( $v['city'] ); ?>" autocomplete="address-level2" placeholder="مثال: بورصة">
		</div>
		<div class="zd-field zd-field--wide">
			<label for="<?php echo $id( 'address' ); // phpcs:ignore ?>">عنوان المحل<?php echo $star; // phpcs:ignore ?></label>
			<input type="text" id="<?php echo $id( 'address' ); // phpcs:ignore ?>" name="zad_address" value="<?php echo esc_attr( $v['address'] ); ?>" autocomplete="street-address" required aria-required="true" placeholder="الحي، الشارع، رقم المحل، علامة مميزة">
		</div>
		<fieldset class="zd-field zd-field--wide zd-locate" data-zd-locate>
			<legend>موقع المحل على الخريطة <span class="zd-opt">(اختياري)</span></legend>
			<p class="zd-locate__hint">يساعد المندوب على الوصول إلى باب محلك مباشرة. اضغط «موقعي الحالي» إن كنت في المحل، أو المس مكانه على الخريطة، أو تجاوز هذه الخطوة ونتصل بك لتأكيد العنوان.</p>
			<div class="zd-locate__bar">
				<button type="button" class="zd-btn zd-btn--dark zd-locate__gps" data-zd-gps><?php zad_the_icon( 'pin', '', 18 ); ?><span>موقعي الحالي</span></button>
				<p class="zd-locate__status" data-zd-loc-status role="status" aria-live="polite"><?php echo ( is_numeric( $v['lat'] ) && is_numeric( $v['lng'] ) ) ? 'تم تحديد موقع المحل ✓' : 'لم يُحدَّد الموقع بعد'; ?></p>
			</div>
			<div class="zd-locate__map" data-zd-map dir="ltr" role="application" aria-label="خريطة لتحديد موقع المحل"></div>
			<input type="hidden" name="zad_lat" value="<?php echo esc_attr( $v['lat'] ); ?>" data-zd-lat>
			<input type="hidden" name="zad_lng" value="<?php echo esc_attr( $v['lng'] ); ?>" data-zd-lng>
		</fieldset>
	</div>
	<?php
}

/**
 * لوحة «حسابي»: بطاقة المحل، وآخر طلبية مع «اطلبها مجدداً»، واختصارات.
 */
function zad_account_dashboard() {
	$user_id = get_current_user_id();
	$p       = zad_customer_profile( $user_id );
	$orders  = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'limit'       => 1,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
	$last    = $orders ? $orders[0] : null;
	$count   = wc_get_customer_order_count( $user_id );
	$place   = 'other' === $p['district'] ? $p['city'] : zad_district_label( $p['district'], true );
	$missing = ! $p['shop'] || ! $p['wa'] || ! $p['district'];
	$edit    = wc_get_account_endpoint_url( 'edit-account' );
	?>
	<div class="zd-acc">
		<div class="zd-acc__hello">
			<p class="zd-eyebrow">حساب التاجر</p>
			<h2>أهلاً <?php echo esc_html( $p['name'] ? $p['name'] : wp_get_current_user()->display_name ); ?></h2>
			<p>من هنا تطلب بأسعار الجملة، وتتابع طلبياتك، وتعدّل بيانات محلك.</p>
		</div>
		<?php if ( $missing ) : ?>
			<div class="zd-acc__warn" role="note">
				<strong>أكمل بيانات محلك</strong>
				<span>نحتاج اسم المحل ورقم الواتساب والمنطقة لنوصل طلبياتك إلى باب المحل بسرعة.</span>
				<a class="zd-btn zd-btn--dark" href="<?php echo esc_url( $edit ); ?>">أكمل البيانات</a>
			</div>
		<?php endif; ?>
		<?php
		$zd_promo = function_exists( 'zad_promo' ) ? zad_promo() : null;
		if ( $zd_promo && $zd_promo['active'] ) :
			?>
			<a class="zd-acc__promo" href="<?php echo esc_url( $zd_promo['href'] ); ?>">
				<span class="zd-acc__promo-kicker"><?php zad_the_icon( 'percent', '', 16 ); ?> عرض فعّال<?php echo zad_promo_ends_text() ? ' · ' . esc_html( zad_promo_ends_text() ) : ''; ?></span>
				<strong><?php echo esc_html( $zd_promo['title'] ); ?><?php echo zad_promo_discount_label() ? ' — ' . esc_html( zad_promo_discount_label() ) : ''; ?></strong>
				<span><?php echo esc_html( $zd_promo['text'] ); ?></span>
				<span class="zd-acc__promo-go"><?php echo esc_html( $zd_promo['button'] ? $zd_promo['button'] : 'تسوّق العرض' ); ?> <?php zad_the_icon( 'arrow-left', '', 16 ); ?></span>
			</a>
		<?php endif; ?>
		<div class="zd-acc__grid">
			<section class="zd-acc__card" aria-labelledby="zd-acc-shop">
				<h3 id="zd-acc-shop">بيانات المحل</h3>
				<dl class="zd-acc__dl">
					<div><dt>المحل</dt><dd><?php echo esc_html( $p['shop'] ? $p['shop'] : '—' ); ?></dd></div>
					<div><dt>واتساب</dt><dd dir="ltr"><?php echo esc_html( $p['wa'] ? zad_format_phone( $p['wa'] ) : '—' ); ?></dd></div>
					<div><dt>المنطقة</dt><dd><?php echo esc_html( $place ? $place : '—' ); ?></dd></div>
					<div><dt>العنوان</dt><dd><?php echo esc_html( $p['address'] ? $p['address'] : '—' ); ?></dd></div>
					<div><dt>الموقع</dt><dd>
						<?php if ( null !== $p['lat'] ) : ?>
							<a href="<?php echo esc_url( zad_map_link( $p['lat'], $p['lng'] ) ); ?>" target="_blank" rel="noopener">عرض على الخريطة</a>
						<?php else : ?>
							لم يُحدَّد
						<?php endif; ?>
					</dd></div>
				</dl>
				<a class="zd-link" href="<?php echo esc_url( $edit ); ?>">تعديل بيانات المحل</a>
			</section>
			<section class="zd-acc__card" aria-labelledby="zd-acc-orders">
				<h3 id="zd-acc-orders">طلبياتك</h3>
				<?php if ( $last ) : ?>
					<p class="zd-acc__stat"><b><?php echo (int) $count; ?></b> طلبية حتى الآن</p>
					<p class="zd-acc__last">
						آخر طلبية <a href="<?php echo esc_url( $last->get_view_order_url() ); ?>">#<?php echo esc_html( $last->get_order_number() ); ?></a>
						· <?php echo esc_html( wc_format_datetime( $last->get_date_created() ) ); ?>
						· <?php echo esc_html( wc_get_order_status_name( $last->get_status() ) ); ?>
						<?php if ( zad_show_prices() ) : ?>
							· <?php echo wp_kses_post( $last->get_formatted_order_total() ); ?>
						<?php endif; ?>
					</p>
					<div class="zd-acc__actions">
						<?php if ( $last->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) ) ) ) : ?>
							<a class="zd-btn zd-btn--dark" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'order_again', $last->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' ) ); ?>">اطلب نفس الطلبية مجدداً</a>
						<?php endif; ?>
						<a class="zd-btn zd-btn--ghost" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">كل الطلبيات</a>
					</div>
				<?php else : ?>
					<p class="zd-acc__stat">لم ترسل أي طلبية بعد.</p>
					<p>حدّد عدد الكراتين لكل صنف من قائمة الأسعار، ونوصلها إلى محلك مع الدفع عند الاستلام.</p>
				<?php endif; ?>
			</section>
		</div>
		<?php
		$zd_new = function_exists( 'zad_new_product_ids' ) ? zad_new_product_ids( 4 ) : array();
		if ( $zd_new ) :
			?>
			<section class="zd-acc__new" aria-labelledby="zd-acc-new">
				<div class="zd-acc__new-head">
					<h3 id="zd-acc-new">وصل حديثاً</h3>
					<a class="zd-link" href="<?php echo esc_url( zad_new_url() ); ?>">كل الأصناف الجديدة</a>
				</div>
				<?php zad_product_grid( array( 'include' => $zd_new, 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC' ), 'zd-grid--acc' ); ?>
			</section>
		<?php endif; ?>
		<?php echo function_exists( 'zad_pitch_html' ) ? zad_pitch_html( 'account' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="zd-acc__quick">
			<a class="zd-btn zd-btn--primary zd-btn--lg" href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">افتح قائمة الأسعار</a>
			<?php if ( zad_wa_number() ) : ?>
				<a class="zd-btn zd-btn--wa zd-btn--lg" href="<?php echo esc_url( zad_wa_link( 'مرحباً، أنا ' . $p['name'] . ' من ' . $p['shop'] . '.' ) ); ?>" target="_blank" rel="noopener">راسل المبيعات</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
