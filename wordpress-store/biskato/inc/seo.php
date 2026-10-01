<?php
/**
 * السيو العربي: العناوين، الوصف، Open Graph، البيانات المنظمة (Schema)، الفهرسة.
 *
 * يتنحّى تلقائياً عن العناوين والوصف إذا كانت إضافة سيو مفعّلة (Yoast / Rank Math / AIOSEO / SEOPress)،
 * ويُبقي على البيانات المنظمة الخاصة بالمتجر والأسئلة الشائعة.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * هل توجد إضافة سيو مفعلة؟
 *
 * @return bool
 */
function zad_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * قص نص الوصف بطول مناسب.
 *
 * @param string $text النص.
 * @param int    $max  الطول.
 * @return string
 */
function zad_seo_trim( $text, $max = 158 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( (string) $text ) ) ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max );
	$pos = mb_strrpos( $cut, ' ' );
	return rtrim( $pos ? mb_substr( $cut, 0, $pos ) : $cut, '،,.؛ ' ) . '…';
}

/**
 * هل الصفحة الحالية من قوالب القالب الخاصة؟
 *
 * @return string مفتاح القالب أو فارغ.
 */
function zad_current_template_key() {
	if ( ! is_page() ) {
		return '';
	}
	$map = array(
		'page-templates/template-quick-order.php'     => 'quick_order',
		'page-templates/template-special-request.php' => 'special_request',
		'page-templates/template-export.php'          => 'export',
		'page-templates/template-contact.php'         => 'contact',
	);
	$tpl = get_page_template_slug();
	return isset( $map[ $tpl ] ) ? $map[ $tpl ] : '';
}

/**
 * الوصف المختصر للموقع (عنوان الرئيسية في نتائج البحث).
 *
 * يُقدَّم ما كتبه المدير في المظهر ← تخصيص، ثم «الوصف» في الإعدادات العامة،
 * ثم الافتراضي. الوصف القديم الذي كتبه معالج الإعداد لا يُحتسب لأنه بلا ذكر للمدينة.
 *
 * @return string
 */
function zad_seo_tagline() {
	$mod = (string) get_theme_mod( 'zad_seo_tagline', '' );
	if ( '' !== trim( $mod ) ) {
		return $mod;
	}
	$desc = (string) get_option( 'blogdescription', '' );
	// أوصاف افتراضية قديمة كتبها معالج الإعداد في إصدارات سابقة.
	$old  = array( '', 'جملة وتوزيع الكيك والبسكويت والشيبس لتجار التجزئة والموزعين', 'جملة وتوزيع الحلويات والتسالي التركية لتجار التجزئة والموزعين', 'Just another WordPress site', 'موقع ووردبريس عربي آخر' );
	if ( ! in_array( trim( $desc ), $old, true ) ) {
		return $desc;
	}
	return zad_opt( 'seo_tagline' );
}

/**
 * عنوان الصفحة.
 *
 * @param array $parts أجزاء العنوان.
 * @return array
 */
