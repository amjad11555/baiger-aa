<?php
/**
 * دوال مساعدة عامة.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

/**
 * القيم الافتراضية لإعدادات القالب (قابلة للتعديل من المخصّص).
 *
 * @return array
 */
function lazza_defaults() {
	return array(
		'whatsapp'      => '',
		'phone'         => '',
		'email'         => '',
		'address'       => '',
		'city'          => 'إسطنبول',
		'hours'         => 'يومياً من 9 صباحاً حتى 9 مساءً',
		'announcement'  => 'توصيل سريع لجميع البقاليات • الدفع عند الاستلام • اطلب بالكرتونة عبر الموقع أو واتساب',
		'show_prices'   => true,
		'min_order'     => 0,
		'free_delivery' => 0,
		'hero_kicker'   => 'موزّع جملة للبقاليات والماركت',
		'hero_title'    => 'كل ما تحتاجه بقالتك من الحلويات… بضغطة واحدة',
		'hero_text'     => 'كيك وبسكويت وشيبس وتسالي من إيتي وأولكر وبونوتشي بأسعار الجملة. حدّد الكميات بالكرتونة وأرسل طلبك خلال دقيقة، ونوصله إلى باب محلّك.',
		'offer_end'     => '',
		'force_rtl'     => true,
		'instagram'     => '',
		'facebook'      => '',
		'tiktok'        => '',
		'telegram'      => '',
		'youtube'       => '',
		'seo_tagline'   => 'حلويات وبسكويت وشيبس وتسالي تركية بالجملة للبقالات',
	);
}

/**
 * قراءة إعداد من إعدادات القالب.
 *
 * @param string $key     المفتاح.
 * @param mixed  $default قيمة بديلة.
 * @return mixed
 */
function lazza_opt( $key, $default = null ) {
	$defaults = lazza_defaults();
	if ( null === $default && array_key_exists( $key, $defaults ) ) {
		$default = $defaults[ $key ];
	}
	return get_theme_mod( 'lazza_' . $key, $default );
}

/**
 * رقم واتساب بصيغة دولية أرقام فقط.
 *
 * @return string
 */
