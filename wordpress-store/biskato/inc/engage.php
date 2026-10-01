<?php
/**
 * إبقاء الزبائن على اطلاع: الأصناف الجديدة، والعرض الحالي، ورسالة «نلبي كل احتياجات محلك».
 *
 * - الأصناف الجديدة: شارة «جديد»، وقسم «وصل حديثاً»، وصفحة الأصناف الجديدة، وفلتر في قائمة الأسعار،
 *   وإشعار في الجرس ورسالة منبثقة للزائر العائد عند وصول أصناف منذ زيارته الأخيرة.
 * - العرض الحالي: عنوان ونص ومدة، وخصم تلقائي اختياري (مبلغ ثابت أو نسبة) على كل الأصناف أو أقسام محددة،
 *   ونافذة تظهر مرة واحدة عند أول دخول، وسطر في شريط الإعلان.
 * - رسالة لأصحاب المحلات مع زر للتواصل، في الرئيسية و«حسابي» وصفحة الشكر ومركز الإشعارات.
 *
 * الإعدادات في «الزبائن ← العروض والإشعارات» (inc/engage-admin.php).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * الإعدادات
 * ---------------------------------------------------------------------- */

/**
 * القيم الافتراضية.
 *
 * @return array
 */
function zad_engage_defaults() {
	return array(
		'promo' => array(
			'enabled' => false,
			'title'   => 'عرض نهاية السنة',
			'text'    => 'خصم 10 ليرات على كل كرتونة من كل الأصناف حتى نهاية الشهر. اطلب الآن قبل انتهاء العرض.',
			'type'    => 'fixed',
			'amount'  => 10,
			'scope'   => 'all',
			'cats'    => array(),
			'start'   => '',
			'end'     => '',
			'button'  => 'تسوّق العرض',
			'link'    => 'quick_order',
			'url'     => '',
			'popup'   => true,
			'bar'     => true,
			'version' => 1,
		),
		'news'  => array(
			'days'   => 21,
			'notify' => true,
			'badge'  => true,
		),
		'pitch' => array(
			'enabled' => true,
			'title'   => 'كل احتياجات محلك من مورد واحد',
			'text'    => 'نستطيع تلبية كافة احتياجات محلكم من المواد الغذائية والمشروبات بأفضل الأسعار. تواصل معنا ونتفق على قائمة تناسب محلك وتوريد منتظم يصلك إلى الباب.',
			'button'  => 'تواصل معنا للاتفاق',
			'home'    => true,
			'account' => true,
			'thanks'  => true,
			'bell'    => true,
		),
	);
}

/**
 * قراءة مجموعة إعدادات.
 *
 * @param string $group promo|news|pitch.
 * @return array
 */
function zad_engage( $group ) {
	static $cache = array();
	if ( isset( $cache[ $group ] ) ) {
		return $cache[ $group ];
	}
	$defaults = zad_engage_defaults();
	$saved    = get_option( 'zad_engage_' . $group, array() );
	return $cache[ $group ] = wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults[ $group ] ); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
}

/**
 * تاريخ من حقل datetime-local (بتوقيت الموقع) إلى طابع زمني.
 *
 * @param string $value القيمة.
 * @return int 0 إن كان فارغاً.
 */
function zad_engage_ts( $value ) {
	if ( ! $value ) {
		return 0;
	}
	try {
		return ( new DateTimeImmutable( $value, wp_timezone() ) )->getTimestamp();
	} catch ( Exception $e ) {
		return 0;
	}
}

/* -------------------------------------------------------------------------
 * العرض الحالي
 * ---------------------------------------------------------------------- */

/**
 * العرض مع حالته.
 *
 * @return array يضيف: start_ts, end_ts, state (off|scheduled|active|ended), active, id, href.
 */
