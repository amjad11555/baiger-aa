<?php
/**
 * مجموعة أيقونات SVG خفيفة (خطية) خاصة بالقالب.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

/**
 * سجل مسارات الأيقونات.
 *
 * @return array
 */
function zad_icon_registry() {
	static $icons = null;
	if ( null === $icons ) {
		$icons = array(
			'bag'          => '<path d="M5 8h14l-1.2 12H6.2z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/>',
		'filter'       => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
		'cols-1'       => '<rect x="5" y="4" width="14" height="16" rx="1"/>',
		'cols-2'       => '<rect x="4" y="4" width="7" height="16" rx="1"/><rect x="13" y="4" width="7" height="16" rx="1"/>',
		'cols-3'       => '<rect x="3" y="4" width="4.6" height="16" rx="1"/><rect x="9.7" y="4" width="4.6" height="16" rx="1"/><rect x="16.4" y="4" width="4.6" height="16" rx="1"/>',
		'cols-4'       => '<rect x="2.5" y="4" width="3.6" height="16" rx=".8"/><rect x="7.8" y="4" width="3.6" height="16" rx=".8"/><rect x="13" y="4" width="3.6" height="16" rx=".8"/><rect x="18.2" y="4" width="3.6" height="16" rx=".8"/>',
		'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
			'cart'         => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2.5 3h2.2l2.4 11.2a2 2 0 0 0 2 1.6h8.3a2 2 0 0 0 1.9-1.5L21 7H6"/>',
			'menu'         => '<path d="M4 6h16M4 12h16M4 18h10"/>',
			'close'        => '<path d="M18 6 6 18M6 6l12 12"/>',
			'plus'         => '<path d="M12 5v14M5 12h14"/>',
			'minus'        => '<path d="M5 12h14"/>',
			'phone'        => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
			'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
			'pin'          => '<path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
			'bell'         => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
			'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
			'truck'        => '<path d="M3 6h11v10H3zM14 9h4l3 3v4h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
			'box'          => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
			'tag'          => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
			'fire'         => '<path d="M12 22c4 0 7-2.8 7-6.8 0-3.4-2.3-5.6-3.6-7.2-.4 1.8-1.4 3-2.6 3.4.5-3.2-.8-6.5-3.8-8.4.2 3-1.3 5-3 6.9C4.6 11.4 5 13 5 15.2 5 19.2 8 22 12 22z"/>',
			'bolt'         => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
			'check'        => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
			'arrow-left'   => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
			'arrow-right'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
			'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
			'globe'        => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
			'ship'         => '<path d="M3 16.5 5 21h14l2-4.5-9-3.5z"/><path d="M5 15V9h14v6M9 9V5h6v4M12 5V2.5"/>',
			'file'         => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
			'star'         => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9z"/>',
			'user'         => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
			'home'         => '<path d="M3 11 12 3l9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
			'grid'         => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
			'list'         => '<path d="M9 6h12M9 12h12M9 18h12"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/>',
			'print'        => '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M6 14h12v7H6z"/>',
			'trash'        => '<path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/>',
			'gift'         => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M5 12v9h14v-9M12 8v13"/><path d="M12 8S10 3 7.5 3.8C5.5 4.5 6.5 8 12 8zM12 8s2-5 4.5-4.2C18.5 4.5 17.5 8 12 8z"/>',
			'shield'       => '<path d="M12 3 4 6v6c0 5 3.5 8.5 8 9.5 4.5-1 8-4.5 8-9.5V6z"/><path d="m9 12 2 2 4-4"/>',
			'sparkle'      => '<path d="M12 3c.6 4.2 2.8 6.4 7 7-4.2.6-6.4 2.8-7 7-.6-4.2-2.8-6.4-7-7 4.2-.6 6.4-2.8 7-7z"/><path d="M19 3v4M17 5h4"/>',
			'upload'       => '<path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>',
			'send'         => '<path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/>',
			'percent'      => '<path d="M19 5 5 19"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>',
			'cake'         => '<path d="M4 21h16v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2z"/><path d="M4 16c1.5 1 2.5 1 4 0s2.5-1 4 0 2.5 1 4 0 2.5-1 4 0"/><path d="M12 11V8"/><path d="M12 5.5c.8 0 1.3-.7 1.3-1.4S12 2 12 2s-1.3 1.4-1.3 2.1.5 1.4 1.3 1.4z"/>',
			'cookie'       => '<circle cx="12" cy="12" r="9"/><circle cx="9" cy="9" r=".8"/><circle cx="15" cy="9.5" r=".8"/><circle cx="9.5" cy="15" r=".8"/><circle cx="15.5" cy="15" r=".8"/><circle cx="12.3" cy="12.2" r=".8"/>',
			'chips'        => '<path d="M6 3h12l-1 3c1.5 4 1.5 8 0 12l1 3H6l1-3c-1.5-4-1.5-8 0-12z"/><path d="M9.5 10.5c1.5-1 3.5-1 5 0M9.5 14c1.5 1 3.5 1 5 0"/>',
			'candy'        => '<rect x="7" y="8" width="10" height="8" rx="4"/><path d="M7 12 3 8.5v7zM17 12l4-3.5v7z"/>',
			'instagram'    => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8"/>',
			'facebook'     => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v7h4v-7h3l1-4h-4V8.5c0-.3.2-.5.5-.5z"/>',
			'tiktok'       => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5M14 3c.5 2.5 2.5 4.5 5 5"/>',
			'telegram'     => '<path d="M21 4 3 11l6 2 2 6 3-4 5 4z"/><path d="m9 13 12-9"/>',
			'youtube'      => '<rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3z"/>',
			'share'        => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
			'copy'         => '<rect x="8" y="8" width="13" height="13" rx="2"/><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"/>',
			'store'        => '<path d="M3 9 4.5 4h15L21 9"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 11.5V20h14v-8.5M10 20v-5h4v5"/>',
			'wallet'       => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18M16 15h2"/><path d="M6 6V4.5A1.5 1.5 0 0 1 7.5 3h11"/>',
			'whatsapp'     => '<path d="M12 2.6a9.4 9.4 0 0 0-8.1 14.2L2.6 21.4l4.7-1.2A9.4 9.4 0 1 0 12 2.6z"/><path fill="currentColor" stroke="none" d="M8.7 7.5c.2-.4.5-.4.8-.4h.6c.2 0 .4.1.5.4l.8 1.9c.1.2 0 .5-.1.7l-.6.7c-.1.2-.1.4 0 .6.6 1 1.4 1.9 2.4 2.5.2.1.4.1.6 0l.7-.8c.2-.2.4-.2.7-.1l1.8.9c.3.1.4.3.4.6 0 .5-.2 1.2-.7 1.6-.6.5-1.5.7-2.5.4-1.6-.5-3.1-1.5-4.3-2.8-1-1.1-1.8-2.4-2-3.7-.1-.9.2-1.8.6-2.5z"/>',
			'heart'        => '<path d="M12 20.5s-7.5-4.6-9.2-9.3C1.6 7.8 3.8 4.5 7.2 4.5c2 0 3.6 1.1 4.8 2.8 1.2-1.7 2.8-2.8 4.8-2.8 3.4 0 5.6 3.3 4.4 6.7C19.5 15.9 12 20.5 12 20.5Z"/>',
			'eye'          => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
			'arrow-up'     => '<path d="m6 15 6-6 6 6"/>',
		);
	}
	return $icons;
}

