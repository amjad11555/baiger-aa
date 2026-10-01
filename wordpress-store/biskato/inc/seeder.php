<?php
/**
 * إعداد المتجر بضغطة واحدة: الإعدادات، الأقسام، العلامات، الصفحات، القوائم، الشحن، والمنتجات.
 *
 * من لوحة التحكم: المظهر ← إعداد متجر بسكاتو
 * أو عبر WP-CLI:  wp zad seed [--mode=all|products|pages]
 *
 * العملية آمنة للتكرار: لا تكرر المنتجات (تُطابق بالـ SKU) ولا الصفحات (تُطابق بالمسار).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * صفحة الإعداد في لوحة التحكم.
 */
function zad_seed_menu() {
	add_theme_page( 'إعداد متجر بسكاتو', 'إعداد متجر بسكاتو', 'manage_options', 'zad-setup', 'zad_seed_page' );
}
add_action( 'admin_menu', 'zad_seed_menu' );

/**
 * تنبيه بعد تفعيل القالب.
 */
function zad_seed_activation_flag() {
	update_option( 'zad_show_setup_notice', 1 );
}
add_action( 'after_switch_theme', 'zad_seed_activation_flag' );

/**
 * عرض التنبيه.
 */
function zad_seed_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'zad_show_setup_notice' ) || get_option( 'zad_seeded' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_zad-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>مرحباً بك في قالب بسكاتو!</strong> أنشئ الأقسام التسعة والصفحات وأكثر من 330 منتجاً بأسعارها (إيتي، أولكر، بونجو، الوان…) بضغطة واحدة. <a class="button button-primary" href="%s">ابدأ الإعداد</a></p></div>',
		esc_url( admin_url( 'themes.php?page=zad-setup' ) )
	);
}
add_action( 'admin_notices', 'zad_seed_notice' );

/**
 * تنبيه إدخال رقم واتساب.
 */
