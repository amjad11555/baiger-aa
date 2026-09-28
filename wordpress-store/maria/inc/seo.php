<?php
/**
 * السيو العربي: العناوين، الوصف، Open Graph، البيانات المنظمة (Schema)، الفهرسة.
 *
 * يتنحّى تلقائياً عن العناوين والوصف إذا كانت إضافة سيو مفعّلة (Yoast / Rank Math / AIOSEO / SEOPress)،
 * ويُبقي على البيانات المنظمة الخاصة بالمتجر والأسئلة الشائعة.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

/**
 * هل توجد إضافة سيو مفعلة؟
 *
 * @return bool
 */
function maria_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * قص نص الوصف بطول مناسب.
 *
 * @param string $text النص.
 * @param int    $max  الطول.
 * @return string
 */
function maria_seo_trim( $text, $max = 158 ) {
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
function maria_current_template_key() {
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
 * عنوان الصفحة.
 *
 * @param array $parts أجزاء العنوان.
 * @return array
 */
function maria_title_parts( $parts ) {
	if ( maria_seo_plugin_active() ) {
		return $parts;
	}
	$site = get_bloginfo( 'name' );

	if ( is_front_page() ) {
		return array(
			'title'   => $site,
			'tagline' => maria_opt( 'seo_tagline' ),
		);
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		$parts['title'] = single_post_title( '', false ) . ' بالجملة';
	} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		if ( 'offers' === $term->slug ) {
			$parts['title'] = 'عروض وتخفيضات حلويات الجملة للبقالات';
		} else {
			$parts['title'] = sprintf( '%s بالجملة للبقالات والماركت – إيتي وأولكر وبونوتشي', $term->name );
		}
	} elseif ( is_tax( 'product_brand' ) ) {
		$term           = get_queried_object();
		$parts['title'] = sprintf( 'منتجات %s بالجملة – كيك وبسكويت وتسالي', $term->name );
	} elseif ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
		$parts['title'] = 'متجر الجملة: كيك، بسكويت، شيبس وتسالي تركية';
	} else {
		$titles = array(
			'quick_order'     => 'الطلب السريع للبقاليات – اطلب بالكرتونة خلال دقيقة',
			'special_request' => 'اطلب منتجاً غير متوفر – نؤمّن كل احتياجات بقالتك',
			'export'          => 'تصدير حلويات تركية بالجملة – حاويات وشحن دولي',
			'contact'         => 'تواصل معنا – واتساب وهاتف وعنوان المستودع',
		);
		$key = maria_current_template_key();
		if ( $key && isset( $titles[ $key ] ) ) {
			$parts['title'] = $titles[ $key ];
		}
	}
	$parts['site'] = $site;
	unset( $parts['tagline'] );
	return $parts;
}
add_filter( 'document_title_parts', 'maria_title_parts', 20 );
add_filter(
	'document_title_separator',
	static function ( $sep ) {
		return maria_seo_plugin_active() ? $sep : '|';
	}
);

/**
 * الوصف التعريفي للصفحة الحالية.
 *
 * @return string
 */
