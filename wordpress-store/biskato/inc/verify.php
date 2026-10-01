<?php
/**
 * تأكيد حساب الجملة عبر واتساب قبل إظهار الأسعار والطلب.
 *
 * طريقتان:
 * 1) تلقائية (إن أُدخلت بيانات WhatsApp Cloud API في المخصّص): يصل الزبون رمز من 6 أرقام على واتساب،
 *    يكتبه في «حسابي» فيُفعَّل حسابه فوراً.
 * 2) يدوية (الافتراضية، بلا أي إعداد): يضغط الزبون «أكّد حسابي عبر واتساب» فتُفتح رسالة جاهزة من رقمه
 *    إلى رقم المتجر، فيها بيانات المحل ورابط تفعيل. يفتح صاحب المتجر الرابط (وهو مسجّل الدخول)
 *    ويضغط «تفعيل»، ثم يبلغ الزبون برسالة واتساب جاهزة. وصول الرسالة من رقم الزبون نفسه هو التأكيد.
 *
 * الحسابات القديمة (قبل هذه الميزة) والمديرون مؤكَّدون تلقائياً.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * هل التأكيد عبر واتساب مفعّل؟
 *
 * @return bool
 */
function zad_verify_enabled() {
	return (bool) zad_opt( 'confirm_wa' );
}

/**
 * هل بيانات WhatsApp Cloud API مكتملة (الإرسال التلقائي للرمز)؟
 *
 * @return bool
 */
function zad_verify_api_ready() {
	return '' !== trim( (string) zad_opt( 'wa_api_token' ) ) && '' !== trim( (string) zad_opt( 'wa_api_phone' ) ) && '' !== trim( (string) zad_opt( 'wa_api_template' ) );
}

/**
 * هل حساب المستخدم مؤكَّد؟
 *
 * @param int $user_id المستخدم (الحالي افتراضياً).
 * @return bool
 */
function zad_user_verified( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id || ! zad_verify_enabled() ) {
		return (bool) $user_id;
	}
	if ( user_can( $user_id, 'edit_shop_orders' ) || user_can( $user_id, 'manage_options' ) ) {
		return true;
	}
	// يُطلب التأكيد فقط من الحسابات المسجّلة بعد تفعيل الميزة.
	if ( ! get_user_meta( $user_id, 'zad_verify_required', true ) ) {
		return true;
	}
	return (bool) get_user_meta( $user_id, 'zad_verified', true );
}

/**
 * تجهيز رمز ورابط التأكيد لحساب جديد، وإرسال الرمز تلقائياً إن أمكن.
 *
 * @param int $user_id المستخدم.
 * @return bool أُرسل الرمز عبر الواجهة البرمجية.
 */
function zad_verify_start( $user_id ) {
	update_user_meta( $user_id, 'zad_verify_required', 1 );
	update_user_meta( $user_id, 'zad_verify_code', (string) wp_rand( 100000, 999999 ) );
	update_user_meta( $user_id, 'zad_verify_token', wp_generate_password( 32, false ) );
	delete_user_meta( $user_id, 'zad_verified' );
	return zad_verify_api_send( $user_id );
}

/**
 * إرسال الرمز عبر WhatsApp Cloud API (قالب «مصادقة» معتمد من Meta).
 *
 * @param int $user_id المستخدم.
 * @return bool
 */
