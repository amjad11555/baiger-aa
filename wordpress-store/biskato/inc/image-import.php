<?php
/**
 * صور المنتجات تلقائياً من الإنترنت (يعمل على استضافتك).
 *
 * لكل منتج بلا صورة:
 * 1) بحث صور باسمه التركي مع العلامة والوزن (Trendyol، ثم Bing Images، ثم DuckDuckGo، ثم Open Food Facts).
 * 2) ترتيب النتائج: تطابق كلمات الاسم والعلامة والوزن، والمتاجر التركية الموثوقة أولاً،
 *    واستبعاد مواقع الصور العامة والتواصل الاجتماعي.
 * 3) تنزيل أفضل صورة صالحة، وتوحيدها: مربع 800×800 بخلفية بيضاء بصيغة WebP (أو JPG).
 * 4) تعيينها صورة رئيسية للمنتج، مع حفظ باقي النتائج لزر «صورة أخرى».
 *
 * يعمل وحده في الخلفية (دفعات كل دقيقة) بعد إضافة المنتجات، ومن لوحة التحكم:
 * المنتجات ← صور المنتجات (مراجعة، صورة أخرى، لصق رابط صورة). سطر الأوامر: wp zad images
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * أدوات
 * ---------------------------------------------------------------------- */

/**
 * طلب HTTP بمتصفح عادي.
 *
 * @param string $url     الرابط.
 * @param array  $headers ترويسات إضافية.
 * @param int    $timeout المهلة.
 * @return array|WP_Error
 */
function zad_img_http( $url, $headers = array(), $timeout = 15 ) {
	return wp_safe_remote_get(
		$url,
		array(
			'timeout'     => $timeout,
			'redirection' => 4,
			'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0 Safari/537.36',
			'headers'     => array_merge(
				array(
					'Accept-Language' => 'tr-TR,tr;q=0.9,en;q=0.7',
					'Accept'          => 'text/html,application/json,image/avif,image/webp,image/*,*/*;q=0.8',
				),
				$headers
			),
		)
	);
}

/**
 * نص مبسّط للمقارنة: أحرف لاتينية صغيرة بلا علامات تركية.
 *
 * @param string $text النص.
 * @return string
 */
function zad_img_fold( $text ) {
	$text = strtr( (string) $text, array( 'İ' => 'i', 'I' => 'i', 'ı' => 'i', 'Ş' => 's', 'ş' => 's', 'Ğ' => 'g', 'ğ' => 'g', 'Ü' => 'u', 'ü' => 'u', 'Ö' => 'o', 'ö' => 'o', 'Ç' => 'c', 'ç' => 'c', 'Â' => 'a', 'â' => 'a' ) );
	$text = strtolower( remove_accents( $text ) );
	return trim( preg_replace( '/[^a-z0-9]+/', ' ', $text ) );
}

/**
 * كلمات الاسم المهمة (بلا وحدات وأرقام وكلمات عامة).
 *
 * @param string $tr الاسم التركي.
 * @return array
 */
function zad_img_tokens( $tr ) {
	$stop = array( 'g', 'gr', 'ml', 'adet', 'li', 'lu', 'lik', 'aile', 'boy', 'kek', 'biskuvi', 'cikolatali', 'sakiz', 'seker', 'and', 'ile', 've', 'the' );
	$out  = array();
	foreach ( explode( ' ', zad_img_fold( $tr ) ) as $w ) {
		if ( strlen( $w ) >= 3 && ! ctype_digit( $w ) && ! in_array( $w, $stop, true ) ) {
			$out[] = $w;
		}
	}
	return array_values( array_unique( $out ) );
}

/**
 * عبارة البحث لمنتج.
 *
 * @param int $product_id المنتج.
 * @return string
 */
function zad_img_query( $product_id ) {
	$q = (string) get_post_meta( $product_id, '_zad_img_query', true );
	if ( '' !== $q ) {
		return $q;
	}
	$tr = (string) get_post_meta( $product_id, '_zad_tr', true );
	if ( '' === $tr ) {
		$tr = get_the_title( $product_id );
	}
	return trim( preg_replace( '/[()]+/', ' ', $tr ) );
}

/**
 * المضيف بلا www.
 *
 * @param string $url الرابط.
 * @return string
 */
function zad_img_host( $url ) {
	return preg_replace( '/^www\./', '', strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) ) );
}

/**
 * مواقع موثوقة لصور المنتجات التركية (تُفضَّل)، ومواقع تُستبعد.
 *
 * @return array [مفضّلة، مستبعدة]
 */
