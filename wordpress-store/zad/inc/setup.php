<?php
/**
 * تهيئة القالب: الدعم، القوائم، الأنماط والسكربتات.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * خصائص القالب.
 */
function zad_setup() {
	load_theme_textdomain( 'zad', ZAD_DIR . '/languages' );

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
add_action( 'after_setup_theme', 'zad_setup' );

/**
 * عرض المحتوى.
 */
function zad_content_width() {
	$GLOBALS['content_width'] = 1240; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
}
add_action( 'after_setup_theme', 'zad_content_width', 0 );

/**
 * فرض الاتجاه من اليمين لليسار في الواجهة حتى لو كانت لغة الموقع غير العربية.
 */
function zad_force_rtl() {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! zad_opt( 'force_rtl' ) ) {
		return;
	}
	global $wp_locale;
	if ( $wp_locale instanceof WP_Locale ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'init', 'zad_force_rtl', 1 );

/**
 * سمات اللغة في وسم html.
 *
 * @param string $output المخرجات.
 * @return string
 */
function zad_language_attributes( $output ) {
	if ( is_admin() || ! zad_opt( 'force_rtl' ) ) {
		return $output;
	}
	return 'dir="rtl" lang="ar"';
}
add_filter( 'language_attributes', 'zad_language_attributes', 20 );

/**
 * الأنماط والسكربتات.
 */
function zad_assets() {
	$css_file = ZAD_DIR . '/assets/css/main.css';
	$js_file  = ZAD_DIR . '/assets/js/main.js';
	$css_ver  = ZAD_VERSION . '.' . ( file_exists( $css_file ) ? filemtime( $css_file ) : '0' );
	$js_ver   = ZAD_VERSION . '.' . ( file_exists( $js_file ) ? filemtime( $js_file ) : '0' );

	// خطوط Google (Tajawal + Poppins + Amiri + Libre Baskerville) مستضافة داخل القالب: أسرع ولا تعتمد على خدمة خارجية.
	wp_enqueue_style( 'zad-fonts', ZAD_URI . '/assets/css/fonts.css', array(), ZAD_VERSION );
	wp_enqueue_style( 'zad-main', ZAD_URI . '/assets/css/main.css', array( 'zad-fonts' ), $css_ver );

	wp_enqueue_script(
		'zad-main',
		ZAD_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$config = array(
		'home'       => home_url( '/' ),
		'shopUrl'    => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
		'quickOrderUrl' => zad_page_url( 'quick_order' ),
		'searchUrl'  => esc_url_raw( rest_url( 'zad/v1/search' ) ),
		// يعرّف البحث الفوري بالزبون المسجّل (لازم لوضع «الأسعار للأعضاء»).
		'restNonce'  => is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '',
		'popular'    => array( 'بسكريم', 'براوني', 'شيبس', 'ويفر', 'شوكولاتة دبي', 'كراكرز' ),
		'wa'         => zad_wa_number(),
		'storeName'  => get_bloginfo( 'name' ),
		'showPrices' => zad_show_prices(),
		'minOrder'   => zad_show_prices() ? (float) zad_opt( 'min_order' ) : 0,
		'minCartons' => zad_min_cartons(),
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
		$config['syncUrl']     = WC_AJAX::get_endpoint( 'zad_sync_cart' );
		$config['cart']        = (object) zad_cart_qty_cached();
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

	wp_add_inline_script( 'zad-main', 'window.ZAD=' . wp_json_encode( $config ) . ';', 'before' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zad_assets', 20 );

/**
 * تحميل مسبق لملفي الخط العربي الأساسيين لتسريع ظهور النصوص.
 */
function zad_preload_fonts() {
	foreach ( array( 'tajawal-400-arabic.woff2', 'tajawal-700-arabic.woff2', 'poppins-500-latin.woff2' ) as $file ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( ZAD_URI . '/assets/fonts/' . $file ) );
	}
}
add_action( 'wp_head', 'zad_preload_fonts', 1 );

/**
 * تعطيل سكربت الإيموجي لتسريع التحميل.
 */
function zad_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'zad_disable_emojis' );

/**
 * كلاسات body.
 *
 * @param array $classes الكلاسات.
 * @return array
 */
function zad_body_class( $classes ) {
	$classes[] = 'sh';
	if ( ! zad_show_prices() ) {
		$classes[] = 'zd-prices-hidden';
	}
	if ( is_page_template( 'page-templates/template-quick-order.php' ) ) {
		$classes[] = 'zd-quick-order-page';
	}
	return $classes;
}
add_filter( 'body_class', 'zad_body_class' );

/**
 * تنبيه في حال عدم تفعيل ووكومرس.
 */
function zad_wc_missing_notice() {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>قالب زاد:</strong> يحتاج هذا القالب إلى إضافة <strong>WooCommerce</strong>. ثبّتها وفعّلها من صفحة الإضافات ثم افتح «المظهر ← إعداد متجر زاد».</p></div>';
}
add_action( 'admin_notices', 'zad_wc_missing_notice' );

/**
 * ترحيل بيانات الإصدارات السابقة من القالب (البادئتان maria ثم shami) إلى بادئة zad، مرة واحدة.
 * يشمل حقول المنتجات، وخصائص الأقسام، وأرقام الصفحات الخاصة، وإعدادات التخصيص، والصور المجلوبة.
 */
function zad_migrate_legacy_data() {
	if ( get_option( 'zad_migrated_v3' ) ) {
		return;
	}
	global $wpdb;
	foreach ( array( 'maria', 'shami' ) as $old ) {
		$len = strlen( $old ) + 1;
		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = CONCAT('_zad_', SUBSTR(meta_key, %d)) WHERE meta_key LIKE %s", $len + 2, '\\_' . $old . '\\_%' ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->termmeta} SET meta_key = CONCAT('zad_', SUBSTR(meta_key, %d)) WHERE meta_key LIKE %s", $len + 1, $old . '\\_%' ) );
		$wpdb->update( $wpdb->posts, array( 'post_type' => 'zad_request' ), array( 'post_type' => $old . '_request' ) );
		$legacy = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s", $old . '\\_page\\_%' ) );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery
		foreach ( $legacy as $row ) {
			$new = 'zad_' . substr( $row->option_name, $len );
			if ( false === get_option( $new ) ) {
				update_option( $new, $row->option_value );
			}
		}
		foreach ( array( 'maria' => 'maria', 'shami' => 'alshami' ) as $prefix => $slug ) {
			if ( $prefix !== $old ) {
				continue;
			}
			$old_mods = get_option( 'theme_mods_' . $slug );
			if ( is_array( $old_mods ) ) {
				foreach ( $old_mods as $key => $value ) {
					if ( 0 === strpos( (string) $key, $old . '_' ) ) {
						$new = 'zad_' . substr( $key, $len );
						if ( '' === (string) get_theme_mod( $new, '' ) ) {
							set_theme_mod( $new, $value );
						}
					}
				}
			}
		}
	}
	update_option( 'zad_migrated_v3', 1, false );
}
add_action( 'after_switch_theme', 'zad_migrate_legacy_data' );
add_action( 'admin_init', 'zad_migrate_legacy_data' );

/**
 * ترحيل مكمّل: حالة «تم الإعداد» وصور الموقع المجلوبة في قالب «الشامي».
 * بدونه يظهر تنبيه «ابدأ الإعداد» لمتجر مُعدّ فعلاً، وتعود الواجهة إلى الصور المؤقتة.
 */
function zad_migrate_legacy_extra() {
	if ( get_option( 'zad_migrated_v4' ) ) {
		return;
	}
	foreach ( array( 'shami', 'maria' ) as $old ) {
		$seeded = get_option( $old . '_seeded' );
		if ( $seeded && ! get_option( 'zad_seeded' ) ) {
			update_option( 'zad_seeded', $seeded );
		}
	}

	$old_images = get_option( 'shami_site_images' );
	if ( is_array( $old_images ) && $old_images && ! get_option( 'zad_site_images' ) ) {
		$up      = wp_upload_dir( null, false );
		$old_dir = trailingslashit( $up['basedir'] ) . 'alshami-site';
		$new_dir = trailingslashit( $up['basedir'] ) . 'zad-site';
		if ( is_dir( $old_dir ) && wp_mkdir_p( $new_dir ) ) {
			$moved = array();
			foreach ( $old_images as $name => $meta ) {
				$ok = true;
				foreach ( array( '', '-sm' ) as $suffix ) {
					$file = '/' . sanitize_file_name( $name . $suffix . '.' . $meta['ext'] );
					if ( file_exists( $old_dir . $file ) && ! file_exists( $new_dir . $file ) ) {
						$ok = copy( $old_dir . $file, $new_dir . $file ) && $ok;
					}
				}
				if ( $ok ) {
					$moved[ $name ] = $meta;
				}
			}
			if ( $moved ) {
				update_option( 'zad_site_images', $moved, false );
			}
		}
	}
	update_option( 'zad_migrated_v4', 1, false );
}
add_action( 'after_switch_theme', 'zad_migrate_legacy_extra', 11 );
add_action( 'admin_init', 'zad_migrate_legacy_extra', 11 );

/**
 * jquery-migrate غير مطلوب في الواجهة (لا القالب ولا ووكومرس يعتمد عليه): 10 كيلوبايت أقل في كل صفحة.
 *
 * @param WP_Scripts $scripts السكربتات.
 */
function zad_drop_jquery_migrate( $scripts ) {
	if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
		$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
	}
}
add_action( 'wp_default_scripts', 'zad_drop_jquery_migrate' );

