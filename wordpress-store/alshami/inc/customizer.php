<?php
/**
 * إعدادات القالب في «المظهر ← تخصيص».
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

/**
 * تنظيف قيمة منطقية.
 *
 * @param mixed $value القيمة.
 * @return bool
 */
function shami_sanitize_bool( $value ) {
	return (bool) $value;
}

/**
 * تنظيف رقم عشري موجب.
 *
 * @param mixed $value القيمة.
 * @return float
 */
function shami_sanitize_amount( $value ) {
	return max( 0, (float) $value );
}

/**
 * تسجيل الإعدادات.
 *
 * @param WP_Customize_Manager $wp_customize المخصص.
 */
function shami_customize_register( $wp_customize ) {
	$defaults = shami_defaults();

	$wp_customize->add_panel(
		'shami_panel',
		array(
			'title'       => 'إعدادات متجر الشامي',
			'description' => 'بيانات التواصل، الواجهة الرئيسية، إعدادات الطلب والأسعار.',
			'priority'    => 20,
		)
	);

	$sections = array(
		'shami_contact' => 'التواصل وواتساب',
		'shami_home'    => 'الواجهة الرئيسية',
		'shami_shop'    => 'الطلبات والأسعار',
		'shami_social'  => 'حسابات التواصل الاجتماعي',
	);
	$priority = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'shami_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	$fields = array(
		// التواصل.
		'whatsapp'      => array( 'shami_contact', 'text', 'رقم واتساب (بالصيغة الدولية)', 'مثال: 905xxxxxxxxx — رقم قسم المبيعات، تُرسل إليه الطلبيات ورسائل التجار.', 'sanitize_text_field' ),
		'phone'         => array( 'shami_contact', 'text', 'رقم الهاتف', 'يظهر في الترويسة والتذييل وصفحة التواصل.', 'sanitize_text_field' ),
		'email'         => array( 'shami_contact', 'email', 'البريد الإلكتروني للطلبات', 'تصل إليه إشعارات الطلبات الخاصة وطلبات التصدير. إن ترك فارغاً يُستخدم بريد المدير.', 'sanitize_email' ),
		'address'       => array( 'shami_contact', 'textarea', 'العنوان / المستودع', '', 'sanitize_textarea_field' ),
		'city'          => array( 'shami_contact', 'text', 'المدينة الرئيسية للتوريد', 'تُستخدم في نصوص السيو والبيانات المنظمة.', 'sanitize_text_field' ),
		'hours'         => array( 'shami_contact', 'text', 'ساعات العمل', '', 'sanitize_text_field' ),
		// الرئيسية.
		'announcement'  => array( 'shami_home', 'text', 'شريط الإعلان العلوي', 'اتركه فارغاً لإخفائه.', 'sanitize_text_field' ),
		'hero_kicker'   => array( 'shami_home', 'text', 'عنوان صغير فوق العنوان الرئيسي', '', 'sanitize_text_field' ),
		'hero_title'    => array( 'shami_home', 'text', 'العنوان الرئيسي (H1)', 'ضع فيه الكلمة المفتاحية الأهم.', 'sanitize_text_field' ),
		'hero_text'     => array( 'shami_home', 'textarea', 'النص التعريفي', '', 'sanitize_textarea_field' ),
		'seo_tagline'   => array( 'shami_home', 'text', 'الوصف المختصر للسيو (يظهر في عنوان الصفحة الرئيسية)', '', 'sanitize_text_field' ),
		// المتجر.
		'show_prices'   => array( 'shami_shop', 'checkbox', 'إظهار الأسعار للزوار', 'ألغِ التفعيل لعرض «السعر عند الطلب» بدل الأسعار في الكتالوج.', 'shami_sanitize_bool' ),
		'min_order'     => array( 'shami_shop', 'number', 'الحد الأدنى لقيمة الطلب', '0 = بدون حد أدنى.', 'shami_sanitize_amount' ),
		'free_delivery' => array( 'shami_shop', 'number', 'التوصيل مجاني للطلبات فوق', 'للعرض فقط في الواجهة (0 = إخفاء). اضبط طرق الشحن الفعلية من إعدادات ووكومرس.', 'shami_sanitize_amount' ),
		'force_rtl'     => array( 'shami_shop', 'checkbox', 'فرض الاتجاه من اليمين لليسار (عربي)', 'مفيد إذا كانت لغة لوحة التحكم غير العربية.', 'shami_sanitize_bool' ),
		// اجتماعي.
		'instagram'     => array( 'shami_social', 'url', 'Instagram', '', 'esc_url_raw' ),
		'facebook'      => array( 'shami_social', 'url', 'Facebook', '', 'esc_url_raw' ),
		'tiktok'        => array( 'shami_social', 'url', 'TikTok', '', 'esc_url_raw' ),
		'telegram'      => array( 'shami_social', 'url', 'Telegram', '', 'esc_url_raw' ),
		'youtube'       => array( 'shami_social', 'url', 'YouTube', '', 'esc_url_raw' ),
	);

	foreach ( $fields as $key => $field ) {
		list( $section, $type, $label, $description, $sanitize ) = $field;
		$wp_customize->add_setting(
			'shami_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'shami_' . $key,
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
		'shami_hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'shami_hero_image',
			array(
				'section'     => 'shami_home',
				'label'       => 'صورة الواجهة الأولى',
				'description' => 'صورة أفقية بعرض 2400 بكسل على الأقل (مستودعك أو فريقك). اتركها فارغة لاستخدام الصورة الافتراضية.',
			)
		)
	);
}
add_action( 'customize_register', 'shami_customize_register' );