function zad_promo() {
	static $p = null;
	if ( null !== $p ) {
		return $p;
	}
	$p             = zad_engage( 'promo' );
	$p['start_ts'] = zad_engage_ts( $p['start'] );
	$p['end_ts']   = zad_engage_ts( $p['end'] );
	$now           = time();
	if ( ! $p['enabled'] || '' === trim( $p['title'] ) ) {
		$p['state'] = 'off';
	} elseif ( $p['start_ts'] && $now < $p['start_ts'] ) {
		$p['state'] = 'scheduled';
	} elseif ( $p['end_ts'] && $now > $p['end_ts'] ) {
		$p['state'] = 'ended';
	} else {
		$p['state'] = 'active';
	}
	$p['active'] = 'active' === $p['state'];
	$p['id']     = 'offer-' . (int) $p['version'];
	$targets     = array(
		'quick_order' => zad_page_url( 'quick_order' ),
		'shop'        => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
		'offers'      => zad_cat_url( 'offers' ),
	);
	$p['href']   = 'custom' === $p['link'] && $p['url'] ? $p['url'] : ( isset( $targets[ $p['link'] ] ) ? $targets[ $p['link'] ] : $targets['quick_order'] );
	return $p;
}

/**
 * هل يُطبَّق خصم تلقائي الآن؟
 *
 * @return bool
 */
function zad_promo_discounting() {
	$p = zad_promo();
	return $p['active'] && in_array( $p['type'], array( 'fixed', 'percent' ), true ) && (float) $p['amount'] > 0;
}

/**
 * وصف الخصم المختصر: «خصم 10 ₺ على كل كرتونة» أو «خصم 15%».
 *
 * @return string
 */
function zad_promo_discount_label() {
	$p = zad_promo();
	if ( ! zad_promo_discounting() ) {
		return '';
	}
	$amount = 'fixed' === $p['type'] ? zad_money_plain( (float) $p['amount'] ) : ( 0 + (float) $p['amount'] ) . '%';
	return 'خصم ' . $amount . ( 'fixed' === $p['type'] ? ' على كل كرتونة' : ' على السعر' );
}

/**
 * «ينتهي خلال 3 أيام» / «حتى 31 ديسمبر».
 *
 * @return string
 */
function zad_promo_ends_text() {
	$p = zad_promo();
	if ( ! $p['end_ts'] ) {
		return '';
	}
	$left = $p['end_ts'] - time();
	if ( $left <= 0 ) {
		return 'انتهى العرض';
	}
	if ( $left < DAY_IN_SECONDS ) {
		return 'ينتهي اليوم';
	}
	$days = (int) ceil( $left / DAY_IN_SECONDS );
	if ( $days <= 10 ) {
		return 1 === $days ? 'ينتهي غداً' : ( 2 === $days ? 'ينتهي خلال يومين' : 'ينتهي خلال ' . $days . ' أيام' );
	}
	return 'حتى ' . wp_date( 'j F', $p['end_ts'] );
}

/**
 * سعر الكرتونة بعد خصم العرض (أو null إن لم يشمله العرض).
 *
 * @param WC_Product $product المنتج.
 * @return float|null
 */
function zad_promo_price_for( $product ) {
	static $cache = array();
	if ( ! $product || ! zad_promo_discounting() || ! $product->is_type( 'simple' ) ) {
		return null;
	}
	$id = $product->get_id();
	if ( array_key_exists( $id, $cache ) ) {
		return $cache[ $id ];
	}
	$p = zad_promo();
	if ( 'cats' === $p['scope'] && ! array_intersect( array_map( 'intval', (array) $p['cats'] ), array_map( 'intval', $product->get_category_ids( 'edit' ) ) ) ) {
		return $cache[ $id ] = null; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
	}
	$regular = (float) $product->get_regular_price( 'edit' );
	$current = (float) $product->get_price( 'edit' );
	if ( $regular <= 0 ) {
		return $cache[ $id ] = null; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
	}
	$new = 'fixed' === $p['type'] ? $regular - (float) $p['amount'] : $regular * ( 1 - min( 90, (float) $p['amount'] ) / 100 );
	$new = round( $new, wc_get_price_decimals() );
	// لا يرفع العرض سعراً مخفّضاً أصلاً، ولا يُنزل الكرتونة إلى صفر.
	if ( $new <= 0 || ( $current > 0 && $new >= $current ) ) {
		return $cache[ $id ] = null; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
	}
	return $cache[ $id ] = $new; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
}

