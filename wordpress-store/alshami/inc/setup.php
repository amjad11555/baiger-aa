<?php
/**
 * تهيئة القالب: الدعم، القوائم، الأنماط والسكربتات.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

/**
 * خصائص القالب.
 */
function shami_setup() {
	load_theme_textdomain( 'alshami', SHAMI_DIR . '/languages' );

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
add_action( 'after_setup_theme', 'shami_setup' );

/**
 * عرض المحتوى.
 */
function shami_content_width() {
	$GLOBALS['content_width'] = 1240; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
}
add_action( 'after_setup_theme', 'shami_content_width', 0 );

/**
 * فرض الاتجاه من اليمين لليسار في الواجهة حتى لو كانت لغة الموقع غير العربية.
 */
function shami_force_rtl() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! shami_opt( 'force_rtl' ) ) {
		return;
	}
	global $wp_locale;
	if ( $wp_locale instanceof WP_Locale ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'init', 'shami_force_rtl', 1 );

/**
 * سمات اللغة في وسم html.
 *
 * @param string $output المخرجات.
 * @return string
 */
function shami_language_attributes( $output ) {
	if ( is_admin() || ! shami_opt( 'force_rtl' ) ) {
		return $output;
	}
	return 'dir="rtl" lang="ar"';
}
add_filter( 'language_attributes', 'shami_language_attributes', 20 );

/**
 * الأنماط والسكربتات.
 */
