<?php
/**
 * الواجهة الرئيسية: شرائح البنرات (صور من «التخصيص» أو بنرات جاهزة بألوان المتجر).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_title = trim( preg_replace( '/\s*(…|\.\.\.)\s*/u', ' ', (string) maria_opt( 'hero_title' ) ) );

// صور مرفوعة من «التخصيص ← بنرات الرئيسية».
$mr_images = array();
for ( $mr_i = 1; $mr_i <= 3; $mr_i++ ) {
	$mr_src = (string) get_theme_mod( 'maria_banner_' . $mr_i, '' );
	if ( $mr_src ) {
		$mr_images[] = array( $mr_src, (string) get_theme_mod( 'maria_banner_' . $mr_i . '_link', '' ) );
	}
}

$mr_slides = array();
if ( ! $mr_images && function_exists( 'wc_get_products' ) ) {
	$mr_pick = static function ( $args ) {
		return wc_get_products(
			array_merge(
				array(
					'status'     => 'publish',
					'visibility' => 'catalog',
					'orderby'    => 'menu_order',
					'order'      => 'ASC',
				),
				$args
			)
		);
	};
	$mr_sale_ids = array_values( array_filter( array_map( 'intval', wc_get_product_ids_on_sale() ) ) );
	$mr_max      = 0;
	foreach ( $mr_sale_ids as $mr_id ) {
		$mr_p = wc_get_product( $mr_id );
		if ( $mr_p ) {
			$mr_max = max( $mr_max, maria_discount_percent( $mr_p ) );
		}
	}

	$mr_slides[] = array(
		'mod'    => 'red',
		'url'    => maria_page_url( 'quick_order' ),
		'kicker' => maria_opt( 'hero_kicker' ),
		'title'  => $mr_title,
		'sub'    => 'اطلب من كرتونة واحدة · توصيل إلى محلّك · الدفع عند الاستلام',
		'cta'    => 'ابدأ طلبك الآن',
		'arts'   => $mr_pick( array( 'featured' => true, 'limit' => 3 ) ),
	);
	if ( $mr_sale_ids ) {
		$mr_slides[] = array(
			'mod'    => 'yellow',
			'url'    => maria_cat_url( 'offers' ),
			'kicker' => 'عروض الأسبوع',
			'title'  => $mr_max ? sprintf( 'خصم حتى %d%% على الكرتونة', $mr_max ) : 'خصومات على سعر الكرتونة',
			'sub'    => 'عروض على أصناف مختارة من إيتي وأولكر وبونوتشي، تتجدد كل أسبوع',
			'cta'    => 'تسوّق العروض',
			'arts'   => $mr_pick( array( 'include' => array_slice( $mr_sale_ids, 0, 12 ), 'limit' => 3 ) ),
		);
	}
	if ( maria_wa_number() ) {
		$mr_slides[] = array(
			'mod'    => 'green',
			'url'    => maria_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ),
			'ext'    => true,
			'kicker' => 'أسرع طريقة للطلب',
			'title'  => 'اطلب عبر واتساب برسالة واحدة',
			'sub'    => 'أرسل قائمتك أو صورة الرف، ونؤكد طلبك وموعد التوصيل',
			'cta'    => 'راسلنا على واتساب',
			'icon'   => 'whatsapp',
		);
	}
	$mr_slides[] = array(
		'mod'    => 'purple',
		'url'    => maria_page_url( 'special_request' ),
		'kicker' => 'كل احتياجات بقالتك من مكان واحد',
		'title'  => 'لم تجد منتجك؟ نؤمّنه لك',
		'sub'    => 'أي علامة تركية أو مستوردة، بسعر الجملة',
		'cta'    => 'اطلب منتجاً غير متوفر',
		'arts'   => $mr_pick( array( 'category' => array( 'snacks' ), 'limit' => 3 ) ),
	);
}
$mr_count = $mr_images ? count( $mr_images ) : count( $mr_slides );
?>
<section class="mr-banners" aria-roledescription="carousel" aria-label="عروض ماريا">
	<div class="mr-container">
		<?php if ( $mr_images ) : ?>
			<h1 class="screen-reader-text"><?php echo esc_html( $mr_title ); ?></h1>
		<?php endif; ?>
		<div class="mr-slider" data-mr-rail data-mr-autoplay="6000">
			<div class="mr-slider__track" data-mr-rail-track>
				<?php if ( $mr_images ) : ?>
					<?php foreach ( $mr_images as $mr_n => $mr_img ) : ?>
						<?php $mr_tag = $mr_img[1] ? 'a' : 'div'; ?>
						<<?php echo esc_attr( $mr_tag ); ?> class="mr-slide mr-slide--img"<?php echo $mr_img[1] ? ' href="' . esc_url( $mr_img[1] ) . '"' : ''; ?> role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $mr_n + 1 ) . ' من ' . $mr_count ); ?>">
							<img src="<?php echo esc_url( $mr_img[0] ); ?>" alt="" <?php echo 0 === $mr_n ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
						</<?php echo esc_attr( $mr_tag ); ?>>
					<?php endforeach; ?>
				<?php else : ?>
					<?php foreach ( $mr_slides as $mr_n => $mr_s ) : ?>
						<a class="mr-slide mr-bnr mr-bnr--<?php echo esc_attr( $mr_s['mod'] ); ?>" href="<?php echo esc_url( $mr_s['url'] ); ?>"<?php echo ! empty( $mr_s['ext'] ) ? ' target="_blank" rel="noopener"' : ''; ?> role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $mr_n + 1 ) . ' من ' . $mr_count ); ?>">
							<span class="mr-bnr__text">
								<span class="mr-bnr__kicker"><?php echo esc_html( $mr_s['kicker'] ); ?></span>
								<?php if ( 0 === $mr_n ) : ?>
									<h1 class="mr-bnr__title"><?php echo esc_html( $mr_s['title'] ); ?></h1>
								<?php else : ?>
									<strong class="mr-bnr__title"><?php echo esc_html( $mr_s['title'] ); ?></strong>
								<?php endif; ?>
								<span class="mr-bnr__sub"><?php echo esc_html( $mr_s['sub'] ); ?></span>
								<span class="mr-bnr__cta"><?php echo esc_html( $mr_s['cta'] ); ?> <?php maria_the_icon( 'arrow-left', '', 18 ); ?></span>
							</span>
							<span class="mr-bnr__arts" aria-hidden="true">
								<?php if ( ! empty( $mr_s['icon'] ) ) : ?>
									<span class="mr-bnr__icon"><?php maria_the_icon( $mr_s['icon'], '', 120 ); ?></span>
								<?php else : ?>
									<?php foreach ( $mr_s['arts'] as $mr_k => $mr_p ) : ?>
										<span class="mr-bnr__art mr-bnr__art--<?php echo (int) $mr_k + 1; ?>"><?php echo $mr_p->get_image( 'woocommerce_thumbnail', array( 'loading' => 0 === $mr_n ? 'eager' : 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<?php endforeach; ?>
								<?php endif; ?>
							</span>
						</a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="mr-dots" data-mr-rail-dots></div>
		</div>
	</div>
</section>
