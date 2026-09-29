<?php
/**
 * «الزبائن ← العروض والإشعارات»: إعداد العرض الحالي، والأصناف الجديدة، ورسالة أصحاب المحلات،
 * ومساعد إرسال الإعلان للزبائن عبر واتساب.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * الصفحة في القائمة.
 */
function zad_engage_menu() {
	$hook = add_submenu_page( 'zad-customers', 'العروض والإشعارات', 'العروض والإشعارات', 'manage_woocommerce', 'zad-engage', 'zad_engage_page' );
	add_action( 'admin_print_styles-' . $hook, 'zad_engage_admin_assets' );
	// اسم أوضح لأول عنصر في القائمة الفرعية.
	global $submenu;
	if ( isset( $submenu['zad-customers'][0] ) ) {
		$submenu['zad-customers'][0][0] = 'كل الزبائن'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	}
}
add_action( 'admin_menu', 'zad_engage_menu', 20 );

/**
 * أنماط وسكربت الصفحة.
 */
function zad_engage_admin_assets() {
	wp_enqueue_style( 'zad-customers-admin', ZAD_URI . '/assets/css/admin-customers.css', array(), ZAD_VERSION );
	wp_enqueue_script( 'zad-engage-admin', ZAD_URI . '/assets/js/engage-admin.js', array(), ZAD_VERSION, true );
}

/**
 * حفظ الإعدادات.
 */
function zad_engage_save() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'غير مسموح.', 403 );
	}
	check_admin_referer( 'zad_engage_save' );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- تحققنا أعلاه.
	$in   = static function ( $key, $default = '' ) {
		return isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : $default; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	};
	$date = static function ( $v ) {
		$v = sanitize_text_field( (string) $v );
		return preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v ) ? $v : '';
	};

	$old   = zad_engage( 'promo' );
	$type  = sanitize_key( $in( 'promo_type', 'none' ) );
	$type  = in_array( $type, array( 'none', 'fixed', 'percent' ), true ) ? $type : 'none';
	$amt   = max( 0, (float) str_replace( ',', '.', sanitize_text_field( $in( 'promo_amount', '0' ) ) ) );
	$link  = sanitize_key( $in( 'promo_link', 'quick_order' ) );
	$promo = array(
		'enabled' => ! empty( $_POST['promo_enabled'] ),
		'title'   => sanitize_text_field( $in( 'promo_title' ) ),
		'text'    => sanitize_textarea_field( $in( 'promo_text' ) ),
		'type'    => $type,
		'amount'  => 'percent' === $type ? min( 90, $amt ) : $amt,
		'scope'   => 'cats' === $in( 'promo_scope' ) ? 'cats' : 'all',
		'cats'    => array_values( array_filter( array_map( 'absint', (array) $in( 'promo_cats', array() ) ) ) ),
		'start'   => $date( $in( 'promo_start' ) ),
		'end'     => $date( $in( 'promo_end' ) ),
		'button'  => sanitize_text_field( $in( 'promo_button' ) ),
		'link'    => in_array( $link, array( 'quick_order', 'shop', 'offers', 'custom' ), true ) ? $link : 'quick_order',
		'url'     => esc_url_raw( $in( 'promo_url' ) ),
		'popup'   => ! empty( $_POST['promo_popup'] ),
		'bar'     => ! empty( $_POST['promo_bar'] ),
	);
	// أي تغيير في مضمون العرض يجعله «جديداً»، فتظهر نافذته من جديد لكل زبون.
	$changed = false;
	foreach ( array( 'enabled', 'title', 'text', 'type', 'amount', 'start', 'end' ) as $k ) {
		if ( (string) $old[ $k ] !== (string) $promo[ $k ] ) {
			$changed = true;
		}
	}
	$promo['version'] = (int) $old['version'] + ( $changed ? 1 : 0 );
	update_option( 'zad_engage_promo', $promo, true );

	update_option(
		'zad_engage_news',
		array(
			'days'   => max( 1, min( 180, absint( $in( 'news_days', 21 ) ) ) ),
			'notify' => ! empty( $_POST['news_notify'] ),
			'badge'  => ! empty( $_POST['news_badge'] ),
		),
		true
	);

	$pitch = array(
		'enabled' => ! empty( $_POST['pitch_enabled'] ),
		'title'   => sanitize_text_field( $in( 'pitch_title' ) ),
		'text'    => sanitize_textarea_field( $in( 'pitch_text' ) ),
		'button'  => sanitize_text_field( $in( 'pitch_button' ) ),
	);
	foreach ( array( 'home', 'account', 'thanks', 'bell' ) as $place ) {
		$pitch[ $place ] = ! empty( $_POST[ 'pitch_' . $place ] );
	}
	update_option( 'zad_engage_pitch', $pitch, true );
	// phpcs:enable

	wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=zad-engage' ) ) );
	exit;
}
add_action( 'admin_post_zad_engage_save', 'zad_engage_save' );