function zad_title_parts( $parts ) {
	if ( zad_seo_plugin_active() ) {
		return $parts;
	}
	$site = get_bloginfo( 'name' );

	if ( is_front_page() ) {
		return array(
			'title'   => $site,
			'tagline' => zad_seo_tagline(),
		);
	}
	$city = zad_opt( 'city' );

	if ( is_search() ) {
		$parts['title'] = sprintf( 'نتائج البحث عن «%s»', get_search_query( false ) );
	} elseif ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
		$parts['title'] = 'إتمام الطلبية';
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		// الاسم العربي أولاً (بحث العرب في إسطنبول)، ثم الاسم التركي كما هو مكتوب على العبوة إن اتسع العنوان.
		$name           = single_post_title( '', false );
		$tr             = (string) get_post_meta( get_queried_object_id(), '_zad_tr', true );
		$parts['title'] = $name . ' بالجملة في ' . $city;
		if ( $tr && mb_strlen( $parts['title'] . $tr ) < 72 ) {
			$parts['title'] .= ' – ' . $tr;
		}
	} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		if ( 'offers' === $term->slug ) {
			$parts['title'] = sprintf( 'عروض الجملة في %s: خصم دائم على إيتي وأولكر', $city );
		} else {
			$cats           = zad_categories();
			$name           = isset( $cats[ $term->slug ] ) ? $cats[ $term->slug ]['title'] : $term->name;
			$parts['title'] = sprintf( '%1$s بالجملة في %2$s – إيتي وأولكر وبونجو', $name, $city );
		}
	} elseif ( is_tax( 'product_brand' ) ) {
		$term           = get_queried_object();
		$brands         = zad_brands();
		$latin          = isset( $brands[ $term->slug ]['latin'] ) ? $brands[ $term->slug ]['latin'] : '';
		$parts['title'] = sprintf( 'منتجات %1$s بالجملة في %2$s%3$s', $term->name, $city, $latin ? ' (' . $latin . ' Toptan)' : '' );
	} elseif ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$parts['title'] = function_exists( 'zad_is_new_view' ) && zad_is_new_view()
			? 'الأصناف الجديدة بالجملة'
			: sprintf( 'كل الأصناف بسعر الجملة في %s: كيك وبسكويت وشيبس تركي', $city );
	} else {
		$titles = array(
			'quick_order'     => sprintf( 'قائمة أسعار الجملة في %s – كيك وبسكويت وشيبس بالكرتونة', $city ),
			'special_request' => 'طلب توريد خاص – نؤمّن الأصناف غير المتوفرة من المصدر',
			'export'          => 'تصدير كيك وبسكويت وشيبس تركي بالجملة – حاويات مختلطة وشحن دولي',
			'contact'         => sprintf( 'تواصل معنا – تاجر جملة كيك وبسكويت في %s', $city ),
		);
		$key = zad_current_template_key();
		if ( $key && isset( $titles[ $key ] ) ) {
			$parts['title'] = $titles[ $key ];
		}
	}
	if ( function_exists( 'zad_filtered_title' ) && function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$parts['title'] = zad_filtered_title( $parts['title'] );
	}
	$parts['site'] = $site;
	unset( $parts['tagline'] );
	return $parts;
}
add_filter( 'document_title_parts', 'zad_title_parts', 20 );
add_filter(
	'document_title_separator',
	static function ( $sep ) {
		return zad_seo_plugin_active() ? $sep : '|';
	}
);

/**
 * الوصف التعريفي للصفحة الحالية.
 *
 * @return string
 */
