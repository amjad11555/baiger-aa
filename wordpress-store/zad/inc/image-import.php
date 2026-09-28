<?php
/**
 * استيراد صور المنتجات الرسمية من مواقع العلامات (إيتي، أولكر، بونوتشي).
 *
 * يعمل على استضافتك مباشرة:
 * 1) صفحات مصدر محفوظة لكل منتج (inc/data/image-sources.php): وُجدت بالبحث عن اسم كل منتج،
 *    الصفحة الرسمية أولاً ثم صفحته في متاجر تركية كبرى (A101، Migros).
 * 2) للمنتجات غير المذكورة: فهرسة موقع العلامة (sitemap أو زحف محدود) ومطابقة الاسم التركي.
 * 3) استخراج صورة المنتج من الصفحة (بيانات Product أو og:image أو أنسب <img>).
 * 4) مراجعة النتائج ثم تنزيل الصور وتعيينها صورة رئيسية للمنتج.
 *
 * لوحة التحكم: المنتجات ← الصور الرسمية   |   WP-CLI: wp zad images
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * مصادر الصور لكل علامة (قابلة للتعديل عبر الفلتر zad_image_sources).
 *
 * @return array
 */
function zad_image_sources() {
	return apply_filters(
		'zad_image_sources',
		array(
			'eti'     => array(
				'base'  => 'https://www.etietieti.com',
				'start' => array( 'https://www.etietieti.com/tr-tr' ),
			),
			'ulker'   => array(
				'base'  => 'https://www.ulker.com.tr',
				'start' => array( 'https://www.ulker.com.tr/tr' ),
			),
			'bonucci' => array(
				'base'  => 'https://www.bonuccisweet.com',
				'start' => array( 'https://www.bonuccisweet.com/tr/' ),
			),
		)
	);
}

/**
 * صفحات المصدر المحفوظة لمنتج (حسب SKU)، قابلة للتعديل عبر الفلتر zad_image_hints.
 *
 * @param int $product_id رقم المنتج.
 * @return array روابط مرتبة.
 */
function zad_img_hints( $product_id ) {
	static $map = null;
	if ( null === $map ) {
		$map = (array) apply_filters( 'zad_image_hints', include ZAD_DIR . '/inc/data/image-sources.php' );
	}
	$sku = (string) get_post_meta( $product_id, '_sku', true );
	return ( $sku && ! empty( $map[ $sku ] ) ) ? array_values( (array) $map[ $sku ] ) : array();
}

/**
 * جلب صفحة/ملف عبر HTTP بأمان (يمنع العناوين الداخلية).
 *
 * @param string $url الرابط.
 * @return string المحتوى أو فارغ.
 */
function zad_img_get( $url, &$final_url = null ) {
	$final_url = $url;
	$res       = wp_safe_remote_get(
		$url,
		array(
			'timeout'     => 20,
			'redirection' => 5,
			'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
			'headers'     => array( 'Accept-Language' => 'tr-TR,tr;q=0.9,en;q=0.6' ),
		)
	);
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return '';
	}
	// الرابط النهائي بعد التحويلات (لحساب الروابط النسبية بشكل صحيح).
	if ( isset( $res['http_response'] ) && is_object( $res['http_response'] ) && method_exists( $res['http_response'], 'get_response_object' ) ) {
		$obj = $res['http_response']->get_response_object();
		if ( is_object( $obj ) && ! empty( $obj->url ) ) {
			$final_url = $obj->url;
		}
	}
	return (string) wp_remote_retrieve_body( $res );
}

/**
 * المضيف بدون www.
 *
 * @param string $url الرابط.
 * @return string
 */
function zad_img_host( $url ) {
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	return preg_replace( '/^www\./', '', $host );
}

/**
 * تحويل رابط نسبي إلى مطلق.
 *
 * @param string $url  الرابط.
 * @param string $base رابط الصفحة.
 * @return string
 */
function zad_img_abs( $url, $base ) {
	$url = trim( html_entity_decode( $url, ENT_QUOTES, 'UTF-8' ) );
	if ( '' === $url || 0 === strpos( $url, 'data:' ) || 0 === strpos( $url, 'javascript:' ) || 0 === strpos( $url, '#' ) ) {
		return '';
	}
	if ( 0 === strpos( $url, '//' ) ) {
		return 'https:' . $url;
	}
	return WP_Http::make_absolute_url( $url, $base );
}

/**
 * تقسيم نص إلى كلمات مبسّطة (بدون أحرف تركية خاصة).
 *
 * @param string $text النص.
 * @return array
 */
function zad_img_tokens( $text ) {
	$text = strtr(
		mb_strtolower( rawurldecode( (string) $text ), 'UTF-8' ),
		array(
			'ç' => 'c',
			'ğ' => 'g',
			'ı' => 'i',
			'i̇' => 'i',
			'ö' => 'o',
			'ş' => 's',
			'ü' => 'u',
			'â' => 'a',
			'î' => 'i',
		)
	);
	$parts = preg_split( '/[^a-z0-9]+/', $text, -1, PREG_SPLIT_NO_EMPTY );
	return array_values( array_unique( $parts ) );
}

