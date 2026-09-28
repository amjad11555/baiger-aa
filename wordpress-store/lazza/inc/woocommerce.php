<?php
/**
 * تكامل ووكومرس: التخطيط، بطاقة المنتج، صفحة المنتج، السلة والدفع.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * الغلاف العام لصفحات المتجر
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * فتح الغلاف.
 */
function lazza_wc_wrapper_open() {
	echo '<main id="main" class="lz-main lz-shop"><div class="lz-container">';
}
add_action( 'woocommerce_before_main_content', 'lazza_wc_wrapper_open', 5 );

/**
 * إغلاق الغلاف.
 */
function lazza_wc_wrapper_close() {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'lazza_wc_wrapper_close', 50 );

/**
 * مسار التنقل (Breadcrumb).
 *
 * @param array $args الإعدادات.
 * @return array
 */
function lazza_breadcrumb_defaults( $args ) {
	$args['delimiter']   = '<span class="lz-bc__sep" aria-hidden="true">‹</span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb lz-bc" aria-label="مسار التنقل">';
	$args['wrap_after']  = '</nav>';
	$args['home']        = 'الرئيسية';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'lazza_breadcrumb_defaults' );

/* -------------------------------------------------------------------------
 * صفحات الأرشيف (المتجر، الأقسام، العلامات، البحث)
 * ---------------------------------------------------------------------- */
remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );

/**
 * رأس الأرشيف المخصص.
 */
function lazza_archive_hero() {
	get_template_part( 'template-parts/shop/archive-hero' );
}
add_action( 'woocommerce_shop_loop_header', 'lazza_archive_hero' );

/**
 * نص السيو أسفل القسم.
 */
function lazza_archive_seo_text() {
	if ( ! is_product_taxonomy() ) {
		return;
	}
	$term = get_queried_object();
	if ( ! $term || empty( $term->term_id ) ) {
		return;
	}
	$text = get_term_meta( $term->term_id, 'lazza_seo_text', true );
	if ( ! $text ) {
		return;
	}
	echo '<section class="lz-seo-text lz-prose" aria-label="معلومات عن القسم">' . wp_kses_post( wpautop( $text ) ) . '</section>';
}
add_action( 'woocommerce_after_shop_loop', 'lazza_archive_seo_text', 30 );
add_action( 'woocommerce_no_products_found', 'lazza_archive_seo_text', 30 );

/**
 * رسالة عند عدم وجود نتائج: توجيه لطلب منتج غير متوفر.
 */
function lazza_no_products_cta() {
	printf(
		'<div class="lz-empty"><div class="lz-empty__icon">%1$s</div><h2>لم نجد المنتج الذي تبحث عنه؟</h2><p>لا تقلق — أخبرنا باسمه ونؤمّنه لبقالتك في أسرع وقت.</p><div class="lz-empty__actions"><a class="lz-btn lz-btn--primary" href="%2$s">اطلب منتجاً غير متوفر</a><a class="lz-btn lz-btn--ghost" href="%3$s">تصفّح كل المنتجات</a></div></div>',
		lazza_icon( 'search', '', 34 ), // phpcs:ignore WordPress.Security.EscapeOutput
		esc_url( lazza_page_url( 'special_request' ) ),
		esc_url( wc_get_page_permalink( 'shop' ) )
	);
}
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found' );
add_action( 'woocommerce_no_products_found', 'lazza_no_products_cta', 10 );

/**
 * شريط الأدوات (عدد النتائج + الترتيب).
 */
function lazza_toolbar_open() {
	echo '<div class="lz-toolbar">';
}
/**
 * إغلاق شريط الأدوات.
 */