/**
 * تطبيق سعر العرض على السعر وسعر التخفيض (في الواجهة والطلبية فقط، لا يُحفظ في المنتج).
 *
 * @param string     $price   السعر.
 * @param WC_Product $product المنتج.
 * @return string
 */
function zad_promo_filter_price( $price, $product ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $price;
	}
	$new = zad_promo_price_for( $product );
	return null === $new ? $price : wc_format_decimal( $new );
}
add_filter( 'woocommerce_product_get_price', 'zad_promo_filter_price', 20, 2 );
add_filter( 'woocommerce_product_get_sale_price', 'zad_promo_filter_price', 20, 2 );

/* -------------------------------------------------------------------------
 * الأصناف الجديدة
 * ---------------------------------------------------------------------- */

/**
 * عدد الأيام التي يبقى فيها الصنف «جديداً».
 *
 * @return int
 */
function zad_new_days() {
	return max( 1, min( 180, (int) zad_engage( 'news' )['days'] ) );
}

/**
 * هل الصنف جديد؟ (أُضيف خلال المدة، وليس من الكتالوج الأولي).
 *
 * @param WC_Product $product المنتج.
 * @return bool
 */
function zad_is_new( $product ) {
	if ( ! $product || get_post_meta( $product->get_id(), '_zad_seeded', true ) ) {
		return false;
	}
	$created = $product->get_date_created();
	return $created && $created->getTimestamp() >= time() - zad_new_days() * DAY_IN_SECONDS;
}

/**
 * الأصناف الجديدة من الأحدث.
 *
 * @param int $limit الحد الأقصى.
 * @return int[]
 */
function zad_new_product_ids( $limit = 12 ) {
	static $cache = array();
	if ( isset( $cache[ $limit ] ) ) {
		return $cache[ $limit ];
	}
	$q   = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => array(
				'date' => 'DESC',
				'ID'   => 'DESC',
			),
			'date_query'     => array(
				array(
					'column'    => 'post_date_gmt',
					'after'     => gmdate( 'Y-m-d H:i:s', time() - zad_new_days() * DAY_IN_SECONDS ),
					'inclusive' => true,
				),
			),
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- استعلام صغير مخزّن لكل طلب.
			'meta_query'     => array(
				array(
					'key'     => '_zad_seeded',
					'compare' => 'NOT EXISTS',
				),
			),
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
				),
			),
		)
	);
	$ids = array_map( 'intval', $q->posts );
	return $cache[ $limit ] = $ids; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
}

/**
 * رابط صفحة الأصناف الجديدة.
 *
 * @return string
 */
function zad_new_url() {
	return add_query_arg( 'zd_new', '1', wc_get_page_permalink( 'shop' ) );
}

/**
 * صفحة المتجر مع ?zd_new=1: الأصناف الجديدة فقط، من الأحدث.
 *
 * @param WP_Query $q الاستعلام.
 */
function zad_new_arrivals_query( $q ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( is_admin() || ! $q->is_main_query() || empty( $_GET['zd_new'] ) || ! ( $q->is_post_type_archive( 'product' ) || $q->get( 'post_type' ) === 'product' ) ) {
		return;
	}
	$ids = zad_new_product_ids( 500 );
	$q->set( 'post__in', $ids ? $ids : array( 0 ) );
	$q->set(
		'orderby',
		array(
			'date' => 'DESC',
			'ID'   => 'DESC',
		)
	);
}
add_action( 'pre_get_posts', 'zad_new_arrivals_query', 30 );