function zad_img_hosts() {
	return apply_filters(
		'zad_img_hosts',
		array(
			array( 'migros', 'migrosone', 'a101', 'carrefoursa', 'sokmarket', 'getir', 'dsmcdn', 'trendyol', 'hepsiburada', 'cimri', 'akakce', 'n11', 'istegelsin', 'macroonline', 'bizimtoptan', 'etietieti', 'ulker', 'elvan', 'solen', 'bonucci', 'simsek', 'toptan', 'market', 'gida', 'sepet', 'cdn' ),
			array( 'pinterest', 'pinimg', 'facebook', 'fbsbx', 'instagram', 'cdninstagram', 'youtube', 'ytimg', 'tiktok', 'twitter', 'twimg', 'alamy', 'shutterstock', 'dreamstime', 'freepik', 'istockphoto', 'gettyimages', 'depositphotos', '123rf', 'vecteezy', 'wikipedia', 'wikimedia', 'reddit', 'blogspot', 'wordpress.com', 'aliexpress', 'alibaba', 'ebay', 'amazon' ),
		)
	);
}

/* -------------------------------------------------------------------------
 * مصادر البحث
 * ---------------------------------------------------------------------- */

/**
 * بحث Trendyol (أكبر متجر تركي، صور منتجات نظيفة على خلفية بيضاء).
 *
 * @param string $q العبارة.
 * @return array
 */
function zad_img_search_trendyol( $q ) {
	$res  = zad_img_http(
		'https://public.trendyol.com/discovery-web-searchgw-service/v2/api/infinite-scroll/sr?q=' . rawurlencode( $q ) . '&qt=' . rawurlencode( $q ) . '&st=' . rawurlencode( $q ) . '&os=1&pi=1&culture=tr-TR&searchStrategyType=DEFAULT',
		array(
			'Accept'  => 'application/json',
			'Origin'  => 'https://www.trendyol.com',
			'Referer' => 'https://www.trendyol.com/',
		)
	);
	$data = is_wp_error( $res ) ? null : json_decode( (string) wp_remote_retrieve_body( $res ), true );
	$out  = array();
	foreach ( isset( $data['result']['products'] ) ? (array) $data['result']['products'] : array() as $p ) {
		if ( empty( $p['images'][0] ) ) {
			continue;
		}
		$img   = (string) $p['images'][0];
		$out[] = array(
			'url'   => 0 === strpos( $img, 'http' ) ? $img : 'https://cdn.dsmcdn.com' . $img,
			'thumb' => '',
			'page'  => isset( $p['url'] ) ? 'https://www.trendyol.com' . $p['url'] : '',
			'title' => trim( ( isset( $p['brand']['name'] ) ? $p['brand']['name'] . ' ' : '' ) . ( isset( $p['name'] ) ? $p['name'] : '' ) ),
			'src'   => 'trendyol',
		);
		if ( count( $out ) >= 20 ) {
			break;
		}
	}
	return $out;
}

/**
 * بحث صور Bing (يقرأ بيانات النتائج من الصفحة نفسها).
 *
 * @param string $q العبارة.
 * @return array
 */
function zad_img_search_bing( $q ) {
	$url = add_query_arg(
		array(
			'q'       => rawurlencode( $q ),
			'form'    => 'HDRSC2',
			'first'   => 1,
			'setmkt'  => 'tr-TR',
			'setlang' => 'tr',
		),
		'https://www.bing.com/images/search'
	);
	$res = zad_img_http( $url, array( 'Cookie' => 'SRCHHPGUSR=ADLT=OFF&NRSLT=35' ) );
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return array();
	}
	$html = (string) wp_remote_retrieve_body( $res );
	$out  = array();
	if ( preg_match_all( '/\bm="(\{[^"]+\})"/', $html, $m ) ) {
		foreach ( $m[1] as $raw ) {
			$d = json_decode( html_entity_decode( $raw, ENT_QUOTES, 'UTF-8' ), true );
			if ( empty( $d['murl'] ) ) {
				continue;
			}
			$out[] = array(
				'url'   => $d['murl'],
				'thumb' => isset( $d['turl'] ) ? $d['turl'] : '',
				'page'  => isset( $d['purl'] ) ? $d['purl'] : '',
				'title' => isset( $d['t'] ) ? wp_strip_all_tags( $d['t'] ) : '',
				'src'   => 'bing',
			);
			if ( count( $out ) >= 30 ) {
				break;
			}
		}
	}
	return $out;
}

/**
 * بحث صور DuckDuckGo (احتياطي).
 *
 * @param string $q العبارة.
 * @return array
 */
