<?php
/**
 * تكامل ووكومرس: التخطيط، بطاقة المنتج، صفحة المنتج، السلة والدفع.
 *
 * @package Maria
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
function maria_wc_wrapper_open() {
	echo '<main id="main" class="mr-main mr-shop"><div class="mr-container">';
}
add_action( 'woocommerce_before_main_content', 'maria_wc_wrapper_open', 5 );

/**
 * إغلاق الغلاف.
 */
function maria_wc_wrapper_close() {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'maria_wc_wrapper_close', 50 );

/**
 * مسار التنقل (Breadcrumb).
 *
 * @param array $args الإعدادات.
 * @return array
 */
function maria_breadcrumb_defaults( $args ) {
	$args['delimiter']   = '<span class="mr-bc__sep" aria-hidden="true">‹</span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb mr-bc" aria-label="مسار التنقل">';
	$args['wrap_after']  = '</nav>';
	$args['home']        = 'الرئيسية';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'maria_breadcrumb_defaults' );

/* -------------------------------------------------------------------------
 * صفحات الأرشيف (المتجر، الأقسام، العلامات، البحث)
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );

/**
 * رأس الأرشيف المخصص.
 */
function maria_archive_hero() {
	get_template_part( 'template-parts/shop/archive-hero' );
}
add_action( 'woocommerce_shop_loop_header', 'maria_archive_hero' );

/**
 * نص السيو أسفل القسم.
 */
function maria_archive_seo_text() {
	if ( ! is_product_taxonomy() ) {
		return;
	}
	$term = get_queried_object();
	if ( ! $term || empty( $term->term_id ) ) {
		return;
	}
	$text = get_term_meta( $term->term_id, 'maria_seo_text', true );
	if ( ! $text ) {
		return;
	}
	echo '<section class="mr-seo-text mr-prose" aria-label="معلومات عن القسم">' . wp_kses_post( wpautop( $text ) ) . '</section>';
}
add_action( 'woocommerce_after_shop_loop', 'maria_archive_seo_text', 30 );
add_action( 'woocommerce_no_products_found', 'maria_archive_seo_text', 30 );

/**
 * رسالة عند عدم وجود نتائج: توجيه لطلب منتج غير متوفر.
 */
function maria_no_products_cta() {
	printf(
		'<div class="mr-empty"><div class="mr-empty__icon">%1$s</div><h2>لم نجد ما تبحث عنه</h2><p>جرّب كلمة أقصر أو اسم الشركة، أو أخبرنا باسم المنتج ونؤمّنه لبقالتك.</p><div class="mr-empty__actions"><a class="mr-btn mr-btn--primary" href="%2$s">اطلب منتجاً غير متوفر</a><a class="mr-btn mr-btn--ghost" href="%3$s">تصفّح كل المنتجات</a></div></div>',
		maria_icon( 'search', '', 34 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_url( maria_page_url( 'special_request' ) ),
		esc_url( wc_get_page_permalink( 'shop' ) )
	);
}
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
add_action( 'woocommerce_no_products_found', 'maria_no_products_cta', 10 );

/**
 * شريط الأدوات (عدد النتائج + الترتيب).
 */
function maria_toolbar_open() {
	echo '<div class="mr-toolbar">';
}
/**
 * إغلاق شريط الأدوات.
 */
function maria_toolbar_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'maria_toolbar_open', 19 );
add_action( 'woocommerce_before_shop_loop', 'maria_toolbar_close', 31 );

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
function maria_price_html( $html, $product ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $html;
	}
	if ( ! maria_show_prices() ) {
		return '<span class="mr-price-request">السعر عند الطلب</span>';
	}
	if ( '' === $html || ! $product ) {
		return $html;
	}
	return $html . ' <small class="mr-per">/ كرتونة</small>';
}
add_filter( 'woocommerce_get_price_html', 'maria_price_html', 20, 2 );

/**
 * سعر القطعة الواحدة داخل الكرتونة.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function maria_unit_price_html( $product ) {
	if ( ! maria_show_prices() ) {
		return '';
	}
	$units = (int) get_post_meta( $product->get_id(), '_maria_units', true );
	$price = (float) wc_get_price_to_display( $product );
	if ( $units < 2 || $price <= 0 ) {
		return '';
	}
	return sprintf( '<span class="mr-unit-price">≈ %s للقطعة</span>', wc_price( $price / $units ) );
}

/* -------------------------------------------------------------------------
 * أزرار + و − حول حقل الكمية
 * ---------------------------------------------------------------------- */