function lazza_toolbar_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'lazza_toolbar_open', 19 );
add_action( 'woocommerce_before_shop_loop', 'lazza_toolbar_close', 31 );

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
function lazza_price_html( $html, $product ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $html;
	}
	if ( ! lazza_show_prices() ) {
		return '<span class="lz-price-request">السعر عند الطلب</span>';
	}
	if ( '' === $html || ! $product ) {
		return $html;
	}
	return $html . ' <small class="lz-per">/ كرتونة</small>';
}
add_filter( 'woocommerce_get_price_html', 'lazza_price_html', 20, 2 );

/**
 * سعر القطعة الواحدة داخل الكرتونة.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function lazza_unit_price_html( $product ) {
	if ( ! lazza_show_prices() ) {
		return '';
	}
	$units = (int) get_post_meta( $product->get_id(), '_lazza_units', true );
	$price = (float) wc_get_price_to_display( $product );
	if ( $units < 2 || $price <= 0 ) {
		return '';
	}
	return sprintf( '<span class="lz-unit-price">≈ %s للقطعة</span>', wc_price( $price / $units ) );
}

/* -------------------------------------------------------------------------
 * أزرار + و − حول حقل الكمية
 * ---------------------------------------------------------------------- */

/**
 * زر الإنقاص.
 */
