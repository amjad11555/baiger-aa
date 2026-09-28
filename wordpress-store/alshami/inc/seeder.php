<?php
/**
 * إعداد المتجر بضغطة واحدة: الإعدادات، الأقسام، العلامات، الصفحات، القوائم، الشحن، والمنتجات.
 *
 * من لوحة التحكم: المظهر ← إعداد متجر الشامي
 * أو عبر WP-CLI:  wp shami seed [--mode=all|products|pages]
 *
 * العملية آمنة للتكرار: لا تكرر المنتجات (تُطابق بالـ SKU) ولا الصفحات (تُطابق بالمسار).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

/**
 * صفحة الإعداد في لوحة التحكم.
 */
function shami_seed_menu() {
	add_theme_page( 'إعداد متجر الشامي', 'إعداد متجر الشامي', 'manage_options', 'shami-setup', 'shami_seed_page' );
}
add_action( 'admin_menu', 'shami_seed_menu' );

/**
 * تنبيه بعد تفعيل القالب.
 */
function shami_seed_activation_flag() {
	update_option( 'shami_show_setup_notice', 1 );
}
add_action( 'after_switch_theme', 'shami_seed_activation_flag' );

/**
 * عرض التنبيه.
 */
function shami_seed_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'shami_show_setup_notice' ) || get_option( 'shami_seeded' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_shami-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>مرحباً بك في قالب الشامي!</strong> أنشئ الأقسام والصفحات وأكثر من 130 منتجاً (إيتي، أولكر، بونوتشي) بضغطة واحدة. <a class="button button-primary" href="%s">ابدأ الإعداد</a></p></div>',
		esc_url( admin_url( 'themes.php?page=shami-setup' ) )
	);
}
add_action( 'admin_notices', 'shami_seed_notice' );

/**
 * تنبيه إدخال رقم واتساب.
 */
function shami_whatsapp_notice() {
	if ( ! current_user_can( 'manage_options' ) || shami_wa_number() || ! get_option( 'shami_seeded' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p><strong>قالب الشامي:</strong> أضف رقم واتساب لتفعيل «أرسل الطلب عبر واتساب». <a href="%s">المظهر ← تخصيص ← إعدادات متجر الشامي</a></p></div>',
		esc_url( admin_url( 'customize.php?autofocus[section]=shami_contact' ) )
	);
}
add_action( 'admin_notices', 'shami_whatsapp_notice' );

/**
 * محتوى صفحة الإعداد.
 */
function shami_seed_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$report   = get_transient( 'shami_seed_report' );
	$products = function_exists( 'wc_get_products' ) ? count( wc_get_products( array( 'limit' => -1, 'return' => 'ids', 'status' => array( 'publish', 'draft', 'private' ) ) ) ) : 0;
	$catalog  = include SHAMI_DIR . '/inc/data/catalog.php';
	?>
	<div class="wrap" dir="rtl">
		<h1>إعداد متجر الشامي</h1>
		<?php if ( $report ) : ?>
			<?php delete_transient( 'shami_seed_report' ); ?>
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
				<p>آخر إعداد: <strong><?php echo get_option( 'shami_seeded' ) ? esc_html( wp_date( 'Y-m-d H:i', (int) get_option( 'shami_seeded' ) ) ) : 'لم يتم بعد'; ?></strong></p>
			</div>

			<div class="card" style="max-width:760px">
				<h2>1) الإعداد الكامل (موصى به أول مرة)</h2>
				<p>يضبط العملة (الليرة التركية)، الدفع عند الاستلام، حقول الدفع المختصرة، منطقة الشحن، ويُنشئ الأقسام الخمسة (كيك، بسكويت، شيبسات، تسالي، عروض)، العلامات التجارية، الصفحات (الرئيسية، الطلب السريع، طلب منتج غير متوفر، التصدير، تواصل معنا، من نحن، التوصيل)، القوائم، وجميع المنتجات.</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'shami_seed' ); ?>
					<input type="hidden" name="action" value="shami_seed">
					<input type="hidden" name="mode" value="all">
					<?php submit_button( 'تشغيل الإعداد الكامل', 'primary large', 'submit', false ); ?>
				</form>
			</div>

			<div class="card" style="max-width:760px">
				<h2>2) إضافة المنتجات الناقصة فقط</h2>
				<p>يضيف منتجات الكتالوج غير الموجودة (حسب رمز SKU) دون تعديل المنتجات الحالية أو أسعارك.</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'shami_seed' ); ?>
					<input type="hidden" name="action" value="shami_seed">
					<input type="hidden" name="mode" value="products">
					<?php submit_button( 'إضافة المنتجات الناقصة', 'secondary', 'submit', false ); ?>
				</form>
			</div>

			<div class="card" style="max-width:760px">
				<h2>الخطوات التالية</h2>
				<ol>
					<li>أدخل رقم واتساب والهاتف والعنوان من <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=shami_panel' ) ); ?>">المظهر ← تخصيص ← إعدادات متجر الشامي</a>.</li>
					<li>راجع الأسعار (الأسعار المضافة تقديرية) من المنتجات، أو استورد ملف CSV المرفق مع القالب بعد تعديله.</li>
					<li>ارفع صور المنتجات الحقيقية (اختياري) — إلى ذلك الحين يعرض القالب رسومات عبوات أنيقة تلقائياً.</li>
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
function shami_seed_handle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'غير مسموح.' );
	}
	check_admin_referer( 'shami_seed' );
	$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : 'all';
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	$report = shami_seed_run( $mode );
	set_transient( 'shami_seed_report', $report, 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'themes.php?page=shami-setup' ) );
	exit;
}
add_action( 'admin_post_shami_seed', 'shami_seed_handle' );

