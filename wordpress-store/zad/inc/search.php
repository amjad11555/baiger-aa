<?php
/**
 * البحث الذكي: فهرس موحّد للمنتجات والشركات والأقسام.
 *
 * - تطبيع العربية (الهمزات، التاء المربوطة، الألف المقصورة، التشكيل) والتركية (ç ş ğ ı ö ü).
 * - يطابق الاسم العربي والتركي واسم الشركة والقسم ومرادفات شائعة (شيبس، ويفر، شوكولاته…).
 * - يغذّي البحث الفوري (REST) وصفحة نتائج البحث وفلاتر الأرشيف (?company= و ?section=).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * تطبيع نص للبحث.
 *
 * @param string $s النص.
 * @return string
 */
function zad_normalize( $s ) {
	$s = mb_strtolower( (string) $s, 'UTF-8' );
	$s = preg_replace( '/[\x{064B}-\x{065F}\x{0670}\x{0640}]/u', '', $s );
	$s = strtr(
		$s,
		array(
			'i̇' => 'i',
			'أ'  => 'ا',
			'إ'  => 'ا',
			'آ'  => 'ا',
			'ٱ'  => 'ا',
			'ى'  => 'ي',
			'ة'  => 'ه',
			'ؤ'  => 'و',
			'ئ'  => 'ي',
			'ç'  => 'c',
			'ş'  => 's',
			'ğ'  => 'g',
			'ı'  => 'i',
			'ö'  => 'o',
			'ü'  => 'u',
			'â'  => 'a',
			'î'  => 'i',
		)
	);
	$s = preg_replace( '/[^\p{L}\p{N}]+/u', ' ', $s );
	return trim( preg_replace( '/\s+/u', ' ', $s ) );
}

/**
 * مرادفات الأقسام (كلمات يكتبها أصحاب البقالات).
 *
 * @return array slug => [كلمات]
 */
function zad_category_aliases() {
	return array(
		'cake'     => array( 'كيك', 'كيكه', 'كعك', 'kek', 'cake' ),
		'biscuits' => array( 'بسكويت', 'بسكوت', 'بيسكويت', 'biskuvi', 'biscuit' ),
		'chips'    => array( 'شيبس', 'شيبسات', 'تشيبس', 'جيبس', 'cips', 'chips' ),
		'snacks'   => array( 'تسالي', 'سناكس', 'snack' ),
		'offers'   => array( 'عرض', 'عروض', 'تخفيض', 'تخفيضات', 'خصم', 'offer' ),
	);
}

/**
 * كلمات تقترح القسم في البحث الفوري فقط (لا تُضاف لكل منتجات القسم).
 *
 * @return array slug => [كلمات]
 */
function zad_category_hints() {
	return array(
		'cake'     => array( 'براوني', 'رول', 'كب كيك', 'مافن' ),
		'biscuits' => array( 'ويفر', 'وافر', 'كوكيز', 'gofret', 'kurabiye', 'wafer' ),
		'chips'    => array( 'كراكر', 'كراكرز', 'فشار', 'ذره', 'kraker', 'popcorn' ),
		'snacks'   => array( 'شوكولاته', 'شكولاته', 'شوكولا', 'حلوى', 'حلويات', 'جيلي', 'مارشميلو', 'سكاكر', 'cikolata', 'chocolate', 'bar' ),
		'offers'   => array( 'باقه', 'باقات' ),
	);
}

/**
 * فهرس البحث (مخزّن مؤقتاً ويُحدَّث عند تعديل أي منتج).
 *
 * @return array id => [n, t, h, cat, brand, o, sale]
 */
function zad_search_index() {
	static $index = null;
	if ( null !== $index ) {
		return $index;
	}
	$index = get_transient( 'zad_search_index' );
	if ( is_array( $index ) ) {
		return $index;
	}
	$index = array();
	if ( ! function_exists( 'wc_get_products' ) ) {
		return $index;
	}
	$brands  = zad_all_brands();
	$cats    = zad_categories();
	$aliases = zad_category_aliases();
	$ids     = wc_get_products(
		array(
			'status'     => 'publish',
			'limit'      => -1,
			'visibility' => 'catalog',
			'return'     => 'ids',
			'orderby'    => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
	$order   = 0;
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		if ( ! $p ) {
			continue;
		}
		$info  = zad_product_info( $id );
		$b     = isset( $brands[ $info['brand'] ] ) ? $brands[ $info['brand'] ] : null;
		$parts = array( $p->get_name(), $info['tr'], $info['line'], $p->get_sku() );
		if ( $b ) {
			array_push( $parts, $b['ar'], $b['alt'], $b['latin'] );
		}
		$in_cats = wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'slugs' ) );
		foreach ( is_array( $in_cats ) ? $in_cats : array() as $slug ) {
			if ( isset( $cats[ $slug ] ) ) {
				$parts[] = $cats[ $slug ]['name'];
			}
			if ( isset( $aliases[ $slug ] ) ) {
				$parts = array_merge( $parts, $aliases[ $slug ] );
			}
		}
		$index[ $id ] = array(
			'n'     => zad_normalize( $p->get_name() ),
			't'     => zad_normalize( $info['tr'] ),
			'h'     => ' ' . zad_normalize( implode( ' ', $parts ) ) . ' ',
			'cat'   => $info['cat'],
			'cats'  => is_array( $in_cats ) ? $in_cats : array(),
			'brand' => $info['brand'],
			'o'     => $order++,
			'sale'  => $p->is_on_sale() ? 1 : 0,
		);
	}
	set_transient( 'zad_search_index', $index, DAY_IN_SECONDS );
	return $index;
}

