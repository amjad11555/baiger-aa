<?php
/**
 * رسومات عبوات SVG تلقائية للمنتجات التي لا تملك صورة.
 *
 * كل منتج يحصل على رسم عبوة فريد: الشكل حسب القسم (كيك/بسكويت/شيبس/تسالي/عرض)،
 * والألوان حسب العلامة التجارية والنكهة. تُعرّف الأشكال مرة واحدة في Sprite
 * وتُستدعى بـ <use> مع متغيرات CSS، فتبقى الصفحات خفيفة جداً.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * النكهات: المفتاح => [الاسم، اللون، اللون الداكن].
 *
 * @return array
 */
function zad_flavors() {
	return array(
		'chocolate'   => array( 'شوكولاتة', '#7B3F1D', '#4A230F' ),
		'cocoa'       => array( 'كاكاو', '#6B3A22', '#3F1F10' ),
		'dark'        => array( 'شوكولاتة داكنة', '#4A2412', '#2A1208' ),
		'milk'        => array( 'حليب', '#F5EBDD', '#C9A77E' ),
		'white'       => array( 'شوكولاتة بيضاء', '#F7EEDC', '#D8C3A0' ),
		'banana'      => array( 'موز', '#F7D348', '#C99A12' ),
		'strawberry'  => array( 'فراولة', '#EF4B6C', '#B0203F' ),
		'lemon'       => array( 'ليمون', '#F4E04D', '#C8B21E' ),
		'orange'      => array( 'برتقال', '#F68B1F', '#B85E09' ),
		'vanilla'     => array( 'فانيليا', '#F4E3B5', '#CFAE62' ),
		'hazelnut'    => array( 'بندق', '#B87333', '#7A4A1E' ),
		'caramel'     => array( 'كراميل', '#D39434', '#94621A' ),
		'pistachio'   => array( 'فستق حلبي', '#9BC53D', '#5E8A1C' ),
		'cherry'      => array( 'كرز', '#B3102E', '#7A0A1F' ),
		'raspberry'   => array( 'توت العليق', '#D6336C', '#9C1A48' ),
		'apricot'     => array( 'مشمش', '#F7A35C', '#C06A26' ),
		'coconut'     => array( 'جوز الهند', '#FFFFFF', '#D9CFC0' ),
		'cheese'      => array( 'جبنة', '#F6C343', '#C8911A' ),
		'spicy'       => array( 'حار', '#E84A2F', '#A82A15' ),
		'salty'       => array( 'مملّح', '#E7D3A8', '#B89A5C' ),
		'sesame'      => array( 'سمسم', '#EAD39C', '#B8984F' ),
		'fruit'       => array( 'فواكه مشكّلة', '#F25C54', '#C23B2E' ),
		'raisin'      => array( 'زبيب', '#7E3B5B', '#4F2238' ),
		'mosaic'      => array( 'موزاييك', '#A0522D', '#5C2E14' ),
		'spices'      => array( 'بهارات', '#D2691E', '#8B4513' ),
		'corn'        => array( 'ذرة حلوة', '#F2C230', '#B88D12' ),
		'oat'         => array( 'شوفان', '#D8B98A', '#A5845A' ),
		'peanut'      => array( 'فول سوداني', '#C68E4E', '#8D5E2A' ),
		'honey'       => array( 'عسل', '#E3A21A', '#A87310' ),
		'marshmallow' => array( 'مارشميلو', '#FBE3EC', '#E6A6BE' ),
		'wholegrain'  => array( 'حبوب كاملة', '#C9A26B', '#8C6A3C' ),
		'onion'       => array( 'جبنة وبصل', '#B5CC6A', '#7A9139' ),
		'mixed'       => array( 'تشكيلة', '#EFB455', '#CE0006' ),
		'plain'       => array( 'كلاسيك', '#F1D9A8', '#C9A35F' ),
		'kunafa'      => array( 'فستق وكنافة', '#9BC53D', '#6B4A1F' ),
		'jelly'       => array( 'جيلي فواكه', '#FF6F91', '#C93D63' ),
	);
}