function lazza_wa_number() {
	$num = preg_replace( '/\D+/', '', (string) lazza_opt( 'whatsapp' ) );
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
function lazza_wa_link( $text = '' ) {
	$num = lazza_wa_number();
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
function lazza_special_pages() {
	return array(
		'quick_order'     => array( 'quick-order', 'page-templates/template-quick-order.php', 'الطلب السريع للبقاليات' ),
		'special_request' => array( 'special-request', 'page-templates/template-special-request.php', 'اطلب منتجاً غير متوفر' ),
		'export'          => array( 'wholesale-export', 'page-templates/template-export.php', 'الجملة الدولية والتصدير خارج تركيا' ),
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
function lazza_page_url( $key ) {
	static $cache = array();
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$pages = lazza_special_pages();
	$url   = '';
	$id    = (int) get_option( 'lazza_page_' . $key );
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
function lazza_brands() {
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
function lazza_categories() {
	return array(
		'cake'     => array(
			'name'  => 'كيك',
			'icon'  => 'cake',
			'color' => '#CE0006',
			'tint'  => '#FFE8E3',
			'line'  => 'كيك طري ومحشي بالشوكولاتة والفواكه',
		),
		'biscuits' => array(
			'name'  => 'بسكويت',
			'icon'  => 'cookie',
			'color' => '#B86A12',
			'tint'  => '#FFF0D9',
			'line'  => 'بسكويت وويفر وكوكيز لكل الأذواق',
		),
		'chips'    => array(
			'name'  => 'شيبسات',
			'icon'  => 'chips',
			'color' => '#E39B00',
			'tint'  => '#FFF6D6',
			'line'  => 'شيبس ذرة وكراكرز وأصابع مملحة',
		),
		'snacks'   => array(
			'name'  => 'تسالي',
			'icon'  => 'candy',
			'color' => '#6B2F14',
			'tint'  => '#F6E6DC',
			'line'  => 'شوكولاتة وألواح ومارشميلو وحلوى',
		),
		'offers'   => array(
			'name'  => 'عروض',
			'icon'  => 'percent',
			'color' => '#E4002B',
			'tint'  => '#FFE3E8',
			'line'  => 'تخفيضات وباقات جملة موفّرة',
		),
	);
}

/**
 * رابط قسم منتجات حسب الاسم اللطيف (slug).
 *
 * @param string $slug slug.
 * @return string
 */
function lazza_cat_url( $slug ) {
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
function lazza_cat_count( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return ( $term && ! is_wp_error( $term ) ) ? (int) $term->count : 0;
}

/**
 * العلامة التجارية للمنتج (slug).
 *
 * @param int $product_id رقم المنتج.
 * @return string
 */
function lazza_product_brand( $product_id ) {
	$brand = get_post_meta( $product_id, '_lazza_brand', true );
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
function lazza_product_main_cat( $product_id ) {
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
function lazza_product_info( $product_id ) {
	return array(
		'pack'   => (string) get_post_meta( $product_id, '_lazza_pack', true ),
		'units'  => (int) get_post_meta( $product_id, '_lazza_units', true ),
		'flavor' => (string) get_post_meta( $product_id, '_lazza_flavor', true ),
		'line'   => (string) get_post_meta( $product_id, '_lazza_line', true ),
		'tr'     => (string) get_post_meta( $product_id, '_lazza_tr', true ),
		'brand'  => lazza_product_brand( $product_id ),
		'cat'    => lazza_product_main_cat( $product_id ),
	);
}

/**
 * سعر نصي بدون HTML (لرسائل واتساب).
 *
 * @param float $amount المبلغ.
 * @return string
 */
function lazza_money_plain( $amount ) {
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
function lazza_show_prices() {
	return (bool) lazza_opt( 'show_prices' );
}

/**
 * كميات المنتجات الموجودة في السلة حالياً.
 *
 * @return array product_id => qty
 */
function lazza_cart_qty_map() {
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
function lazza_socials() {
	$out = array();
	foreach ( array( 'instagram', 'facebook', 'tiktok', 'telegram', 'youtube' ) as $net ) {
		$url = lazza_opt( $net );
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
function lazza_offer_end_iso() {
	$custom = lazza_opt( 'offer_end' );
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
 * طباعة شعار نصي في حال عدم رفع شعار.
 */
function lazza_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$name = get_bloginfo( 'name' );
	printf(
		'<a class="lz-logo" href="%1$s" rel="home" aria-label="%2$s"><span class="lz-logo__sun" aria-hidden="true">%3$s</span><span class="lz-logo__text">%4$s</span></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $name . ' — الصفحة الرئيسية' ),
		lazza_sun_svg(), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_html( $name )
	);
}

/**
 * قرص الشمس الزخرفي (هوية بصرية مستوحاة من الشمس الحثّية).
 *
 * @param int $rays عدد الأشعة.
 * @return string
 */
function lazza_sun_svg( $rays = 12 ) {
	$pts = '';
	for ( $i = 0; $i < $rays; $i++ ) {
		$a1   = deg2rad( ( 360 / $rays ) * $i - 7 );
		$a2   = deg2rad( ( 360 / $rays ) * $i + 7 );
		$a3   = deg2rad( ( 360 / $rays ) * $i );
		$pts .= sprintf(
			'<path d="M%.1f %.1f L%.1f %.1f L%.1f %.1f Z"/>',
			20 + 11 * cos( $a1 ),
			20 + 11 * sin( $a1 ),
			20 + 19 * cos( $a3 ),
			20 + 19 * sin( $a3 ),
			20 + 11 * cos( $a2 ),
			20 + 11 * sin( $a2 )
		);
	}
	return '<svg viewBox="0 0 40 40" width="40" height="40" focusable="false"><g class="lz-sun-rays">' . $pts . '</g><circle cx="20" cy="20" r="9.5" class="lz-sun-core"/></svg>';
}

/**
 * صيغة العدد العربية لكلمة «صنف».
 *
 * @param int $n العدد.
 * @return string
 */
function lazza_n_items( $n ) {
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
function lazza_products_total() {
	$counts = wp_count_posts( 'product' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * كميات السلة مع تخزين مؤقت لطلب الصفحة الحالي.
 *
 * @return array
 */
function lazza_cart_qty_cached() {
	static $map = null;
	if ( null === $map ) {
		$map = lazza_cart_qty_map();
	}
	return $map;
}