function zad_img_search_ddg( $q ) {
	$res = zad_img_http( 'https://duckduckgo.com/?q=' . rawurlencode( $q ) . '&iax=images&ia=images' );
	if ( is_wp_error( $res ) || ! preg_match( '/vqd=["\']?([0-9-]+)/', (string) wp_remote_retrieve_body( $res ), $m ) ) {
		return array();
	}
	$res = zad_img_http(
		'https://duckduckgo.com/i.js?l=tr-tr&o=json&q=' . rawurlencode( $q ) . '&vqd=' . rawurlencode( $m[1] ) . '&f=,,,,,&p=1',
		array( 'Referer' => 'https://duckduckgo.com/' )
	);
	$data = is_wp_error( $res ) ? null : json_decode( (string) wp_remote_retrieve_body( $res ), true );
	$out  = array();
	foreach ( isset( $data['results'] ) ? (array) $data['results'] : array() as $r ) {
		if ( empty( $r['image'] ) ) {
			continue;
		}
		$out[] = array(
			'url'   => $r['image'],
			'thumb' => isset( $r['thumbnail'] ) ? $r['thumbnail'] : '',
			'page'  => isset( $r['url'] ) ? $r['url'] : '',
			'title' => isset( $r['title'] ) ? wp_strip_all_tags( $r['title'] ) : '',
			'src'   => 'ddg',
		);
		if ( count( $out ) >= 30 ) {
			break;
		}
	}
	return $out;
}

/**
 * Open Food Facts (قاعدة منتجات مفتوحة، فيها كثير من منتجات إيتي وأولكر).
 *
 * @param string $q العبارة.
 * @return array
 */
function zad_img_search_off( $q ) {
	$res  = zad_img_http( 'https://world.openfoodfacts.org/cgi/search.pl?search_simple=1&json=1&page_size=12&fields=product_name,brands,quantity,image_front_url,url&search_terms=' . rawurlencode( $q ), array(), 20 );
	$data = is_wp_error( $res ) ? null : json_decode( (string) wp_remote_retrieve_body( $res ), true );
	$out  = array();
	foreach ( isset( $data['products'] ) ? (array) $data['products'] : array() as $p ) {
		if ( empty( $p['image_front_url'] ) ) {
			continue;
		}
		$out[] = array(
			'url'   => $p['image_front_url'],
			'thumb' => '',
			'page'  => isset( $p['url'] ) ? $p['url'] : '',
			'title' => trim( ( isset( $p['brands'] ) ? $p['brands'] . ' ' : '' ) . ( isset( $p['product_name'] ) ? $p['product_name'] : '' ) . ' ' . ( isset( $p['quantity'] ) ? $p['quantity'] : '' ) ),
			'src'   => 'off',
		);
	}
	return $out;
}

/**
 * نقاط ملاءمة نتيجة لمنتج.
 *
 * @param array  $c      النتيجة.
 * @param array  $tokens كلمات الاسم.
 * @param string $brand  اسم العلامة اللاتيني (مبسّطاً).
 * @param string $weight الوزن (رقم) إن وُجد.
 * @return float -1 = مستبعدة.
 */
function zad_img_score( $c, $tokens, $brand, $weight ) {
	list( $good, $bad ) = zad_img_hosts();
	$host               = zad_img_host( $c['url'] ) . ' ' . zad_img_host( $c['page'] );
	foreach ( $bad as $b ) {
		if ( false !== strpos( $host, $b ) ) {
			return -1;
		}
	}
	$hay = ' ' . zad_img_fold( $c['title'] . ' ' . rawurldecode( $c['page'] ) . ' ' . rawurldecode( $c['url'] ) ) . ' ';
	$hit = 0;
	foreach ( $tokens as $t ) {
		// «findikli» تطابق «findik»، و«cilekli» تطابق «cilek».
		if ( false !== strpos( $hay, $t ) || ( strlen( $t ) >= 6 && false !== strpos( $hay, substr( $t, 0, 5 ) ) ) ) {
			++$hit;
		}
	}
	$ratio = $tokens ? $hit / count( $tokens ) : 0.6;
	// أقل من نصف كلمات الاسم: صنف آخر غالباً.
	if ( $ratio < 0.5 ) {
		return -1;
	}
	$score = $ratio;
	if ( $brand && false !== strpos( $hay, $brand ) ) {
		$score += 0.35;
	}
	if ( $weight && preg_match( '/(^|\D)' . preg_quote( $weight, '/' ) . '\s?(g|gr|ml|\D|$)/', $hay ) ) {
		$score += 0.25;
	}
	foreach ( $good as $g ) {
		if ( false !== strpos( $host, $g ) ) {
			$score += 0.3;
			break;
		}
	}
	if ( preg_match( '/\.(jpe?g|png|webp)(\?|$)/i', $c['url'] ) ) {
		$score += 0.05;
	}
	if ( 'off' === $c['src'] ) {
		$score -= 0.1;
	}
	return $score;
}

