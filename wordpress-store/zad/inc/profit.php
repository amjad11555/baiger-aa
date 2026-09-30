<?php
/**
 * سعر البيع المقترح وربح البقال (هامش الربح).
 *
 * لكل صنف سعر بيع مقترح للقطعة (سعر الرف للمستهلك): يُكتب في حقل «سعر البيع المقترح» في صفحة تحرير المنتج،
 * وإن تُرك فارغاً يُقدَّر من «هامش ربح البقال الافتراضي» في «المظهر ← تخصيص ← إعدادات متجر زاد».
 * هامش الربح يُحسب كما يحسبه البقال: (سعر البيع − سعر الشراء) ÷ سعر البيع.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * تقريب سعر الرف للأعلى إلى نصف ليرة (أو ليرة كاملة فوق 100) كما تُسعَّر الرفوف عادة.
 *
 * @param float $price السعر.
 * @return float
 */
function zad_round_shelf_price( $price ) {
	$step = $price < 100 ? 0.5 : 1;
	return ceil( round( $price / $step, 6 ) ) * $step;
}

/**
 * مبلغ منسّق: كسور عشرية فقط حين يلزم (22.50 ₺، 110 ₺).
 *
 * @param float $amount المبلغ.
 * @return string HTML.
 */
function zad_profit_money( $amount ) {
	$decimals = abs( $amount - round( $amount ) ) < 0.005 ? 0 : 2;
	return wc_price( $amount, array( 'decimals' => $decimals ) );
}

/**
 * أرقام الربح لصنف واحد.
 *
 * @param WC_Product $product المنتج.
 * @return array|null [cost, rrp, unit_profit, carton_profit, margin, units, estimated] أو null إن تعذّر الحساب.
 */
function zad_profit_info( $product ) {
	if ( ! $product || ! zad_show_prices() || ! zad_opt( 'show_profit' ) ) {
		return null;
	}
	$units  = (int) get_post_meta( $product->get_id(), '_zad_units', true );
	$carton = (float) wc_get_price_to_display( $product );
	if ( $units < 1 || $carton <= 0 ) {
		return null;
	}
	$cost      = $carton / $units;
	$rrp       = (float) get_post_meta( $product->get_id(), '_zad_rrp', true );
	$estimated = false;
	if ( $rrp <= 0 ) {
		$margin    = min( 90, max( 1, (float) zad_opt( 'retail_margin' ) ) );
		$rrp       = zad_round_shelf_price( $cost / ( 1 - $margin / 100 ) );
		$estimated = true;
	}
	if ( $rrp <= $cost ) {
		return null;
	}
	$unit_profit = $rrp - $cost;
	return array(
		'cost'          => $cost,
		'rrp'           => $rrp,
		'unit_profit'   => $unit_profit,
		'carton_profit' => $unit_profit * $units,
		'margin'        => (int) round( $unit_profit / $rrp * 100 ),
		'units'         => $units,
		'estimated'     => $estimated,
	);
}

/**
 * سطر مختصر للبطاقة وقائمة الأسعار: سعر البيع للقطعة + هامش الربح (وربح الكرتونة للعرض السريع).
 *
 * @param WC_Product $product المنتج.
 * @param string     $context card|row.
 * @return string HTML.
 */
function zad_profit_line_html( $product, $context = 'card' ) {
	$p = zad_profit_info( $product );
	if ( ! $p ) {
		return '';
	}
	if ( 'row' === $context ) {
		return sprintf(
			'<span class="zd-qo-row__profit">بيع القطعة %1$s · ربح <b><bdi>%2$d%%</bdi></b></span>',
			wp_strip_all_tags( zad_profit_money( $p['rrp'] ) ),
			$p['margin']
		);
	}
	return sprintf(
		'<p class="zd-profit-line" data-carton="%3$s"><span>بيع القطعة <b>%1$s</b></span><span class="zd-profit-line__gain">ربحك <bdi>%2$d%%</bdi></span></p>',
		zad_profit_money( $p['rrp'] ),
		$p['margin'],
		esc_attr( zad_money_plain( round( $p['carton_profit'] ) ) )
	);
}

/**
 * صندوق «ربحك من هذا الصنف» في صفحة المنتج.
 */