/**
 * هل الكلمتان متطابقتان (مع تسامح في اللواحق التركية)؟
 *
 * @param string $a كلمة.
 * @param string $b كلمة.
 * @return bool
 */
function zad_img_token_match( $a, $b ) {
	if ( $a === $b ) {
		return true;
	}
	if ( strlen( $a ) >= 5 && strlen( $b ) >= 4 ) {
		$n = min( 5, strlen( $a ), strlen( $b ) );
		return substr( $a, 0, $n ) === substr( $b, 0, $n );
	}
	return false;
}

/**
 * كلمات وصفية (نكهة/تغليف/نوع): لا تصلح اسماً للخط، وتعارضها في رابط الصفحة يُبطل المطابقة.
 *
 * @param string $t كلمة.
 * @return bool
 */
function zad_img_is_desc( $t ) {
	static $descriptors = array( 'cikolatali', 'cikolata', 'kakaolu', 'kakao', 'muzlu', 'cilekli', 'cilek', 'limonlu', 'portakalli', 'portakal', 'findikli', 'findik', 'sutlu', 'sut', 'bitter', 'beyaz', 'meyveli', 'karamelli', 'kremali', 'dolgulu', 'kapli', 'kaplamali', 'mini', 'sade', 'acili', 'baharatli', 'peynirli', 'susamli', 'orijinal', 'soslu', 'joleli', 'antep', 'fistikli', 'fistigi', 'visneli', 'uzumlu', 'kayisili', 'frambuazli', 'mozaik', 'extra', 'ekstra', 'klasik', 'yulaf', 'kirmizi', 'tam', 'bugdayli', 'lifli', 'karisik', 'cokodamla', 'aromali', 'kek', 'biskuvi', 'gofret', 'kraker', 'cips', 'bar', 'tablet', 'jelibon', 'misir' );
	foreach ( $descriptors as $d ) {
		if ( zad_img_token_match( $t, $d ) ) {
			return true;
		}
	}
	return false;
}

/**
 * كلمات الاسم التركي للمنتج بدون اسم العلامة وحروف الربط.
 *
 * @param string $tr الاسم التركي.
 * @return array
 */
function zad_img_product_tokens( $tr ) {
	return array_values( array_diff( zad_img_tokens( $tr ), array( 'eti', 'ulker', 'bonucci', 've', 'ile' ) ) );
}

/**
 * اسم الخط: أول كلمة غير وصفية (مثل popkek، browni، crax، أو gofret عند غيابها).
 *
 * @param array $tokens كلمات المنتج.
 * @return string
 */
function zad_img_line_token( $tokens ) {
	foreach ( $tokens as $t ) {
		if ( strlen( $t ) >= 3 && ! zad_img_is_desc( $t ) && ! ctype_digit( $t ) ) {
			return $t;
		}
	}
	return $tokens ? (string) end( $tokens ) : '';
}

/* -------------------------------------------------------------------------
 * 1) الفهرسة
 * ---------------------------------------------------------------------- */

/**
 * فهرسة روابط موقع علامة.
 *
 * @param string $brand المفتاح.
 * @return array ['count'=>int, 'method'=>string]
 */
function zad_img_build_index( $brand ) {
	$sources = zad_image_sources();
	if ( ! isset( $sources[ $brand ] ) ) {
		return array(
			'count'  => 0,
			'method' => 'none',
		);
	}
	$src  = $sources[ $brand ];
	$host = zad_img_host( $src['base'] );
	$urls = array();

	// أ) خرائط الموقع.
	$queue = array( trailingslashit( $src['base'] ) . 'sitemap.xml', trailingslashit( $src['base'] ) . 'sitemap_index.xml' );
	$robots = zad_img_get( trailingslashit( $src['base'] ) . 'robots.txt' );
	if ( $robots && preg_match_all( '/^\s*Sitemap:\s*(\S+)/mi', $robots, $m ) ) {
		$queue = array_merge( $m[1], $queue );
	}
	$seen = array();
	while ( $queue && count( $seen ) < 40 && count( $urls ) < 6000 ) {
		$sm = array_shift( $queue );
		if ( isset( $seen[ $sm ] ) ) {
			continue;
		}
		$seen[ $sm ] = true;
		$xml         = zad_img_get( $sm );
		if ( ! $xml || ! preg_match_all( '#<loc>\s*(?:<!\[CDATA\[)?\s*([^<\]\s]+)#i', $xml, $locs ) ) {
			continue;
		}
		foreach ( $locs[1] as $loc ) {
			$loc = html_entity_decode( $loc, ENT_QUOTES, 'UTF-8' );
			if ( false !== stripos( $xml, '<sitemapindex' ) || preg_match( '/\.xml(\.gz)?(\?|$)/i', $loc ) ) {
				$queue[] = $loc;
			} elseif ( zad_img_host( $loc ) === $host ) {
				$urls[ $loc ] = true;
			}
		}
	}
	$method = 'sitemap';

	// ب) زحف محدود إن لم توجد خريطة موقع مفيدة.
	if ( count( $urls ) < 10 ) {
		$method  = 'crawl';
		$visited = array();
		$frontier = array_map(
			static function ( $u ) {
				return array( $u, 0 );
			},
			(array) $src['start']
		);
		while ( $frontier && count( $visited ) < 250 ) {
			list( $page, $depth ) = array_shift( $frontier );
			if ( isset( $visited[ $page ] ) ) {
				continue;
			}
			$visited[ $page ] = true;
			$final            = $page;
			$html             = zad_img_get( $page, $final );
			if ( ! $html ) {
				continue;
			}
			$visited[ $final ] = true;
			$urls[ $final ]    = true;
			$page              = $final;
			if ( $depth >= 2 || ! preg_match_all( '#<a\s[^>]*href=["\']([^"\']+)["\']#i', $html, $links ) ) {
				continue;
			}
			foreach ( $links[1] as $href ) {
				$abs = zad_img_abs( $href, $page );
				$abs = preg_replace( '/#.*$/', '', $abs );
				if ( ! $abs || zad_img_host( $abs ) !== $host || preg_match( '/\.(jpe?g|png|gif|webp|svg|pdf|zip|css|js|xml|mp4)(\?|$)/i', $abs ) ) {
					continue;
				}
				if ( ! isset( $visited[ $abs ] ) ) {
					$frontier[] = array( $abs, $depth + 1 );
				}
			}
		}
	}

	$list = array_slice( array_keys( $urls ), 0, 6000 );
	update_option( 'zad_img_index_' . $brand, $list, false );

	// صورة المشاركة العامة للموقع (لاستبعادها إن تكررت في كل الصفحات).
	$home = zad_img_get( reset( $src['start'] ) );
	update_option( 'zad_img_site_og_' . $brand, $home ? zad_img_meta_image( $home, reset( $src['start'] ) ) : '', false );

	return array(
		'count'  => count( $list ),
		'method' => $method,
	);
}