function zad_meta_description() {
	$site = get_bloginfo( 'name' );
	$city = zad_opt( 'city' );

	if ( is_front_page() ) {
		$count = (int) wp_count_posts( 'product' )->publish;
		$count = $count >= 20 ? (int) floor( $count / 10 ) * 10 : 0;
		return zad_seo_trim( sprintf( 'كيك وبسكويت وشيبس وشوكولاتة بالجملة في %1$s لأصحاب المحلات: %2$sإيتي وأولكر وبونجو بخصم دائم، توصيل مجاني للمحل والدفع نقداً عند الاستلام.', $city, $count ? '+' . $count . ' صنفاً من ' : '' ), 160 );
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$info  = zad_product_info( $product->get_id() );
			$price = zad_show_prices() && $product->get_price() ? ' سعر الكرتونة ' . zad_money_plain( wc_get_price_to_display( $product ) ) . '.' : '';
			$disc  = function_exists( 'zad_brand_discount' ) ? zad_brand_discount( $info['brand'] ) : 0;
			$brand = $info['brand'] && isset( zad_brands()[ $info['brand'] ] );
			return zad_seo_trim(
				sprintf(
					'%1$s%2$s بالجملة في %5$s%3$s.%4$s%6$s توصيل مجاني للمحل والدفع عند الاستلام.',
					$product->get_name(),
					$info['tr'] ? ' (' . $info['tr'] . ')' : '',
					$info['pack'] ? '، الكرتونة ' . $info['pack'] : '',
					$price,
					$city,
					$disc > 0 && $brand ? ' خصم دائم ' . ( 0 + $disc ) . '%.' : ''
				),
				160
			);
		}
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		$desc = term_description( $term );
		if ( $desc ) {
			return zad_seo_trim( $desc );
		}
		return zad_seo_trim( sprintf( '%1$s بالجملة في %3$s من %2$s: البيع بالكرتونة لأصحاب المحلات، توصيل مجاني والدفع نقداً عند الاستلام.', $term->name, $site, $city ) );
	}

	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return zad_seo_trim( sprintf( 'كل أصناف %1$s بسعر الجملة في %2$s: كيك وبسكويت وشيبس وشوكولاتة وسكاكر من إيتي وأولكر وبونجو والوان، بالكرتونة مع توصيل مجاني لمحلك.', $site, $city ) );
	}

	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_zad_meta_desc', true );
		if ( $custom ) {
			return zad_seo_trim( $custom );
		}
		$descs = array(
			'quick_order'     => sprintf( 'قائمة أسعار الجملة في %s: كل أصناف الكيك والبسكويت والشيبس والشوكولاتة بسعر الكرتونة. سجّل حسابك، حدّد الكميات وأرسل الطلبية في دقيقة.', $city ),
			'special_request' => 'صنف غير موجود في القائمة؟ أرسل اسمه والكمية ويؤمّنه فريق المشتريات من المصدر بسعر الجملة — أي علامة تركية أو مستوردة.',
			'export'          => 'تصدير كيك وبسكويت وشيبس تركي بالجملة خارج تركيا: بسكويت وكيك وشيبس وشوكولاتة من إيتي وأولكر وبونجو. حاويات 20 و40 قدم وطبليات مختلطة مع شهادات المنشأ والحلال.',
			'contact'         => sprintf( 'تواصل مع %1$s في %2$s عبر واتساب أو الهاتف: فتح حساب جملة، أسعار الكميات، مواعيد التوصيل وطلبات التصدير.', $site, $city ),
		);
		$key = zad_current_template_key();
		if ( $key && isset( $descs[ $key ] ) ) {
			return zad_seo_trim( $descs[ $key ] );
		}
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			return zad_seo_trim( has_excerpt( $post ) ? $post->post_excerpt : $post->post_content );
		}
	}

	return zad_seo_trim( zad_seo_tagline() );
}

/**
 * صورة المشاركة.
 *
 * @return string
 */
function zad_og_image() {
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( get_queried_object_id() ), 'large' );
		if ( $src ) {
			return $src[0];
		}
	}
	if ( has_custom_logo() ) {
		$src = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
		if ( $src && is_front_page() ) {
			return $src[0];
		}
	}
	return file_exists( ZAD_DIR . '/assets/img/og-default.jpg' ) ? ZAD_URI . '/assets/img/og-default.jpg' : '';
}

/**
 * الرابط المعياري للصفحة الحالية.
 *
 * @return string
 */
function zad_current_url() {
	if ( is_singular() ) {
		return (string) wp_get_canonical_url();
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$paged = max( 1, (int) get_query_var( 'paged' ) );
		return $paged > 1 ? get_pagenum_link( $paged ) : wc_get_page_permalink( 'shop' );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$paged = max( 1, (int) get_query_var( 'paged' ) );
		if ( $paged > 1 ) {
			return get_pagenum_link( $paged );
		}
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? '' : $link;
	}
	return '';
}

/**
 * طباعة وسوم الميتا.
 */