function zad_whatsapp_notice() {
	if ( ! current_user_can( 'manage_options' ) || zad_wa_number() || ! get_option( 'zad_seeded' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p><strong>قالب بسكاتو:</strong> أضف رقم واتساب لتفعيل «أرسل الطلب عبر واتساب». <a href="%s">المظهر ← تخصيص ← إعدادات متجر بسكاتو</a></p></div>',
		esc_url( admin_url( 'customize.php?autofocus[section]=zad_contact' ) )
	);
}
add_action( 'admin_notices', 'zad_whatsapp_notice' );

/**
 * محتوى صفحة الإعداد.
 */
function zad_seed_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$report   = get_transient( 'zad_seed_report' );
	$products = function_exists( 'wc_get_products' ) ? count( wc_get_products( array( 'limit' => -1, 'return' => 'ids', 'status' => array( 'publish', 'draft', 'private' ) ) ) ) : 0;
	$catalog  = include ZAD_DIR . '/inc/data/catalog.php';
	?>
	<div class="wrap" dir="rtl">
		<h1>إعداد متجر بسكاتو</h1>
		<?php if ( $report ) : ?>
			<?php delete_transient( 'zad_seed_report' ); ?>
			<div class="notice notice-success"><p><strong>تم الإعداد بنجاح ✓</strong></p><ul style="list-style:disc;padding-inline-start:20px">
				<?php foreach ( $report as $k => $v ) : ?>
					<li><?php echo esc_html( $k . ': ' . ( is_array( $v ) ? wp_json_encode( $v, JSON_UNESCAPED_UNICODE ) : $v ) ); ?></li>
				<?php endforeach; ?>
			</ul></div>
		<?php endif; ?>

		<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
			<div class="notice notice-error"><p>فعّل إضافة WooCommerce أولاً.</p></div>
		<?php else : ?>
			<div class="card" style="max-width:760px">
				<h2>الحالة الحالية</h2>
				<p>المنتجات في المتجر: <strong><?php echo (int) $products; ?></strong> — المنتجات في كتالوج القالب: <strong><?php echo (int) count( $catalog ); ?></strong></p>
				<p>آخر إعداد: <strong><?php echo get_option( 'zad_seeded' ) ? esc_html( wp_date( 'Y-m-d H:i', (int) get_option( 'zad_seeded' ) ) ) : 'لم يتم بعد'; ?></strong></p>
			</div>

			<div class="card" style="max-width:760px">
				<h2>1) الإعداد الكامل (موصى به أول مرة)</h2>
				<p>يضبط العملة (الليرة التركية)، والدفع حسب المنطقة (نقداً عند الاستلام داخل إسطنبول، وتحويل بنكي خارجها)، ومناطق الشحن، ويُنشئ الأقسام التسعة (الكيك، البسكويت، الشوكولاتة والويفر، الشيبس، السكاكر، العلكة، الألعاب، العصائر، العروض)، والعلامات التجارية، والصفحات (الرئيسية، قائمة الأسعار، طلب توريد خاص، التصدير، تواصل معنا، من نحن، التوصيل)، والقوائم، وجميع المنتجات بأسعارها مع خصم إيتي وأولكر.</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'zad_seed' ); ?>
					<input type="hidden" name="action" value="zad_seed">
					<input type="hidden" name="mode" value="all">
					<?php submit_button( 'تشغيل الإعداد الكامل', 'primary large', 'submit', false ); ?>
				</form>
			</div>

			<div class="card" style="max-width:760px">
				<h2>2) إضافة المنتجات الناقصة فقط</h2>
				<p>يضيف منتجات الكتالوج غير الموجودة (حسب رمز SKU) دون تعديل المنتجات الحالية أو أسعارك.</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'zad_seed' ); ?>
					<input type="hidden" name="action" value="zad_seed">
					<input type="hidden" name="mode" value="products">
					<?php submit_button( 'إضافة المنتجات الناقصة', 'secondary', 'submit', false ); ?>
				</form>
			</div>

			<div class="card" style="max-width:760px">
				<h2>الخطوات التالية</h2>
				<ol>
					<li>أدخل رقم واتساب والهاتف والعنوان من <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=zad_panel' ) ); ?>">المظهر ← تخصيص ← إعدادات متجر بسكاتو</a>.</li>
					<li>أضف رقم الآيبان (IBAN) من <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=bacs' ) ); ?>">ووكومرس ← الإعدادات ← المدفوعات ← تحويل بنكي</a>: هو طريقة الدفع الوحيدة للطلبات خارج إسطنبول.</li>
					<li>صور المنتجات تُجلب تلقائياً في الخلفية (تابع التقدّم أو غيّر أي صورة من <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&page=zad-images' ) ); ?>">المنتجات ← صور المنتجات</a>).</li>
					<li>نسب خصم إيتي وأولكر وتأكيد الحسابات عبر واتساب من المظهر ← تخصيص ← إعدادات متجر بسكاتو.</li>
					<li>اجعل لغة الموقع «العربية» من الإعدادات ← عام لتنزيل ترجمة ووكومرس الكاملة.</li>
				</ol>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * معالجة زر الإعداد.
 */
function zad_seed_handle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'غير مسموح.' );
	}
	check_admin_referer( 'zad_seed' );
	$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : 'all';
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	$report = zad_seed_run( $mode );
	set_transient( 'zad_seed_report', $report, 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'themes.php?page=zad-setup' ) );
	exit;
}
add_action( 'admin_post_zad_seed', 'zad_seed_handle' );

/**
 * تشغيل الإعداد.
 *
 * @param string $mode all|products|pages.
 * @return array تقرير.
 */
function zad_seed_run( $mode = 'all' ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array( 'error' => 'WooCommerce غير مفعّل' );
	}
	$report = array();
	if ( 'all' === $mode ) {
		$report['الإعدادات'] = zad_seed_settings();
	}
	$report['الأقسام والعلامات'] = zad_seed_terms();
	if ( in_array( $mode, array( 'all', 'pages' ), true ) ) {
		$report['الصفحات'] = zad_seed_pages();
		$report['القوائم']  = zad_seed_menus();
	}
	if ( in_array( $mode, array( 'all', 'products' ), true ) ) {
		$report['المنتجات'] = zad_seed_products();
	}
	if ( 'all' === $mode ) {
		$report['الشحن']          = zad_seed_shipping();
		$report['المحتوى التجريبي'] = zad_seed_trash_samples();
	}
	update_option( 'zad_seeded', time() );
	delete_option( 'zad_show_setup_notice' );
	flush_rewrite_rules( false );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
	return $report;
}