/**
 * بيانات نكهة.
 *
 * @param string $key المفتاح.
 * @return array
 */
function zad_flavor( $key ) {
	$all = zad_flavors();
	return isset( $all[ $key ] ) ? $all[ $key ] : $all['plain'];
}

/**
 * نقاط حافة مسننة (للأطراف المضغوطة في الأغلفة).
 *
 * @param float $x1 بداية.
 * @param float $x2 نهاية.
 * @param float $y  المحور.
 * @param float $h  الارتفاع.
 * @param float $step الخطوة.
 * @param bool  $vertical عمودي.
 * @return string
 */
function zad_zigzag( $x1, $x2, $y, $h, $step, $vertical = false ) {
	$pts = array();
	$i   = 0;
	for ( $x = $x1; $x <= $x2 + 0.01; $x += $step ) {
		$off   = ( 0 === $i % 2 ) ? 0 : $h;
		$pts[] = $vertical ? sprintf( '%.1f %.1f', $y + $off, $x ) : sprintf( '%.1f %.1f', $x, $y + $off );
		++$i;
	}
	return 'M' . implode( ' L', $pts );
}

/**
 * طباعة ملف الـ Sprite مرة واحدة في التذييل.
 */
function zad_art_sprite() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$crimp_l = zad_zigzag( 132, 272, 56, -16, 10, true );
	$crimp_r = zad_zigzag( 132, 272, 344, 16, 10, true );
	$bis_l   = zad_zigzag( 122, 258, 62, -14, 9.7, true );
	$bis_r   = zad_zigzag( 122, 258, 338, 14, 9.7, true );
	$chip_t  = zad_zigzag( 112, 288, 72, -12, 8 );
	$chip_b  = zad_zigzag( 112, 288, 340, 12, 8 );

	?>
