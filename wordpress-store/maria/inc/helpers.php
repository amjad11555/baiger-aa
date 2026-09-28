<?php
/**
 * دوال مساعدة عامة.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

/**
 * القيم الافتراضية لإعدادات القالب (قابلة للتعديل من المخصّص).
 *
 * @return array
 */
function maria_defaults() {
	return array(
		'whatsapp'      => '',
		'phone'         => '',
		'email'         => '',
		'address'       => '',
		'city'          => 'إسطنبول',
		'hours'         => 'يومياً من 9 صباحاً حتى 9 مساءً',
		'announcement'  => 'توصيل إلى باب محلّك · الدفع عند الاستلام · الطلب من كرتونة واحدة',
		'show_prices'   => true,
		'min_order'     => 0,
		'free_delivery' => 0,
		'hero_kicker'   => 'جملة الحلويات التركية للبقالات والماركت',
		'hero_title'    => 'حلويات تركية أصلية… بسعر الجملة',
		'hero_text'     => 'كيك وبسكويت وشيبس وتسالي من إيتي وأولكر وبونوتشي، بصلاحية حديثة وسعر كرتونة واضح. اختر أصنافك وأرسل طلبك في دقيقة، ونوصله إلى محلّك والدفع عند الاستلام.',
		'offer_end'     => '',
		'force_rtl'     => true,
		'instagram'     => '',
		'facebook'      => '',
		'tiktok'        => '',
		'telegram'      => '',
		'youtube'       => '',
		'seo_tagline'   => 'ماريا للتجارة: حلويات وبسكويت وشيبس وتسالي تركية أصلية بالجملة للبقالات',
	);
}

/**
 * قراءة إعداد من إعدادات القالب.
 *
 * @param string $key     المفتاح.
 * @param mixed  $default قيمة بديلة.
 * @return mixed
 */
function maria_opt( $key, $default = null ) {
	$defaults = maria_defaults();
	if ( null === $default && array_key_exists( $key, $defaults ) ) {
		$default = $defaults[ $key ];
	}
	return get_theme_mod( 'maria_' . $key, $default );
}

/**
 * رقم واتساب بصيغة دولية أرقام فقط.
 *
 * @return string
 */
