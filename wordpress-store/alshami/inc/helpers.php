<?php
/**
 * دوال مساعدة عامة.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

/**
 * القيم الافتراضية لإعدادات القالب (قابلة للتعديل من المخصّص).
 *
 * @return array
 */
function shami_defaults() {
	return array(
		'whatsapp'      => '',
		'phone'         => '',
		'email'         => '',
		'address'       => '',
		'city'          => 'إسطنبول',
		'hours'         => 'السبت – الخميس · 9:00 – 19:00',
		'announcement'  => 'أسعار جملة للحسابات التجارية · توريد إلى جميع الولايات التركية · تصدير بالحاويات',
		'show_prices'   => true,
		'min_order'     => 0,
		'free_delivery' => 0,
		'hero_kicker'   => 'الشامي للتجارة · جملة وتوزيع',
		'hero_title'    => 'مورّدك الثابت للحلويات والتسالي التركية بالجملة',
		'hero_text'     => 'نوفّر لمتجرك أكثر من 130 صنفاً من إيتي وأولكر وبونوتشي بأسعار الجملة، مع توريد منتظم إلى كل الولايات وتصدير بالحاويات. اطلب بالكرتونة أو بالطبلية، ونتولى نحن التجهيز والتوصيل.',
		'hero_image'    => '',
		'force_rtl'     => true,
		'instagram'     => '',
		'facebook'      => '',
		'tiktok'        => '',
		'telegram'      => '',
		'youtube'       => '',
		'seo_tagline'   => 'الشامي للتجارة: جملة وتوزيع الحلويات والتسالي التركية لتجار التجزئة والموزعين',
	);
}

/**
 * قراءة إعداد من إعدادات القالب.
 *
 * @param string $key     المفتاح.
 * @param mixed  $default قيمة بديلة.
 * @return mixed
 */
function shami_opt( $key, $default = null ) {
	$defaults = shami_defaults();
	if ( null === $default && array_key_exists( $key, $defaults ) ) {
		$default = $defaults[ $key ];
	}
	return get_theme_mod( 'shami_' . $key, $default );
}

/**
 * رقم واتساب بصيغة دولية أرقام فقط.
 *
 * @return string
 */
function shami_wa_number() {
	$num = preg_replace( '/\D+/', '', (string) shami_opt( 'whatsapp' ) );
	if ( 0 === strpos( $num, '00' ) ) {
		$num = substr( $num, 2 );
	}
	return $num;
}

/**
 * رابط محادثة واتساب مع نص جاهز.
 *
 * @param string $text النص.
 * @return string
 */