/**
 * مسح الفهرس عند تغيّر المنتجات أو الأقسام أو الشركات.
 */
function zad_search_flush() {
	delete_transient( 'zad_search_index' );
}
foreach ( array( 'woocommerce_update_product', 'woocommerce_new_product', 'before_delete_post', 'trashed_post', 'untrashed_post', 'edited_product_brand', 'edited_product_cat', 'created_product_brand', 'delete_product_brand', 'woocommerce_product_import_inserted_product_object' ) as $zad_hook ) {
	add_action( $zad_hook, 'zad_search_flush' );
}
unset( $zad_hook );

/**
 * مطابقة المنتجات لعبارة بحث مع ترتيب حسب الصلة.
 *
 * @param string $q     العبارة.
 * @param array  $scope قيود اختيارية: brand, cat.
 * @return int[] أرقام المنتجات.
 */
function zad_search_ids( $q, $scope = array() ) {
	$nq     = zad_normalize( $q );
	$tokens = array_values( array_filter( explode( ' ', $nq ), 'strlen' ) );
	if ( ! $tokens ) {
		return array();
	}
	$scored = zad_search_score( $tokens, $nq, $scope, true );
	// لا نتائج لكل الكلمات معاً؟ اعرض ما يطابق بعضها (مثل «كراكرز حار» ← كراكس حار).
	if ( ! $scored && count( $tokens ) > 1 ) {
		$scored = zad_search_score( $tokens, $nq, $scope, false );
	}
	uasort(
		$scored,
		static function ( $a, $b ) {
			return $a[0] === $b[0] ? $a[1] - $b[1] : $b[0] - $a[0];
		}
	);
	return array_map( 'intval', array_keys( $scored ) );
}

/**
 * حساب نقاط المطابقة لكل منتج.
 *
 * @param array  $tokens كلمات البحث المطبّعة.
 * @param string $nq     العبارة كاملة مطبّعة.
 * @param array  $scope  قيود: brand, cat.
 * @param bool   $strict اشتراط كل الكلمات.
 * @return array id => [score, order]
 */
function zad_search_score( $tokens, $nq, $scope, $strict ) {
	$scored = array();
	foreach ( zad_search_index() as $id => $row ) {
		if ( ! empty( $scope['brand'] ) && $row['brand'] !== $scope['brand'] ) {
			continue;
		}
		if ( ! empty( $scope['cat'] ) && ! in_array( $scope['cat'], $row['cats'], true ) ) {
			continue;
		}
		$score = 0;
		$hits  = 0;
		foreach ( $tokens as $t ) {
			$found = false !== strpos( $row['h'], $t );
			if ( ! $found && 0 === strpos( $t, 'ال' ) && mb_strlen( $t ) > 4 ) {
				// كلمة بـ «ال» التعريف: جرّبها بدونها.
				$found = false !== strpos( $row['h'], mb_substr( $t, 2 ) );
			}
			if ( ! $found && ! $strict && mb_strlen( $t ) >= 4 ) {
				// مطابقة جذر الكلمة (أول 4 أحرف) في الوضع المرن.
				$found = false !== strpos( $row['h'], mb_substr( $t, 0, 4 ) );
			}
			if ( ! $found ) {
				if ( $strict ) {
					$score = -1;
					break;
				}
				continue;
			}
			++$hits;
			$score += 2;
			if ( false !== strpos( ' ' . $row['n'], ' ' . $t ) ) {
				$score += 4;
			} elseif ( false !== strpos( $row['n'], $t ) ) {
				$score += 2;
			}
			if ( false !== strpos( ' ' . $row['t'], ' ' . $t ) ) {
				$score += 2;
			}
		}
		if ( $score < 0 || ! $hits ) {
			continue;
		}
		if ( 0 === strpos( $row['n'], $nq ) || 0 === strpos( $row['t'], $nq ) ) {
			$score += 6;
		}
		$scored[ $id ] = array( $score + $hits * 3, $row['o'] );
	}
	return $scored;
}