/**
 * نقل محتوى ووردبريس التجريبي إلى سلة المهملات («أهلاً بالعالم» و«صفحة نموذجية»)
 * إن لم يُعدَّل منذ التثبيت، حتى لا يظهر في خريطة الموقع ونتائج جوجل.
 *
 * @return string
 */
function zad_seed_trash_samples() {
	$done = 0;
	foreach ( array( 1 => 'post', 2 => 'page' ) as $id => $type ) {
		$post = get_post( $id );
		if ( $post && $type === $post->post_type && 'publish' === $post->post_status && $post->post_modified_gmt === $post->post_date_gmt && (int) get_option( 'page_on_front' ) !== $id ) {
			wp_trash_post( $id );
			++$done;
		}
	}
	return $done ? sprintf( 'نُقل %d عنصر تجريبي إلى سلة المهملات.', $done ) : 'لا يوجد محتوى تجريبي.';
}

/**
 * إعدادات ووكومرس المناسبة لمتجر جملة تركي.
 *
 * @return string
 */
function zad_seed_settings() {
	$opts = array(
		'woocommerce_currency'                          => 'TRY',
		'woocommerce_currency_pos'                      => 'right_space',
		'woocommerce_price_num_decimals'                => 2,
		'woocommerce_price_thousand_sep'                => ',',
		'woocommerce_price_decimal_sep'                 => '.',
		'woocommerce_default_country'                   => 'TR:TR34',
		'woocommerce_allowed_countries'                 => 'all',
		'woocommerce_ship_to_countries'                 => '',
		'woocommerce_ship_to_destination'               => 'billing_only',
		'woocommerce_enable_guest_checkout'             => 'yes',
		'woocommerce_enable_checkout_login_reminder'    => 'no',
		'woocommerce_enable_signup_and_login_from_checkout' => 'no',
		'woocommerce_enable_reviews'                    => 'no',
		'woocommerce_enable_ajax_add_to_cart'           => 'yes',
		'woocommerce_cart_redirect_after_add'           => 'no',
		'woocommerce_calc_taxes'                        => 'no',
		'woocommerce_enable_coupons'                    => 'yes',
		'woocommerce_checkout_company_field'            => 'required',
		'woocommerce_checkout_address_2_field'          => 'hidden',
		'woocommerce_checkout_phone_field'              => 'required',
		'woocommerce_coming_soon'                       => 'no',
		'woocommerce_store_pages_only'                  => 'no',
		'woocommerce_weight_unit'                       => 'kg',
		'woocommerce_manage_stock'                      => 'no',
		'woocommerce_stock_format'                      => 'no_amount',
		'woocommerce_enable_myaccount_registration'     => 'yes',
	);
	foreach ( $opts as $k => $v ) {
		update_option( $k, $v );
	}

	zad_setup_gateways();

	update_option( 'woocommerce_checkout_privacy_policy_text', 'نستخدم بياناتك لمعالجة طلبك والتواصل معك بخصوصه فقط، كما هو موضّح في [privacy_policy].' );
	update_option( 'woocommerce_registration_privacy_policy_text', 'نستخدم بياناتك لإدارة حسابك وطلباتك فقط، كما هو موضّح في [privacy_policy].' );
	$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $privacy && 'Privacy Policy' === get_the_title( $privacy ) ) {
		wp_update_post(
			array(
				'ID'         => $privacy,
				'post_title' => 'سياسة الخصوصية',
			)
		);
	}

	$name = get_option( 'blogname' );
	if ( ! $name || in_array( $name, array( 'WordPress', 'My WordPress Site', 'My Blog', 'موقعي', 'ووردبريس' ), true ) ) {
		update_option( 'blogname', 'بسكاتو' );
	}
	$desc = get_option( 'blogdescription' );
	if ( ! $desc || 'Just another WordPress site' === $desc ) {
		update_option( 'blogdescription', zad_opt( 'seo_tagline' ) );
	}
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	if ( ! get_option( 'timezone_string' ) && ! (float) get_option( 'gmt_offset' ) ) {
		update_option( 'timezone_string', 'Europe/Istanbul' );
	}
	return 'تم';
}