function zad_verify_api_send( $user_id ) {
	if ( ! zad_verify_api_ready() ) {
		return false;
	}
	$to   = (string) get_user_meta( $user_id, 'zad_wa', true );
	$code = (string) get_user_meta( $user_id, 'zad_verify_code', true );
	if ( '' === $to || '' === $code ) {
		return false;
	}
	$body = array(
		'messaging_product' => 'whatsapp',
		'to'                => $to,
		'type'              => 'template',
		'template'          => array(
			'name'       => trim( (string) zad_opt( 'wa_api_template' ) ),
			'language'   => array( 'code' => trim( (string) zad_opt( 'wa_api_lang' ) ) ? trim( (string) zad_opt( 'wa_api_lang' ) ) : 'ar' ),
			'components' => array(
				array(
					'type'       => 'body',
					'parameters' => array(
						array(
							'type' => 'text',
							'text' => $code,
						),
					),
				),
				array(
					'type'       => 'button',
					'sub_type'   => 'url',
					'index'      => '0',
					'parameters' => array(
						array(
							'type' => 'text',
							'text' => $code,
						),
					),
				),
			),
		),
	);
	$res = wp_remote_post(
		apply_filters( 'zad_wa_api_url', 'https://graph.facebook.com/v21.0/' . rawurlencode( trim( (string) zad_opt( 'wa_api_phone' ) ) ) . '/messages' ),
		array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Bearer ' . trim( (string) zad_opt( 'wa_api_token' ) ),
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( apply_filters( 'zad_wa_api_body', $body, $user_id ) ),
		)
	);
	$ok = ! is_wp_error( $res ) && 200 === (int) wp_remote_retrieve_response_code( $res );
	if ( $ok ) {
		update_user_meta( $user_id, 'zad_verify_sent', time() );
		delete_option( 'zad_wa_api_error' );
	} else {
		// يُعرض الخطأ للمدير في لوحة التحكم، وتعمل الطريقة اليدوية تلقائياً.
		$msg = is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_body( $res );
		update_option( 'zad_wa_api_error', substr( wp_strip_all_tags( (string) $msg ), 0, 300 ), false );
	}
	return $ok;
}

/**
 * تفعيل الحساب.
 *
 * @param int    $user_id المستخدم.
 * @param string $by      admin|code.
 */
function zad_verify_confirm( $user_id, $by = 'admin' ) {
	update_user_meta( $user_id, 'zad_verified', time() );
	update_user_meta( $user_id, 'zad_verified_by', $by );
	delete_user_meta( $user_id, 'zad_verify_code' );
	delete_user_meta( $user_id, 'zad_verify_token' );
	do_action( 'zad_customer_verified', $user_id, $by );
}

/**
 * رابط التفعيل للإدارة (يُرسل داخل رسالة الزبون).
 *
 * @param int $user_id المستخدم.
 * @return string
 */
function zad_verify_admin_url( $user_id ) {
	$token = (string) get_user_meta( $user_id, 'zad_verify_token', true );
	if ( '' === $token ) {
		$token = wp_generate_password( 32, false );
		update_user_meta( $user_id, 'zad_verify_token', $token );
	}
	return add_query_arg(
		array(
			'zad_verify' => $token,
			'u'          => (int) $user_id,
		),
		home_url( '/' )
	);
}

/**
 * رابط واتساب الذي يرسله الزبون إلى المتجر لتأكيد حسابه.
 *
 * @param int $user_id المستخدم.
 * @return string
 */
function zad_verify_customer_wa_link( $user_id ) {
	$p     = zad_customer_profile( $user_id );
	$place = 'other' === $p['district'] ? $p['city'] : zad_district_label( $p['district'], true );
	$lines = array(
		'مرحباً ' . get_bloginfo( 'name' ) . '، أريد تأكيد حساب الجملة:',
		'المحل: ' . $p['shop'],
		'الاسم: ' . $p['name'],
		'واتساب: ' . zad_format_phone( $p['wa'] ),
		'المنطقة: ' . $place,
		'',
		'رابط التفعيل (للإدارة):',
		zad_verify_admin_url( $user_id ),
	);
	return zad_wa_link( implode( "\n", $lines ) );
}

/**
 * رسالة واتساب جاهزة من المتجر إلى الزبون بعد التفعيل.
 *
 * @param int $user_id المستخدم.
 * @return string
 */