/**
 * مطابقة الشركات والأقسام لعبارة البحث.
 *
 * @param string $q العبارة.
 * @return array ['brands'=>[], 'cats'=>[]]
 */
function zad_search_terms( $q ) {
	$nq  = zad_normalize( $q );
	$out = array(
		'brands' => array(),
		'cats'   => array(),
	);
	if ( '' === $nq ) {
		return $out;
	}
	$hit = static function ( $words ) use ( $nq ) {
		foreach ( $words as $w ) {
			$w = zad_normalize( $w );
			if ( '' === $w ) {
				continue;
			}
			if ( 0 === strpos( $w, $nq ) || ( mb_strlen( $nq ) >= 3 && false !== strpos( ' ' . $nq . ' ', ' ' . $w . ' ' ) ) ) {
				return true;
			}
		}
		return false;
	};
	foreach ( zad_all_brands() as $slug => $b ) {
		if ( $b['count'] > 0 && $hit( array( $b['ar'], $b['alt'], $b['latin'], $slug ) ) ) {
			$out['brands'][] = array(
				'slug'  => $slug,
				'name'  => $b['ar'],
				'latin' => $b['latin'],
				'url'   => $b['url'],
				'count' => zad_n_items( $b['count'] ),
				'c1'    => $b['c1'],
			);
		}
	}
	$aliases = zad_category_aliases();
	$hints   = zad_category_hints();
	foreach ( zad_categories() as $slug => $c ) {
		$words = array_merge( array( $c['name'] ), isset( $aliases[ $slug ] ) ? $aliases[ $slug ] : array(), isset( $hints[ $slug ] ) ? $hints[ $slug ] : array() );
		if ( $hit( $words ) ) {
			$out['cats'][] = array(
				'slug'  => $slug,
				'name'  => $c['title'],
				'url'   => zad_cat_url( $slug ),
				'count' => zad_n_items( zad_cat_count( $slug ) ),
				'img'   => zad_img_url( $c['image'], true ),
			);
		}
	}
	return $out;
}

/**
 * بيانات منتج لنتائج البحث الفوري.
 *
 * @param WC_Product $p المنتج.
 * @return array
 */
function zad_search_item( $p ) {
	$info   = zad_product_info( $p->get_id() );
	$brands = zad_all_brands();
	$simple = $p->is_type( 'simple' ) && $p->is_purchasable() && $p->is_in_stock();
	return array(
		'id'    => $p->get_id(),
		'name'  => $p->get_name(),
		'url'   => get_permalink( $p->get_id() ),
		'brand' => isset( $brands[ $info['brand'] ] ) ? $brands[ $info['brand'] ]['ar'] : '',
		'pack'  => $info['pack'],
		'price' => zad_show_prices() ? zad_money_plain( wc_get_price_to_display( $p ) ) : '',
		'num'   => (float) wc_get_price_to_display( $p ),
		'sale'  => $p->is_on_sale(),
		'buy'   => $simple,
		'img'   => $p->get_image_id() ? $p->get_image( 'woocommerce_gallery_thumbnail' ) : zad_product_art( $p, 'mini' ),
	);
}

/**
 * مسار REST للبحث الفوري.
 */
function zad_register_search_rest() {
	register_rest_route(
		'zad/v1',
		'/search',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
			'callback'            => 'zad_rest_search',
		)
	);
}
add_action( 'rest_api_init', 'zad_register_search_rest' );

/**
 * نتائج البحث الفوري: منتجات + شركات + أقسام.
 *
 * @param WP_REST_Request $request الطلب.
 * @return WP_REST_Response
 */
function zad_rest_search( $request ) {
	$q = trim( (string) $request->get_param( 'q' ) );
	if ( mb_strlen( $q ) < 2 || ! function_exists( 'wc_get_product' ) ) {
		return rest_ensure_response(
			array(
				'products' => array(),
				'brands'   => array(),
				'cats'     => array(),
				'total'    => 0,
			)
		);
	}
	$ids      = zad_search_ids( $q );
	$products = array();
	foreach ( array_slice( $ids, 0, 8 ) as $id ) {
		$p = wc_get_product( $id );
		if ( $p && $p->is_visible() ) {
			$products[] = zad_search_item( $p );
		}
	}
	$terms    = zad_search_terms( $q );
	$response = rest_ensure_response(
		array(
			'q'        => $q,
			'products' => $products,
			'brands'   => $terms['brands'],
			'cats'     => $terms['cats'],
			'total'    => count( $ids ),
			'url'      => add_query_arg(
				array(
					's'         => $q,
					'post_type' => 'product',
				),
				home_url( '/' )
			),
		)
	);
	$response->header( 'Cache-Control', 'public, max-age=300' );
	return $response;
}