/**
 * الأقسام والعلامات التجارية.
 *
 * @return array
 */
function zad_seed_terms( $force = false ) {
	$cats  = zad_categories();
	$data  = include ZAD_DIR . '/inc/data/categories.php';
	$done  = array();
	$order = 0;
	foreach ( $cats as $slug => $cat ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		$args = array(
			'slug'        => $slug,
			'description' => isset( $data[ $slug ] ) ? $data[ $slug ]['desc'] : '',
		);
		if ( $term ) {
			$id = (int) $term->term_id;
			if ( $force ) {
				wp_update_term(
					$id,
					'product_cat',
					array(
						'name'        => $cat['name'],
						'description' => $args['description'],
					)
				);
				if ( isset( $data[ $slug ] ) ) {
					update_term_meta( $id, 'zad_seo_text', $data[ $slug ]['seo'] );
				}
			} elseif ( ! $term->description && $args['description'] ) {
				wp_update_term( $id, 'product_cat', array( 'description' => $args['description'] ) );
			}
		} else {
			$res = wp_insert_term( $cat['name'], 'product_cat', $args );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$id = (int) $res['term_id'];
		}
		if ( isset( $data[ $slug ] ) && ! get_term_meta( $id, 'zad_seo_text', true ) ) {
			update_term_meta( $id, 'zad_seo_text', $data[ $slug ]['seo'] );
		}
		update_term_meta( $id, 'order', $order++ );
		$done[] = $cat['name'];
	}

	// إعادة تسمية القسم الافتراضي.
	$uncat = get_term_by( 'slug', 'uncategorized', 'product_cat' );
	if ( $uncat && 'Uncategorized' === $uncat->name ) {
		wp_update_term( $uncat->term_id, 'product_cat', array( 'name' => 'منوعات' ) );
	}

	if ( taxonomy_exists( 'product_brand' ) ) {
		foreach ( zad_brands() as $slug => $b ) {
			$desc = trim( sprintf( 'منتجات %1$s (%2$s) بالجملة لمحلات إسطنبول. %3$s', $b['ar'], $b['latin'], $b['about'] ) );
			$bt   = get_term_by( 'slug', $slug, 'product_brand' );
			if ( ! $bt ) {
				wp_insert_term(
					$b['ar'],
					'product_brand',
					array(
						'slug'        => $slug,
						'description' => $desc,
					)
				);
			} elseif ( $force && ( $bt->name !== $b['ar'] || $bt->description !== $desc ) ) {
				wp_update_term(
					$bt->term_id,
					'product_brand',
					array(
						'name'        => $b['ar'],
						'description' => $desc,
					)
				);
			}
			$done[] = $b['ar'];
		}
	}
	return implode( '، ', $done );
}

/**
 * الطلبات خارج تركيا: طريقة شحن «نتفق على التكلفة» في منطقة «باقي الدول»، وإلا لا يكتمل الطلب.
 */
function zad_seed_world_shipping() {
	if ( ! class_exists( 'WC_Shipping_Zone' ) ) {
		return;
	}
	$zone = new WC_Shipping_Zone( 0 );
	foreach ( $zone->get_shipping_methods() as $m ) {
		if ( in_array( $m->id, array( 'flat_rate', 'free_shipping' ), true ) ) {
			return;
		}
	}
	$id = $zone->add_shipping_method( 'flat_rate' );
	if ( $id ) {
		update_option(
			'woocommerce_flat_rate_' . $id . '_settings',
			array(
				'title'      => 'شحن دولي (نتفق معك على التكلفة)',
				'tax_status' => 'none',
				'cost'       => '0',
			)
		);
	}
	WC_Cache_Helper::get_transient_version( 'shipping', true );
}