/**
 * تشغيل الإعداد.
 *
 * @param string $mode all|products|pages.
 * @return array تقرير.
 */
function shami_seed_run( $mode = 'all' ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array( 'error' => 'WooCommerce غير مفعّل' );
	}
	$report = array();
	if ( 'all' === $mode ) {
		$report['الإعدادات'] = shami_seed_settings();
	}
	$report['الأقسام والعلامات'] = shami_seed_terms();
	if ( in_array( $mode, array( 'all', 'pages' ), true ) ) {
		$report['الصفحات'] = shami_seed_pages();
		$report['القوائم']  = shami_seed_menus();
	}
	if ( in_array( $mode, array( 'all', 'products' ), true ) ) {
		$report['المنتجات'] = shami_seed_products();
	}
	if ( 'all' === $mode ) {
		$report['الشحن'] = shami_seed_shipping();
	}
	update_option( 'shami_seeded', time() );
	delete_option( 'shami_show_setup_notice' );
	flush_rewrite_rules( false );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
	return $report;
}

/**
 * إعدادات ووكومرس المناسبة لمتجر جملة تركي.
 *
 * @return string
 */
function shami_seed_settings() {
	$opts = array(
		'woocommerce_currency'                          => 'TRY',
		'woocommerce_currency_pos'                      => 'right_space',
		'woocommerce_price_num_decimals'                => 0,
		'woocommerce_price_thousand_sep'                => ',',
		'woocommerce_price_decimal_sep'                 => '.',
		'woocommerce_default_country'                   => 'TR:TR34',
		'woocommerce_allowed_countries'                 => 'specific',
		'woocommerce_specific_allowed_countries'        => array( 'TR' ),
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
	);
	foreach ( $opts as $k => $v ) {
		update_option( $k, $v );
	}

	$cod = (array) get_option( 'woocommerce_cod_settings', array() );
	$cod = array_merge(
		$cod,
		array(
			'enabled'            => 'yes',
			'title'              => 'الدفع عند الاستلام',
			'description'        => 'ادفع نقداً أو بالتحويل عند استلام الطلب في محلك.',
			'instructions'       => 'سنتواصل معك هاتفياً أو عبر واتساب لتأكيد الطلب وموعد التوصيل.',
			'enable_for_methods' => array(),
			'enable_for_virtual' => 'yes',
		)
	);
	update_option( 'woocommerce_cod_settings', $cod );

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
		update_option( 'blogname', 'الشامي' );
	}
	$desc = get_option( 'blogdescription' );
	if ( ! $desc || 'Just another WordPress site' === $desc ) {
		update_option( 'blogdescription', shami_opt( 'seo_tagline' ) );
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
function shami_seed_terms() {
	$cats  = shami_categories();
	$data  = include SHAMI_DIR . '/inc/data/categories.php';
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
			if ( ! $term->description && $args['description'] ) {
				wp_update_term( $id, 'product_cat', array( 'description' => $args['description'] ) );
			}
		} else {
			$res = wp_insert_term( $cat['name'], 'product_cat', $args );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$id = (int) $res['term_id'];
		}
		if ( isset( $data[ $slug ] ) && ! get_term_meta( $id, 'shami_seo_text', true ) ) {
			update_term_meta( $id, 'shami_seo_text', $data[ $slug ]['seo'] );
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
		foreach ( shami_brands() as $slug => $b ) {
			if ( ! get_term_by( 'slug', $slug, 'product_brand' ) ) {
				wp_insert_term(
					$b['ar'],
					'product_brand',
					array(
						'slug'        => $slug,
						'description' => sprintf( 'منتجات %1$s (%2$s – %3$s) بالجملة لتجار التجزئة: %4$s', $b['ar'], $b['alt'], $b['latin'], $b['about'] ),
					)
				);
			}
			$done[] = $b['ar'];
		}
	}
	return implode( '، ', $done );
}

