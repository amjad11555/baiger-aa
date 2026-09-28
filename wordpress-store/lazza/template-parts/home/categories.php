<?php
/**
 * الواجهة الرئيسية: الأقسام.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_samples = array(
	'cake'     => 'ETI-POP-CHO',
	'biscuits' => 'ULK-HAL-CLS',
	'chips'    => 'ETI-CRX-CHE',
	'snacks'   => 'ULK-ALB-CLS',
	'offers'   => 'LZ-BND-CAKE',
);
?>
<section class="lz-section lz-cats" aria-labelledby="lz-cats-title">
	<div class="lz-container">
		<div class="lz-section__head">
			<span class="lz-kicker"><?php lazza_the_icon( 'grid', '', 16 ); ?> تسوّق حسب القسم</span>
			<h2 class="lz-section__title" id="lz-cats-title">أقسام متجر الجملة</h2>
			<p class="lz-section__sub">كل ما يحتاجه رف الحلويات في بقالتك مرتب في خمسة أقسام واضحة.</p>
		</div>
		<div class="lz-cats__grid">
			<?php foreach ( lazza_categories() as $lz_slug => $lz_cat ) : ?>
				<?php
				$lz_art = '';
				if ( function_exists( 'wc_get_product_id_by_sku' ) && isset( $lz_samples[ $lz_slug ] ) ) {
					$lz_pid = wc_get_product_id_by_sku( $lz_samples[ $lz_slug ] );
					$lz_art = $lz_pid ? wc_get_product( $lz_pid )->get_image( 'woocommerce_thumbnail' ) : '';
				}
				?>
				<a class="lz-cat-tile lz-cat-tile--<?php echo esc_attr( $lz_slug ); ?> lz-reveal" href="<?php echo esc_url( lazza_cat_url( $lz_slug ) ); ?>" style="--c:<?php echo esc_attr( $lz_cat['color'] ); ?>;--t:<?php echo esc_attr( $lz_cat['tint'] ); ?>">
					<span class="lz-cat-tile__icon"><?php lazza_the_icon( $lz_cat['icon'], '', 30 ); ?></span>
					<span class="lz-cat-tile__name"><?php echo esc_html( $lz_cat['name'] ); ?></span>
					<span class="lz-cat-tile__line"><?php echo esc_html( $lz_cat['line'] ); ?></span>
					<span class="lz-cat-tile__count"><?php echo esc_html( lazza_n_items( lazza_cat_count( $lz_slug ) ) ); ?></span>
					<?php if ( $lz_art ) : ?>
						<span class="lz-cat-tile__art" aria-hidden="true"><?php echo $lz_art; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
					<span class="lz-cat-tile__go" aria-hidden="true"><?php lazza_the_icon( 'arrow-left', '', 20 ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
