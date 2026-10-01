<?php
/**
 * الواجهة الرئيسية: «الأعلى دوراناً» بأسلوب Kalles (عنوان في الوسط، تبويبات نصية، شبكة أربعة أعمدة، وزر «المزيد»).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_tabs = array_intersect_key( zad_categories(), array_flip( array( 'cake', 'biscuits', 'chips', 'snacks' ) ) );
?>
<section class="zd-section zd-best" aria-labelledby="zd-best-title">
	<div class="zd-container">
		<?php zad_section_head( 'الأعلى دوراناً', 'أصناف يطلبها زبائن متجرك كل أسبوع', 'zd-best-title' ); ?>
		<div class="zd-tabs" role="tablist" aria-label="أقسام الأصناف">
			<?php $zd_first = true; ?>
			<?php foreach ( $zd_tabs as $zd_slug => $zd_cat ) : ?>
				<button type="button" class="zd-tab<?php echo $zd_first ? ' is-active' : ''; ?>" role="tab" id="zd-tab-<?php echo esc_attr( $zd_slug ); ?>" aria-controls="zd-panel-<?php echo esc_attr( $zd_slug ); ?>" aria-selected="<?php echo $zd_first ? 'true' : 'false'; ?>" tabindex="<?php echo $zd_first ? '0' : '-1'; ?>"><?php echo esc_html( $zd_cat['title'] ); ?></button>
				<?php $zd_first = false; ?>
			<?php endforeach; ?>
		</div>
		<?php $zd_first = true; ?>
		<?php foreach ( $zd_tabs as $zd_slug => $zd_cat ) : ?>
			<div class="zd-tab-panel" role="tabpanel" id="zd-panel-<?php echo esc_attr( $zd_slug ); ?>" aria-labelledby="zd-tab-<?php echo esc_attr( $zd_slug ); ?>"<?php echo $zd_first ? '' : ' hidden'; ?>>
				<?php
				// التبويبات المخفية داخل <template>: لا تُرسم ولا تُحمَّل صورها حتى يفتحها الزائر (صفحة أخف وأسرع).
				ob_start();
				zad_product_grid(
					array(
						'category' => array( $zd_slug ),
						'limit'    => 8,
						'orderby'  => 'menu_order',
						'order'    => 'ASC',
					),
					'zd-grid--home'
				);
				?>
				<p class="zd-center"><a class="zd-btn zd-btn--outline" href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>">كل أصناف <?php echo esc_html( $zd_cat['title'] ); ?> (<?php echo esc_html( zad_n_items( zad_cat_count( $zd_slug ) ) ); ?>)</a></p>
				<?php
				$zd_html = ob_get_clean();
				echo $zd_first ? $zd_html : '<template data-zd-lazy>' . $zd_html . '</template>'; // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</div>
			<?php $zd_first = false; ?>
		<?php endforeach; ?>
	</div>
</section>