/**
 * فلترة الشركة/القسم المطلوبة في رابط الأرشيف.
 *
 * @return array ['company'=>slug, 'section'=>slug]
 */
function zad_archive_filters() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$company = isset( $_GET['company'] ) ? sanitize_title( wp_unslash( $_GET['company'] ) ) : '';
	$section = isset( $_GET['section'] ) ? sanitize_title( wp_unslash( $_GET['section'] ) ) : '';
	// phpcs:enable
	$brands = zad_all_brands();
	$cats   = zad_categories();
	return array(
		'company' => isset( $brands[ $company ] ) ? $company : '',
		'section' => isset( $cats[ $section ] ) ? $section : '',
	);
}

/**
 * البحث الذكي في صفحة النتائج + فلاتر الشركة والقسم في كل أرشيفات المنتجات.
 *
 * @param WP_Query $q الاستعلام.
 */
function zad_products_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	$is_product_search = $q->is_search() && ( 'product' === $q->get( 'post_type' ) || ( function_exists( 'is_woocommerce' ) && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$is_archive        = $q->is_post_type_archive( 'product' ) || $q->is_tax( array( 'product_cat', 'product_brand', 'product_tag' ) ) || $is_product_search;
	if ( ! $is_archive ) {
		return;
	}
	$f   = zad_archive_filters();
	$tax = (array) $q->get( 'tax_query' );
	if ( $f['company'] && taxonomy_exists( 'product_brand' ) && ! $q->is_tax( 'product_brand' ) ) {
		$tax[] = array(
			'taxonomy' => 'product_brand',
			'field'    => 'slug',
			'terms'    => $f['company'],
		);
	}
	if ( $f['section'] && ! $q->is_tax( 'product_cat' ) ) {
		$tax[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $f['section'],
		);
	}
	if ( count( $tax ) ) {
		$q->set( 'tax_query', $tax );
	}
	if ( $is_product_search ) {
		$ids = zad_search_ids( (string) $q->get( 's' ) );
		$q->set( 'post_type', 'product' );
		$q->set( 'post__in', $ids ? $ids : array( 0 ) );
		$q->set( 'zad_smart', 1 );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['orderby'] ) ) {
			$q->set( 'orderby', 'post__in' );
		}
	}
}
add_action( 'pre_get_posts', 'zad_products_query', 20 );

/**
 * المتجر بلا مدونة: أي بحث في الواجهة هو بحث عن الأصناف، حتى لو جاء من رابط مباشر
 * أو من محرك بحث دون post_type، فيُعرض في قالب الأرشيف بالبطاقات والأسعار.
 *
 * @param array $qv متغيرات الاستعلام.
 * @return array
 */
function zad_search_products_only( $qv ) {
	if ( ! is_admin() && isset( $qv['s'] ) && empty( $qv['post_type'] ) && function_exists( 'wc_get_product' ) ) {
		$qv['post_type'] = 'product';
	}
	return $qv;
}
add_filter( 'request', 'zad_search_products_only' );

// نتيجة بحث واحدة تبقى في صفحة النتائج (مع زر الطلبية) بدل القفز إلى صفحة الصنف، خاصة مع الفلاتر.
add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

/**
 * تعطيل مطابقة LIKE الافتراضية حين يتولى البحث الذكي النتائج.
 *
 * @param string   $search جملة البحث.
 * @param WP_Query $q      الاستعلام.
 * @return string
 */
function zad_disable_like_search( $search, $q ) {
	return $q->get( 'zad_smart' ) ? '' : $search;
}
add_filter( 'posts_search', 'zad_disable_like_search', 50, 2 );

/**
 * عدد المنتجات لكل شركة/قسم داخل النطاق الحالي (لشرائح الفلترة).
 *
 * @param string $field brand|cat.
 * @param array  $scope قيود: brand, cat, ids.
 * @return array slug => count
 */
function zad_facet_counts( $field, $scope = array() ) {
	$out = array();
	foreach ( zad_search_index() as $id => $row ) {
		// نطاق البحث الفارغ يعني «لا نتائج»، لا «كل الكتالوج».
		if ( isset( $scope['ids'] ) && ! isset( $scope['ids'][ $id ] ) ) {
			continue;
		}
		if ( ! empty( $scope['brand'] ) && $row['brand'] !== $scope['brand'] ) {
			continue;
		}
		if ( ! empty( $scope['cat'] ) && ! in_array( $scope['cat'], $row['cats'], true ) ) {
			continue;
		}
		$keys = 'brand' === $field ? array( $row['brand'] ) : $row['cats'];
		foreach ( $keys as $k ) {
			if ( $k ) {
				$out[ $k ] = ( isset( $out[ $k ] ) ? $out[ $k ] : 0 ) + 1;
			}
		}
	}
	return $out;
}
