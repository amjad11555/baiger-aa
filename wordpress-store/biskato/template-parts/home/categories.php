<?php
/**
 * الواجهة الرئيسية: الأقسام التسعة (صورة مربعة واسم القسم وعدد الأصناف تحتها).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_cats = zad_categories();
?>
<section class="zd-section zd-collections" aria-labelledby="zd-cats-title">
	<div class="zd-container">
		<?php zad_section_head( 'تسوّق حسب القسم', 'كل ما يحتاجه رف محلك من الكيك إلى العلكة، بسعر الجملة', 'zd-cats-title' ); ?>
		<ul class="zd-collections__grid">
			<?php foreach ( $zd_cats as $zd_slug => $zd_cat ) : ?>
				<li class="zd-collection">
					<a href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>">
						<span class="zd-collection__media" style="background:<?php echo esc_attr( $zd_cat['tint'] ); ?>"><?php echo zad_img( $zd_cat['image'], '', array( 'sizes' => '(min-width: 900px) 140px, 31vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="zd-collection__label"><?php echo esc_html( $zd_cat['title'] ); ?><small><?php echo esc_html( zad_n_items( zad_cat_count( $zd_slug ) ) ); ?></small></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