<svg xmlns="http://www.w3.org/2000/svg" class="zd-sprite" aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
	<symbol id="zd-bg" viewBox="0 0 400 400">
		<rect width="400" height="400" style="fill:var(--t1)"/>
		<circle cx="200" cy="196" r="148" style="fill:#ECECEF"/>
		<ellipse cx="200" cy="332" rx="118" ry="12" style="fill:#13201F;opacity:.07"/>
	</symbol>
	<symbol id="zd-pack-cake" viewBox="0 0 400 400">
		<path d="<?php echo esc_attr( $crimp_l ); ?> L58 272 L58 132 Z" style="fill:var(--b1)"/>
		<path d="<?php echo esc_attr( $crimp_r ); ?> L342 272 L342 132 Z" style="fill:var(--b1)"/>
		<rect x="52" y="122" width="296" height="160" rx="34" style="fill:var(--b1)"/>
		<rect x="74" y="132" width="252" height="12" rx="6" style="fill:#fff;opacity:.22"/>
		<rect x="74" y="262" width="252" height="8" rx="4" style="fill:#000;opacity:.12"/>
		<path d="M232 252 C196 242 200 176 246 166 C278 148 334 168 330 212 C328 252 282 264 232 252 Z" style="fill:var(--f1)"/>
		<path d="M236 222 L316 204 L316 236 L236 256 Z" style="fill:#E7B465"/>
		<path d="M236 236 L316 218" style="stroke:#FFF3D6;stroke-width:6"/>
		<path d="M236 222 L316 204 L302 190 L226 206 Z" style="fill:var(--f2)"/>
		<path d="M250 212 q6 8 12 0 M276 206 q6 8 12 0" style="stroke:var(--f2);stroke-width:5;fill:none;stroke-linecap:round"/>
		<rect x="76" y="160" width="146" height="44" rx="22" style="fill:var(--b2)"/>
	</symbol>
	<symbol id="zd-pack-biscuit" viewBox="0 0 400 400">
		<path d="<?php echo esc_attr( $bis_l ); ?> L64 258 L64 122 Z" style="fill:var(--b1)"/>
		<path d="<?php echo esc_attr( $bis_r ); ?> L336 258 L336 122 Z" style="fill:var(--b1)"/>
		<rect x="58" y="112" width="284" height="156" rx="22" style="fill:var(--b1)"/>
		<rect x="78" y="122" width="244" height="10" rx="5" style="fill:#fff;opacity:.22"/>
		<rect x="70" y="240" width="260" height="12" style="fill:var(--b2);opacity:.85"/>
		<rect x="78" y="148" width="146" height="44" rx="22" style="fill:var(--b2)"/>
		<circle cx="318" cy="244" r="42" style="fill:#C98B43"/>
		<circle cx="318" cy="244" r="34" style="fill:none;stroke:#B0742F;stroke-width:3;stroke-dasharray:4 7"/>
		<circle cx="266" cy="262" r="50" style="fill:var(--f1)"/>
		<circle cx="272" cy="270" r="50" style="fill:#D69A4E"/>
		<circle cx="272" cy="270" r="41" style="fill:none;stroke:#BE7F37;stroke-width:3;stroke-dasharray:5 7"/>
		<circle cx="258" cy="258" r="3.5" style="fill:#A8692A"/><circle cx="284" cy="260" r="3.5" style="fill:#A8692A"/>
		<circle cx="262" cy="284" r="3.5" style="fill:#A8692A"/><circle cx="288" cy="284" r="3.5" style="fill:#A8692A"/>
		<circle cx="273" cy="271" r="3.5" style="fill:#A8692A"/>
	</symbol>
	<symbol id="zd-pack-chips" viewBox="0 0 400 400">
		<path d="<?php echo esc_attr( $chip_t ); ?> L288 84 L112 84 Z" style="fill:var(--b1)"/>
		<path d="<?php echo esc_attr( $chip_b ); ?> L288 326 L112 326 Z" style="fill:var(--b1)"/>
		<path d="M112 72 H288 L296 98 C320 172 320 250 296 314 L288 340 H112 L104 314 C80 250 80 172 104 98 Z" style="fill:var(--b1)"/>
		<path d="M104 98 H296 M104 314 H296" style="stroke:#fff;stroke-opacity:.35;stroke-width:3"/>
		<path d="M122 110 C112 170 112 240 122 300" style="stroke:#fff;stroke-opacity:.2;stroke-width:10;fill:none;stroke-linecap:round"/>
		<rect x="128" y="112" width="144" height="42" rx="21" style="fill:var(--b2)"/>
		<circle cx="200" cy="268" r="50" style="fill:var(--f1)"/>
		<path d="M158 262 q22 -32 54 -12 q-8 32 -54 12 z" style="fill:#F4C54A;stroke:#D99A1E;stroke-width:3"/>
		<path d="M196 282 q30 -28 56 0 q-28 26 -56 0 z" style="fill:#F7CF5C;stroke:#D99A1E;stroke-width:3"/>
		<path d="M168 296 q18 -20 42 -4 q-16 22 -42 4 z" style="fill:#F2BF3E;stroke:#D99A1E;stroke-width:3"/>
	</symbol>
	<symbol id="zd-pack-snack" viewBox="0 0 400 400">
		<rect x="262" y="146" width="96" height="108" rx="10" style="fill:#5A2D0C"/>
		<rect x="272" y="156" width="36" height="40" rx="6" style="fill:#7B4019"/><rect x="314" y="156" width="36" height="40" rx="6" style="fill:#7B4019"/>
		<rect x="272" y="204" width="36" height="40" rx="6" style="fill:#7B4019"/><rect x="314" y="204" width="36" height="40" rx="6" style="fill:#7B4019"/>
		<path d="M276 140 L266 150 L278 160 L266 170 L278 180 L266 190 L278 200 L266 210 L278 220 L266 230 L278 240 L266 250 L276 260 L250 260 L250 140 Z" style="fill:#D8D8DC"/>
		<rect x="42" y="134" width="226" height="132" rx="16" style="fill:var(--b1)"/>
		<rect x="58" y="144" width="194" height="10" rx="5" style="fill:#fff;opacity:.22"/>
		<rect x="60" y="164" width="150" height="42" rx="21" style="fill:var(--b2)"/>
		<circle cx="236" cy="236" r="24" style="fill:var(--f1);stroke:#fff;stroke-width:4"/>
	</symbol>
	<symbol id="zd-pack-gift" viewBox="0 0 400 400">
		<rect x="88" y="176" width="224" height="150" rx="16" style="fill:var(--b1)"/>
		<rect x="88" y="176" width="224" height="150" rx="16" style="fill:#000;opacity:.08"/>
		<rect x="74" y="144" width="252" height="46" rx="12" style="fill:var(--b1)"/>
		<rect x="186" y="144" width="28" height="182" style="fill:var(--b2)"/>
		<ellipse cx="176" cy="132" rx="30" ry="16" transform="rotate(-24 176 132)" style="fill:var(--b2)"/>
		<ellipse cx="224" cy="132" rx="30" ry="16" transform="rotate(24 224 132)" style="fill:var(--b2)"/>
		<circle cx="200" cy="140" r="10" style="fill:var(--b2)"/>
		<circle cx="318" cy="112" r="38" style="fill:var(--f1);stroke:#fff;stroke-width:5"/>
	</symbol>