/* -------------------------------------------------------------------------
 * 2) المطابقة
 * ---------------------------------------------------------------------- */

/**
 * إيجاد أفضل صفحة رسمية لمنتج.
 *
 * @param int $product_id رقم المنتج.
 * @return array ['url'=>string, 'score'=>float]
 */
function zad_img_match_product( $product_id ) {
	$none  = array(
		'url'   => '',
		'score' => 0,
	);
	$info  = zad_product_info( $product_id );
	$index = $info['brand'] ? (array) get_option( 'zad_img_index_' . $info['brand'], array() ) : array();
	if ( ! $index || ! $info['tr'] ) {
		return $none;
	}
	$tokens = zad_img_product_tokens( $info['tr'] );
	if ( ! $tokens ) {
		return $none;
	}
	$line = zad_img_line_token( $tokens );

	$best = $none;
	foreach ( $index as $url ) {
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		$ut   = array_values( array_diff( zad_img_tokens( $path ), array( 'tr', 'en', 'ar', 'urunler', 'urun', 'products', 'product', 'markalar', 'our', 'brands', 'html', 'php', 'index', 'eti', 'ulker', 'bonucci' ) ) );
		if ( ! $ut ) {
			continue;
		}
		$hit_line = false;
		$matched  = 0;
		$used     = array();
		foreach ( $tokens as $t ) {
			foreach ( $ut as $i => $u ) {
				if ( zad_img_token_match( $t, $u ) ) {
					++$matched;
					$used[ $i ] = true;
					if ( $t === $line ) {
						$hit_line = true;
					}
					break;
				}
			}
		}
		if ( ! $hit_line ) {
			continue;
		}
		$score = $matched / count( $tokens );
		foreach ( $ut as $i => $u ) {
			if ( isset( $used[ $i ] ) ) {
				continue;
			}
			// نكهة مختلفة في رابط الصفحة (مثل peynirli لمنتج acili) تعني منتجاً آخر.
			$score -= ( zad_img_is_desc( $u ) && ! in_array( $u, array( 'kek', 'biskuvi', 'gofret', 'kraker', 'krakerler', 'cips' ), true ) ) ? 0.5 : 0.04;
		}
		if ( $score > $best['score'] ) {
			$best = array(
				'url'   => $url,
				'score' => round( $score, 3 ),
			);
		}
	}
	return $best['score'] >= 0.5 ? $best : array(
		'url'   => '',
		'score' => $best['score'],
	);
}

/* -------------------------------------------------------------------------
 * 3) استخراج الصورة من الصفحة
 * ---------------------------------------------------------------------- */

/**
 * صورة المشاركة (og:image / twitter:image) في الصفحة.
 *
 * @param string $html HTML.
 * @param string $page الرابط.
 * @return string
 */
function zad_img_meta_image( $html, $page ) {
	foreach ( array( 'og:image:secure_url', 'og:image', 'twitter:image' ) as $prop ) {
		if ( preg_match( '#<meta[^>]+(?:property|name)=["\']' . preg_quote( $prop, '#' ) . '["\'][^>]*content=["\']([^"\']+)["\']#i', $html, $m )
			|| preg_match( '#<meta[^>]+content=["\']([^"\']+)["\'][^>]*(?:property|name)=["\']' . preg_quote( $prop, '#' ) . '["\']#i', $html, $m ) ) {
			return zad_img_abs( $m[1], $page );
		}
	}
	return '';
}