/**
 * تحديث النص الافتراضي لصفحة خاصة، فقط إن لم يعدّله المدير منذ إنشائها.
 *
 * @param int    $id      رقم الصفحة.
 * @param string $default النص الافتراضي الجديد.
 */
function zad_refresh_page_content( $id, $default ) {
	$hash    = (string) get_post_meta( $id, '_zad_default_hash', true );
	$current = (string) get_post_field( 'post_content', $id );
	if ( $default && $hash && md5( $current ) === $hash && md5( $default ) !== $hash ) {
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => $default,
			)
		);
		update_post_meta( $id, '_zad_default_hash', md5( (string) get_post_field( 'post_content', $id ) ) );
	}
}

/**
 * تحديث نصوص الصفحات الخاصة الموجودة (من نحن، التوصيل…) دون إنشاء شيء جديد.
 */
function zad_refresh_default_pages() {
	$contents = include ZAD_DIR . '/inc/data/pages.php';
	foreach ( array_keys( zad_special_pages() ) as $key ) {
		$id = (int) get_option( 'zad_page_' . $key );
		if ( $id && isset( $contents[ $key ] ) && get_post( $id ) ) {
			zad_refresh_page_content( $id, $contents[ $key ] );
		}
	}
}

/**
 * إنشاء الصفحات.
 *
 * @return array
 */
function zad_seed_pages() {
	$contents = include ZAD_DIR . '/inc/data/pages.php';
	$created  = array();

	foreach ( zad_special_pages() as $key => $page ) {
		list( $slug, $template, $title ) = $page;
		$existing = (int) get_option( 'zad_page_' . $key );
		if ( $existing && get_post( $existing ) && 'trash' !== get_post_status( $existing ) ) {
			zad_refresh_page_content( $existing, isset( $contents[ $key ] ) ? $contents[ $key ] : '' );
			continue;
		}
		$found = get_page_by_path( $slug );
		if ( $found ) {
			update_option( 'zad_page_' . $key, $found->ID );
			if ( $template ) {
				update_post_meta( $found->ID, '_wp_page_template', $template );
			}
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => isset( $contents[ $key ] ) ? $contents[ $key ] : '',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			if ( $template ) {
				update_post_meta( $id, '_wp_page_template', $template );
			}
			update_post_meta( $id, '_zad_default_hash', md5( (string) get_post_field( 'post_content', $id ) ) );
			update_option( 'zad_page_' . $key, $id );
			$created[] = $title;
		}
	}

	// الصفحة الرئيسية الثابتة.
	if ( 'page' !== get_option( 'show_on_front' ) || ! get_option( 'page_on_front' ) ) {
		$home = get_page_by_path( 'home' );
		$id   = $home ? $home->ID : wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'الرئيسية',
				'post_name'   => 'home',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $id );
			$created[] = 'الرئيسية';
		}
	}

	// صفحات ووكومرس: عناوين عربية + سلة ودفع بالقوالب الكلاسيكية (لتخصيص الحقول بالكامل).
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}
	$wc_pages = array(
		'shop'      => array( 'كل الأصناف', '' ),
		'cart'      => array( 'الطلبية', '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->' ),
		'checkout'  => array( 'إتمام الطلب', '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->' ),
		'myaccount' => array( 'حسابي', '' ),
	);
	foreach ( $wc_pages as $key => $data ) {
		$pid = wc_get_page_id( $key );
		if ( $pid <= 0 ) {
			continue;
		}
		$update = array(
			'ID'         => $pid,
			'post_title' => $data[0],
		);
		if ( $data[1] ) {
			$content = (string) get_post_field( 'post_content', $pid );
			if ( false === strpos( $content, '[woocommerce_' ) ) {
				$update['post_content'] = $data[1];
			}
		}
		wp_update_post( $update );
	}

	return $created ? $created : array( 'موجودة مسبقاً' );
}

/**
 * القوائم.
 *
 * @return string
 */