/**
 * البحث وترتيب النتائج لمنتج.
 *
 * @param int $product_id المنتج.
 * @return array النتائج مرتبة (الأفضل أولاً).
 */
function zad_img_candidates( $product_id ) {
	$q      = zad_img_query( $product_id );
	$tr     = (string) get_post_meta( $product_id, '_zad_tr', true );
	$bslug  = (string) get_post_meta( $product_id, '_zad_brand', true );
	$brands = zad_brands();
	$brand  = ( $bslug && isset( $brands[ $bslug ] ) ) ? zad_img_fold( $brands[ $bslug ]['latin'] ) : '';
	// كلمات الاسم بلا اسم العلامة (للعلامة نقاط مستقلة).
	$tokens = array_values( array_diff( zad_img_tokens( $tr ? $tr : $q ), explode( ' ', $brand ) ) );
	$weight = preg_match( '/(\d+(?:[.,]\d+)?)\s*(g|gr|ml)\b/i', $tr, $m ) ? str_replace( ',', '.', $m[1] ) : '';

	$all = array();
	foreach ( apply_filters( 'zad_img_engines', array( 'zad_img_search_trendyol', 'zad_img_search_bing', 'zad_img_search_ddg', 'zad_img_search_off' ) ) as $engine ) {
		foreach ( (array) call_user_func( $engine, $q ) as $c ) {
			$c['score'] = zad_img_score( $c, $tokens, $brand, $weight );
			if ( $c['score'] >= 0.9 && ! isset( $all[ $c['url'] ] ) ) {
				$all[ $c['url'] ] = $c;
			}
		}
		// نتائج كافية من المصدر الأول: لا داعي للباقي.
		if ( count( $all ) >= 4 ) {
			break;
		}
	}
	$all = array_values( $all );
	usort(
		$all,
		static function ( $a, $b ) {
			return $b['score'] <=> $a['score'];
		}
	);
	return array_slice( $all, 0, 12 );
}

/* -------------------------------------------------------------------------
 * التنزيل والتوحيد
 * ---------------------------------------------------------------------- */

/**
 * تنزيل صورة والتحقق منها.
 *
 * @param string $url     الرابط.
 * @param string $referer الصفحة المصدر.
 * @return string|WP_Error البيانات.
 */