function shami_wa_link( $text = '' ) {
	$num = shami_wa_number();
	$url = 'https://wa.me/' . $num;
	if ( '' !== $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

/**
 * الصفحات الخاصة بالقالب: المفتاح => [المسار، القالب، العنوان].
 *
 * @return array
 */
function shami_special_pages() {
	return array(
		'quick_order'     => array( 'quick-order', 'page-templates/template-quick-order.php', 'قائمة أسعار الجملة' ),
		'special_request' => array( 'special-request', 'page-templates/template-special-request.php', 'طلب توريد خاص' ),
		'export'          => array( 'wholesale-export', 'page-templates/template-export.php', 'التصدير والجملة الدولية' ),
		'brands'          => array( 'brands', 'page-templates/template-brands.php', 'العلامات التجارية' ),
		'contact'         => array( 'contact', 'page-templates/template-contact.php', 'تواصل مع المبيعات' ),
		'about'           => array( 'about', '', 'عن الشامي' ),
		'delivery'        => array( 'delivery-returns', '', 'التوريد والدفع والإرجاع' ),
	);
}

/**
 * تاريخ اليوم بأسماء الأشهر العربية، مهما كانت لغة الموقع.
 *
 * @return string مثل: 28 سبتمبر 2026.
 */
function shami_ar_date() {
	$months = array( 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر' );
	return wp_date( 'j' ) . ' ' . $months[ (int) wp_date( 'n' ) - 1 ] . ' ' . wp_date( 'Y' );
}

/**
 * صورة رأس الصفحة وعنوانها الصغير لصفحات الشركة.
 *
 * @param int    $page_id رقم الصفحة.
 * @param string $field   image|eyebrow.
 * @return string
 */
function shami_page_image( $page_id, $field = 'image' ) {
	$map   = array(
		'about'    => array( 'about-team', 'الشامي للتجارة · جملة وتوزيع' ),
		'delivery' => array( 'step-deliver', 'شروط التعامل مع حسابات الجملة' ),
	);
	$pages = shami_special_pages();
	foreach ( $map as $key => $row ) {
		$match = (int) get_option( 'shami_page_' . $key ) === (int) $page_id;
		if ( ! $match ) {
			$page  = get_page_by_path( $pages[ $key ][0] );
			$match = $page && (int) $page->ID === (int) $page_id;
		}
		if ( $match ) {
			return 'eyebrow' === $field ? $row[1] : $row[0];
		}
	}
	return '';
}

/**
 * رابط صفحة خاصة بالقالب.
 *
 * @param string $key مفتاح الصفحة.
 * @return string
 */
function shami_page_url( $key ) {
	static $cache = array();
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$pages = shami_special_pages();
	$url   = '';
	$id    = (int) get_option( 'shami_page_' . $key );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		$url = get_permalink( $id );
	} elseif ( isset( $pages[ $key ] ) ) {
		$page = get_page_by_path( $pages[ $key ][0] );
		if ( ! $page && $pages[ $key ][1] ) {
			$found = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => $pages[ $key ][1], // phpcs:ignore WordPress.DB.SlowDBQuery
					'fields'         => 'ids',
				)
			);
			$page  = $found ? get_post( $found[0] ) : null;
		}
		$url = $page ? get_permalink( $page ) : home_url( '/' . $pages[ $key ][0] . '/' );
	}
	$cache[ $key ] = $url;
	return $url;
}

/**
 * العلامات التجارية المعتمدة في المتجر.
 *
 * @return array
 */
function shami_brands() {
	return array(
		'eti'     => array(
			'ar'    => 'إيتي',
			'alt'   => 'ايتي',
			'latin' => 'Eti',
			'c1'    => '#CE0006',
			'c2'    => '#EFB455',
			'about' => 'علامة تركية عريقة تأسست عام 1962 في مدينة إسكي شهير، ومن أشهر منتجاتها براوني وبوب كيك وتوب كيك وتوتكو وجين وكراكس.',
		),
		'ulker'   => array(
			'ar'    => 'أولكر',
			'alt'   => 'اولكر',
			'latin' => 'Ülker',
			'c1'    => '#1F3C88',
			'c2'    => '#F4C542',
			'about' => 'من أقدم وأكبر شركات الحلويات في تركيا منذ 1944، صاحبة بسكريم وشوكوبرنس وهالي وألبيني وويفر أولكر الشهير.',
		),
		'bonucci' => array(
			'ar'    => 'بونوتشي',
			'alt'   => 'بونوچي',
			'latin' => 'Bonucci',
			'c1'    => '#4A2412',
			'c2'    => '#E6B36A',
			'about' => 'علامة تركية متخصصة في الكيك المحشو والشوكولاتة الفاخرة، ومن منتجاتها كيك شوكيكس ورول كريموسو وشوكولاتة دبي بالكنافة.',
		),
	);
}

/**
 * أقسام المتجر الرئيسية بالترتيب.
 *
 * @return array
 */
