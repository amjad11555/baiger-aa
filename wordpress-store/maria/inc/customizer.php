<?php
/**
 * إعدادات القالب في «المظهر ← تخصيص».
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

/**
 * تنظيف قيمة منطقية.
 *
 * @param mixed $value القيمة.
 * @return bool
 */
function maria_sanitize_bool( $value ) {
	return (bool) $value;
}

/**
 * تنظيف رقم عشري موجب.
 *
 * @param mixed $value القيمة.
 * @return float
 */
function maria_sanitize_amount( $value ) {
	return max( 0, (float) $value );
}

/**
 * تسجيل الإعدادات.
 *
 * @param WP_Customize_Manager $wp_customize المخصص.
 */
function maria_customize_register( $wp_customize ) {
	$defaults = maria_defaults();

	$wp_customize->add_panel(
		'maria_panel',
		array(
			'title'       => 'إعدادات متجر ماريا',
			'description' => 'بيانات التواصل، الواجهة الرئيسية، إعدادات الطلب والأسعار.',
			'priority'    => 20,
		)
	);

	$sections = array(
		'maria_contact' => 'التواصل وواتساب',
		'maria_home'    => 'الواجهة الرئيسية',
		'maria_shop'    => 'الطلبات والأسعار',
		'maria_social'  => 'حسابات التواصل الاجتماعي',
	);
	$priority = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'maria_panel',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	$fields = array(
		// التواصل.
		'whatsapp'      => array( 'maria_contact', 'text', 'رقم واتساب (بالصيغة الدولية)', 'مثال: 905xxxxxxxxx — تُرسل إليه الطلبات السريعة ورسائل العملاء.', 'sanitize_text_field' ),
		'phone'         => array( 'maria_contact', 'text', 'رقم الهاتف', 'يظهر في الترويسة والتذييل وصفحة التواصل.', 'sanitize_text_field' ),
		'email'         => array( 'maria_contact', 'email', 'البريد الإلكتروني للطلبات', 'تصل إليه إشعارات الطلبات الخاصة وطلبات التصدير. إن ترك فارغاً يُستخدم بريد المدير.', 'sanitize_email' ),
		'address'       => array( 'maria_contact', 'textarea', 'العنوان / المستودع', '', 'sanitize_textarea_field' ),
		'city'          => array( 'maria_contact', 'text', 'المدينة الرئيسية للتوصيل', 'تُستخدم في نصوص السيو والبيانات المنظمة.', 'sanitize_text_field' ),
		'hours'         => array( 'maria_contact', 'text', 'ساعات العمل', '', 'sanitize_text_field' ),
		// الرئيسية.
		'announcement'  => array( 'maria_home', 'text', 'شريط الإعلان العلوي', 'اتركه فارغاً لإخفائه.', 'sanitize_text_field' ),
		'hero_kicker'   => array( 'maria_home', 'text', 'عنوان صغير فوق العنوان الرئيسي', '', 'sanitize_text_field' ),
		'hero_title'    => array( 'maria_home', 'text', 'العنوان الرئيسي (H1)', 'ضع فيه الكلمة المفتاحية الأهم.', 'sanitize_text_field' ),
		'hero_text'     => array( 'maria_home', 'textarea', 'النص التعريفي', '', 'sanitize_textarea_field' ),
		'seo_tagline'   => array( 'maria_home', 'text', 'الوصف المختصر للسيو (يظهر في عنوان الصفحة الرئيسية)', '', 'sanitize_text_field' ),
		'offer_end'     => array( 'maria_home', 'text', 'تاريخ انتهاء العروض (للعداد التنازلي)', 'مثال: 2026-12-31 23:59 — اتركه فارغاً ليتجدد أسبوعياً تلقائياً (نهاية الخميس).', 'sanitize_text_field' ),
		// المتجر.
		'show_prices'   => array( 'maria_shop', 'checkbox', 'إظهار الأسعار للزوار', 'ألغِ التفعيل لعرض «السعر عند الطلب» بدل الأسعار في الكتالوج.', 'maria_sanitize_bool' ),
		'min_order'     => array( 'maria_shop', 'number', 'الحد الأدنى لقيمة الطلب', '0 = بدون حد أدنى.', 'maria_sanitize_amount' ),
		'free_delivery' => array( 'maria_shop', 'number', 'التوصيل مجاني للطلبات فوق', 'للعرض فقط في الواجهة (0 = إخفاء). اضبط طرق الشحن الفعلية من إعدادات ووكومرس.', 'maria_sanitize_amount' ),
		'force_rtl'     => array( 'maria_shop', 'checkbox', 'فرض الاتجاه من اليمين لليسار (عربي)', 'مفيد إذا كانت لغة لوحة التحكم غير العربية.', 'maria_sanitize_bool' ),
		// اجتماعي.
		'instagram'     => array( 'maria_social', 'url', 'Instagram', '', 'esc_url_raw' ),
		'facebook'      => array( 'maria_social', 'url', 'Facebook', '', 'esc_url_raw' ),
		'tiktok'        => array( 'maria_social', 'url', 'TikTok', '', 'esc_url_raw' ),
		'telegram'      => array( 'maria_social', 'url', 'Telegram', '', 'esc_url_raw' ),
		'youtube'       => array( 'maria_social', 'url', 'YouTube', '', 'esc_url_raw' ),
	);

	foreach ( $fields as $key => $field ) {
		list( $section, $type, $label, $description, $sanitize ) = $field;
		$wp_customize->add_setting(
			'maria_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'maria_' . $key,
			array(
				'section'     => $section,
				'type'        => $type,
				'label'       => $label,
				'description' => $description,
			)
		);
	}

	// بنرات الرئيسية: صورة + رابط لكل شريحة (تحل محل الشرائح المصممة مسبقاً عند رفعها).
	$wp_customize->add_section(
		'maria_banners',
		array(
			'title'       => 'بنرات الرئيسية',
			'description' => 'ارفع صور عروضك (يفضّل 1600×900 للكمبيوتر أو مربعة 1080×1080 للجوال). إن تركتها فارغة تظهر بنرات جاهزة بألوان المتجر.',
			'panel'       => 'maria_panel',
			'priority'    => 15,
		)
	);
	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting(
			'maria_banner_' . $i,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'maria_banner_' . $i,
				array(
					'section' => 'maria_banners',
					/* translators: %d: رقم البنر. */
					'label'   => sprintf( 'صورة البنر %d', $i ),
				)
			)
		);
		$wp_customize->add_setting(
			'maria_banner_' . $i . '_link',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'maria_banner_' . $i . '_link',
			array(
				'section' => 'maria_banners',
				'type'    => 'url',
				/* translators: %d: رقم البنر. */
				'label'   => sprintf( 'رابط البنر %d', $i ),
			)
		);
	}
}
add_action( 'customize_register', 'maria_customize_register' );
