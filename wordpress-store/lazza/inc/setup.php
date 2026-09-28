<?php
/**
 * تهيئة القالب: الدعم، القوائم، الأنماط والسكربتات.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

/**
 * خصائص القالب.
 */
function lazza_setup() {
	load_theme_textdomain( 'lazza', LAZZA_DIR . '/languages' );

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
add_action( 'after_setup_theme', 'lazza_setup' );

/**
 * عرض المحتوى.
 */
function lazza_content_width() {
	$GLOBALS['content_width'] = 1240; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
}
add_action( 'after_setup_theme', 'lazza_content_width', 0 );

/**
 * فرض الاتجاه من اليمين لليسار في الواجهة حتى لو كانت لغة الموقع غير العربية.
 */
function lazza_force_rtl() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! lazza_opt( 'force_rtl' ) ) {
		return;
	}
	global $wp_locale;
	if ( $wp_locale instanceof WP_Locale ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'init', 'lazza_force_rtl', 1 );

/**
 * سمات اللغة في وسم html.
 *
 * @param string $output المخرجات.
 * @return string
 */
function lazza_language_attributes( $output ) {
	if ( is_admin() || ! lazza_opt( 'force_rtl' ) ) {
		return $output;
	}
	return 'dir="rtl" lang="ar"';
}
add_filter( 'language_attributes', 'lazza_language_attributes', 20 );

/**
 * الأنماط والسكربتات.
 */