function shami_categories() {
	return array(
		'cake'     => array(
			'name'  => 'كيك',
			'title' => 'الكيك المغلّف',
			'icon'  => 'cake',
			'image' => 'cat-cake',
			'color' => '#124A39',
			'tint'  => '#E4EDE8',
			'line'  => 'براوني وبوب كيك ورول ومحشو، الأسرع دوراناً على رف الحلويات',
		),
		'biscuits' => array(
			'name'  => 'بسكويت',
			'title' => 'البسكويت والكوكيز',
			'icon'  => 'cookie',
			'image' => 'cat-biscuits',
			'color' => '#7A5A2B',
			'tint'  => '#F1E8D8',
			'line'  => 'محشو وسادة ومغطّى، طلبه ثابت على مدار السنة',
		),
		'chips'    => array(
			'name'  => 'شيبسات',
			'title' => 'الشيبس والمقرمشات',
			'icon'  => 'chips',
			'image' => 'cat-chips',
			'color' => '#8A5A1E',
			'tint'  => '#F4EAD9',
			'line'  => 'كراكرز وأصابع مملّحة وذرة، هامش جيد ودوران سريع',
		),
		'snacks'   => array(
			'name'  => 'تسالي',
			'title' => 'الشوكولاتة والتسالي',
			'icon'  => 'candy',
			'image' => 'cat-snacks',
			'color' => '#4B2A1C',
			'tint'  => '#EFE4DC',
			'line'  => 'ألواح وويفر وحلوى، أصناف الكاشير والشراء السريع',
		),
		'offers'   => array(
			'name'  => 'عروض',
			'title' => 'عروض الجملة',
			'icon'  => 'percent',
			'image' => 'cat-offers',
			'color' => '#9A3B22',
			'tint'  => '#F5E3DC',
			'line'  => 'أسعار خاصة على كميات محددة، تتجدد كل أسبوع',
		),
	);
}

/**
 * الشرائح التي نورّد لها (قسم «لمن نورّد» في الرئيسية).
 *
 * @return array
 */
function shami_segments() {
	return array(
		array(
			'image' => 'seg-supermarket',
			'title' => 'السوبرماركت والسلاسل',
			'text'  => 'تشكيلة تغطي رف الحلويات بالكامل، وتوريد بجدول ثابت، وفاتورة نظامية مع كل شحنة.',
		),
		array(
			'image' => 'seg-grocery',
			'title' => 'البقالات والميني ماركت',
			'text'  => 'اطلب من كرتونة واحدة لكل صنف، ونوصل الطلبية إلى باب المحل والدفع عند الاستلام.',
		),
		array(
			'image' => 'seg-distributor',
			'title' => 'الموزعون وتجار نصف الجملة',
			'text'  => 'أسعار كميات بالطبلية، وتحميل مباشر من المستودع لتغذية شبكة التوزيع في منطقتك.',
		),
		array(
			'image' => 'seg-export',
			'title' => 'المستوردون خارج تركيا',
			'text'  => 'حاويات 20 و40 قدماً أو طبليات مختلطة، مع شهادات المنشأ والحلال ومستندات التخليص.',
			'page'  => 'export',
		),
	);
}

/**
 * أبعاد صور الموقع (عرض × ارتفاع النسخة الكبيرة).
 *
 * @param string $name اسم الصورة.
 * @return array
 */
