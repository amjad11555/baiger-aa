<?php
/**
 * صور الموقع الاحترافية: الصور العشرون المصممة لزاد (Nano Banana Pro)
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
	$base = 'https://d8j0ntlcm91z4.cloudfront.net/user_3HMjZPgpKCsHgo8BLC9DovmF1SX/hf_20260928_';
	$ids  = array(
		'hero'            => array( '150213_fd44b848-7fd0-42b4-b8c5-6db3da86fc8f', 2400 ),
		'hero-m'          => array( '150214_d8b8f104-2f8b-4733-9608-ec17b64c6604', 1100 ),
		'cat-cake'        => array( '150213_092c504a-1fb9-401d-b3ff-1f1561534176', 900 ),
		'cat-biscuits'    => array( '150216_13844631-9953-4c55-9701-a0390cc1a15d', 900 ),
		'cat-chips'       => array( '150215_37026964-f3cc-4b8b-8ba7-87dcc4fa2ecd', 900 ),
		'cat-snacks'      => array( '150536_e8ac65f8-06ce-4a66-b7c4-b398be90719c', 900 ),
		'cat-offers'      => array( '150218_7ae267f2-59f3-4364-afc6-7d83f04bff89', 900 ),
		'seg-supermarket' => array( '150213_cd9346d5-60a2-45de-ad66-e59d1bb4ab17', 1200 ),
		'seg-grocery'     => array( '150214_b3500843-63de-4887-aef8-c8379eb20975', 1200 ),
		'seg-distributor' => array( '150212_a3db1fa3-6cb1-4f56-b0c2-2f699c85c168', 1200 ),
		'seg-export'      => array( '150214_c175752c-52c2-4a6d-ad85-360f477ae863', 1600 ),
		'step-order'      => array( '150219_f818c341-fd83-4642-9f5e-67f03bbd8b07', 1000 ),
		'step-pick'       => array( '150536_9b1ab5f9-d6d3-44d6-8bef-e993044e40e6', 1000 ),
		'step-deliver'    => array( '150536_32140017-8135-4b0e-9ab5-e20e99c64e4d', 1000 ),
		'about-team'      => array( '150538_ad505396-ad3d-4175-94b0-7ab444e4561e', 1600 ),
		'sourcing'        => array( '150536_d05e6213-008a-4dad-b0b2-9ef8904d5806', 1400 ),
		'quality'         => array( '150536_6071e71b-d1c0-478f-99ee-727398dd950d', 1000 ),
		'cta-docks'       => array( '150536_91d9b086-b145-4130-bc14-05928feb6ba3', 2400 ),
		'texture'         => array( '150537_e4147041-fa62-4bdc-a139-216b13afb610', 1600 ),
		'flatlay'         => array( '150538_e4b8fd25-b7cf-4967-a687-1628c8eced43', 1800 ),
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
 * رابط النسخة المستوردة إن وُجدت.
 *
 * @param string $name  الاسم.
 * @param bool   $small النسخة الصغيرة.
 * @return string رابط أو فارغ.
 */
function zad_site_image_imported_url( $name, $small = false ) {
	$stored = zad_site_images_stored();
	if ( empty( $stored[ $name ] ) ) {
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
	);
	update_option( 'zad_site_images', $stored, false );
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
 * لوحة التحكم: المظهر ← صور موقع زاد
 * ---------------------------------------------------------------------- */

/**
 * القائمة.
 */
function zad_site_images_menu() {
	add_theme_page( 'صور موقع زاد', 'صور الموقع', 'manage_options', 'zad-site-images', 'zad_site_images_page' );
}
add_action( 'admin_menu', 'zad_site_images_menu' );

/**
 * تنبيه لمرة واحدة حتى تُجلب الصور.
 */
function zad_site_images_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || zad_site_images_stored() || ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'appearance_page_zad-setup' ), true ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p><strong>صور موقع زاد جاهزة للجلب.</strong> عشرون صورة احترافية مصممة للواجهة والأقسام والصفحات، تُجلب إلى استضافتك بضغطة واحدة. <a class="button button-primary" href="%s" style="margin-inline-start:8px">جلب الصور الآن</a></p></div>',
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
			'imported' => (bool) zad_site_image_imported_url( $name ),
		);
	}
	$config = array(
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'zad_site_images' ),
		'items' => $items,
	);
	?>
	<div class="wrap zd-si" dir="rtl">
		<h1>صور موقع زاد</h1>
		<p>عشرون صورة مصممة خصيصاً لهوية زاد: واجهة الصفحة الرئيسية، والأقسام، وشرائح العملاء، وخطوات الطلب، وصفحات الشركة. تجلب هذه الأداة الصور من مصدرها مباشرة إلى استضافتك، وتحوّلها إلى WebP بمقاسين للجوال والشاشات الكبيرة، ثم تستخدمها الواجهة تلقائياً بدل الصور المؤقتة المضمّنة في القالب.</p>
		<p>
			<button type="button" class="button button-primary button-hero" id="zd-si-run">جلب الصور العشرين</button>
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
		array( 'shortdesc' => 'جلب صور موقع زاد العشرين وتحويلها إلى WebP.' )
	);
}