function zad_single_profit() {
	global $product;
	$p = zad_profit_info( $product );
	if ( ! $p ) {
		return;
	}
	?>
	<section class="zd-profit" aria-labelledby="zd-profit-title">
		<p class="zd-profit__title" id="zd-profit-title">ربحك من هذا الصنف</p>
		<dl class="zd-profit__grid">
			<div><dt>سعر الشراء للقطعة</dt><dd><?php echo wp_kses_post( zad_profit_money( $p['cost'] ) ); ?></dd></div>
			<div><dt>سعر البيع المقترح</dt><dd><?php echo wp_kses_post( zad_profit_money( $p['rrp'] ) ); ?></dd></div>
			<div><dt>ربح الكرتونة (<?php echo (int) $p['units']; ?> قطعة)</dt><dd class="zd-profit__gain"><?php echo wp_kses_post( zad_profit_money( round( $p['carton_profit'] ) ) ); ?></dd></div>
			<div><dt>هامش الربح</dt><dd class="zd-profit__gain"><bdi><?php echo (int) $p['margin']; ?>%</bdi></dd></div>
		</dl>
		<p class="zd-profit__note">
			<?php
			if ( $p['estimated'] ) {
				printf( 'سعر بيع تقديري بهامش %d%% فأكثر بعد تقريب السعر، والتسعير على رفك قرارك.', (int) zad_opt( 'retail_margin' ) );
			} else {
				echo 'سعر البيع المعتاد للمستهلك في السوق.';
			}
			?>
		</p>
	</section>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'zad_single_profit', 19 );

/* -------------------------------------------------------------------------
 * لوحة التحكم: حقول الجملة في صفحة تحرير المنتج (تبويب «عام»)
 * ---------------------------------------------------------------------- */

/**
 * حقول التعبئة وعدد القطع وسعر البيع المقترح والاسم التركي.
 */
function zad_product_wholesale_fields() {
	echo '<div class="options_group zd-admin-wholesale">';
	echo '<p class="form-field"><strong>بيانات الجملة (قالب زاد)</strong></p>';
	woocommerce_wp_text_input(
		array(
			'id'          => '_zad_pack',
			'label'       => 'تعبئة الكرتونة',
			'placeholder' => '24 × 45 غ',
			'desc_tip'    => true,
			'description' => 'تظهر على البطاقة وفي قائمة الأسعار.',
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'                => '_zad_units',
			'label'             => 'عدد القطع في الكرتونة',
			'type'              => 'number',
			'custom_attributes' => array(
				'min'  => 1,
				'step' => 1,
			),
			'desc_tip'          => true,
			'description'       => 'يُحسب منه سعر القطعة وربح الكرتونة.',
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'                => '_zad_rrp',
			'label'             => 'سعر البيع المقترح للقطعة (' . get_woocommerce_currency_symbol() . ')',
			'placeholder'       => 'مثال: 12.5',
			'custom_attributes' => array( 'inputmode' => 'decimal' ),
			'desc_tip'          => true,
			'description'       => 'سعر الرف للمستهلك. اتركه فارغاً ليُقدَّر من هامش ربح البقال الافتراضي في إعدادات المتجر.',
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => '_zad_tr',
			'label'       => 'الاسم التركي الأصلي',
			'placeholder' => 'Eti Browni Intense',
			'desc_tip'    => true,
			'description' => 'يساعد البحث بالتركية ويظهر تحت اسم الصنف.',
		)
	);
	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'zad_product_wholesale_fields' );

/**
 * حفظ حقول الجملة.
 *
 * @param WC_Product $product المنتج.
 */
function zad_product_wholesale_save( $product ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- ووكومرس يتحقق من nonce المنتج قبل هذا الخطاف.
	if ( isset( $_POST['_zad_pack'] ) ) {
		$product->update_meta_data( '_zad_pack', sanitize_text_field( wp_unslash( $_POST['_zad_pack'] ) ) );
	}
	if ( isset( $_POST['_zad_units'] ) ) {
		$units = absint( $_POST['_zad_units'] );
		if ( $units ) {
			$product->update_meta_data( '_zad_units', $units );
		} else {
			$product->delete_meta_data( '_zad_units' );
		}
	}
	if ( isset( $_POST['_zad_rrp'] ) ) {
		$rrp = sanitize_text_field( wp_unslash( $_POST['_zad_rrp'] ) );
		// الفاصلة العشرية الشائعة في تركيا: 12,5 تعني 12.5 لا 125.
		if ( false === strpos( $rrp, '.' ) && 1 === substr_count( $rrp, ',' ) ) {
			$rrp = str_replace( ',', '.', $rrp );
		}
		$rrp = wc_format_decimal( $rrp );
		if ( '' !== $rrp && (float) $rrp > 0 ) {
			$product->update_meta_data( '_zad_rrp', $rrp );
		} else {
			$product->delete_meta_data( '_zad_rrp' );
		}
	}
	if ( isset( $_POST['_zad_tr'] ) ) {
		$product->update_meta_data( '_zad_tr', sanitize_text_field( wp_unslash( $_POST['_zad_tr'] ) ) );
	}
	// phpcs:enable
}
add_action( 'woocommerce_admin_process_product_object', 'zad_product_wholesale_save' );
