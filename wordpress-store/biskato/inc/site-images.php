<?php
/**
 * صور الموقع الاحترافية المصممة لبسكاتو (Nano Banana Pro)
 * تُجلب من استضافتك مرة واحدة، وتُحوَّل إلى WebP بمقاسين، وتحل محل
 * الصور المضمّنة في القالب. المصدر ثابت، ولا يُقبل أي رابط من المستخدم.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * مصادر الصور: الاسم => [رابط الأصل، العرض المستهدف].
 *
 * @return array
 */
function zad_site_image_sources() {
	$base = 'https://d8j0ntlcm91z4.cloudfront.net/user_3HMjZPgpKCsHgo8BLC9DovmF1SX/hf_';
	$ids  = array(
		// الإصدار 5 (Nano Banana Pro): الواجهة، وصور الأقسام التسعة، والتوصيل في إسطنبول، وصفحة «عن بسكاتو».
		'hero-v5'           => array( '20261001_211006_c925a0a0-ef73-4413-b5b8-629a0b2ecd25', 1800 ),
		'about-team'        => array( '20261001_211006_16ce5fac-1654-4543-b615-7cf27f13c816', 1600 ),
		'banner-delivery'   => array( '20261001_211006_3645c5bd-158d-4ce1-8045-da5b18a0bc0b', 1600 ),
		'tile-cake'         => array( '20261001_211006_81df1103-6fea-4bb4-bf53-2e0161dcae8f', 900 ),
		'tile-biscuits'     => array( '20261001_211006_4e9e5bc9-1f1c-4329-b1ec-6b443d90fc83', 900 ),
		'tile-snacks'       => array( '20261001_211104_39ccf519-10d9-40a7-b98e-501f0ad838b7', 900 ),
		'tile-chips'        => array( '20261001_211006_2866f8db-434c-4142-853b-a062328bbe6e', 900 ),
		'tile-candy'        => array( '20261001_211006_26ae0322-551b-4fbc-846c-63628d5ec1a8', 900 ),
		'tile-gum'          => array( '20261001_211006_3e325cf8-f9bc-456f-9a31-6b53289d383f', 900 ),
		'tile-toys'         => array( '20261001_211006_d405bda8-89b0-4d2d-8ef3-67d514c721ed', 900 ),
		'tile-drinks'       => array( '20261001_211006_3a70b91d-9326-45ab-83d6-553957b4ab5e', 900 ),
		'tile-offers'       => array( '20261001_211006_cf9bd06a-d35b-40b5-9b5d-65d2b2af75ad', 900 ),
	);
	$out = array();
	foreach ( $ids as $name => $row ) {
		$out[ $name ] = array(
			'url'   => $base . $row[0] . '.png',
			'width' => $row[1],
		);
	}
	return $out;
}

/**
 * الصور المستوردة: الاسم => [w, h, ext, v].
 *
 * @return array
 */
function zad_site_images_stored() {
	$stored = get_option( 'zad_site_images', array() );
	return is_array( $stored ) ? $stored : array();
}

/**
 * مجلد الحفظ داخل uploads.
 *
 * @return array [path, url]
 */
function zad_site_images_dir() {
	$up = wp_upload_dir( null, false );
	return array( trailingslashit( $up['basedir'] ) . 'zad-site', trailingslashit( $up['baseurl'] ) . 'zad-site' );
}

/**
 * الصور التي تحتاج جلباً: لم تُجلب بعد، أو تغيّر مصدرها بعد تحديث القالب.
 *
 * @return string[]
 */
function zad_site_images_pending() {
	$stored  = zad_site_images_stored();
	$pending = array();
	foreach ( zad_site_image_sources() as $name => $src ) {
		$old = isset( $stored[ $name ]['src'] )
			? $stored[ $name ]['src'] !== $src['url']
			// صور الرئيسية المجلوبة قبل تسجيل المصدر (الإصدار 3.7.0) تُعد قديمة، فقد تغيّرت مصادرها.
			: ! empty( $stored[ $name ] ) && preg_match( '/^(hero-eti|tile|banner|stage)-/', $name );
		if ( empty( $stored[ $name ] ) || $old ) {
			$pending[] = $name;
		}
	}
	return $pending;
}

/**
 * رابط النسخة المجلوبة من صورة (فارغ إن لم تُجلب).
 *
 * @param string $name  الاسم.
 * @param bool   $small النسخة الصغيرة.
 * @return string
 */