function shami_assets() {
	$css_file = SHAMI_DIR . '/assets/css/main.css';
	$js_file  = SHAMI_DIR . '/assets/js/main.js';
	$css_ver  = SHAMI_VERSION . '.' . ( file_exists( $css_file ) ? filemtime( $css_file ) : '0' );
	$js_ver   = SHAMI_VERSION . '.' . ( file_exists( $js_file ) ? filemtime( $js_file ) : '0' );

	// خطوط Google (Alexandria + IBM Plex Sans Arabic) مستضافة داخل القالب: أسرع ولا تعتمد على خدمة خارجية.
	wp_enqueue_style( 'shami-fonts', SHAMI_URI . '/assets/css/fonts.css', array(), SHAMI_VERSION );
	wp_enqueue_style( 'shami-main', SHAMI_URI . '/assets/css/main.css', array( 'shami-fonts' ), $css_ver );

	wp_enqueue_script(
		'shami-main',
		SHAMI_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$config = array(
		'home'       => home_url( '/' ),
		'searchUrl'  => esc_url_raw( rest_url( 'alshami/v1/search' ) ),
		'popular'    => array( 'بسكريم', 'براوني', 'شيبس', 'ويفر', 'شوكولاتة دبي', 'كراكرز' ),
		'wa'         => shami_wa_number(),
		'storeName'  => get_bloginfo( 'name' ),
		'showPrices' => shami_show_prices(),
		'i18n'       => array(
			'added'        => 'أُضيف إلى الطلبية',
			'updated'      => 'تم تحديث الكمية',
			'removed'      => 'أُزيل من الطلبية',
			'error'        => 'تعذّر تحديث الطلبية. تحقق من الاتصال وحاول مرة أخرى',
			'carton'       => 'كرتونة',
			'cartons'      => 'كرتونة',
			'items'        => 'صنف',
			'waIntro'      => 'مرحباً، أرغب بطلب الأصناف التالية بالجملة:',
			'waTotal'      => 'الإجمالي التقديري',
			'waName'       => 'اسم المتجر / الشركة:',
			'waAddress'    => 'العنوان:',
			'empty'        => 'لم تحدّد أي صنف بعد. اضغط + بجانب الصنف لإضافة الكمية',
			'confirmClear' => 'هل تريد تفريغ جميع الكميات من الطلبية؟',
			'noResults'    => 'لا توجد نتائج مطابقة. جرّب كلمة أقصر أو اسم العلامة، أو أرسل طلب توريد خاص',
			'searching'    => 'جارِ البحث…',
			'viewAll'      => 'عرض كل النتائج',
			'copied'       => 'تم نسخ الرابط ✓',
			'row'          => 'منتج',
			'companies'    => 'العلامات',
			'sections'     => 'الأقسام',
			'products'     => 'الأصناف',
			'popular'      => 'الأكثر بحثاً لدى التجار',
			'add'          => 'أضف',
			'orderNow'     => 'إتمام الطلب',
		),
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$config['syncUrl']     = WC_AJAX::get_endpoint( 'shami_sync_cart' );
		$config['cart']        = (object) shami_cart_qty_cached();
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

	wp_add_inline_script( 'shami-main', 'window.SHAMI=' . wp_json_encode( $config ) . ';', 'before' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'shami_assets', 20 );

/**
 * تحميل مسبق لملفي الخط العربي الأساسيين لتسريع ظهور النصوص.
 */
function shami_preload_fonts() {
	foreach ( array( 'plex-arabic-400-arabic.woff2', 'alexandria-arabic.woff2' ) as $file ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( SHAMI_URI . '/assets/fonts/' . $file ) );
	}
}
add_action( 'wp_head', 'shami_preload_fonts', 1 );

/**
 * تعطيل سكربت الإيموجي لتسريع التحميل.
 */
function shami_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'shami_disable_emojis' );

/**
 * كلاسات body.
 *
 * @param array $classes الكلاسات.
 * @return array
 */
function shami_body_class( $classes ) {
	$classes[] = 'sh';
	if ( ! shami_show_prices() ) {
		$classes[] = 'sh-prices-hidden';
	}
	if ( is_page_template( 'page-templates/template-quick-order.php' ) ) {
		$classes[] = 'sh-quick-order-page';
	}
	return $classes;
}
add_filter( 'body_class', 'shami_body_class' );

/**
 * تنبيه في حال عدم تفعيل ووكومرس.
 */
function shami_wc_missing_notice() {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>قالب الشامي:</strong> يحتاج هذا القالب إلى إضافة <strong>WooCommerce</strong>. ثبّتها وفعّلها من صفحة الإضافات ثم افتح «المظهر ← إعداد متجر الشامي».</p></div>';
}
add_action( 'admin_notices', 'shami_wc_missing_notice' );

/**
 * ترحيل بيانات الإصدار السابق من القالب (البادئة maria) إلى بادئة الشامي، مرة واحدة.
 *
 * يشمل بيانات المنتجات (التعبئة، عدد القطع، العلامة…)، نصوص السيو للأقسام،
 * أرقام الصفحات الخاصة، وإعدادات «التخصيص» (واتساب، الهاتف، العنوان…).
 */
function shami_migrate_legacy_data() {
	if ( get_option( 'shami_migrated_v2' ) ) {
		return;
	}
	global $wpdb;
	// phpcs:disable WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_key = REPLACE(meta_key, '_maria_', '_shami_') WHERE meta_key LIKE '\\_maria\\_%'" );
	$wpdb->query( "UPDATE {$wpdb->termmeta} SET meta_key = REPLACE(meta_key, 'maria_', 'shami_') WHERE meta_key LIKE 'maria\\_%'" );
	$legacy = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'maria\\_page\\_%'" );
	// phpcs:enable WordPress.DB.DirectDatabaseQuery
	foreach ( $legacy as $row ) {
		$new = 'shami_' . substr( $row->option_name, 6 );
		if ( false === get_option( $new ) ) {
			update_option( $new, $row->option_value );
		}
	}
	$old_mods = get_option( 'theme_mods_maria' );
	if ( is_array( $old_mods ) ) {
		foreach ( $old_mods as $key => $value ) {
			if ( 0 === strpos( (string) $key, 'maria_' ) ) {
				$new = 'shami_' . substr( $key, 6 );
				if ( '' === (string) get_theme_mod( $new, '' ) ) {
					set_theme_mod( $new, $value );
				}
			}
		}
	}
	update_option( 'shami_migrated_v2', 1, false );
}
add_action( 'after_switch_theme', 'shami_migrate_legacy_data' );
add_action( 'admin_init', 'shami_migrate_legacy_data' );

/**
 * طول المقتطف.
 *
 * @return int
 */
function shami_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'shami_excerpt_length' );

/**
 * تسميات قائمة افتراضية في حال عدم إنشاء قائمة.
 */
function shami_fallback_menu() {
	$links = array(
		shami_page_url( 'about' )           => 'عن الشامي',
		shami_page_url( 'quick_order' )     => 'قائمة أسعار الجملة',
		shami_page_url( 'special_request' ) => 'طلب توريد خاص',
		shami_page_url( 'export' )          => 'التصدير والجملة الدولية',
		shami_page_url( 'contact' )         => 'تواصل معنا',
	);
	echo '<ul class="sh-menu">';
	foreach ( $links as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * علامة تفعيل JavaScript مبكراً (لتجنب وميض عناصر الحركة).
 */
function shami_js_class() {
	echo "<script>document.documentElement.classList.add('sh-js');</script>\n";
}
add_action( 'wp_head', 'shami_js_class', 0 );

/**
 * أيقونة الموقع من رمز الشعار (إن لم يرفع المدير أيقونة) + لون شريط المتصفح في الجوال.
 */
function shami_head_icons() {
	echo '<meta name="theme-color" content="#0B2A21">' . "\n";
	if ( has_site_icon() ) {
		return;
	}
	$svg = shami_brand_svg( 'mark.svg' );
	echo '<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,' . rawurlencode( $svg ) . '">' . "\n";
}
add_action( 'wp_head', 'shami_head_icons', 2 );
