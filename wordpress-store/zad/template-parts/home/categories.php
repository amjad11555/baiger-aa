<?php
/**
 * الواجهة الرئيسية: بطاقات الأقسام بأسلوب Kalles (صورة كبيرة + أربع صور، وعلى كل صورة زر أبيض باسم القسم).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_cats = zad_categories();
?>
<section class="zd-section zd-collections" aria-labelledby="zd-cats-title">
	<div class="zd-container">
		<?php zad_section_head( 'تسوّق حسب القسم', 'خمسة أقسام تغطي طلب زبائنك اليومي، بسعر الكرتونة', 'zd-cats-title' ); ?>
		<ul class="zd-collections__grid">
			<?php $zd_i = 0; ?>
			<?php foreach ( $zd_cats as $zd_slug => $zd_cat ) : ?>
				<li class="zd-collection<?php echo 0 === $zd_i ? ' zd-collection--big' : ''; ?>">
					<a href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>">
						<span class="zd-collection__media"><?php echo zad_img( $zd_cat['image'], '', array( 'sizes' => 0 === $zd_i ? '(min-width: 1024px) 40vw, 100vw' : '(min-width: 1024px) 25vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="zd-collection__label"><?php echo esc_html( $zd_cat['title'] ); ?><small><?php echo esc_html( zad_n_items( zad_cat_count( $zd_slug ) ) ); ?></small></span>
					</a>
				</li>
				<?php ++$zd_i; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
