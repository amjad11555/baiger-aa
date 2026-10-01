<?php
/**
 * دوال مساعدة عامة.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * القيم الافتراضية لإعدادات القالب (قابلة للتعديل من المخصّص).
 *
 * @return array
 */
function zad_defaults() {
	return array(
		'whatsapp'        => '',
		'phone'           => '',
		'email'           => '',
		'address'         => '',
		'city'            => 'إسطنبول',
		'hours'           => 'السبت – الخميس · 9:00 – 19:00',
		'announcement'    => 'خصم دائم على إيتي وأولكر · توصيل مجاني لمحلات إسطنبول · نقداً عند الاستلام',
		'show_prices'     => true,
		'min_order'       => 0,
		'min_cartons'     => 15,
		'free_delivery'   => 0,
		'show_profit'     => true,
		'retail_margin'   => 25,
		'require_account' => true,
		'members_prices'  => true,
		'disc_eti'        => 2,
		'disc_ulker'      => 5,
		'hero_kicker'     => 'جملة إسطنبول · كيك وبسكويت وشيبس',
		'hero_title'      => 'رفّ محلك ممتلئ دائماً، بسعر الجملة',
		'hero_text'       => 'أكثر من 330 صنفاً من إيتي وأولكر وبونجو والوان وشولين ووينر، تصل إلى باب محلك في إسطنبول والدفع نقداً عند الاستلام.',
		'hero_image'      => '',
		'force_rtl'       => true,
		'instagram'       => '',
		'facebook'        => '',
		'tiktok'          => '',
		'telegram'        => '',
		'youtube'         => '',
		'seo_tagline'     => 'كيك وبسكويت وشيبس بالجملة في إسطنبول',
	);
}

/**
 * قراءة إعداد من إعدادات القالب.
 *
 * @param string $key     المفتاح.
 * @param mixed  $default قيمة بديلة.
 * @return mixed
 */
function zad_opt( $key, $default = null ) {
	$defaults = zad_defaults();
	if ( null === $default && array_key_exists( $key, $defaults ) ) {
		$default = $defaults[ $key ];
	}
	return get_theme_mod( 'zad_' . $key, $default );
}

/**
 * رقم واتساب بصيغة دولية أرقام فقط.
 *
 * @return string
 */