function shami_img_meta( $name ) {
	$map = array(
		'hero'            => array( 2400, 1340 ),
		'hero-m'          => array( 1100, 1366 ),
		'cat-cake'        => array( 900, 1117 ),
		'cat-biscuits'    => array( 900, 1117 ),
		'cat-chips'       => array( 900, 1117 ),
		'cat-snacks'      => array( 900, 1117 ),
		'cat-offers'      => array( 900, 1117 ),
		'seg-supermarket' => array( 1200, 805 ),
		'seg-grocery'     => array( 1200, 805 ),
		'seg-distributor' => array( 1200, 805 ),
		'seg-export'      => array( 1600, 1073 ),
		'step-order'      => array( 1000, 671 ),
		'step-pick'       => array( 1000, 671 ),
		'step-deliver'    => array( 1000, 671 ),
		'about-team'      => array( 1600, 893 ),
		'sourcing'        => array( 1400, 939 ),
		'quality'         => array( 1000, 671 ),
		'cta-docks'       => array( 2400, 1018 ),
		'texture'         => array( 1600, 893 ),
		'flatlay'         => array( 1800, 1005 ),
	);
	$stored = function_exists( 'shami_site_images_stored' ) ? shami_site_images_stored() : array();
	if ( ! empty( $stored[ $name ]['w'] ) ) {
		return array( (int) $stored[ $name ]['w'], (int) $stored[ $name ]['h'] );
	}
	return isset( $map[ $name ] ) ? $map[ $name ] : array( 1200, 800 );
}

/**
 * رابط صورة من صور الموقع (assets/img/site) بصيغة WebP.
 *
 * @param string $name  اسم الصورة.
 * @param bool   $small النسخة الصغيرة (نصف العرض).
 * @return string
 */
function shami_img_url( $name, $small = false ) {
	// النسخة المجلوبة إلى الاستضافة (المظهر ← صور الموقع) تتقدم على المضمّنة.
	$imported = function_exists( 'shami_site_image_imported_url' ) ? shami_site_image_imported_url( $name, $small ) : '';
	if ( $imported ) {
		return $imported;
	}
	return SHAMI_URI . '/assets/img/site/' . $name . ( $small ? '-sm' : '' ) . '.webp';
}

/**
 * وسم صورة من صور الموقع بنسختين (srcset) وأبعاد ثابتة لمنع اهتزاز الصفحة.
 *
 * @param string $name اسم الصورة.
 * @param string $alt  النص البديل (فارغ للصور الزخرفية).
 * @param array  $args class, sizes, loading (lazy|eager), fetchpriority.
 * @return string
 */
function shami_img( $name, $alt = '', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'         => '',
			'sizes'         => '100vw',
			'loading'       => 'lazy',
			'fetchpriority' => '',
		)
	);
	list( $w, $h ) = shami_img_meta( $name );
	$html          = sprintf(
		'<img src="%1$s" srcset="%2$s %3$dw, %1$s %4$dw" sizes="%5$s" width="%4$d" height="%6$d" alt="%7$s" decoding="async"',
		esc_url( shami_img_url( $name ) ),
		esc_url( shami_img_url( $name, true ) ),
		(int) round( $w / 2 ),
		(int) $w,
		esc_attr( $args['sizes'] ),
		(int) $h,
		esc_attr( $alt )
	);
	if ( $args['class'] ) {
		$html .= ' class="' . esc_attr( $args['class'] ) . '"';
	}
	if ( 'eager' !== $args['loading'] ) {
		$html .= ' loading="lazy"';
	}
	if ( $args['fetchpriority'] ) {
		$html .= ' fetchpriority="' . esc_attr( $args['fetchpriority'] ) . '"';
	}
	return $html . '>';
}

/**
 * رابط قسم منتجات حسب الاسم اللطيف (slug).
 *
 * @param string $slug slug.
 * @return string
 */
function shami_cat_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return home_url( '/product-category/' . $slug . '/' );
}

/**
 * عدد المنتجات في قسم.
 *
 * @param string $slug slug.
 * @return int
 */
function shami_cat_count( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return ( $term && ! is_wp_error( $term ) ) ? (int) $term->count : 0;
}

/**
 * العلامة التجارية للمنتج (slug).
 *
 * @param int $product_id رقم المنتج.
 * @return string
 */
function shami_product_brand( $product_id ) {
	$brand = get_post_meta( $product_id, '_shami_brand', true );
	if ( $brand ) {
		return $brand;
	}
	if ( taxonomy_exists( 'product_brand' ) ) {
		$terms = get_the_terms( $product_id, 'product_brand' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			return $terms[0]->slug;
		}
	}
	return '';
}