/**
 * طول المقتطف.
 *
 * @return int
 */
function zad_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'zad_excerpt_length' );

/**
 * تسميات قائمة افتراضية في حال عدم إنشاء قائمة.
 */
function zad_fallback_menu() {
	$links = array(
		zad_page_url( 'about' )           => 'عن زاد',
		zad_page_url( 'quick_order' )     => 'قائمة أسعار الجملة',
		zad_page_url( 'special_request' ) => 'طلب توريد خاص',
		zad_page_url( 'export' )          => 'التصدير والجملة الدولية',
		zad_page_url( 'contact' )         => 'تواصل معنا',
	);
	echo '<ul class="zd-menu">';
	foreach ( $links as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * علامة تفعيل JavaScript مبكراً (لتجنب وميض عناصر الحركة).
 */
function zad_js_class() {
	echo "<script>document.documentElement.classList.add('zd-js');</script>\n";
}
add_action( 'wp_head', 'zad_js_class', 0 );

/**
 * أيقونة الموقع من رمز الشعار (إن لم يرفع المدير أيقونة) + لون شريط المتصفح في الجوال.
 */
function zad_head_icons() {
	echo '<meta name="theme-color" content="#FFFFFF">' . "\n";
	if ( has_site_icon() ) {
		return;
	}
	$svg = zad_brand_svg( 'mark.svg' );
	echo '<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,' . rawurlencode( $svg ) . '">' . "\n";
}
add_action( 'wp_head', 'zad_head_icons', 2 );