/**
 * إنشاء الصفحات.
 *
 * @return array
 */
function shami_seed_pages() {
	$contents = include SHAMI_DIR . '/inc/data/pages.php';
	$created  = array();

	foreach ( shami_special_pages() as $key => $page ) {
		list( $slug, $template, $title ) = $page;
		$existing = (int) get_option( 'shami_page_' . $key );
		if ( $existing && get_post( $existing ) && 'trash' !== get_post_status( $existing ) ) {
			// تحديث النص الافتراضي فقط إن لم يعدّله المدير منذ إنشائه.
			$default = isset( $contents[ $key ] ) ? $contents[ $key ] : '';
			$hash    = (string) get_post_meta( $existing, '_shami_default_hash', true );
			$current = (string) get_post_field( 'post_content', $existing );
			if ( $default && $hash && md5( $current ) === $hash && md5( $default ) !== $hash ) {
				wp_update_post(
					array(
						'ID'           => $existing,
						'post_content' => $default,
					)
				);
				update_post_meta( $existing, '_shami_default_hash', md5( (string) get_post_field( 'post_content', $existing ) ) );
			}
			continue;
		}
		$found = get_page_by_path( $slug );
		if ( $found ) {
			update_option( 'shami_page_' . $key, $found->ID );
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
			update_post_meta( $id, '_shami_default_hash', md5( (string) get_post_field( 'post_content', $id ) ) );
			update_option( 'shami_page_' . $key, $id );
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
function shami_seed_menus() {
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
				$pid = (int) get_option( 'shami_page_' . $key );
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
 * منطقة الشحن: تركيا (توصيل مجاني + استلام من المستودع).
 *
 * @return string
 */
function shami_seed_shipping() {
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return 'غير متاح';
	}
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
				'title'      => 'توريد إلى المتجر',
				'requires'   => '',
				'min_amount' => '0',
			)
		);
	}
	$pickup = $zone->add_shipping_method( 'local_pickup' );
	if ( $pickup ) {
		update_option(
			'woocommerce_local_pickup_' . $pickup . '_settings',
			array(
				'title'      => 'استلام من المستودع',
				'tax_status' => 'none',
				'cost'       => '',
			)
		);
	}
	WC_Cache_Helper::get_transient_version( 'shipping', true );
	return 'تم إنشاء منطقة تركيا';
}

/**
 * سبب البيع حسب القسم (لوصف المنتج).
 *
 * @param string $cat القسم.
 * @return array
 */
