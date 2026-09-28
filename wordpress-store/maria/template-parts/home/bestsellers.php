<?php
/**
 * الواجهة الرئيسية: الأكثر مبيعاً (تبويبات حسب القسم وشرائط منتجات).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_tabs = array_intersect_key( maria_categories(), array_flip( array( 'cake', 'biscuits', 'chips', 'snacks' ) ) );
?>
<section class="mr-section mr-best" aria-labelledby="mr-best-title">
	<div class="mr-container">
		<div class="mr-section__head">
			<h2 class="mr-section__title" id="mr-best-title">الأكثر مبيعاً</h2>
		</div>
		<div class="mr-tabs mr-tabs--line" role="tablist" aria-label="أقسام الأكثر مبيعاً">
			<?php $mr_first = true; ?>
			<?php foreach ( $mr_tabs as $mr_slug => $mr_cat ) : ?>
				<button type="button" class="mr-tab<?php echo $mr_first ? ' is-active' : ''; ?>" role="tab" id="mr-tab-<?php echo esc_attr( $mr_slug ); ?>" aria-controls="mr-panel-<?php echo esc_attr( $mr_slug ); ?>" aria-selected="<?php echo $mr_first ? 'true' : 'false'; ?>" tabindex="<?php echo $mr_first ? '0' : '-1'; ?>"><?php echo esc_html( $mr_cat['name'] ); ?></button>
				<?php $mr_first = false; ?>
			<?php endforeach; ?>
		</div>
		<?php $mr_first = true; ?>
		<?php foreach ( $mr_tabs as $mr_slug => $mr_cat ) : ?>
			<div class="mr-tab-panel" role="tabpanel" id="mr-panel-<?php echo esc_attr( $mr_slug ); ?>" aria-labelledby="mr-tab-<?php echo esc_attr( $mr_slug ); ?>"<?php echo $mr_first ? '' : ' hidden'; ?>>
				<?php
				maria_product_rail(
					array(
						'category' => array( $mr_slug ),
						'limit'    => 12,
						'orderby'  => 'menu_order',
						'order'    => 'ASC',
					)
				);
				?>
				<p class="mr-center"><a class="mr-btn mr-btn--ghost" href="<?php echo esc_url( maria_cat_url( $mr_slug ) ); ?>">كل <?php echo esc_html( $mr_cat['name'] ); ?> (<?php echo esc_html( maria_n_items( maria_cat_count( $mr_slug ) ) ); ?>)</a></p>
			</div>
			<?php $mr_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
