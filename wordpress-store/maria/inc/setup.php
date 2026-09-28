<?php
/**
 * تهيئة القالب: الدعم، القوائم، الأنماط والسكربتات.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

/**
 * خصائص القالب.
 */
function maria_setup() {
	load_theme_textdomain( 'maria', MARIA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_columns' => 4,
				'default_rows'    => 6,
				'min_columns'     => 2,
				'max_columns'     => 6,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => 'القائمة الرئيسية (روابط الصفحات)',
			'footer'  => 'روابط التذييل',
		)
	);
}
add_action( 'after_setup_theme', 'maria_setup' );

/**
 * عرض المحتوى.
 */
function maria_content_width() {
	$GLOBALS['content_width'] = 1240; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
}
add_action( 'after_setup_theme', 'maria_content_width', 0 );

/**
 * فرض الاتجاه من اليمين لليسار في الواجهة حتى لو كانت لغة الموقع غير العربية.
 */
function maria_force_rtl() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! maria_opt( 'force_rtl' ) ) {
		return;
	}
	global $wp_locale;
	if ( $wp_locale instanceof WP_Locale ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'init', 'maria_force_rtl', 1 );

/**
 * سمات اللغة في وسم html.
 *
 * @param string $output المخرجات.
 * @return string
 */
function maria_language_attributes( $output ) {
	if ( is_admin() || ! maria_opt( 'force_rtl' ) ) {
		return $output;
	}
	return 'dir="rtl" lang="ar"';
}
add_filter( 'language_attributes', 'maria_language_attributes', 20 );

/**
 * الأنماط والسكربتات.
 */
