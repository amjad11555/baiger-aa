<?php
/**
 * الواجهة الرئيسية: رسالة «نلبي كل احتياجات محلك» مع زر للتواصل.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_pitch = zad_pitch_html( 'home' );
if ( ! $zd_pitch ) {
	return;
}
?>
<div class="zd-section zd-section--tight">
	<div class="zd-container"><?php echo $zd_pitch; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
</div>