function zad_verify_notify_link( $user_id ) {
	$p = zad_customer_profile( $user_id );
	return zad_customer_wa_link(
		$p['wa'],
		sprintf(
			"أهلاً %1\$s، تم تفعيل حساب «%2\$s» في %3\$s ✓\nالأسعار ظاهرة لك الآن، ويمكنك الطلب مباشرة:\n%4\$s",
			$p['name'],
			$p['shop'],
			get_bloginfo( 'name' ),
			function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' )
		)
	);
}

/* -------------------------------------------------------------------------
 * صفحة التفعيل لصاحب المتجر (من الرابط داخل رسالة الزبون)
 * ---------------------------------------------------------------------- */

/**
 * عرض صفحة التفعيل ومعالجتها.
 */
function zad_verify_admin_page() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['zad_verify'] ) || empty( $_GET['u'] ) ) {
		return;
	}
	$token   = sanitize_text_field( wp_unslash( $_GET['zad_verify'] ) );
	$user_id = absint( $_GET['u'] );
	// phpcs:enable
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url( add_query_arg( array( 'zad_verify' => $token, 'u' => $user_id ), home_url( '/' ) ) ) );
		exit;
	}
	nocache_headers();
	$title = 'تفعيل حساب جملة';
	$user  = get_userdata( $user_id );
	if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_options' ) ) {
		zad_verify_render( $title, '<p>هذا الرابط لإدارة المتجر فقط. سيصلك تأكيد على واتساب حين يُفعَّل حسابك.</p>' );
	}
	if ( ! $user ) {
		zad_verify_render( $title, '<p>الحساب غير موجود.</p>' );
	}
	$p    = zad_customer_profile( $user_id );
	$info = sprintf(
		'<ul class="zd-verify__info"><li><b>المحل:</b> %1$s</li><li><b>الاسم:</b> %2$s</li><li><b>واتساب الحساب:</b> <span dir="ltr">%3$s</span></li><li><b>المنطقة:</b> %4$s</li></ul>',
		esc_html( $p['shop'] ),
		esc_html( $p['name'] ),
		esc_html( zad_format_phone( $p['wa'] ) ),
		esc_html( 'other' === $p['district'] ? $p['city'] : zad_district_label( $p['district'], true ) )
	);
	if ( zad_user_verified( $user_id ) ) {
		zad_verify_render( $title, '<p class="zd-verify__ok">الحساب مفعّل ✓</p>' . $info . sprintf( '<p><a class="zd-btn zd-btn--wa" href="%s" target="_blank" rel="noopener">أبلغ الزبون على واتساب</a></p>', esc_url( zad_verify_notify_link( $user_id ) ) ) );
	}
	if ( ! hash_equals( (string) get_user_meta( $user_id, 'zad_verify_token', true ), $token ) ) {
		zad_verify_render( $title, '<p>رابط التفعيل غير صالح أو قديم. فعّل الحساب من «المستخدمون» في لوحة التحكم.</p>' . $info );
	}
	if ( isset( $_POST['zad_do_verify'] ) && check_admin_referer( 'zad_verify_' . $user_id ) ) {
		zad_verify_confirm( $user_id, 'admin' );
		zad_verify_render( $title, '<p class="zd-verify__ok">تم تفعيل الحساب ✓ صارت الأسعار والطلب متاحة له الآن.</p>' . $info . sprintf( '<p><a class="zd-btn zd-btn--wa" href="%s" target="_blank" rel="noopener">أبلغ الزبون على واتساب</a></p>', esc_url( zad_verify_notify_link( $user_id ) ) ) );
	}
	zad_verify_render(
		$title,
		'<p>تأكد أن رسالة التأكيد وصلتك من رقم واتساب الحساب نفسه، ثم اضغط «تفعيل».</p>' . $info
		. '<form method="post">' . wp_nonce_field( 'zad_verify_' . $user_id, '_wpnonce', true, false ) . '<button type="submit" name="zad_do_verify" value="1" class="zd-btn zd-btn--primary">تفعيل الحساب</button></form>'
	);
}
add_action( 'template_redirect', 'zad_verify_admin_page', 1 );