function maria_wa_number() {
	$num = preg_replace( '/\D+/', '', (string) maria_opt( 'whatsapp' ) );
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
function maria_wa_link( $text = '' ) {
	$num = maria_wa_number();
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
function maria_special_pages() {
	return array(
		'quick_order'     => array( 'quick-order', 'page-templates/template-quick-order.php', 'قائمة الطلب السريع' ),
		'special_request' => array( 'special-request', 'page-templates/template-special-request.php', 'اطلب منتجاً غير متوفر' ),
		'export'          => array( 'wholesale-export', 'page-templates/template-export.php', 'الجملة الدولية والتصدير خارج تركيا' ),
		'brands'          => array( 'brands', 'page-templates/template-brands.php', 'الشركات والعلامات التجارية' ),
		'contact'         => array( 'contact', 'page-templates/template-contact.php', 'تواصل معنا' ),
		'about'           => array( 'about', '', 'من نحن' ),
		'delivery'        => array( 'delivery-returns', '', 'التوصيل والدفع والإرجاع' ),
	);
}

/**
 * رابط صفحة خاصة بالقالب.
 *
 * @param string $key مفتاح الصفحة.
 * @return string
 */
function maria_page_url( $key ) {
	static $cache = array();
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$pages = maria_special_pages();
	$url   = '';
	$id    = (int) get_option( 'maria_page_' . $key );
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
function maria_brands() {
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
function maria_categories() {
	return array(
		'cake'     => array(
			'name'  => 'كيك',
			'icon'  => 'cake',
			'color' => '#006C78',
			'tint'  => '#E0F1F2',
			'line'  => 'طري ومحشو، يُباع بسرعة',
		),
		'biscuits' => array(
			'name'  => 'بسكويت',
			'icon'  => 'cookie',
			'color' => '#8C51FF',
			'tint'  => '#EFE7FF',
			'line'  => 'للشاي والضيافة والمدارس',
		),
		'chips'    => array(
			'name'  => 'شيبسات',
			'icon'  => 'chips',
			'color' => '#FF863B',
			'tint'  => '#FFEDE1',
			'line'  => 'تسالي مالحة للسهرة',
		),
		'snacks'   => array(
			'name'  => 'تسالي',
			'icon'  => 'candy',
			'color' => '#1CB3D2',
			'tint'  => '#E1F5FA',
			'line'  => 'شوكولاتة وحلوى للكاشير',
		),
		'offers'   => array(
			'name'  => 'عروض',
			'icon'  => 'percent',
			'color' => '#EB1C24',
			'tint'  => '#FDE4E5',
			'line'  => 'خصومات وباقات موفّرة',
		),
	);
}

/**
 * رابط قسم منتجات حسب الاسم اللطيف (slug).
 *
 * @param string $slug slug.
 * @return string
 */
function maria_cat_url( $slug ) {
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
function maria_cat_count( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return ( $term && ! is_wp_error( $term ) ) ? (int) $term->count : 0;
}

/**
 * العلامة التجارية للمنتج (slug).
 *
 * @param int $product_id رقم المنتج.
 * @return string
 */
function maria_product_brand( $product_id ) {
	$brand = get_post_meta( $product_id, '_maria_brand', true );
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
function maria_product_main_cat( $product_id ) {
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
function maria_product_info( $product_id ) {
	return array(
		'pack'   => (string) get_post_meta( $product_id, '_maria_pack', true ),
		'units'  => (int) get_post_meta( $product_id, '_maria_units', true ),
		'flavor' => (string) get_post_meta( $product_id, '_maria_flavor', true ),
		'line'   => (string) get_post_meta( $product_id, '_maria_line', true ),
		'tr'     => (string) get_post_meta( $product_id, '_maria_tr', true ),
		'brand'  => maria_product_brand( $product_id ),
		'cat'    => maria_product_main_cat( $product_id ),
	);
}

/**
 * سعر نصي بدون HTML (لرسائل واتساب).
 *
 * @param float $amount المبلغ.
 * @return string
 */
function maria_money_plain( $amount ) {
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
function maria_show_prices() {
	return (bool) maria_opt( 'show_prices' );
}

/**
 * كميات المنتجات الموجودة في السلة حالياً.
 *
 * @return array product_id => qty
 */
function maria_cart_qty_map() {
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
function maria_socials() {
	$out = array();
	foreach ( array( 'instagram', 'facebook', 'tiktok', 'telegram', 'youtube' ) as $net ) {
		$url = maria_opt( $net );
		if ( $url ) {
			$out[ $net ] = $url;
		}
	}
	return $out;
}

/**
 * نهاية العرض الحالي (للعداد التنازلي) بصيغة ISO.
 *
 * @return string
 */
function maria_offer_end_iso() {
	$custom = maria_opt( 'offer_end' );
	$tz     = wp_timezone();
	if ( $custom ) {
		try {
			$date = new DateTime( $custom, $tz );
			if ( $date->getTimestamp() > time() ) {
				return $date->format( DATE_ATOM );
			}
		} catch ( Exception $e ) {
			unset( $e );
		}
	}
	// افتراضياً: نهاية يوم الخميس القادم (دورة عروض أسبوعية).
	$date = new DateTime( 'now', $tz );
	$date->modify( 'next thursday' )->setTime( 23, 59, 59 );
	return $date->format( DATE_ATOM );
}

/**
 * الشعار: شارة حمراء بكلمة «ماريا» بخط عريض + اسم لاتيني صغير.
 */
function maria_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$name = get_bloginfo( 'name' );
	printf(
		'<a class="mr-logo" href="%1$s" rel="home" aria-label="%2$s"><span class="mr-logo__name">%3$s</span><span class="mr-logo__sub" lang="en">%4$s</span></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $name . ' — الصفحة الرئيسية' ),
		esc_html( $name ),
		esc_html( apply_filters( 'maria_logo_sub', 'MARIA TRADE' ) )
	);
}

/**
 * رمز الموقع: حلوى مغلّفة بيضاء على مربع أحمر (للأيقونة وصفحة 404).
 *
 * @param string $class كلاس إضافي.
 * @return string
 */
function maria_logo_mark( $class = 'mr-logo__mark' ) {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true" focusable="false">'
		. '<rect width="48" height="48" rx="12" fill="#EB1C24"/>'
		. '<g fill="#fff"><ellipse cx="24" cy="24" rx="9.5" ry="8"/><path d="M15.5 24 6 16.5l2.4 7.5L6 31.5Z"/><path d="M32.5 24 42 16.5 39.6 24 42 31.5Z"/></g>'
		. '<path d="M21.5 16.8c-2.6 4.6-2.6 9.8 0 14.4M26.5 16.8c2.6 4.6 2.6 9.8 0 14.4" fill="none" stroke="#EB1C24" stroke-width="2.2" stroke-linecap="round"/></svg>';
}

/**
 * صيغة العدد العربية لكلمة «صنف».
 *
 * @param int $n العدد.
 * @return string
 */
function maria_n_items( $n ) {
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
function maria_products_total() {
	$counts = wp_count_posts( 'product' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * كميات السلة مع تخزين مؤقت لطلب الصفحة الحالي.
 *
 * @return array
 */
function maria_cart_qty_cached() {
	static $map = null;
	if ( null === $map ) {
		$map = maria_cart_qty_map();
	}
	return $map;
}

/**
 * رابط صفحة شركة (علامة تجارية).
 *
 * @param string $slug slug.
 * @return string
 */
function maria_brand_url( $slug ) {
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
function maria_all_brands() {
	static $out = null;
	if ( null !== $out ) {
		return $out;
	}
	$known = maria_brands();
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
				'c1'    => '#EB1C24',
				'c2'    => '#500878',
				'about' => wp_strip_all_tags( $t->description ),
			);
		}
		$out[ $t->slug ]['count'] = (int) $t->count;
	}
	foreach ( $out as $slug => $b ) {
		$out[ $slug ]['url'] = maria_brand_url( $slug );
	}
	return $out;
}
