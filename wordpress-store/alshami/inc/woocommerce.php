<?php
/**
 * تكامل ووكومرس: التخطيط، بطاقة المنتج، صفحة المنتج، السلة والدفع.
 *
 * @package AlShami
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
function shami_wc_wrapper_open() {
	echo '<main id="main" class="sh-main sh-shop"><div class="sh-container">';
}
add_action( 'woocommerce_before_main_content', 'shami_wc_wrapper_open', 5 );

/**
 * إغلاق الغلاف.
 */
function shami_wc_wrapper_close() {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'shami_wc_wrapper_close', 50 );

/**
 * مسار التنقل (Breadcrumb).
 *
 * @param array $args الإعدادات.
 * @return array
 */
function shami_breadcrumb_defaults( $args ) {
	$args['delimiter']   = '<span class="sh-bc__sep" aria-hidden="true">‹</span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb sh-bc" aria-label="مسار التنقل">';
	$args['wrap_after']  = '</nav>';
	$args['home']        = 'الرئيسية';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'shami_breadcrumb_defaults' );

/* -------------------------------------------------------------------------
 * صفحات الأرشيف (المتجر، الأقسام، العلامات، البحث)
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );

/**
 * رأس الأرشيف المخصص.
 */
function shami_archive_hero() {
	get_template_part( 'template-parts/shop/archive-hero' );
}
add_action( 'woocommerce_shop_loop_header', 'shami_archive_hero' );

/**
 * نص السيو أسفل القسم.
 */
function shami_archive_seo_text() {
	if ( ! is_product_taxonomy() ) {
		return;
	}
	$term = get_queried_object();
	if ( ! $term || empty( $term->term_id ) ) {
		return;
	}
	$text = get_term_meta( $term->term_id, 'shami_seo_text', true );
	if ( ! $text ) {
		return;
	}
	echo '<section class="sh-seo-text sh-prose" aria-label="معلومات عن القسم">' . wp_kses_post( wpautop( $text ) ) . '</section>';
}
add_action( 'woocommerce_after_shop_loop', 'shami_archive_seo_text', 30 );
add_action( 'woocommerce_no_products_found', 'shami_archive_seo_text', 30 );

/**
 * رسالة عند عدم وجود نتائج: توجيه لطلب منتج غير متوفر.
 */
function shami_no_products_cta() {
	printf(
		'<div class="sh-empty"><h2>لم نجد هذا الصنف في القائمة</h2><p>جرّب كلمة أقصر أو اسم العلامة بالتركية، أو أرسل لنا اسم الصنف ونؤمّنه لك من المصدر.</p><div class="sh-empty__actions"><a class="sh-btn sh-btn--primary" href="%1$s">أرسل طلب توريد خاص</a><a class="sh-btn sh-btn--ghost" href="%2$s">تصفّح كل الأصناف</a></div></div>',
		esc_url( shami_page_url( 'special_request' ) ),
		esc_url( wc_get_page_permalink( 'shop' ) )
	);
}
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
add_action( 'woocommerce_no_products_found', 'shami_no_products_cta', 10 );

/**
 * شريط الأدوات (عدد النتائج + الترتيب).
 */
function shami_toolbar_open() {
	echo '<div class="sh-toolbar">';
}
/**
 * إغلاق شريط الأدوات.
 */
function shami_toolbar_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'shami_toolbar_open', 19 );
add_action( 'woocommerce_before_shop_loop', 'shami_toolbar_close', 31 );

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
function shami_price_html( $html, $product ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $html;
	}
	if ( ! shami_show_prices() ) {
		return '<span class="sh-price-request">السعر عند الطلب</span>';
	}
	if ( '' === $html || ! $product ) {
		return $html;
	}
	return $html . ' <small class="sh-per">/ كرتونة</small>';
}
add_filter( 'woocommerce_get_price_html', 'shami_price_html', 20, 2 );

/**
 * سعر القطعة الواحدة داخل الكرتونة.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function shami_unit_price_html( $product ) {
	if ( ! shami_show_prices() ) {
		return '';
	}
	$units = (int) get_post_meta( $product->get_id(), '_shami_units', true );
	$price = (float) wc_get_price_to_display( $product );
	if ( $units < 2 || $price <= 0 ) {
		return '';
	}
	return sprintf( '<span class="sh-unit-price">≈ %s للقطعة</span>', wc_price( $price / $units ) );
}

/* -------------------------------------------------------------------------
 * أزرار + و − حول حقل الكمية
 * ---------------------------------------------------------------------- */