/**
 * رسالة واتساب جاهزة للأصناف الجديدة.
 *
 * @return string
 */
function zad_engage_news_message() {
	$ids = zad_new_product_ids( 8 );
	if ( ! $ids ) {
		return '';
	}
	$lines = array( 'مرحباً {الاسم}، وصلت أصناف جديدة إلى ' . get_bloginfo( 'name' ) . ':' );
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		if ( $p ) {
			$lines[] = '• ' . $p->get_name() . ( zad_show_prices() ? ' — ' . zad_money_plain( wc_get_price_to_display( $p ) ) . ' / كرتونة' : '' );
		}
	}
	$lines[] = '';
	$lines[] = 'شاهدها واطلب من هنا: ' . zad_new_url();
	return implode( "\n", $lines );
}

/**
 * رسالة واتساب جاهزة للعرض.
 *
 * @return string
 */
function zad_engage_promo_message() {
	$p = zad_promo();
	if ( 'off' === $p['state'] && ! $p['title'] ) {
		return '';
	}
	$lines   = array( 'مرحباً {الاسم}، ' . $p['title'] . ' في ' . get_bloginfo( 'name' ) . '!' );
	// لا نكرر الخصم إن كان مذكوراً في نص العرض.
	$mention = zad_promo_discounting() && false === strpos( $p['text'], (string) ( 0 + (float) $p['amount'] ) );
	$lines[] = trim( ( $mention ? zad_promo_discount_label() . '. ' : '' ) . $p['text'] );
	if ( $p['end_ts'] ) {
		$lines[] = 'العرض حتى ' . wp_date( 'j F Y', $p['end_ts'] ) . '.';
	}
	$lines[] = '';
	$lines[] = 'اطلب الآن: ' . $p['href'];
	return implode( "\n", $lines );
}

/**
 * الصفحة.
 */