function lazza_qty_minus() {
	echo '<button type="button" class="lz-qty-btn lz-qty-minus" aria-label="إنقاص الكمية">' . lazza_icon( 'minus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
/**
 * زر الزيادة.
 */
function lazza_qty_plus() {
	echo '<button type="button" class="lz-qty-btn lz-qty-plus" aria-label="زيادة الكمية">' . lazza_icon( 'plus', '', 18 ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_before_quantity_input_field', 'lazza_qty_minus' );
add_action( 'woocommerce_after_quantity_input_field', 'lazza_qty_plus' );

/* -------------------------------------------------------------------------
 * بطاقة المنتج (تُستخدم في woocommerce/content-product.php)
 * ---------------------------------------------------------------------- */

/**
 * شارات البطاقة.
 *
 * @param WC_Product $product المنتج.
 * @return string
 */
function lazza_card_badges( $product ) {
	$out = '';
	if ( $product->is_on_sale() && lazza_show_prices() ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		if ( $regular > 0 && $sale > 0 ) {
			$out .= sprintf( '<span class="lz-badge lz-badge--sale">وفّر %d%%</span>', (int) round( ( 1 - $sale / $regular ) * 100 ) );
		} else {
			$out .= '<span class="lz-badge lz-badge--sale">عرض</span>';
		}
	}
	if ( get_post_meta( $product->get_id(), '_lazza_bundle', true ) ) {
		$out .= '<span class="lz-badge lz-badge--gold">باقة</span>';
	} elseif ( $product->is_featured() ) {
		$out .= '<span class="lz-badge lz-badge--hot">' . lazza_icon( 'fire', '', 14 ) . ' الأكثر طلباً</span>';
	}
	if ( ! $product->is_in_stock() ) {
		$out .= '<span class="lz-badge lz-badge--muted">نفدت الكمية</span>';
	}
	return $out ? '<div class="lz-card__badges">' . $out . '</div>' : '';
}

/**
 * متحكم السلة في البطاقة (إضافة ثم عدّاد + / −).
 *
 * @param WC_Product $product المنتج.
 * @param int        $qty     الكمية الحالية بالسلة.
 * @param string     $context card|row.
 * @return string
 */
function lazza_cart_control( $product, $qty = 0, $context = 'card' ) {
	$name = $product->get_name();
	if ( ! $product->is_type( 'simple' ) ) {
		return sprintf( '<a class="lz-btn lz-btn--primary lz-btn--sm" href="%s">اختر الخيارات</a>', esc_url( $product->get_permalink() ) );
	}
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return '<span class="lz-btn lz-btn--disabled lz-btn--sm" aria-disabled="true">غير متوفر حالياً</span>';
	}
	$price = (float) wc_get_price_to_display( $product );
	return sprintf(
		'<div class="lz-cart-ctl%1$s" data-id="%2$d" data-qty="%3$d" data-price="%4$s" data-name="%5$s">'
		. '<button type="button" class="lz-cart-ctl__add" aria-label="%6$s">%7$s<span>أضف</span></button>'
		. '<div class="lz-cart-ctl__stepper" role="group" aria-label="%8$s">'
		. '<button type="button" class="lz-step lz-step--minus" data-step="-1" aria-label="إنقاص كرتونة">%9$s</button>'
		. '<input type="number" class="lz-step__input" inputmode="numeric" min="0" max="9999" step="1" value="%3$d" aria-label="عدد الكراتين">'
		. '<button type="button" class="lz-step lz-step--plus" data-step="1" aria-label="زيادة كرتونة">%10$s</button>'
		. '</div></div>',
		$qty > 0 ? ' is-active' : '',
		$product->get_id(),
		(int) $qty,
		esc_attr( $price ),
		esc_attr( $name ),
		esc_attr( 'أضف ' . $name . ' إلى السلة' ),
		lazza_icon( 'plus', '', 18 ),
		esc_attr( 'كمية ' . $name . ' بالكرتونة' ),
		lazza_icon( 'minus', '', 16 ),
		lazza_icon( 'plus', '', 16 )
	);
}

/**
 * طباعة شبكة منتجات مخصصة (للرئيسية).
 *
 * @param array  $args  معايير wc_get_products.
 * @param string $class كلاس إضافي.
 */
function lazza_product_grid( $args, $class = '' ) {
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
	echo '<ul class="products lz-grid ' . esc_attr( $class ) . '">';
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
function lazza_single_brand() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info   = lazza_product_info( $product->get_id() );
	$brands = lazza_brands();
	echo '<div class="lz-single__top">';
	if ( isset( $brands[ $info['brand'] ] ) ) {
		$b    = $brands[ $info['brand'] ];
		$link = taxonomy_exists( 'product_brand' ) ? get_term_link( $info['brand'], 'product_brand' ) : '';
		printf(
			'<a class="lz-chip lz-chip--brand" style="--c:%1$s" href="%2$s">%3$s <span lang="tr">%4$s</span></a>',
			esc_attr( $b['c1'] ),
			esc_url( is_wp_error( $link ) ? '' : $link ),
			esc_html( $b['ar'] ),
			esc_html( $b['latin'] )
		);
	}
	$cats = lazza_categories();
	if ( isset( $cats[ $info['cat'] ] ) ) {
		printf( '<a class="lz-chip" href="%s">%s %s</a>', esc_url( lazza_cat_url( $info['cat'] ) ), lazza_icon( $cats[ $info['cat'] ]['icon'], '', 16 ), esc_html( $cats[ $info['cat'] ]['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'lazza_single_brand', 3 );

/**
 * الاسم التركي الأصلي تحت العنوان (يساعد البحث والسيو).
 */
function lazza_single_tr_name() {
	global $product;
	$tr = $product ? get_post_meta( $product->get_id(), '_lazza_tr', true ) : '';
	if ( $tr ) {
		printf( '<p class="lz-single__tr" lang="tr">%s</p>', esc_html( $tr ) );
	}
}
add_action( 'woocommerce_single_product_summary', 'lazza_single_tr_name', 6 );

/**
 * بيانات الجملة بعد السعر.
 */
function lazza_single_pack() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$info = lazza_product_info( $product->get_id() );
	echo '<div class="lz-single__pack">';
	echo wp_kses_post( lazza_unit_price_html( $product ) );
	if ( $info['pack'] ) {
		printf( '<span class="lz-pack-pill">%1$s التعبئة: <strong>%2$s</strong></span>', lazza_icon( 'box', '', 16 ), esc_html( $info['pack'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	if ( $info['units'] ) {
		printf( '<span class="lz-pack-pill">%1$s الكرتونة = <strong>%2$d قطعة</strong></span>', lazza_icon( 'tag', '', 16 ), (int) $info['units'] ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'lazza_single_pack', 15 );

/**
 * أزرار إضافية: واتساب + الطلب السريع.
 */
function lazza_single_extras() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<div class="lz-single__extras">';
	if ( lazza_wa_number() ) {
		$text = sprintf( "مرحباً، أستفسر عن المنتج: %s\n%s", $product->get_name(), get_permalink( $product->get_id() ) );
		printf( '<a class="lz-btn lz-btn--wa" href="%1$s" target="_blank" rel="noopener">%2$s اسأل عبر واتساب</a>', esc_url( lazza_wa_link( $text ) ), lazza_icon( 'whatsapp', '', 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	printf( '<a class="lz-btn lz-btn--ghost" href="%1$s">%2$s الطلب السريع لكل المنتجات</a>', esc_url( lazza_page_url( 'quick_order' ) ), lazza_icon( 'bolt', '', 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'lazza_single_extras', 35 );

/**
 * شارات الثقة.
 */
function lazza_trust_badges() {
	$items = array(
		array( 'truck', 'توصيل سريع', 'لباب محلّك' ),
		array( 'wallet', 'الدفع عند الاستلام', 'نقداً أو تحويل' ),
		array( 'shield', 'منتجات أصلية', 'من المصنع مباشرة' ),
		array( 'box', 'البيع بالكرتونة', 'أسعار جملة' ),
	);
	echo '<ul class="lz-trust">';
	foreach ( $items as $it ) {
		printf( '<li>%1$s<span><strong>%2$s</strong><small>%3$s</small></span></li>', lazza_icon( $it[0], '', 22 ), esc_html( $it[1] ), esc_html( $it[2] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul>';
}
add_action( 'woocommerce_single_product_summary', 'lazza_trust_badges', 45 );

/**
 * التبويبات.
 *
 * @param array $tabs التبويبات.
 * @return array
 */
function lazza_product_tabs( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = 'وصف المنتج';
	}
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = 'معلومات إضافية';
	}
	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = 'التقييمات';
	}
	$tabs['lazza_wholesale'] = array(
		'title'    => 'معلومات الجملة والتوصيل',
		'priority' => 15,
		'callback' => 'lazza_wholesale_tab',
	);
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'lazza_product_tabs', 20 );

/**
 * محتوى تبويب الجملة.
 */
function lazza_wholesale_tab() {
	global $product;
	$info = lazza_product_info( $product->get_id() );
	$min  = (float) lazza_opt( 'min_order' );
	echo '<div class="lz-prose"><ul class="lz-checklist">';
	if ( $info['pack'] ) {
		printf( '<li>التعبئة: <strong>%s</strong></li>', esc_html( $info['pack'] ) );
	}
	echo '<li>وحدة البيع: <strong>كرتونة كاملة</strong> — يمكنك طلب كرتونة واحدة فقط.</li>';
	if ( $min > 0 ) {
		printf( '<li>الحد الأدنى لقيمة الطلب: <strong>%s</strong></li>', wp_kses_post( wc_price( $min ) ) );
	}
	printf( '<li>التوصيل: داخل %s وإلى جميع الولايات التركية حسب الاتفاق.</li>', esc_html( lazza_opt( 'city' ) ) );
	echo '<li>الدفع: عند الاستلام نقداً أو بالتحويل البنكي.</li>';
	printf( '<li>تحتاج منتجاً غير موجود؟ <a href="%s">اطلبه من هنا</a> ونؤمّنه لك.</li>', esc_url( lazza_page_url( 'special_request' ) ) );
	printf( '<li>للجملة خارج تركيا (حاويات وشحن دولي): <a href="%s">قدّم طلب تصدير</a>.</li>', esc_url( lazza_page_url( 'export' ) ) );
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
function lazza_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'lazza_related_args' );
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
function lazza_cart_whatsapp_text() {
	$lines = array( 'مرحباً 👋 أرغب بطلب المنتجات التالية:' );
	$i     = 1;
	foreach ( WC()->cart->get_cart() as $item ) {
		$p = $item['data'];
		if ( ! $p ) {
			continue;
		}
		$lines[] = sprintf( '%d) %s — %d كرتونة', $i++, $p->get_name(), (int) $item['quantity'] );
	}
	if ( lazza_show_prices() ) {
		$lines[] = '';
		$lines[] = 'الإجمالي التقديري: ' . lazza_money_plain( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() );
	}
	$lines[] = '';
	$lines[] = 'الاسم / اسم البقالة:';
	$lines[] = 'العنوان:';
	return implode( "\n", $lines );
}

/**
 * زر إرسال السلة عبر واتساب.
 */
function lazza_cart_whatsapp_button() {
	if ( ! lazza_wa_number() || WC()->cart->is_empty() ) {
		return;
	}
	printf(
		'<a class="lz-btn lz-btn--wa lz-btn--block" href="%1$s" target="_blank" rel="noopener">%2$s أرسل الطلب عبر واتساب</a>',
		esc_url( lazza_wa_link( lazza_cart_whatsapp_text() ) ),
		lazza_icon( 'whatsapp', '', 20 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}
add_action( 'woocommerce_proceed_to_checkout', 'lazza_cart_whatsapp_button', 30 );

/**
 * رابط متابعة التسوق أسفل السلة.
 */
function lazza_cart_continue() {
	printf( '<a class="lz-link-more" href="%1$s">%2$s أضف المزيد من الطلب السريع</a>', esc_url( lazza_page_url( 'quick_order' ) ), lazza_icon( 'bolt', '', 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_proceed_to_checkout', 'lazza_cart_continue', 40 );

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/**
 * التحقق من الحد الأدنى للطلب.
 */
function lazza_min_order_check() {
	$min = (float) lazza_opt( 'min_order' );
	if ( $min <= 0 || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	$total = (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax();
	if ( $total < $min ) {
		wc_add_notice(
			sprintf(
				'الحد الأدنى للطلب هو %1$s — أضف منتجات بقيمة %2$s لإتمام الطلب.',
				wc_price( $min ),
				wc_price( $min - $total )
			),
			'error'
		);
	}
}
add_action( 'woocommerce_check_cart_items', 'lazza_min_order_check' );

/**
 * أجزاء السلة المحدّثة عبر AJAX.
 *
 * @param array $fragments الأجزاء.
 * @return array
 */
function lazza_cart_fragments( $fragments ) {
	$count = WC()->cart->get_cart_contents_count();
	$fragments['span.lz-cart-count'] = sprintf( '<span class="lz-cart-count" data-count="%1$d">%1$d</span>', $count );
	$fragments['span.lz-cart-total'] = '<span class="lz-cart-total">' . WC()->cart->get_cart_subtotal() . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'lazza_cart_fragments' );

/* -------------------------------------------------------------------------
 * صفحة الدفع: حقول مختصرة تناسب أصحاب البقاليات
 * ---------------------------------------------------------------------- */

/**
 * حقول العنوان الافتراضية.
 *
 * @param array $fields الحقول.
 * @return array
 */
function lazza_default_address_fields( $fields ) {
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
add_filter( 'woocommerce_default_address_fields', 'lazza_default_address_fields', 20 );

/**
 * حقول الفوترة.
 *
 * @param array $fields الحقول.
 * @return array
 */
function lazza_billing_fields( $fields ) {
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
add_filter( 'woocommerce_billing_fields', 'lazza_billing_fields', 20 );

/**
 * ملاحظات الطلب.
 *
 * @param array $fields الحقول.
 * @return array
 */
function lazza_checkout_fields( $fields ) {
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'ملاحظات الطلب';
		$fields['order']['order_comments']['placeholder'] = 'مثال: التوصيل بعد الظهر، أو استبدال منتج غير متوفر بمنتج مشابه';
	}
	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'lazza_checkout_fields', 20 );

add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
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
function lazza_thankyou_text() {
	return 'شكراً لك! استلمنا طلبك بنجاح وسنتواصل معك قريباً لتأكيد موعد التوصيل.';
}
add_filter( 'woocommerce_thankyou_order_received_text', 'lazza_thankyou_text' );

/**
 * بطاقة تأكيد الطلب عبر واتساب في صفحة الشكر.
 *
 * @param int $order_id رقم الطلب.
 */
function lazza_thankyou_whatsapp( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! lazza_wa_number() ) {
		return;
	}
	$lines = array( sprintf( 'مرحباً، أرسلت طلباً من الموقع برقم #%s', $order->get_order_number() ) );
	foreach ( $order->get_items() as $item ) {
		$lines[] = sprintf( '• %s — %d كرتونة', $item->get_name(), (int) $item->get_quantity() );
	}
	$lines[] = 'الإجمالي: ' . lazza_money_plain( $order->get_total() );
	$lines[] = 'الاسم: ' . trim( $order->get_billing_first_name() . ' ' . $order->get_billing_company() );
	$lines[] = 'الجوال: ' . $order->get_billing_phone();
	printf(
		'<div class="lz-thanks-wa"><div><strong>أكّد طلبك بسرعة عبر واتساب</strong><p>أرسل تفاصيل الطلب لفريقنا لتسريع التجهيز والتوصيل.</p></div><a class="lz-btn lz-btn--wa" href="%1$s" target="_blank" rel="noopener">%2$s تأكيد عبر واتساب</a></div>',
		esc_url( lazza_wa_link( implode( "\n", $lines ) ) ),
		lazza_icon( 'whatsapp', '', 20 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}
add_action( 'woocommerce_thankyou', 'lazza_thankyou_whatsapp', 5 );

/* -------------------------------------------------------------------------
 * قسم العروض: مزامنة تلقائية للمنتجات المخفّضة والباقات
 * ---------------------------------------------------------------------- */

/**
 * إضافة/إزالة المنتج من قسم العروض حسب حالة التخفيض.
 *
 * @param int $product_id رقم المنتج.
 */
function lazza_sync_offers_category( $product_id ) {
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
	$should   = $product->is_on_sale( 'edit' ) || get_post_meta( $product_id, '_lazza_bundle', true );

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
add_action( 'woocommerce_update_product', 'lazza_sync_offers_category', 20 );
add_action( 'woocommerce_new_product', 'lazza_sync_offers_category', 20 );

/**
 * ربط المنتج بتصنيف العلامة التجارية تلقائياً من الحقل _lazza_brand
 * (مفيد عند استيراد المنتجات من ملف CSV).
 *
 * @param int $product_id رقم المنتج.
 */
function lazza_sync_brand_term( $product_id ) {
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$slug = get_post_meta( $product_id, '_lazza_brand', true );
	if ( ! $slug ) {
		return;
	}
	$current = wp_get_object_terms( $product_id, 'product_brand', array( 'fields' => 'slugs' ) );
	if ( ! is_wp_error( $current ) && in_array( $slug, $current, true ) ) {
		return;
	}
	$term = get_term_by( 'slug', $slug, 'product_brand' );
	if ( ! $term && isset( lazza_brands()[ $slug ] ) ) {
		$res  = wp_insert_term( lazza_brands()[ $slug ]['ar'], 'product_brand', array( 'slug' => $slug ) );
		$term = is_wp_error( $res ) ? null : get_term( $res['term_id'], 'product_brand' );
	}
	if ( $term ) {
		wp_set_object_terms( $product_id, array( (int) $term->term_id ), 'product_brand', true );
	}
}
add_action( 'woocommerce_update_product', 'lazza_sync_brand_term', 25 );
add_action( 'woocommerce_new_product', 'lazza_sync_brand_term', 25 );