function zad_head_meta() {

	if ( zad_seo_plugin_active() ) {
		return;
	}

	$desc  = zad_meta_description();
	$url   = zad_current_url();
	$title = wp_get_document_title();
	$image = zad_og_image();
	$type  = 'website';
	if ( function_exists( 'is_product' ) && is_product() ) {
		$type = 'product';
	} elseif ( is_singular( 'post' ) ) {
		$type = 'article';
	}

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $url && ! is_singular() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}

	$og = array(
		'og:locale'    => 'ar_AR',
		'og:type'      => $type,
		'og:site_name' => get_bloginfo( 'name' ),
		'og:title'     => $title,
		'og:description' => $desc,
		'og:url'       => $url,
		'og:image'     => $image,
	);
	foreach ( $og as $prop => $content ) {
		if ( $content ) {
			printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $prop ), esc_attr( $content ) );
		}
	}
	if ( 'product' === $type ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product && $product->get_price() && zad_show_prices() ) {
			printf( '<meta property="product:price:amount" content="%s">' . "\n", esc_attr( wc_get_price_to_display( $product ) ) );
			printf( '<meta property="product:price:currency" content="%s">' . "\n", esc_attr( get_woocommerce_currency() ) );
		}
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'zad_head_meta', 2 );

/**
 * قواعد الفهرسة.
 *
 * @param array $robots القواعد.
 * @return array
 */
function zad_robots( $robots ) {
	$noindex = is_search() || is_404();
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$noindex = true;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['zd_sent'] ) || isset( $_GET['zd_err'] ) || isset( $_GET['orderby'] ) || isset( $_GET['add-to-cart'] ) ) {
		$noindex = true;
	}
	if ( $noindex ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	} elseif ( ! zad_seo_plugin_active() && '0' !== (string) get_option( 'blog_public' ) ) {
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = '-1';
	}
	return $robots;
}
add_filter( 'wp_robots', 'zad_robots', 20 );

/**
 * إزالة خريطة المستخدمين من خريطة الموقع (خصوصية).
 *
 * @param WP_Sitemaps_Provider $provider المزوّد.
 * @param string               $name     الاسم.
 * @return WP_Sitemaps_Provider|false
 */
function zad_sitemap_providers( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'zad_sitemap_providers', 10, 2 );

/**
 * هل في الموقع مقالات حقيقية؟ (مقال «أهلاً بالعالم» الافتراضي لا يُحتسب).
 *
 * @return bool
 */
function zad_has_blog() {
	static $has = null;
	if ( null === $has ) {
		$ids = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 2,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$has = count( array_diff( $ids, array( 1 ) ) ) > 0;
	}
	return $has;
}

/**
 * متجر بلا مدونة: لا ترسل إلى جوجل مقالات وتصنيفات فارغة أو افتراضية.
 *
 * @param array $types الأنواع.
 * @return array
 */
function zad_sitemap_post_types( $types ) {
	if ( ! zad_has_blog() ) {
		unset( $types['post'] );
	}
	return $types;
}
add_filter( 'wp_sitemaps_post_types', 'zad_sitemap_post_types' );

/**
 * التصنيفات في خريطة الموقع.
 *
 * @param array $taxonomies التصنيفات.
 * @return array
 */
function zad_sitemap_taxonomies( $taxonomies ) {
	if ( ! zad_has_blog() ) {
		unset( $taxonomies['category'], $taxonomies['post_tag'] );
	}
	return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'zad_sitemap_taxonomies' );

/**
 * إخفاء اسم مستخدم المدير عن الزوار: مسار المستخدمين في REST وصفحات الكاتب.
 *
 * @param array $endpoints المسارات.
 * @return array
 */
function zad_hide_user_endpoints( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'zad_hide_user_endpoints' );

/**
 * صفحات الكاتب (/author/…) لا معنى لها في متجر جملة، وتكشف اسم الدخول.
 */
function zad_no_author_archives() {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'zad_no_author_archives' );

/**
 * بيانات oEmbed دون اسم الكاتب.
 *
 * @param array $data البيانات.
 * @return array
 */
function zad_oembed_no_author( $data ) {
	unset( $data['author_name'], $data['author_url'] );
	return $data;
}
add_filter( 'oembed_response_data', 'zad_oembed_no_author' );

/* -------------------------------------------------------------------------
 * الأسئلة الشائعة (تُعرض في الصفحات وتُضاف كبيانات FAQPage)
 * ---------------------------------------------------------------------- */

/**
 * الأسئلة الشائعة حسب السياق.
 *
 * @param string $context home|export|contact.
 * @return array
 */