/**
 * استخراج أنسب صورة منتج من صفحة.
 *
 * @param string $html    HTML.
 * @param string $page    رابط الصفحة.
 * @param array  $tokens  كلمات المنتج.
 * @param string $site_og صورة المشاركة العامة للموقع (تُستبعد).
 * @return string
 */
function zad_img_extract( $html, $page, $tokens, $site_og = '' ) {
	$cands = array();
	$add   = static function ( $url, $score ) use ( &$cands, $page ) {
		$url = zad_img_abs( $url, $page );
		if ( ! $url ) {
			return;
		}
		$cands[ $url ] = max( isset( $cands[ $url ] ) ? $cands[ $url ] : -99, $score );
	};

	// بيانات Product المنظمة.
	if ( preg_match_all( '#<script[^>]+application/ld\+json[^>]*>(.*?)</script>#is', $html, $blocks ) ) {
		foreach ( $blocks[1] as $json ) {
			if ( false !== stripos( $json, '"Product"' ) && preg_match( '#"image"\s*:\s*(?:\[\s*)?"([^"]+)"#i', $json, $m ) ) {
				$add( stripslashes( $m[1] ), 8 );
			}
		}
	}
	$meta = zad_img_meta_image( $html, $page );
	if ( $meta ) {
		$add( $meta, 5 );
	}

	if ( preg_match_all( '#<img\b[^>]*>#i', $html, $imgs ) ) {
		foreach ( $imgs[0] as $tag ) {
			$src = '';
			foreach ( array( 'data-zoom-image', 'data-large', 'data-src', 'data-lazy-src', 'data-original', 'src' ) as $attr ) {
				if ( preg_match( '#\s' . $attr . '=["\']([^"\']+)["\']#i', $tag, $m ) && 0 !== strpos( $m[1], 'data:' ) ) {
					$src = $m[1];
					break;
				}
			}
			if ( ! $src && preg_match( '#\ssrcset=["\']([^"\'\s,]+)#i', $tag, $m ) ) {
				$src = $m[1];
			}
			if ( ! $src ) {
				continue;
			}
			$alt   = preg_match( '#\salt=["\']([^"\']*)["\']#i', $tag, $m ) ? $m[1] : '';
			$score = 0;
			$words = array_merge( zad_img_tokens( $src ), zad_img_tokens( $alt ) );
			foreach ( $tokens as $t ) {
				foreach ( $words as $w ) {
					if ( zad_img_token_match( $t, $w ) ) {
						$score += 2;
						break;
					}
				}
			}
			if ( preg_match( '#(urun|product|upload|media|content|images?/p)#i', $src ) ) {
				++$score;
			}
			if ( preg_match( '#(logo|icon|favicon|sprite|banner|flag|social|facebook|instagram|twitter|youtube|linkedin|arrow|header|footer|bg[-_]|background|placeholder|loader)#i', $src ) ) {
				$score -= 6;
			}
			if ( preg_match( '#\.svg(\?|$)#i', $src ) ) {
				$score -= 6;
			} elseif ( preg_match( '#\.gif(\?|$)#i', $src ) ) {
				$score -= 2;
			}
			$add( $src, $score );
		}
	}

	if ( $site_og && isset( $cands[ $site_og ] ) ) {
		$cands[ $site_og ] -= 10;
	}
	arsort( $cands );
	foreach ( $cands as $url => $score ) {
		if ( $score > 0 ) {
			return $url;
		}
	}
	return '';
}

/**
 * صورة المشاركة العامة لموقع (شعار المتجر غالباً) لاستبعادها من النتائج.
 *
 * @param string $url   رابط صفحة في الموقع.
 * @param string $brand علامة المنتج.
 * @return string
 */
function zad_img_site_og( $url, $brand ) {
	$host    = zad_img_host( $url );
	$sources = zad_image_sources();
	if ( $brand && isset( $sources[ $brand ] ) && zad_img_host( $sources[ $brand ]['base'] ) === $host && get_option( 'zad_img_site_og_' . $brand ) ) {
		return (string) get_option( 'zad_img_site_og_' . $brand );
	}
	$parts = wp_parse_url( $url );
	$home  = ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'https' ) . '://' . ( isset( $parts['host'] ) ? $parts['host'] : '' ) . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' ) . '/';
	$key   = 'zad_img_og_' . md5( $home );
	$og    = get_transient( $key );
	if ( false === $og ) {
		$html = zad_img_get( $home );
		$og   = $html ? zad_img_meta_image( $html, $home ) : '';
		set_transient( $key, $og, $html ? WEEK_IN_SECONDS : HOUR_IN_SECONDS );
	}
	return (string) $og;
}

/**
 * هل الصفحة التي وصلنا إليها ما زالت صفحة هذا المنتج؟
 * إن حوّلنا المتجر إلى مسار آخر (منتج محذوف ← الرئيسية أو قسم) نشترط ظهور اسم الخط في الرابط أو العنوان.
 *
 * @param string $asked  الرابط المطلوب.
 * @param string $final  الرابط بعد التحويلات.
 * @param string $html   HTML.
 * @param array  $tokens كلمات المنتج.
 * @return bool
 */