function zad_site_image_imported_url( $name, $small = false ) {
	static $sources = null;
	if ( null === $sources ) {
		$sources = zad_site_image_sources();
	}
	$stored = zad_site_images_stored();
	// صورة قديمة لم تعد في القائمة أو تغيّر مصدرها: تُعرض الصورة المضمّنة حتى تُجلب الجديدة.
	if ( empty( $stored[ $name ] ) || ! isset( $sources[ $name ] ) || ( isset( $stored[ $name ]['src'] ) && $stored[ $name ]['src'] !== $sources[ $name ]['url'] ) ) {
		return '';
	}
	list( , $url ) = zad_site_images_dir();
	$s             = $stored[ $name ];
	return $url . '/' . $name . ( $small ? '-sm' : '' ) . '.' . $s['ext'] . '?v=' . rawurlencode( (string) $s['v'] );
}

/**
 * جلب صورة واحدة وتحويلها إلى مقاسين.
 *
 * @param string $name الاسم.
 * @return true|WP_Error
 */
function zad_site_image_import( $name ) {
	$sources = zad_site_image_sources();
	if ( ! isset( $sources[ $name ] ) ) {
		return new WP_Error( 'zad_name', 'اسم صورة غير معروف.' );
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$tmp = download_url( $sources[ $name ]['url'], 90 );
	if ( is_wp_error( $tmp ) ) {
		return $tmp;
	}

	list( $dir ) = zad_site_images_dir();
	if ( ! wp_mkdir_p( $dir ) ) {
		wp_delete_file( $tmp );
		return new WP_Error( 'zad_dir', 'تعذر إنشاء مجلد الصور داخل uploads.' );
	}

	$ext  = wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ? 'webp' : 'jpg';
	$mime = 'webp' === $ext ? 'image/webp' : 'image/jpeg';
	$size = array();

	foreach ( array( '' => 1, '-sm' => 2 ) as $suffix => $div ) {
		$editor = wp_get_image_editor( $tmp );
		if ( is_wp_error( $editor ) ) {
			wp_delete_file( $tmp );
			return $editor;
		}
		$target = (int) round( $sources[ $name ]['width'] / $div );
		$cur    = $editor->get_size();
		if ( $cur['width'] > $target ) {
			$editor->resize( $target, null, false );
		}
		$editor->set_quality( 'texture' === $name ? 70 : 80 );
		$saved = $editor->save( $dir . '/' . $name . $suffix . '.' . $ext, $mime );
		if ( is_wp_error( $saved ) ) {
			wp_delete_file( $tmp );
			return $saved;
		}
		if ( '' === $suffix ) {
			$size = array( (int) $saved['width'], (int) $saved['height'] );
		}
	}
	wp_delete_file( $tmp );

	$stored          = zad_site_images_stored();
	$stored[ $name ] = array(
		'w'   => $size[0],
		'h'   => $size[1],
		'ext' => $ext,
		'v'   => time(),
		'src' => $sources[ $name ]['url'],
	);
	update_option( 'zad_site_images', $stored, false );
	if ( function_exists( 'zad_cache_flush' ) ) {
		zad_cache_flush();
	}
	return true;
}

/**
 * الرجوع إلى الصور المضمّنة في القالب (حذف المستوردة).
 */
function zad_site_images_reset() {
	list( $dir ) = zad_site_images_dir();
	foreach ( zad_site_images_stored() as $name => $s ) {
		foreach ( array( '', '-sm' ) as $suffix ) {
			$file = $dir . '/' . $name . $suffix . '.' . $s['ext'];
			if ( file_exists( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}
	delete_option( 'zad_site_images' );
}

/* -------------------------------------------------------------------------
 * جلب تلقائي في الخلفية (بلا ضغط زر): يبدأ بعد تفعيل القالب أو تحديثه،
 * ويجلب صورتين كل دقيقة حتى تكتمل.
 * ---------------------------------------------------------------------- */

/**
 * جدولة الجلب إن بقيت صور.
 */
function zad_site_images_schedule() {
	if ( zad_site_images_pending() && ! wp_next_scheduled( 'zad_site_images_cron' ) ) {
		wp_schedule_event( time() + 10, 'zad_minute', 'zad_site_images_cron' );
	}
}
add_action( 'after_switch_theme', 'zad_site_images_schedule' );
add_action(
	'admin_init',
	static function () {
		if ( ! wp_doing_ajax() && ! get_transient( 'zad_si_checked' ) ) {
			set_transient( 'zad_si_checked', 1, 30 * MINUTE_IN_SECONDS );
			zad_site_images_schedule();
		}
	}
);
add_action(
	'zad_site_images_cron',
	static function () {
		if ( get_transient( 'zad_si_lock' ) ) {
			return;
		}
		set_transient( 'zad_si_lock', 1, 4 * MINUTE_IN_SECONDS );
		$failed = (array) get_option( 'zad_site_images_failed', array() );
		$done   = 0;
		foreach ( zad_site_images_pending() as $name ) {
			// صورة فشلت 3 مرات تُترك للزر اليدوي حتى لا تتكرر المحاولة بلا نهاية.
			if ( isset( $failed[ $name ] ) && $failed[ $name ] >= 3 ) {
				continue;
			}
			$r = zad_site_image_import( $name );
			if ( is_wp_error( $r ) ) {
				$failed[ $name ] = isset( $failed[ $name ] ) ? $failed[ $name ] + 1 : 1;
			} else {
				unset( $failed[ $name ] );
			}
			if ( ++$done >= 2 ) {
				break;
			}
		}
		update_option( 'zad_site_images_failed', $failed, false );
		delete_transient( 'zad_si_lock' );
		$left = array_diff( zad_site_images_pending(), array_keys( array_filter( $failed, static function ( $n ) { return $n >= 3; } ) ) );
		if ( ! $left ) {
			wp_clear_scheduled_hook( 'zad_site_images_cron' );
		}
	}
);

/* -------------------------------------------------------------------------
 * لوحة التحكم: المظهر ← صور موقع بسكاتو
 * ---------------------------------------------------------------------- */

/**
 * القائمة.
 */
function zad_site_images_menu() {
	add_theme_page( 'صور موقع بسكاتو', 'صور الموقع', 'manage_options', 'zad-site-images', 'zad_site_images_page' );
}
add_action( 'admin_menu', 'zad_site_images_menu' );

/**
 * تنبيه لمرة واحدة حتى تُجلب الصور.
 */
function zad_site_images_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'appearance_page_zad-setup' ), true ) ) {
		return;
	}
	// تظهر كلما وُجدت صور لم تُجلب بعد (أول تثبيت، أو صور جديدة أضافها تحديث القالب).
	$missing = count( zad_site_images_pending() );
	if ( ! $missing ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>%1$s</strong> تُجلب الصور الاحترافية للواجهة والأقسام إلى استضافتك تلقائياً في الخلفية. <a class="button" href="%2$s" style="margin-inline-start:8px">تسريع الجلب</a></p></div>',
		esc_html( sprintf( 'صور موقع بسكاتو: بقيت %d صورة.', $missing ) ),
		esc_url( admin_url( 'themes.php?page=zad-site-images' ) )
	);
}
add_action( 'admin_notices', 'zad_site_images_notice' );