function zad_faqs( $context = 'home' ) {
	$city = zad_opt( 'city' );
	$min  = (float) zad_opt( 'min_order' );
	$min_text = ( $min > 0 && function_exists( 'wc_price' ) ) ? ' والحد الأدنى لقيمة الطلب ' . zad_money_plain( $min ) . '.' : '';
	$min_answer = zad_min_cartons() > 0
		? sprintf( 'الحد الأدنى للطلبية %d كرتونة مشكّلة من أي أصناف تختارها، ويمكنك أخذ كرتونة واحدة فقط من الصنف.', zad_min_cartons() )
		: 'الحد الأدنى كرتونة واحدة من كل صنف.';

	$home = array(
		array( 'كيف أفتح حساب جملة لدى بسكاتو؟', 'سجّل مجاناً باسم المحل ورقم واتساب والمنطقة، ثم أكّد حسابك برسالة واتساب واحدة. بعد التأكيد تظهر لك أسعار الجملة وتستطيع إرسال طلبيتك مباشرة من قائمة الأسعار.' ),
		array( 'ما الحد الأدنى للطلبية؟', $min_answer . $min_text . ' وللطلب بالطبلية أو بكميات شهرية ثابتة نقدّم أسعار كميات خاصة.' ),
		array( 'كيف تتم عملية الدفع؟', 'داخل إسطنبول: نقداً عند الاستلام فقط، تدفع للمندوب حين تصل الكراتين إلى محلك بلا دفع مسبق. خارج إسطنبول وخارج تركيا: بتحويل إلى حسابنا البنكي الرسمي، ونرسل لك بيانات الحساب مع تأكيد الطلبية. نصدر فاتورة نظامية مع كل طلبية.' ),
		array( 'متى تصل الطلبية؟', sprintf( 'داخل %s غالباً خلال 24 إلى 48 ساعة من التأكيد، وإلى باقي الولايات وفق جدول التوزيع. نؤكد موعد التسليم قبل خروج الشحنة.', $city ) ),
		array( 'هل أستلم الطلبية من المستودع؟', 'لا حاجة لذلك: الطلب أونلاين فقط، والتوصيل مجاني إلى محلك. تختار عند تأكيد الطلبية أن نسلّمك الكراتين عند باب المحل، أو أن ندخلها ونرتّب الأصناف على رفوفك.' ),
		array( 'هل المنتجات أصلية وصلاحيتها حديثة؟', 'نعم. كل الأصناف أصلية من إيتي (Eti) وأولكر (Ülker) وبونجو (Bonucci) والوان (Elvan) وغيرها، ونختار دفعات إنتاج حديثة ونراجع تواريخ الصلاحية قبل الشحن.' ),
		array( 'أحتاج صنفاً غير موجود في القائمة، ماذا أفعل؟', 'أرسل اسمه والكمية من صفحة «طلب توريد خاص»، ويعود إليك فريق المشتريات بالسعر والتوفر وموعد التوريد.' ),
		array( 'لماذا لا تظهر الأسعار في الموقع؟', 'أسعارنا أسعار جملة خاصة بأصحاب المحلات، لذلك تظهر بعد فتح حساب مجاني وتأكيده برسالة واتساب. التسجيل يأخذ أقل من دقيقة، والتأكيد غالباً خلال دقائق في أوقات العمل.' ),
		array( 'هل توصلون إلى كل مناطق إسطنبول؟', 'نعم، نوصل مجاناً إلى المحلات في كل مناطق إسطنبول على الجانبين الأوروبي والآسيوي: الفاتح وإسنيورت وباشاك شهير وباغجلار وسلطان غازي وإسنلر وأفجلار وزيتون بورنو وغيرها.' ),
		array( 'هل تصدّرون خارج تركيا؟', 'نعم، نصدّر إلى الأسواق العربية وأوروبا بطبليات مختلطة أو حاويات 20 و40 قدماً، مع شهادات المنشأ والحلال. اطلب عرض سعر من صفحة «التصدير».' ),
	);

	$export = array(
		array( 'ما الحد الأدنى لطلبات التصدير؟', 'نبدأ من طبليات مختلطة (Mixed Pallets) للطلبات التجريبية، والأوفر لك حاوية 20 أو 40 قدم. نساعدك في توزيع الأصناف لتعبئة الحاوية بأفضل شكل.' ),
		array( 'هل يمكن خلط عدة منتجات وعلامات في حاوية واحدة؟', 'نعم، نوفر حاويات مختلطة (Mix Container) تجمع الكيك والبسكويت والشيبسات والشوكولاتة من إيتي وأولكر وبونجو وعلامات أخرى في شحنة واحدة.' ),
		array( 'ما المستندات التي توفرونها مع الشحنة؟', 'الفاتورة التجارية وقائمة التعبئة وشهادة المنشأ، إضافة إلى الشهادات الصحية وشهادات الحلال المتوفرة لدى المصنّعين حسب متطلبات بلد الوصول.' ),
		array( 'ما شروط التسليم المتاحة؟', 'نعمل بشروط EXW وFOB من الموانئ التركية وCIF حتى ميناء الوصول، ويمكن ترتيب DAP حتى مستودعكم في بعض الدول.' ),
		array( 'كم تستغرق مدة تجهيز الطلبية؟', 'تُحدد المدة بدقة في عرض السعر حسب الكمية وتوفر الأصناف، وغالباً ما تكون بين أسبوع وثلاثة أسابيع قبل التحميل.' ),
		array( 'كيف يتم الدفع في طلبات التصدير؟', 'عادةً بتحويل بنكي: دفعة مقدمة عند التأكيد والباقي قبل الشحن، أو حسب الاتفاق مع العملاء الدائمين.' ),
		array( 'هل تراعون تواريخ الصلاحية في الشحن الدولي؟', 'نعم، نختار دفعات إنتاج حديثة بصلاحية طويلة تناسب مدة الشحن والتخليص والتوزيع في بلدكم.' ),
	);

	$contact = array_slice( $home, 1, 4 );

	$all = array(
		'home'    => $home,
		'export'  => $export,
		'contact' => $contact,
	);
	return isset( $all[ $context ] ) ? $all[ $context ] : array();
}

