<?php
/**
 * الواجهة الرئيسية: شريط العلامات بأسلوب Kalles (الشعار إن توفّر، وإلا الاسم مكتوباً بالرمادي ويصبح أسود عند المرور).
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
// أكبر عشر علامات في التشكيلة، والباقي في صفحة «كل العلامات».
uasort(
	$zd_brands,
	static function ( $a, $b ) {
		return $b['count'] <=> $a['count'];
	}
);
$zd_brands = array_slice( $zd_brands, 0, 10, true );
?>
<section class="zd-brandbar" aria-label="العلامات في تشكيلتنا">
	<div class="zd-container">
		<ul class="zd-brandbar__list">
			<?php foreach ( $zd_brands as $zd_b ) : ?>
				<?php $zd_logo = zad_brand_logo( $zd_b, 40 ); ?>
				<li><a href="<?php echo esc_url( $zd_b['url'] ); ?>"><?php if ( $zd_logo ) : ?><span class="zd-brandbar__logo"><?php echo $zd_logo; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php else : ?><span class="zd-brandbar__word" lang="tr"><?php echo esc_html( $zd_b['latin'] ); ?></span><?php endif; ?><span class="zd-brandbar__meta"><?php echo esc_html( $zd_b['ar'] . ' · ' . zad_n_items( $zd_b['count'] ) ); ?></span></a></li>
			<?php endforeach; ?>
			<li><a href="<?php echo esc_url( zad_page_url( 'brands' ) ); ?>"><span class="zd-brandbar__word">+</span><span class="zd-brandbar__meta">كل العلامات</span></a></li>
		</ul>
	</div>
</section>