function zad_seed_menus() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$menus     = array(
		'primary' => array(
			'name'  => 'القائمة الرئيسية',
			'items' => array( 'special_request', 'export', 'about', 'contact' ),
		),
		'footer'  => array(
			'name'  => 'روابط التذييل',
			'items' => array( 'about', 'delivery', 'contact', 'export' ),
		),
	);
	$out = array();
	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			continue;
		}
		$existing = wp_get_nav_menu_object( $menu['name'] );
		$menu_id  = $existing ? $existing->term_id : wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		if ( ! $existing ) {
			foreach ( $menu['items'] as $key ) {
				$pid = (int) get_option( 'zad_page_' . $key );
				if ( ! $pid ) {
					continue;
				}
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-object-id' => $pid,
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
			}
		}
		$locations[ $location ] = $menu_id;
		$out[]                  = $menu['name'];
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	return $out ? implode( '، ', $out ) : 'موجودة مسبقاً';
}

/**
 * منطقة الشحن: تركيا (توصيل مجاني إلى المحل فقط، بلا استلام من المستودع).
 *
 * @return string
 */
function zad_seed_shipping() {
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return 'غير متاح';
	}
	zad_seed_world_shipping();
	foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
		foreach ( $zone['zone_locations'] as $loc ) {
			if ( 'country' === $loc->type && 'TR' === $loc->code ) {
				return 'موجودة مسبقاً';
			}
		}
	}
	$zone = new WC_Shipping_Zone();
	$zone->set_zone_name( 'تركيا' );
	$zone->set_zone_order( 1 );
	$zone->add_location( 'TR', 'country' );
	$zone->save();

	$free = $zone->add_shipping_method( 'free_shipping' );
	if ( $free ) {
		update_option(
			'woocommerce_free_shipping_' . $free . '_settings',
			array(
				'title'      => zad_delivery_title(),
				'requires'   => '',
				'min_amount' => '0',
			)
		);
	}
	WC_Cache_Helper::get_transient_version( 'shipping', true );
	return 'تم إنشاء منطقة تركيا';
}

/**
 * أسباب إضافة الصنف إلى الرف حسب القسم.
 *
 * @param string $cat القسم.
 * @return array
 */
function zad_seed_reasons( $cat ) {
	$reasons = array(
		'cake'     => array( 'وجبة خفيفة مشبعة يطلبها الطلاب والعمال يومياً', 'مغلّف فردياً ومناسب للبيع بالقطعة بجانب المشروبات', 'يُعرض على الرف دون تبريد' ),
		'biscuits' => array( 'من أكثر الأصناف دوراناً في البقالات طوال العام', 'مناسب للضيافة ووقت الشاي والمدارس', 'هامش ربح جيد عند البيع بالقطعة' ),
		'snacks'   => array( 'صنف شراء سريع قرب الكاشير', 'سعر القطعة صغير فيشجع الشراء المتكرر', 'مطلوب لدى الأطفال والشباب' ),
		'chips'    => array( 'تسالي مالحة ترفع قيمة سلة الزبون', 'يزداد طلبها في المساء وأيام المباريات', 'تُعرض بجانب المشروبات الغازية' ),
		'candy'    => array( 'يشتريها الأطفال بالقطعة كل يوم', 'سعر صغير ودوران سريع', 'تجذب الصغار إلى رف الكاشير' ),
		'gum'      => array( 'أول صنف على الكاشير', 'تُباع طوال اليوم دون موسم', 'لا تحتاج مساحة كبيرة على الرف' ),
		'toys'     => array( 'مفاجأة يحبها الأطفال', 'ربح جيد للقطعة الواحدة', 'تُعرض قرب الكاشير وتجذب العائلات' ),
		'drinks'   => array( 'تكمل رف المشروبات', 'يطلبها الزبائن مع الكيك والبسكويت', 'عبوات مناسبة للبيع بالقطعة' ),
	);
	return isset( $reasons[ $cat ] ) ? $reasons[ $cat ] : $reasons['snacks'];
}

/**
 * وصف المنتج الطويل: الاسمان العربي والتركي، والعلامة، والتعبئة، والتوصيل والدفع في إسطنبول.
 *
 * @param array $r بيانات المنتج.
 * @return string
 */
