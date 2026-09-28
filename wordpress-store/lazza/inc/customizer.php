<?php
/**
 * إعدادات القالب في «المظهر ← تخصيص».
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

/**
 * تنظيف قيمة منطقية.
 *
 * @param mixed $value القيمة.
 * @return bool
 */
function lazza_sanitize_bool( $value ) {
	return (bool) $value;
}

/**
 * تنظيف رقم عشري موجب.
 *
 * @param mixed $value القيمة.
 * @return float
 */
function lazza_sanitize_amount( $value ) {
	return max( 0, (float) $value );
}

/**
 * تسجيل الإعدادات.
 *
 * @param WP_Customize_Manager $wp_customize المخصص.
 */
function lazza_customize_register( $wp_customize ) {
	$defaults = lazza_defaults();

	$wp_customize->add_panel(
		'lazza_panel',
		array(
			'title'       => 'إعدادات متجر لذّة',
			'description' => 'بيانات التواصل، الواجهة الرئيسية، إعدادات الطلب والأسعار.',
			'priority'    => 20,
		)
	);

	$sections = array(
		'lazza_contact' => 'التواصل وواتساب',
		'lazza_home'    => 'الواجهة الرئيسية',
		'lazza_shop'    => 'الطلبات والأسعار',
		'lazza_social'  => 'حسابات التواصل الاجتماعي',
	);
	$priority = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'lazza_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	$fields = array(
		// التواصل.
		'whatsapp'      => array( 'lazza_contact', 'text', 'رقم واتساب (بالصيغة الدولية)', 'مثال: 905xxxxxxxxx — تُرسل إليه الطلبات السريعة ورسائل العملاء.', 'sanitize_text_field' ),
		'phone'         => array( 'lazza_contact', 'text', 'رقم الهاتف', 'يظهر في الترويسة والتذييل وصفحة التواصل.', 'sanitize_text_field' ),
		'email'         => array( 'lazza_contact', 'email', 'البريد الإلكتروني للطلبات', 'تصل إليه إشعارات الطلبات الخاصة وطلبات التصدير. إن ترك فارغاً يُستخدم بريد المدير.', 'sanitize_email' ),
		'address'       => array( 'lazza_contact', 'textarea', 'العنوان / المستودع', '', 'sanitize_textarea_field' ),
		'city'          => array( 'lazza_contact', 'text', 'المدينة الرئيسية للتوصيل', 'تُستخدم في نصوص السيو والبيانات المنظمة.', 'sanitize_text_field' ),
		'hours'         => array( 'lazza_contact', 'text', 'ساعات العمل', '', 'sanitize_text_field' ),
		// الرئيسية.
		'announcement'  => array( 'lazza_home', 'text', 'شريط الإعلان العلوي', 'اتركه فارغاً لإخفائه.', 'sanitize_text_field' ),
		'hero_kicker'   => array( 'lazza_home', 'text', 'عنوان صغير فوق العنوان الرئيسي', '', 'sanitize_text_field' ),
		'hero_title'    => array( 'lazza_home', 'text', 'العنوان الرئيسي (H1)', 'ضع فيه الكلمة المفتاحية الأهم.', 'sanitize_text_field' ),
		'hero_text'     => array( 'lazza_home', 'textarea', 'النص التعريفي', '', 'sanitize_textarea_field' ),
		'seo_tagline'   => array( 'lazza_home', 'text', 'الوصف المختصر للسيو (يظهر في عنوان الصفحة الرئيسية)', '', 'sanitize_text_field' ),
		'offer_end'     => array( 'lazza_home', 'text', 'تاريخ انتهاء العروض (للعداد التنازلي)', 'مثال: 2026-12-31 23:59 — اتركه فارغاً ليتجدد أسبوعياً تلقائياً (نهاية الخميس).', 'sanitize_text_field' ),
		// المتجر.
		'show_prices'   => array( 'lazza_shop', 'checkbox', 'إظهار الأسعار للزوار', 'ألغِ التفعيل لعرض «السعر عند الطلب» بدل الأسعار في الكتالوج.', 'lazza_sanitize_bool' ),
		'min_order'     => array( 'lazza_shop', 'number', 'الحد الأدنى لقيمة الطلب', '0 = بدون حد أدنى.', 'lazza_sanitize_amount' ),
		'free_delivery' => array( 'lazza_shop', 'number', 'التوصيل مجاني للطلبات فوق', 'للعرض فقط في الواجهة (0 = إخفاء). اضبط طرق الشحن الفعلية من إعدادات ووكومرس.', 'lazza_sanitize_amount' ),
		'force_rtl'     => array( 'lazza_shop', 'checkbox', 'فرض الاتجاه من اليمين لليسار (عربي)', 'مفيد إذا كانت لغة لوحة التحكم غير العربية.', 'lazza_sanitize_bool' ),
		// اجتماعي.
		'instagram'     => array( 'lazza_social', 'url', 'Instagram', '', 'esc_url_raw' ),
		'facebook'      => array( 'lazza_social', 'url', 'Facebook', '', 'esc_url_raw' ),
		'tiktok'        => array( 'lazza_social', 'url', 'TikTok', '', 'esc_url_raw' ),
		'telegram'      => array( 'lazza_social', 'url', 'Telegram', '', 'esc_url_raw' ),
		'youtube'       => array( 'lazza_social', 'url', 'YouTube', '', 'esc_url_raw' ),
	);

	foreach ( $fields as $key => $field ) {
		list( $section, $type, $label, $description, $sanitize ) = $field;
		$wp_customize->add_setting(
			'lazza_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'lazza_' . $key,
			array(
				'section'     => $section,
				'type'        => $type,
				'label'       => $label,
				'description' => $description,
			)
		);
	}
}
add_action( 'customize_register', 'lazza_customize_register' );