function zad_img_download( $url, $referer = '' ) {
	$res = zad_img_http( $url, $referer ? array( 'Referer' => $referer ) : array(), 20 );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	if ( 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return new WP_Error( 'zad_img_http', 'HTTP ' . (int) wp_remote_retrieve_response_code( $res ) );
	}
	$body = (string) wp_remote_retrieve_body( $res );
	if ( strlen( $body ) < 3000 || strlen( $body ) > 12 * MB_IN_BYTES ) {
		return new WP_Error( 'zad_img_size', 'حجم الملف غير مناسب' );
	}
	$info = @getimagesizefromstring( $body ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	if ( ! $info || ! in_array( $info[2], array( IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF ), true ) ) {
		return new WP_Error( 'zad_img_type', 'ليس صورة JPG/PNG/WEBP' );
	}
	list( $w, $h ) = $info;
	if ( min( $w, $h ) < 220 || $w / max( 1, $h ) > 2.6 || $h / max( 1, $w ) > 2.6 ) {
		return new WP_Error( 'zad_img_dim', sprintf( 'مقاس غير مناسب %d×%d', $w, $h ) );
	}
	return $body;
}

/**
 * توحيد الصورة: مربع بخلفية بيضاء وهامش صغير، 800px، WebP إن أمكن.
 *
 * @param string $bytes البيانات.
 * @return array [bytes, ext]
 */
function zad_img_normalize( $bytes ) {
	if ( ! function_exists( 'imagecreatefromstring' ) ) {
		$info = getimagesizefromstring( $bytes );
		return array( $bytes, image_type_to_extension( $info[2], false ) );
	}
	$src = @imagecreatefromstring( $bytes ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	if ( ! $src ) {
		$info = getimagesizefromstring( $bytes );
		return array( $bytes, image_type_to_extension( $info[2], false ) );
	}
	$w    = imagesx( $src );
	$h    = imagesy( $src );
	$side = 800;
	$pad  = 40;
	$box  = $side - 2 * $pad;
	$k    = min( $box / $w, $box / $h, 2 );
	$nw   = max( 1, (int) round( $w * $k ) );
	$nh   = max( 1, (int) round( $h * $k ) );
	$dst  = imagecreatetruecolor( $side, $side );
	imagefill( $dst, 0, 0, imagecolorallocate( $dst, 255, 255, 255 ) );
	imagealphablending( $dst, true );
	imagecopyresampled( $dst, $src, (int) ( ( $side - $nw ) / 2 ), (int) ( ( $side - $nh ) / 2 ), 0, 0, $nw, $nh, $w, $h );
	ob_start();
	$ext = 'jpg';
	if ( function_exists( 'imagewebp' ) && imagewebp( $dst, null, 82 ) ) {
		$ext = 'webp';
	} else {
		ob_clean();
		imagejpeg( $dst, null, 86 );
	}
	$out = (string) ob_get_clean();
	imagedestroy( $src );
	imagedestroy( $dst );
	return array( $out, $ext );
}

/**
 * حفظ الصورة في المكتبة وتعيينها صورة رئيسية (تحل محل صورة سابقة من المستورد نفسه).
 *
 * @param int    $product_id المنتج.
 * @param string $bytes      البيانات.
 * @param array  $c          النتيجة (للمصدر).
 * @return int|WP_Error المرفق.
 */
function zad_img_attach( $product_id, $bytes, $c ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	list( $data, $ext ) = zad_img_normalize( $bytes );
	$tmp                = wp_tempnam( 'zad-img' );
	file_put_contents( $tmp, $data ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$tr   = (string) get_post_meta( $product_id, '_zad_tr', true );
	$name = sanitize_title( str_replace( '%', '', $tr ? $tr : get_the_title( $product_id ) ) );
	$att  = media_handle_sideload(
		array(
			'name'     => ( $name ? $name : 'product-' . $product_id ) . '.' . $ext,
			'tmp_name' => $tmp,
		),
		$product_id,
		get_the_title( $product_id )
	);
	if ( is_wp_error( $att ) ) {
		wp_delete_file( $tmp );
		return $att;
	}
	update_post_meta( $att, '_wp_attachment_image_alt', get_the_title( $product_id ) . ( $tr ? ' – ' . $tr : '' ) );
	update_post_meta( $att, '_zad_auto_img', 1 );
	$old = (int) get_post_thumbnail_id( $product_id );
	set_post_thumbnail( $product_id, $att );
	if ( $old && $old !== $att && get_post_meta( $old, '_zad_auto_img', true ) ) {
		wp_delete_attachment( $old, true );
	}
	update_post_meta( $product_id, '_zad_img_src', esc_url_raw( $c['url'] ) );
	update_post_meta( $product_id, '_zad_img_page', esc_url_raw( $c['page'] ) );
	update_post_meta( $product_id, '_zad_img_status', 'ok' );
	zad_cache_flush();
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients( $product_id );
	}
	return (int) $att;
}

/**
 * إيجاد صورة لمنتج وتعيينها.
 *
 * @param int  $product_id المنتج.
 * @param bool $next       تجاوز الصورة الحالية إلى النتيجة التالية.
 * @return array [ok, message]
 */
function zad_img_fetch_for( $product_id, $next = false ) {
	$saved = $next ? get_post_meta( $product_id, '_zad_img_cands', true ) : array();
	$cands = is_array( $saved ) ? array_values(
		array_filter(
			$saved,
			static function ( $c ) {
				return is_array( $c ) && ! empty( $c['url'] );
			}
		)
	) : array();
	if ( ! $cands ) {
		$cands = zad_img_candidates( $product_id );
	}
	$current = (string) get_post_meta( $product_id, '_zad_img_src', true );
	update_post_meta( $product_id, '_zad_img_try', time() );
	$tries = 0;
	while ( $cands && $tries < 5 ) {
		$c = array_shift( $cands );
		if ( $next && $c['url'] === $current ) {
			continue;
		}
		++$tries;
		$bytes = zad_img_download( $c['url'], $c['page'] );
		// المصدر يمنع التنزيل؟ نسخة Bing المصغّرة بمقاس أكبر.
		if ( is_wp_error( $bytes ) && ! empty( $c['thumb'] ) ) {
			$bytes = zad_img_download( add_query_arg( array( 'w' => 800, 'h' => 800, 'c' => 7 ), $c['thumb'] ) );
		}
		if ( is_wp_error( $bytes ) ) {
			continue;
		}
		$att = zad_img_attach( $product_id, $bytes, $c );
		if ( ! is_wp_error( $att ) ) {
			update_post_meta( $product_id, '_zad_img_cands', $cands );
			return array( true, zad_img_host( $c['page'] ? $c['page'] : $c['url'] ) );
		}
	}
	update_post_meta( $product_id, '_zad_img_cands', $cands );
	if ( ! get_post_thumbnail_id( $product_id ) ) {
		update_post_meta( $product_id, '_zad_img_status', 'fail' );
	}
	return array( false, 'لم توجد صورة مناسبة' );
}

/**
 * تعيين صورة من رابط يلصقه المدير.
 *
 * @param int    $product_id المنتج.
 * @param string $url        الرابط.
 * @return array [ok, message]
 */
function zad_img_from_url( $product_id, $url ) {
	$bytes = zad_img_download( $url );
	if ( is_wp_error( $bytes ) ) {
		return array( false, $bytes->get_error_message() );
	}
	$att = zad_img_attach(
		$product_id,
		$bytes,
		array(
			'url'  => $url,
			'page' => '',
		)
	);
	return is_wp_error( $att ) ? array( false, $att->get_error_message() ) : array( true, zad_img_host( $url ) );
}

/* -------------------------------------------------------------------------
 * العمل في الخلفية
 * ---------------------------------------------------------------------- */

/**
 * المنتجات التي تحتاج صورة (بلا صورة، ولم تفشل خلال آخر 3 أيام).
 *
 * @param int $limit العدد.
 * @return int[]
 */
function zad_img_pending_ids( $limit = 10 ) {
	return get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'fields'         => 'ids',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'ID'         => 'ASC',
			),
			// phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'NOT EXISTS',
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => '_zad_img_try',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_zad_img_try',
						'value'   => time() - 3 * DAY_IN_SECONDS,
						'compare' => '<',
						'type'    => 'NUMERIC',
					),
				),
			),
		)
	);
}