/**
 * زر الإنقاص.
 */
function shami_qty_minus() {
	echo '<button type="button" class="sh-qty-btn sh-qty-minus" aria-label="إنقاص الكمية">' . shami_icon( 'minus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
/**
 * زر الزيادة.
 */
function shami_qty_plus() {
	echo '<button type="button" class="sh-qty-btn sh-qty-plus" aria-label="زيادة الكمية">' . shami_icon( 'plus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_before_quantity_input_field', 'shami_qty_minus' );
add_action( 'woocommerce_after_quantity_input_field', 'shami_qty_plus' );

/* -------------------------------------------------------------------------
 * بطاقة المنتج (تُستخدم في woocommerce/content-product.php)
 * ---------------------------------------------------------------------- */

/**
 * نسبة الخصم المئوية لمنتج (0 إن لم يكن عليه عرض).
 *
 * @param WC_Product $product المنتج.
 * @return int
 */
function shami_discount_percent( $product ) {
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
function shami_card_badges( $product ) {
	$out = '';
	if ( ! $product->is_in_stock() ) {
		$out .= '<span class="sh-badge sh-badge--muted">نفدت الكمية</span>';
	} else {
		if ( $product->is_on_sale() && shami_show_prices() ) {
			$pct  = shami_discount_percent( $product );
			$out .= $pct ? sprintf( '<span class="sh-badge sh-badge--sale"><bdi dir="ltr">−%d%%</bdi></span>', $pct ) : '<span class="sh-badge sh-badge--sale">عرض</span>';
		}
		if ( get_post_meta( $product->get_id(), '_shami_bundle', true ) ) {
			$out .= '<span class="sh-badge sh-badge--bundle">باقة</span>';
		} elseif ( $product->is_featured() ) {
			$out .= '<span class="sh-badge sh-badge--hot">الأعلى طلباً</span>';
		}
	}
	return $out ? '<span class="sh-card__badges">' . $out . '</span>' : '';
}

/**
 * متحكم السلة: زر «أضف» ثم عدّاد + / − متزامن فوراً مع السلة.
 *
 * @param WC_Product $product المنتج.
 * @param int        $qty     الكمية الحالية بالسلة.
 * @param string     $context card|row|lg.
 * @return string
 */
function shami_cart_control( $product, $qty = 0, $context = 'card' ) {
	$name = $product->get_name();
	if ( ! $product->is_type( 'simple' ) ) {
		return sprintf( '<a class="sh-btn sh-btn--ghost sh-btn--sm" href="%s">اختر الخيارات</a>', esc_url( $product->get_permalink() ) );
	}
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return '<span class="sh-cart-ctl sh-cart-ctl--' . esc_attr( $context ) . ' is-disabled"><span class="sh-cart-ctl__na">غير متوفر حالياً</span></span>';
	}
	$price = (float) wc_get_price_to_display( $product );
	$label = 'row' === $context ? '<span class="screen-reader-text">أضف</span>' : '<span>أضف إلى الطلبية</span>';
	return sprintf(
		'<div class="sh-cart-ctl sh-cart-ctl--%11$s%1$s" data-id="%2$d" data-qty="%3$d" data-price="%4$s" data-name="%5$s">'
		. '<button type="button" class="sh-cart-ctl__add" aria-label="%6$s">%7$s%12$s</button>'
		. '<div class="sh-cart-ctl__stepper" role="group" aria-label="%8$s">'
		. '<button type="button" class="sh-step sh-step--minus" data-step="-1" aria-label="إنقاص كرتونة">%9$s</button>'
		. '<input type="number" class="sh-step__input" inputmode="numeric" min="0" max="9999" step="1" value="%3$d" aria-label="عدد الكراتين">'
		. '<button type="button" class="sh-step sh-step--plus" data-step="1" aria-label="زيادة كرتونة">%10$s</button>'
		. '</div></div>',
		$qty > 0 ? ' is-active' : '',
		$product->get_id(),
		(int) $qty,
		esc_attr( $price ),
		esc_attr( $name ),
		esc_attr( 'أضف ' . $name . ' إلى الطلبية' ),
		'row' === $context ? shami_icon( 'plus', '', 20 ) : '',
		esc_attr( 'كمية ' . $name . ' بالكرتونة' ),
		shami_icon( 'minus', '', 18 ),
		shami_icon( 'plus', '', 18 ),
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
function shami_product_grid( $args, $class = '' ) {
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
	echo '<ul class="products sh-grid ' . esc_attr( $class ) . '">';
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
function shami_single_brand() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info   = shami_product_info( $product->get_id() );
	$brands = shami_brands();
	echo '<div class="sh-single__top">';
	if ( isset( $brands[ $info['brand'] ] ) ) {
		$b    = $brands[ $info['brand'] ];
		$link = taxonomy_exists( 'product_brand' ) ? get_term_link( $info['brand'], 'product_brand' ) : '';
		printf(
			'<a class="sh-chip sh-chip--brand" href="%1$s">%2$s <span lang="tr">%3$s</span></a>',
			esc_url( is_wp_error( $link ) ? '' : $link ),
			esc_html( $b['ar'] ),
			esc_html( $b['latin'] )
		);
	}
	$cats = shami_categories();
	if ( isset( $cats[ $info['cat'] ] ) ) {
		printf( '<a class="sh-chip" href="%s">%s</a>', esc_url( shami_cat_url( $info['cat'] ) ), esc_html( $cats[ $info['cat'] ]['title'] ) );
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'shami_single_brand', 3 );

/**
 * الاسم التركي الأصلي تحت العنوان (يساعد البحث والسيو).
 */
function shami_single_tr_name() {
	global $product;
	$tr = $product ? get_post_meta( $product->get_id(), '_shami_tr', true ) : '';
	if ( $tr ) {
		printf( '<p class="sh-single__tr" lang="tr">%s</p>', esc_html( $tr ) );
	}
}
add_action( 'woocommerce_single_product_summary', 'shami_single_tr_name', 6 );

/**
 * بيانات الجملة بعد السعر.
 */
function shami_single_pack() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info = shami_product_info( $product->get_id() );
	echo '<div class="sh-single__pack">';
	echo wp_kses_post( shami_unit_price_html( $product ) );
	echo '<dl class="sh-specs">';
	if ( $info['pack'] ) {
		printf( '<div><dt>تعبئة الكرتونة</dt><dd><bdi>%s</bdi></dd></div>', esc_html( $info['pack'] ) );
	}
	if ( $info['units'] ) {
		printf( '<div><dt>عدد القطع</dt><dd>%d قطعة</dd></div>', (int) $info['units'] );
	}
	echo '<div><dt>وحدة البيع</dt><dd>كرتونة كاملة</dd></div>';
	echo '</dl></div>';
}
add_action( 'woocommerce_single_product_summary', 'shami_single_pack', 15 );

/**
 * صندوق الشراء في صفحة المنتج: عدّاد كراتين متزامن فوراً بدل نموذج ووكومرس.
 */
function shami_single_buy() {
	global $product;
	if ( ! $product ) {
		return;
	}
	if ( ! $product->is_type( 'simple' ) ) {
		woocommerce_template_single_add_to_cart();
		return;
	}
	$map = shami_cart_qty_cached();
	$qty = isset( $map[ $product->get_id() ] ) ? (int) $map[ $product->get_id() ] : 0;
	echo '<div class="sh-buy"><span class="sh-buy__label">عدد الكراتين</span>';
	echo shami_cart_control( $product, $qty, 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput
	printf( '<a class="sh-buy__checkout" href="%s" data-sh-checkout>إتمام الطلب</a>', esc_url( wc_get_checkout_url() ) );
	echo '</div>';
}
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'shami_single_buy', 30 );

/**
 * أزرار إضافية: واتساب + الطلب السريع.
 */
function shami_single_extras() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="sh-single__extras">';
	if ( shami_wa_number() ) {
		$text = sprintf( "مرحباً، أرغب بالاستفسار عن سعر الكميات للصنف: %s\n%s", $product->get_name(), get_permalink( $product->get_id() ) );
		printf( '<a class="sh-btn sh-btn--wa" href="%s" target="_blank" rel="noopener">سعر الكميات عبر واتساب</a>', esc_url( shami_wa_link( $text ) ) );
	}
	printf( '<a class="sh-btn sh-btn--ghost" href="%s">قائمة الأسعار الكاملة</a>', esc_url( shami_page_url( 'quick_order' ) ) );
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'shami_single_extras', 35 );

/**
 * شروط الجملة المختصرة تحت أزرار الشراء (نص فقط، دون أيقونات).
 */
function shami_trust_badges() {
	$items = array(
		array( 'التوريد', 'داخل ' . shami_opt( 'city' ) . ' خلال 24–48 ساعة، ولكل الولايات حسب الجدول' ),
		array( 'الدفع', 'عند الاستلام أو بالتحويل البنكي، مع فاتورة نظامية' ),
		array( 'الجودة', 'منتجات أصلية بدفعات إنتاج حديثة' ),
		array( 'الحد الأدنى', 'كرتونة واحدة من الصنف' ),
	);
	echo '<dl class="sh-trust">';
	foreach ( $items as $it ) {
		printf( '<div><dt>%1$s</dt><dd>%2$s</dd></div>', esc_html( $it[0] ), esc_html( $it[1] ) );
	}
	echo '</dl>';
}
add_action( 'woocommerce_single_product_summary', 'shami_trust_badges', 45 );

/**
 * التبويبات.
 *
 * @param array $tabs التبويبات.
 * @return array
 */
function shami_product_tabs( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = 'وصف المنتج';
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = 'معلومات إضافية';
	}
	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = 'التقييمات';
	}
	$tabs['shami_wholesale'] = array(
		'title'    => 'شروط الجملة والتوريد',
		'priority' => 15,
		'callback' => 'shami_wholesale_tab',
	);
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'shami_product_tabs', 20 );

/**
 * محتوى تبويب الجملة.
 */
function shami_wholesale_tab() {
	global $product;
	$info = shami_product_info( $product->get_id() );
	$min  = (float) shami_opt( 'min_order' );
	echo '<div class="sh-prose"><ul class="sh-checklist">';
	if ( $info['pack'] ) {
		printf( '<li>التعبئة: <strong>%s</strong></li>', esc_html( $info['pack'] ) );
	}
	echo '<li>وحدة البيع: <strong>كرتونة كاملة</strong>، والحد الأدنى كرتونة واحدة من الصنف.</li>';
	echo '<li>أسعار الكميات: للطلب بالطبلية أو بكميات دورية ثابتة، تواصل مع قسم المبيعات للحصول على سعر خاص.</li>';
	if ( $min > 0 ) {
		printf( '<li>الحد الأدنى لقيمة الطلبية: <strong>%s</strong></li>', wp_kses_post( wc_price( $min ) ) );
	}
	printf( '<li>التوريد: داخل %s خلال 24 إلى 48 ساعة من التأكيد، وإلى باقي الولايات وفق جدول التوزيع.</li>', esc_html( shami_opt( 'city' ) ) );
	echo '<li>الدفع: عند الاستلام نقداً أو بالتحويل البنكي، مع فاتورة نظامية لكل طلبية.</li>';
	printf( '<li>صنف غير موجود في القائمة؟ <a href="%s">أرسل طلب توريد خاص</a> ونؤمّنه من المصدر.</li>', esc_url( shami_page_url( 'special_request' ) ) );
	printf( '<li>للتصدير خارج تركيا (حاويات وطبليات مختلطة): <a href="%s">اطلب عرض سعر للتصدير</a>.</li>', esc_url( shami_page_url( 'export' ) ) );
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
function shami_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'shami_related_args' );
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
function shami_cart_whatsapp_text() {
	$lines = array( 'مرحباً، أرغب بطلب الأصناف التالية بالجملة:' );
	$i     = 1;
	foreach ( WC()->cart->get_cart() as $item ) {
		$p = $item['data'];
		if ( ! $p ) {
			continue;
		}
		$lines[] = sprintf( '%d) %s — %d كرتونة', $i++, $p->get_name(), (int) $item['quantity'] );
	}
	if ( shami_show_prices() ) {
		$lines[] = '';
		$lines[] = 'الإجمالي التقديري: ' . shami_money_plain( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
	}
	$lines[] = '';
	$lines[] = 'اسم المتجر / الشركة:';
	$lines[] = 'العنوان:';
	return implode( "\n", $lines );
}

/**
 * زر إرسال السلة عبر واتساب.
 */
function shami_cart_whatsapp_button() {
	if ( ! shami_wa_number() || WC()->cart->is_empty() ) {
		return;
	}
	printf(
		'<a class="sh-btn sh-btn--wa sh-btn--block" href="%s" target="_blank" rel="noopener">أرسل الطلبية عبر واتساب</a>',
		esc_url( shami_wa_link( shami_cart_whatsapp_text() ) )
	);
}
add_action( 'woocommerce_proceed_to_checkout', 'shami_cart_whatsapp_button', 30 );

/**
 * رابط متابعة الطلب أسفل السلة.
 */
function shami_cart_continue() {
	printf( '<a class="sh-link" href="%s">أضف أصنافاً أخرى من قائمة الأسعار</a>', esc_url( shami_page_url( 'quick_order' ) ) );
}
add_action( 'woocommerce_proceed_to_checkout', 'shami_cart_continue', 40 );

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

// السلة الفارغة: توجيه مباشر لقائمة الطلب السريع.
add_filter(
	'woocommerce_return_to_shop_redirect',
	static function () {
		return shami_page_url( 'quick_order' );
	}
);
add_action(
	'woocommerce_cart_is_empty',
	static function () {
		echo '<p class="sh-cart-empty__text">حدّد عدد الكراتين لكل صنف من قائمة الأسعار، أو أرسل قائمة أصنافك إلى قسم المبيعات ونجهّزها لك.</p>';
	},
	20
);
add_filter(
	'woocommerce_return_to_shop_text',
	static function () {
		return 'افتح قائمة الأسعار';
	}
);

/**
 * التحقق من الحد الأدنى للطلب.
 */
function shami_min_order_check() {
	$min = (float) shami_opt( 'min_order' );
	if ( $min <= 0 || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	$total = (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax();
	if ( $total < $min ) {
		wc_add_notice(
			sprintf(
				'الحد الأدنى للطلب %1$s. أضف منتجات بقيمة %2$s لإتمام الطلب.',
				wc_price( $min ),
				wc_price( $min - $total )
			),
			'error'
		);
	}
}
add_action( 'woocommerce_check_cart_items', 'shami_min_order_check' );

/**
 * أجزاء السلة المحدّثة عبر AJAX.
 *
 * @param array $fragments الأجزاء.
 * @return array
 */
function shami_cart_fragments( $fragments ) {
	$count = WC()->cart->get_cart_contents_count();
	$fragments['span.sh-cart-count'] = sprintf( '<span class="sh-cart-count" data-count="%1$d">%1$d</span>', $count );
	$fragments['span.sh-cart-total'] = '<span class="sh-cart-total">' . WC()->cart->get_cart_subtotal() . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'shami_cart_fragments' );

/* -------------------------------------------------------------------------
 * صفحة الدفع: حقول مختصرة تناسب أصحاب البقاليات
 * ---------------------------------------------------------------------- */

/**
 * حقول العنوان الافتراضية.
 *
 * @param array $fields الحقول.
 * @return array
 */
function shami_default_address_fields( $fields ) {
	unset( $fields['last_name'], $fields['address_2'], $fields['postcode'], $fields['state'] );
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
		$fields['city']['label']       = 'المدينة / المنطقة';
		$fields['city']['placeholder'] = 'مثال: إسطنبول – الفاتح';
		$fields['city']['priority']    = 50;
	}
	if ( isset( $fields['address_1'] ) ) {
		$fields['address_1']['label']       = 'العنوان بالتفصيل';
		$fields['address_1']['placeholder'] = 'الحي، الشارع، رقم المتجر أو المستودع';
		$fields['address_1']['priority']    = 60;
	}
	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'shami_default_address_fields', 20 );

/**
 * حقول الفوترة.
 *
 * @param array $fields الحقول.
 * @return array
 */
function shami_billing_fields( $fields ) {
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
add_filter( 'woocommerce_billing_fields', 'shami_billing_fields', 20 );

/**
 * ملاحظات الطلب.
 *
 * @param array $fields الحقول.
 * @return array
 */
function shami_checkout_fields( $fields ) {
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'ملاحظات الطلبية';
		$fields['order']['order_comments']['placeholder'] = 'مثال: التسليم صباحاً، رقم ضريبي للفاتورة، أو بديل مقبول لصنف غير متوفر';
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'shami_checkout_fields', 20 );

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
function shami_thankyou_text() {
	return 'شكراً لك! استلمنا طلبيتك، وسيتواصل معك قسم المبيعات لتأكيد الأصناف وموعد التسليم.';
}
add_filter( 'woocommerce_thankyou_order_received_text', 'shami_thankyou_text' );

/**
 * بطاقة تأكيد الطلب عبر واتساب في صفحة الشكر.
 *
 * @param int $order_id رقم الطلب.
 */
function shami_thankyou_whatsapp( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! shami_wa_number() ) {
		return;
	}
	$lines = array( sprintf( 'مرحباً، أرسلت طلبية من الموقع برقم #%s', $order->get_order_number() ) );
	foreach ( $order->get_items() as $item ) {
		$lines[] = sprintf( '• %s — %d كرتونة', $item->get_name(), (int) $item->get_quantity() );
	}
	$lines[] = 'الإجمالي: ' . shami_money_plain( $order->get_total() );
	$lines[] = 'الاسم: ' . trim( $order->get_billing_first_name() . ' ' . $order->get_billing_company() );
	$lines[] = 'الجوال: ' . $order->get_billing_phone();
	printf(
		'<div class="sh-thanks-wa"><div><strong>سرّع تجهيز الطلبية عبر واتساب</strong><p>أرسل ملخص الطلبية لقسم المبيعات ليؤكدها ويحدد موعد التسليم.</p></div><a class="sh-btn sh-btn--wa" href="%s" target="_blank" rel="noopener">تأكيد عبر واتساب</a></div>',
		esc_url( shami_wa_link( implode( "\n", $lines ) ) )
	);
}
add_action( 'woocommerce_thankyou', 'shami_thankyou_whatsapp', 5 );

/* -------------------------------------------------------------------------
 * قسم العروض: مزامنة تلقائية للمنتجات المخفّضة والباقات
 * ---------------------------------------------------------------------- */

/**
 * إضافة/إزالة المنتج من قسم العروض حسب حالة التخفيض.
 *
 * @param int $product_id رقم المنتج.
 */
function shami_sync_offers_category( $product_id ) {
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
	$should   = $product->is_on_sale( 'edit' ) || get_post_meta( $product_id, '_shami_bundle', true );

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
add_action( 'woocommerce_update_product', 'shami_sync_offers_category', 20 );
add_action( 'woocommerce_new_product', 'shami_sync_offers_category', 20 );

/**
 * ربط المنتج بتصنيف العلامة التجارية تلقائياً من الحقل _shami_brand
 * (مفيد عند استيراد المنتجات من ملف CSV).
 *
 * @param int $product_id رقم المنتج.
 */
function shami_sync_brand_term( $product_id ) {
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$slug = get_post_meta( $product_id, '_shami_brand', true );
	if ( ! $slug ) {
		return;
	}
	$current = wp_get_object_terms( $product_id, 'product_brand', array( 'fields' => 'slugs' ) );
	if ( ! is_wp_error( $current ) && in_array( $slug, $current, true ) ) {
		return;
	}
	$term = get_term_by( 'slug', $slug, 'product_brand' );
	if ( ! $term && isset( shami_brands()[ $slug ] ) ) {
		$res  = wp_insert_term( shami_brands()[ $slug ]['ar'], 'product_brand', array( 'slug' => $slug ) );
		$term = is_wp_error( $res ) ? null : get_term( $res['term_id'], 'product_brand' );
	}
	if ( $term ) {
		wp_set_object_terms( $product_id, array( (int) $term->term_id ), 'product_brand', true );
	}
}
add_action( 'woocommerce_update_product', 'shami_sync_brand_term', 25 );
add_action( 'woocommerce_new_product', 'shami_sync_brand_term', 25 );
