<?php
/**
 * تكامل ووكومرس: التخطيط، بطاقة المنتج، صفحة المنتج، السلة والدفع.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * الغلاف العام لصفحات المتجر
 * ---------------------------------------------------------------------- */
// التصميم كاملاً من القالب: لا حاجة لأنماط ووكومرس الافتراضية.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * فتح الغلاف.
 */
function zad_wc_wrapper_open() {
	echo '<main id="main" class="zd-main zd-shop"><div class="zd-container">';
}
add_action( 'woocommerce_before_main_content', 'zad_wc_wrapper_open', 5 );

/**
 * إغلاق الغلاف.
 */
function zad_wc_wrapper_close() {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'zad_wc_wrapper_close', 50 );

/**
 * مسار التنقل (Breadcrumb).
 *
 * @param array $args الإعدادات.
 * @return array
 */
function zad_breadcrumb_defaults( $args ) {
	$args['delimiter']   = '<span class="zd-bc__sep" aria-hidden="true">‹</span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb zd-bc" aria-label="مسار التنقل">';
	$args['wrap_after']  = '</nav>';
	$args['home']        = 'الرئيسية';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'zad_breadcrumb_defaults' );

/* -------------------------------------------------------------------------
 * صفحات الأرشيف (المتجر، الأقسام، العلامات، البحث)
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );

// في صفحات الأرشيف يظهر مسار التنقل داخل لافتة العنوان.
add_action(
	'wp',
	static function () {
		if ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) {
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}
	}
);

/**
 * رأس الأرشيف المخصص.
 */
function zad_archive_hero() {
	get_template_part( 'template-parts/shop/archive-hero' );
}
add_action( 'woocommerce_shop_loop_header', 'zad_archive_hero' );

/**
 * نص السيو أسفل القسم.
 */
function zad_archive_seo_text() {
	if ( ! is_product_taxonomy() ) {
		return;
	}
	$term = get_queried_object();
	if ( ! $term || empty( $term->term_id ) ) {
		return;
	}
	$text = get_term_meta( $term->term_id, 'zad_seo_text', true );
	if ( ! $text ) {
		return;
	}
	echo '<section class="zd-seo-text zd-prose" aria-label="معلومات عن القسم">' . wp_kses_post( wpautop( $text ) ) . '</section>';
}
add_action( 'woocommerce_after_shop_loop', 'zad_archive_seo_text', 30 );
add_action( 'woocommerce_no_products_found', 'zad_archive_seo_text', 30 );

/**
 * رسالة عند عدم وجود نتائج: توجيه لطلب منتج غير متوفر.
 */
function zad_no_products_cta() {
	printf(
		'<div class="zd-empty"><h2>لم نجد هذا الصنف في القائمة</h2><p>جرّب كلمة أقصر أو اسم العلامة بالتركية، أو أرسل لنا اسم الصنف ونؤمّنه لك من المصدر.</p><div class="zd-empty__actions"><a class="zd-btn zd-btn--primary" href="%1$s">أرسل طلب توريد خاص</a><a class="zd-btn zd-btn--ghost" href="%2$s">تصفّح كل الأصناف</a></div></div>',
		esc_url( zad_page_url( 'special_request' ) ),
		esc_url( wc_get_page_permalink( 'shop' ) )
	);
}
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
add_action( 'woocommerce_no_products_found', 'zad_no_products_cta', 10 );

/**
 * شريط الأدوات بأسلوب Kalles: زر التصفية وعدد النتائج، ثم تبديل الأعمدة والترتيب.
 */
function zad_toolbar_open() {
	$f      = zad_archive_filters();
	$active = ( $f['section'] ? 1 : 0 ) + ( $f['company'] ? 1 : 0 );
	echo '<div class="zd-toolbar"><div class="zd-toolbar__start">';
	printf(
		'<button type="button" class="zd-toolbar__filter" data-zd-open="zd-filter-drawer" aria-controls="zd-filter-drawer" aria-expanded="false">%1$s<span>تصفية</span>%2$s</button>',
		zad_icon( 'filter', '', 20 ), // phpcs:ignore WordPress.Security.EscapeOutput
		$active ? '<span class="zd-toolbar__n">' . (int) $active . '</span>' : ''
	);
}
/**
 * وسط الشريط: تبديل عدد الأعمدة.
 */
