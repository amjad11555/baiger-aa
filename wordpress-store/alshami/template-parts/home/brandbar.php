<?php
/**
 * الواجهة الرئيسية: شريط العلامات الموجودة في التشكيلة (أسماء مكتوبة، دون شعارات).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_brands = array_filter(
	shami_all_brands(),
	static function ( $b ) {
		return $b['count'] > 0;
	}
);
if ( ! $sh_brands ) {
	return;
}
?>
<section class="sh-brandbar" aria-label="العلامات في تشكيلتنا">
	<div class="sh-container sh-brandbar__row">
		<p class="sh-brandbar__label">علامات في تشكيلتنا</p>
		<ul class="sh-brandbar__list">
			<?php foreach ( $sh_brands as $sh_b ) : ?>
				<li><a href="<?php echo esc_url( $sh_b['url'] ); ?>"><span class="sh-brandbar__word" lang="tr"><?php echo esc_html( $sh_b['latin'] ); ?></span><span class="sh-brandbar__meta"><?php echo esc_html( $sh_b['ar'] . ' · ' . shami_n_items( $sh_b['count'] ) ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>
		<a class="sh-link" href="<?php echo esc_url( shami_page_url( 'brands' ) ); ?>">كل العلامات</a>
	</div>
</section>