/**
 * عدد المنتجات بلا صورة.
 *
 * @return array [بلا صورة، الكل]
 */
function zad_img_counts() {
	$all  = (int) wp_count_posts( 'product' )->publish;
	$none = count(
		get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_query'     => array(
					array(
						'key'     => '_thumbnail_id',
						'compare' => 'NOT EXISTS',
					),
				),
			)
		)
	);
	return array( $none, $all );
}

/**
 * دفعة من الصور ضمن مهلة.
 *
 * @param int $budget الثواني.
 * @return array [معالَج، وُجد]
 */
function zad_img_run( $budget = 25 ) {
	if ( get_transient( 'zad_img_lock' ) ) {
		return array( 0, 0 );
	}
	set_transient( 'zad_img_lock', 1, $budget + 40 );
	$until = microtime( true ) + $budget;
	$done  = 0;
	$found = 0;
	foreach ( zad_img_pending_ids( 30 ) as $pid ) {
		if ( microtime( true ) > $until ) {
			break;
		}
		list( $ok ) = zad_img_fetch_for( $pid );
		++$done;
		$found += $ok ? 1 : 0;
	}
	delete_transient( 'zad_img_lock' );
	if ( ! zad_img_pending_ids( 1 ) ) {
		wp_clear_scheduled_hook( 'zad_img_cron' );
	}
	return array( $done, $found );
}

/**
 * بدء العمل في الخلفية.
 */
function zad_img_schedule() {
	if ( ! wp_next_scheduled( 'zad_img_cron' ) && zad_img_pending_ids( 1 ) ) {
		wp_schedule_event( time() + 20, 'zad_minute', 'zad_img_cron' );
	}
}
add_action( 'zad_catalog_done', 'zad_img_schedule' );
add_action( 'after_switch_theme', 'zad_img_schedule' );
add_action(
	'zad_img_cron',
	static function () {
		zad_img_run( 25 );
	}
);
// إن توقفت المهمة (استضافة بلا زيارات)، تُعاد جدولتها عند دخول المدير.
add_action(
	'admin_init',
	static function () {
		if ( ! wp_doing_ajax() && ! get_transient( 'zad_img_checked' ) ) {
			set_transient( 'zad_img_checked', 1, HOUR_IN_SECONDS );
			zad_img_schedule();
		}
	}
);

/* -------------------------------------------------------------------------
 * لوحة التحكم: المنتجات ← صور المنتجات
 * ---------------------------------------------------------------------- */

/**
 * القائمة.
 */
function zad_img_menu() {
	add_submenu_page( 'edit.php?post_type=product', 'صور المنتجات', 'صور المنتجات', 'manage_woocommerce', 'zad-images', 'zad_img_page' );
}
add_action( 'admin_menu', 'zad_img_menu', 60 );

/**
 * الصفحة.
 */
