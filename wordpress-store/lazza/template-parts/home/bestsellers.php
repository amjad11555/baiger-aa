<?php
/**
 * الواجهة الرئيسية: الأكثر طلباً (تبويبات حسب القسم).
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_tabs = array_intersect_key( lazza_categories(), array_flip( array( 'cake', 'biscuits', 'chips', 'snacks' ) ) );
?>
<section class="lz-section lz-best" aria-labelledby="lz-best-title">
	<div class="lz-container">
		<div class="lz-section__head lz-section__head--row">
			<div>
				<span class="lz-kicker"><?php lazza_the_icon( 'star', '', 16 ); ?> الأكثر دوراناً على الرفوف</span>
				<h2 class="lz-section__title" id="lz-best-title">منتجات يطلبها زبائن بقالتك يومياً</h2>
			</div>
			<div class="lz-tabs" role="tablist" aria-label="الأقسام">
				<?php $lz_first = true; ?>
				<?php foreach ( $lz_tabs as $lz_slug => $lz_cat ) : ?>
					<button type="button" class="lz-tab<?php echo $lz_first ? ' is-active' : ''; ?>" role="tab" id="lz-tab-<?php echo esc_attr( $lz_slug ); ?>" aria-controls="lz-panel-<?php echo esc_attr( $lz_slug ); ?>" aria-selected="<?php echo $lz_first ? 'true' : 'false'; ?>" tabindex="<?php echo $lz_first ? '0' : '-1'; ?>" style="--c:<?php echo esc_attr( $lz_cat['color'] ); ?>">
						<?php lazza_the_icon( $lz_cat['icon'], '', 18 ); ?> <?php echo esc_html( $lz_cat['name'] ); ?>
					</button>
					<?php $lz_first = false; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php $lz_first = true; ?>
		<?php foreach ( $lz_tabs as $lz_slug => $lz_cat ) : ?>
			<div class="lz-tab-panel" role="tabpanel" id="lz-panel-<?php echo esc_attr( $lz_slug ); ?>" aria-labelledby="lz-tab-<?php echo esc_attr( $lz_slug ); ?>"<?php echo $lz_first ? '' : ' hidden'; ?>>
				<?php
				lazza_product_grid(
					array(
						'category' => array( $lz_slug ),
						'limit'    => 8,
						'orderby'  => 'menu_order',
						'order'    => 'ASC',
					)
				);
				?>
				<p class="lz-center"><a class="lz-btn lz-btn--ghost" href="<?php echo esc_url( lazza_cat_url( $lz_slug ) ); ?>">عرض كل <?php echo esc_html( $lz_cat['name'] ); ?> (<?php echo esc_html( lazza_n_items( lazza_cat_count( $lz_slug ) ) ); ?>) <?php lazza_the_icon( 'arrow-left', '', 18 ); ?></a></p>
			</div>
			<?php $lz_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