/**
 * طباعة الأسئلة الشائعة.
 *
 * @param string $context السياق.
 * @param string $title   العنوان.
 */
function zad_render_faqs( $context, $title = 'الأسئلة الشائعة' ) {
	$faqs = zad_faqs( $context );
	if ( ! $faqs ) {
		return;
	}
	$GLOBALS['zad_faq_contexts'][] = $context;
	echo '<section class="zd-faq zd-section" id="faq" aria-labelledby="zd-faq-title-' . esc_attr( $context ) . '"><div class="zd-container zd-container--narrow">';
	printf( '<div class="zd-stitle"><h2 class="zd-stitle__title" id="zd-faq-title-%1$s"><span>%2$s</span></h2><p class="zd-stitle__sub">إجابات مختصرة عن الحساب والطلب والتوريد</p></div>', esc_attr( $context ), esc_html( $title ) );
	echo '<div class="zd-faq__list">';
	foreach ( $faqs as $i => $faq ) {
		printf(
			'<details class="zd-faq__item"%1$s><summary>%2$s<span class="zd-faq__icon" aria-hidden="true"></span></summary><div class="zd-faq__answer"><p>%3$s</p></div></details>',
			0 === $i ? ' open' : '',
			esc_html( $faq[0] ),
			esc_html( $faq[1] )
		);
	}
	echo '</div></div></section>';
}

/* -------------------------------------------------------------------------
 * البيانات المنظمة (JSON-LD)
 * ---------------------------------------------------------------------- */

/**
 * بيانات المتجر: متجر جملة محلي في إسطنبول يخدم كل أحيائها، ويشحن داخل تركيا وخارجها.
 *
 * @return array
 */