function zad_img_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	list( $none, $all ) = zad_img_counts();
	$filter             = isset( $_GET['show'] ) ? sanitize_key( wp_unslash( $_GET['show'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$args               = array(
		'limit'   => -1,
		'status'  => 'publish',
		'orderby' => 'menu_order',
		'order'   => 'ASC',
	);
	$products           = wc_get_products( $args );
	?>
	<div class="wrap zad-img" dir="rtl">
		<h1>صور المنتجات</h1>
		<p>يبحث القالب عن صورة كل منتج باسمه التركي في متاجر تركيا، ويوحّد مقاسها (مربع بخلفية بيضاء). يعمل وحده في الخلفية، ويمكنك تسريعه من هنا أو تبديل أي صورة.</p>
		<p class="zad-img__stats"><strong><?php echo (int) ( $all - $none ); ?></strong> من <strong><?php echo (int) $all; ?></strong> منتجاً لها صورة.
			<?php if ( $none ) : ?>
				<button type="button" class="button button-primary" id="zad-img-run">ابحث عن الصور الناقصة الآن (<?php echo (int) $none; ?>)</button>
				<span id="zad-img-progress" aria-live="polite"></span>
			<?php endif; ?>
		</p>
		<p>
			<a href="<?php echo esc_url( remove_query_arg( 'show' ) ); ?>" class="<?php echo 'all' === $filter ? 'current' : ''; ?>">الكل</a> |
			<a href="<?php echo esc_url( add_query_arg( 'show', 'missing' ) ); ?>" class="<?php echo 'missing' === $filter ? 'current' : ''; ?>">بلا صورة</a>
		</p>
		<div class="zad-img__grid">
			<?php foreach ( $products as $p ) : ?>
				<?php
				$thumb = $p->get_image_id();
				if ( 'missing' === $filter && $thumb ) {
					continue;
				}
				$src = (string) $p->get_meta( '_zad_img_page' );
				$src = $src ? $src : (string) $p->get_meta( '_zad_img_src' );
				?>
				<figure class="zad-img__card" data-id="<?php echo (int) $p->get_id(); ?>">
					<div class="zad-img__pic"><?php echo $thumb ? wp_get_attachment_image( $thumb, 'thumbnail' ) : '<span>بلا صورة</span>'; ?></div>
					<figcaption>
						<strong><?php echo esc_html( $p->get_name() ); ?></strong>
						<small dir="ltr"><?php echo esc_html( (string) $p->get_meta( '_zad_tr' ) ); ?></small>
						<?php if ( $src ) : ?>
							<a class="zad-img__src" href="<?php echo esc_url( $src ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( zad_img_host( $src ) ); ?></a>
						<?php endif; ?>
						<span class="zad-img__actions">
							<button type="button" class="button button-small" data-act="next"><?php echo $thumb ? 'صورة أخرى' : 'ابحث'; ?></button>
							<button type="button" class="button button-small" data-act="url">رابط صورة</button>
							<a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $p->get_id() ) ); ?>">رفع</a>
						</span>
						<span class="zad-img__msg" aria-live="polite"></span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
	<style>
		.zad-img__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px;margin-top:12px}
		.zad-img__card{margin:0;background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:10px;display:flex;flex-direction:column;gap:8px}
		.zad-img__pic{aspect-ratio:1;display:grid;place-items:center;background:#f6f7f7;border-radius:6px;overflow:hidden}
		.zad-img__pic img{width:100%;height:100%;object-fit:contain}
		.zad-img__card figcaption{display:flex;flex-direction:column;gap:4px;font-size:12px}
		.zad-img__actions{display:flex;gap:4px;flex-wrap:wrap}
		.zad-img__msg{color:#2271b1}
		.zad-img .current{font-weight:700}
	</style>
	<script>
	( function () {
		var nonce = <?php echo wp_json_encode( wp_create_nonce( 'zad_img' ) ); ?>;
		function post( data ) {
			var b = new FormData(); b.append( 'nonce', nonce );
			Object.keys( data ).forEach( function ( k ) { b.append( k, data[ k ] ); } );
			return fetch( ajaxurl, { method: 'POST', body: b, credentials: 'same-origin' } ).then( function ( r ) { return r.json(); } );
		}
		document.querySelectorAll( '.zad-img__card' ).forEach( function ( card ) {
			card.addEventListener( 'click', function ( e ) {
				var btn = e.target.closest( 'button[data-act]' ); if ( ! btn ) { return; }
				var msg = card.querySelector( '.zad-img__msg' ), data = { action: 'zad_img_one', id: card.dataset.id, act: btn.dataset.act };
				if ( 'url' === btn.dataset.act ) { var u = prompt( 'الصق رابط الصورة (jpg / png / webp):' ); if ( ! u ) { return; } data.url = u; }
				msg.textContent = 'جارٍ…'; btn.disabled = true;
				post( data ).then( function ( r ) {
					btn.disabled = false;
					msg.textContent = r && r.data ? r.data.message : 'تعذّر';
					if ( r && r.success && r.data.thumb ) { card.querySelector( '.zad-img__pic' ).innerHTML = r.data.thumb; }
				} ).catch( function () { btn.disabled = false; msg.textContent = 'تعذّر الاتصال'; } );
			} );
		} );
		var run = document.getElementById( 'zad-img-run' ), prog = document.getElementById( 'zad-img-progress' );
		if ( run ) {
			run.addEventListener( 'click', function () {
				run.disabled = true;
				( function step() {
					post( { action: 'zad_img_batch' } ).then( function ( r ) {
						if ( ! r || ! r.success ) { prog.textContent = 'توقف، أعد المحاولة.'; run.disabled = false; return; }
						prog.textContent = 'بقي ' + r.data.left + ' منتجاً بلا صورة';
						if ( r.data.left > 0 && r.data.done > 0 ) { setTimeout( step, 300 ); } else { prog.textContent += ' — انتهى. حدّث الصفحة لرؤية الصور.'; run.disabled = false; }
					} ).catch( function () { setTimeout( step, 3000 ); } );
				} )();
			} );
		}
	} )();
	</script>
	<?php
}

/**
 * التحقق من صلاحية طلبات AJAX.
 */
function zad_img_ajax_guard() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( array( 'message' => 'غير مسموح' ), 403 );
	}
	check_ajax_referer( 'zad_img', 'nonce' );
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
}