function zad_toolbar_middle() {
	echo '</div><div class="zd-toolbar__end"><div class="zd-cols" role="group" aria-label="عدد الأعمدة">';
	foreach ( array( 2, 3, 4 ) as $n ) {
		printf( '<button type="button" class="zd-cols__btn zd-cols__btn--%1$d%2$s" data-zd-cols="%1$d" aria-pressed="%3$s" aria-label="%4$s">%5$s</button>', $n, 4 === $n ? ' is-active' : '', 4 === $n ? 'true' : 'false', esc_attr( $n . ' أعمدة' ), zad_icon( 'cols-' . $n, '', 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}
/**
 * إغلاق شريط الأدوات.
 */
function zad_toolbar_close() {
	echo '</div></div>';
}
add_action( 'woocommerce_before_shop_loop', 'zad_toolbar_open', 19 );
add_action( 'woocommerce_before_shop_loop', 'zad_toolbar_middle', 25 );
add_action( 'woocommerce_before_shop_loop', 'zad_toolbar_close', 31 );

add_filter(
	'loop_shop_per_page',
	static function () {
		return 24;
	}
);
add_filter(
	'loop_shop_columns',
	static function () {
		return 4;
	}
);

/* -------------------------------------------------------------------------
 * الأسعار: السعر للكرتونة + سعر القطعة + وضع «السعر عند الطلب»
 * ---------------------------------------------------------------------- */

/**
 * تعديل عرض السعر.
 *
 * @param string     $html    السعر.
 * @param WC_Product $product المنتج.
 * @return string
 */
function zad_price_html( $html, $product ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $html;
	}
	if ( zad_prices_need_login() ) {
		list( $cta_url, $cta_text ) = zad_price_cta();
		return sprintf( '<a class="zd-price-request zd-price-login" href="%1$s">%2$s</a>', esc_url( $cta_url ), esc_html( $cta_text ) );
	}
	if ( ! zad_show_prices() ) {
		return '<span class="zd-price-request">السعر عند الطلب</span>';
	}
	if ( '' === $html || ! $product ) {
		return $html;
	}
	return $html . ' <small class="zd-per">/ كرتونة</small>';
}
add_filter( 'woocommerce_get_price_html', 'zad_price_html', 20, 2 );
// أسعار الجملة بكسور بعد الخصم (240.10 ₺)، والأسعار الصحيحة بلا «.00».
add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

/**
 * وضع «الأسعار للأعضاء فقط»: لا شراء للزائر قبل تسجيل الدخول
 * (يمنع الإضافة عبر ?add-to-cart وواجهة Store API أيضاً).
 *
 * @param bool $purchasable قابل للشراء.
 * @return bool
 */
function zad_members_purchasable( $purchasable ) {
	return zad_prices_need_login() ? false : $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'zad_members_purchasable', 20 );

/**
 * البيانات المنظّمة للمنتج: لا سعر فيها حين تكون الأسعار مخفية.
 *
 * @param array      $markup  بيانات Product.
 * @param WC_Product $product المنتج.
 * @return array
 */
function zad_structured_data_price( $markup, $product = null ) {
	if ( ! zad_show_prices() ) {
		unset( $markup['offers'] );
	}
	// الاسم التركي المكتوب على العبوة والقسم: يساعدان على الظهور في بحث «اسم الصنف + جملة».
	$id = $product instanceof WC_Product ? $product->get_id() : get_the_ID();
	if ( $id ) {
		$info = zad_product_info( $id );
		if ( $info['tr'] ) {
			$markup['alternateName'] = $info['tr'];
		}
		$cats = zad_categories();
		if ( $info['cat'] && isset( $cats[ $info['cat'] ] ) ) {
			$markup['category'] = $cats[ $info['cat'] ]['title'];
		}
		if ( empty( $markup['brand'] ) && $info['brand'] && isset( zad_brands()[ $info['brand'] ] ) ) {
			$markup['brand'] = array(
				'@type' => 'Brand',
				'name'  => zad_brands()[ $info['brand'] ]['latin'],
			);
		}
	}
	return $markup;
}
add_filter( 'woocommerce_structured_data_product', 'zad_structured_data_price', 20, 2 );

/**
 * واجهة ووكومرس العامة (Store API): إزالة الأسعار من ردود المنتجات حين تكون مخفية.
 *
 * @param WP_REST_Response|WP_Error $response الرد.
 * @param array                     $handler  المعالج.
 * @param WP_REST_Request           $request  الطلب.
 * @return WP_REST_Response|WP_Error
 */
function zad_store_api_hide_prices( $response, $handler, $request ) {
	if ( zad_show_prices() || ! $response instanceof WP_REST_Response || ! preg_match( '#^/wc/store(/v\d+)?/products#', $request->get_route() ) ) {
		return $response;
	}
	$strip = static function ( $item ) {
		if ( is_array( $item ) ) {
			unset( $item['prices'] );
			if ( isset( $item['price_html'] ) ) {
				$item['price_html'] = '';
			}
		}
		return $item;
	};
	$data = $response->get_data();
	$data = isset( $data['id'] ) ? $strip( $data ) : array_map( $strip, (array) $data );
	$response->set_data( $data );
	return $response;
}
add_filter( 'rest_request_after_callbacks', 'zad_store_api_hide_prices', 20, 3 );

/**
 * وضع «السعر عند الطلب»: لا تظهر المبالغ في الطلبية والدفع أيضاً، وإلا كفى إضافة صنف لقراءة السعر.
 * يؤكد قسم المبيعات السعر عند الاتصال، ويظهر في تفاصيل الطلب بعد إرساله.
 *
 * @param string $html المبلغ.
 * @return string
 */
function zad_hide_cart_amount( $html ) {
	if ( zad_show_prices() || ( is_admin() && ! wp_doing_ajax() ) ) {
		return $html;
	}
	return '<span class="zd-price-request">يُؤكَّد عند الاتصال</span>';
}
foreach ( array( 'woocommerce_cart_item_price', 'woocommerce_cart_item_subtotal', 'woocommerce_cart_subtotal', 'woocommerce_cart_totals_order_total_html', 'woocommerce_cart_totals_taxes_total_html' ) as $zad_hook ) {
	add_filter( $zad_hook, 'zad_hide_cart_amount', 99 );
}

/**
 * سطر الكمية في الطلبية المصغّرة: «3 × كرتونة» بدل «3 × السعر» حين تكون الأسعار مخفية.
 *
 * @param string $html     السطر.
 * @param array  $item     عنصر السلة.
 * @return string
 */
function zad_hide_mini_cart_price( $html, $item ) {
	if ( zad_show_prices() ) {
		return $html;
	}
	return sprintf( '<span class="quantity">%d × كرتونة</span>', (int) $item['quantity'] );
}
add_filter( 'woocommerce_widget_cart_item_quantity', 'zad_hide_mini_cart_price', 99, 2 );

/**
 * سعر القطعة الواحدة داخل الكرتونة.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function zad_unit_price_html( $product ) {
	if ( ! zad_show_prices() ) {
		return '';
	}
	$units = (int) get_post_meta( $product->get_id(), '_zad_units', true );
	$price = (float) wc_get_price_to_display( $product );
	if ( $units < 2 || $price <= 0 ) {
		return '';
	}
	$unit = $price / $units;
	// كسور القطعة مهمة للبقال (7.92 ₺ لا 8 ₺) حتى لو كانت أسعار الكرتونة بلا كسور.
	$decimals = abs( $unit - round( $unit ) ) < 0.005 ? 0 : 2;
	return sprintf( '<span class="zd-unit-price">≈ %s للقطعة</span>', wc_price( $unit, array( 'decimals' => $decimals ) ) );
}

/* -------------------------------------------------------------------------
 * أزرار + و − حول حقل الكمية
 * ---------------------------------------------------------------------- */

/**
 * زر الإنقاص.
 */
function zad_qty_minus() {
	echo '<button type="button" class="zd-qty-btn zd-qty-minus" aria-label="إنقاص الكمية">' . zad_icon( 'minus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
/**
 * زر الزيادة.
 */
function zad_qty_plus() {
	echo '<button type="button" class="zd-qty-btn zd-qty-plus" aria-label="زيادة الكمية">' . zad_icon( 'plus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_before_quantity_input_field', 'zad_qty_minus' );
add_action( 'woocommerce_after_quantity_input_field', 'zad_qty_plus' );

/* -------------------------------------------------------------------------
 * بطاقة المنتج (تُستخدم في woocommerce/content-product.php)
 * ---------------------------------------------------------------------- */

/**
 * نسبة الخصم المئوية لمنتج (0 إن لم يكن عليه عرض).
 *
 * @param WC_Product $product المنتج.
 * @return int
 */
function zad_discount_percent( $product ) {
	if ( ! $product->is_on_sale() ) {
		return 0;
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	return ( $regular > 0 && $sale > 0 ) ? (int) round( ( 1 - $sale / $regular ) * 100 ) : 0;
}

/**
 * شارات صورة البطاقة: نفاد الكمية، نسبة الخصم، الباقة، الأعلى طلباً.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function zad_card_badges( $product ) {
	$out = '';
	if ( ! $product->is_in_stock() ) {
		$out .= '<span class="zd-badge zd-badge--muted">نفدت الكمية</span>';
	} else {
		// نسبة الخصم (إيتي 2%، أولكر 5%…) تظهر للجميع، فهي لا تكشف السعر وتشجّع على فتح حساب.
		if ( $product->is_on_sale() && 'hidden' !== zad_price_gate() ) {
			$pct = zad_discount_percent( $product );
			$out .= $pct ? sprintf( '<span class="zd-badge zd-badge--sale">خصم <bdi dir="ltr">%d%%</bdi></span>', $pct ) : '<span class="zd-badge zd-badge--sale">عرض</span>';
		}
		if ( function_exists( 'zad_is_new' ) && zad_engage( 'news' )['badge'] && zad_is_new( $product ) ) {
			$out .= '<span class="zd-badge zd-badge--new">جديد</span>';
		}
		if ( get_post_meta( $product->get_id(), '_zad_bundle', true ) ) {
			$out .= '<span class="zd-badge zd-badge--bundle">باقة</span>';
		} elseif ( $product->is_featured() ) {
			$out .= '<span class="zd-badge zd-badge--hot">الأعلى طلباً</span>';
		}
	}
	return $out ? '<span class="zd-card__badges">' . $out . '</span>' : '';
}

/**
 * متحكم السلة: زر «أضف» ثم عدّاد + / − متزامن فوراً مع السلة.
 *
 * @param WC_Product $product المنتج.
 * @param int        $qty     الكمية الحالية بالسلة.
 * @param string     $context card|row|lg.
 * @return string
 */
function zad_cart_control( $product, $qty = 0, $context = 'card' ) {
	$name = $product->get_name();
	if ( ! $product->is_type( 'simple' ) ) {
		return sprintf( '<a class="zd-btn zd-btn--ghost zd-btn--sm" href="%s">اختر الخيارات</a>', esc_url( $product->get_permalink() ) );
	}
	// وضع «الأسعار للأعضاء فقط»: الزائر يسجّل دخوله أولاً، ولا يصل أي سعر إلى الصفحة.
	if ( zad_prices_need_login() ) {
		$verify = 'verify' === zad_price_gate();
		$label  = $verify ? 'أكّد حسابك للطلب' : 'سجّل للطلب ورؤية السعر';
		return sprintf(
			'<a class="zd-login-buy zd-login-buy--%1$s" href="%2$s">%3$s</a>',
			esc_attr( $context ),
			esc_url( $verify ? wc_get_page_permalink( 'myaccount' ) . '#zd-verify' : add_query_arg( 'tab', 'register', wc_get_page_permalink( 'myaccount' ) ) ),
			'row' === $context ? zad_icon( $verify ? 'whatsapp' : 'user', '', 18 ) . '<span class="screen-reader-text">' . esc_html( $label . ': ' . $name ) . '</span>' : '<span>' . esc_html( $label ) . '</span>'
		);
	}
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return '<span class="zd-cart-ctl zd-cart-ctl--' . esc_attr( $context ) . ' is-disabled"><span class="zd-cart-ctl__na">غير متوفر حالياً</span></span>';
	}
	$price = zad_show_prices() ? (float) wc_get_price_to_display( $product ) : '';
	$label = 'row' === $context ? '<span class="screen-reader-text">أضف</span>' : '<span>أضف إلى الطلبية</span>';
	return sprintf(
		'<div class="zd-cart-ctl zd-cart-ctl--%11$s%1$s" data-id="%2$d" data-qty="%3$d" data-price="%4$s" data-name="%5$s">'
		. '<button type="button" class="zd-cart-ctl__add" aria-label="%6$s">%7$s%12$s</button>'
		. '<div class="zd-cart-ctl__stepper" role="group" aria-label="%8$s">'
		. '<button type="button" class="zd-step zd-step--minus" data-step="-1" aria-label="إنقاص كرتونة">%9$s</button>'
		. '<input type="number" class="zd-step__input" inputmode="numeric" min="0" max="9999" step="1" value="%3$d" aria-label="عدد الكراتين">'
		. '<button type="button" class="zd-step zd-step--plus" data-step="1" aria-label="زيادة كرتونة">%10$s</button>'
		. '</div></div>',
		$qty > 0 ? ' is-active' : '',
		$product->get_id(),
		(int) $qty,
		esc_attr( $price ),
		esc_attr( $name ),
		esc_attr( 'أضف ' . $name . ' إلى الطلبية' ),
		'row' === $context ? zad_icon( 'plus', '', 20 ) : '',
		esc_attr( 'كمية ' . $name . ' بالكرتونة' ),
		zad_icon( 'minus', '', 18 ),
		zad_icon( 'plus', '', 18 ),
		esc_attr( $context ),
		$label
	);
}

/**
 * طباعة شبكة منتجات مخصصة (للرئيسية).
 *
 * @param array  $args  معايير wc_get_products.
 * @param string $class كلاس إضافي.
 */
function zad_product_grid( $args, $class = '' ) {
	$args     = wp_parse_args(
		$args,
		array(
			'status'     => 'publish',
			'limit'      => 8,
			'visibility' => 'catalog',
			'return'     => 'ids',
		)
	);
	$ids = wc_get_products( $args );
	if ( ! $ids ) {
		return;
	}
	global $product, $post;
	$prev_product = $product;
	$prev_post    = $post;
	echo '<ul class="products zd-grid ' . esc_attr( $class ) . '">';
	foreach ( $ids as $pid ) {
		$post = get_post( $pid ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $post );
		$product = wc_get_product( $pid ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		wc_get_template_part( 'content', 'product' );
	}
	echo '</ul>';
	$product = $prev_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	$post    = $prev_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	wp_reset_postdata();
}

/* -------------------------------------------------------------------------
 * صفحة المنتج المفرد
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/**
 * شارة العلامة التجارية فوق العنوان.
 */
function zad_single_brand() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info   = zad_product_info( $product->get_id() );
	$brands = zad_brands();
	echo '<div class="zd-single__top">';
	if ( isset( $brands[ $info['brand'] ] ) ) {
		$b    = $brands[ $info['brand'] ];
		$link = taxonomy_exists( 'product_brand' ) ? get_term_link( $info['brand'], 'product_brand' ) : '';
		$logo = zad_brand_logo( $b, 20 );
		printf(
			'<a class="zd-chip zd-chip--brand" href="%1$s">%4$s%2$s%5$s</a>',
			esc_url( is_wp_error( $link ) ? '' : $link ),
			esc_html( $b['ar'] ),
			esc_html( $b['latin'] ),
			$logo, // phpcs:ignore WordPress.Security.EscapeOutput
			$logo ? '' : ' <span lang="tr">' . esc_html( $b['latin'] ) . '</span>'
		);
	}
	$cats = zad_categories();
	if ( isset( $cats[ $info['cat'] ] ) ) {
		printf( '<a class="zd-chip" href="%s">%s</a>', esc_url( zad_cat_url( $info['cat'] ) ), esc_html( $cats[ $info['cat'] ]['title'] ) );
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'zad_single_brand', 3 );

/**
 * الاسم التركي الأصلي تحت العنوان (يساعد البحث والسيو).
 */
function zad_single_tr_name() {
	global $product;
	$tr = $product ? get_post_meta( $product->get_id(), '_zad_tr', true ) : '';
	if ( $tr ) {
		printf( '<p class="zd-single__tr" lang="tr">%s</p>', esc_html( $tr ) );
	}
}
add_action( 'woocommerce_single_product_summary', 'zad_single_tr_name', 6 );

/**
 * بيانات الجملة بعد السعر.
 */
function zad_single_pack() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info = zad_product_info( $product->get_id() );
	echo '<div class="zd-single__pack">';
	echo wp_kses_post( zad_unit_price_html( $product ) );
	echo '<dl class="zd-specs">';
	if ( $info['pack'] ) {
		printf( '<div><dt>تعبئة الكرتونة</dt><dd><bdi>%s</bdi></dd></div>', esc_html( $info['pack'] ) );
	}
	if ( $info['units'] ) {
		printf( '<div><dt>عدد القطع</dt><dd>%d قطعة</dd></div>', (int) $info['units'] );
	}
	echo '<div><dt>وحدة البيع</dt><dd>كرتونة كاملة</dd></div>';
	echo '</dl></div>';
}
add_action( 'woocommerce_single_product_summary', 'zad_single_pack', 15 );

/**
 * صندوق الشراء في صفحة المنتج: عدّاد كراتين متزامن فوراً بدل نموذج ووكومرس.
 */
function zad_single_buy() {
	global $product;
	if ( ! $product ) {
		return;
	}
	if ( ! $product->is_type( 'simple' ) ) {
		woocommerce_template_single_add_to_cart();
		return;
	}
	$map = zad_cart_qty_cached();
	$qty = isset( $map[ $product->get_id() ] ) ? (int) $map[ $product->get_id() ] : 0;
	echo '<div class="zd-buy"><span class="zd-buy__label">عدد الكراتين</span>';
	echo zad_cart_control( $product, $qty, 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput
	printf( '<a class="zd-buy__checkout" href="%s" data-zd-checkout>إتمام الطلب</a>', esc_url( wc_get_checkout_url() ) );
	printf(
		'<button type="button" class="zd-buy__wish" data-zd-wish="%1$d" data-name="%2$s" data-url="%3$s" aria-pressed="false" aria-label="%4$s">%5$s</button>',
		(int) $product->get_id(),
		esc_attr( $product->get_name() ),
		esc_url( get_permalink( $product->get_id() ) ),
		esc_attr( 'احفظ ' . $product->get_name() ),
		zad_icon( 'heart', '', 22 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
	echo '</div>';
	if ( zad_min_cartons() > 0 ) {
		printf( '<p class="zd-buy__min">%1$s<span>الحد الأدنى للطلبية <b>%2$d كرتونة</b> من أي أصناف تختارها.</span></p>', zad_icon( 'box', '', 18 ), zad_min_cartons() ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	// شريط ثابت على الجوال يظهر حين يخرج زر الإضافة الأساسي من الشاشة (inert حتى يظهر).
	printf(
		'<div class="zd-stickybuy" data-zd-stickybuy inert><div class="zd-stickybuy__info"><span class="zd-stickybuy__name">%1$s</span><span class="zd-stickybuy__price">%2$s</span></div>%3$s</div>',
		esc_html( $product->get_name() ),
		wp_kses_post( $product->get_price_html() ),
		zad_cart_control( $product, $qty, 'sticky' ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
// زر الإضافة مباشرة بعد السعر والتعبئة (كان بعد صندوق الربح والوصف، على بعد شاشة ونصف في الجوال).
add_action( 'woocommerce_single_product_summary', 'zad_single_buy', 17 );

/**
 * أزرار إضافية: واتساب + الطلب السريع.
 */
function zad_single_extras() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="zd-single__extras">';
	if ( zad_wa_number() ) {
		$text = sprintf( "مرحباً، أرغب بالاستفسار عن سعر الكميات للصنف: %s\n%s", $product->get_name(), get_permalink( $product->get_id() ) );
		printf( '<a class="zd-btn zd-btn--wa" href="%s" target="_blank" rel="noopener">سعر الكميات عبر واتساب</a>', esc_url( zad_wa_link( $text ) ) );
	}
	printf( '<a class="zd-btn zd-btn--ghost" href="%s">قائمة الأسعار الكاملة</a>', esc_url( zad_page_url( 'quick_order' ) ) );
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'zad_single_extras', 35 );

/**
 * شروط الجملة المختصرة تحت أزرار الشراء (نص فقط، دون أيقونات).
 */
function zad_trust_badges() {
	$items = array(
		array( 'التوريد', 'داخل ' . zad_opt( 'city' ) . ' خلال 24–48 ساعة، ولكل الولايات حسب الجدول' ),
		array( 'الدفع', 'نقداً عند الاستلام في إسطنبول، وتحويل بنكي خارجها' ),
		array( 'الجودة', 'منتجات أصلية بدفعات إنتاج حديثة' ),
		array( 'الحد الأدنى', zad_min_cartons() > 0 ? sprintf( '%d كرتونة للطلبية من أي أصناف', zad_min_cartons() ) : 'كرتونة واحدة من الصنف' ),
	);
	echo '<dl class="zd-trust">';
	foreach ( $items as $it ) {
		printf( '<div><dt>%1$s</dt><dd>%2$s</dd></div>', esc_html( $it[0] ), esc_html( $it[1] ) );
	}
	echo '</dl>';
}
add_action( 'woocommerce_single_product_summary', 'zad_trust_badges', 45 );

/**
 * التبويبات.
 *
 * @param array $tabs التبويبات.
 * @return array
 */
function zad_product_tabs( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = 'وصف المنتج';
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = 'معلومات إضافية';
	}
	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = 'التقييمات';
	}
	$tabs['zad_wholesale'] = array(
		'title'    => 'شروط الجملة والتوريد',
		'priority' => 15,
		'callback' => 'zad_wholesale_tab',
	);
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'zad_product_tabs', 20 );

/**
 * محتوى تبويب الجملة.
 */
function zad_wholesale_tab() {
	global $product;
	$info = zad_product_info( $product->get_id() );
	$min  = (float) zad_opt( 'min_order' );
	echo '<div class="zd-prose"><ul class="zd-checklist">';
	if ( $info['pack'] ) {
		printf( '<li>التعبئة: <strong>%s</strong></li>', esc_html( $info['pack'] ) );
	}
	echo '<li>وحدة البيع: <strong>كرتونة كاملة</strong>، ويمكنك طلب كرتونة واحدة من الصنف.</li>';
	if ( zad_min_cartons() > 0 ) {
		printf( '<li>الحد الأدنى للطلبية: <strong>%d كرتونة</strong> مشكّلة من أي أصناف.</li>', (int) zad_min_cartons() );
	}
	echo '<li>أسعار الكميات: للطلب بالطبلية أو بكميات دورية ثابتة، تواصل مع قسم المبيعات للحصول على سعر خاص.</li>';
	if ( $min > 0 ) {
		printf( '<li>الحد الأدنى لقيمة الطلبية: <strong>%s</strong></li>', wp_kses_post( wc_price( $min ) ) );
	}
	printf( '<li>التوريد: داخل %s خلال 24 إلى 48 ساعة من التأكيد، وإلى باقي الولايات وفق جدول التوزيع.</li>', esc_html( zad_opt( 'city' ) ) );
	echo '<li>الدفع: نقداً عند الاستلام داخل إسطنبول فقط، وبتحويل بنكي للطلبات خارج إسطنبول وخارج تركيا. فاتورة نظامية مع كل طلبية.</li>';
	printf( '<li>صنف غير موجود في القائمة؟ <a href="%s">أرسل طلب توريد خاص</a> ونؤمّنه من المصدر.</li>', esc_url( zad_page_url( 'special_request' ) ) );
	printf( '<li>للتصدير خارج تركيا (حاويات وطبليات مختلطة): <a href="%s">اطلب عرض سعر للتصدير</a>.</li>', esc_url( zad_page_url( 'export' ) ) );
	echo '</ul></div>';
}

add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

/**
 * المنتجات ذات الصلة.
 *
 * @param array $args الإعدادات.
 * @return array
 */
function zad_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'zad_related_args' );
add_filter(
	'woocommerce_product_related_products_heading',
	static function () {
		return 'أصناف تكمل طلبيتك';
	}
);
add_filter(
	'woocommerce_product_single_add_to_cart_text',
	static function () {
		return 'أضف إلى الطلبية';
	}
);

/* -------------------------------------------------------------------------
 * السلة
 * ---------------------------------------------------------------------- */

/**
 * نص الطلب لواتساب من السلة الحالية.
 *
 * @return string
 */
function zad_cart_whatsapp_text() {
	$lines = array( 'مرحباً، أرغب بطلب الأصناف التالية بالجملة:' );
	$i     = 1;
	foreach ( WC()->cart->get_cart() as $item ) {
		$p = $item['data'];
		if ( ! $p ) {
			continue;
		}
		$lines[] = sprintf( '%d) %s — %d كرتونة', $i++, $p->get_name(), (int) $item['quantity'] );
	}
	if ( zad_show_prices() ) {
		$lines[] = '';
		$lines[] = 'الإجمالي التقديري: ' . zad_money_plain( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
	}
	$lines[] = '';
	$lines[] = 'التسليم: عند باب المحل / ترتيبها على الرف';
	$who     = is_user_logged_in() && function_exists( 'zad_customer_profile' ) ? zad_customer_profile( get_current_user_id() ) : null;
	$lines[] = 'اسم المحل: ' . ( $who ? $who['shop'] : '' );
	$lines[] = 'العنوان: ' . ( $who ? trim( $who['address'] . ' ' . $who['city'] ) : '' );
	return implode( "\n", $lines );
}

/**
 * زر إرسال السلة عبر واتساب.
 */
function zad_cart_whatsapp_button() {
	if ( ! zad_wa_number() || WC()->cart->is_empty() ) {
		return;
	}
	printf(
		'<a class="zd-btn zd-btn--wa zd-btn--block" href="%s" target="_blank" rel="noopener">أرسل الطلبية عبر واتساب</a>',
		esc_url( zad_wa_link( zad_cart_whatsapp_text() ) )
	);
}
add_action( 'woocommerce_proceed_to_checkout', 'zad_cart_whatsapp_button', 30 );

/**
 * رابط متابعة الطلب أسفل السلة.
 */
function zad_cart_continue() {
	printf( '<a class="zd-link" href="%s">أضف أصنافاً أخرى من قائمة الأسعار</a>', esc_url( zad_page_url( 'quick_order' ) ) );
}
add_action( 'woocommerce_proceed_to_checkout', 'zad_cart_continue', 40 );

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

// السلة الفارغة: توجيه مباشر لقائمة الطلب السريع.
add_filter(
	'woocommerce_return_to_shop_redirect',
	static function () {
		return zad_page_url( 'quick_order' );
	}
);
add_action(
	'woocommerce_cart_is_empty',
	static function () {
		echo '<p class="zd-cart-empty__text">حدّد عدد الكراتين لكل صنف من قائمة الأسعار، أو أرسل قائمة أصنافك إلى قسم المبيعات ونجهّزها لك.</p>';
	},
	20
);
add_filter(
	'woocommerce_return_to_shop_text',
	static function () {
		return 'افتح قائمة الأسعار';
	}
);

/* -------------------------------------------------------------------------
 * الحد الأدنى للطلبية (بالكراتين، واختيارياً بالمبلغ)
 * ---------------------------------------------------------------------- */

/**
 * صندوق «الحد الأدنى للطلبية» مع شريط التقدّم (السلة، درج الطلبية، الدفع).
 *
 * @param int    $count   عدد الكراتين في الطلبية.
 * @param string $context cart | drawer | checkout.
 * @return string
 */
function zad_min_box_html( $count, $context = 'cart' ) {
	$min = zad_min_cartons();
	if ( $min <= 0 ) {
		return '';
	}
	$left = zad_min_cartons_left( $count );
	$pct  = (int) min( 100, round( $count / $min * 100 ) );
	$text = $left > 0
		? sprintf( 'الحد الأدنى للطلبية <b>%1$d كرتونة</b> من أي أصناف. في طلبيتك <b>%2$d</b>، أضف <b>%3$d</b> كرتونة لإتمام الطلب.', $min, $count, $left )
		: sprintf( 'طلبيتك %1$d كرتونة، وبلغت الحد الأدنى (%2$d كرتونة) ✓', $count, $min );
	return sprintf(
		'<div class="zd-minbox zd-minbox--%1$s%2$s" role="status"><p class="zd-minbox__text">%3$s</p><span class="zd-minbox__track" aria-hidden="true"><span class="zd-minbox__fill" style="width:%4$d%%"></span></span></div>',
		esc_attr( $context ),
		$left > 0 ? '' : ' is-ok',
		wp_kses( $text, array( 'b' => array() ) ),
		$pct
	);
}

/**
 * التحقق عند الدفع وعند إرسال الطلب: لا طلبية تحت الحد الأدنى.
 * في صفحة السلة لا تظهر رسالة خطأ، بل صندوق التقدّم وزر إتمام معطّل (أدناه).
 */
function zad_min_order_check() {
	if ( ! WC()->cart || WC()->cart->is_empty() || is_cart() ) {
		return;
	}
	$left = zad_min_cartons_left();
	if ( $left > 0 ) {
		wc_add_notice(
			sprintf(
				'الحد الأدنى للطلبية %1$d كرتونة من أي أصناف، وفي طلبيتك %2$d. أضف %3$d كرتونة لإتمام الطلب.',
				zad_min_cartons(),
				zad_cart_cartons(),
				$left
			),
			'error'
		);
	}
	$min = (float) zad_opt( 'min_order' );
	if ( $min <= 0 ) {
		return;
	}
	$total = (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax();
	if ( $total < $min ) {
		wc_add_notice(
			sprintf(
				'الحد الأدنى لقيمة الطلب %1$s. أضف أصنافاً بقيمة %2$s لإتمام الطلب.',
				wc_price( $min ),
				wc_price( $min - $total )
			),
			'error'
		);
	}
}
add_action( 'woocommerce_check_cart_items', 'zad_min_order_check' );

/**
 * من يفتح صفحة الدفع وطلبيته تحت الحد الأدنى يعود إلى السلة، وفيها ما ينقصه بالضبط
 * (قبل تحويل الزائر إلى التسجيل، فلا يسجّل ثم يكتشف أن طلبيته غير مكتملة).
 */
function zad_min_checkout_redirect() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) {
		return;
	}
	if ( ! WC()->cart || WC()->cart->is_empty() || zad_min_cartons_left() <= 0 ) {
		return;
	}
	wc_add_notice(
		sprintf( 'الحد الأدنى للطلبية %1$d كرتونة من أي أصناف. أضف %2$d كرتونة ثم أتمّ الطلب.', zad_min_cartons(), zad_min_cartons_left() ),
		'notice'
	);
	wp_safe_redirect( wc_get_cart_url() );
	exit;
}
add_action( 'template_redirect', 'zad_min_checkout_redirect', 4 );

/**
 * صفحة السلة: صندوق الحد الأدنى فوق زر الإتمام، وتحت الحد يُستبدل الزر بزر معطّل
 * وتختفي «أرسل الطلبية عبر واتساب».
 */
function zad_min_cart_actions() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	echo zad_min_box_html( zad_cart_cartons(), 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput
	$left = zad_min_cartons_left();
	if ( $left > 0 ) {
		remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		remove_action( 'woocommerce_proceed_to_checkout', 'zad_cart_whatsapp_button', 30 );
		printf( '<span class="checkout-button button alt wc-forward zd-btn-disabled" aria-disabled="true">أضف %d كرتونة لإتمام الطلب</span>', (int) $left );
	}
}
add_action( 'woocommerce_proceed_to_checkout', 'zad_min_cart_actions', 5 );

/**
 * درج الطلبية: الصندوق المختصر، وتحت الحد يُعطَّل زر «إتمام الطلب».
 */
function zad_min_drawer_box() {
	if ( ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	echo zad_min_box_html( zad_cart_cartons(), 'drawer' ); // phpcs:ignore WordPress.Security.EscapeOutput
	if ( zad_min_cartons_left() > 0 ) {
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
		add_action( 'woocommerce_widget_shopping_cart_buttons', 'zad_min_drawer_disabled_button', 20 );
	} else {
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'zad_min_drawer_disabled_button', 20 );
		if ( ! has_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout' ) ) {
			add_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
		}
	}
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'zad_min_drawer_box', 5 );

/**
 * زر «إتمام الطلب» المعطّل في الدرج.
 */
function zad_min_drawer_disabled_button() {
	printf( '<span class="button checkout wc-forward zd-btn-disabled" aria-disabled="true">أضف %d كرتونة</span>', (int) zad_min_cartons_left() );
}

/**
 * صفحة الدفع: سطر الحد الأدنى فوق زر «تأكيد الطلبية».
 */
function zad_min_checkout_note() {
	echo zad_min_box_html( zad_cart_cartons(), 'checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_review_order_before_submit', 'zad_min_checkout_note', 5 );

/**
 * أجزاء السلة المحدّثة عبر AJAX.
 *
 * @param array $fragments الأجزاء.
 * @return array
 */
function zad_cart_fragments( $fragments ) {
	$count = WC()->cart->get_cart_contents_count();
	$fragments['span.zd-cart-count'] = sprintf( '<span class="zd-cart-count" data-count="%1$d">%1$d</span>', $count );
	$fragments['span.zd-cart-total'] = '<span class="zd-cart-total">' . WC()->cart->get_cart_subtotal() . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'zad_cart_fragments' );

/* -------------------------------------------------------------------------
 * صفحة الدفع: حقول مختصرة تناسب أصحاب البقاليات
 * ---------------------------------------------------------------------- */

/**
 * تسمية حقل الولاية لتركيا: ووكومرس يسميها «Province» ويبدّلها سكربت الصفحة حسب الدولة.
 *
 * @param array $locale إعدادات الدول.
 * @return array
 */
function zad_country_locale_labels( $locale ) {
	$locale['TR']['state']['label']    = 'الولاية';
	$locale['TR']['state']['required'] = true;
	return $locale;
}
add_filter( 'woocommerce_get_country_locale', 'zad_country_locale_labels', 20 );

/**
 * حقول العنوان الافتراضية.
 *
 * @param array $fields الحقول.
 * @return array
 */
function zad_default_address_fields( $fields ) {
	unset( $fields['last_name'], $fields['address_2'], $fields['postcode'] );
	// الولاية تحدد طريقة الدفع: إسطنبول = نقداً عند الاستلام، وغيرها = تحويل بنكي.
	if ( isset( $fields['state'] ) ) {
		$fields['state']['label']    = 'الولاية';
		$fields['state']['required'] = true;
		$fields['state']['priority'] = 45;
		$fields['state']['class']    = array( 'form-row-wide', 'address-field', 'update_totals_on_change' );
	}
	if ( isset( $fields['first_name'] ) ) {
		$fields['first_name']['label']       = 'اسم المسؤول';
		$fields['first_name']['placeholder'] = 'صاحب المتجر أو مسؤول المشتريات';
		$fields['first_name']['class']       = array( 'form-row-wide' );
		$fields['first_name']['priority']    = 10;
	}
	if ( isset( $fields['company'] ) ) {
		$fields['company']['label']       = 'اسم المتجر / الشركة';
		$fields['company']['placeholder'] = 'مثال: ماركت النور';
		$fields['company']['required']    = true;
		$fields['company']['priority']    = 20;
		$fields['company']['class']       = array( 'form-row-wide' );
	}
	if ( isset( $fields['phone'] ) ) {
		// يستخدم سكربت ووكومرس هذه القيم لترتيب الحقول وتسميتها في المتصفح.
		$fields['phone']['priority']    = 30;
		$fields['phone']['label']       = 'رقم الجوال (واتساب)';
		$fields['phone']['placeholder'] = '05xx xxx xx xx';
		$fields['phone']['required']    = true;
	}
	if ( isset( $fields['country'] ) ) {
		$fields['country']['priority'] = 40;
	}
	if ( isset( $fields['city'] ) ) {
		$fields['city']['label']       = 'المنطقة / الحي';
		$fields['city']['placeholder'] = 'مثال: الفاتح';
		$fields['city']['priority']    = 50;
	}
	if ( isset( $fields['address_1'] ) ) {
		$fields['address_1']['label']       = 'العنوان بالتفصيل';
		$fields['address_1']['placeholder'] = 'الحي، الشارع، رقم المتجر أو المستودع';
		$fields['address_1']['priority']    = 60;
	}
	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'zad_default_address_fields', 20 );

/**
 * حقول الفوترة.
 *
 * @param array $fields الحقول.
 * @return array
 */
function zad_billing_fields( $fields ) {
	if ( isset( $fields['billing_phone'] ) ) {
		$fields['billing_phone']['label']       = 'رقم الجوال (واتساب)';
		$fields['billing_phone']['placeholder'] = '05xx xxx xx xx';
		$fields['billing_phone']['required']    = true;
		$fields['billing_phone']['priority']    = 30;
		$fields['billing_phone']['class']       = array( 'form-row-wide' );
	}
	if ( isset( $fields['billing_email'] ) ) {
		$fields['billing_email']['label']    = 'البريد الإلكتروني';
		$fields['billing_email']['required'] = false;
		$fields['billing_email']['priority'] = 90;
	}
	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'zad_billing_fields', 20 );

/**
 * ملاحظات الطلب.
 *
 * @param array $fields الحقول.
 * @return array
 */
function zad_checkout_fields( $fields ) {
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'ملاحظات الطلبية';
		$fields['order']['order_comments']['placeholder'] = 'مثال: التسليم صباحاً، رقم ضريبي للفاتورة، أو بديل مقبول لصنف غير متوفر';
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'zad_checkout_fields', 20 );

add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
// صفحة دفع أبسط: لا خانة كوبونات (أسعار الجملة واضحة مسبقاً).
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
add_filter(
	'woocommerce_order_button_text',
	static function () {
		return 'تأكيد الطلبية';
	}
);

/**
 * نص صفحة الشكر.
 *
 * @return string
 */
function zad_thankyou_text() {
	return 'شكراً لك! استلمنا طلبيتك، وسيتواصل معك قسم المبيعات لتأكيد الأصناف وموعد التسليم.';
}
add_filter( 'woocommerce_thankyou_order_received_text', 'zad_thankyou_text' );

/**
 * بطاقة تأكيد الطلب عبر واتساب في صفحة الشكر.
 *
 * @param int $order_id رقم الطلب.
 */
function zad_thankyou_whatsapp( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! zad_wa_number() ) {
		return;
	}
	$lines = array( sprintf( 'مرحباً، أرسلت طلبية من الموقع برقم #%s', $order->get_order_number() ) );
	foreach ( $order->get_items() as $item ) {
		$lines[] = sprintf( '• %s — %d كرتونة', $item->get_name(), (int) $item->get_quantity() );
	}
	$lines[] = 'الإجمالي: ' . zad_money_plain( $order->get_total() );
	$drop    = function_exists( 'zad_drop_label' ) ? zad_drop_label( $order ) : '';
	if ( $drop ) {
		$lines[] = 'التسليم: ' . $drop;
	}
	$cod = zad_cod_method_label( $order );
	if ( $cod ) {
		$lines[] = 'الدفع: ' . ( 'bacs' === $order->get_payment_method() ? 'تحويل بنكي' : 'نقداً عند الاستلام' );
	}
	$lines[] = 'الاسم: ' . trim( $order->get_billing_first_name() . ' ' . $order->get_billing_company() );
	$lines[] = 'الجوال: ' . $order->get_billing_phone();
	printf(
		'<div class="zd-thanks-wa"><div><strong>سرّع تجهيز الطلبية عبر واتساب</strong><p>أرسل ملخص الطلبية لقسم المبيعات ليؤكدها ويحدد موعد التسليم.</p></div><a class="zd-btn zd-btn--wa" href="%s" target="_blank" rel="noopener">تأكيد عبر واتساب</a></div>',
		esc_url( zad_wa_link( implode( "\n", $lines ) ) )
	);
}
add_action( 'woocommerce_thankyou', 'zad_thankyou_whatsapp', 5 );

/* -------------------------------------------------------------------------
 * قسم العروض: مزامنة تلقائية للمنتجات المخفّضة والباقات
 * ---------------------------------------------------------------------- */

/**
 * إضافة/إزالة المنتج من قسم العروض حسب حالة التخفيض.
 *
 * @param int $product_id رقم المنتج.
 */
function zad_sync_offers_category( $product_id ) {
	static $running = false;
	if ( $running ) {
		return;
	}
	$term = get_term_by( 'slug', 'offers', 'product_cat' );
	if ( ! $term ) {
		return;
	}
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return;
	}
	$ids      = array_map( 'intval', $product->get_category_ids( 'edit' ) );
	$in_offer = in_array( (int) $term->term_id, $ids, true );
	$should   = $product->is_on_sale( 'edit' ) || get_post_meta( $product_id, '_zad_bundle', true );

	if ( $should && ! $in_offer ) {
		$ids[] = (int) $term->term_id;
	} elseif ( ! $should && $in_offer && count( $ids ) > 1 ) {
		$ids = array_values( array_diff( $ids, array( (int) $term->term_id ) ) );
	} else {
		return;
	}
	$running = true;
	wp_set_object_terms( $product_id, $ids, 'product_cat' );
	$running = false;
}
add_action( 'woocommerce_update_product', 'zad_sync_offers_category', 20 );
add_action( 'woocommerce_new_product', 'zad_sync_offers_category', 20 );

/**
 * ربط المنتج بتصنيف العلامة التجارية تلقائياً من الحقل _zad_brand
 * (مفيد عند استيراد المنتجات من ملف CSV).
 *
 * @param int $product_id رقم المنتج.
 */
function zad_sync_brand_term( $product_id ) {
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$slug = get_post_meta( $product_id, '_zad_brand', true );
	if ( ! $slug ) {
		return;
	}
	$current = wp_get_object_terms( $product_id, 'product_brand', array( 'fields' => 'slugs' ) );
	if ( ! is_wp_error( $current ) && in_array( $slug, $current, true ) ) {
		return;
	}
	$term = get_term_by( 'slug', $slug, 'product_brand' );
	if ( ! $term && isset( zad_brands()[ $slug ] ) ) {
		$res  = wp_insert_term( zad_brands()[ $slug ]['ar'], 'product_brand', array( 'slug' => $slug ) );
		$term = is_wp_error( $res ) ? null : get_term( $res['term_id'], 'product_brand' );
	}
	if ( $term ) {
		wp_set_object_terms( $product_id, array( (int) $term->term_id ), 'product_brand', true );
	}
}
add_action( 'woocommerce_update_product', 'zad_sync_brand_term', 25 );
add_action( 'woocommerce_new_product', 'zad_sync_brand_term', 25 );