function zad_schema_store() {
	$city   = zad_opt( 'city' );
	$served = array(
		array(
			'@type'          => 'City',
			'name'           => 'İstanbul',
			'alternateName'  => array( 'إسطنبول', 'اسطنبول', 'Istanbul' ),
			'containedInPlace' => array(
				'@type' => 'Country',
				'name'  => 'Türkiye',
			),
		),
	);
	if ( function_exists( 'zad_districts' ) ) {
		foreach ( zad_districts() as $slug => $d ) {
			if ( 'other' === $slug || empty( $d[1] ) ) {
				continue;
			}
			$served[] = array(
				'@type'         => 'AdministrativeArea',
				'name'          => $d[1] . ', İstanbul',
				'alternateName' => $d[0],
			);
		}
	}
	$served[] = array(
		'@type' => 'Country',
		'name'  => 'Türkiye',
	);

	$catalog = array();
	foreach ( zad_categories() as $slug => $cat ) {
		if ( 'offers' === $slug ) {
			continue;
		}
		$catalog[] = array(
			'@type' => 'OfferCatalog',
			'name'  => $cat['title'] . ' بالجملة',
			'url'   => zad_cat_url( $slug ),
		);
	}

	$brands = array();
	foreach ( array_slice( zad_brands(), 0, 12, true ) as $b ) {
		$brands[] = $b['latin'];
	}

	$store = array(
		'@type'              => 'WholesaleStore',
		'@id'                => home_url( '/#store' ),
		'name'               => get_bloginfo( 'name' ),
		'alternateName'      => 'Biskato',
		'description'        => sprintf( 'تاجر جملة كيك وبسكويت وشيبس وشوكولاتة تركية في %s لأصحاب المحلات والبقالات العربية، مع توصيل مجاني للمحل.', $city ),
		'slogan'             => zad_seo_tagline(),
		'url'                => home_url( '/' ),
		'currenciesAccepted' => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'TRY',
		'paymentAccepted'    => 'Cash on delivery (İstanbul), Bank transfer',
		'priceRange'         => '₺₺',
		'areaServed'         => $served,
		'knowsLanguage'      => array( 'ar', 'tr' ),
		'knowsAbout'         => array_merge( $brands, array( 'كيك بالجملة', 'بسكويت بالجملة', 'شيبس بالجملة', 'شوكولاتة بالجملة', 'سكاكر بالجملة', 'جملة إسطنبول', 'toptan bisküvi', 'toptan kek' ) ),
		'hasOfferCatalog'    => array(
			'@type'           => 'OfferCatalog',
			'name'            => 'أصناف الجملة',
			'itemListElement' => $catalog,
		),
	);
	$logo = has_custom_logo() ? wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ) : false;
	$og   = file_exists( ZAD_DIR . '/assets/img/og-default.jpg' ) ? ZAD_URI . '/assets/img/og-default.jpg' : '';
	if ( $og ) {
		$store['image'] = $og;
	}
	if ( $logo ) {
		$store['logo'] = $logo[0];
	} elseif ( $og ) {
		$store['logo'] = $og;
	}
	$phone = zad_opt( 'phone' ) ? zad_opt( 'phone' ) : ( zad_wa_number() ? '+' . zad_wa_number() : '' );
	if ( $phone ) {
		$store['telephone']    = $phone;
		$store['contactPoint'] = array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'sales',
			'telephone'         => $phone,
			'areaServed'        => 'TR',
			'availableLanguage' => array( 'Arabic', 'Turkish' ),
		);
	}
	if ( zad_opt( 'email' ) ) {
		$store['email'] = zad_opt( 'email' );
	}
	$address = array(
		'@type'           => 'PostalAddress',
		'addressLocality' => $city,
		'addressRegion'   => 'İstanbul',
		'addressCountry'  => 'TR',
	);
	if ( zad_opt( 'address' ) ) {
		$address['streetAddress'] = zad_opt( 'address' );
	}
	$store['address'] = $address;
	$same             = array_values( zad_socials() );
	if ( $same ) {
		$store['sameAs'] = $same;
	}
	return $store;
}

/**
 * طباعة JSON-LD.
 */
