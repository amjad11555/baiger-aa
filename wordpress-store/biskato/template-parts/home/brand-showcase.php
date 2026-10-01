<?php
/**
 * الواجهة الرئيسية: لافتات العلامات (مثل «من تشكيلة إيتي») بصور العلامة وشعارها.
 *
 * تظهر لكل علامة لها لافتات في zad_brands() ولها أصناف في المتجر.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

foreach ( zad_all_brands() as $zd_slug => $zd_b ) :
	if ( empty( $zd_b['showcase'] ) || empty( $zd_b['count'] ) ) {
		continue;
	}
	$zd_id = 'zd-showcase-' . $zd_slug;
	?>
	<section class="zd-section zd-showcase" aria-labelledby="<?php echo esc_attr( $zd_id ); ?>">
		<div class="zd-container">
			<div class="zd-showcase__head">
				<?php $zd_logo = zad_brand_logo( $zd_b, 44 ); ?>
				<?php if ( $zd_logo ) : ?>
					<span class="zd-showcase__logo"><?php echo $zd_logo; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endif; ?>
				<div class="zd-showcase__titles">
					<h2 class="zd-showcase__title" id="<?php echo esc_attr( $zd_id ); ?>">من تشكيلة <?php echo esc_html( $zd_b['ar'] ); ?></h2>
					<p class="zd-showcase__sub">أصناف <?php echo esc_html( $zd_b['ar'] ); ?> الأكثر طلباً على رفوف البقالة، بسعر الكرتونة.</p>
				</div>
				<a class="zd-link zd-showcase__all" href="<?php echo esc_url( $zd_b['url'] ); ?>">كل أصناف <?php echo esc_html( $zd_b['ar'] ); ?> (<?php echo (int) $zd_b['count']; ?>)</a>
			</div>
			<ul class="zd-showcase__grid">
				<?php foreach ( $zd_b['showcase'] as $zd_t ) : ?>
					<?php list( $zd_img, $zd_kicker, $zd_title, $zd_text, $zd_target, $zd_alt ) = $zd_t; ?>
					<li>
						<a class="zd-showcase__tile" href="<?php echo esc_url( zad_showcase_url( $zd_b, $zd_target ) ); ?>">
							<span class="zd-showcase__media"><?php echo zad_img( $zd_img, $zd_alt, array( 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 85vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="zd-showcase__body">
								<span class="zd-showcase__kicker"><?php echo esc_html( $zd_kicker ); ?></span>
								<span class="zd-showcase__name"><?php echo esc_html( $zd_title ); ?></span>
								<span class="zd-showcase__text"><?php echo esc_html( $zd_text ); ?></span>
								<span class="zd-showcase__cta">اطلب بالجملة <?php zad_the_icon( 'arrow-left', '', 16 ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
endforeach;
