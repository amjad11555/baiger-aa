<?php
/**
 * إعدادات القالب في «المظهر ← تخصيص».
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * تنظيف قيمة منطقية.
 *
 * @param mixed $value القيمة.
 * @return bool
 */
function zad_sanitize_bool( $value ) {
	return (bool) $value;
}

/**
 * تنظيف رقم عشري موجب.
 *
 * @param mixed $value القيمة.
 * @return float
 */
function zad_sanitize_amount( $value ) {
	return max( 0, (float) $value );
}

/**
 * تسجيل الإعدادات.
 *
 * @param WP_Customize_Manager $wp_customize المخصص.
 */
function zad_customize_register( $wp_customize ) {
	$defaults = zad_defaults();

	$wp_customize->add_panel(
		'zad_panel',
		array(
			'title'       => 'إعدادات متجر زاد',
			'description' => 'بيانات التواصل، الواجهة الرئيسية، إعدادات الطلب والأسعار.',
			'priority'    => 20,
		)
	);

	$sections = array(
		'zad_contact' => 'التواصل وواتساب',
		'zad_home'    => 'الواجهة الرئيسية',
		'zad_shop'    => 'الطلبات والأسعار',
		'zad_social'  => 'حسابات التواصل الاجتماعي',
	);
	$priority = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'zad_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	$fields = array(
		// التواصل.
		'whatsapp'      => array( 'zad_contact', 'text', 'رقم واتساب (بالصيغة الدولية)', 'مثال: 905xxxxxxxxx — رقم قسم المبيعات، تُرسل إليه الطلبيات ورسائل التجار.', 'sanitize_text_field' ),
		'phone'         => array( 'zad_contact', 'text', 'رقم الهاتف', 'يظهر في الترويسة والتذييل وصفحة التواصل.', 'sanitize_text_field' ),
		'email'         => array( 'zad_contact', 'email', 'البريد الإلكتروني للطلبات', 'تصل إليه إشعارات الطلبات الخاصة وطلبات التصدير. إن ترك فارغاً يُستخدم بريد المدير.', 'sanitize_email' ),
		'address'       => array( 'zad_contact', 'textarea', 'العنوان / المستودع', '', 'sanitize_textarea_field' ),
		'city'          => array( 'zad_contact', 'text', 'المدينة الرئيسية للتوريد', 'تُستخدم في نصوص السيو والبيانات المنظمة.', 'sanitize_text_field' ),
		'hours'         => array( 'zad_contact', 'text', 'ساعات العمل', '', 'sanitize_text_field' ),
		// الرئيسية.
		'announcement'  => array( 'zad_home', 'text', 'شريط الإعلان العلوي', 'اتركه فارغاً لإخفائه.', 'sanitize_text_field' ),
		'hero_kicker'   => array( 'zad_home', 'text', 'عنوان صغير فوق العنوان الرئيسي', '', 'sanitize_text_field' ),
		'hero_title'    => array( 'zad_home', 'text', 'العنوان الرئيسي (H1)', 'ضع فيه الكلمة المفتاحية الأهم.', 'sanitize_text_field' ),
		'hero_text'     => array( 'zad_home', 'textarea', 'النص التعريفي', '', 'sanitize_textarea_field' ),
		'seo_tagline'   => array( 'zad_home', 'text', 'الوصف المختصر للسيو (يظهر في عنوان الصفحة الرئيسية)', '', 'sanitize_text_field' ),
		// المتجر.
		'show_prices'   => array( 'zad_shop', 'checkbox', 'إظهار الأسعار للزوار', 'ألغِ التفعيل لعرض «السعر عند الطلب» بدل الأسعار في الكتالوج.', 'zad_sanitize_bool' ),
		'min_order'     => array( 'zad_shop', 'number', 'الحد الأدنى لقيمة الطلب', '0 = بدون حد أدنى.', 'zad_sanitize_amount' ),
		'free_delivery' => array( 'zad_shop', 'number', 'التوصيل مجاني للطلبات فوق', 'للعرض فقط في الواجهة (0 = إخفاء). اضبط طرق الشحن الفعلية من إعدادات ووكومرس.', 'zad_sanitize_amount' ),
		'force_rtl'     => array( 'zad_shop', 'checkbox', 'فرض الاتجاه من اليمين لليسار (عربي)', 'مفيد إذا كانت لغة لوحة التحكم غير العربية.', 'zad_sanitize_bool' ),
		// اجتماعي.
		'instagram'     => array( 'zad_social', 'url', 'Instagram', '', 'esc_url_raw' ),
		'facebook'      => array( 'zad_social', 'url', 'Facebook', '', 'esc_url_raw' ),
		'tiktok'        => array( 'zad_social', 'url', 'TikTok', '', 'esc_url_raw' ),
		'telegram'      => array( 'zad_social', 'url', 'Telegram', '', 'esc_url_raw' ),
		'youtube'       => array( 'zad_social', 'url', 'YouTube', '', 'esc_url_raw' ),
	);

	foreach ( $fields as $key => $field ) {
		list( $section, $type, $label, $description, $sanitize ) = $field;
		$wp_customize->add_setting(
			'zad_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'zad_' . $key,
			array(
				'section'     => $section,
				'type'        => $type,
				'label'       => $label,
				'description' => $description,
			)
		);
	}

	// صورة الواجهة الأولى: تحل محل صورة المستودع الافتراضية عند رفعها.
	$wp_customize->add_setting(
		'zad_hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'zad_hero_image',
			array(
				'section'     => 'zad_home',
				'label'       => 'صورة الواجهة الأولى',
				'description' => 'صورة أفقية بعرض 2400 بكسل على الأقل (مستودعك أو فريقك). اتركها فارغة لاستخدام الصورة الافتراضية.',
			)
		)
	);
}
add_action( 'customize_register', 'zad_customize_register' );
