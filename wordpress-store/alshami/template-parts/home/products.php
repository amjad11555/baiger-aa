<?php
/**
 * الواجهة الرئيسية: الأصناف الأعلى دوراناً (تبويب لكل قسم وشبكة منتجات).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_tabs = array_intersect_key( shami_categories(), array_flip( array( 'cake', 'biscuits', 'chips', 'snacks' ) ) );
?>
<section class="sh-section sh-section--paper sh-best" aria-labelledby="sh-best-title">
	<div class="sh-container">
		<div class="sh-head">
			<div class="sh-head__text">
				<p class="sh-eyebrow">الأعلى دوراناً</p>
				<h2 class="sh-head__title" id="sh-best-title">أصناف يطلبها زبائن متجرك باستمرار</h2>
			</div>
			<a class="sh-link" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">قائمة الأسعار الكاملة</a>
		</div>
		<div class="sh-tabs" role="tablist" aria-label="أقسام الأصناف">
			<?php $sh_first = true; ?>
			<?php foreach ( $sh_tabs as $sh_slug => $sh_cat ) : ?>
				<button type="button" class="sh-tab<?php echo $sh_first ? ' is-active' : ''; ?>" role="tab" id="sh-tab-<?php echo esc_attr( $sh_slug ); ?>" aria-controls="sh-panel-<?php echo esc_attr( $sh_slug ); ?>" aria-selected="<?php echo $sh_first ? 'true' : 'false'; ?>" tabindex="<?php echo $sh_first ? '0' : '-1'; ?>"><?php echo esc_html( $sh_cat['title'] ); ?></button>
				<?php $sh_first = false; ?>
			<?php endforeach; ?>
		</div>
		<?php $sh_first = true; ?>
		<?php foreach ( $sh_tabs as $sh_slug => $sh_cat ) : ?>
			<div class="sh-tab-panel" role="tabpanel" id="sh-panel-<?php echo esc_attr( $sh_slug ); ?>" aria-labelledby="sh-tab-<?php echo esc_attr( $sh_slug ); ?>"<?php echo $sh_first ? '' : ' hidden'; ?>>
				<?php
				shami_product_grid(
					array(
						'category' => array( $sh_slug ),
						'limit'    => 8,
						'orderby'  => 'menu_order',
						'order'    => 'ASC',
					),
					'sh-grid--home'
				);
				?>
				<p class="sh-center"><a class="sh-btn sh-btn--ghost" href="<?php echo esc_url( shami_cat_url( $sh_slug ) ); ?>">كل أصناف <?php echo esc_html( $sh_cat['title'] ); ?> · <?php echo esc_html( shami_n_items( shami_cat_count( $sh_slug ) ) ); ?></a></p>
			</div>
			<?php $sh_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