add_action(
	'wp_ajax_zad_img_one',
	static function () {
		zad_img_ajax_guard();
		$id  = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$act = isset( $_POST['act'] ) ? sanitize_key( wp_unslash( $_POST['act'] ) ) : 'next';
		if ( ! $id || 'product' !== get_post_type( $id ) ) {
			wp_send_json_error( array( 'message' => 'منتج غير موجود' ) );
		}
		if ( 'url' === $act ) {
			$url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
			list( $ok, $msg ) = $url ? zad_img_from_url( $id, $url ) : array( false, 'رابط غير صالح' );
		} else {
			list( $ok, $msg ) = zad_img_fetch_for( $id, (bool) get_post_thumbnail_id( $id ) );
		}
		$res = array(
			'message' => $ok ? 'تم ✓ ' . $msg : $msg,
			'thumb'   => $ok ? wp_get_attachment_image( get_post_thumbnail_id( $id ), 'thumbnail' ) : '',
		);
		$ok ? wp_send_json_success( $res ) : wp_send_json_error( $res );
	}
);

add_action(
	'wp_ajax_zad_img_batch',
	static function () {
		zad_img_ajax_guard();
		list( $done, $found ) = zad_img_run( 20 );
		list( $none )         = zad_img_counts();
		wp_send_json_success(
			array(
				'done'  => $done,
				'found' => $found,
				'left'  => $none,
			)
		);
	}
);

/**
 * تنبيه التقدّم في لوحة التحكم.
 */
function zad_img_notice() {
	if ( ! current_user_can( 'manage_woocommerce' ) || ! wp_next_scheduled( 'zad_img_cron' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'product_page_zad-images' === $screen->id ) {
		return;
	}
	list( $none, $all ) = zad_img_counts();
	printf(
		'<div class="notice notice-info" dir="rtl"><p><strong>بسكاتو:</strong> يجري البحث عن صور المنتجات في الخلفية — %1$d من %2$d لها صورة. <a href="%3$s">صور المنتجات</a></p></div>',
		(int) ( $all - $none ),
		(int) $all,
		esc_url( admin_url( 'edit.php?post_type=product&page=zad-images' ) )
	);
}
add_action( 'admin_notices', 'zad_img_notice' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'zad images',
		static function ( $args, $assoc ) {
			$limit = isset( $assoc['limit'] ) ? max( 1, (int) $assoc['limit'] ) : 1000;
			$ids   = ! empty( $assoc['redo'] ) ? wc_get_products(
				array(
					'limit'  => $limit,
					'return' => 'ids',
					'status' => 'publish',
				)
			) : zad_img_pending_ids( $limit );
			$found = 0;
			foreach ( $ids as $pid ) {
				list( $ok, $msg ) = zad_img_fetch_for( $pid, ! empty( $assoc['redo'] ) );
				$found           += $ok ? 1 : 0;
				WP_CLI::log( ( $ok ? '✓ ' : '✗ ' ) . get_the_title( $pid ) . ' — ' . $msg );
			}
			WP_CLI::success( sprintf( 'وُجدت %1$d صورة من %2$d.', $found, count( $ids ) ) );
		},
		array( 'shortdesc' => 'البحث عن صور المنتجات وتعيينها (--limit=N، --redo لإعادة البحث للكل).' )
	);
}
