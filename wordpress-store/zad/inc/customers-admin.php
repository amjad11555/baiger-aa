<?php
/**
 * لوحة «الزبائن» في لوحة التحكم.
 *
 * - أرقام سريعة: عدد الزبائن، الجدد، من طلب خلال 30 يوماً، من لم يطلب بعد.
 * - توزيع الزبائن على المناطق، وخريطة بمواقع المحلات.
 * - جدول قابل للبحث والتصفية مع زر واتساب وموقع كل محل، وتصدير CSV.
 * - ملخّص في الصفحة الرئيسية للوحة التحكم، وأعمدة في قائمة المستخدمين، وحقول المحل في صفحة المستخدم.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * بيانات كل الزبائن (مخزّنة مؤقتاً 10 دقائق وتُحدَّث عند أي تسجيل أو طلب).
 *
 * @return array
 */
function zad_customers_dataset() {
	$cached = get_transient( 'zad_customers_dataset' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$ids  = get_users(
		array(
			'role__in' => array( 'customer' ),
			'fields'   => 'ID',
			'orderby'  => 'registered',
			'order'    => 'DESC',
			'number'   => 5000,
		)
	);
	$rows = array();
	foreach ( $ids as $id ) {
		$p    = zad_customer_profile( $id );
		$user = get_userdata( $id );
		$last = wc_get_customer_last_order( $id );
		if ( ! $p['shop'] && ! $p['name'] ) {
			$p['name'] = $user->display_name;
		}
		$rows[] = array(
			'id'       => (int) $id,
			'name'     => $p['name'],
			'shop'     => $p['shop'],
			'wa'       => $p['wa'],
			'district' => $p['district'],
			'city'     => $p['city'],
			'address'  => $p['address'],
			'lat'      => $p['lat'],
			'lng'      => $p['lng'],
			'email'    => $user->user_email,
			'reg'      => strtotime( $user->user_registered . ' UTC' ),
			'orders'   => (int) wc_get_customer_order_count( $id ),
			'spent'    => (float) wc_get_customer_total_spent( $id ),
			'last'     => $last && $last->get_date_created() ? $last->get_date_created()->getTimestamp() : 0,
		);
	}
	set_transient( 'zad_customers_dataset', $rows, 10 * MINUTE_IN_SECONDS );
	return $rows;
}

/**
 * الأرقام المجمّعة.
 *
 * @param array $rows الزبائن.
 * @return array
 */
function zad_customers_stats( $rows ) {
	$month     = time() - 30 * DAY_IN_SECONDS;
	$districts = array();
	$stats     = array(
		'total'      => count( $rows ),
		'new30'      => 0,
		'active30'   => 0,
		'never'      => 0,
		'nopin'      => 0,
		'districts'  => array(),
		'top'        => '',
		'district_n' => 0,
	);
	foreach ( $rows as $r ) {
		if ( $r['reg'] >= $month ) {
			++$stats['new30'];
		}
		if ( $r['last'] >= $month ) {
			++$stats['active30'];
		}
		if ( ! $r['orders'] ) {
			++$stats['never'];
		}
		if ( null === $r['lat'] ) {
			++$stats['nopin'];
		}
		$key               = $r['district'] ? $r['district'] : 'none';
		$districts[ $key ] = isset( $districts[ $key ] ) ? $districts[ $key ] + 1 : 1;
	}
	arsort( $districts );
	$stats['districts']  = $districts;
	$stats['district_n'] = count( array_diff_key( $districts, array( 'none' => 1 ) ) );
	foreach ( $districts as $slug => $n ) {
		if ( 'none' !== $slug && 'other' !== $slug ) {
			$stats['top'] = zad_district_label( $slug );
			break;
		}
	}
	return $stats;
}

/**
 * رابط طلبيات زبون في لوحة ووكومرس (يدعم جدول الطلبات الجديد والقديم).
 *
 * @param int $user_id رقم المستخدم.
 * @return string
 */
function zad_customer_orders_admin_url( $user_id ) {
	$hpos = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	return $hpos
		? admin_url( 'admin.php?page=wc-orders&_customer_user=' . (int) $user_id )
		: admin_url( 'edit.php?post_type=shop_order&_customer_user=' . (int) $user_id );
}

/**
 * «منذ X» بالعربية.
 *
 * @param int $ts الوقت.
 * @return string
 */
function zad_ago( $ts ) {
	if ( ! $ts ) {
		return '';
	}
	$days = (int) floor( ( time() - $ts ) / DAY_IN_SECONDS );
	if ( $days < 1 ) {
		return 'اليوم';
	}
	if ( 1 === $days ) {
		return 'أمس';
	}
	if ( $days < 11 ) {
		return 'منذ ' . $days . ' أيام';
	}
	if ( $days < 60 ) {
		return 'منذ ' . $days . ' يوماً';
	}
	return wp_date( 'Y/m/d', $ts );
}

/**
 * القائمة في لوحة التحكم.
 */
function zad_customers_menu() {
	$hook = add_menu_page( 'الزبائن', 'الزبائن', 'manage_woocommerce', 'zad-customers', 'zad_customers_page', 'dashicons-groups', 56 );
	add_action( 'admin_print_styles-' . $hook, 'zad_customers_admin_assets' );
}
add_action( 'admin_menu', 'zad_customers_menu' );

/**
 * أنماط وسكربت اللوحة (صفحتها فقط).
 */
function zad_customers_admin_assets() {
	wp_enqueue_style( 'zad-leaflet', ZAD_URI . '/assets/vendor/leaflet/leaflet.css', array(), '1.9.4' );
	wp_enqueue_style( 'zad-customers-admin', ZAD_URI . '/assets/css/admin-customers.css', array(), ZAD_VERSION );
	wp_enqueue_script( 'zad-leaflet', ZAD_URI . '/assets/vendor/leaflet/leaflet.js', array(), '1.9.4', true );
	wp_enqueue_script( 'zad-customers-admin', ZAD_URI . '/assets/js/customers-admin.js', array( 'zad-leaflet' ), ZAD_VERSION, true );
}

/**
 * صفحة «الزبائن».
 */
function zad_customers_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$rows  = zad_customers_dataset();
	$stats = zad_customers_stats( $rows );
	$max   = $stats['districts'] ? max( $stats['districts'] ) : 1;
	$pins  = array();
	foreach ( $rows as $r ) {
		if ( null === $r['lat'] ) {
			continue;
		}
		$pins[] = array(
			'id'  => $r['id'],
			'lat' => $r['lat'],
			'lng' => $r['lng'],
			's'   => $r['shop'] ? $r['shop'] : $r['name'],
			'n'   => $r['name'],
			'd'   => 'other' === $r['district'] ? $r['city'] : zad_district_label( $r['district'] ),
			'w'   => $r['wa'] ? zad_customer_wa_link( $r['wa'] ) : '',
			'o'   => $r['orders'],
		);
	}
	$csv = wp_nonce_url( admin_url( 'admin-post.php?action=zad_customers_csv' ), 'zad_customers_csv' );
	?>
	<div class="wrap zd-cus" dir="rtl">
		<div class="zd-cus__head">
			<h1 class="wp-heading-inline">الزبائن</h1>
			<a class="page-title-action" href="<?php echo esc_url( $csv ); ?>">تصدير Excel (CSV)</a>
			<p class="zd-cus__lead">كل محل سجّل في الموقع: اسمه، ورقم الواتساب، ومنطقته، وموقعه على الخريطة، وطلبياته.</p>
		</div>
		<hr class="wp-header-end">

		<div class="zd-cus__kpis">
			<div class="zd-kpi">
				<p class="zd-kpi__label">عدد الزبائن</p>
				<p class="zd-kpi__value"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></p>
				<p class="zd-kpi__sub"><?php echo esc_html( $stats['new30'] ? 'سجّل منهم ' . number_format_i18n( $stats['new30'] ) . ' خلال 30 يوماً' : 'لا تسجيلات جديدة خلال 30 يوماً' ); ?></p>
			</div>
			<div class="zd-kpi">
				<p class="zd-kpi__label">طلبوا خلال 30 يوماً</p>
				<p class="zd-kpi__value"><?php echo esc_html( number_format_i18n( $stats['active30'] ) ); ?></p>
				<p class="zd-kpi__sub"><?php echo esc_html( $stats['total'] ? 'من أصل ' . number_format_i18n( $stats['total'] ) . ' زبون' : '—' ); ?></p>
			</div>
			<div class="zd-kpi">
				<p class="zd-kpi__label">المناطق</p>
				<p class="zd-kpi__value"><?php echo esc_html( number_format_i18n( $stats['district_n'] ) ); ?></p>
				<p class="zd-kpi__sub"><?php echo esc_html( $stats['top'] ? 'أكثرها زبائن: ' . $stats['top'] : '—' ); ?></p>
			</div>
			<div class="zd-kpi">
				<p class="zd-kpi__label">لم يطلبوا بعد</p>
				<p class="zd-kpi__value"><?php echo esc_html( number_format_i18n( $stats['never'] ) ); ?></p>
				<p class="zd-kpi__sub"><?php echo $stats['never'] ? '<button type="button" class="button-link" data-zd-quick="never">اعرضهم وراسلهم لأول طلبية</button>' : 'كل الزبائن طلبوا مرة على الأقل'; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</div>
		</div>

		<?php if ( ! $rows ) : ?>
			<div class="zd-cus__empty">
				<h2>لا يوجد زبائن مسجلون بعد</h2>
				<p>عندما يسجّل صاحب محل من صفحة «حسابي» في الموقع، يظهر هنا مع رقم واتسابه وموقع محله.</p>
				<p><a class="button button-primary" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" target="_blank" rel="noopener">افتح صفحة التسجيل</a></p>
			</div>
		<?php else : ?>
			<div class="zd-cus__grid">
				<section class="zd-card zd-cus__map-card" aria-labelledby="zd-cus-map-title">
					<div class="zd-card__head">
						<h2 id="zd-cus-map-title">مواقع المحلات</h2>
						<span class="zd-card__meta"><?php echo esc_html( number_format_i18n( count( $pins ) ) . ' محل على الخريطة' . ( $stats['nopin'] ? ' · ' . number_format_i18n( $stats['nopin'] ) . ' بلا موقع' : '' ) ); ?></span>
					</div>
					<div class="zd-cus__map" data-zd-cus-map dir="ltr" role="region" aria-label="خريطة مواقع المحلات"></div>
				</section>
				<section class="zd-card zd-cus__areas" aria-labelledby="zd-cus-areas-title">
					<div class="zd-card__head">
						<h2 id="zd-cus-areas-title">الزبائن حسب المنطقة</h2>
						<span class="zd-card__meta">اضغط منطقة لعرض زبائنها</span>
					</div>
					<ol class="zd-bars">
						<?php
						foreach ( $stats['districts'] as $slug => $n ) :
							$label = 'none' === $slug ? 'بلا منطقة' : zad_district_label( $slug );
							$pct   = $stats['total'] ? round( $n / $stats['total'] * 100 ) : 0;
							?>
							<li>
								<button type="button" class="zd-bars__row" data-zd-area="<?php echo esc_attr( $slug ); ?>" title="<?php echo esc_attr( $label . ': ' . $n . ' زبون (' . $pct . '%)' ); ?>">
									<span class="zd-bars__label"><?php echo esc_html( $label ); ?></span>
									<span class="zd-bars__track"><span class="zd-bars__fill" style="width:<?php echo esc_attr( max( 2, round( $n / $max * 82 ) ) ); ?>%"></span><span class="zd-bars__value"><?php echo esc_html( number_format_i18n( $n ) ); ?></span></span>
								</button>
							</li>
						<?php endforeach; ?>
					</ol>
				</section>
			</div>

			<section class="zd-card zd-cus__list" aria-labelledby="zd-cus-list-title">
				<div class="zd-card__head">
					<h2 id="zd-cus-list-title">قائمة الزبائن</h2>
					<span class="zd-card__meta" data-zd-count aria-live="polite"><?php echo esc_html( number_format_i18n( $stats['total'] ) . ' زبون' ); ?></span>
				</div>
				<div class="zd-cus__filters">
					<label class="screen-reader-text" for="zd-cus-q">بحث</label>
					<input type="search" id="zd-cus-q" placeholder="ابحث بالاسم أو المحل أو الرقم أو العنوان" data-zd-q>
					<label class="screen-reader-text" for="zd-cus-area">المنطقة</label>
					<select id="zd-cus-area" data-zd-area-select>
						<option value="">كل المناطق</option>
						<?php foreach ( $stats['districts'] as $slug => $n ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( ( 'none' === $slug ? 'بلا منطقة' : zad_district_label( $slug ) ) . ' (' . $n . ')' ); ?></option>
						<?php endforeach; ?>
					</select>
					<label class="screen-reader-text" for="zd-cus-status">الحالة</label>
					<select id="zd-cus-status" data-zd-status>
						<option value="">كل الزبائن</option>
						<option value="active">طلبوا خلال 30 يوماً</option>
						<option value="never">لم يطلبوا بعد</option>
						<option value="new">سجّلوا خلال 30 يوماً</option>
						<option value="nopin">بلا موقع على الخريطة</option>
					</select>
					<label class="screen-reader-text" for="zd-cus-sort">الترتيب</label>
					<select id="zd-cus-sort" data-zd-sort>
						<option value="reg">الأحدث تسجيلاً</option>
						<option value="last">آخر طلبية</option>
						<option value="orders">الأكثر طلبيات</option>
						<option value="spent">الأعلى مشتريات</option>
					</select>
				</div>
				<div class="zd-cus__table-wrap">
					<table class="widefat striped zd-cus__table">
						<thead>
							<tr>
								<th scope="col">المحل</th>
								<th scope="col">واتساب</th>
								<th scope="col">المنطقة والعنوان</th>
								<th scope="col">الطلبيات</th>
								<th scope="col">آخر طلبية</th>
								<th scope="col">التسجيل</th>
								<th scope="col"><span class="screen-reader-text">إجراءات</span></th>
							</tr>
						</thead>
						<tbody data-zd-rows>
							<?php
							$month = time() - 30 * DAY_IN_SECONDS;
							foreach ( $rows as $r ) :
								$place  = 'other' === $r['district'] ? $r['city'] : zad_district_label( $r['district'], true );
								$status = array();
								if ( $r['last'] >= $month ) {
									$status[] = 'active';
								}
								if ( ! $r['orders'] ) {
									$status[] = 'never';
								}
								if ( $r['reg'] >= $month ) {
									$status[] = 'new';
								}
								if ( null === $r['lat'] ) {
									$status[] = 'nopin';
								}
								$search = mb_strtolower( implode( ' ', array( $r['shop'], $r['name'], $r['wa'], zad_format_phone( $r['wa'] ), $place, $r['address'], $r['email'] ) ) );
								$hello  = 'مرحباً ' . $r['name'] . '، معك ' . get_bloginfo( 'name' ) . '.';
								?>
								<tr data-area="<?php echo esc_attr( $r['district'] ? $r['district'] : 'none' ); ?>" data-status="<?php echo esc_attr( implode( ' ', $status ) ); ?>" data-search="<?php echo esc_attr( $search ); ?>" data-reg="<?php echo (int) $r['reg']; ?>" data-last="<?php echo (int) $r['last']; ?>" data-orders="<?php echo (int) $r['orders']; ?>" data-spent="<?php echo esc_attr( $r['spent'] ); ?>" data-id="<?php echo (int) $r['id']; ?>">
									<td class="zd-cus__who">
										<strong><?php echo esc_html( $r['shop'] ? $r['shop'] : '—' ); ?></strong>
										<span><?php echo esc_html( $r['name'] ); ?></span>
									</td>
									<td>
										<?php if ( $r['wa'] ) : ?>
											<a class="zd-cus__wa" href="<?php echo esc_url( zad_customer_wa_link( $r['wa'], $hello ) ); ?>" target="_blank" rel="noopener" dir="ltr"><?php echo esc_html( zad_format_phone( $r['wa'] ) ); ?></a>
										<?php else : ?>
											—
										<?php endif; ?>
									</td>
									<td class="zd-cus__place">
										<strong><?php echo esc_html( $place ? $place : 'بلا منطقة' ); ?></strong>
										<span><?php echo esc_html( $r['address'] ); ?></span>
										<?php if ( null !== $r['lat'] ) : ?>
											<a href="<?php echo esc_url( zad_map_link( $r['lat'], $r['lng'] ) ); ?>" target="_blank" rel="noopener">فتح في خرائط Google</a>
											· <button type="button" class="button-link" data-zd-focus="<?php echo (int) $r['id']; ?>">على الخريطة هنا</button>
										<?php else : ?>
											<em class="zd-cus__warn">بلا موقع</em>
										<?php endif; ?>
									</td>
									<td>
										<strong><?php echo esc_html( number_format_i18n( $r['orders'] ) ); ?></strong>
										<?php if ( $r['orders'] ) : ?>
											<span class="zd-cus__muted"><?php echo wp_kses_post( wc_price( $r['spent'] ) ); ?></span>
										<?php endif; ?>
									</td>
									<td><bdi><?php echo esc_html( $r['last'] ? zad_ago( $r['last'] ) : 'لم يطلب بعد' ); ?></bdi></td>
									<td><bdi><?php echo esc_html( zad_ago( $r['reg'] ) ); ?></bdi></td>
									<td class="zd-cus__actions">
										<?php if ( $r['orders'] ) : ?>
											<a class="button button-small" href="<?php echo esc_url( zad_customer_orders_admin_url( $r['id'] ) ); ?>">الطلبيات</a>
										<?php endif; ?>
										<a class="button button-small" href="<?php echo esc_url( get_edit_user_link( $r['id'] ) ); ?>">تعديل</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<p class="zd-cus__none" data-zd-none hidden>لا يوجد زبائن يطابقون البحث.</p>
				</div>
			</section>
			<script type="application/json" id="zd-cus-data"><?php echo wp_json_encode( array( 'pins' => $pins, 'tiles' => apply_filters( 'zad_map_tiles', 'https://tile.openstreetmap.org/{z}/{x}/{y}.png' ) ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?></script>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * تصدير الزبائن إلى CSV (يفتح في Excel بالعربية).
 */
function zad_customers_csv() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'غير مسموح.', 403 );
	}
	check_admin_referer( 'zad_customers_csv' );
	$rows = zad_customers_dataset();
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=zad-customers-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" );
	fputcsv( $out, array( 'المحل', 'الاسم', 'واتساب', 'رابط واتساب', 'المنطقة', 'المدينة', 'العنوان', 'خط العرض', 'خط الطول', 'رابط الخريطة', 'البريد', 'تاريخ التسجيل', 'عدد الطلبيات', 'إجمالي المشتريات', 'آخر طلبية' ), ',', '"', '\\' );
	foreach ( $rows as $r ) {
		fputcsv(
			$out,
			array(
				$r['shop'],
				$r['name'],
				zad_format_phone( $r['wa'] ),
				zad_customer_wa_link( $r['wa'] ),
				zad_district_label( $r['district'], true ),
				$r['city'],
				$r['address'],
				null !== $r['lat'] ? $r['lat'] : '',
				null !== $r['lng'] ? $r['lng'] : '',
				zad_map_link( $r['lat'], $r['lng'] ),
				$r['email'],
				wp_date( 'Y-m-d', $r['reg'] ),
				$r['orders'],
				$r['spent'],
				$r['last'] ? wp_date( 'Y-m-d', $r['last'] ) : '',
			),
			',',
			'"',
			'\\'
		);
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_zad_customers_csv', 'zad_customers_csv' );

/**
 * ملخّص في الصفحة الرئيسية للوحة التحكم.
 */
function zad_customers_dashboard_widget() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	wp_add_dashboard_widget(
		'zad_customers_widget',
		'زبائن زاد',
		static function () {
			$rows  = zad_customers_dataset();
			$stats = zad_customers_stats( $rows );
			printf(
				'<ul class="zd-dash"><li><b>%1$s</b><span>زبون مسجّل</span></li><li><b>%2$s</b><span>جديد خلال 30 يوماً</span></li><li><b>%3$s</b><span>طلبوا خلال 30 يوماً</span></li></ul>',
				esc_html( number_format_i18n( $stats['total'] ) ),
				esc_html( number_format_i18n( $stats['new30'] ) ),
				esc_html( number_format_i18n( $stats['active30'] ) )
			);
			$top = array_slice( array_diff_key( $stats['districts'], array( 'none' => 1 ) ), 0, 5, true );
			if ( $top ) {
				echo '<p class="zd-dash__title">أكثر المناطق زبائن</p><ol class="zd-dash__areas">';
				foreach ( $top as $slug => $n ) {
					printf( '<li><span>%1$s</span><b>%2$s</b></li>', esc_html( zad_district_label( $slug ) ), esc_html( number_format_i18n( $n ) ) );
				}
				echo '</ol>';
			}
			$recent = array_slice( $rows, 0, 3 );
			if ( $recent ) {
				echo '<p class="zd-dash__title">آخر من سجّل</p><ul class="zd-dash__recent">';
				foreach ( $recent as $r ) {
					printf(
						'<li><b>%1$s</b> · %2$s · %3$s%4$s</li>',
						esc_html( $r['shop'] ? $r['shop'] : $r['name'] ),
						esc_html( 'other' === $r['district'] ? $r['city'] : zad_district_label( $r['district'] ) ),
						esc_html( zad_ago( $r['reg'] ) ),
						$r['wa'] ? ' · <a href="' . esc_url( zad_customer_wa_link( $r['wa'] ) ) . '" target="_blank" rel="noopener">واتساب</a>' : ''
					);
				}
				echo '</ul>';
			}
			printf( '<p><a class="button button-primary" href="%s">كل الزبائن والخريطة</a></p>', esc_url( admin_url( 'admin.php?page=zad-customers' ) ) );
			echo '<style>.zd-dash{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:0 0 12px}.zd-dash li{margin:0;padding:10px;border:1px solid #dcdcde;border-radius:6px;text-align:center}.zd-dash b{display:block;font-size:22px;line-height:1.3}.zd-dash span{font-size:12px;color:#50575e}.zd-dash__title{margin:12px 0 6px;font-weight:600}.zd-dash__areas{margin:0;padding:0;list-style:none}.zd-dash__areas li{display:flex;justify-content:space-between;margin:0;padding:4px 0;border-bottom:1px solid #f0f0f1}.zd-dash__recent{margin:0}</style>';
		}
	);
}
add_action( 'wp_dashboard_setup', 'zad_customers_dashboard_widget' );

/* -------------------------------------------------------------------------
 * قائمة المستخدمين وصفحة المستخدم
 * ---------------------------------------------------------------------- */

/**
 * أعمدة المحل في قائمة المستخدمين.
 *
 * @param array $cols الأعمدة.
 * @return array
 */
function zad_users_columns( $cols ) {
	$out = array();
	foreach ( $cols as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'username' === $key ) {
			$out['zad_shop']     = 'المحل';
			$out['zad_wa']       = 'واتساب';
			$out['zad_district'] = 'المنطقة';
		}
	}
	return $out;
}
add_filter( 'manage_users_columns', 'zad_users_columns' );

/**
 * قيم أعمدة المحل.
 *
 * @param string $value   القيمة.
 * @param string $column  العمود.
 * @param int    $user_id المستخدم.
 * @return string
 */
function zad_users_column_value( $value, $column, $user_id ) {
	switch ( $column ) {
		case 'zad_shop':
			return esc_html( (string) get_user_meta( $user_id, 'billing_company', true ) );
		case 'zad_wa':
			$wa = (string) get_user_meta( $user_id, 'zad_wa', true );
			return $wa ? sprintf( '<a href="%1$s" target="_blank" rel="noopener" dir="ltr">%2$s</a>', esc_url( zad_customer_wa_link( $wa ) ), esc_html( zad_format_phone( $wa ) ) ) : '';
		case 'zad_district':
			$d = (string) get_user_meta( $user_id, 'zad_district', true );
			return esc_html( 'other' === $d ? get_user_meta( $user_id, 'billing_city', true ) : zad_district_label( $d ) );
	}
	return $value;
}
add_filter( 'manage_users_custom_column', 'zad_users_column_value', 10, 3 );

/**
 * حقول المحل في صفحة المستخدم (للإدارة).
 *
 * @param WP_User $user المستخدم.
 */
function zad_user_profile_fields( $user ) {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$p = zad_customer_profile( $user->ID );
	?>
	<h2 id="zad-shop">بيانات المحل (زاد)</h2>
	<p>الاسم واسم المحل والعنوان في قسم «عنوان الفاتورة» أعلاه. هنا رقم الدخول والمنطقة والموقع.</p>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="zad_wa">رقم الواتساب (للدخول)</label></th>
			<td><input type="tel" name="zad_wa" id="zad_wa" value="<?php echo esc_attr( $p['wa'] ? zad_format_phone( $p['wa'] ) : '' ); ?>" class="regular-text" dir="ltr">
				<?php if ( $p['wa'] ) : ?>
					<a href="<?php echo esc_url( zad_customer_wa_link( $p['wa'] ) ); ?>" target="_blank" rel="noopener">مراسلة</a>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><label for="zad_district">المنطقة</label></th>
			<td><select name="zad_district" id="zad_district">
				<option value="">—</option>
				<?php foreach ( zad_districts() as $slug => $d ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $p['district'], $slug ); ?>><?php echo esc_html( $d[1] ? $d[0] . ' · ' . $d[1] : $d[0] ); ?></option>
				<?php endforeach; ?>
			</select></td>
		</tr>
		<tr>
			<th><label for="zad_lat">الموقع (خط العرض، خط الطول)</label></th>
			<td>
				<input type="text" name="zad_lat" id="zad_lat" value="<?php echo esc_attr( null !== $p['lat'] ? $p['lat'] : '' ); ?>" class="small-text" dir="ltr" style="width:9em" aria-label="خط العرض">
				<input type="text" name="zad_lng" id="zad_lng" value="<?php echo esc_attr( null !== $p['lng'] ? $p['lng'] : '' ); ?>" class="small-text" dir="ltr" style="width:9em" aria-label="خط الطول">
				<?php if ( null !== $p['lat'] ) : ?>
					<a href="<?php echo esc_url( zad_map_link( $p['lat'], $p['lng'] ) ); ?>" target="_blank" rel="noopener">فتح في خرائط Google</a>
				<?php endif; ?>
				<p class="description">يمكنك لصق الإحداثيات من خرائط Google (اضغط مطولاً على المكان ثم انسخ الرقمين).</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'zad_user_profile_fields', 20 );
add_action( 'edit_user_profile', 'zad_user_profile_fields', 20 );

/**
 * حفظ حقول المحل من صفحة المستخدم.
 *
 * @param int $user_id المستخدم.
 */
function zad_user_profile_save( $user_id ) {
	// ووردبريس تحقق من nonce «update-user_{id}» قبل هذا الخطاف.
	if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( isset( $_POST['zad_wa'] ) ) {
		$wa = zad_normalize_phone( sanitize_text_field( wp_unslash( $_POST['zad_wa'] ) ) );
		if ( $wa && ! zad_find_customer_by_phone( $wa, $user_id ) ) {
			update_user_meta( $user_id, 'zad_wa', $wa );
		}
	}
	if ( isset( $_POST['zad_district'] ) ) {
		$d = sanitize_key( wp_unslash( $_POST['zad_district'] ) );
		if ( '' === $d || array_key_exists( $d, zad_districts() ) ) {
			update_user_meta( $user_id, 'zad_district', $d );
		}
	}
	if ( isset( $_POST['zad_lat'], $_POST['zad_lng'] ) ) {
		$lat = str_replace( ',', '.', sanitize_text_field( wp_unslash( $_POST['zad_lat'] ) ) );
		$lng = str_replace( ',', '.', sanitize_text_field( wp_unslash( $_POST['zad_lng'] ) ) );
		if ( is_numeric( $lat ) && is_numeric( $lng ) && abs( (float) $lat ) <= 90 && abs( (float) $lng ) <= 180 ) {
			update_user_meta( $user_id, 'zad_lat', number_format( (float) $lat, 6, '.', '' ) );
			update_user_meta( $user_id, 'zad_lng', number_format( (float) $lng, 6, '.', '' ) );
		} elseif ( '' === $lat && '' === $lng ) {
			delete_user_meta( $user_id, 'zad_lat' );
			delete_user_meta( $user_id, 'zad_lng' );
		}
	}
	// phpcs:enable
	zad_customers_flush();
}
add_action( 'personal_options_update', 'zad_user_profile_save' );
add_action( 'edit_user_profile_update', 'zad_user_profile_save' );