/**
 * صفحة الأداة.
 */
function zad_site_images_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$items = array();
	foreach ( array_keys( zad_site_image_sources() ) as $name ) {
		$items[] = array(
			'name'     => $name,
			'thumb'    => zad_img_url( $name, true ),
			'imported' => (bool) zad_site_image_imported_url( $name ) && ! in_array( $name, zad_site_images_pending(), true ),
		);
	}
	$config = array(
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'zad_site_images' ),
		'items' => $items,
	);
	?>
	<div class="wrap zd-si" dir="rtl">
		<h1>صور موقع بسكاتو</h1>
		<p><?php echo esc_html( count( $items ) ); ?> صورة مصممة خصيصاً لهوية بسكاتو: شرائح الصفحة الرئيسية وبطاقات الأقسام ولافتاتها، وشرائح العملاء، وخطوات الطلب، وصفحات الشركة. تجلب هذه الأداة الصور من مصدرها مباشرة إلى استضافتك، وتحوّلها إلى WebP بمقاسين للجوال والشاشات الكبيرة، ثم تستخدمها الواجهة تلقائياً بدل الصور المؤقتة المضمّنة في القالب.</p>
		<p>
			<button type="button" class="button button-primary button-hero" id="zd-si-run">جلب كل الصور</button>
			<button type="button" class="button button-hero" id="zd-si-reset">الرجوع إلى الصور المضمّنة</button>
		</p>
		<div class="zd-si__bar" hidden><span></span></div>
		<p class="zd-si__status" aria-live="polite"></p>
		<ul class="zd-si__grid" id="zd-si-grid"></ul>
	</div>
	<style>
		.zd-si__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-top:16px;max-width:1200px}
		.zd-si__grid li{margin:0;background:#fff;border:1px solid #dcdcde;border-radius:10px;overflow:hidden}
		.zd-si__grid img{display:block;width:100%;aspect-ratio:3/2;object-fit:cover;background:#0B2A21}
		.zd-si__grid p{margin:0;padding:8px 10px;display:flex;justify-content:space-between;font-size:12px}
		.zd-si__grid code{direction:ltr}
		.zd-si .ok{color:#1B7A4B;font-weight:600}.zd-si .bad{color:#A33A22;font-weight:600}
		.zd-si__bar{height:10px;background:#eee;border-radius:99px;overflow:hidden;max-width:640px}
		.zd-si__bar span{display:block;height:100%;width:0;background:#222;transition:width .3s}
		.zd-si .button-hero{margin-inline-end:8px}
	</style>
	<script>
	(function(){
		var C = <?php echo wp_json_encode( $config ); ?>;
		var $ = function(s){ return document.querySelector(s); };
		function esc(s){ return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
		function render(){
			$('#zd-si-grid').innerHTML = C.items.map(function(it){
				return '<li><img src="'+esc(it.thumb)+'" alt=""><p><code>'+esc(it.name)+'</code><span class="'+(it.imported?'ok':'')+'">'+(it.err?'<span class="bad">'+esc(it.err)+'</span>':(it.imported?'مستوردة':'مؤقتة'))+'</span></p></li>';
			}).join('');
		}
		function post(data){
			var body = new URLSearchParams(Object.assign({_ajax_nonce: C.nonce}, data));
			return fetch(C.ajax,{method:'POST',credentials:'same-origin',body:body}).then(function(r){return r.json();});
		}
		$('#zd-si-run').addEventListener('click', async function(){
			this.disabled = true; $('.zd-si__bar').hidden = false;
			var ok = 0;
			for (var i = 0; i < C.items.length; i++) {
				var it = C.items[i];
				$('.zd-si__status').textContent = 'جارٍ جلب ' + it.name + ' (' + (i+1) + ' من ' + C.items.length + ')…';
				try {
					var j = await post({action:'zad_site_image', name: it.name});
					if (j && j.success) { it.imported = true; it.thumb = j.data.thumb; it.err = ''; ok++; } else { it.err = (j && j.data) || 'تعذر الجلب'; }
				} catch(e) { it.err = 'انقطع الاتصال'; }
				$('.zd-si__bar span').style.width = ((i+1)/C.items.length*100) + '%';
				render();
			}
			$('.zd-si__status').textContent = 'اكتمل: ' + ok + ' من ' + C.items.length + ' صورة.' + (ok < C.items.length ? ' أعد المحاولة للصور المتبقية.' : '');
			this.disabled = false;
		});
		$('#zd-si-reset').addEventListener('click', function(){
			if (!confirm('حذف الصور المستوردة والرجوع إلى الصور المضمّنة في القالب؟')) { return; }
			post({action:'zad_site_images_reset'}).then(function(){ location.reload(); });
		});
		render();
	})();
	</script>
	<?php
}

add_action(
	'wp_ajax_zad_site_image',
	static function () {
		check_ajax_referer( 'zad_site_images' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'غير مسموح', 403 );
		}
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 180 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		$name = isset( $_POST['name'] ) ? sanitize_key( wp_unslash( $_POST['name'] ) ) : '';
		$r    = zad_site_image_import( $name );
		if ( is_wp_error( $r ) ) {
			wp_send_json_error( $r->get_error_message() );
		}
		wp_send_json_success( array( 'thumb' => zad_img_url( $name, true ) ) );
	}
);

add_action(
	'wp_ajax_zad_site_images_reset',
	static function () {
		check_ajax_referer( 'zad_site_images' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'غير مسموح', 403 );
		}
		zad_site_images_reset();
		wp_send_json_success();
	}
);

/* -------------------------------------------------------------------------
 * WP-CLI: wp zad site-images [--only=hero,cat-cake] [--reset]
 * ---------------------------------------------------------------------- */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'zad site-images',
		static function ( $args, $assoc ) {
			if ( ! empty( $assoc['reset'] ) ) {
				zad_site_images_reset();
				WP_CLI::success( 'حُذفت الصور المستوردة، وعادت الواجهة إلى الصور المضمّنة.' );
				return;
			}
			$names = array_keys( zad_site_image_sources() );
			if ( ! empty( $assoc['only'] ) ) {
				$names = array_intersect( $names, array_map( 'sanitize_key', explode( ',', $assoc['only'] ) ) );
			}
			$ok = 0;
			foreach ( $names as $name ) {
				$r = zad_site_image_import( $name );
				if ( is_wp_error( $r ) ) {
					WP_CLI::warning( $name . ': ' . $r->get_error_message() );
				} else {
					++$ok;
					WP_CLI::log( '✓ ' . $name );
				}
			}
			WP_CLI::success( sprintf( 'استُوردت %d من %d صورة.', $ok, count( $names ) ) );
		},
		array( 'shortdesc' => 'جلب صور موقع بسكاتو وتحويلها إلى WebP.' )
	);
}