function zad_engage_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$promo  = zad_promo();
	$news   = zad_engage( 'news' );
	$pitch  = zad_engage( 'pitch' );
	$cats   = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	$states = array(
		'off'       => array( 'غير مفعّل', 'is-off' ),
		'scheduled' => array( 'مجدول: يبدأ ' . ( $promo['start_ts'] ? wp_date( 'j F Y، H:i', $promo['start_ts'] ) : '' ), 'is-wait' ),
		'active'    => array( 'فعّال الآن' . ( zad_promo_ends_text() ? ' · ' . zad_promo_ends_text() : '' ), 'is-on' ),
		'ended'     => array( 'انتهى في ' . ( $promo['end_ts'] ? wp_date( 'j F Y', $promo['end_ts'] ) : '' ), 'is-off' ),
	);
	$state  = $states[ $promo['state'] ];
	// أمثلة أسعار لمعاينة الخصم.
	$sample = array();
	if ( zad_promo_discounting() ) {
		foreach ( wc_get_products( array( 'limit' => 40, 'status' => 'publish', 'type' => 'simple' ) ) as $p ) {
			$new = zad_promo_price_for( $p );
			if ( null !== $new ) {
				$sample[] = array( $p->get_name(), (float) $p->get_price( 'edit' ), $new );
			}
			if ( count( $sample ) >= 3 ) {
				break;
			}
		}
	}
	$customers = function_exists( 'zad_customers_dataset' ) ? zad_customers_dataset() : array();
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$updated = ! empty( $_GET['updated'] );
	?>
	<div class="wrap zd-cus zd-eng" dir="rtl">
		<h1>العروض والإشعارات</h1>
		<p class="zd-cus__lead">ما يراه الزبون عند دخول الموقع: العرض الحالي، والأصناف الجديدة، ورسالتك لأصحاب المحلات. ومن الأسفل ترسل الإعلان لزبائنك عبر واتساب.</p>
		<?php if ( $updated ) : ?>
			<div class="notice notice-success is-dismissible"><p>تم حفظ الإعدادات.</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="zd-eng__form">
			<?php wp_nonce_field( 'zad_engage_save' ); ?>
			<input type="hidden" name="action" value="zad_engage_save">

			<section class="zd-card zd-eng__card" aria-labelledby="zd-eng-promo">
				<div class="zd-card__head">
					<h2 id="zd-eng-promo">العرض الحالي</h2>
					<span class="zd-eng__state <?php echo esc_attr( $state[1] ); ?>"><?php echo esc_html( $state[0] ); ?></span>
				</div>
				<p class="zd-eng__help">مثال: «عرض نهاية السنة» بخصم 10 ليرات على كل كرتونة لمدة أسبوع. يرى الزبون نافذة العرض أول ما يدخل الموقع (مرة واحدة)، ويبقى العرض في شريط الإعلان وفي جرس الإشعارات حتى ينتهي.</p>
				<table class="form-table" role="presentation">
					<tr><th scope="row">التفعيل</th><td><label><input type="checkbox" name="promo_enabled" value="1"<?php checked( $promo['enabled'] ); ?>> تفعيل العرض</label></td></tr>
					<tr><th scope="row"><label for="promo_title">عنوان العرض</label></th><td><input type="text" class="regular-text" id="promo_title" name="promo_title" value="<?php echo esc_attr( $promo['title'] ); ?>" placeholder="عرض نهاية السنة"></td></tr>
					<tr><th scope="row"><label for="promo_text">نص العرض</label></th><td><textarea class="large-text" rows="2" id="promo_text" name="promo_text"><?php echo esc_textarea( $promo['text'] ); ?></textarea></td></tr>
					<tr>
						<th scope="row"><label for="promo_type">الخصم على الأسعار</label></th>
						<td>
							<select id="promo_type" name="promo_type" data-zd-eng-type>
								<option value="none"<?php selected( $promo['type'], 'none' ); ?>>بلا خصم تلقائي (إعلان فقط)</option>
								<option value="fixed"<?php selected( $promo['type'], 'fixed' ); ?>>مبلغ ثابت من سعر الكرتونة (₺)</option>
								<option value="percent"<?php selected( $promo['type'], 'percent' ); ?>>نسبة من سعر الكرتونة (%)</option>
							</select>
							<label class="screen-reader-text" for="promo_amount">مقدار الخصم</label>
							<input type="number" step="0.5" min="0" id="promo_amount" name="promo_amount" value="<?php echo esc_attr( $promo['amount'] ); ?>" class="small-text" data-zd-eng-amount>
							<p class="description">يُطبَّق تلقائياً على الأسعار والطلبية طوال مدة العرض فقط، ويعود السعر الأصلي بعدها وحده. لا يغيّر أسعارك المحفوظة، ولا يرفع سعر صنف مخفّض أصلاً.</p>
						</td>
					</tr>
					<tr>
						<th scope="row">يشمل</th>
						<td>
							<label><input type="radio" name="promo_scope" value="all"<?php checked( $promo['scope'], 'all' ); ?>> كل الأصناف</label>
							&nbsp; <label><input type="radio" name="promo_scope" value="cats"<?php checked( $promo['scope'], 'cats' ); ?>> أقسام محددة:</label>
							<div class="zd-eng__cats">
								<?php foreach ( (array) $cats as $t ) : ?>
									<label><input type="checkbox" name="promo_cats[]" value="<?php echo (int) $t->term_id; ?>"<?php checked( in_array( (int) $t->term_id, array_map( 'intval', (array) $promo['cats'] ), true ) ); ?>> <?php echo esc_html( $t->name ); ?></label>
								<?php endforeach; ?>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">المدة</th>
						<td>
							<label>من <input type="datetime-local" name="promo_start" value="<?php echo esc_attr( $promo['start'] ); ?>"></label>
							&nbsp; <label>إلى <input type="datetime-local" name="promo_end" value="<?php echo esc_attr( $promo['end'] ); ?>"></label>
							<p class="description">اتركهما فارغين ليبدأ العرض فوراً ويستمر حتى تلغي تفعيله. التوقيت بتوقيت الموقع (<?php echo esc_html( wp_timezone_string() ); ?>).</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="promo_button">زر العرض</label></th>
						<td>
							<input type="text" id="promo_button" name="promo_button" value="<?php echo esc_attr( $promo['button'] ); ?>" placeholder="تسوّق العرض">
							<label class="screen-reader-text" for="promo_link">وجهة الزر</label>
							<select id="promo_link" name="promo_link">
								<option value="quick_order"<?php selected( $promo['link'], 'quick_order' ); ?>>قائمة الأسعار</option>
								<option value="shop"<?php selected( $promo['link'], 'shop' ); ?>>كل الأصناف</option>
								<option value="offers"<?php selected( $promo['link'], 'offers' ); ?>>قسم العروض</option>
								<option value="custom"<?php selected( $promo['link'], 'custom' ); ?>>رابط آخر</option>
							</select>
							<label class="screen-reader-text" for="promo_url">الرابط</label>
							<input type="url" class="regular-text" id="promo_url" name="promo_url" value="<?php echo esc_attr( $promo['url'] ); ?>" placeholder="https://" dir="ltr">
						</td>
					</tr>
					<tr>
						<th scope="row">أين يظهر</th>
						<td>
							<label><input type="checkbox" name="promo_popup" value="1"<?php checked( $promo['popup'] ); ?>> نافذة منبثقة أول ما يدخل الزبون (مرة واحدة لكل عرض)</label><br>
							<label><input type="checkbox" name="promo_bar" value="1"<?php checked( $promo['bar'] ); ?>> في شريط الإعلان أعلى كل الصفحات</label><br>
							<span class="description">ويظهر دائماً في جرس الإشعارات وفي «حسابي» طوال مدته.</span>
						</td>
					</tr>
				</table>
				<?php if ( $sample ) : ?>
					<div class="zd-eng__sample">
						<strong>أسعار الآن مع الخصم:</strong>
						<ul>
							<?php foreach ( $sample as $s ) : ?>
								<li><?php echo esc_html( $s[0] ); ?>: <del><?php echo esc_html( zad_money_plain( $s[1] ) ); ?></del> ← <b><?php echo esc_html( zad_money_plain( $s[2] ) ); ?></b></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</section>

			<section class="zd-card zd-eng__card" aria-labelledby="zd-eng-news">
				<div class="zd-card__head"><h2 id="zd-eng-news">الأصناف الجديدة</h2><span class="zd-card__meta"><?php echo esc_html( count( zad_new_product_ids( 500 ) ) . ' صنف جديد الآن' ); ?></span></div>
				<p class="zd-eng__help">كل صنف تضيفه من «المنتجات ← إضافة منتج» يصير «جديداً» تلقائياً: شارة «جديد» على بطاقته، وقسم «أحدث الأصناف» في الرئيسية، وفلتر «جديد» في قائمة الأسعار، وإشعار في الجرس. والزبون العائد تصله رسالة «وصل أصناف جديدة منذ زيارتك الأخيرة».</p>
				<table class="form-table" role="presentation">
					<tr><th scope="row"><label for="news_days">يبقى الصنف «جديداً»</label></th><td><input type="number" min="1" max="180" id="news_days" name="news_days" value="<?php echo (int) $news['days']; ?>" class="small-text"> يوماً من تاريخ إضافته</td></tr>
					<tr><th scope="row">الإشعار</th><td>
						<label><input type="checkbox" name="news_notify" value="1"<?php checked( $news['notify'] ); ?>> إشعار الزبائن بالأصناف الجديدة (الجرس ورسالة الزائر العائد)</label><br>
						<label><input type="checkbox" name="news_badge" value="1"<?php checked( $news['badge'] ); ?>> شارة «جديد» على البطاقات وفي قائمة الأسعار</label>
					</td></tr>
				</table>
				<p class="description">لإعادة صنف قديم إلى «الجديد» (مثلاً عند عودته للتوفر): غيّر «تاريخ النشر» من صفحة تحرير المنتج إلى اليوم.</p>
			</section>

			<section class="zd-card zd-eng__card" aria-labelledby="zd-eng-pitch">
				<div class="zd-card__head"><h2 id="zd-eng-pitch">رسالة لأصحاب المحلات</h2></div>
				<p class="zd-eng__help">رسالة ثابتة تدعو صاحب المحل للتواصل معك والاتفاق، مع زر واتساب برسالة جاهزة فيها اسم محله ومنطقته.</p>
				<table class="form-table" role="presentation">
					<tr><th scope="row">التفعيل</th><td><label><input type="checkbox" name="pitch_enabled" value="1"<?php checked( $pitch['enabled'] ); ?>> إظهار الرسالة</label></td></tr>
					<tr><th scope="row"><label for="pitch_title">العنوان</label></th><td><input type="text" class="regular-text" id="pitch_title" name="pitch_title" value="<?php echo esc_attr( $pitch['title'] ); ?>"></td></tr>
					<tr><th scope="row"><label for="pitch_text">النص</label></th><td><textarea class="large-text" rows="3" id="pitch_text" name="pitch_text"><?php echo esc_textarea( $pitch['text'] ); ?></textarea></td></tr>
					<tr><th scope="row"><label for="pitch_button">نص الزر</label></th><td><input type="text" id="pitch_button" name="pitch_button" value="<?php echo esc_attr( $pitch['button'] ); ?>"></td></tr>
					<tr><th scope="row">أين تظهر</th><td>
						<label><input type="checkbox" name="pitch_home" value="1"<?php checked( $pitch['home'] ); ?>> الصفحة الرئيسية</label><br>
						<label><input type="checkbox" name="pitch_account" value="1"<?php checked( $pitch['account'] ); ?>> «حسابي» (لوحة الزبون)</label><br>
						<label><input type="checkbox" name="pitch_thanks" value="1"<?php checked( $pitch['thanks'] ); ?>> صفحة الشكر بعد الطلب</label><br>
						<label><input type="checkbox" name="pitch_bell" value="1"<?php checked( $pitch['bell'] ); ?>> أسفل جرس الإشعارات</label>
					</td></tr>
				</table>
			</section>

			<p class="submit"><button type="submit" class="button button-primary button-hero">حفظ</button></p>
		</form>

		<section class="zd-card zd-eng__card zd-eng__wa" aria-labelledby="zd-eng-wa">
			<div class="zd-card__head"><h2 id="zd-eng-wa">أرسل الإعلان لزبائنك عبر واتساب</h2><span class="zd-card__meta"><?php echo esc_html( count( $customers ) . ' زبون' ); ?></span></div>
			<p class="zd-eng__help">اختر الرسالة وعدّلها إن أردت، ثم اضغط «إرسال» بجانب كل زبون: تُفتح محادثته في واتساب والرسالة جاهزة باسمه. أو انسخ الرسالة وانشرها في قائمة بث أو حالة واتساب.</p>
			<div class="zd-eng__msgtabs" role="group" aria-label="نوع الرسالة">
				<button type="button" class="button" data-zd-msg="news"<?php disabled( ! zad_engage_news_message() ); ?>>الأصناف الجديدة</button>
				<button type="button" class="button" data-zd-msg="promo"<?php disabled( ! zad_engage_promo_message() ); ?>>العرض</button>
			</div>
			<label class="screen-reader-text" for="zd-eng-text">نص الرسالة</label>
			<textarea id="zd-eng-text" class="large-text" rows="8" data-zd-msg-text dir="rtl"><?php echo esc_textarea( zad_engage_news_message() ? zad_engage_news_message() : zad_engage_promo_message() ); ?></textarea>
			<script type="application/json" id="zd-eng-msgs"><?php echo wp_json_encode( array( 'news' => zad_engage_news_message(), 'promo' => zad_engage_promo_message() ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?></script>
			<p class="description">{الاسم} و{المحل} تُستبدل باسم كل زبون ومحله.</p>
			<p><button type="button" class="button" data-zd-copy>نسخ الرسالة</button> <span data-zd-copied role="status" aria-live="polite"></span></p>
			<?php if ( $customers ) : ?>
				<label class="screen-reader-text" for="zd-eng-q">بحث في الزبائن</label>
				<input type="search" id="zd-eng-q" class="regular-text" placeholder="ابحث بالاسم أو المحل أو المنطقة" data-zd-eng-q>
				<ul class="zd-eng__list" data-zd-eng-list>
					<?php foreach ( $customers as $c ) : ?>
						<?php
						if ( ! $c['wa'] ) {
							continue;
						}
						$place = 'other' === $c['district'] ? $c['city'] : zad_district_label( $c['district'] );
						?>
						<li data-search="<?php echo esc_attr( mb_strtolower( $c['shop'] . ' ' . $c['name'] . ' ' . $place ) ); ?>">
							<span><b><?php echo esc_html( $c['shop'] ? $c['shop'] : $c['name'] ); ?></b> · <?php echo esc_html( $c['name'] ); ?><?php echo $place ? ' · ' . esc_html( $place ) : ''; ?></span>
							<button type="button" class="button button-small" data-zd-send data-wa="<?php echo esc_attr( $c['wa'] ); ?>" data-name="<?php echo esc_attr( $c['name'] ); ?>" data-shop="<?php echo esc_attr( $c['shop'] ); ?>">إرسال</button>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>لا يوجد زبائن مسجلون بعد.</p>
			<?php endif; ?>
		</section>
	</div>
	<?php
}
