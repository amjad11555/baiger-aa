<?php
/**
 * نظام الطلبات: «طلب توريد خاص» + «التصدير» + «تواصل مع المبيعات».
 *
 * يحفظ كل طلب في لوحة التحكم (طلبات العملاء) مع حالة المتابعة،
 * ويرسل إشعاراً بالبريد، مع حماية من السبام (حقل فخ + فحص زمني + حد للمعدل).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * تسجيل نوع المحتوى.
 */
function zad_register_request_cpt() {
	register_post_type(
		'zad_request',
		array(
			'labels'              => array(
				'name'               => 'طلبات العملاء',
				'singular_name'      => 'طلب عميل',
				'menu_name'          => 'طلبات العملاء',
				'all_items'          => 'كل الطلبات',
				'edit_item'          => 'تفاصيل الطلب',
				'view_item'          => 'عرض الطلب',
				'search_items'       => 'بحث في الطلبات',
				'not_found'          => 'لا توجد طلبات بعد',
				'not_found_in_trash' => 'لا توجد طلبات في سلة المهملات',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'menu_position'       => 56,
			'menu_icon'           => 'dashicons-clipboard',
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'zad_register_request_cpt' );

/**
 * أنواع الطلبات وحقولها.
 *
 * @return array
 */
function zad_request_types() {
	return array(
		'special' => array(
			'label'  => 'منتج غير متوفر',
			'prefix' => 'SP',
			'fields' => array(
				'name'    => array( 'الاسم الكامل', 'text', true ),
				'shop'    => array( 'اسم المتجر / الشركة', 'text', true ),
				'phone'   => array( 'رقم الجوال (واتساب)', 'tel', true ),
				'city'    => array( 'المدينة / المنطقة', 'text', true ),
				'urgency' => array( 'موعد الحاجة', 'select', false ),
				'notes'   => array( 'ملاحظات إضافية', 'textarea', false ),
			),
		),
		'export'  => array(
			'label'  => 'جملة دولية / تصدير',
			'prefix' => 'EX',
			'fields' => array(
				'company'   => array( 'اسم الشركة', 'text', true ),
				'name'      => array( 'اسم المسؤول', 'text', true ),
				'country'   => array( 'الدولة', 'select', true ),
				'city'      => array( 'المدينة / ميناء الوصول', 'text', false ),
				'phone'     => array( 'الهاتف / واتساب (مع رمز الدولة)', 'tel', true ),
				'email'     => array( 'البريد الإلكتروني', 'email', true ),
				'business'  => array( 'نوع النشاط', 'select', false ),
				'interests' => array( 'الأقسام المطلوبة', 'checkboxes', false ),
				'volume'    => array( 'الكمية التقديرية', 'select', false ),
				'incoterm'  => array( 'شرط التسليم المفضل', 'select', false ),
				'docs'      => array( 'المستندات المطلوبة', 'checkboxes', false ),
				'products'  => array( 'المنتجات والكميات المطلوبة', 'textarea', false ),
				'notes'     => array( 'ملاحظات', 'textarea', false ),
			),
		),
		'contact' => array(
			'label'  => 'رسالة تواصل',
			'prefix' => 'CT',
			'fields' => array(
				'name'    => array( 'الاسم', 'text', true ),
				'phone'   => array( 'رقم الجوال', 'tel', true ),
				'email'   => array( 'البريد الإلكتروني', 'email', false ),
				'subject' => array( 'الموضوع', 'select', false ),
				'message' => array( 'الرسالة', 'textarea', true ),
			),
		),
	);
}

/**
 * خيارات القوائم المنسدلة ومجموعات الاختيار.
 *
 * @return array
 */
function zad_request_options() {
	return array(
		'urgency'   => array( 'عادي (خلال أسبوع)', 'مستعجل (خلال 48 ساعة)', 'طلب دوري (أسبوعي أو شهري)' ),
		'business'  => array( 'مستورد', 'موزع جملة', 'سلسلة سوبرماركت', 'متجر إلكتروني', 'أخرى' ),
		'interests' => array( 'كيك', 'بسكويت وويفر', 'شيبسات وكراكرز', 'تسالي وشوكولاتة', 'تشكيلة كاملة' ),
		'volume'    => array( 'طبليات مختلطة (أقل من حاوية)', 'حاوية 20 قدم', 'حاوية 40 قدم', 'أكثر من حاوية شهرياً', 'غير محدد بعد' ),
		'incoterm'  => array( 'EXW — استلام من مستودعنا', 'FOB — تسليم ميناء تركي', 'CIF — حتى ميناء الوصول', 'DAP — توصيل حتى مستودعكم', 'لست متأكداً — ساعدوني في الاختيار' ),
		'docs'      => array( 'شهادة منشأ', 'شهادة حلال', 'شهادة صحية', 'فاتورة وقائمة تعبئة', 'ملصقات بلغة بلد الوصول' ),
		'subject'   => array( 'استفسار عام', 'طلب تسعيرة', 'متابعة طلب', 'اقتراح أو شكوى', 'شراكة وتوزيع' ),
		'unit'      => array( 'كرتونة', 'قطعة', 'علبة', 'كيلو', 'طبلية' ),
		'country'   => array(
			'الدول العربية' => array( 'السعودية', 'الإمارات', 'الكويت', 'قطر', 'البحرين', 'سلطنة عُمان', 'العراق', 'الأردن', 'سوريا', 'لبنان', 'فلسطين', 'اليمن', 'مصر', 'ليبيا', 'تونس', 'الجزائر', 'المغرب', 'السودان', 'موريتانيا', 'الصومال', 'جيبوتي', 'جزر القمر' ),
			'أوروبا'        => array( 'ألمانيا', 'هولندا', 'بلجيكا', 'فرنسا', 'النمسا', 'السويد', 'الدنمارك', 'النرويج', 'المملكة المتحدة', 'إيطاليا', 'إسبانيا', 'سويسرا', 'بولندا', 'رومانيا', 'بلغاريا', 'اليونان' ),
			'دول أخرى'      => array( 'أذربيجان', 'جورجيا', 'كازاخستان', 'أوزبكستان', 'تركمانستان', 'قيرغيزستان', 'أفغانستان', 'باكستان', 'روسيا', 'أوكرانيا', 'الولايات المتحدة', 'كندا', 'أستراليا', 'ماليزيا', 'إندونيسيا', 'نيجيريا', 'غانا', 'كينيا', 'إثيوبيا', 'جنوب أفريقيا', 'دولة أخرى' ),
		),
	);
}

/**
 * قائمة مسطّحة للدول (للتحقق).
 *
 * @return array
 */
function zad_countries_flat() {
	$opts = zad_request_options();
	$out  = array();
	foreach ( $opts['country'] as $group ) {
		$out = array_merge( $out, $group );
	}
	return $out;
}

/**
 * عنوان IP للزائر (للحد من المعدل فقط، يُخزن مُشفّراً).
 *
 * @return string
 */
function zad_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return (string) filter_var( $ip, FILTER_VALIDATE_IP );
}

/**
 * معالجة إرسال النموذج.
 */
function zad_handle_request() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- نموذج عام للزوار؛ الحماية عبر فخ + توقيع زمني + حد للمعدل.
	$types = zad_request_types();
	$type  = isset( $_POST['zd_type'] ) ? sanitize_key( wp_unslash( $_POST['zd_type'] ) ) : '';
	$back  = wp_get_referer();
	$back  = $back ? remove_query_arg( array( 'zd_sent', 'zd_err' ), $back ) : home_url( '/' );

	if ( ! isset( $types[ $type ] ) ) {
		wp_safe_redirect( add_query_arg( 'zd_err', 'invalid', $back ) . '#zd-form' );
		exit;
	}

	// 1) فخ الروبوتات: حقل مخفي يجب أن يبقى فارغاً.
	if ( ! empty( $_POST['zd_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'zd_sent', 'OK', $back ) . '#zd-form' );
		exit;
	}

	// 2) توقيع زمني: يمنع الإرسال الآلي الفوري.
	$ts  = isset( $_POST['zd_ts'] ) ? (int) $_POST['zd_ts'] : 0;
	$sig = isset( $_POST['zd_sig'] ) ? sanitize_text_field( wp_unslash( $_POST['zd_sig'] ) ) : '';
	$age = time() - $ts;
	if ( ! $ts || ! hash_equals( wp_hash( 'zad-form|' . $ts ), $sig ) || $age < 3 || $age > 14 * DAY_IN_SECONDS ) {
		wp_safe_redirect( add_query_arg( 'zd_err', 'expired', $back ) . '#zd-form' );
		exit;
	}

	// 3) حد المعدل: 6 طلبات في الساعة لكل عنوان.
	$rl_key = 'zd_rl_' . md5( zad_client_ip() );
	$hits   = (int) get_transient( $rl_key );
	if ( $hits >= 6 ) {
		wp_safe_redirect( add_query_arg( 'zd_err', 'rate', $back ) . '#zd-form' );
		exit;
	}
	set_transient( $rl_key, $hits + 1, HOUR_IN_SECONDS );

	$options = zad_request_options();
	$values  = array();
	$missing = false;

	foreach ( $types[ $type ]['fields'] as $key => $def ) {
		list( $label, $ftype, $required ) = $def;
		$name = 'zd_' . $key;
		$val  = '';
		switch ( $ftype ) {
			case 'textarea':
				$val = isset( $_POST[ $name ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $name ] ) ) : '';
				break;
			case 'email':
				$val = isset( $_POST[ $name ] ) ? sanitize_email( wp_unslash( $_POST[ $name ] ) ) : '';
				if ( $val && ! is_email( $val ) ) {
					$val = '';
				}
				break;
			case 'checkboxes':
				$raw     = isset( $_POST[ $name ] ) ? (array) wp_unslash( $_POST[ $name ] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$allowed = isset( $options[ $key ] ) ? $options[ $key ] : array();
				$val     = implode( '، ', array_values( array_intersect( array_map( 'sanitize_text_field', $raw ), $allowed ) ) );
				break;
			case 'select':
				$val     = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
				$allowed = 'country' === $key ? zad_countries_flat() : ( isset( $options[ $key ] ) ? $options[ $key ] : array() );
				if ( ! in_array( $val, $allowed, true ) ) {
					$val = '';
				}
				break;
			case 'tel':
				$val = isset( $_POST[ $name ] ) ? preg_replace( '/[^\d+\s\-()]/', '', sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) ) : '';
				if ( strlen( preg_replace( '/\D/', '', $val ) ) < 7 ) {
					$val = '';
				}
				break;
			default:
				$val = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
		}
		$val            = mb_substr( $val, 0, 'textarea' === $ftype ? 4000 : 200 );
		$values[ $key ] = $val;
		if ( $required && '' === $val ) {
			$missing = true;
		}
	}

	// صفوف المنتجات (طلب منتج غير متوفر).
	$items = array();
	if ( 'special' === $type ) {
		$names  = isset( $_POST['zd_item_name'] ) ? (array) wp_unslash( $_POST['zd_item_name'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$brands = isset( $_POST['zd_item_brand'] ) ? (array) wp_unslash( $_POST['zd_item_brand'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$qtys   = isset( $_POST['zd_item_qty'] ) ? (array) wp_unslash( $_POST['zd_item_qty'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$units  = isset( $_POST['zd_item_unit'] ) ? (array) wp_unslash( $_POST['zd_item_unit'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( array_slice( $names, 0, 30 ) as $i => $n ) {
			$n = mb_substr( sanitize_text_field( $n ), 0, 150 );
			if ( '' === $n ) {
				continue;
			}
			$unit    = isset( $units[ $i ] ) ? sanitize_text_field( $units[ $i ] ) : '';
			$items[] = array(
				'name'  => $n,
				'brand' => isset( $brands[ $i ] ) ? mb_substr( sanitize_text_field( $brands[ $i ] ), 0, 80 ) : '',
				'qty'   => isset( $qtys[ $i ] ) ? max( 0, min( 99999, (int) $qtys[ $i ] ) ) : 0,
				'unit'  => in_array( $unit, $options['unit'], true ) ? $unit : 'كرتونة',
			);
		}
		if ( ! $items ) {
			$missing = true;
		}
	}
	// phpcs:enable

	if ( $missing ) {
		wp_safe_redirect( add_query_arg( 'zd_err', 'missing', $back ) . '#zd-form' );
		exit;
	}

	$ref   = $types[ $type ]['prefix'] . '-' . wp_date( 'ymd' ) . '-' . wp_rand( 100, 999 );
	$who   = ! empty( $values['shop'] ) ? $values['shop'] : ( ! empty( $values['company'] ) ? $values['company'] : $values['name'] );
	$title = $ref . ' — ' . $who;

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'zad_request',
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wp_safe_redirect( add_query_arg( 'zd_err', 'server', $back ) . '#zd-form' );
		exit;
	}

	update_post_meta( $post_id, '_sh_type', $type );
	update_post_meta( $post_id, '_sh_ref', $ref );
	update_post_meta( $post_id, '_sh_status', 'new' );
	update_post_meta( $post_id, '_sh_fields', $values );
	update_post_meta( $post_id, '_sh_items', $items );

	// صورة اختيارية للمنتج المطلوب.
	if ( 'special' === $type && ! empty( $_FILES['zd_photo']['name'] ) ) {
		$att = zad_handle_request_photo( $post_id );
		if ( $att ) {
			update_post_meta( $post_id, '_sh_photo', $att );
		}
	}

	zad_request_notify( $post_id );

	wp_safe_redirect( add_query_arg( 'zd_sent', rawurlencode( $ref ), $back ) . '#zd-form' );
	exit;
}
add_action( 'admin_post_nopriv_zad_request', 'zad_handle_request' );
add_action( 'admin_post_zad_request', 'zad_handle_request' );

/**
 * رفع صورة المنتج المطلوب (JPG/PNG/WEBP حتى 5MB).
 *
 * @param int $post_id رقم الطلب.
 * @return int رقم المرفق أو 0.
 */
function zad_handle_request_photo( $post_id ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( empty( $_FILES['zd_photo'] ) || ! empty( $_FILES['zd_photo']['error'] ) ) {
		return 0;
	}
	$size = isset( $_FILES['zd_photo']['size'] ) ? (int) $_FILES['zd_photo']['size'] : 0;
	if ( $size <= 0 || $size > 5 * MB_IN_BYTES ) {
		return 0;
	}
	// phpcs:enable
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);
	$att   = media_handle_upload(
		'zd_photo',
		$post_id,
		array(),
		array(
			'test_form' => false,
			'mimes'     => $mimes,
		)
	);
	return is_wp_error( $att ) ? 0 : (int) $att;
}

/**
 * إرسال إشعار بالبريد.
 *
 * @param int $post_id رقم الطلب.
 */
function zad_request_notify( $post_id ) {
	$types  = zad_request_types();
	$type   = get_post_meta( $post_id, '_sh_type', true );
	$ref    = get_post_meta( $post_id, '_sh_ref', true );
	$fields = (array) get_post_meta( $post_id, '_sh_fields', true );
	$items  = (array) get_post_meta( $post_id, '_sh_items', true );
	$to     = zad_opt( 'email' ) ? zad_opt( 'email' ) : get_option( 'admin_email' );

	$lines = array( 'طلب جديد من موقع ' . get_bloginfo( 'name' ), 'النوع: ' . $types[ $type ]['label'], 'المرجع: ' . $ref, '' );
	foreach ( $types[ $type ]['fields'] as $key => $def ) {
		if ( ! empty( $fields[ $key ] ) ) {
			$lines[] = $def[0] . ': ' . $fields[ $key ];
		}
	}
	if ( $items ) {
		$lines[] = '';
		$lines[] = 'المنتجات المطلوبة:';
		foreach ( $items as $i => $it ) {
			$lines[] = sprintf( '%d) %s %s — %s %s', $i + 1, $it['name'], $it['brand'] ? '(' . $it['brand'] . ')' : '', $it['qty'] ? $it['qty'] : '؟', $it['unit'] );
		}
	}
	$lines[] = '';
	$lines[] = 'إدارة الطلب: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $fields['email'] ) && is_email( $fields['email'] ) ) {
		$headers[] = 'Reply-To: ' . $fields['email'];
	}
	wp_mail( $to, sprintf( '[%s] %s — %s', get_bloginfo( 'name' ), $types[ $type ]['label'], $ref ), implode( "\n", $lines ), $headers );
}

/**
 * حقول الحماية المخفية.
 *
 * @return string
 */
function zad_form_guard_fields() {
	$ts = time();
	return sprintf(
		'<div class="zd-hp" aria-hidden="true"><label>اترك هذا الحقل فارغاً<input type="text" name="zd_website" tabindex="-1" autocomplete="off"></label></div><input type="hidden" name="zd_ts" value="%1$d"><input type="hidden" name="zd_sig" value="%2$s"><input type="hidden" name="action" value="zad_request">',
		$ts,
		esc_attr( wp_hash( 'zad-form|' . $ts ) )
	);
}

/**
 * رسالة نجاح/خطأ بعد الإرسال.
 *
 * @param string $type النوع.
 * @return bool هل تم عرض رسالة نجاح.
 */
function zad_form_feedback( $type ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['zd_sent'] ) ) {
		$ref  = sanitize_text_field( wp_unslash( $_GET['zd_sent'] ) );
		$ref  = preg_match( '/^[A-Z]{2}-\d{6}-\d{3}$/', $ref ) ? $ref : '';
		$msgs = array(
			'special' => 'استلمنا طلب التوريد، وسيتواصل معك فريق المشتريات خلال يوم عمل بالسعر والتوفر.',
			'export'  => 'استلمنا طلب التصدير، وسنرسل لك عرض سعر مفصلاً مع توزيع الحاوية خلال يوم عمل.',
			'contact' => 'وصلت رسالتك إلى قسم المبيعات، وسنرد عليك خلال ساعات العمل.',
		);
		echo '<div class="zd-success" role="status"><p class="zd-eyebrow">تم الإرسال</p><h2>شكراً لك، وصل طلبك</h2>';
		echo '<p>' . esc_html( isset( $msgs[ $type ] ) ? $msgs[ $type ] : $msgs['contact'] ) . '</p>';
		if ( $ref ) {
			printf( '<p class="zd-success__ref">رقم المرجع: <strong dir="ltr">%s</strong></p>', esc_html( $ref ) );
			if ( zad_wa_number() ) {
				printf(
					'<a class="zd-btn zd-btn--wa" href="%s" target="_blank" rel="noopener">تابع طلبك عبر واتساب</a>',
					esc_url( zad_wa_link( 'مرحباً، أرسلت طلباً من الموقع برقم المرجع ' . $ref . ' وأرغب بالمتابعة.' ) )
				);
			}
		}
		echo '</div>';
		return true;
	}
	if ( isset( $_GET['zd_err'] ) ) {
		$code = sanitize_key( wp_unslash( $_GET['zd_err'] ) );
		$errs = array(
			'missing' => 'يرجى تعبئة جميع الحقول المطلوبة (المشار إليها بـ *) وإدخال رقم جوال صحيح.',
			'expired' => 'انتهت صلاحية النموذج أو تم إرساله بسرعة كبيرة. أعد المحاولة من فضلك.',
			'rate'    => 'أرسلت عدة طلبات خلال وقت قصير. حاول بعد قليل أو تواصل معنا عبر واتساب.',
			'server'  => 'حدث خطأ أثناء الحفظ. حاول مرة أخرى أو تواصل معنا عبر واتساب.',
			'invalid' => 'طلب غير صالح.',
		);
		printf( '<div class="zd-alert zd-alert--error" role="alert">%s</div>', esc_html( isset( $errs[ $code ] ) ? $errs[ $code ] : $errs['invalid'] ) );
	}
	// phpcs:enable
	return false;
}

/**
 * طباعة حقل.
 *
 * @param string $type النوع.
 * @param string $key  المفتاح.
 * @param array  $args إعدادات إضافية.
 */
function zad_form_field( $type, $key, $args = array() ) {
	$types = zad_request_types();
	if ( ! isset( $types[ $type ]['fields'][ $key ] ) ) {
		return;
	}
	list( $label, $ftype, $required ) = $types[ $type ]['fields'][ $key ];
	$options = zad_request_options();
	$id      = 'zd-' . $type . '-' . $key;
	$name    = 'zd_' . $key;
	$args    = wp_parse_args(
		$args,
		array(
			'placeholder'  => '',
			'wide'         => false,
			'autocomplete' => '',
			'dir'          => '',
		)
	);
	$req     = $required ? ' required aria-required="true"' : '';
	$star    = $required ? ' <span class="zd-req" aria-hidden="true">*</span>' : '';

	printf( '<div class="zd-field%s">', $args['wide'] || in_array( $ftype, array( 'textarea', 'checkboxes' ), true ) ? ' zd-field--wide' : '' );

	if ( 'checkboxes' === $ftype ) {
		printf( '<fieldset><legend>%s%s</legend><div class="zd-checks">', esc_html( $label ), $star ); // phpcs:ignore WordPress.Security.EscapeOutput
		foreach ( $options[ $key ] as $opt ) {
			printf( '<label class="zd-check"><input type="checkbox" name="%1$s[]" value="%2$s"><span>%2$s</span></label>', esc_attr( $name ), esc_attr( $opt ) );
		}
		echo '</div></fieldset></div>';
		return;
	}

	printf( '<label for="%1$s">%2$s%3$s</label>', esc_attr( $id ), esc_html( $label ), $star ); // phpcs:ignore WordPress.Security.EscapeOutput

	$extra = '';
	if ( $args['placeholder'] ) {
		$extra .= ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
	}
	if ( $args['autocomplete'] ) {
		$extra .= ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"';
	}
	if ( $args['dir'] ) {
		$extra .= ' dir="' . esc_attr( $args['dir'] ) . '"';
	}

	if ( 'textarea' === $ftype ) {
		printf( '<textarea id="%1$s" name="%2$s" rows="4"%3$s%4$s></textarea>', esc_attr( $id ), esc_attr( $name ), $req, $extra ); // phpcs:ignore WordPress.Security.EscapeOutput
	} elseif ( 'select' === $ftype ) {
		printf( '<select id="%1$s" name="%2$s"%3$s><option value="">— اختر —</option>', esc_attr( $id ), esc_attr( $name ), $req ); // phpcs:ignore WordPress.Security.EscapeOutput
		$opts = isset( $options[ $key ] ) ? $options[ $key ] : array();
		foreach ( $opts as $group => $opt ) {
			if ( is_array( $opt ) ) {
				printf( '<optgroup label="%s">', esc_attr( $group ) );
				foreach ( $opt as $o ) {
					printf( '<option value="%1$s">%1$s</option>', esc_attr( $o ) );
				}
				echo '</optgroup>';
			} else {
				printf( '<option value="%1$s">%1$s</option>', esc_attr( $opt ) );
			}
		}
		echo '</select>';
	} else {
		$html_type = in_array( $ftype, array( 'tel', 'email' ), true ) ? $ftype : 'text';
		if ( 'tel' === $ftype ) {
			$extra .= ' inputmode="tel" dir="ltr"';
		}
		printf( '<input type="%1$s" id="%2$s" name="%3$s"%4$s%5$s>', esc_attr( $html_type ), esc_attr( $id ), esc_attr( $name ), $req, $extra ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}

/* -------------------------------------------------------------------------
 * لوحة التحكم
 * ---------------------------------------------------------------------- */

/**
 * حالات المتابعة.
 *
 * @return array
 */
function zad_request_statuses() {
	return array(
		'new'      => 'جديد',
		'progress' => 'قيد المتابعة',
		'done'     => 'تم التنفيذ',
		'closed'   => 'مغلق',
	);
}

/**
 * أعمدة القائمة.
 *
 * @param array $cols الأعمدة.
 * @return array
 */
function zad_request_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => 'الطلب',
		'zd_type'   => 'النوع',
		'zd_phone'  => 'التواصل',
		'zd_place'  => 'المكان',
		'zd_status' => 'الحالة',
		'date'      => 'التاريخ',
	);
}
add_filter( 'manage_zad_request_posts_columns', 'zad_request_columns' );

/**
 * محتوى الأعمدة.
 *
 * @param string $col     العمود.
 * @param int    $post_id الطلب.
 */
function zad_request_column_content( $col, $post_id ) {
	$types  = zad_request_types();
	$fields = (array) get_post_meta( $post_id, '_sh_fields', true );
	switch ( $col ) {
		case 'zd_type':
			$t = get_post_meta( $post_id, '_sh_type', true );
			echo esc_html( isset( $types[ $t ] ) ? $types[ $t ]['label'] : '—' );
			break;
		case 'zd_phone':
			echo esc_html( isset( $fields['phone'] ) ? $fields['phone'] : '' );
			if ( ! empty( $fields['email'] ) ) {
				echo '<br><small>' . esc_html( $fields['email'] ) . '</small>';
			}
			break;
		case 'zd_place':
			echo esc_html( trim( ( isset( $fields['country'] ) ? $fields['country'] . ' ' : '' ) . ( isset( $fields['city'] ) ? $fields['city'] : '' ) ) );
			break;
		case 'zd_status':
			$s   = get_post_meta( $post_id, '_sh_status', true );
			$all = zad_request_statuses();
			printf( '<span class="zd-status zd-status--%1$s">%2$s</span>', esc_attr( $s ), esc_html( isset( $all[ $s ] ) ? $all[ $s ] : '—' ) );
			break;
	}
}
add_action( 'manage_zad_request_posts_custom_column', 'zad_request_column_content', 10, 2 );

/**
 * صناديق التفاصيل.
 */
function zad_request_meta_boxes() {
	add_meta_box( 'zd_request_details', 'تفاصيل الطلب', 'zad_request_details_box', 'zad_request', 'normal', 'high' );
	add_meta_box( 'zd_request_status', 'حالة المتابعة', 'zad_request_status_box', 'zad_request', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'zad_request_meta_boxes' );

/**
 * صندوق التفاصيل.
 *
 * @param WP_Post $post الطلب.
 */
function zad_request_details_box( $post ) {
	$types  = zad_request_types();
	$type   = get_post_meta( $post->ID, '_sh_type', true );
	$fields = (array) get_post_meta( $post->ID, '_sh_fields', true );
	$items  = (array) get_post_meta( $post->ID, '_sh_items', true );
	$photo  = (int) get_post_meta( $post->ID, '_sh_photo', true );

	echo '<table class="widefat striped"><tbody>';
	printf( '<tr><th style="width:190px">المرجع</th><td><strong>%s</strong> — %s</td></tr>', esc_html( get_post_meta( $post->ID, '_sh_ref', true ) ), esc_html( isset( $types[ $type ] ) ? $types[ $type ]['label'] : '' ) );
	if ( isset( $types[ $type ] ) ) {
		foreach ( $types[ $type ]['fields'] as $key => $def ) {
			if ( isset( $fields[ $key ] ) && '' !== $fields[ $key ] ) {
				printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $def[0] ), nl2br( esc_html( $fields[ $key ] ) ) );
			}
		}
	}
	echo '</tbody></table>';

	if ( $items ) {
		echo '<h3>المنتجات المطلوبة</h3><table class="widefat striped"><thead><tr><th>#</th><th>المنتج</th><th>العلامة</th><th>الكمية</th></tr></thead><tbody>';
		foreach ( $items as $i => $it ) {
			printf( '<tr><td>%d</td><td>%s</td><td>%s</td><td>%s %s</td></tr>', (int) $i + 1, esc_html( $it['name'] ), esc_html( $it['brand'] ), esc_html( $it['qty'] ? $it['qty'] : '—' ), esc_html( $it['unit'] ) );
		}
		echo '</tbody></table>';
	}

	if ( $photo ) {
		echo '<h3>صورة مرفقة</h3>' . wp_get_attachment_image( $photo, 'medium' );
	}

	echo '<p style="margin-top:16px">';
	if ( ! empty( $fields['phone'] ) ) {
		$num = preg_replace( '/\D+/', '', $fields['phone'] );
		if ( 0 === strpos( $num, '0' ) && 11 === strlen( $num ) ) {
			$num = '90' . substr( $num, 1 );
		}
		printf( '<a class="button button-primary" href="https://wa.me/%1$s" target="_blank" rel="noopener">مراسلة العميل عبر واتساب</a> ', esc_attr( $num ) );
		printf( '<a class="button" href="tel:%1$s">اتصال</a> ', esc_attr( preg_replace( '/[^\d+]/', '', $fields['phone'] ) ) );
	}
	if ( ! empty( $fields['email'] ) ) {
		printf( '<a class="button" href="mailto:%1$s">مراسلة بالبريد</a>', esc_attr( $fields['email'] ) );
	}
	echo '</p>';
}

/**
 * صندوق الحالة.
 *
 * @param WP_Post $post الطلب.
 */
function zad_request_status_box( $post ) {
	wp_nonce_field( 'zd_request_status', 'zd_request_status_nonce' );
	$current = get_post_meta( $post->ID, '_sh_status', true );
	echo '<select name="zd_status" style="width:100%">';
	foreach ( zad_request_statuses() as $k => $label ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $k ), selected( $current, $k, false ), esc_html( $label ) );
	}
	echo '</select><p class="description">غيّر الحالة ثم اضغط «تحديث».</p>';
}

/**
 * حفظ الحالة.
 *
 * @param int $post_id الطلب.
 */
function zad_request_save_status( $post_id ) {
	if ( ! isset( $_POST['zd_request_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zd_request_status_nonce'] ) ), 'zd_request_status' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$status = isset( $_POST['zd_status'] ) ? sanitize_key( wp_unslash( $_POST['zd_status'] ) ) : '';
	if ( array_key_exists( $status, zad_request_statuses() ) ) {
		update_post_meta( $post_id, '_sh_status', $status );
	}
}
add_action( 'save_post_zad_request', 'zad_request_save_status' );

/**
 * عدّاد الطلبات الجديدة في القائمة الجانبية.
 */
function zad_request_menu_bubble() {
	global $menu;
	if ( ! is_array( $menu ) ) {
		return;
	}
	$count = count(
		get_posts(
			array(
				'post_type'      => 'zad_request',
				'post_status'    => 'publish',
				'posts_per_page' => 99,
				'fields'         => 'ids',
				'meta_key'       => '_sh_status', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => 'new', // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		)
	);
	if ( ! $count ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=zad_request' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
}
add_action( 'admin_menu', 'zad_request_menu_bubble', 99 );

/**
 * تنسيق بسيط لشارات الحالة في لوحة التحكم.
 */
function zad_request_admin_css() {
	$screen = get_current_screen();
	if ( ! $screen || 'zad_request' !== $screen->post_type ) {
		return;
	}
	echo '<style>.zd-status{display:inline-block;padding:2px 10px;border-radius:99px;font-weight:600;font-size:12px;background:#eee}.zd-status--new{background:#fde8e8;color:#b00005}.zd-status--progress{background:#fff4d6;color:#8a5a00}.zd-status--done{background:#e3f6ea;color:#1e7a44}</style>';
}
add_action( 'admin_head', 'zad_request_admin_css' );