function zad_schema_output() {
	$graph = array( zad_schema_store() );

	if ( is_front_page() ) {
		$graph[] = array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'url'             => home_url( '/' ),
			'name'            => get_bloginfo( 'name' ),
			'inLanguage'      => 'ar',
			'publisher'       => array( '@id' => home_url( '/#store' ) ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}&post_type=product' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	// مسار التنقل للصفحات العادية (صفحات ووكومرس لها مسارها الخاص).
	if ( is_page() && ! is_front_page() && ! ( function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) {
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'الرئيسية',
					'item'     => home_url( '/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => get_the_title( get_queried_object_id() ),
					'item'     => get_permalink( get_queried_object_id() ),
				),
			),
		);
	}

	// صفحات الأقسام والعلامات والمتجر: مسار التنقل + قائمة المنتجات المعروضة (ItemList).
	if ( function_exists( 'is_product_taxonomy' ) && ( is_product_taxonomy() || ( is_shop() && ! is_search() ) ) ) {
		$crumbs = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'الرئيسية',
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'كل الأصناف',
				'item'     => wc_get_page_permalink( 'shop' ),
			),
		);
		if ( is_product_taxonomy() ) {
			$term = get_queried_object();
			$link = $term instanceof WP_Term ? get_term_link( $term ) : '';
			if ( $link && ! is_wp_error( $link ) ) {
				$crumbs[] = array(
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => $term->name,
					'item'     => $link,
				);
			}
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $crumbs,
		);

		global $wp_query;
		$items = array();
		$pos   = 1 + max( 0, (int) get_query_var( 'paged' ) - 1 ) * max( 1, (int) $wp_query->get( 'posts_per_page' ) );
		foreach ( (array) $wp_query->posts as $p ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos++,
				'url'      => get_permalink( $p ),
				'name'     => get_the_title( $p ),
			);
		}
		if ( $items ) {
			$graph[] = array(
				'@type'           => 'ItemList',
				'name'            => wp_get_document_title(),
				'numberOfItems'   => count( $items ),
				'itemListElement' => $items,
			);
		}
	}

	// الأسئلة الشائعة المعروضة في هذه الصفحة.
	$contexts = isset( $GLOBALS['zad_faq_contexts'] ) ? array_unique( $GLOBALS['zad_faq_contexts'] ) : array();
	$qa       = array();
	foreach ( $contexts as $ctx ) {
		foreach ( zad_faqs( $ctx ) as $faq ) {
			$qa[] = array(
				'@type'          => 'Question',
				'name'           => $faq[0],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $faq[1],
				),
			);
		}
	}
	if ( $qa ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => $qa,
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
		)
	);
}
add_action( 'wp_footer', 'zad_schema_output', 30 );

/**
 * مسار تنقل للصفحات العادية.
 */
function zad_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	$trail = array( array( 'الرئيسية', home_url( '/' ) ) );
	$wc    = class_exists( 'WooCommerce' );
	if ( $wc && ( is_product_taxonomy() || is_search() ) ) {
		$trail[] = array( 'كل الأصناف', wc_get_page_permalink( 'shop' ) );
	}
	if ( is_search() ) {
		$current = 'نتائج البحث';
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term    = get_queried_object();
		$cats    = zad_categories();
		$current = ( $term && is_tax( 'product_cat' ) && isset( $cats[ $term->slug ] ) ) ? $cats[ $term->slug ]['title'] : single_term_title( '', false );
	} elseif ( $wc && is_shop() ) {
		$current = 'كل الأصناف';
	} elseif ( is_archive() ) {
		$current = wp_strip_all_tags( get_the_archive_title() );
	} else {
		$current = get_the_title();
	}
	$sep = '<span class="zd-bc__sep" aria-hidden="true">‹</span>';
	$out = '';
	foreach ( $trail as $item ) {
		$out .= sprintf( '<a href="%s">%s</a>%s', esc_url( $item[1] ), esc_html( $item[0] ), $sep );
	}
	printf( '<nav class="zd-bc" aria-label="مسار التنقل">%s<span aria-current="page">%s</span></nav>', $out, esc_html( $current ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- الروابط مهرّبة أعلاه.
}