/**
 * إرجاع أيقونة SVG.
 *
 * @param string $name  اسم الأيقونة.
 * @param string $class كلاس إضافي.
 * @param int    $size  الحجم.
 * @return string
 */
function zad_icon( $name, $class = '', $size = 24 ) {
	$icons = zad_icon_registry();
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	$attrs = sprintf(
		'class="zd-i zd-i-%1$s %2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"',
		esc_attr( $name ),
		esc_attr( $class ),
		(int) $size
	);
	// في الواجهة: مرجع إلى Sprite يُطبع مرة واحدة في التذييل (صفحات أخف بكثير).
	$is_page = ! is_admin() && ! wp_doing_ajax() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST );
	if ( $is_page && ! did_action( 'zad_icon_sprite_printed' ) ) {
		$GLOBALS['zad_icons_used'][ $name ] = true;
		// الخصائص (fill/stroke) معرّفة في CSS عبر .zd-i لتقليل حجم الصفحة.
		return sprintf( '<svg class="zd-i zd-i-%1$s%2$s" width="%3$d" height="%3$d" aria-hidden="true"><use href="#zdi-%1$s"/></svg>', esc_attr( $name ), $class ? ' ' . esc_attr( $class ) : '', (int) $size );
	}
	return '<svg ' . $attrs . '>' . $icons[ $name ] . '</svg>';
}

/**
 * طباعة Sprite الأيقونات المستخدمة في الصفحة.
 */
function zad_icon_sprite() {
	$used = isset( $GLOBALS['zad_icons_used'] ) ? array_keys( $GLOBALS['zad_icons_used'] ) : array();
	do_action( 'zad_icon_sprite_printed' );
	// مجموعة أساسية دائماً (لأجزاء HTML التي يستبدلها ووكومرس عبر AJAX).
	$used = array_unique( array_merge( $used, array( 'plus', 'minus', 'check', 'close', 'cart', 'whatsapp', 'bolt', 'box', 'fire', 'tag', 'trash', 'search', 'arrow-left', 'list', 'store', 'grid', 'heart', 'eye' ) ) );
	$all = zad_icon_registry();
	echo '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">';
	foreach ( $used as $name ) {
		if ( isset( $all[ $name ] ) ) {
			echo '<symbol id="zdi-' . esc_attr( $name ) . '" viewBox="0 0 24 24">' . $all[ $name ] . '</symbol>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
	echo '</svg>';
}
add_action( 'wp_footer', 'zad_icon_sprite', 6 );

/**
 * طباعة أيقونة.
 *
 * @param string $name  الاسم.
 * @param string $class كلاس.
 * @param int    $size  الحجم.
 */
function zad_the_icon( $name, $class = '', $size = 24 ) {
	echo zad_icon( $name, $class, $size ); // phpcs:ignore WordPress.Security.EscapeOutput
}