function zad_wa_number() {
	$num = preg_replace( '/\D+/', '', (string) zad_opt( 'whatsapp' ) );
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
function zad_wa_link( $text = '' ) {
	$num = zad_wa_number();
	$url = 'https://wa.me/' . $num;
	if ( '' !== $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

/**
 * esc_url() يحذف %0A (السطر الجديد) من أي رابط، فتصل رسائل واتساب متلاصقة الأسطر.
 * روابط wa.me التي نبنيها بـ rawurlencode آمنة أصلاً (أحرف وأرقام و%XX فقط)، فنعيدها كما هي.
 *
 * @param string $good     الرابط بعد التنظيف.
 * @param string $original الرابط الأصلي.
 * @return string
 */
function zad_keep_wa_newlines( $good, $original ) {
	if ( is_string( $original ) && preg_match( '#^https://wa\.me/\d*(\?text=[A-Za-z0-9%._~-]*)?$#', $original ) ) {
		return $original;
	}
	return $good;
}
add_filter( 'clean_url', 'zad_keep_wa_newlines', 10, 2 );

/**
 * الصفحات الخاصة بالقالب: المفتاح => [المسار، القالب، العنوان].
 *
 * @return array
 */
function zad_special_pages() {
	return array(
		'quick_order'     => array( 'quick-order', 'page-templates/template-quick-order.php', 'قائمة أسعار الجملة' ),
		'special_request' => array( 'special-request', 'page-templates/template-special-request.php', 'طلب توريد خاص' ),
		'export'          => array( 'wholesale-export', 'page-templates/template-export.php', 'التصدير والجملة الدولية' ),
		'brands'          => array( 'brands', 'page-templates/template-brands.php', 'العلامات التجارية' ),
		'contact'         => array( 'contact', 'page-templates/template-contact.php', 'تواصل مع المبيعات' ),
		'about'           => array( 'about', '', 'عن بسكاتو' ),
		'delivery'        => array( 'delivery-returns', '', 'التوريد والدفع والإرجاع' ),
	);
}

/**
 * تاريخ اليوم بأسماء الأشهر العربية، مهما كانت لغة الموقع.
 *
 * @return string مثل: 28 سبتمبر 2026.
 */
function zad_ar_date() {
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
function zad_page_image( $page_id, $field = 'image' ) {
	$map   = array(
		'about'    => array( 'about-team', 'بسكاتو للتجارة · جملة وتوزيع' ),
		'delivery' => array( 'banner-delivery', 'التوصيل في إسطنبول والدفع' ),
	);
	$pages = zad_special_pages();
	foreach ( $map as $key => $row ) {
		$match = (int) get_option( 'zad_page_' . $key ) === (int) $page_id;
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
function zad_page_url( $key ) {
	static $cache = array();
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$pages = zad_special_pages();
	if ( ! $cache ) {
		// تحميل كل الصفحات الخاصة باستعلام واحد بدل استعلام لكل رابط في الهيدر والفوتر.
		$ids = array();
		foreach ( array_keys( $pages ) as $k ) {
			$ids[] = (int) get_option( 'zad_page_' . $k );
		}
		$ids = array_filter( $ids );
		if ( $ids ) {
			_prime_post_caches( $ids, false, false );
		}
	}
	$url = '';
	$id  = (int) get_option( 'zad_page_' . $key );
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
function zad_brands() {
	static $out = null;
	if ( null !== $out ) {
		return $out;
	}
	$out = array(
		'eti'     => array(
			'ar'    => 'إيتي',
			'alt'   => 'ايتي',
			'latin' => 'Eti',
			'c1'       => '#CE0006',
			'c2'       => '#EFB455',
			'about'    => 'علامة تركية عريقة تأسست عام 1962 في إسكي شهير، ومن أشهر منتجاتها براوني وبوب كيك وبورشاك وتوتكو وجين وكراكس وهوشبيش.',
			// الشعار (SVG) ولافتة صفحة العلامة.
			'logo'     => array( 'eti.svg', 840, 540 ),
			'hero'     => 'eti-cikolata',
		),
		'ulker'   => array(
			'ar'    => 'أولكر',
			'alt'   => 'اولكر',
			'latin' => 'Ülker',
			'c1'    => '#1F3C88',
			'c2'    => '#F4C542',
			'about' => 'من أقدم وأكبر شركات البسكويت والشوكولاتة في تركيا منذ 1944، صاحبة بيسكريم وهاللي وهانيملار وألبيني وميترو وديدو.',
		),
		'bonucci' => array(
			'ar'    => 'بونجو',
			'alt'   => 'بونوتشي',
			'latin' => 'Bonucci',
			'c1'    => '#4A2412',
			'c2'    => '#E6B36A',
			'about' => 'علامة تركية متخصصة في الكيك المحشو، ومن منتجاتها دولسي رول وشوكيكس وبيستاه ولوبيكس وسيكريت.',
		),
	);
	// باقي العلامات من ملف البيانات (الاسم والألوان)، ونص تعريفي عام.
	foreach ( (array) include ZAD_DIR . '/inc/data/brands.php' as $slug => $b ) {
		if ( ! isset( $out[ $slug ] ) ) {
			$out[ $slug ] = array(
				'ar'    => $b[0],
				'alt'   => $b[0],
				'latin' => $b[1],
				'c1'    => $b[2],
				'c2'    => $b[3],
				'about' => isset( $b[4] ) ? $b[4] : '',
			);
		}
	}
	return $out;
}

/**
 * شعار العلامة إن توفّر (وإلا نص فارغ ليُعرض الاسم بدلاً منه).
 *
 * @param array  $brand  بيانات العلامة من zad_all_brands().
 * @param int    $height الارتفاع بالبكسل.
 * @param string $class  صنف إضافي.
 * @return string
 */
function zad_brand_logo( $brand, $height = 36, $class = '' ) {
	if ( empty( $brand['logo'] ) ) {
		return '';
	}
	list( $file, $w, $h ) = $brand['logo'];
	return sprintf(
		'<img class="zd-brand-logo%1$s" src="%2$s" width="%3$d" height="%4$d" alt="%5$s" loading="lazy" decoding="async">',
		$class ? ' ' . esc_attr( $class ) : '',
		esc_url( ZAD_URI . '/assets/img/brands/' . $file ),
		(int) round( $w * $height / $h ),
		(int) $height,
		esc_attr( 'شعار ' . $brand['ar'] . ' ' . $brand['latin'] )
	);
}

/**
 * أقسام المتجر الرئيسية بالترتيب.
 *
 * @return array
 */
function zad_categories() {
	return array(
		'cake'     => array(
			'name'  => 'كيك',
			'title' => 'الكيك والرول',
			'icon'  => 'cake',
			'image' => 'tile-cake',
			'color' => '#124A39',
			'tint'  => '#E4EDE8',
			'line'  => 'سويس رول وكروسان وبراوني ومحشو، الأسرع دوراناً على الرف',
		),
		'biscuits' => array(
			'name'  => 'بسكويت',
			'title' => 'البسكويت والكوكيز',
			'icon'  => 'cookie',
			'image' => 'tile-biscuits',
			'color' => '#7A5A2B',
			'tint'  => '#F1E8D8',
			'line'  => 'محشو وسادة ومغطّى، طلبه ثابت على مدار السنة',
		),
		'snacks'   => array(
			'name'  => 'شوكولاتة',
			'title' => 'الشوكولاتة والويفر',
			'icon'  => 'candy',
			'image' => 'tile-snacks',
			'color' => '#4B2A1C',
			'tint'  => '#EFE4DC',
			'line'  => 'ألواح وويفر محشو، أصناف الكاشير والشراء السريع',
		),
		'chips'    => array(
			'name'  => 'شيبس',
			'title' => 'الشيبس والمقرمشات',
			'icon'  => 'chips',
			'image' => 'tile-chips',
			'color' => '#8A5A1E',
			'tint'  => '#F4EAD9',
			'line'  => 'شيبس وينر وكراكس وغونغ، تسالي مالحة بهامش جيد',
		),
		'candy'    => array(
			'name'  => 'سكاكر',
			'title' => 'السكاكر والجيلي',
			'icon'  => 'lollipop',
			'image' => 'tile-candy',
			'color' => '#A0225A',
			'tint'  => '#FBE6EF',
			'line'  => 'جيلي ومارشميللو ومصاصات، يطلبها الأطفال كل يوم',
		),
		'gum'      => array(
			'name'  => 'علكة',
			'title' => 'العلكة',
			'icon'  => 'gum',
			'image' => 'tile-gum',
			'color' => '#1F6E8C',
			'tint'  => '#E2F2F7',
			'line'  => 'فيرست ونازار وتاكسي وفالم، صنف الكاشير الأول',
		),
		'toys'     => array(
			'name'  => 'ألعاب',
			'title' => 'الألعاب والمفاجآت',
			'icon'  => 'egg',
			'image' => 'tile-toys',
			'color' => '#5B3FA0',
			'tint'  => '#ECE7F8',
			'line'  => 'بيض المفاجآت والظروف والألعاب الصغيرة للأطفال',
		),
		'drinks'   => array(
			'name'  => 'مشروبات',
			'title' => 'العصائر والشاي',
			'icon'  => 'cup',
			'image' => 'tile-drinks',
			'color' => '#B4541A',
			'tint'  => '#FBEADF',
			'line'  => 'عصائر جانم وسبيكو وشاي الفواكه',
		),
		'offers'   => array(
			'name'  => 'عروض',
			'title' => 'عروض الجملة',
			'icon'  => 'percent',
			'image' => 'tile-offers',
			'color' => '#9A3B22',
			'tint'  => '#F5E3DC',
			'line'  => 'خصم إيتي وأولكر الدائم، وعروض الأسبوع',
		),
	);
}

/**
 * أبعاد صور الموقع (عرض × ارتفاع النسخة الكبيرة).
 *
 * @param string $name اسم الصورة.
 * @return array
 */
function zad_img_meta( $name ) {
	$map = array(
		'seg-supermarket'   => array( 1200, 805 ),
		'seg-export'        => array( 1600, 1073 ),
		'about-team'        => array( 1600, 893 ),
		'sourcing'          => array( 1400, 939 ),
		'eti-cikolata'      => array( 1600, 1740 ),
		'hero-v5'           => array( 1600, 1200 ),
		'banner-delivery'   => array( 1400, 780 ),
		'tile-snacks'       => array( 900, 900 ),
		'tile-biscuits'     => array( 900, 900 ),
		'tile-cake'         => array( 900, 900 ),
		'tile-chips'        => array( 900, 900 ),
		'tile-offers'       => array( 900, 900 ),
		'tile-candy'        => array( 900, 900 ),
		'tile-gum'          => array( 900, 900 ),
		'tile-toys'         => array( 900, 900 ),
		'tile-drinks'       => array( 900, 900 ),
		'stage-order'       => array( 1200, 800 ),
		'stage-pick'        => array( 1200, 800 ),
		'stage-deliver'     => array( 1200, 800 ),
	);
	$stored = function_exists( 'zad_site_images_stored' ) ? zad_site_images_stored() : array();
	if ( ! empty( $stored[ $name ]['w'] ) && function_exists( 'zad_site_image_imported_url' ) && zad_site_image_imported_url( $name ) ) {
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
function zad_img_url( $name, $small = false ) {
	// النسخة المجلوبة إلى الاستضافة (المظهر ← صور الموقع) تتقدم على المضمّنة.
	$imported = function_exists( 'zad_site_image_imported_url' ) ? zad_site_image_imported_url( $name, $small ) : '';
	if ( $imported ) {
		return $imported;
	}
	return ZAD_URI . '/assets/img/site/' . $name . ( $small ? '-sm' : '' ) . '.webp';
}

/**
 * وسم صورة من صور الموقع بنسختين (srcset) وأبعاد ثابتة لمنع اهتزاز الصفحة.
 *
 * @param string $name اسم الصورة.
 * @param string $alt  النص البديل (فارغ للصور الزخرفية).
 * @param array  $args class, sizes, loading (lazy|eager), fetchpriority.
 * @return string
 */
function zad_img( $name, $alt = '', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'         => '',
			'sizes'         => '100vw',
			'loading'       => 'lazy',
			'fetchpriority' => '',
		)
	);
	list( $w, $h ) = zad_img_meta( $name );
	$html          = sprintf(
		'<img src="%1$s" srcset="%2$s %3$dw, %1$s %4$dw" sizes="%5$s" width="%4$d" height="%6$d" alt="%7$s" decoding="async"',
		esc_url( zad_img_url( $name ) ),
		esc_url( zad_img_url( $name, true ) ),
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
 * جلب تصنيف حسب الـ slug مع ذاكرة مؤقتة للطلب الحالي.
 *
 * تُحمَّل كل تصنيفات الأقسام/العلامات باستعلام واحد بدل استعلام لكل رابط
 * (الصفحة الرئيسية وحدها كانت تنفذ نحو 40 استعلاماً من هذا النوع).
 *
 * @param string $slug slug.
 * @param string $tax  التصنيف.
 * @return WP_Term|null
 */
function zad_term_by_slug( $slug, $tax = 'product_cat' ) {
	static $cache = array();
	if ( ! isset( $cache[ $tax ] ) ) {
		$cache[ $tax ] = array();
		if ( taxonomy_exists( $tax ) ) {
			$terms = get_terms(
				array(
					'taxonomy'               => $tax,
					'hide_empty'             => false,
					'update_term_meta_cache' => false,
				)
			);
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $t ) {
					$cache[ $tax ][ $t->slug ] = $t;
				}
			}
		}
	}
	if ( ! isset( $cache[ $tax ][ $slug ] ) ) {
		$t = get_term_by( 'slug', $slug, $tax );
		if ( ! $t || is_wp_error( $t ) ) {
			return null;
		}
		$cache[ $tax ][ $slug ] = $t;
	}
	return $cache[ $tax ][ $slug ];
}

/**
 * رابط قسم منتجات حسب الاسم اللطيف (slug).
 *
 * @param string $slug slug.
 * @return string
 */
function zad_cat_url( $slug ) {
	$term = zad_term_by_slug( $slug );
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
function zad_cat_count( $slug ) {
	$term = zad_term_by_slug( $slug );
	return ( $term && ! is_wp_error( $term ) ) ? (int) $term->count : 0;
}

/**
 * العلامة التجارية للمنتج (slug).
 *
 * @param int $product_id رقم المنتج.
 * @return string
 */
function zad_product_brand( $product_id ) {
	$brand = get_post_meta( $product_id, '_zad_brand', true );
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
function zad_product_main_cat( $product_id ) {
	$terms = get_the_terms( $product_id, 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return 'snacks';
	}
	$slugs = wp_list_pluck( $terms, 'slug' );
	foreach ( array_diff( array_keys( zad_categories() ), array( 'offers' ) ) as $slug ) {
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
function zad_product_info( $product_id ) {
	return array(
		'pack'   => (string) get_post_meta( $product_id, '_zad_pack', true ),
		'units'  => (int) get_post_meta( $product_id, '_zad_units', true ),
		'flavor' => (string) get_post_meta( $product_id, '_zad_flavor', true ),
		'line'   => (string) get_post_meta( $product_id, '_zad_line', true ),
		'tr'     => (string) get_post_meta( $product_id, '_zad_tr', true ),
		'brand'  => zad_product_brand( $product_id ),
		'cat'    => zad_product_main_cat( $product_id ),
	);
}

/**
 * سعر نصي بدون HTML (لرسائل واتساب).
 *
 * @param float $amount المبلغ.
 * @return string
 */
function zad_money_plain( $amount ) {
	if ( ! function_exists( 'wc_price' ) ) {
		return (string) $amount;
	}
	return trim( html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' ) );
}

/**
 * حالة الأسعار للزائر الحالي:
 * '' ظاهرة (زبون مسجّل) · 'login' زائر لم يسجّل · 'hidden' الأسعار مخفية للجميع.
 *
 * @return string
 */
function zad_price_gate() {
	if ( ! zad_opt( 'show_prices' ) ) {
		return 'hidden';
	}
	if ( ! zad_opt( 'members_prices' ) ) {
		return '';
	}
	return is_user_logged_in() ? '' : 'login';
}

/**
 * هل الأسعار ظاهرة للزائر الحالي؟
 *
 * @return bool
 */
function zad_show_prices() {
	return '' === zad_price_gate();
}

/**
 * هل الأسعار مخفية حتى يسجّل الزائر دخوله؟
 *
 * @return bool
 */
function zad_prices_need_login() {
	return 'login' === zad_price_gate();
}

/**
 * رابط ونص الدعوة لإظهار السعر حسب الحالة.
 *
 * @return array [الرابط، النص القصير]
 */
function zad_price_cta() {
	$account = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
	return array( add_query_arg( 'tab', 'register', $account ), 'سجّل لرؤية سعر الجملة' );
}

/**
 * الحد الأدنى للطلبية بعدد الكراتين (مجموع الكراتين من أي أصناف). 0 = بلا حد أدنى.
 *
 * @return int
 */
function zad_min_cartons() {
	return max( 0, (int) zad_opt( 'min_cartons' ) );
}

/**
 * عدد الكراتين في الطلبية الحالية.
 *
 * @return int
 */
function zad_cart_cartons() {
	return function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/**
 * كم كرتونة تنقص الطلبية لتبلغ الحد الأدنى.
 *
 * @param int|null $count عدد الكراتين (الافتراضي: الطلبية الحالية).
 * @return int
 */
function zad_min_cartons_left( $count = null ) {
	$count = null === $count ? zad_cart_cartons() : (int) $count;
	return max( 0, zad_min_cartons() - $count );
}

/**
 * كميات المنتجات الموجودة في السلة حالياً.
 *
 * @return array product_id => qty
 */
function zad_cart_qty_map() {
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
function zad_socials() {
	$out = array();
	foreach ( array( 'instagram', 'facebook', 'tiktok', 'telegram', 'youtube' ) as $net ) {
		$url = zad_opt( $net );
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
function zad_brand_svg( $file ) {
	static $cache = array();
	if ( ! isset( $cache[ $file ] ) ) {
		$path           = ZAD_DIR . '/assets/img/brand/' . $file;
		$cache[ $file ] = is_readable( $path ) ? trim( (string) file_get_contents( $path ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	return $cache[ $file ];
}

/**
 * الشعار: كلمة «بسكاتو» السوداء ونقطة الباء «بسكويتة» بلون التمييز، مع BISKATO WHOLESALE.
 *
 * @param string $variant light للخلفيات الفاتحة، dark للخلفيات الداكنة.
 * @param bool   $tagline النسخة مع سطر «كيك وبسكويت وشيبس بالجملة» تحت الشعار.
 * @param bool   $mobile  إضافة نسخة الجوال التي تظهر بدل نسخة الكمبيوتر (للترويسة).
 */
function zad_logo( $variant = 'light', $tagline = false, $mobile = false ) {
	if ( has_custom_logo() && 'light' === $variant ) {
		the_custom_logo();
		return;
	}
	$suffix = 'dark' === $variant ? '-dark' : '';
	$file   = ( $tagline ? 'logo-tag' : 'logo' ) . $suffix . '.svg';
	$svg    = preg_replace( '/^<svg /', '<svg class="zd-logo__svg' . ( $mobile ? ' zd-logo__svg--full' : '' ) . '" aria-hidden="true" focusable="false" ', zad_brand_svg( $file ) );
	// الترويسة على الجوال: نفس الشعار (بسكاتو | BISKATO) بحدود ضيقة وحروف لاتينية أكبر لتبقى مقروءة.
	if ( $mobile ) {
		$svg .= preg_replace( '/^<svg /', '<svg class="zd-logo__svg zd-logo__svg--mobile" aria-hidden="true" focusable="false" ', zad_brand_svg( 'logo-mobile' . $suffix . '.svg' ) );
	}
	printf(
		'<a class="zd-logo zd-logo--%1$s" href="%2$s" rel="home" aria-label="%3$s">%4$s</a>',
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
function zad_logo_mark( $class = 'zd-logo__mark' ) {
	return preg_replace( '/^<svg /', '<svg class="' . esc_attr( $class ) . '" aria-hidden="true" focusable="false" ', zad_brand_svg( 'mark.svg' ) );
}

/**
 * صيغة العدد العربية لكلمة «صنف».
 *
 * @param int $n العدد.
 * @return string
 */
function zad_n_items( $n ) {
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
function zad_products_total() {
	$counts = wp_count_posts( 'product' );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * كميات السلة مع تخزين مؤقت لطلب الصفحة الحالي.
 *
 * @return array
 */
function zad_cart_qty_cached() {
	static $map = null;
	if ( null === $map ) {
		$map = zad_cart_qty_map();
	}
	return $map;
}

/**
 * رابط صفحة شركة (علامة تجارية).
 *
 * @param string $slug slug.
 * @return string
 */
function zad_brand_url( $slug ) {
	$term = zad_term_by_slug( $slug, 'product_brand' );
	if ( $term ) {
		$link = get_term_link( $term );
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
function zad_all_brands() {
	static $out = null;
	if ( null !== $out ) {
		return $out;
	}
	$known = zad_brands();
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
				'c1'    => '#222222',
				'c2'    => '#56CFE1',
				'about' => wp_strip_all_tags( $t->description ),
			);
		}
		$out[ $t->slug ]['count'] = (int) $t->count;
	}
	foreach ( $out as $slug => $b ) {
		$out[ $slug ]['url'] = zad_brand_url( $slug );
	}
	return $out;
}

/**
 * عنوان قسم بأسلوب Kalles: عنوان في الوسط بين خطين رفيعين، وسطر فرعي بخط نسخي هادئ.
 *
 * @param string $title العنوان.
 * @param string $sub   السطر الفرعي.
 * @param string $id    معرّف العنوان (لـ aria-labelledby).
 * @param string $tag   وسم العنوان.
 */
function zad_section_head( $title, $sub = '', $id = '', $tag = 'h2' ) {
	printf(
		'<div class="zd-stitle"><%1$s class="zd-stitle__title"%2$s><span>%3$s</span></%1$s>%4$s</div>',
		tag_escape( $tag ),
		$id ? ' id="' . esc_attr( $id ) . '"' : '',
		esc_html( $title ),
		$sub ? '<p class="zd-stitle__sub">' . esc_html( $sub ) . '</p>' : ''
	);
}

/* -------------------------------------------------------------------------
 * ذاكرة مؤقتة خفيفة لأجزاء الصفحات الثقيلة (تُفرَّغ عند أي تعديل في المنتجات أو الأقسام)
 * ---------------------------------------------------------------------- */

/**
 * مفتاح الذاكرة المؤقتة مع رقم إصدار يتغير عند التفريغ.
 *
 * @param string $key المفتاح.
 * @return string
 */
function zad_cache_key( $key ) {
	return 'zad_c' . (int) get_option( 'zad_cache_v', 1 ) . '_' . md5( $key . '|' . ZAD_VERSION );
}

/**
 * قراءة قيمة مخزنة، أو حسابها وتخزينها.
 *
 * @param string   $key      المفتاح.
 * @param callable $callback دالة الحساب.
 * @param int      $ttl      المدة بالثواني.
 * @return mixed
 */
function zad_cache_remember( $key, $callback, $ttl = 6 * HOUR_IN_SECONDS ) {
	$k   = zad_cache_key( $key );
	$val = get_transient( $k );
	if ( false === $val ) {
		$val = call_user_func( $callback );
		set_transient( $k, $val, $ttl );
	}
	return $val;
}

/**
 * تفريغ الذاكرة المؤقتة كلها (برفع رقم الإصدار، فتنتهي القيم القديمة وحدها).
 */
function zad_cache_flush() {
	update_option( 'zad_cache_v', (int) get_option( 'zad_cache_v', 1 ) + 1, true );
}
foreach ( array( 'save_post_product', 'deleted_post', 'edited_product_cat', 'created_product_cat', 'edited_product_brand', 'created_product_brand', 'customize_save_after', 'woocommerce_update_product' ) as $zad_hook ) {
	add_action( $zad_hook, 'zad_cache_flush' );
}

/**
 * جزء قالب مخزّن مؤقتاً للزوار غير المسجلين (أغلب الزيارات وزحف جوجل): يُرسم مرة ويُقدَّم من الذاكرة.
 * لا يُخزَّن للمسجلين لأن بطاقات المنتجات عندهم تحمل الأسعار وكميات طلبيتهم.
 *
 * @param string $slug مسار الجزء (template-parts/...).
 * @param int    $ttl  المدة بالثواني.
 */
function zad_cached_part( $slug, $ttl = 6 * HOUR_IN_SECONDS ) {
	$cacheable = ! is_user_logged_in() && 'login' === zad_price_gate() && ! ( function_exists( 'WC' ) && WC()->cart && WC()->cart->get_cart_contents_count() );
	if ( ! $cacheable ) {
		get_template_part( $slug );
		return;
	}
	echo zad_cache_remember( // phpcs:ignore WordPress.Security.EscapeOutput
		'part:' . $slug,
		static function () use ( $slug ) {
			ob_start();
			get_template_part( $slug );
			return (string) ob_get_clean();
		},
		$ttl
	);
}
