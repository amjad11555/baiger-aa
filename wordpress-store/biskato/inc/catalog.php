<?php
/**
 * الكتالوج: إضافة منتجات ملف الجرد وتحديثها، وحذف المنتجات القديمة، وخصومات العلامات.
 *
 * يعمل على دفعات قصيرة حتى لا تتجاوز الاستضافة مهلة التنفيذ:
 * - من لوحة التحكم: شريط تقدّم يعمل تلقائياً ما دامت الصفحة مفتوحة.
 * - في الخلفية: مهمة WP-Cron كل دقيقة حتى ينتهي العمل ولو أُغلقت الصفحة.
 * - من سطر الأوامر: wp zad catalog
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * صفوف الكتالوج بأسماء الحقول.
 *
 * @return array
 */
function zad_catalog_rows() {
	static $rows = null;
	if ( null !== $rows ) {
		return $rows;
	}
	$keys = array( 'sku', 'brand', 'cat', 'line', 'name', 'tr', 'flavor', 'pack', 'units', 'price', 'sale', 'best', 'desc', 'unit' );
	$rows = array();
	foreach ( (array) include ZAD_DIR . '/inc/data/catalog.php' as $row ) {
		$row    = array_pad( $row, count( $keys ), '' );
		$rows[] = array_combine( $keys, $row );
	}
	return $rows;
}

/**
 * بصمة الكتالوج (تتغير عند تعديل ملف البيانات).
 *
 * @return string
 */
function zad_catalog_version() {
	return substr( md5_file( ZAD_DIR . '/inc/data/catalog.php' ), 0, 12 );
}

/**
 * نسبة خصم العلامة من الإعدادات (إيتي 2% وأولكر 5% افتراضياً).
 *
 * @param string $brand رمز العلامة.
 * @return float
 */
function zad_brand_discount( $brand ) {
	$map = apply_filters(
		'zad_brand_discounts',
		array(
			'eti'   => (float) zad_opt( 'disc_eti' ),
			'ulker' => (float) zad_opt( 'disc_ulker' ),
		)
	);
	return isset( $map[ $brand ] ) ? max( 0, min( 90, (float) $map[ $brand ] ) ) : 0.0;
}

/**
 * سعر العرض بعد خصم العلامة (لأقرب قرش)، أو '' بلا خصم.
 *
 * @param float  $price السعر.
 * @param string $brand العلامة.
 * @return string
 */
function zad_discounted_price( $price, $brand ) {
	$d = zad_brand_discount( $brand );
	if ( $d <= 0 || (float) $price <= 0 ) {
		return '';
	}
	return (string) round( (float) $price * ( 100 - $d ) / 100, 2 );
}

/**
 * معرّفات أقسام القالب.
 *
 * @return array slug => term_id
 */
function zad_catalog_cat_ids() {
	static $ids = null;
	if ( null === $ids ) {
		$ids = array();
		foreach ( array_keys( zad_categories() ) as $slug ) {
			$t            = get_term_by( 'slug', $slug, 'product_cat' );
			$ids[ $slug ] = $t ? (int) $t->term_id : 0;
		}
	}
	return $ids;
}

/**
 * إضافة صف من الكتالوج أو تحديث منتجه (حسب SKU).
 *
 * عند التحديث: يُحدَّث السعر والاسم والأقسام والبيانات، ولا تُمس الصورة ولا الوصف إن عدّلته.
 *
 * @param array $r   الصف.
 * @param int   $pos ترتيبه.
 * @return string created|updated|error
 */