function shami_seed_reasons( $cat ) {
	$reasons = array(
		'cake'     => array( 'وجبة خفيفة مشبعة يطلبها الطلاب والعمال يومياً', 'مغلّف فردياً ومناسب للبيع بالقطعة بجانب المشروبات', 'صلاحية مناسبة للعرض على الرف دون تبريد' ),
		'biscuits' => array( 'من أكثر المنتجات دوراناً في البقالات طوال العام', 'مناسب للضيافة ووقت الشاي والمدارس', 'هامش ربح جيد عند البيع بالقطعة' ),
		'chips'    => array( 'منتج شراء اندفاعي يرفع قيمة سلة الزبون', 'يزداد الطلب عليه في المساء وعطلات نهاية الأسبوع والمباريات', 'مثالي للعرض بجانب المشروبات الغازية' ),
		'snacks'   => array( 'حلوى مفضلة للأطفال والشباب قرب الكاشير', 'حجم صغير وسعر مناسب يشجع الشراء المتكرر', 'مناسب للهدايا والمناسبات والضيافة' ),
		'offers'   => array( 'توفير مباشر مقارنة بشراء الأصناف منفردة', 'تشكيلة جاهزة لتنويع الرف بسرعة', 'مناسبة للبقالات الجديدة ولتجربة أصناف جديدة' ),
	);
	return isset( $reasons[ $cat ] ) ? $reasons[ $cat ] : $reasons['snacks'];
}

/**
 * وصف المنتج الطويل (غني بالكلمات المفتاحية وبشكل طبيعي).
 *
 * @param array $r بيانات المنتج.
 * @return string
 */
function shami_seed_description( $r ) {
	$brands = shami_brands();
	$cats   = shami_categories();
	$flv    = shami_flavor( $r['flavor'] );
	$city   = shami_opt( 'city' );
	$b      = isset( $brands[ $r['brand'] ] ) ? $brands[ $r['brand'] ] : null;

	$html  = '<p><strong>' . esc_html( $r['name'] ) . '</strong> (<span lang="tr">' . esc_html( $r['tr'] ) . '</span>) — ' . esc_html( $r['desc'] ) . '.';
	$html .= $b ? ' من إنتاج شركة ' . esc_html( $b['ar'] ) . ' التركية (' . esc_html( $b['alt'] ) . ' – ' . esc_html( $b['latin'] ) . ')، ومن المنتجات المطلوبة يومياً في البقالات والسوبرماركت والمقاصف.</p>' : ' باقة موفّرة مختارة من أكثر المنتجات مبيعاً في البقالات.</p>';

	$html .= '<h2>لماذا يضيف أصحاب البقالات ' . esc_html( $r['line'] ) . ' إلى رفوفهم؟</h2><ul>';
	foreach ( shami_seed_reasons( $r['cat'] ) as $reason ) {
		$html .= '<li>' . esc_html( $reason ) . '.</li>';
	}
	$html .= '</ul>';

	$html .= '<h2>تفاصيل البيع بالجملة</h2><ul>';
	$html .= '<li>التعبئة: ' . esc_html( $r['pack'] ) . '</li>';
	$html .= $r['units'] ? '<li>وحدة البيع: كرتونة كاملة (' . (int) $r['units'] . ' قطعة)</li>' : '<li>وحدة البيع: باقة كاملة</li>';
	if ( 'offers' !== $r['cat'] ) {
		$html .= '<li>النكهة: ' . esc_html( $flv[0] ) . '</li>';
	}
	$html .= '<li>القسم: ' . esc_html( $cats[ $r['cat'] ]['name'] ) . '</li>';
	$html .= '</ul>';

	$html .= '<p>اطلب ' . esc_html( $r['name'] ) . ' بالجملة الآن من صفحة الطلب السريع أو عبر واتساب، ونوصله إلى محلك في ' . esc_html( $city ) . ' وجميع الولايات التركية مع الدفع عند الاستلام.';
	if ( $b ) {
		$html .= ' قد يبحث عنه زبائنك أيضاً باسم «' . esc_html( $r['tr'] ) . '» أو «' . esc_html( $b['alt'] . ' ' . $r['line'] ) . '».';
	}
	$html .= '</p>';
	return $html;
}

/**
 * إنشاء المنتجات.
 *
 * @return string
 */
