<?php
/**
 * الواجهة الرئيسية: «أحدث الأصناف» — ما أُضيف مؤخراً، والجديد منها يحمل شارة «جديد».
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_new_ids = zad_new_product_ids( 40 );
$zd_new_n   = count( $zd_new_ids );
$zd_sub     = $zd_new_n
	? sprintf( 'وصل %s خلال آخر %d يوماً. اطّلع عليها قبل غيرك.', zad_n_items( $zd_new_n ), zad_new_days() )
	: 'آخر ما أضفناه إلى التشكيلة. تظهر الأصناف الجديدة هنا فور وصولها.';
?>
<section class="zd-section zd-newest" aria-labelledby="zd-newest-title">
	<div class="zd-container">
		<?php zad_section_head( 'أحدث الأصناف', $zd_sub, 'zd-newest-title' ); ?>
		<?php
		// الجديد أولاً، ثم نكمل بآخر ما أُضيف إلى التشكيلة.
		$zd_ids = array_slice( $zd_new_ids, 0, 8 );
		if ( count( $zd_ids ) < 8 ) {
			$zd_ids = array_merge(
				$zd_ids,
				wc_get_products(
					array(
						'status'     => 'publish',
						'visibility' => 'catalog',
						'limit'      => 8 - count( $zd_ids ),
						'exclude'    => $zd_ids,
						'orderby'    => array(
							'date' => 'DESC',
							'ID'   => 'DESC',
						),
						'return'     => 'ids',
					)
				)
			);
		}
		zad_product_grid(
			array(
				'include' => $zd_ids,
				'limit'   => 8,
				'orderby' => 'post__in',
			),
			'zd-grid--home'
		);
		?>
		<p class="zd-center">
			<a class="zd-btn zd-btn--outline" href="<?php echo esc_url( $zd_new_n ? zad_new_url() : add_query_arg( 'orderby', 'date', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php echo $zd_new_n ? 'كل الأصناف الجديدة (' . (int) $zd_new_n . ')' : 'تصفّح التشكيلة من الأحدث'; ?></a>
		</p>
	</div>
</section>