function lazza_assets() {
	$css_file = LAZZA_DIR . '/assets/css/main.css';
	$js_file  = LAZZA_DIR . '/assets/js/main.js';
	$css_ver  = LAZZA_VERSION . '.' . ( file_exists( $css_file ) ? filemtime( $css_file ) : '0' );
	$js_ver   = LAZZA_VERSION . '.' . ( file_exists( $js_file ) ? filemtime( $js_file ) : '0' );

	wp_enqueue_style( 'lazza-fonts', 'https://fonts.googleapis.com/css2?family=Lalezar&family=Rubik:wght@400;500;600;700;800;900&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'lazza-main', LAZZA_URI . '/assets/css/main.css', array( 'lazza-fonts' ), $css_ver );

	wp_enqueue_script(
		'lazza-main',
		LAZZA_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$config = array(
		'home'       => home_url( '/' ),
		'searchUrl'  => esc_url_raw( rest_url( 'lazza/v1/search' ) ),
		'wa'         => lazza_wa_number(),
		'storeName'  => get_bloginfo( 'name' ),
		'showPrices' => lazza_show_prices(),
		'offerEnd'   => lazza_offer_end_iso(),
		'i18n'       => array(
			'added'        => 'تمت الإضافة إلى السلة',
			'updated'      => 'تم تحديث الكمية',
			'removed'      => 'تمت الإزالة من السلة',
			'error'        => 'تعذّر تحديث السلة، حاول مرة أخرى',
			'carton'       => 'كرتونة',
			'cartons'      => 'كرتونة',
			'items'        => 'صنف',
			'waIntro'      => 'مرحباً 👋 أرغب بطلب المنتجات التالية:',
			'waTotal'      => 'الإجمالي التقديري',
			'waName'       => 'الاسم / اسم البقالة:',
			'waAddress'    => 'العنوان:',
			'empty'        => 'لم تحدّد أي منتج بعد — اضغط + لإضافة الكميات',
			'confirmClear' => 'هل تريد تفريغ جميع الكميات من السلة؟',
			'noResults'    => 'لا توجد نتائج مطابقة — يمكنك طلب المنتج من صفحة «اطلب منتجاً غير متوفر»',
			'searching'    => 'جارِ البحث…',
			'viewAll'      => 'عرض كل النتائج',
			'copied'       => 'تم نسخ الرابط ✓',
			'days'         => 'يوم',
			'hours'        => 'ساعة',
			'minutes'      => 'دقيقة',
			'seconds'      => 'ثانية',
			'row'          => 'منتج',
		),
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$config['syncUrl']     = WC_AJAX::get_endpoint( 'lazza_sync_cart' );
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

	wp_add_inline_script( 'lazza-main', 'window.LAZZA=' . wp_json_encode( $config ) . ';', 'before' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'lazza_assets', 20 );

/**
 * الاتصال المسبق بخطوط جوجل.
 *
 * @param array  $urls روابط.
 * @param string $relation نوع العلاقة.
 * @return array
 */
function lazza_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'lazza_resource_hints', 10, 2 );

/**
 * تعطيل سكربت الإيموجي لتسريع التحميل.
 */
function lazza_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'lazza_disable_emojis' );

/**
 * كلاسات body.
 *
 * @param array $classes الكلاسات.
 * @return array
 */
function lazza_body_class( $classes ) {
	$classes[] = 'lz';
	if ( ! lazza_show_prices() ) {
		$classes[] = 'lz-prices-hidden';
	}
	if ( is_page_template( 'page-templates/template-quick-order.php' ) ) {
		$classes[] = 'lz-quick-order-page';
	}
	return $classes;
}
add_filter( 'body_class', 'lazza_body_class' );

/**
 * تنبيه في حال عدم تفعيل ووكومرس.
 */
function lazza_wc_missing_notice() {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>قالب لذّة:</strong> يحتاج هذا القالب إلى إضافة <strong>WooCommerce</strong>. ثبّتها وفعّلها من صفحة الإضافات ثم افتح «المظهر ← إعداد متجر لذّة».</p></div>';
}
add_action( 'admin_notices', 'lazza_wc_missing_notice' );

/**
 * طول المقتطف.
 *
 * @return int
 */
function lazza_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'lazza_excerpt_length' );

/**
 * تسميات قائمة افتراضية في حال عدم إنشاء قائمة.
 */
function lazza_fallback_menu() {
	$links = array(
		lazza_page_url( 'quick_order' )     => 'الطلب السريع',
		lazza_page_url( 'special_request' ) => 'اطلب منتجاً غير متوفر',
		lazza_page_url( 'export' )          => 'الجملة خارج تركيا',
		lazza_page_url( 'contact' )         => 'تواصل معنا',
	);
	echo '<ul class="lz-menu">';
	foreach ( $links as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * مسار REST للبحث الحي.
 */
function lazza_register_rest() {
	register_rest_route(
		'lazza/v1',
		'/search',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
			'callback'            => 'lazza_rest_search',
		)
	);
}
add_action( 'rest_api_init', 'lazza_register_rest' );

/**
 * نتائج البحث الحي.
 *
 * @param WP_REST_Request $request الطلب.
 * @return WP_REST_Response
 */
function lazza_rest_search( $request ) {
	$q = trim( (string) $request->get_param( 'q' ) );
	if ( mb_strlen( $q ) < 2 || ! function_exists( 'wc_get_product' ) ) {
		return rest_ensure_response( array( 'items' => array() ) );
	}
	$query = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $q,
			'posts_per_page' => 8,
			'no_found_rows'  => false,
		)
	);
	$items = array();
	foreach ( $query->posts as $post ) {
		$product = wc_get_product( $post );
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}
		$info    = lazza_product_info( $product->get_id() );
		$brands  = lazza_brands();
		$items[] = array(
			'name'  => $product->get_name(),
			'url'   => get_permalink( $product->get_id() ),
			'price' => lazza_show_prices() ? wp_strip_all_tags( $product->get_price_html() ) : '',
			'brand' => isset( $brands[ $info['brand'] ] ) ? $brands[ $info['brand'] ]['ar'] : '',
			'pack'  => $info['pack'],
			'art'   => lazza_product_art( $product, 'mini' ),
		);
	}
	return rest_ensure_response(
		array(
			'items' => $items,
			'total' => (int) $query->found_posts,
			'url'   => add_query_arg(
				array(
					's'         => $q,
					'post_type' => 'product',
				),
				home_url( '/' )
			),
		)
	);
}

/**
 * علامة تفعيل JavaScript مبكراً (لتجنب وميض عناصر الحركة).
 */
function lazza_js_class() {
	echo "<script>document.documentElement.classList.add('lz-js');</script>\n";
}
add_action( 'wp_head', 'lazza_js_class', 0 );
