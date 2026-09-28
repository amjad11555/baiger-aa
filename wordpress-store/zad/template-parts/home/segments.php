<?php
/**
 * الواجهة الرئيسية: لمن نورّد (أربع شرائح بصور).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="zd-section zd-section--soft zd-segments" aria-labelledby="zd-seg-title">
	<div class="zd-container">
		<?php zad_section_head( 'لمن نورّد', 'من البقالة إلى المستورد، لكل حلقة في سلسلة البيع حجم طلبية يناسبها', 'zd-seg-title' ); ?>
		<ul class="zd-segments__grid">
			<?php foreach ( zad_segments() as $zd_seg ) : ?>
				<li class="zd-seg">
					<div class="zd-seg__media"><?php echo zad_img( $zd_seg['image'], '', array( 'sizes' => '(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php if ( ! empty( $zd_seg['page'] ) ) : ?><a class="zd-seg__pill" href="<?php echo esc_url( zad_page_url( $zd_seg['page'] ) ); ?>">تفاصيل التصدير</a><?php endif; ?></div>
					<h3 class="zd-seg__title"><?php echo esc_html( $zd_seg['title'] ); ?></h3>
					<p class="zd-seg__text"><?php echo esc_html( $zd_seg['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