/**
 * القسم الرئيسي للمنتج (أحد أقسام المتجر، مع تجاهل العروض).
 *
 * @param int $product_id رقم المنتج.
 * @return string
 */
function shami_product_main_cat( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return 'snacks';
	}
	$slugs = wp_list_pluck( $terms, 'slug' );
	foreach ( array( 'cake', 'biscuits', 'chips', 'snacks' ) as $slug ) {
		if ( in_array( $slug, $slugs, true ) ) {
			return $slug;
		}
	}
	return in_array( 'offers', $slugs, true ) ? 'offers' : 'snacks';
}

/**
 * بيانات الجملة الخاصة بالمنتج.
 *
 * @param int $product_id رقم المنتج.
 * @return array
 */
function shami_product_info( $product_id ) {
	return array(
		'pack'   => (string) get_post_meta( $product_id, '_shami_pack', true ),
		'units'  => (int) get_post_meta( $product_id, '_shami_units', true ),
		'flavor' => (string) get_post_meta( $product_id, '_shami_flavor', true ),
		'line'   => (string) get_post_meta( $product_id, '_shami_line', true ),
		'tr'     => (string) get_post_meta( $product_id, '_shami_tr', true ),
		'brand'  => shami_product_brand( $product_id ),
		'cat'    => shami_product_main_cat( $product_id ),
	);
}

/**
 * سعر نصي بدون HTML (لرسائل واتساب).
 *
 * @param float $amount المبلغ.
 * @return string
 */
function shami_money_plain( $amount ) {
	if ( ! function_exists( 'wc_price' ) ) {
		return (string) $amount;
	}
	return trim( html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' ) );
}

/**
 * هل الأسعار ظاهرة للزوار؟
 *
 * @return bool
 */
function shami_show_prices() {
	return (bool) shami_opt( 'show_prices' );
}

/**
 * كميات المنتجات الموجودة في السلة حالياً.
 *
 * @return array product_id => qty
 */
function shami_cart_qty_map() {
	$map = array();
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $map;
	}
	foreach ( WC()->cart->get_cart() as $item ) {
		$pid         = (int) $item['product_id'];
		$map[ $pid ] = ( isset( $map[ $pid ] ) ? $map[ $pid ] : 0 ) + (int) $item['quantity'];
	}
	return $map;
}

/**
 * روابط التواصل الاجتماعي المفعّلة.
 *
 * @return array
 */
function shami_socials() {
	$out = array();
	foreach ( array( 'instagram', 'facebook', 'tiktok', 'telegram', 'youtube' ) as $net ) {
		$url = shami_opt( $net );
		if ( $url ) {
			$out[ $net ] = $url;
		}
	}
	return $out;
}

/**
 * محتوى ملف SVG من مجلد الهوية (يُقرأ مرة واحدة لكل طلب).
 *
 * @param string $file اسم الملف.
 * @return string
 */