</svg>
	<?php
}
add_action( 'wp_footer', 'zad_art_sprite', 5 );

/**
 * رسم العبوة لمنتج.
 *
 * @param WC_Product|int $product المنتج.
 * @param string         $variant card|mini|large.
 * @return string
 */
function zad_product_art( $product, $variant = 'card' ) {
	if ( is_numeric( $product ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product );
	}
	if ( ! $product ) {
		return '';
	}
	$id     = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	$info   = zad_product_info( $id );
	$brands = zad_brands();
	$brand  = isset( $brands[ $info['brand'] ] ) ? $brands[ $info['brand'] ] : array(
		'latin' => get_bloginfo( 'name' ),
		'ar'    => '',
		'c1'    => '#222222',
		'c2'    => '#56CFE1',
	);
	$flv    = zad_flavor( $info['flavor'] ? $info['flavor'] : 'plain' );
	$shapes = array(
		'cake'     => 'cake',
		'biscuits' => 'biscuit',
		'chips'    => 'chips',
		'snacks'   => 'snack',
		'offers'   => 'gift',
	);
	$shape  = isset( $shapes[ $info['cat'] ] ) ? $shapes[ $info['cat'] ] : 'snack';
	$hash   = crc32( (string) $id );
	$jitter = ( $hash % 5 ) - 2;
	$rot    = array(
		'cake'    => -4,
		'biscuit' => 3,
		'chips'   => -2,
		'snack'   => -5,
		'gift'    => 0,
	);
	$angle  = $rot[ $shape ] + ( 'gift' === $shape ? 0 : (int) round( $jitter / 2 ) );

	$style = sprintf(
		'--b1:%1$s;--b2:%2$s;--f1:%3$s;--f2:%4$s;--t1:%5$s',
		$brand['c1'],
		$brand['c2'],
		$flv[1],
		$flv[2],
		'#F6F6F8'
	);

	$texts = '';
	if ( 'mini' !== $variant ) {
		$line = $info['line'] ? $info['line'] : wp_trim_words( $product->get_name(), 2, '' );
		// مواضع النصوص لكل شكل: [x, brandY, lineY, flavorY, maxWidth].
		$pos = array(
			'cake'    => array( 149, 191, 240, 264, 158 ),
			'biscuit' => array( 151, 179, 222, 234, 160 ),
			'chips'   => array( 200, 142, 188, 212, 170 ),
			'snack'   => array( 135, 194, 236, 256, 170 ),
			'gift'    => array( 200, 0, 262, 294, 190 ),
		);
		list( $x, $by, $ly, $fy, $maxw ) = $pos[ $shape ];
		$len   = max( 1, mb_strlen( $line ) );
		$fsize = (int) max( 15, min( 30, floor( $maxw / ( $len * 0.56 ) ) ) );
		$bsize = mb_strlen( $brand['latin'] ) > 5 ? 21 : 25;

		if ( $by ) {
			$texts .= sprintf(
				'<text x="%1$d" y="%2$d" text-anchor="middle" class="zd-art__brand" style="fill:var(--b1);font-size:%3$dpx">%4$s</text>',
				$x,
				$by,
				$bsize,
				esc_html( $brand['latin'] )
			);
		}
		$texts .= sprintf(
			'<text x="%1$d" y="%2$d" text-anchor="middle" direction="rtl" class="zd-art__line" style="font-size:%3$dpx">%4$s</text>',
			$x,
			$ly,
			$fsize,
			esc_html( $line )
		);
		if ( 'biscuit' !== $shape ) {
			$texts .= sprintf(
				'<text x="%1$d" y="%2$d" text-anchor="middle" direction="rtl" class="zd-art__flavor">%3$s</text>',
				$x,
				$fy,
				esc_html( 'gift' === $shape ? 'باقة جملة موفّرة' : $flv[0] )
			);
		}
		if ( 'gift' === $shape ) {
			$texts .= '<text x="318" y="124" text-anchor="middle" class="zd-art__badge">%</text>';
		}
	}

	$decor = '';
	if ( 'large' === $variant ) {
		$decor = '<g class="zd-art__crumbs" style="fill:var(--f1);opacity:.5"><circle cx="70" cy="96" r="8"/><circle cx="336" cy="318" r="6"/><circle cx="350" cy="90" r="5"/></g>';
	}

	return sprintf(
		'<svg class="zd-art zd-art--%1$s zd-art--%2$s" viewBox="0 0 400 400" role="img" aria-label="%3$s" style="%4$s"><use href="#zd-bg" width="400" height="400"/>%5$s<g transform="rotate(%6$d 200 200)"><use href="#zd-pack-%1$s" width="400" height="400"/>%7$s</g></svg>',
		esc_attr( $shape ),
		esc_attr( $variant ),
		esc_attr( $product->get_name() ),
		esc_attr( $style ),
		$decor,
		$angle,
		$texts
	);
}