function zad_catalog_upsert( $r, $pos ) {
	$id      = wc_get_product_id_by_sku( $r['sku'] );
	$product = $id ? wc_get_product( $id ) : null;
	$is_new  = ! $product;
	if ( $is_new ) {
		$product = new WC_Product_Simple();
		$product->set_sku( $r['sku'] );
		$product->set_slug( sanitize_title( str_replace( '%', '', $r['tr'] ) ) );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_description( zad_seed_description( $r ) );
		// الكتالوج الأولي ليس «وصل حديثاً»: تاريخ سابق، حتى تظهر الأصناف التي تضيفها لاحقاً كجديدة.
		$product->set_date_created( time() - 120 * DAY_IN_SECONDS + $pos * MINUTE_IN_SECONDS );
		$product->update_meta_data( '_zad_seeded', 1 );
	}
	$product->set_name( $r['name'] );
	$product->set_regular_price( (string) $r['price'] );
	$product->set_sale_price( zad_discounted_price( $r['price'], $r['brand'] ) );
	$product->set_featured( (bool) $r['best'] );
	// بلا إدارة مخزون: لا تظهر أي كميات للزبائن.
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );
	$product->set_menu_order( $pos );
	$product->set_short_description( '<p>' . esc_html( $r['desc'] ) . '.' . ( $r['pack'] ? ' التعبئة: ' . esc_html( $r['pack'] ) . '.' : '' ) . '</p>' );

	$ids  = zad_catalog_cat_ids();
	$cats = array();
	if ( ! empty( $ids[ $r['cat'] ] ) ) {
		$cats[] = $ids[ $r['cat'] ];
	}
	if ( '' !== $product->get_sale_price( 'edit' ) && ! empty( $ids['offers'] ) ) {
		$cats[] = $ids['offers'];
	}
	$product->set_category_ids( array_values( array_unique( $cats ) ) );

	$product->update_meta_data( '_zad_brand', $r['brand'] );
	$product->update_meta_data( '_zad_line', $r['line'] );
	$product->update_meta_data( '_zad_tr', $r['tr'] );
	$product->update_meta_data( '_zad_flavor', $r['flavor'] );
	$product->update_meta_data( '_zad_pack', $r['pack'] );
	$product->update_meta_data( '_zad_units', (int) $r['units'] );
	$product->update_meta_data( '_zad_unit', $r['unit'] ? $r['unit'] : 'علبة' );
	$product->delete_meta_data( '_zad_bundle' );
	$new_id = $product->save();
	if ( ! $new_id ) {
		return 'error';
	}
	if ( taxonomy_exists( 'product_brand' ) ) {
		$bt = $r['brand'] ? get_term_by( 'slug', $r['brand'], 'product_brand' ) : null;
		wp_set_object_terms( $new_id, $bt ? array( (int) $bt->term_id ) : array(), 'product_brand' );
	}
	return $is_new ? 'created' : 'updated';
}

/**
 * بدء مهمة الكتالوج.
 *
 * @param bool $replace حذف كل منتج ليس في الكتالوج (المنتجات القديمة).
 */
function zad_catalog_job_start( $replace = false ) {
	update_option(
		'zad_catalog_job',
		array(
			'replace' => (bool) $replace,
			'phase'   => $replace ? 'delete' : 'upsert',
			'offset'  => 0,
			'deleted' => 0,
			'created' => 0,
			'updated' => 0,
			'total'   => count( zad_catalog_rows() ),
			'started' => time(),
		),
		false
	);
	if ( ! wp_next_scheduled( 'zad_catalog_cron' ) ) {
		wp_schedule_event( time() + 30, 'zad_minute', 'zad_catalog_cron' );
	}
}

/**
 * المهمة الحالية أو null.
 *
 * @return array|null
 */
function zad_catalog_job() {
	$job = get_option( 'zad_catalog_job' );
	return is_array( $job ) ? $job : null;
}

/**
 * تنفيذ جزء من المهمة ضمن مهلة.
 *
 * @param int $budget الثواني المتاحة.
 * @return array الحالة.
 */