function shami_brand_svg( $file ) {
	static $cache = array();
	if ( ! isset( $cache[ $file ] ) ) {
		$path           = SHAMI_DIR . '/assets/img/brand/' . $file;
		$cache[ $file ] = is_readable( $path ) ? trim( (string) file_get_contents( $path ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	return $cache[ $file ];
}

/**
 * الشعار: قوس دمشقي بحرف «ش» (يستحضر خانات التجارة القديمة) + كلمة «الشامي».
 *
 * @param string $variant light للخلفيات الفاتحة، dark للخلفيات الداكنة.
 * @param bool   $tagline إظهار السطر اللاتيني تحت الاسم.
 */
function shami_logo( $variant = 'light', $tagline = false ) {
	if ( has_custom_logo() && 'light' === $variant ) {
		the_custom_logo();
		return;
	}
	$file = ( $tagline ? 'logo-tag' : 'logo' ) . ( 'dark' === $variant ? '-dark' : '' ) . '.svg';
	$svg  = preg_replace( '/^<svg /', '<svg class="sh-logo__svg" aria-hidden="true" focusable="false" ', shami_brand_svg( $file ) );
	printf(
		'<a class="sh-logo sh-logo--%1$s" href="%2$s" rel="home" aria-label="%3$s">%4$s</a>',
		esc_attr( $variant ),
		esc_url( home_url( '/' ) ),
		esc_attr( get_bloginfo( 'name' ) . ' — الصفحة الرئيسية' ),
		$svg // phpcs:ignore WordPress.Security.EscapeOutput -- ملف SVG ثابت من القالب.
	);
}

/**
 * رمز الهوية (القوس وحده) للأيقونة وصفحة 404.
 *
 * @param string $class كلاس إضافي.
 * @return string
 */
function shami_logo_mark( $class = 'sh-logo__mark' ) {
	return preg_replace( '/^<svg /', '<svg class="' . esc_attr( $class ) . '" aria-hidden="true" focusable="false" ', shami_brand_svg( 'mark.svg' ) );
}

/**
 * صيغة العدد العربية لكلمة «صنف».
 *
 * @param int $n العدد.
 * @return string
 */
function shami_n_items( $n ) {
	$n = (int) $n;
	if ( 0 === $n ) {
		return 'لا توجد أصناف';
	}
	if ( 1 === $n ) {
		return 'صنف واحد';
	}
	if ( 2 === $n ) {
		return 'صنفان';
	}
	$mod = $n % 100;
	if ( $mod >= 3 && $mod <= 10 ) {
		return $n . ' أصناف';
	}
	if ( $mod >= 11 && $mod <= 99 ) {
		return $n . ' صنفاً';
	}
	return $n . ' صنف';
}

/**
 * عدد المنتجات المنشورة.
 *
 * @return int
 */
function shami_products_total() {
	$counts = wp_count_posts( 'product' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * كميات السلة مع تخزين مؤقت لطلب الصفحة الحالي.
 *
 * @return array
 */
function shami_cart_qty_cached() {
	static $map = null;
	if ( null === $map ) {
		$map = shami_cart_qty_map();
	}
	return $map;
}

/**
 * رابط صفحة شركة (علامة تجارية).
 *
 * @param string $slug slug.
 * @return string
 */
function shami_brand_url( $slug ) {
	if ( taxonomy_exists( 'product_brand' ) ) {
		$link = get_term_link( $slug, 'product_brand' );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return add_query_arg( 'company', $slug, function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
}

/**
 * كل الشركات في المتجر: العلامات المعرّفة في القالب + أي علامة يضيفها المدير من «المنتجات ← العلامات».
 *
 * @return array slug => [ar, alt, latin, c1, c2, about, count, url]
 */
function shami_all_brands() {
	static $out = null;
	if ( null !== $out ) {
		return $out;
	}
	$known = shami_brands();
	$out   = array();
	$terms = taxonomy_exists( 'product_brand' ) ? get_terms(
		array(
			'taxonomy'   => 'product_brand',
			'hide_empty' => false,
		)
	) : array();
	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}
	foreach ( $known as $slug => $b ) {
		$out[ $slug ] = $b + array(
			'count' => 0,
			'url'   => '',
		);
	}
	foreach ( $terms as $t ) {
		if ( ! isset( $out[ $t->slug ] ) ) {
			$out[ $t->slug ] = array(
				'ar'    => $t->name,
				'alt'   => $t->name,
				'latin' => $t->name,
				'c1'    => '#124A39',
				'c2'    => '#B8904F',
				'about' => wp_strip_all_tags( $t->description ),
			);
		}
		$out[ $t->slug ]['count'] = (int) $t->count;
	}
	foreach ( $out as $slug => $b ) {
		$out[ $slug ]['url'] = shami_brand_url( $slug );
	}
	return $out;
}