/**
 * صفحة بسيطة مستقلة (بلا قالب الموقع) لعرض نتيجة التفعيل.
 *
 * @param string $title العنوان.
 * @param string $html  المحتوى.
 */
function zad_verify_render( $title, $html ) {
	status_header( 200 );
	header( 'X-Robots-Tag: noindex, nofollow', true );
	?>
	<!doctype html>
	<html <?php language_attributes(); ?> dir="rtl">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow">
		<title><?php echo esc_html( $title . ' | ' . get_bloginfo( 'name' ) ); ?></title>
		<style>
			body{margin:0;background:#F6F6F8;color:#222;font:16px/1.7 Tahoma,Arial,sans-serif}
			.zd-verify{max-width:480px;margin:40px auto;padding:28px 24px;background:#fff;border-radius:16px;box-shadow:0 8px 30px rgba(0,0,0,.06)}
			h1{font-size:22px;margin:0 0 12px}
			.zd-verify__info{list-style:none;padding:12px 16px;margin:16px 0;background:#F6F6F8;border-radius:12px}
			.zd-verify__ok{color:#15803D;font-weight:700}
			.zd-btn{display:inline-block;padding:12px 22px;border:0;border-radius:999px;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}
			.zd-btn--primary{background:#222;color:#fff}.zd-btn--wa{background:#1F8A4C;color:#fff}
		</style>
	</head>
	<body><main class="zd-verify"><h1><?php echo esc_html( $title ); ?></h1><?php echo zad_verify_kses( $html ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">العودة إلى الموقع</a></p></main></body>
	</html>
	<?php
	exit;
}

/**
 * تنظيف محتوى صفحة التفعيل مع السماح بنموذج الزر.
 *
 * @param string $html المحتوى.
 * @return string
 */
function zad_verify_kses( $html ) {
	$allowed           = wp_kses_allowed_html( 'post' );
	$allowed['form']   = array( 'method' => true );
	$allowed['input']  = array(
		'type'  => true,
		'name'  => true,
		'value' => true,
		'id'    => true,
	);
	$allowed['button'] = array(
		'type'  => true,
		'name'  => true,
		'value' => true,
		'class' => true,
	);
	return wp_kses( $html, $allowed );
}

/* -------------------------------------------------------------------------
 * جهة الزبون: صندوق التأكيد في «حسابي»، وإدخال الرمز، وشريط التنبيه
 * ---------------------------------------------------------------------- */

/**
 * التحقق من الرمز المكتوب (الطريقة التلقائية).
 */
function zad_verify_code_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( empty( $_POST['zad_verify_code'] ) || ! is_user_logged_in() ) {
		return;
	}
	if ( ! isset( $_POST['_zadv'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_zadv'] ) ), 'zad_verify_code' ) ) {
		return;
	}
	$uid   = get_current_user_id();
	$typed = preg_replace( '/\D+/', '', strtr( sanitize_text_field( wp_unslash( $_POST['zad_verify_code'] ) ), array_combine( array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ), range( 0, 9 ) ) ) );
	// phpcs:enable
	$tries = (int) get_user_meta( $uid, 'zad_verify_tries', true );
	if ( $tries >= 8 ) {
		wc_add_notice( 'تجاوزت عدد المحاولات. اطلب رمزاً جديداً أو أكّد عبر رسالة واتساب.', 'error' );
		return;
	}
	if ( '' !== $typed && hash_equals( (string) get_user_meta( $uid, 'zad_verify_code', true ), $typed ) ) {
		delete_user_meta( $uid, 'zad_verify_tries' );
		zad_verify_confirm( $uid, 'code' );
		wc_add_notice( 'تم تأكيد حسابك ✓ الأسعار ظاهرة لك الآن.', 'success' );
		wp_safe_redirect( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
		exit;
	}
	update_user_meta( $uid, 'zad_verify_tries', $tries + 1 );
	wc_add_notice( 'الرمز غير صحيح. تأكد من آخر رسالة وصلتك على واتساب.', 'error' );
}
add_action( 'wp_loaded', 'zad_verify_code_submit', 25 );

/**
 * إعادة إرسال الرمز (مرة كل دقيقتين).
 */
function zad_verify_resend() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( empty( $_POST['zad_verify_resend'] ) || ! is_user_logged_in() || ! isset( $_POST['_zadv'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_zadv'] ) ), 'zad_verify_code' ) ) {
		return;
	}
	$uid  = get_current_user_id();
	$last = (int) get_user_meta( $uid, 'zad_verify_sent', true );
	if ( $last && time() - $last < 120 ) {
		wc_add_notice( 'أُرسل الرمز قبل قليل. انتظر دقيقتين قبل طلب رمز جديد.', 'notice' );
		return;
	}
	update_user_meta( $uid, 'zad_verify_code', (string) wp_rand( 100000, 999999 ) );
	delete_user_meta( $uid, 'zad_verify_tries' );
	if ( zad_verify_api_send( $uid ) ) {
		wc_add_notice( 'أرسلنا رمزاً جديداً إلى واتساب.', 'success' );
	} else {
		wc_add_notice( 'تعذّر إرسال الرمز الآن. أكّد حسابك برسالة واتساب من الزر أدناه.', 'error' );
	}
}
add_action( 'wp_loaded', 'zad_verify_resend', 25 );

/**
 * صندوق «أكّد حسابك» (في حسابي، وفي أي مكان يحتاج تأكيداً).
 *
 * @return string
 */
function zad_verify_box() {
	$uid = get_current_user_id();
	if ( ! $uid || zad_user_verified( $uid ) ) {
		return '';
	}
	$sent = (int) get_user_meta( $uid, 'zad_verify_sent', true );
	$html = '<section class="zd-verify-box" id="zd-verify" aria-labelledby="zd-verify-title">'
		. '<span class="zd-verify-box__icon">' . zad_icon( 'whatsapp', '', 28 ) . '</span>'
		. '<div class="zd-verify-box__body"><h2 id="zd-verify-title">خطوة أخيرة: أكّد حسابك عبر واتساب</h2>'
		. '<p>تظهر أسعار الجملة ويُفتح الطلب بعد تأكيد رقم واتساب محلك. هذا يحمي أسعارنا ويضمن وصول الطلبيات إلى المحل الصحيح.</p>';
	if ( zad_verify_api_ready() && $sent ) {
		$html .= '<form method="post" class="zd-verify-box__form">' . wp_nonce_field( 'zad_verify_code', '_zadv', false, false )
			. '<label for="zad_verify_code">اكتب الرمز الذي وصلك على واتساب</label>'
			. '<div class="zd-verify-box__row"><input id="zad_verify_code" name="zad_verify_code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9٠-٩]*" maxlength="6" required dir="ltr">'
			. '<button type="submit" class="zd-btn zd-btn--primary">تأكيد</button></div>'
			. '<button type="submit" name="zad_verify_resend" value="1" formnovalidate class="zd-link-btn">لم يصلك الرمز؟ أرسله مرة أخرى</button></form>'
			. '<p class="zd-verify-box__alt">أو <a href="' . esc_url( zad_verify_customer_wa_link( $uid ) ) . '" target="_blank" rel="noopener">أرسل لنا رسالة تأكيد على واتساب</a>.</p>';
	} else {
		$html .= '<ol class="zd-verify-box__steps"><li>اضغط الزر فتُفتح رسالة جاهزة في واتساب.</li><li>أرسلها كما هي من رقم محلك.</li><li>نفعّل حسابك ونبلغك على واتساب، وغالباً خلال دقائق في أوقات العمل.</li></ol>'
			. '<a class="zd-btn zd-btn--wa zd-verify-box__cta" href="' . esc_url( zad_verify_customer_wa_link( $uid ) ) . '" target="_blank" rel="noopener">' . zad_icon( 'whatsapp', '', 20 ) . '<span>أكّد حسابي عبر واتساب</span></a>';
	}
	return $html . '</div></section>';
}

/**
 * الصندوق أعلى لوحة «حسابي».
 */
function zad_verify_account_box() {
	echo zad_verify_box(); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_account_dashboard', 'zad_verify_account_box', 1 );

/**
 * شريط تنبيه أعلى كل الصفحات للحساب غير المؤكَّد.
 */
function zad_verify_bar() {
	if ( ! is_user_logged_in() || zad_user_verified() || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
		return;
	}
	printf(
		'<div class="zd-verify-bar" role="status"><div class="zd-container"><span>%1$s حسابك بانتظار التأكيد عبر واتساب، وبعده تظهر الأسعار.</span> <a href="%2$s">أكّد الآن</a></div></div>',
		zad_icon( 'whatsapp', '', 18 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_url( ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ) . '#zd-verify' )
	);
}
add_action( 'wp_body_open', 'zad_verify_bar', 6 );

/**
 * لا إتمام للطلبية قبل التأكيد: تبقى الطلبية محفوظة ويُوجَّه الزبون إلى صندوق التأكيد.
 */
function zad_verify_checkout_gate() {
	if ( ! is_user_logged_in() || zad_user_verified() || ! function_exists( 'is_checkout' ) || ! is_checkout() || is_wc_endpoint_url( 'order-received' ) ) {
		return;
	}
	wc_add_notice( 'أكّد حسابك عبر واتساب أولاً، ثم أكمل طلبيتك. طلبيتك محفوظة.', 'notice' );
	wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) . '#zd-verify' );
	exit;
}
add_action( 'template_redirect', 'zad_verify_checkout_gate', 6 );

/* -------------------------------------------------------------------------
 * لوحة التحكم: عمود الحالة وزر التفعيل في «المستخدمون»، وتنبيه خطأ الواجهة البرمجية
 * ---------------------------------------------------------------------- */

add_filter(
	'manage_users_columns',
	static function ( $cols ) {
		if ( zad_verify_enabled() ) {
			$cols['zad_verified'] = 'تأكيد واتساب';
		}
		return $cols;
	}
);

add_filter(
	'manage_users_custom_column',
	static function ( $out, $col, $user_id ) {
		if ( 'zad_verified' !== $col ) {
			return $out;
		}
		if ( zad_user_verified( $user_id ) ) {
			return '<span style="color:#15803D">مؤكَّد ✓</span>';
		}
		return sprintf(
			'<a class="button button-small" href="%s">تفعيل</a>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=zad_verify_user&u=' . (int) $user_id ), 'zad_verify_user_' . (int) $user_id ) )
		);
	},
	10,
	3
);

add_action(
	'admin_post_zad_verify_user',
	static function () {
		$uid = isset( $_GET['u'] ) ? absint( $_GET['u'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $uid || ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( 'غير مسموح.' );
		}
		check_admin_referer( 'zad_verify_user_' . $uid );
		zad_verify_confirm( $uid, 'admin' );
		wp_safe_redirect( admin_url( 'users.php?zad_verified=' . $uid ) );
		exit;
	}
);

add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['zad_verified'] ) ) {
			$uid = absint( $_GET['zad_verified'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			printf(
				'<div class="notice notice-success is-dismissible" dir="rtl"><p>تم تفعيل الحساب ✓ <a class="button" href="%s" target="_blank" rel="noopener">أبلغ الزبون على واتساب</a></p></div>',
				esc_url( zad_verify_notify_link( $uid ) )
			);
		}
		$err = get_option( 'zad_wa_api_error' );
		if ( $err ) {
			printf( '<div class="notice notice-warning" dir="rtl"><p><strong>بسكاتو:</strong> تعذّر إرسال رمز التأكيد عبر WhatsApp Cloud API، فيعمل التأكيد اليدوي برسالة الزبون. السبب: <code>%s</code></p></div>', esc_html( $err ) );
		}
	}
);
