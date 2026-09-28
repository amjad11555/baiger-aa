<?php
/**
 * الواجهة الرئيسية: الأكثر طلباً + الباقات الموفّرة (تبويبان وشرائط منتجات).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_tabs = array(
	'hot'     => array(
		'الأكثر طلباً',
		array(
			'featured' => true,
			'limit'    => 12,
		),
	),
	'bundles' => array(
		'باقات موفّرة',
		array(
			'category' => array( 'offers' ),
			'limit'    => 12,
		),
	),
);
?>
<section class="mr-section mr-picks" aria-label="مختارات ماريا">
	<div class="mr-container">
		<div class="mr-tabs mr-tabs--center" role="tablist" aria-label="مختارات ماريا">
			<?php $mr_first = true; ?>
			<?php foreach ( $mr_tabs as $mr_key => $mr_tab ) : ?>
				<button type="button" class="mr-tab<?php echo $mr_first ? ' is-active' : ''; ?>" role="tab" id="mr-tab-<?php echo esc_attr( $mr_key ); ?>" aria-controls="mr-panel-<?php echo esc_attr( $mr_key ); ?>" aria-selected="<?php echo $mr_first ? 'true' : 'false'; ?>" tabindex="<?php echo $mr_first ? '0' : '-1'; ?>"><?php echo esc_html( $mr_tab[0] ); ?></button>
				<?php $mr_first = false; ?>
			<?php endforeach; ?>
		</div>
		<?php $mr_first = true; ?>
		<?php foreach ( $mr_tabs as $mr_key => $mr_tab ) : ?>
			<div class="mr-tab-panel" role="tabpanel" id="mr-panel-<?php echo esc_attr( $mr_key ); ?>" aria-labelledby="mr-tab-<?php echo esc_attr( $mr_key ); ?>"<?php echo $mr_first ? '' : ' hidden'; ?>>
				<?php
				maria_product_rail(
					array_merge(
						array(
							'orderby' => 'menu_order',
							'order'   => 'ASC',
						),
						$mr_tab[1]
					)
				);
				?>
			</div>
			<?php $mr_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