function zad_img_page_is_product( $asked, $final, $html, $tokens ) {
	$path = static function ( $u ) {
		return untrailingslashit( strtolower( (string) wp_parse_url( $u, PHP_URL_PATH ) ) );
	};
	if ( $path( $asked ) === $path( $final ) ) {
		return true;
	}
	$line = zad_img_line_token( $tokens );
	if ( ! $line ) {
		return false;
	}
	$title = preg_match( '#<title[^>]*>(.*?)</title>#is', $html, $m ) ? html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' ) : '';
	foreach ( array_merge( zad_img_tokens( $path( $final ) ), zad_img_tokens( $title ) ) as $w ) {
		if ( zad_img_token_match( $line, $w ) ) {
			return true;
		}
	}
	return false;
}

/**
 * البحث عن صورة منتج واحد.
 *
 * ترتيب الصفحات المجرَّبة: الرابط اليدوي/المحفوظ ← صفحات المصدر المحفوظة للمنتج ← مطابقة فهرس موقع العلامة.
 * تُستخدم أول صفحة تُرجع صورة منتج.
 *
 * @param int    $product_id رقم المنتج.
 * @param string $only_page  تجربة هذه الصفحة وحدها (رابط يدوي).
 * @return array ['page'=>string, 'image'=>string]
 */
function zad_img_find( $product_id, $only_page = '' ) {
	$info  = zad_product_info( $product_id );
	$saved = $only_page ? $only_page : (string) get_post_meta( $product_id, '_zad_img_page', true );
	$pages = array_merge( $saved ? array( $saved ) : array(), $only_page ? array() : zad_img_hints( $product_id ) );
	if ( ! $saved ) {
		$match = zad_img_match_product( $product_id );
		if ( $match['url'] ) {
			$pages[] = $match['url'];
		}
	}
	$pages  = array_values( array_unique( array_filter( $pages ) ) );
	$tokens = zad_img_product_tokens( $info['tr'] );
	$result = array(
		'page'  => $pages ? $pages[0] : '',
		'image' => '',
	);

	foreach ( array_slice( $pages, 0, 5 ) as $page ) {
		$final = $page;
		$html  = zad_img_get( $page, $final );
		if ( ! $html || ! zad_img_page_is_product( $page, $final, $html, $tokens ) ) {
			continue;
		}
		$image = zad_img_extract( $html, $final, $tokens, zad_img_site_og( $final, $info['brand'] ) );
		if ( $image ) {
			$result = array(
				'page'  => $final,
				'image' => $image,
			);
			break;
		}
	}

	if ( $result['page'] ) {
		update_post_meta( $product_id, '_zad_img_page', $result['page'] );
	}
	update_post_meta( $product_id, '_zad_img_found', $result['image'] );
	return $result;
}

/* -------------------------------------------------------------------------
 * 4) الاستيراد
 * ---------------------------------------------------------------------- */

/**
 * تنزيل صورة وتعيينها صورة رئيسية للمنتج.
 *
 * @param int    $product_id رقم المنتج.
 * @param string $url        رابط الصورة.
 * @return int|WP_Error رقم المرفق.
 */
function zad_img_sideload( $product_id, $url ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = download_url( $url, 30 );
	if ( is_wp_error( $tmp ) ) {
		return $tmp;
	}
	$mime = function_exists( 'wp_get_image_mime' ) ? wp_get_image_mime( $tmp ) : '';
	$exts = array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/webp' => 'webp',
		'image/gif'  => 'gif',
	);
	if ( ! isset( $exts[ $mime ] ) ) {
		wp_delete_file( $tmp );
		return new WP_Error( 'zad_img_type', 'الملف ليس صورة JPG/PNG/WEBP.' );
	}
	$info = zad_product_info( $product_id );
	$name = sanitize_title( str_replace( '%', '', $info['tr'] ? $info['tr'] : get_the_title( $product_id ) ) );
	$file = array(
		'name'     => ( $name ? $name : 'product-' . $product_id ) . '.' . $exts[ $mime ],
		'tmp_name' => $tmp,
	);
	$att = media_handle_sideload( $file, $product_id, get_the_title( $product_id ) );
	if ( is_wp_error( $att ) ) {
		wp_delete_file( $tmp );
		return $att;
	}
	update_post_meta( $att, '_wp_attachment_image_alt', get_the_title( $product_id ) );
	set_post_thumbnail( $product_id, $att );
	update_post_meta( $product_id, '_zad_img_src', esc_url_raw( $url ) );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients( $product_id );
	}
	return (int) $att;
}

/* -------------------------------------------------------------------------
 * لوحة التحكم
 * ---------------------------------------------------------------------- */

/**
 * القائمة.
 */
function zad_img_menu() {
	add_submenu_page( 'edit.php?post_type=product', 'الصور الرسمية للمنتجات', 'الصور الرسمية', 'manage_woocommerce', 'zad-images', 'zad_img_page' );
}
add_action( 'admin_menu', 'zad_img_menu', 60 );

/**
 * صف في جدول المراجعة.
 *
 * @param WC_Product $p المنتج.
 * @return array
 */