function zad_catalog_job_step( $budget = 15 ) {
	$job = zad_catalog_job();
	if ( ! $job || ! class_exists( 'WooCommerce' ) ) {
		return array( 'done' => true );
	}
	// قفل بسيط حتى لا تعمل نسختان معاً (الصفحة والمهمة الخلفية).
	if ( get_transient( 'zad_catalog_lock' ) ) {
		return zad_catalog_job_status( $job );
	}
	set_transient( 'zad_catalog_lock', 1, $budget + 30 );
	$until = microtime( true ) + $budget;
	wp_defer_term_counting( true );

	if ( 'delete' === $job['phase'] ) {
		$keep = wp_list_pluck( zad_catalog_rows(), 'sku' );
		while ( microtime( true ) < $until ) {
			$ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
					'posts_per_page' => 25,
					'fields'         => 'ids',
					'orderby'        => 'ID',
					'order'          => 'ASC',
					// phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_query'     => array(
						'relation' => 'OR',
						array(
							'key'     => '_sku',
							'value'   => $keep,
							'compare' => 'NOT IN',
						),
						array(
							'key'     => '_sku',
							'compare' => 'NOT EXISTS',
						),
					),
				)
			);
			if ( ! $ids ) {
				$job['phase']  = 'upsert';
				$job['offset'] = 0;
				break;
			}
			foreach ( $ids as $pid ) {
				$p = wc_get_product( $pid );
				if ( $p ) {
					$p->delete( true );
				} else {
					wp_delete_post( $pid, true );
				}
				++$job['deleted'];
			}
		}
	}

	if ( 'upsert' === $job['phase'] ) {
		$rows = zad_catalog_rows();
		while ( microtime( true ) < $until && $job['offset'] < count( $rows ) ) {
			$res = zad_catalog_upsert( $rows[ $job['offset'] ], $job['offset'] );
			if ( isset( $job[ $res ] ) ) {
				++$job[ $res ];
			}
			++$job['offset'];
		}
		if ( $job['offset'] >= count( $rows ) ) {
			$job['phase'] = 'done';
		}
	}

	wp_defer_term_counting( false );
	delete_transient( 'zad_catalog_lock' );

	if ( 'done' === $job['phase'] ) {
		delete_option( 'zad_catalog_job' );
		wp_clear_scheduled_hook( 'zad_catalog_cron' );
		update_option( 'zad_catalog_version', zad_catalog_version() );
		update_option(
			'zad_catalog_report',
			sprintf( 'الكتالوج محدَّث: أُضيف %1$d، وحُدّث %2$d، وحُذف %3$d منتجاً قديماً.', $job['created'], $job['updated'], $job['deleted'] ),
			false
		);
		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}
		zad_cache_flush();
		do_action( 'zad_catalog_done' );
		return array(
			'done'    => true,
			'percent' => 100,
			'message' => get_option( 'zad_catalog_report' ),
		);
	}
	update_option( 'zad_catalog_job', $job, false );
	return zad_catalog_job_status( $job );
}

/**
 * وصف حالة المهمة.
 *
 * @param array $job المهمة.
 * @return array
 */
function zad_catalog_job_status( $job ) {
	$total = max( 1, (int) $job['total'] );
	if ( 'delete' === $job['phase'] ) {
		return array(
			'done'    => false,
			'percent' => 5,
			'message' => sprintf( 'حذف المنتجات القديمة… (%d حتى الآن)', $job['deleted'] ),
		);
	}
	return array(
		'done'    => false,
		'percent' => (int) min( 99, 10 + 90 * $job['offset'] / $total ),
		'message' => sprintf( 'إضافة الأصناف وتحديثها: %1$d من %2$d', $job['offset'], $total ),
	);
}

/**
 * جدول «كل دقيقة» للمهام الخلفية.
 *
 * @param array $s الجداول.
 * @return array
 */
function zad_cron_minute( $s ) {
	$s['zad_minute'] = array(
		'interval' => MINUTE_IN_SECONDS,
		'display'  => 'كل دقيقة (بسكاتو)',
	);
	return $s;
}
add_filter( 'cron_schedules', 'zad_cron_minute' );

add_action(
	'zad_catalog_cron',
	static function () {
		zad_catalog_job_step( 20 );
	}
);

add_action(
	'wp_ajax_zad_catalog_step',
	static function () {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( null, 403 );
		}
		check_ajax_referer( 'zad_catalog', 'nonce' );
		wp_send_json_success( zad_catalog_job_step( 12 ) );
	}
);

/**
 * إشعار التقدّم في لوحة التحكم (يكمل العمل تلقائياً ما دامت الصفحة مفتوحة).
 */
