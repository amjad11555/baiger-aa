<?php
/**
 * الطلب السريع: بيانات جدول المنتجات + مزامنة السلة الفورية.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

/**
 * مجموعات المنتجات حسب الأقسام لصفحة الطلب السريع.
 *
 * @return array slug => ['name'=>..., 'rows'=>[...]]
 */
function lazza_quick_order_groups() {
	$cats   = lazza_categories();
	$groups = array();
	foreach ( array( 'cake', 'biscuits', 'chips', 'snacks', 'offers' ) as $slug ) {
		$groups[ $slug ] = array(
			'name' => $cats[ $slug ]['name'],
			'icon' => $cats[ $slug ]['icon'],
			'rows' => array(),
		);
	}

	$products = wc_get_products(
		array(
			'status'     => 'publish',
			'limit'      => 600,
			'visibility' => 'catalog',
			'orderby'    => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);

	$in_cart = lazza_cart_qty_map();
	$brands  = lazza_brands();

	foreach ( $products as $product ) {
		$id   = $product->get_id();
		$info = lazza_product_info( $id );
		$cat  = isset( $groups[ $info['cat'] ] ) ? $info['cat'] : 'snacks';

		$groups[ $cat ]['rows'][] = array(
			'product' => $product,
			'id'      => $id,
			'name'    => $product->get_name(),
			'tr'      => $info['tr'],
			'pack'    => $info['pack'],
			'units'   => $info['units'],
			'brand'   => $info['brand'],
			'brandAr' => isset( $brands[ $info['brand'] ] ) ? $brands[ $info['brand'] ]['ar'] : '',
			'onsale'  => $product->is_on_sale(),
			'qty'     => isset( $in_cart[ $id ] ) ? (int) $in_cart[ $id ] : 0,
		);
	}

	return array_filter(
		$groups,
		static function ( $g ) {
			return ! empty( $g['rows'] );
		}
	);
}

/**
 * سطر منتج في صفحة الطلب السريع (مخرجات مضغوطة لتخفيف حجم الصفحة).
 *
 * @param array  $r    بيانات السطر.
 * @param string $slug القسم.
 * @return string
 */
function lazza_qo_row_html( $r, $slug ) {
	$brands = lazza_brands();
	$p      = $r['product'];
	$b      = isset( $brands[ $r['brand'] ] ) ? $brands[ $r['brand'] ] : null;
	$url    = get_permalink( $r['id'] );
	$search = implode( ' ', array( $r['name'], $r['tr'], $r['brandAr'], $b ? $b['latin'] . ' ' . $b['alt'] : '', $p->get_sku() ) );

	$meta = '';
	if ( $b ) {
		$meta .= sprintf( '<span class="lz-chip lz-chip--brand lz-chip--xs" style="--c:%s">%s</span>', esc_attr( $b['c1'] ), esc_html( $r['brandAr'] ) );
	}
	if ( $r['pack'] ) {
		$meta .= '<span>' . esc_html( $r['pack'] ) . '</span>';
	}
	if ( $r['onsale'] ) {
		$meta .= '<span class="lz-badge lz-badge--sale lz-badge--xs">عرض</span>';
	}
	if ( $r['tr'] ) {
		$meta .= '<span class="lz-qo-row__tr" lang="tr">' . esc_html( $r['tr'] ) . '</span>';
	}

	return sprintf(
		'<li class="lz-qo-row%1$s" data-id="%2$d" data-cat="%3$s" data-brand="%4$s" data-sale="%5$s" data-search="%6$s">'
		. '<a class="lz-qo-row__art" href="%7$s" tabindex="-1" aria-hidden="true">%8$s</a>'
		. '<div class="lz-qo-row__info"><a class="lz-qo-row__name" href="%7$s">%9$s</a><span class="lz-qo-row__meta">%10$s</span></div>'
		. '<div class="lz-qo-row__price"><span class="price">%11$s</span></div>%12$s</li>',
		$r['qty'] > 0 ? ' is-selected' : '',
		(int) $r['id'],
		esc_attr( $slug ),
		esc_attr( $r['brand'] ),
		$r['onsale'] ? '1' : '0',
		esc_attr( mb_strtolower( $search ) ),
		esc_url( $url ),
		$p->get_image_id() ? $p->get_image( 'woocommerce_gallery_thumbnail' ) : lazza_product_art( $p, 'mini' ),
		esc_html( $r['name'] ),
		$meta,
		wp_kses_post( $p->get_price_html() ),
		lazza_cart_control( $p, $r['qty'], 'row' )
	) . "\n";
}

/**
 * مزامنة كميات السلة (تعيين كميات محددة لكل منتج).
 *
 * يستقبل items بصيغة JSON: {"product_id": qty}. الكمية 0 تحذف المنتج.
 * ملاحظة: مثل نقطة add_to_cart الأصلية في ووكومرس، لا يلزم nonce لأن
 * العملية تخص سلة الزائر نفسه فقط، ونتجنب بذلك مشاكل الصفحات المخزنة مؤقتاً.
 */
function lazza_ajax_sync_cart() {
	ob_start();

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$raw   = isset( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : '';
	$items = is_string( $raw ) ? json_decode( $raw, true ) : null;

	if ( ! is_array( $items ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => 'invalid' ), 400 );
	}

	$items = array_slice( $items, 0, 400, true );
	$cart  = WC()->cart;

	if ( WC()->session && ! WC()->session->has_session() ) {
		WC()->session->set_customer_session_cookie( true );
	}

	// فهرس عناصر السلة الحالية حسب المنتج.
	$keys = array();
	foreach ( $cart->get_cart() as $key => $item ) {
		if ( empty( $item['variation_id'] ) ) {
			$keys[ (int) $item['product_id'] ] = $key;
		}
	}

	$errors = array();
	foreach ( $items as $pid => $qty ) {
		$pid = absint( $pid );
		$qty = max( 0, min( 9999, (int) $qty ) );
		if ( ! $pid ) {
			continue;
		}
		$product = wc_get_product( $pid );
		if ( ! $product || 'publish' !== $product->get_status() || ! $product->is_type( 'simple' ) ) {
			continue;
		}
		if ( isset( $keys[ $pid ] ) ) {
			if ( 0 === $qty ) {
				$cart->remove_cart_item( $keys[ $pid ] );
			} else {
				$cart->set_quantity( $keys[ $pid ], $qty, false );
			}
		} elseif ( $qty > 0 ) {
			$added = $cart->add_to_cart( $pid, $qty );
			if ( false === $added ) {
				$errors[] = $product->get_name();
			}
		}
	}

	$cart->calculate_totals();

	$notices = wc_get_notices( 'error' );
	wc_clear_notices();
	foreach ( $notices as $n ) {
		$errors[] = wp_strip_all_tags( is_array( $n ) ? $n['notice'] : $n );
	}

	ob_start();
	woocommerce_mini_cart();
	$mini = ob_get_clean();

	$fragments = apply_filters(
		'woocommerce_add_to_cart_fragments',
		array( 'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini . '</div>' )
	);

	wp_send_json(
		array(
			'ok'        => true,
			'count'     => $cart->get_cart_contents_count(),
			'lines'     => count( $cart->get_cart() ),
			'subtotal'  => $cart->get_cart_subtotal(),
			'items'     => lazza_cart_qty_map(),
			'fragments' => $fragments,
			'cart_hash' => $cart->get_cart_hash(),
			'errors'    => array_values( array_unique( $errors ) ),
		)
	);
}
add_action( 'wc_ajax_lazza_sync_cart', 'lazza_ajax_sync_cart' );
