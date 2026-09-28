<?php
/**
 * الواجهة الرئيسية: العلامات التجارية.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="lz-section lz-brands" aria-labelledby="lz-brands-title">
	<div class="lz-container">
		<div class="lz-section__head">
			<span class="lz-kicker"><?php lazza_the_icon( 'shield', '', 16 ); ?> منتجات أصلية</span>
			<h2 class="lz-section__title" id="lz-brands-title">علامات تركية يثق بها زبائنك</h2>
			<p class="lz-section__sub">نوفّر لك تشكيلة إيتي وأولكر وبونوتشي كاملة، ونؤمّن أي علامة أخرى عند الطلب.</p>
		</div>
		<div class="lz-brands__grid">
			<?php foreach ( lazza_brands() as $lz_slug => $lz_b ) : ?>
				<?php
				$lz_term  = taxonomy_exists( 'product_brand' ) ? get_term_by( 'slug', $lz_slug, 'product_brand' ) : null;
				$lz_link  = $lz_term ? get_term_link( $lz_term ) : '';
				$lz_count = $lz_term ? (int) $lz_term->count : 0;
				?>
				<a class="lz-brand-card lz-reveal" href="<?php echo esc_url( $lz_link && ! is_wp_error( $lz_link ) ? $lz_link : '#' ); ?>" style="--c1:<?php echo esc_attr( $lz_b['c1'] ); ?>;--c2:<?php echo esc_attr( $lz_b['c2'] ); ?>">
					<span class="lz-brand-card__word" lang="tr"><?php echo esc_html( $lz_b['latin'] ); ?></span>
					<span class="lz-brand-card__ar"><?php echo esc_html( $lz_b['ar'] ); ?></span>
					<span class="lz-brand-card__about"><?php echo esc_html( $lz_b['about'] ); ?></span>
					<span class="lz-brand-card__count"><?php echo esc_html( lazza_n_items( $lz_count ) ); ?> <?php lazza_the_icon( 'arrow-left', '', 16 ); ?></span>
				</a>
			<?php endforeach; ?>
			<a class="lz-brand-card lz-brand-card--more lz-reveal" href="<?php echo esc_url( lazza_page_url( 'special_request' ) ); ?>">
				<span class="lz-brand-card__word"><?php lazza_the_icon( 'plus', '', 34 ); ?></span>
				<span class="lz-brand-card__ar">علامة أخرى؟</span>
				<span class="lz-brand-card__about">تحتاج منتجات من علامات تركية أو مستوردة أخرى؟ اطلبها ونؤمّنها لبقالتك.</span>
				<span class="lz-brand-card__count">اطلب الآن <?php lazza_the_icon( 'arrow-left', '', 16 ); ?></span>
			</a>
		</div>
	</div>
</section>