function zad_catalog_notice() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$job    = zad_catalog_job();
	$report = get_option( 'zad_catalog_report' );
	if ( ! $job ) {
		if ( $report ) {
			delete_option( 'zad_catalog_report' );
			printf( '<div class="notice notice-success is-dismissible" dir="rtl"><p><strong>بسكاتو:</strong> %s</p></div>', esc_html( $report ) );
		}
		return;
	}
	$st = zad_catalog_job_status( $job );
	?>
	<div class="notice notice-info" dir="rtl" id="zad-catalog-job">
		<p><strong>بسكاتو — تحديث الكتالوج من ملف الجرد (<?php echo (int) $job['total']; ?> صنفاً)</strong></p>
		<p><progress max="100" value="<?php echo (int) $st['percent']; ?>" style="width:100%;max-width:520px"></progress></p>
		<p data-msg><?php echo esc_html( $st['message'] ); ?> — يكمل تلقائياً، ويمكنك إغلاق الصفحة وسيستمر في الخلفية.</p>
	</div>
	<script>
	( function () {
		var box = document.getElementById( 'zad-catalog-job' ), bar = box.querySelector( 'progress' ), msg = box.querySelector( '[data-msg]' );
		var body = new FormData(); body.append( 'action', 'zad_catalog_step' ); body.append( 'nonce', <?php echo wp_json_encode( wp_create_nonce( 'zad_catalog' ) ); ?> );
		function step() {
			fetch( ajaxurl, { method: 'POST', body: body, credentials: 'same-origin' } ).then( function ( r ) { return r.json(); } ).then( function ( r ) {
				if ( ! r || ! r.success ) { msg.textContent = 'توقف التحديث مؤقتاً، وسيكمل في الخلفية.'; return; }
				bar.value = r.data.percent || 0; msg.textContent = r.data.message || '';
				if ( r.data.done ) { box.className = 'notice notice-success'; return; }
				setTimeout( step, 400 );
			} ).catch( function () { setTimeout( step, 4000 ); } );
		}
		step();
	} )();
	</script>
	<?php
}
add_action( 'admin_notices', 'zad_catalog_notice', 5 );

/**
 * إعادة تطبيق خصومات العلامات على كل منتجاتها (بعد تغيير النسبة من المخصّص).
 *
 * @return int عدد المنتجات المحدّثة.
 */
function zad_reapply_brand_discounts() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return 0;
	}
	$ids  = zad_catalog_cat_ids();
	$done = 0;
	foreach ( array( 'eti', 'ulker' ) as $brand ) {
		$products = wc_get_products(
			array(
				'limit'      => -1,
				'status'     => array( 'publish', 'draft', 'private' ),
				'meta_key'   => '_zad_brand', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value' => $brand, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		foreach ( $products as $p ) {
			$sale = zad_discounted_price( $p->get_regular_price( 'edit' ), $brand );
			if ( (string) $p->get_sale_price( 'edit' ) === $sale ) {
				continue;
			}
			$p->set_sale_price( $sale );
			$cats = array_diff( $p->get_category_ids(), array( $ids['offers'] ) );
			if ( '' !== $sale && $ids['offers'] ) {
				$cats[] = $ids['offers'];
			}
			$p->set_category_ids( array_values( array_unique( $cats ) ) );
			$p->save();
			++$done;
		}
	}
	if ( $done ) {
		zad_cache_flush();
	}
	return $done;
}

add_action(
	'customize_save_after',
	static function ( $manager ) {
		foreach ( array( 'zad_disc_eti', 'zad_disc_ulker' ) as $key ) {
			$setting = $manager->get_setting( $key );
			if ( $setting && null !== $manager->post_value( $setting ) ) {
				zad_reapply_brand_discounts();
				return;
			}
		}
	}
);

/**
 * تحديث تلقائي مرة واحدة عند تغيّر ملف الكتالوج على موقع أُعدّ سابقاً:
 * يحذف المنتجات القديمة كلها ويضيف أصناف الملف (بطلب صاحب المتجر).
 */
function zad_catalog_maybe_migrate() {
	if ( ! class_exists( 'WooCommerce' ) || ! get_option( 'zad_seeded' ) || zad_catalog_job() ) {
		return;
	}
	if ( get_option( 'zad_catalog_version' ) === zad_catalog_version() ) {
		return;
	}
	if ( ! get_option( 'zad_catalog_replaced_v5' ) ) {
		update_option( 'zad_catalog_replaced_v5', time(), false );
		zad_seed_terms( true );
		zad_catalog_job_start( true );
		return;
	}
	zad_catalog_job_start( false );
}
add_action( 'admin_init', 'zad_catalog_maybe_migrate' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'zad catalog',
		static function ( $args, $assoc ) {
			if ( ! zad_catalog_job() ) {
				zad_seed_terms();
				zad_catalog_job_start( ! empty( $assoc['replace'] ) );
			}
			do {
				$st = zad_catalog_job_step( 60 );
				WP_CLI::log( isset( $st['message'] ) ? $st['message'] : '…' );
			} while ( empty( $st['done'] ) );
			WP_CLI::success( 'تم.' );
		},
		array( 'shortdesc' => 'إضافة أصناف الكتالوج وتحديثها (--replace لحذف كل ما سواها).' )
	);
}