/**
 * زر الإنقاص.
 */
function maria_qty_minus() {
	echo '<button type="button" class="mr-qty-btn mr-qty-minus" aria-label="إنقاص الكمية">' . maria_icon( 'minus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
/**
 * زر الزيادة.
 */
function maria_qty_plus() {
	echo '<button type="button" class="mr-qty-btn mr-qty-plus" aria-label="زيادة الكمية">' . maria_icon( 'plus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_before_quantity_input_field', 'maria_qty_minus' );
add_action( 'woocommerce_after_quantity_input_field', 'maria_qty_plus' );

/* -------------------------------------------------------------------------
 * بطاقة المنتج (تُستخدم في woocommerce/content-product.php)
 * ---------------------------------------------------------------------- */

/**
 * نسبة الخصم المئوية لمنتج (0 إن لم يكن عليه عرض).
 *
 * @param WC_Product $product المنتج.
 * @return int
 */
function maria_discount_percent( $product ) {
	if ( ! $product->is_on_sale() ) {
		return 0;
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	return ( $regular > 0 && $sale > 0 ) ? (int) round( ( 1 - $sale / $regular ) * 100 ) : 0;
}

/**
 * شارات صورة البطاقة (أعلى الصورة): «مطلوب» و«نفدت الكمية».
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function maria_card_badges( $product ) {
	$out = '';
	if ( ! $product->is_in_stock() ) {
		$out .= '<span class="mr-badge mr-badge--muted">نفدت الكمية</span>';
	} elseif ( $product->is_featured() ) {
		$out .= '<span class="mr-badge mr-badge--hot">مطلوب</span>';
	}
	return $out ? '<div class="mr-card__badges">' . $out . '</div>' : '';
}

/**
 * شارات أسفل السعر: نسبة الخصم والباقة الموفّرة.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function maria_card_tags( $product ) {
	$out = '';
	if ( $product->is_on_sale() && maria_show_prices() ) {
		$pct  = maria_discount_percent( $product );
		$out .= $pct ? sprintf( '<span class="mr-badge mr-badge--sale">خصم <bdi>%d%%</bdi></span>', $pct ) : '<span class="mr-badge mr-badge--sale">عرض</span>';
	}
	if ( get_post_meta( $product->get_id(), '_maria_bundle', true ) ) {
		$out .= '<span class="mr-badge mr-badge--bundle">باقة موفّرة</span>';
	}
	return $out ? '<div class="mr-card__tags">' . $out . '</div>' : '';
}

/**
 * متحكم السلة: زر «أضف» ثم عدّاد + / − متزامن فوراً مع السلة.
 *
 * @param WC_Product $product المنتج.
 * @param int        $qty     الكمية الحالية بالسلة.
 * @param string     $context card|row|lg.
 * @return string
 */
function maria_cart_control( $product, $qty = 0, $context = 'card' ) {
	$name = $product->get_name();
	if ( ! $product->is_type( 'simple' ) ) {
		return sprintf( '<a class="mr-btn mr-btn--ghost mr-btn--sm" href="%s">اختر الخيارات</a>', esc_url( $product->get_permalink() ) );
	}
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return '<span class="mr-cart-ctl mr-cart-ctl--' . esc_attr( $context ) . ' is-disabled"><span class="mr-cart-ctl__na">غير متوفر حالياً</span></span>';
	}
	$price = (float) wc_get_price_to_display( $product );
	$label = 'row' === $context ? '<span class="screen-reader-text">أضف</span>' : '<span>أضف إلى السلة</span>';
	return sprintf(
		'<div class="mr-cart-ctl mr-cart-ctl--%11$s%1$s" data-id="%2$d" data-qty="%3$d" data-price="%4$s" data-name="%5$s">'
		. '<button type="button" class="mr-cart-ctl__add" aria-label="%6$s">%7$s%12$s</button>'
		. '<div class="mr-cart-ctl__stepper" role="group" aria-label="%8$s">'
		. '<button type="button" class="mr-step mr-step--minus" data-step="-1" aria-label="إنقاص كرتونة">%9$s</button>'
		. '<input type="number" class="mr-step__input" inputmode="numeric" min="0" max="9999" step="1" value="%3$d" aria-label="عدد الكراتين">'
		. '<button type="button" class="mr-step mr-step--plus" data-step="1" aria-label="زيادة كرتونة">%10$s</button>'
		. '</div></div>',
		$qty > 0 ? ' is-active' : '',
		$product->get_id(),
		(int) $qty,
		esc_attr( $price ),
		esc_attr( $name ),
		esc_attr( 'أضف ' . $name . ' إلى السلة' ),
		'row' === $context ? maria_icon( 'plus', '', 20 ) : maria_icon( 'cart', '', 22 ),
		esc_attr( 'كمية ' . $name . ' بالكرتونة' ),
		maria_icon( 'minus', '', 18 ),
		maria_icon( 'plus', '', 18 ),
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
function maria_product_grid( $args, $class = '' ) {
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
	echo '<ul class="products mr-grid ' . esc_attr( $class ) . '">';
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

/**
 * شريط منتجات أفقي يُمرَّر باللمس مع نقاط تنقّل أسفله.
 *
 * @param array $args معايير wc_get_products.
 */
function maria_product_rail( $args ) {
	ob_start();
	maria_product_grid( $args, 'mr-grid--rail' );
	$grid = ob_get_clean();
	if ( $grid ) {
		echo '<div class="mr-rail" data-mr-rail>' . $grid . '<div class="mr-dots" data-mr-rail-dots></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/* -------------------------------------------------------------------------
 * صفحة المنتج المفرد
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

/**
 * شارة العلامة التجارية فوق العنوان.
 */
function maria_single_brand() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info   = maria_product_info( $product->get_id() );
	$brands = maria_brands();
	echo '<div class="mr-single__top">';
	if ( isset( $brands[ $info['brand'] ] ) ) {
		$b    = $brands[ $info['brand'] ];
		$link = taxonomy_exists( 'product_brand' ) ? get_term_link( $info['brand'], 'product_brand' ) : '';
		printf(
			'<a class="mr-chip mr-chip--brand" style="--c:%1$s" href="%2$s">%3$s <span lang="tr">%4$s</span></a>',
			esc_attr( $b['c1'] ),
			esc_url( is_wp_error( $link ) ? '' : $link ),
			esc_html( $b['ar'] ),
			esc_html( $b['latin'] )
		);
	}
	$cats = maria_categories();
	if ( isset( $cats[ $info['cat'] ] ) ) {
		printf( '<a class="mr-chip" href="%s">%s %s</a>', esc_url( maria_cat_url( $info['cat'] ) ), maria_icon( $cats[ $info['cat'] ]['icon'], '', 16 ), esc_html( $cats[ $info['cat'] ]['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'maria_single_brand', 3 );

/**
 * الاسم التركي الأصلي تحت العنوان (يساعد البحث والسيو).
 */
function maria_single_tr_name() {
	global $product;
	$tr = $product ? get_post_meta( $product->get_id(), '_maria_tr', true ) : '';
	if ( $tr ) {
		printf( '<p class="mr-single__tr" lang="tr">%s</p>', esc_html( $tr ) );
	}
}
add_action( 'woocommerce_single_product_summary', 'maria_single_tr_name', 6 );

/**
 * بيانات الجملة بعد السعر.
 */
function maria_single_pack() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info = maria_product_info( $product->get_id() );
	echo '<div class="mr-single__pack">';
	echo wp_kses_post( maria_unit_price_html( $product ) );
	if ( $info['pack'] ) {
		printf( '<span class="mr-pack-pill">%1$s التعبئة: <strong>%2$s</strong></span>', maria_icon( 'box', '', 16 ), esc_html( $info['pack'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	if ( $info['units'] ) {
		printf( '<span class="mr-pack-pill">%1$s الكرتونة = <strong>%2$d قطعة</strong></span>', maria_icon( 'tag', '', 16 ), (int) $info['units'] ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'maria_single_pack', 15 );

/**
 * صندوق الشراء في صفحة المنتج: عدّاد كراتين متزامن فوراً بدل نموذج ووكومرس.
 */
function maria_single_buy() {
	global $product;
	if ( ! $product ) {
		return;
	}
	if ( ! $product->is_type( 'simple' ) ) {
		woocommerce_template_single_add_to_cart();
		return;
	}
	$map = maria_cart_qty_cached();
	$qty = isset( $map[ $product->get_id() ] ) ? (int) $map[ $product->get_id() ] : 0;
	echo '<div class="mr-buy"><span class="mr-buy__label">عدد الكراتين</span>';
	echo maria_cart_control( $product, $qty, 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput
	printf( '<a class="mr-buy__checkout" href="%1$s" data-mr-checkout>أكمل الطلب %2$s</a>', esc_url( wc_get_checkout_url() ), maria_icon( 'arrow-left', '', 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div>';
}
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'maria_single_buy', 30 );

/**
 * أزرار إضافية: واتساب + الطلب السريع.
 */
function maria_single_extras() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="mr-single__extras">';
	if ( maria_wa_number() ) {
		$text = sprintf( "مرحباً، أستفسر عن المنتج: %s\n%s", $product->get_name(), get_permalink( $product->get_id() ) );
		printf( '<a class="mr-btn mr-btn--wa" href="%1$s" target="_blank" rel="noopener">%2$s اسأل عبر واتساب</a>', esc_url( maria_wa_link( $text ) ), maria_icon( 'whatsapp', '', 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	printf( '<a class="mr-btn mr-btn--ghost" href="%1$s">%2$s قائمة الطلب السريع</a>', esc_url( maria_page_url( 'quick_order' ) ), maria_icon( 'list', '', 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'maria_single_extras', 35 );

/**
 * شارات الثقة.
 */
function maria_trust_badges() {
	$items = array(
		array( 'truck', 'توصيل سريع', 'إلى باب محلّك' ),
		array( 'wallet', 'الدفع عند الاستلام', 'نقداً أو بتحويل' ),
		array( 'shield', 'منتجات أصلية', 'بصلاحية حديثة' ),
		array( 'box', 'البيع بالكرتونة', 'من كرتونة واحدة' ),
	);
	echo '<ul class="mr-trust">';
	foreach ( $items as $it ) {
		printf( '<li>%1$s<span><strong>%2$s</strong><small>%3$s</small></span></li>', maria_icon( $it[0], '', 22 ), esc_html( $it[1] ), esc_html( $it[2] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul>';
}
add_action( 'woocommerce_single_product_summary', 'maria_trust_badges', 45 );

/**
 * التبويبات.
 *
 * @param array $tabs التبويبات.
 * @return array
 */
function maria_product_tabs( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = 'وصف المنتج';
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = 'معلومات إضافية';
	}
	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = 'التقييمات';
	}
	$tabs['maria_wholesale'] = array(
		'title'    => 'معلومات الجملة والتوصيل',
		'priority' => 15,
		'callback' => 'maria_wholesale_tab',
	);
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'maria_product_tabs', 20 );

/**
 * محتوى تبويب الجملة.
 */
function maria_wholesale_tab() {
	global $product;
	$info = maria_product_info( $product->get_id() );
	$min  = (float) maria_opt( 'min_order' );
	echo '<div class="mr-prose"><ul class="mr-checklist">';
	if ( $info['pack'] ) {
		printf( '<li>التعبئة: <strong>%s</strong></li>', esc_html( $info['pack'] ) );
	}
	echo '<li>وحدة البيع: <strong>كرتونة كاملة</strong>، ويمكنك طلب كرتونة واحدة فقط.</li>';
	if ( $min > 0 ) {
		printf( '<li>الحد الأدنى لقيمة الطلب: <strong>%s</strong></li>', wp_kses_post( wc_price( $min ) ) );
	}
	printf( '<li>التوصيل: داخل %s وإلى جميع الولايات التركية حسب الموقع والكمية.</li>', esc_html( maria_opt( 'city' ) ) );
	echo '<li>الدفع: عند الاستلام نقداً أو بالتحويل البنكي.</li>';
	printf( '<li>تحتاج منتجاً غير موجود؟ <a href="%s">اطلبه من هنا</a> ونؤمّنه لك.</li>', esc_url( maria_page_url( 'special_request' ) ) );
	printf( '<li>للجملة خارج تركيا (حاويات وشحن دولي): <a href="%s">قدّم طلب تصدير</a>.</li>', esc_url( maria_page_url( 'export' ) ) );
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
function maria_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'maria_related_args' );
add_filter(
	'woocommerce_product_related_products_heading',
	static function () {
		return 'منتجات قد تهم بقالتك';
	}
);
add_filter(
	'woocommerce_product_single_add_to_cart_text',
	static function () {
		return 'أضف إلى السلة';
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
function maria_cart_whatsapp_text() {
	$lines = array( 'مرحباً 👋 أرغب بطلب المنتجات التالية:' );
	$i     = 1;
	foreach ( WC()->cart->get_cart() as $item ) {
		$p = $item['data'];
		if ( ! $p ) {
			continue;
		}
		$lines[] = sprintf( '%d) %s — %d كرتونة', $i++, $p->get_name(), (int) $item['quantity'] );
	}
	if ( maria_show_prices() ) {
		$lines[] = '';
		$lines[] = 'الإجمالي التقديري: ' . maria_money_plain( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
	}
	$lines[] = '';
	$lines[] = 'الاسم / اسم البقالة:';
	$lines[] = 'العنوان:';
	return implode( "\n", $lines );
}

/**
 * زر إرسال السلة عبر واتساب.
 */
function maria_cart_whatsapp_button() {
	if ( ! maria_wa_number() || WC()->cart->is_empty() ) {
		return;
	}
	printf(
		'<a class="mr-btn mr-btn--wa mr-btn--block" href="%1$s" target="_blank" rel="noopener">%2$s أرسل الطلب عبر واتساب</a>',
		esc_url( maria_wa_link( maria_cart_whatsapp_text() ) ),
		maria_icon( 'whatsapp', '', 20 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}
add_action( 'woocommerce_proceed_to_checkout', 'maria_cart_whatsapp_button', 30 );

/**
 * رابط متابعة التسوق أسفل السلة.
 */
function maria_cart_continue() {
	printf( '<a class="mr-link-more" href="%1$s">%2$s أضف أصنافاً أخرى من قائمة الطلب السريع</a>', esc_url( maria_page_url( 'quick_order' ) ), maria_icon( 'bolt', '', 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_proceed_to_checkout', 'maria_cart_continue', 40 );

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

// السلة الفارغة: توجيه مباشر لقائمة الطلب السريع.
add_filter(
	'woocommerce_return_to_shop_redirect',
	static function () {
		return maria_page_url( 'quick_order' );
	}
);
add_filter(
	'woocommerce_return_to_shop_text',
	static function () {
		return 'ابدأ طلبك الآن';
	}
);

/**
 * التحقق من الحد الأدنى للطلب.
 */
function maria_min_order_check() {
	$min = (float) maria_opt( 'min_order' );
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
add_action( 'woocommerce_check_cart_items', 'maria_min_order_check' );

/**
 * أجزاء السلة المحدّثة عبر AJAX.
 *
 * @param array $fragments الأجزاء.
 * @return array
 */
function maria_cart_fragments( $fragments ) {
	$count = WC()->cart->get_cart_contents_count();
	$fragments['span.mr-cart-count'] = sprintf( '<span class="mr-cart-count" data-count="%1$d">%1$d</span>', $count );
	$fragments['span.mr-cart-total'] = '<span class="mr-cart-total">' . WC()->cart->get_cart_subtotal() . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'maria_cart_fragments' );

/* -------------------------------------------------------------------------
 * صفحة الدفع: حقول مختصرة تناسب أصحاب البقاليات
 * ---------------------------------------------------------------------- */

/**
 * حقول العنوان الافتراضية.
 *
 * @param array $fields الحقول.
 * @return array
 */
function maria_default_address_fields( $fields ) {
	unset( $fields['last_name'], $fields['address_2'], $fields['postcode'], $fields['state'] );
	if ( isset( $fields['first_name'] ) ) {
		$fields['first_name']['label']       = 'الاسم الكامل';
		$fields['first_name']['placeholder'] = 'اسم صاحب البقالة أو المسؤول';
		$fields['first_name']['class']       = array( 'form-row-wide' );
		$fields['first_name']['priority']    = 10;
	}
	if ( isset( $fields['company'] ) ) {
		$fields['company']['label']       = 'اسم البقالة / المحل';
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
		$fields['address_1']['placeholder'] = 'الحي، الشارع، رقم المحل، علامة مميزة';
		$fields['address_1']['priority']    = 60;
	}
	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'maria_default_address_fields', 20 );

/**
 * حقول الفوترة.
 *
 * @param array $fields الحقول.
 * @return array
 */
function maria_billing_fields( $fields ) {
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
add_filter( 'woocommerce_billing_fields', 'maria_billing_fields', 20 );

/**
 * ملاحظات الطلب.
 *
 * @param array $fields الحقول.
 * @return array
 */
function maria_checkout_fields( $fields ) {
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'ملاحظات الطلب';
		$fields['order']['order_comments']['placeholder'] = 'مثال: التوصيل بعد الظهر، أو استبدال منتج غير متوفر بمنتج مشابه';
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'maria_checkout_fields', 20 );

add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
// صفحة دفع أبسط: لا خانة كوبونات (أسعار الجملة واضحة مسبقاً).
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
add_filter(
	'woocommerce_order_button_text',
	static function () {
		return 'تأكيد الطلب';
	}
);

/**
 * نص صفحة الشكر.
 *
 * @return string
 */
function maria_thankyou_text() {
	return 'شكراً لك! استلمنا طلبك بنجاح وسنتواصل معك قريباً لتأكيد موعد التوصيل.';
}
add_filter( 'woocommerce_thankyou_order_received_text', 'maria_thankyou_text' );

/**
 * بطاقة تأكيد الطلب عبر واتساب في صفحة الشكر.
 *
 * @param int $order_id رقم الطلب.
 */
function maria_thankyou_whatsapp( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! maria_wa_number() ) {
		return;
	}
	$lines = array( sprintf( 'مرحباً، أرسلت طلباً من الموقع برقم #%s', $order->get_order_number() ) );
	foreach ( $order->get_items() as $item ) {
		$lines[] = sprintf( '• %s — %d كرتونة', $item->get_name(), (int) $item->get_quantity() );
	}
	$lines[] = 'الإجمالي: ' . maria_money_plain( $order->get_total() );
	$lines[] = 'الاسم: ' . trim( $order->get_billing_first_name() . ' ' . $order->get_billing_company() );
	$lines[] = 'الجوال: ' . $order->get_billing_phone();
	printf(
		'<div class="mr-thanks-wa"><div><strong>أكّد طلبك بسرعة عبر واتساب</strong><p>أرسل تفاصيل الطلب لفريقنا لتسريع التجهيز والتوصيل.</p></div><a class="mr-btn mr-btn--wa" href="%1$s" target="_blank" rel="noopener">%2$s تأكيد عبر واتساب</a></div>',
		esc_url( maria_wa_link( implode( "\n", $lines ) ) ),
		maria_icon( 'whatsapp', '', 20 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}
add_action( 'woocommerce_thankyou', 'maria_thankyou_whatsapp', 5 );

/* -------------------------------------------------------------------------
 * قسم العروض: مزامنة تلقائية للمنتجات المخفّضة والباقات
 * ---------------------------------------------------------------------- */

/**
 * إضافة/إزالة المنتج من قسم العروض حسب حالة التخفيض.
 *
 * @param int $product_id رقم المنتج.
 */
function maria_sync_offers_category( $product_id ) {
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
	$should   = $product->is_on_sale( 'edit' ) || get_post_meta( $product_id, '_maria_bundle', true );

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
add_action( 'woocommerce_update_product', 'maria_sync_offers_category', 20 );
add_action( 'woocommerce_new_product', 'maria_sync_offers_category', 20 );

/**
 * ربط المنتج بتصنيف العلامة التجارية تلقائياً من الحقل _maria_brand
 * (مفيد عند استيراد المنتجات من ملف CSV).
 *
 * @param int $product_id رقم المنتج.
 */
function maria_sync_brand_term( $product_id ) {
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$slug = get_post_meta( $product_id, '_maria_brand', true );
	if ( ! $slug ) {
		return;
	}
	$current = wp_get_object_terms( $product_id, 'product_brand', array( 'fields' => 'slugs' ) );
	if ( ! is_wp_error( $current ) && in_array( $slug, $current, true ) ) {
		return;
	}
	$term = get_term_by( 'slug', $slug, 'product_brand' );
	if ( ! $term && isset( maria_brands()[ $slug ] ) ) {
		$res  = wp_insert_term( maria_brands()[ $slug ]['ar'], 'product_brand', array( 'slug' => $slug ) );
		$term = is_wp_error( $res ) ? null : get_term( $res['term_id'], 'product_brand' );
	}
	if ( $term ) {
		wp_set_object_terms( $product_id, array( (int) $term->term_id ), 'product_brand', true );
	}
}
add_action( 'woocommerce_update_product', 'maria_sync_brand_term', 25 );
add_action( 'woocommerce_new_product', 'maria_sync_brand_term', 25 );
