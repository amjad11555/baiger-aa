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
		'announcement'    => 'أسعار جملة للحسابات التجارية · توريد إلى جميع الولايات التركية · تصدير بالحاويات',
		'show_prices'     => true,
		'min_order'       => 0,
		'min_cartons'     => 15,
		'free_delivery'   => 0,
		'show_profit'     => true,
		'retail_margin'   => 25,
		'require_account' => true,
		'members_prices'  => false,
		'hero_kicker'     => 'بسكاتو للتجارة · جملة وتوزيع',
		'hero_title'      => 'مورّدك الثابت للكيك والبسكويت والشيبس بالجملة',
		'hero_text'       => 'نوفّر لمتجرك أكثر من 130 صنفاً من إيتي وأولكر وبونوتشي بأسعار الجملة، مع توريد منتظم إلى كل الولايات وتصدير بالحاويات. اطلب بالكرتونة أو بالطبلية، ونتولى نحن التجهيز والتوصيل.',
		'hero_image'      => '',
		'force_rtl'       => true,
		'instagram'       => '',
		'facebook'        => '',
		'tiktok'          => '',
		'telegram'        => '',
		'youtube'         => '',
		'seo_tagline'     => 'جملة وتوزيع الكيك والبسكويت والشيبس لتجار التجزئة والموزعين',
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
		'delivery' => array( 'step-deliver', 'شروط التعامل مع حسابات الجملة' ),
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
	$url   = '';
	$id    = (int) get_option( 'zad_page_' . $key );
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
	return array(
		'eti'     => array(
			'ar'    => 'إيتي',
			'alt'   => 'ايتي',
			'latin' => 'Eti',
			'c1'       => '#CE0006',
			'c2'       => '#EFB455',
			'about'    => 'علامة تركية عريقة تأسست عام 1962 في مدينة إسكي شهير، ومن أشهر منتجاتها براوني وبوب كيك وتوب كيك وتوتكو وجين وكراكس.',
			// الشعار (SVG) واللافتات (1600×1740) من صور العلامة بعد رفع جودتها.
			'logo'     => array( 'eti.svg', 840, 540 ),
			'hero'     => 'eti-cikolata',
			'showcase' => array(
				array( 'eti-hosbes', 'ويفر مقرمش', 'هوشبيش', 'بالشوكولاتة الداكنة، والفراولة، والحليب والكاكاو.', array( 'q' => 'هوشبيش' ), 'عبوات ويفر إيتي هوشبيش على خلفية حمراء' ),
				array( 'eti-benimo', 'بسكويت طري بالشوكولاتة', 'بينيمو', 'من الأصناف التي يطلبها زبائن البقالة كل أسبوع.', array( 'q' => 'بينيمو' ), 'عبوات إيتي بينيمو على خلفية برتقالية' ),
				array( 'eti-cikolata', 'شوكولاتة', 'شوكولاتة إيتي', 'كارام، وبيتيتو، وجانغا، ووانتد، وبوف.', array( 'section' => 'snacks' ), 'مربعات شوكولاتة إيتي بنكهات مختلفة' ),
			),
		),
		'ulker'   => array(
			'ar'    => 'أولكر',
			'alt'   => 'اولكر',
			'latin' => 'Ülker',
			'c1'    => '#1F3C88',
			'c2'    => '#F4C542',
			'about' => 'من أقدم وأكبر شركات البسكويت والشوكولاتة في تركيا منذ 1944، صاحبة بسكريم وشوكوبرنس وهالي وألبيني وويفر أولكر الشهير.',
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
 * رابط لافتة من لافتات العلامة: بحث باسم الصنف، أو صفحة العلامة مصفّاة بقسم.
 *
 * @param array $brand  العلامة.
 * @param array $target ['q' => …] أو ['section' => …].
 * @return string
 */
function zad_showcase_url( $brand, $target ) {
	if ( ! empty( $target['q'] ) ) {
		return add_query_arg(
			array(
				's'         => $target['q'],
				'post_type' => 'product',
			),
			home_url( '/' )
		);
	}
	if ( ! empty( $target['section'] ) && ! empty( $brand['url'] ) ) {
		return add_query_arg( 'section', $target['section'], $brand['url'] );
	}
	return ! empty( $brand['url'] ) ? $brand['url'] : home_url( '/' );
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
			'title' => 'الكيك المغلّف',
			'icon'  => 'cake',
			'image' => 'tile-cake',
			'color' => '#124A39',
			'tint'  => '#E4EDE8',
			'line'  => 'براوني وبوب كيك ورول ومحشو، الأسرع دوراناً على الرف',
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
		'chips'    => array(
			'name'  => 'شيبسات',
			'title' => 'الشيبس والمقرمشات',
			'icon'  => 'chips',
			'image' => 'tile-chips',
			'color' => '#8A5A1E',
			'tint'  => '#F4EAD9',
			'line'  => 'كراكرز وأصابع مملّحة وذرة، هامش جيد ودوران سريع',
		),
		'snacks'   => array(
			'name'  => 'تسالي',
			'title' => 'الشوكولاتة والتسالي',
			'icon'  => 'candy',
			'image' => 'tile-snacks',
			'color' => '#4B2A1C',
			'tint'  => '#EFE4DC',
			'line'  => 'ألواح وويفر وحلوى، أصناف الكاشير والشراء السريع',
		),
		'offers'   => array(
			'name'  => 'عروض',
			'title' => 'عروض الجملة',
			'icon'  => 'percent',
			'image' => 'tile-offers',
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
function zad_segments() {
	return array(
		array(
			'image' => 'stage-supermarket',
			'title' => 'السوبرماركت والسلاسل',
			'text'  => 'تشكيلة تغطي رف الكيك والبسكويت والشيبس بالكامل، وتوريد بجدول ثابت، وفاتورة نظامية مع كل شحنة.',
		),
		array(
			'image' => 'stage-grocery',
			'title' => 'البقالات والميني ماركت',
			'text'  => zad_min_cartons() > 0
				? sprintf( 'اطلب %d كرتونة مشكّلة من أي أصناف (ولو كرتونة واحدة من الصنف)، ونوصلها إلى باب المحل أو على الرف والدفع عند الاستلام.', zad_min_cartons() )
				: 'اطلب من كرتونة واحدة لكل صنف، ونوصل الطلبية إلى باب المحل والدفع عند الاستلام.',
		),
		array(
			'image' => 'stage-distributor',
			'title' => 'الموزعون وتجار نصف الجملة',
			'text'  => 'أسعار كميات بالطبلية، ونوصلها إلى مستودعك لتغذية شبكة التوزيع في منطقتك.',
		),
		array(
			'image' => 'stage-export',
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
function zad_img_meta( $name ) {
	$map = array(
		'hero'              => array( 2400, 1340 ),
		'hero-m'            => array( 1100, 1366 ),
		'cat-cake'          => array( 900, 1117 ),
		'cat-biscuits'      => array( 900, 1117 ),
		'cat-chips'         => array( 900, 1117 ),
		'cat-snacks'        => array( 900, 1117 ),
		'cat-offers'        => array( 900, 1117 ),
		'seg-supermarket'   => array( 1200, 805 ),
		'seg-grocery'       => array( 1200, 805 ),
		'seg-distributor'   => array( 1200, 805 ),
		'seg-export'        => array( 1600, 1073 ),
		'step-order'        => array( 1000, 671 ),
		'step-pick'         => array( 1000, 671 ),
		'step-deliver'      => array( 1000, 671 ),
		'about-team'        => array( 1600, 893 ),
		'sourcing'          => array( 1400, 939 ),
		'quality'           => array( 1000, 671 ),
		'cta-docks'         => array( 2400, 1018 ),
		'texture'           => array( 1600, 893 ),
		'flatlay'           => array( 1800, 1005 ),
		'eti-cikolata'      => array( 1600, 1740 ),
		'eti-benimo'        => array( 1600, 1740 ),
		'eti-hosbes'        => array( 1600, 1740 ),
		// صور الواجهة المصممة من صور إيتي (أسماء جديدة لا يغطيها مستورد صور الموقع).
		'hero-eti-1'        => array( 2560, 1120 ),
		'hero-eti-2'        => array( 2560, 1120 ),
		'hero-eti-3'        => array( 2560, 1120 ),
		'hero-eti-1-m'      => array( 1100, 1740 ),
		'hero-eti-2-m'      => array( 1100, 1740 ),
		'hero-eti-3-m'      => array( 1100, 1740 ),
		'tile-snacks'       => array( 1000, 1500 ),
		'tile-biscuits'     => array( 900, 1000 ),
		'tile-cake'         => array( 900, 1000 ),
		'tile-chips'        => array( 900, 1000 ),
		'tile-offers'       => array( 900, 1000 ),
		'banner-sourcing'   => array( 1400, 740 ),
		'banner-export'     => array( 1400, 740 ),
		'stage-supermarket' => array( 1200, 840 ),
		'stage-grocery'     => array( 1200, 840 ),
		'stage-distributor' => array( 1200, 840 ),
		'stage-export'      => array( 1200, 840 ),
		'stage-order'       => array( 1200, 800 ),
		'stage-pick'        => array( 1200, 800 ),
		'stage-deliver'     => array( 1200, 800 ),
	);
	$stored = function_exists( 'zad_site_images_stored' ) ? zad_site_images_stored() : array();
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
 * رابط قسم منتجات حسب الاسم اللطيف (slug).
 *
 * @param string $slug slug.
 * @return string
 */
function zad_cat_url( $slug ) {
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
function zad_cat_count( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
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
 * هل الأسعار ظاهرة للزائر الحالي؟
 *
 * مخفية للجميع إن أُلغي «إظهار الأسعار»، أو لغير المسجلين إن فُعّل «إخفاء الأسعار عن غير المسجلين».
 *
 * @return bool
 */
function zad_show_prices() {
	if ( ! zad_opt( 'show_prices' ) ) {
		return false;
	}
	return ! ( zad_opt( 'members_prices' ) && ! is_user_logged_in() );
}

/**
 * هل الأسعار مخفية لأن الزائر لم يسجّل دخوله فقط؟
 *
 * @return bool
 */
function zad_prices_need_login() {
	return zad_opt( 'show_prices' ) && zad_opt( 'members_prices' ) && ! is_user_logged_in();
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