function zad_seed_description( $r ) {
	$brands = zad_brands();
	$cats   = zad_categories();
	$city   = zad_opt( 'city' );
	$b      = ( $r['brand'] && isset( $brands[ $r['brand'] ] ) ) ? $brands[ $r['brand'] ] : null;
	$unit   = ! empty( $r['unit'] ) ? $r['unit'] : 'علبة';
	$cat    = isset( $cats[ $r['cat'] ] ) ? $cats[ $r['cat'] ] : null;

	$html  = '<p><strong>' . esc_html( $r['name'] ) . '</strong> (<span lang="tr">' . esc_html( $r['tr'] ) . '</span>): ' . esc_html( $r['desc'] ) . '.';
	$html .= $b ? ' من منتجات ' . esc_html( $b['ar'] ) . ' (' . esc_html( $b['latin'] ) . ')، متوفر عندنا بالجملة لمحلات البقالة والماركت في ' . esc_html( $city ) . '.</p>' : ' متوفر بالجملة لمحلات البقالة والماركت في ' . esc_html( $city ) . '.</p>';

	$html .= '<h2>تفاصيل البيع بالجملة</h2><ul>';
	$html .= '<li>وحدة البيع: ' . esc_html( $unit ) . ( $r['units'] ? ' فيها ' . (int) $r['units'] . ' قطعة' : '' ) . '</li>';
	if ( $r['pack'] ) {
		$html .= '<li>التعبئة: ' . esc_html( $r['pack'] ) . '</li>';
	}
	if ( $cat ) {
		$html .= '<li>القسم: ' . esc_html( $cat['title'] ) . '</li>';
	}
	if ( $b ) {
		$html .= '<li>العلامة: ' . esc_html( $b['ar'] . ' · ' . $b['latin'] ) . '</li>';
	}
	$html .= '</ul>';

	$html .= '<h2>لماذا يطلبه أصحاب المحلات؟</h2><ul>';
	foreach ( zad_seed_reasons( $r['cat'] ) as $reason ) {
		$html .= '<li>' . esc_html( $reason ) . '.</li>';
	}
	$html .= '</ul>';

	$html .= '<h2>التوصيل والدفع</h2><p>نوصل ' . esc_html( $r['name'] ) . ' إلى باب محلك أو نرتبه على الرف في كل مناطق ' . esc_html( $city ) . '، والدفع نقداً عند الاستلام. للطلبات خارج ' . esc_html( $city ) . ' أو خارج تركيا يكون الدفع بتحويل بنكي. تظهر الأسعار بعد فتح حساب جملة وتأكيده عبر واتساب.</p>';
	$html .= '<p>يبحث عنه التجار أيضاً باسم «' . esc_html( $r['tr'] ) . '»' . ( $b ? '، و«' . esc_html( $b['alt'] . ' ' . $r['line'] ) . ' جملة»' : '' ) . '، و«' . esc_html( $r['line'] ) . ' جملة اسطنبول».</p>';
	return $html;
}

/**
 * إضافة منتجات الكتالوج وتحديثها: تبدأ المهمة وتُنفّذ أول دفعة، ويكمل الباقي في الخلفية
 * (أو كاملةً من سطر الأوامر).
 *
 * @return string
 */
function zad_seed_products() {
	if ( ! zad_catalog_job() ) {
		zad_catalog_job_start( false );
	}
	$cli = defined( 'WP_CLI' ) && WP_CLI;
	do {
		$st = zad_catalog_job_step( $cli ? 120 : 20 );
	} while ( $cli && empty( $st['done'] ) );
	return isset( $st['message'] ) ? $st['message'] : '';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'zad seed',
		static function ( $args, $assoc ) {
			$mode   = isset( $assoc['mode'] ) ? sanitize_key( $assoc['mode'] ) : 'all';
			$report = zad_seed_run( $mode );
			foreach ( $report as $k => $v ) {
				WP_CLI::log( $k . ': ' . ( is_array( $v ) ? implode( '، ', $v ) : $v ) );
			}
			WP_CLI::success( 'تم إعداد متجر بسكاتو.' );
		},
		array( 'shortdesc' => 'إعداد متجر بسكاتو (الأقسام، الصفحات، المنتجات، الإعدادات).' )
	);
}