/**
 * هل نعرض صفحة الأصناف الجديدة الآن؟
 *
 * @return bool
 */
function zad_is_new_view() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return ! empty( $_GET['zd_new'] ) && function_exists( 'is_shop' ) && is_shop();
}

/**
 * الكتالوج الأولي لا يُعدّ «جديداً»: علامة لمرة واحدة على أصناف الكتالوج الموجودة.
 */
function zad_mark_seeded_catalog() {
	if ( get_option( 'zad_migrated_v5' ) || ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$rows = include ZAD_DIR . '/inc/data/catalog.php';
	foreach ( (array) $rows as $row ) {
		$id = wc_get_product_id_by_sku( $row[0] );
		if ( $id ) {
			update_post_meta( $id, '_zad_seeded', 1 );
		}
	}
	update_option( 'zad_migrated_v5', 1, true );
}
add_action( 'init', 'zad_mark_seeded_catalog', 20 );

/* -------------------------------------------------------------------------
 * رسالة «نلبي كل احتياجات محلك»
 * ---------------------------------------------------------------------- */

/**
 * رابط التواصل للاتفاق (واتساب برسالة جاهزة فيها اسم المحل إن كان مسجلاً).
 *
 * @return string
 */
function zad_pitch_link() {
	$text = 'مرحباً، أرغب بالاتفاق على توريد احتياجات محلي من المواد الغذائية والمشروبات.';
	if ( is_user_logged_in() && function_exists( 'zad_customer_profile' ) ) {
		$p     = zad_customer_profile( get_current_user_id() );
		$place = 'other' === $p['district'] ? $p['city'] : zad_district_label( $p['district'] );
		if ( $p['shop'] ) {
			$text = sprintf( 'مرحباً، أنا %1$s من %2$s%3$s. أرغب بالاتفاق على توريد احتياجات محلي من المواد الغذائية والمشروبات.', $p['name'], $p['shop'], $place ? ' في ' . $place : '' );
		}
	}
	return zad_wa_number() ? zad_wa_link( $text ) : zad_page_url( 'contact' );
}

/**
 * صندوق الرسالة.
 *
 * @param string $context home|account|thanks|bell.
 * @return string HTML.
 */
function zad_pitch_html( $context ) {
	$p = zad_engage( 'pitch' );
	if ( ! $p['enabled'] || empty( $p[ $context ] ) || '' === trim( $p['title'] . $p['text'] ) ) {
		return '';
	}
	$id    = 'zd-pitch-' . $context;
	$phone = preg_replace( '/[^\d+]/', '', (string) zad_opt( 'phone' ) );
	$out   = sprintf( '<section class="zd-pitch zd-pitch--%1$s" aria-labelledby="%2$s">', esc_attr( $context ), esc_attr( $id ) );
	$out  .= '<div class="zd-pitch__icon" aria-hidden="true">' . zad_icon( 'store', '', 'bell' === $context ? 22 : 28 ) . '</div>';
	$out  .= '<div class="zd-pitch__body">';
	$out  .= sprintf( '<%1$s class="zd-pitch__title" id="%2$s">%3$s</%1$s>', 'home' === $context ? 'h2' : 'p', esc_attr( $id ), esc_html( $p['title'] ) );
	$out  .= $p['text'] ? '<p class="zd-pitch__text">' . esc_html( $p['text'] ) . '</p>' : '';
	$out  .= '</div><div class="zd-pitch__actions">';
	$wa    = zad_wa_number();
	$out  .= sprintf(
		'<a class="zd-btn %1$s" href="%2$s"%3$s>%4$s</a>',
		$wa ? 'zd-btn--wa' : 'zd-btn--primary',
		esc_url( zad_pitch_link() ),
		$wa ? ' target="_blank" rel="noopener"' : '',
		esc_html( $p['button'] ? $p['button'] : 'تواصل معنا' )
	);
	if ( $phone && 'bell' !== $context ) {
		$out .= sprintf( '<a class="zd-btn zd-btn--outline" href="tel:%1$s">اتصل <bdi dir="ltr">%2$s</bdi></a>', esc_attr( $phone ), esc_html( zad_opt( 'phone' ) ) );
	}
	$out .= '</div></section>';
	return $out;
}

/**
 * الرسالة في صفحة الشكر بعد الطلب.
 */
function zad_pitch_thanks() {
	echo zad_pitch_html( 'thanks' ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_thankyou', 'zad_pitch_thanks', 25 );

/* -------------------------------------------------------------------------
 * مركز الإشعارات (الجرس) + نافذة العرض + التنبيه بالأصناف الجديدة
 * ---------------------------------------------------------------------- */

/**
 * وقت آخر اطلاع للزبون المسجّل.
 *
 * @return int
 */
function zad_notif_seen_ts() {
	return is_user_logged_in() ? (int) get_user_meta( get_current_user_id(), 'zad_notif_seen', true ) : 0;
}

/**
 * «منذ 3 أيام» للواجهة.
 *
 * @param int $ts الوقت.
 * @return string
 */
function zad_since_text( $ts ) {
	$days = (int) floor( ( time() - $ts ) / DAY_IN_SECONDS );
	if ( $days < 1 ) {
		return 'اليوم';
	}
	if ( 1 === $days ) {
		return 'أمس';
	}
	if ( 2 === $days ) {
		return 'منذ يومين';
	}
	return $days <= 10 ? 'منذ ' . $days . ' أيام' : 'منذ ' . $days . ' يوماً';
}

/**
 * زر الجرس في الترويسة.
 */
function zad_notif_button() {
	printf(
		'<button type="button" class="zd-icon-btn zd-header__bell" data-zd-open="zd-notif" aria-controls="zd-notif" aria-expanded="false" aria-label="الإشعارات">%s<span class="zd-bubble zd-bubble--alert" data-zd-notif-count hidden>0</span></button>',
		zad_icon( 'bell', '', 22 ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/**
 * لوحة الإشعارات + نافذة العرض (في التذييل).
 */
function zad_notif_markup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$promo = zad_promo();
	$news  = zad_engage( 'news' );
	$ids   = $news['notify'] ? zad_new_product_ids( 10 ) : array();
	$pitch = zad_pitch_html( 'bell' );
	?>
	<div class="zd-drawer zd-drawer--end zd-notif" id="zd-notif" role="dialog" aria-modal="true" aria-labelledby="zd-notif-title" aria-hidden="true">
		<div class="zd-drawer__head">
			<strong class="zd-drawer__title" id="zd-notif-title">الإشعارات</strong>
			<button type="button" class="zd-icon-btn" data-zd-close aria-label="إغلاق الإشعارات"><?php zad_the_icon( 'close', '', 22 ); ?></button>
		</div>
		<div class="zd-drawer__body zd-notif__body">
			<?php if ( $promo['active'] ) : ?>
				<a class="zd-notif__promo" href="<?php echo esc_url( $promo['href'] ); ?>" data-zd-notif-promo="<?php echo esc_attr( $promo['id'] ); ?>">
					<span class="zd-notif__kicker"><?php zad_the_icon( 'percent', '', 16 ); ?> عرض فعّال<?php echo zad_promo_ends_text() ? ' · ' . esc_html( zad_promo_ends_text() ) : ''; ?></span>
					<strong><?php echo esc_html( $promo['title'] ); ?></strong>
					<?php if ( zad_promo_discount_label() ) : ?>
						<span class="zd-notif__deal"><?php echo esc_html( zad_promo_discount_label() ); ?></span>
					<?php endif; ?>
					<span class="zd-notif__text"><?php echo esc_html( $promo['text'] ); ?></span>
					<span class="zd-notif__go"><?php echo esc_html( $promo['button'] ? $promo['button'] : 'تسوّق العرض' ); ?> <?php zad_the_icon( 'arrow-left', '', 16 ); ?></span>
				</a>
			<?php endif; ?>

			<p class="zd-notif__sec">وصل حديثاً</p>
			<?php if ( $ids ) : ?>
				<ul class="zd-notif__list">
					<?php
					foreach ( $ids as $pid ) :
						$p = wc_get_product( $pid );
						if ( ! $p ) {
							continue;
						}
						$t = $p->get_date_created() ? $p->get_date_created()->getTimestamp() : 0;
						?>
						<li class="zd-notif__item" data-zd-t="<?php echo (int) $t; ?>">
							<a href="<?php echo esc_url( get_permalink( $pid ) ); ?>">
								<span class="zd-notif__img" aria-hidden="true"><?php echo $p->get_image_id() ? $p->get_image( 'woocommerce_gallery_thumbnail' ) : zad_product_art( $p, 'mini' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span class="zd-notif__info">
									<span class="zd-notif__name"><?php echo esc_html( $p->get_name() ); ?></span>
									<span class="zd-notif__meta"><span class="zd-badge zd-badge--new zd-badge--xs">جديد</span> <?php echo esc_html( zad_since_text( $t ) ); ?><?php echo zad_show_prices() ? ' · <b>' . esc_html( zad_money_plain( wc_get_price_to_display( $p ) ) ) . '</b>' : ''; ?></span>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<a class="zd-btn zd-btn--outline zd-btn--block" href="<?php echo esc_url( zad_new_url() ); ?>">كل الأصناف الجديدة</a>
			<?php else : ?>
				<p class="zd-notif__empty">لا أصناف جديدة خلال آخر <?php echo (int) zad_new_days(); ?> يوماً. نضيف الأصناف الجديدة هنا فور وصولها.</p>
			<?php endif; ?>

			<?php echo $pitch; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>

	<?php if ( $promo['active'] && $promo['popup'] ) : ?>
		<div class="zd-modal zd-promo" id="zd-promo" role="dialog" aria-modal="true" aria-labelledby="zd-promo-title" aria-describedby="zd-promo-text" hidden data-zd-promo="<?php echo esc_attr( $promo['id'] ); ?>">
			<div class="zd-modal__box zd-promo__box">
				<button type="button" class="zd-icon-btn zd-modal__close" data-zd-promo-close aria-label="إغلاق"><?php zad_the_icon( 'close', '', 22 ); ?></button>
				<div class="zd-promo__head">
					<p class="zd-promo__kicker"><?php zad_the_icon( 'percent', '', 18 ); ?> عرض لأصحاب المحلات</p>
					<h2 class="zd-promo__title" id="zd-promo-title"><?php echo esc_html( $promo['title'] ); ?></h2>
					<?php if ( zad_promo_discount_label() ) : ?>
						<p class="zd-promo__deal"><?php echo esc_html( zad_promo_discount_label() ); ?></p>
					<?php endif; ?>
				</div>
				<div class="zd-promo__body">
					<p class="zd-promo__text" id="zd-promo-text"><?php echo esc_html( $promo['text'] ); ?></p>
					<?php if ( zad_promo_ends_text() ) : ?>
						<p class="zd-promo__ends"><?php zad_the_icon( 'clock', '', 16 ); ?> <?php echo esc_html( zad_promo_ends_text() ); ?></p>
					<?php endif; ?>
					<div class="zd-promo__actions">
						<a class="zd-btn zd-btn--primary zd-btn--lg" href="<?php echo esc_url( $promo['href'] ); ?>" data-zd-promo-go><?php echo esc_html( $promo['button'] ? $promo['button'] : 'تسوّق العرض' ); ?></a>
						<button type="button" class="zd-btn zd-btn--ghost zd-btn--lg" data-zd-promo-close>لاحقاً</button>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php
}
add_action( 'wp_footer', 'zad_notif_markup', 5 );

/**
 * بيانات الإشعارات للسكربت.
 */
function zad_notif_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$promo = zad_promo();
	$news  = zad_engage( 'news' );
	wp_enqueue_script( 'zad-notify', ZAD_URI . '/assets/js/notify.js', array(), ZAD_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'zad-notify',
		'ZAD_NOTIFY',
		array(
			'now'       => time(),
			'seen'      => zad_notif_seen_ts(),
			'promoSeen' => is_user_logged_in() ? (string) get_user_meta( get_current_user_id(), 'zad_promo_seen', true ) : '',
			'user'      => is_user_logged_in(),
			'ajax'      => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'zad_notif_seen' ) : '',
			'nonce'     => is_user_logged_in() ? wp_create_nonce( 'zad-notif' ) : '',
			'promo'     => $promo['active'] ? $promo['id'] : '',
			'popup'     => $promo['active'] && $promo['popup'],
			'toast'     => (bool) $news['notify'],
			'newUrl'    => zad_new_url(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'zad_notif_assets', 25 );

/**
 * حفظ «اطّلع على الإشعارات» للزبون المسجّل (ليبقى الجرس صحيحاً على كل أجهزته).
 */
function zad_ajax_notif_seen() {
	if ( ! is_user_logged_in() || ! check_ajax_referer( 'zad-notif', 'nonce', false ) ) {
		wp_send_json_error( null, 403 );
	}
	$uid = get_current_user_id();
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- تحققنا أعلاه.
	if ( ! empty( $_POST['items'] ) ) {
		update_user_meta( $uid, 'zad_notif_seen', time() );
	}
	$promo = isset( $_POST['promo'] ) ? sanitize_key( wp_unslash( $_POST['promo'] ) ) : '';
	// phpcs:enable
	if ( $promo && zad_promo()['id'] === $promo ) {
		update_user_meta( $uid, 'zad_promo_seen', $promo );
	}
	wp_send_json_success();
}
add_action( 'wc_ajax_zad_notif_seen', 'zad_ajax_notif_seen' );

/**
 * زبون جديد: لا نغرقه بإشعارات الأصناف التي سبقت تسجيله.
 *
 * @param int $user_id المستخدم.
 */
function zad_notif_on_register( $user_id ) {
	update_user_meta( $user_id, 'zad_notif_seen', time() );
}
add_action( 'woocommerce_created_customer', 'zad_notif_on_register' );

/**
 * شريط الإعلان: يعرض العرض الفعّال بدل النص المعتاد.
 *
 * @return array|null [النص، الرابط، نص الرابط] أو null لإخفاء الشريط.
 */
function zad_announce_bar() {
	$promo = function_exists( 'zad_promo' ) ? zad_promo() : null;
	if ( $promo && $promo['active'] && $promo['bar'] ) {
		$deal = zad_promo_discount_label();
		$ends = zad_promo_ends_text();
		return array( $promo['title'] . ( $deal ? ': ' . $deal : '' ) . ( $ends ? ' · ' . $ends : '' ), $promo['href'], $promo['button'] ? $promo['button'] : 'تسوّق العرض' );
	}
	if ( ! zad_opt( 'announcement' ) ) {
		return null;
	}
	$gate = function_exists( 'zad_price_gate' ) ? zad_price_gate() : '';
	if ( 'login' === $gate && function_exists( 'wc_get_page_permalink' ) ) {
		return array( zad_opt( 'announcement' ), add_query_arg( 'tab', 'register', wc_get_page_permalink( 'myaccount' ) ), 'افتح حساب جملة' );
	}
	return array( zad_opt( 'announcement' ), zad_page_url( 'quick_order' ), 'افتح قائمة الأسعار' );
}