function shami_seed_products() {
	$rows    = include SHAMI_DIR . '/inc/data/catalog.php';
	$created = 0;
	$skipped = 0;

	$cat_ids = array();
	foreach ( array_keys( shami_categories() ) as $slug ) {
		$t                = get_term_by( 'slug', $slug, 'product_cat' );
		$cat_ids[ $slug ] = $t ? (int) $t->term_id : 0;
	}

	wp_defer_term_counting( true );

	// منتجات من نسخة سابقة من الكتالوج لا تُباع بهذا الاسم فعلياً، واستُبدلت بمنتجات حقيقية: تُنقل إلى المهملات.
	$retired = 0;
	foreach ( array( 'ETI-TOP-RAI', 'ETI-TOP-CAR', 'ETI-TUT-MIN', 'ETI-FRM-WHL', 'ETI-NEG-MIN', 'ETI-HOS-MLK', 'ETI-CRX-SES', 'ULK-DAN-BOR', 'ULK-DAN-LBA', 'ULK-HAN-RAI', 'ULK-CRZ-SWT', 'ULK-ASK-CLS', 'ULK-GRS-SES', 'ULK-CMS-CLS' ) as $old_sku ) {
		$old_id = wc_get_product_id_by_sku( $old_sku );
		if ( $old_id && wp_trash_post( $old_id ) ) {
			++$retired;
		}
	}

	foreach ( $rows as $i => $row ) {
		$r = array_combine( array( 'sku', 'brand', 'cat', 'line', 'name', 'tr', 'flavor', 'pack', 'units', 'price', 'sale', 'best', 'desc' ), $row );

		if ( wc_get_product_id_by_sku( $r['sku'] ) ) {
			++$skipped;
			continue;
		}

		$product = new WC_Product_Simple();
		$product->set_name( $r['name'] );
		// إزالة % لأن ووردبريس يعامل %XX كترميز في الرابط (مثل «%54» في Karam %54).
		$product->set_slug( sanitize_title( str_replace( '%', '', $r['tr'] ) ) );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_sku( $r['sku'] );
		$product->set_regular_price( (string) $r['price'] );
		if ( $r['sale'] ) {
			$product->set_sale_price( (string) $r['sale'] );
		}
		$product->set_featured( (bool) $r['best'] );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->set_menu_order( $i );
		$product->set_short_description( '<p>' . esc_html( $r['desc'] ) . '. التعبئة: ' . esc_html( $r['pack'] ) . '. متوفر بالجملة للبقالات بسعر الكرتونة مع توصيل سريع.</p>' );
		$product->set_description( shami_seed_description( $r ) );

		$cats = array();
		if ( ! empty( $cat_ids[ $r['cat'] ] ) ) {
			$cats[] = $cat_ids[ $r['cat'] ];
		}
		if ( ( $r['sale'] || 'offers' === $r['cat'] ) && ! empty( $cat_ids['offers'] ) ) {
			$cats[] = $cat_ids['offers'];
		}
		$product->set_category_ids( array_values( array_unique( $cats ) ) );

		$product->update_meta_data( '_shami_brand', $r['brand'] );
		$product->update_meta_data( '_shami_line', $r['line'] );
		$product->update_meta_data( '_shami_tr', $r['tr'] );
		$product->update_meta_data( '_shami_flavor', $r['flavor'] );
		$product->update_meta_data( '_shami_pack', $r['pack'] );
		$product->update_meta_data( '_shami_units', (int) $r['units'] );
		if ( 'offers' === $r['cat'] ) {
			$product->update_meta_data( '_shami_bundle', 1 );
		}
		$id = $product->save();

		if ( $id && $r['brand'] && taxonomy_exists( 'product_brand' ) ) {
			$bt = get_term_by( 'slug', $r['brand'], 'product_brand' );
			if ( $bt ) {
				wp_set_object_terms( $id, array( (int) $bt->term_id ), 'product_brand' );
			}
		}
		++$created;
	}

	wp_defer_term_counting( false );

	$msg = sprintf( 'أُضيف %d منتجاً، وتم تخطي %d موجود مسبقاً', $created, $skipped );
	return $retired ? $msg . sprintf( '، ونُقل %d منتجاً قديماً إلى المهملات', $retired ) : $msg;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'shami seed',
		static function ( $args, $assoc ) {
			$mode   = isset( $assoc['mode'] ) ? sanitize_key( $assoc['mode'] ) : 'all';
			$report = shami_seed_run( $mode );
			foreach ( $report as $k => $v ) {
				WP_CLI::log( $k . ': ' . ( is_array( $v ) ? implode( '، ', $v ) : $v ) );
			}
			WP_CLI::success( 'تم إعداد متجر الشامي.' );
		},
		array( 'shortdesc' => 'إعداد متجر الشامي (الأقسام، الصفحات، المنتجات، الإعدادات).' )
	);
}