function maria_meta_description() {
	$site = get_bloginfo( 'name' );
	$city = maria_opt( 'city' );

	if ( is_front_page() ) {
		return maria_seo_trim( sprintf( '%1$s: توريد حلويات تركية بالجملة للبقالات والماركت — كيك وبسكويت وشيبسات وتسالي من إيتي وأولكر وبونوتشي. اطلب بالكرتونة عبر الموقع أو واتساب مع توصيل سريع في %2$s.', $site, $city ) );
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$info  = maria_product_info( $product->get_id() );
			$price = maria_show_prices() && $product->get_price() ? ' سعر الكرتونة ' . maria_money_plain( wc_get_price_to_display( $product ) ) . '.' : '';
			return maria_seo_trim(
				sprintf(
					'اطلب %1$s%2$s بالجملة%3$s.%4$s توصيل سريع للبقالات في %5$s والدفع عند الاستلام.',
					$product->get_name(),
					$info['tr'] ? ' (' . $info['tr'] . ')' : '',
					$info['pack'] ? ' – التعبئة ' . $info['pack'] : '',
					$price,
					$city
				)
			);
		}
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		$desc = term_description( $term );
		if ( $desc ) {
			return maria_seo_trim( $desc );
		}
		return maria_seo_trim( sprintf( 'تسوّق %1$s بالجملة من %2$s: أسعار الكرتونة، توصيل سريع للبقالات والدفع عند الاستلام.', $term->name, $site ) );
	}

	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return maria_seo_trim( sprintf( 'كل منتجات %1$s بالجملة: كيك وبسكويت وشيبسات وتسالي تركية من إيتي وأولكر وبونوتشي بأسعار الكرتونة للبقالات والسوبرماركت.', $site ) );
	}

	if ( is_singular() ) {
		$custom = get_post_meta( get_queried_object_id(), '_maria_meta_desc', true );
		if ( $custom ) {
			return maria_seo_trim( $custom );
		}
		$descs = array(
			'quick_order'     => 'اطلب منتجات بقالتك بالجملة خلال دقيقة: قائمة كاملة بكل الكيك والبسكويت والشيبسات والتسالي، حدّد عدد الكراتين وأرسل الطلب عبر الموقع أو واتساب.',
			'special_request' => 'لم تجد المنتج الذي تحتاجه بقالتك؟ أرسل لنا اسمه وكميته ونؤمّنه لك بسعر الجملة — أي علامة تركية أو مستوردة، مع متابعة عبر واتساب.',
			'export'          => 'تصدير حلويات تركية بالجملة خارج تركيا: بسكويت وكيك وشيبس وشوكولاتة من إيتي وأولكر وبونوتشي. حاويات 20 و40 قدم وطبليات مختلطة مع شهادات المنشأ والحلال.',
			'contact'         => sprintf( 'تواصل مع %s عبر واتساب أو الهاتف لطلبات الجملة والتوصيل للبقالات، وطلبات التصدير خارج تركيا.', $site ),
		);
		$key = maria_current_template_key();
		if ( $key && isset( $descs[ $key ] ) ) {
			return maria_seo_trim( $descs[ $key ] );
		}
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			return maria_seo_trim( has_excerpt( $post ) ? $post->post_excerpt : $post->post_content );
		}
	}

	return maria_seo_trim( maria_opt( 'seo_tagline' ) );
}

/**
 * صورة المشاركة.
 *
 * @return string
 */
function maria_og_image() {
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
	return file_exists( MARIA_DIR . '/assets/img/og-default.jpg' ) ? MARIA_URI . '/assets/img/og-default.jpg' : '';
}

/**
 * الرابط المعياري للصفحة الحالية.
 *
 * @return string
 */