function zad_img_row( $p ) {
	$brands = zad_brands();
	$brand  = zad_product_brand( $p->get_id() );
	return array(
		'id'      => $p->get_id(),
		'name'    => $p->get_name(),
		'tr'      => (string) get_post_meta( $p->get_id(), '_zad_tr', true ),
		'brand'   => isset( $brands[ $brand ] ) ? $brands[ $brand ]['ar'] : '—',
		'current' => $p->get_image_id() ? wp_get_attachment_image_url( $p->get_image_id(), 'thumbnail' ) : '',
		'page'    => (string) get_post_meta( $p->get_id(), '_zad_img_page', true ),
		'found'   => (string) get_post_meta( $p->get_id(), '_zad_img_found', true ),
		'edit'    => get_edit_post_link( $p->get_id(), 'raw' ),
	);
}

/**
 * صفحة الأداة.
 */
function zad_img_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$products = wc_get_products(
		array(
			'status'  => array( 'publish', 'draft', 'private' ),
			'limit'   => -1,
			'orderby' => 'menu_order',
			'order'   => 'ASC',
		)
	);
	$rows     = array_map( 'zad_img_row', $products );
	$counts   = array();
	foreach ( array_keys( zad_image_sources() ) as $b ) {
		$counts[ $b ] = count( (array) get_option( 'zad_img_index_' . $b, array() ) );
	}
	// تُفهرس فقط مواقع العلامات التي لها منتجات بلا صفحات مصدر محفوظة.
	$hinted = 0;
	$index  = array();
	foreach ( $products as $p ) {
		if ( zad_img_hints( $p->get_id() ) ) {
			++$hinted;
		} elseif ( ! $p->get_image_id() ) {
			$index[ zad_product_brand( $p->get_id() ) ] = true;
		}
	}
	$config = array(
		'ajax'   => admin_url( 'admin-ajax.php' ),
		'nonce'  => wp_create_nonce( 'zad_img' ),
		'brands' => array_values( array_intersect( array_keys( zad_image_sources() ), array_keys( $index ) ) ),
		'rows'   => $rows,
	);
	?>
	<div class="wrap zd-img-wrap" dir="rtl">
		<h1>الصور الرسمية للمنتجات</h1>
		<p>تجلب هذه الأداة صورة كل منتج مباشرة من استضافتك وتعيّنها صورةً رئيسية له. لكل منتج صفحات مصدر محفوظة مسبقاً (<strong><?php echo (int) $hinted; ?></strong> منتجاً): صفحته الرسمية على etietieti.com أو ulker.com.tr، أو صفحته في متاجر A101 وMigros، وللباقي تُفهرس المواقع الرسمية ويُطابَق الاسم. راجع الصور قبل الاستيراد، وأدخل رابطاً يدوياً لأي منتج لم تُوجد صورته.</p>
		<p class="description">المنتجات المفهرسة حالياً:
			<?php foreach ( $counts as $b => $n ) : ?>
				<strong><?php echo esc_html( $b ); ?></strong>: <?php echo (int) $n; ?> رابط &nbsp;
			<?php endforeach; ?>
		</p>
		<p class="zd-img-actions">
			<button type="button" class="button button-primary button-hero" id="zd-img-auto">البحث عن صور كل المنتجات</button>
			<button type="button" class="button button-hero" id="zd-img-import" disabled>استيراد الصور المحددة</button>
		</p>
		<div class="zd-img-progress" hidden><div class="zd-img-bar"><span></span></div><p class="zd-img-status" aria-live="polite"></p></div>
		<table class="widefat striped zd-img-table">
			<thead><tr>
				<th style="width:28px"><input type="checkbox" id="zd-img-all" aria-label="تحديد الكل"></th>
				<th>المنتج</th><th style="width:80px">الحالية</th><th style="width:110px">الصورة الرسمية</th><th>الصفحة الرسمية / رابط يدوي</th><th style="width:120px">الحالة</th>
			</tr></thead>
			<tbody id="zd-img-rows"></tbody>
		</table>
	</div>
	<style>
		.zd-img-wrap .zd-img-table img{width:64px;height:64px;object-fit:contain;background:#fff;border:1px solid #ddd;border-radius:8px}
		.zd-img-wrap .zd-img-table td{vertical-align:middle}
		.zd-img-wrap .zd-img-manual{display:flex;gap:6px;margin-top:6px}
		.zd-img-wrap .zd-img-manual input{flex:1;direction:ltr}
		.zd-img-wrap .zd-img-bar{height:10px;background:#eee;border-radius:99px;overflow:hidden;max-width:640px}
		.zd-img-wrap .zd-img-bar span{display:block;height:100%;width:0;background:#CE0006;transition:width .3s}
		.zd-img-wrap .zd-ok{color:#1c9a52;font-weight:600}.zd-img-wrap .zd-bad{color:#b00005}
		.zd-img-wrap .zd-img-actions .button{margin-inline-end:8px}
	</style>
	<script>
	(function(){
		var C = <?php echo wp_json_encode( $config ); ?>;
		var rows = {}; C.rows.forEach(function(r){ rows[r.id] = r; });
		var $ = function(s){ return document.querySelector(s); };
		function esc(s){ return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
		function post(action, data){
			var body = new URLSearchParams(Object.assign({action: action, _ajax_nonce: C.nonce}, data||{}));
			return fetch(C.ajax, {method:'POST', credentials:'same-origin', body: body}).then(function(r){ return r.json(); }).then(function(j){ if(!j || !j.success){ throw new Error((j && j.data) || 'خطأ'); } return j.data; });
		}
		function status(t, pct){ $('.zd-img-progress').hidden = false; $('.zd-img-status').textContent = t; if (pct != null) { $('.zd-img-bar span').style.width = pct + '%'; } }
		function render(){
			var html = '';
			C.rows.forEach(function(r){
				var st = r.current && !r.found ? '<span class="zd-ok">لها صورة</span>' : (r.found ? '<span class="zd-ok">وُجدت صورة</span>' : (r.page ? '<span class="zd-bad">لم تُستخرج صورة</span>' : '<span class="zd-bad">غير مطابق</span>'));
				html += '<tr data-id="'+r.id+'"><td><input type="checkbox" class="zd-img-cb" '+(r.found ? '' : 'disabled')+' '+(r.found && !r.current ? 'checked' : '')+'></td>'+
					'<td><a href="'+esc(r.edit)+'"><strong>'+esc(r.name)+'</strong></a><br><small dir="ltr">'+esc(r.tr)+'</small> · <small>'+esc(r.brand)+'</small></td>'+
					'<td>'+(r.current ? '<img src="'+esc(r.current)+'" alt="">' : '—')+'</td>'+
					'<td>'+(r.found ? '<a href="'+esc(r.found)+'" target="_blank" rel="noopener"><img src="'+esc(r.found)+'" alt="" referrerpolicy="no-referrer"></a>' : '—')+'</td>'+
					'<td>'+(r.page ? '<a href="'+esc(r.page)+'" target="_blank" rel="noopener" dir="ltr">'+esc(r.page.replace(/^https?:\/\//,''))+'</a>' : '')+
					'<div class="zd-img-manual"><input type="url" placeholder="الصق رابط صفحة المنتج أو رابط الصورة مباشرة" aria-label="رابط يدوي"><button type="button" class="button zd-img-save">جلب</button></div></td>'+
					'<td>'+st+'</td></tr>';
			});
			$('#zd-img-rows').innerHTML = html;
			$('#zd-img-import').disabled = !document.querySelector('.zd-img-cb:checked');
		}
		async function batch(action, ids, size, label){
			for (var i = 0; i < ids.length; i += size){
				status(label + ' (' + Math.min(i+size, ids.length) + ' / ' + ids.length + ')', Math.round((i+size)/ids.length*100));
				try {
					var res = await post(action, {ids: ids.slice(i, i+size).join(',')});
					res.forEach(function(r){ rows[r.id] && Object.assign(rows[r.id], r); });
				} catch(e) { status(label + ': ' + e.message); }
				render();
			}
		}
		$('#zd-img-auto').addEventListener('click', async function(){
			this.disabled = true;
			for (var i = 0; i < C.brands.length; i++){
				status('فهرسة موقع ' + C.brands[i] + '…', Math.round(i / C.brands.length * 30));
				try { var r = await post('zad_img_scan', {brand: C.brands[i]}); status('فهرسة ' + C.brands[i] + ': ' + r.count + ' رابط (' + r.method + ')'); } catch(e) { status('تعذّرت فهرسة ' + C.brands[i] + ': ' + e.message); }
			}
			var ids = C.rows.filter(function(r){ return !r.current; }).map(function(r){ return r.id; });
			await batch('zad_img_find', ids, 2, 'البحث عن الصور');
			var found = C.rows.filter(function(r){ return r.found; }).length;
			status('انتهى البحث: وُجدت صور ' + found + ' من أصل ' + C.rows.length + ' منتج. راجع الجدول ثم اضغط «استيراد الصور المحددة».', 100);
			this.disabled = false;
		});
		$('#zd-img-import').addEventListener('click', async function(){
			var ids = Array.prototype.map.call(document.querySelectorAll('.zd-img-cb:checked'), function(cb){ return cb.closest('tr').getAttribute('data-id'); });
			if (!ids.length) { return; }
			this.disabled = true;
			await batch('zad_img_import', ids, 2, 'استيراد الصور');
			status('تم استيراد الصور. افتح المتجر لرؤيتها.', 100);
		});
		document.addEventListener('change', function(e){
			if (e.target.id === 'zd-img-all') { document.querySelectorAll('.zd-img-cb:not(:disabled)').forEach(function(cb){ cb.checked = e.target.checked; }); }
			$('#zd-img-import').disabled = !document.querySelector('.zd-img-cb:checked');
		});
		document.addEventListener('click', async function(e){
			if (!e.target.classList.contains('zd-img-save')) { return; }
			var tr = e.target.closest('tr'), id = tr.getAttribute('data-id'), url = tr.querySelector('input[type=url]').value.trim();
			if (!url) { return; }
			e.target.disabled = true;
			try { var r = await post('zad_img_manual', {id: id, url: url}); Object.assign(rows[id], r); render(); status('تم تحديث «' + rows[id].name + '».'); }
			catch(err) { status('تعذّر الجلب: ' + err.message); e.target.disabled = false; }
		});
		render();
	})();
	</script>
	<?php
}

/**
 * تحقق مشترك لطلبات AJAX.
 */
function zad_img_ajax_guard() {
	check_ajax_referer( 'zad_img' );
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( 'غير مسموح', 403 );
	}
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
}

/**
 * أرقام المنتجات من الطلب.
 *
 * @return array
 */
function zad_img_ajax_ids() {
	$raw = isset( $_POST['ids'] ) ? sanitize_text_field( wp_unslash( $_POST['ids'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	return array_slice( array_filter( array_map( 'absint', explode( ',', $raw ) ) ), 0, 10 );
}

add_action(
	'wp_ajax_zad_img_scan',
	static function () {
		zad_img_ajax_guard();
		$brand = isset( $_POST['brand'] ) ? sanitize_key( wp_unslash( $_POST['brand'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		wp_send_json_success( zad_img_build_index( $brand ) );
	}
);

add_action(
	'wp_ajax_zad_img_find',
	static function () {
		zad_img_ajax_guard();
		$out = array();
		foreach ( zad_img_ajax_ids() as $id ) {
			if ( ! get_post_meta( $id, '_zad_img_manual', true ) ) {
				delete_post_meta( $id, '_zad_img_page' );
			}
			$r     = zad_img_find( $id );
			$out[] = array(
				'id'    => $id,
				'page'  => $r['page'],
				'found' => $r['image'],
			);
		}
		wp_send_json_success( $out );
	}
);

add_action(
	'wp_ajax_zad_img_import',
	static function () {
		zad_img_ajax_guard();
		$out = array();
		foreach ( zad_img_ajax_ids() as $id ) {
			$url = (string) get_post_meta( $id, '_zad_img_found', true );
			if ( ! $url ) {
				continue;
			}
			$att   = zad_img_sideload( $id, $url );
			$out[] = array(
				'id'      => $id,
				'current' => is_wp_error( $att ) ? '' : wp_get_attachment_image_url( $att, 'thumbnail' ),
				'found'   => is_wp_error( $att ) ? $url : '',
			);
		}
		wp_send_json_success( $out );
	}
);

add_action(
	'wp_ajax_zad_img_manual',
	static function () {
		zad_img_ajax_guard();
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$id  = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
		// phpcs:enable
		if ( ! $id || ! $url || ! wc_get_product( $id ) ) {
			wp_send_json_error( 'رابط غير صالح' );
		}
		update_post_meta( $id, '_zad_img_manual', 1 );
		if ( preg_match( '/\.(jpe?g|png|webp|gif)(\?|$)/i', $url ) ) {
			update_post_meta( $id, '_zad_img_found', $url );
			wp_send_json_success(
				array(
					'id'    => $id,
					'found' => $url,
				)
			);
		}
		update_post_meta( $id, '_zad_img_page', $url );
		$r = zad_img_find( $id, $url );
		if ( ! $r['image'] ) {
			wp_send_json_error( 'لم نجد صورة منتج في هذه الصفحة. جرّب لصق رابط الصورة نفسها.' );
		}
		wp_send_json_success(
			array(
				'id'    => $id,
				'page'  => $r['page'],
				'found' => $r['image'],
			)
		);
	}
);

/* -------------------------------------------------------------------------
 * WP-CLI: wp zad images [--brand=eti] [--import] [--force] [--skip-index]
 * ---------------------------------------------------------------------- */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'zad images',
		static function ( $args, $assoc ) {
			$brands = isset( $assoc['brand'] ) ? array( sanitize_key( $assoc['brand'] ) ) : array_keys( zad_image_sources() );
			foreach ( empty( $assoc['skip-index'] ) ? $brands : array() as $b ) {
				$r = zad_img_build_index( $b );
				WP_CLI::log( sprintf( 'فهرسة %s: %d رابط (%s)', $b, $r['count'], $r['method'] ) );
			}
			$products = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => -1,
				)
			);
			$found    = 0;
			$imported = 0;
			foreach ( $products as $p ) {
				if ( ! in_array( zad_product_brand( $p->get_id() ), $brands, true ) ) {
					continue;
				}
				if ( $p->get_image_id() && empty( $assoc['force'] ) ) {
					continue;
				}
				if ( ! get_post_meta( $p->get_id(), '_zad_img_manual', true ) ) {
					delete_post_meta( $p->get_id(), '_zad_img_page' );
				}
				$r = zad_img_find( $p->get_id() );
				WP_CLI::log( sprintf( '%s %s → %s', $r['image'] ? '✓' : '✗', $p->get_sku(), $r['image'] ? $r['image'] : ( $r['page'] ? 'صفحة بلا صورة: ' . $r['page'] : 'غير مطابق' ) ) );
				if ( $r['image'] ) {
					++$found;
					if ( ! empty( $assoc['import'] ) ) {
						$att = zad_img_sideload( $p->get_id(), $r['image'] );
						if ( is_wp_error( $att ) ) {
							WP_CLI::warning( $p->get_sku() . ': ' . $att->get_error_message() );
						} else {
							++$imported;
						}
					}
				}
			}
			WP_CLI::success( sprintf( 'وُجدت %d صورة، واستُوردت %d.', $found, $imported ) );
		},
		array( 'shortdesc' => 'جلب صور المنتجات الرسمية من مواقع العلامات.' )
	);
}