function maria_assets() {
	$css_file = MARIA_DIR . '/assets/css/main.css';
	$js_file  = MARIA_DIR . '/assets/js/main.js';
	$css_ver  = MARIA_VERSION . '.' . ( file_exists( $css_file ) ? filemtime( $css_file ) : '0' );
	$js_ver   = MARIA_VERSION . '.' . ( file_exists( $js_file ) ? filemtime( $js_file ) : '0' );

	// خطوط Google (Alexandria + IBM Plex Sans Arabic) مستضافة داخل القالب: أسرع ولا تعتمد على خدمة خارجية.
	wp_enqueue_style( 'maria-fonts', MARIA_URI . '/assets/css/fonts.css', array(), MARIA_VERSION );
	wp_enqueue_style( 'maria-main', MARIA_URI . '/assets/css/main.css', array( 'maria-fonts' ), $css_ver );

	wp_enqueue_script(
		'maria-main',
		MARIA_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$config = array(
		'home'       => home_url( '/' ),
		'searchUrl'  => esc_url_raw( rest_url( 'maria/v1/search' ) ),
		'popular'    => array( 'بسكريم', 'براوني', 'شيبس', 'ويفر', 'شوكولاتة دبي', 'كراكرز' ),
		'wa'         => maria_wa_number(),
		'storeName'  => get_bloginfo( 'name' ),
		'showPrices' => maria_show_prices(),
		'offerEnd'   => maria_offer_end_iso(),
		'i18n'       => array(
			'added'        => 'تمت الإضافة إلى السلة',
			'updated'      => 'تم تحديث الكمية',
			'removed'      => 'تمت الإزالة من السلة',
			'error'        => 'تعذّر تحديث السلة. تحقق من الاتصال وحاول مرة أخرى',
			'carton'       => 'كرتونة',
			'cartons'      => 'كرتونة',
			'items'        => 'صنف',
			'waIntro'      => 'مرحباً 👋 أرغب بطلب المنتجات التالية:',
			'waTotal'      => 'الإجمالي التقديري',
			'waName'       => 'الاسم / اسم البقالة:',
			'waAddress'    => 'العنوان:',
			'empty'        => 'لم تحدّد أي صنف بعد. اضغط + بجانب الصنف لإضافة الكمية',
			'confirmClear' => 'هل تريد تفريغ جميع الكميات من السلة؟',
			'noResults'    => 'لا توجد نتائج مطابقة. جرّب كلمة أقصر، أو اطلب المنتج من صفحة «اطلب منتجاً غير متوفر»',
			'searching'    => 'جارِ البحث…',
			'viewAll'      => 'عرض كل النتائج',
			'copied'       => 'تم نسخ الرابط ✓',
			'days'         => 'يوم',
			'hours'        => 'ساعة',
			'minutes'      => 'دقيقة',
			'seconds'      => 'ثانية',
			'row'          => 'منتج',
			'companies'    => 'الشركات',
			'sections'     => 'الأقسام',
			'products'     => 'المنتجات',
			'popular'      => 'يبحث عنها أصحاب البقالات',
			'add'          => 'أضف',
			'orderNow'     => 'أكمل الطلب',
		),
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$config['syncUrl']     = WC_AJAX::get_endpoint( 'maria_sync_cart' );
		$config['cart']        = (object) maria_cart_qty_cached();
		$config['cartUrl']     = wc_get_cart_url();
		$config['checkoutUrl'] = wc_get_checkout_url();
		$config['currency']    = array(
			'symbol' => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
			'pos'    => get_option( 'woocommerce_currency_pos', 'right_space' ),
			'dec'    => wc_get_price_decimals(),
			'ds'     => wc_get_price_decimal_separator(),
			'ts'     => wc_get_price_thousand_separator(),
		);
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	wp_add_inline_script( 'maria-main', 'window.MARIA=' . wp_json_encode( $config ) . ';', 'before' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'maria_assets', 20 );

/**
 * تحميل مسبق لملفي الخط العربي الأساسيين لتسريع ظهور النصوص.
 */
function maria_preload_fonts() {
	foreach ( array( 'tajawal-400-arabic.woff2', 'tajawal-800-arabic.woff2', 'aref-ruqaa-700-arabic.woff2' ) as $file ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( MARIA_URI . '/assets/fonts/' . $file ) );
	}
}
add_action( 'wp_head', 'maria_preload_fonts', 1 );

/**
 * تعطيل سكربت الإيموجي لتسريع التحميل.
 */
function maria_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'maria_disable_emojis' );

/**
 * كلاسات body.
 *
 * @param array $classes الكلاسات.
 * @return array
 */
function maria_body_class( $classes ) {
	$classes[] = 'mr';
	if ( ! maria_show_prices() ) {
		$classes[] = 'mr-prices-hidden';
	}
	if ( is_page_template( 'page-templates/template-quick-order.php' ) ) {
		$classes[] = 'mr-quick-order-page';
	}
	return $classes;
}
add_filter( 'body_class', 'maria_body_class' );

/**
 * تنبيه في حال عدم تفعيل ووكومرس.
 */
function maria_wc_missing_notice() {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>قالب ماريا:</strong> يحتاج هذا القالب إلى إضافة <strong>WooCommerce</strong>. ثبّتها وفعّلها من صفحة الإضافات ثم افتح «المظهر ← إعداد متجر ماريا».</p></div>';
}
add_action( 'admin_notices', 'maria_wc_missing_notice' );

/**
 * طول المقتطف.
 *
 * @return int
 */
function maria_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'maria_excerpt_length' );

/**
 * تسميات قائمة افتراضية في حال عدم إنشاء قائمة.
 */
function maria_fallback_menu() {
	$links = array(
		maria_page_url( 'quick_order' )     => 'الطلب السريع',
		maria_page_url( 'special_request' ) => 'اطلب منتجاً غير متوفر',
		maria_page_url( 'export' )          => 'الجملة خارج تركيا',
		maria_page_url( 'contact' )         => 'تواصل معنا',
	);
	echo '<ul class="mr-menu">';
	foreach ( $links as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * علامة تفعيل JavaScript مبكراً (لتجنب وميض عناصر الحركة).
 */
function maria_js_class() {
	echo "<script>document.documentElement.classList.add('mr-js');</script>\n";
}
add_action( 'wp_head', 'maria_js_class', 0 );

/**
 * أيقونة الموقع من رمز الشعار (إن لم يرفع المدير أيقونة) + لون شريط المتصفح في الجوال.
 */
function maria_head_icons() {
	echo '<meta name="theme-color" content="#FFFBF6">' . "\n";
	if ( has_site_icon() ) {
		return;
	}
	$svg = str_replace( '<svg class="mr-logo__mark"', '<svg xmlns="http://www.w3.org/2000/svg"', maria_logo_mark() );
	echo '<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,' . rawurlencode( $svg ) . '">' . "\n";
}
add_action( 'wp_head', 'maria_head_icons', 2 );