function maria_current_url() {
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
function maria_head_meta() {
	echo '<meta name="theme-color" content="#CE0006">' . "\n";

	if ( maria_seo_plugin_active() ) {
		return;
	}

	$desc  = maria_meta_description();
	$url   = maria_current_url();
	$title = wp_get_document_title();
	$image = maria_og_image();
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
		if ( $product && $product->get_price() && maria_show_prices() ) {
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
add_action( 'wp_head', 'maria_head_meta', 2 );

/**
 * قواعد الفهرسة.
 *
 * @param array $robots القواعد.
 * @return array
 */
function maria_robots( $robots ) {
	$noindex = is_search() || is_404();
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$noindex = true;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['mr_sent'] ) || isset( $_GET['mr_err'] ) || isset( $_GET['orderby'] ) || isset( $_GET['add-to-cart'] ) ) {
		$noindex = true;
	}
	if ( $noindex ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	} elseif ( ! maria_seo_plugin_active() && '0' !== (string) get_option( 'blog_public' ) ) {
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = '-1';
	}
	return $robots;
}
add_filter( 'wp_robots', 'maria_robots', 20 );

/**
 * إزالة خريطة المستخدمين من خريطة الموقع (خصوصية).
 *
 * @param WP_Sitemaps_Provider $provider المزوّد.
 * @param string               $name     الاسم.
 * @return WP_Sitemaps_Provider|false
 */
function maria_sitemap_providers( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'maria_sitemap_providers', 10, 2 );

/* -------------------------------------------------------------------------
 * الأسئلة الشائعة (تُعرض في الصفحات وتُضاف كبيانات FAQPage)
 * ---------------------------------------------------------------------- */

/**
 * الأسئلة الشائعة حسب السياق.
 *
 * @param string $context home|export|contact.
 * @return array
 */
function maria_faqs( $context = 'home' ) {
	$city = maria_opt( 'city' );
	$min  = (float) maria_opt( 'min_order' );
	$min_text = ( $min > 0 && function_exists( 'wc_price' ) ) ? ' والحد الأدنى لقيمة الطلب ' . maria_money_plain( $min ) . '.' : ' ولا يوجد حد أدنى لقيمة الطلب.';

	$home = array(
		array( 'كيف أطلب من المتجر كصاحب بقالة؟', 'افتح صفحة «الطلب السريع» وستجد كل المنتجات في قائمة واحدة مقسّمة إلى كيك وبسكويت وشيبسات وتسالي. اضغط + لتحديد عدد الكراتين لكل صنف، ثم اضغط «إتمام الطلب» أو «أرسل عبر واتساب». العملية تستغرق أقل من دقيقة.' ),
		array( 'هل يوجد حد أدنى للطلب؟', 'يمكنك الطلب بدءاً من كرتونة واحدة لكل صنف،' . $min_text ),
		array( 'ما هي طرق الدفع المتاحة؟', 'الدفع عند الاستلام نقداً، أو بالتحويل البنكي للعملاء الدائمين. لا تحتاج أي بطاقة بنكية لإتمام الطلب.' ),
		array( 'كم تستغرق مدة التوصيل؟', sprintf( 'نوصل الطلبات داخل %s غالباً خلال 24 إلى 48 ساعة، ونرتب التوصيل إلى باقي الولايات التركية حسب الكمية والموقع.', $city ) ),
		array( 'هل المنتجات أصلية وبصلاحية جيدة؟', 'نعم، جميع المنتجات أصلية من شركات إيتي (Eti) وأولكر (Ülker) وبونوتشي (Bonucci) وغيرها، ونحرص على تواريخ صلاحية حديثة تناسب البيع في البقالات.' ),
		array( 'المنتج الذي أحتاجه غير موجود في الموقع، ماذا أفعل؟', 'استخدم صفحة «اطلب منتجاً غير متوفر» واكتب اسم المنتج والكمية، ونحن نؤمّنه لك بسعر الجملة — هدفنا أن نغطي كل احتياجات بقالتك من مكان واحد.' ),
		array( 'هل تبيعون بالجملة خارج تركيا؟', 'نعم، نصدّر إلى الدول العربية وأوروبا وغيرها بطبليات مختلطة أو حاويات 20 و40 قدم. قدّم طلبك من صفحة «الجملة الدولية والتصدير» وسنرسل لك عرض سعر مفصلاً.' ),
	);

	$export = array(
		array( 'ما الحد الأدنى لطلبات التصدير؟', 'نبدأ من طبليات مختلطة (Mixed Pallets) للطلبات التجريبية، والأوفر لك حاوية 20 أو 40 قدم. نساعدك في توزيع الأصناف لتعبئة الحاوية بأفضل شكل.' ),
		array( 'هل يمكن خلط عدة منتجات وعلامات في حاوية واحدة؟', 'نعم، نوفر حاويات مختلطة (Mix Container) تجمع الكيك والبسكويت والشيبسات والشوكولاتة من إيتي وأولكر وبونوتشي وعلامات أخرى في شحنة واحدة.' ),
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
function maria_render_faqs( $context, $title = 'الأسئلة الشائعة' ) {
	$faqs = maria_faqs( $context );
	if ( ! $faqs ) {
		return;
	}
	$GLOBALS['maria_faq_contexts'][] = $context;
	echo '<section class="mr-faq mr-section" id="faq" aria-labelledby="mr-faq-title-' . esc_attr( $context ) . '"><div class="mr-container mr-container--narrow">';
	printf( '<div class="mr-section__head"><span class="mr-kicker">لديك سؤال؟</span><h2 class="mr-section__title" id="mr-faq-title-%1$s">%2$s</h2></div>', esc_attr( $context ), esc_html( $title ) );
	echo '<div class="mr-faq__list">';
	foreach ( $faqs as $i => $faq ) {
		printf(
			'<details class="mr-faq__item"%1$s><summary>%2$s<span class="mr-faq__icon" aria-hidden="true">%3$s</span></summary><div class="mr-faq__answer"><p>%4$s</p></div></details>',
			0 === $i ? ' open' : '',
			esc_html( $faq[0] ),
			maria_icon( 'plus', '', 20 ), // phpcs:ignore WordPress.Security.EscapeOutput
			esc_html( $faq[1] )
		);
	}
	echo '</div></div></section>';
}

/* -------------------------------------------------------------------------
 * البيانات المنظمة (JSON-LD)
 * ---------------------------------------------------------------------- */

/**
 * بيانات المتجر.
 *
 * @return array
 */
function maria_schema_store() {
	$store = array(
		'@type'              => 'WholesaleStore',
		'@id'                => home_url( '/#store' ),
		'name'               => get_bloginfo( 'name' ),
		'description'        => maria_opt( 'seo_tagline' ),
		'url'                => home_url( '/' ),
		'currenciesAccepted' => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'TRY',
		'paymentAccepted'    => 'Cash, Bank Transfer',
		'priceRange'         => '₺₺',
		'areaServed'         => array(
			array(
				'@type' => 'Country',
				'name'  => 'Türkiye',
			),
			array(
				'@type' => 'Place',
				'name'  => 'Middle East, Europe (Export)',
			),
		),
		'knowsAbout'         => array( 'Eti', 'Ülker', 'Bonucci', 'حلويات تركية بالجملة', 'بسكويت', 'كيك', 'شيبس', 'تسالي' ),
	);
	$image = maria_og_image();
	if ( $image ) {
		$store['image'] = $image;
		$store['logo']  = $image;
	}
	if ( maria_opt( 'phone' ) ) {
		$store['telephone'] = maria_opt( 'phone' );
	} elseif ( maria_wa_number() ) {
		$store['telephone'] = '+' . maria_wa_number();
	}
	if ( maria_opt( 'email' ) ) {
		$store['email'] = maria_opt( 'email' );
	}
	$address = array(
		'@type'           => 'PostalAddress',
		'addressLocality' => maria_opt( 'city' ),
		'addressCountry'  => 'TR',
	);
	if ( maria_opt( 'address' ) ) {
		$address['streetAddress'] = maria_opt( 'address' );
	}
	$store['address'] = $address;
	$same             = array_values( maria_socials() );
	if ( $same ) {
		$store['sameAs'] = $same;
	}
	return $store;
}

/**
 * طباعة JSON-LD.
 */
function maria_schema_output() {
	$graph = array( maria_schema_store() );

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

	// الأسئلة الشائعة المعروضة في هذه الصفحة.
	$contexts = isset( $GLOBALS['maria_faq_contexts'] ) ? array_unique( $GLOBALS['maria_faq_contexts'] ) : array();
	$qa       = array();
	foreach ( $contexts as $ctx ) {
		foreach ( maria_faqs( $ctx ) as $faq ) {
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
add_action( 'wp_footer', 'maria_schema_output', 30 );

/**
 * مسار تنقل للصفحات العادية.
 */
function maria_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	printf(
		'<nav class="mr-bc" aria-label="مسار التنقل"><a href="%1$s">الرئيسية</a><span class="mr-bc__sep" aria-hidden="true">‹</span><span aria-current="page">%2$s</span></nav>',
		esc_url( home_url( '/' ) ),
		esc_html( get_the_title() )
	);
}