/**
 * استخدام رسم العبوة بدل الصورة الافتراضية في الواجهة.
 *
 * @param string     $image       HTML الصورة.
 * @param WC_Product $product     المنتج.
 * @param string     $size        الحجم.
 * @param array      $attr        السمات.
 * @param bool       $placeholder هل يسمح بالبديل.
 * @return string
 */
function zad_filter_product_image( $image, $product, $size, $attr, $placeholder ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $image;
	}
	if ( ! $placeholder || ! $product || $product->get_image_id() ) {
		return $image;
	}
	if ( $product->get_parent_id() ) {
		$parent = wc_get_product( $product->get_parent_id() );
		if ( $parent && $parent->get_image_id() ) {
			return $image;
		}
	}
	$variant = ( 'woocommerce_thumbnail' === $size || 'thumbnail' === $size || 'woocommerce_gallery_thumbnail' === $size ) ? 'card' : 'large';
	return zad_product_art( $product, $variant );
}
add_filter( 'woocommerce_product_get_image', 'zad_filter_product_image', 10, 5 );

/**
 * رسم كبير في صفحة المنتج بدل الصورة البديلة.
 *
 * @param string $html          HTML.
 * @param int    $attachment_id المرفق.
 * @return string
 */
function zad_single_placeholder_html( $html, $attachment_id ) {
	global $product;
	if ( $product instanceof WC_Product && ! $product->get_image_id() && false !== strpos( $html, 'placeholder' ) ) {
		return '<div class="woocommerce-product-gallery__image--placeholder zd-gallery-art">' . zad_product_art( $product, 'large' ) . '</div>';
	}
	return $html;
}
add_filter( 'woocommerce_single_product_image_thumbnail_html', 'zad_single_placeholder_html', 10, 2 );
