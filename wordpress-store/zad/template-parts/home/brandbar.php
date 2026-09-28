<?php
/**
 * الواجهة الرئيسية: شريط العلامات بأسلوب Kalles (أسماء مكتوبة رمادية تصبح سوداء عند المرور، دون شعارات).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_brands = array_filter(
	zad_all_brands(),
	static function ( $b ) {
		return $b['count'] > 0;
	}
);
if ( ! $zd_brands ) {
	return;
}
?>
<section class="zd-brandbar" aria-label="العلامات في تشكيلتنا">
	<div class="zd-container">
		<ul class="zd-brandbar__list">
			<?php foreach ( $zd_brands as $zd_b ) : ?>
				<li><a href="<?php echo esc_url( $zd_b['url'] ); ?>"><span class="zd-brandbar__word" lang="tr"><?php echo esc_html( $zd_b['latin'] ); ?></span><span class="zd-brandbar__meta"><?php echo esc_html( $zd_b['ar'] . ' · ' . zad_n_items( $zd_b['count'] ) ); ?></span></a></li>
			<?php endforeach; ?>
			<li><a href="<?php echo esc_url( zad_page_url( 'brands' ) ); ?>"><span class="zd-brandbar__word">+</span><span class="zd-brandbar__meta">كل العلامات</span></a></li>
		</ul>
	</div>
</section>
