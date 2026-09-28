<?php
/**
 * التذييل.
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

$lz_has_wc = class_exists( 'WooCommerce' );
$lz_is_qo  = is_page_template( 'page-templates/template-quick-order.php' );
$lz_count  = $lz_has_wc ? (int) WC()->cart->get_cart_contents_count() : 0;
?>

<?php if ( ! $lz_is_qo && ! ( $lz_has_wc && ( is_cart() || is_checkout() ) ) ) : ?>
<section class="lz-cta-band" aria-label="ابدأ الطلب">
	<div class="lz-container lz-cta-band__row">
		<div class="lz-cta-band__text">
			<h2>جاهز تملأ رفوف بقالتك؟</h2>
			<p>اطلب بالكرتونة خلال دقيقة، ونوصل الطلب إلى باب محلّك.</p>
		</div>
		<div class="lz-cta-band__actions">
			<a class="lz-btn lz-btn--gold lz-btn--lg" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"><?php lazza_the_icon( 'bolt', '', 20 ); ?> ابدأ الطلب السريع</a>
			<?php if ( lazza_wa_number() ) : ?>
				<a class="lz-btn lz-btn--wa lz-btn--lg" href="<?php echo esc_url( lazza_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener"><?php lazza_the_icon( 'whatsapp', '', 20 ); ?> واتساب</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<footer class="lz-footer">
	<div class="lz-container lz-footer__grid">
		<div class="lz-footer__brand">
			<?php lazza_logo(); ?>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : lazza_opt( 'seo_tagline' ) ); ?>. نخدم البقالات والماركت والمقاصف في <?php echo esc_html( lazza_opt( 'city' ) ); ?> وكل تركيا، ونصدّر إلى الخارج.</p>
			<?php $lz_socials = lazza_socials(); ?>
			<?php if ( $lz_socials ) : ?>
				<ul class="lz-socials">
					<?php foreach ( $lz_socials as $lz_net => $lz_url ) : ?>
						<li><a href="<?php echo esc_url( $lz_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $lz_net ) ); ?>"><?php lazza_the_icon( $lz_net, '', 20 ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="lz-footer__col" aria-label="أقسام المتجر">
			<h2 class="lz-footer__title">الأقسام</h2>
			<ul>
				<?php foreach ( lazza_categories() as $lz_slug => $lz_cat ) : ?>
					<li><a href="<?php echo esc_url( lazza_cat_url( $lz_slug ) ); ?>"><?php echo esc_html( $lz_cat['name'] ); ?> بالجملة</a></li>
				<?php endforeach; ?>
				<?php foreach ( lazza_brands() as $lz_slug => $lz_brand ) : ?>
					<?php $lz_link = taxonomy_exists( 'product_brand' ) ? get_term_link( $lz_slug, 'product_brand' ) : ''; ?>
					<?php if ( $lz_link && ! is_wp_error( $lz_link ) ) : ?>
						<li><a href="<?php echo esc_url( $lz_link ); ?>">منتجات <?php echo esc_html( $lz_brand['ar'] ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="lz-footer__col" aria-label="روابط مهمة">
			<h2 class="lz-footer__title">روابط مهمة</h2>
			<ul>
				<li><a href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>">الطلب السريع</a></li>
				<li><a href="<?php echo esc_url( lazza_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر</a></li>
				<li><a href="<?php echo esc_url( lazza_page_url( 'export' ) ); ?>">الجملة خارج تركيا</a></li>
				<li><a href="<?php echo esc_url( lazza_page_url( 'delivery' ) ); ?>">التوصيل والدفع والإرجاع</a></li>
				<li><a href="<?php echo esc_url( lazza_page_url( 'about' ) ); ?>">من نحن</a></li>
				<li><a href="<?php echo esc_url( lazza_page_url( 'contact' ) ); ?>">تواصل معنا</a></li>
			</ul>
		</nav>

		<div class="lz-footer__col">
			<h2 class="lz-footer__title">تواصل معنا</h2>
			<ul class="lz-footer__contact">
				<?php if ( lazza_wa_number() ) : ?>
					<li><?php lazza_the_icon( 'whatsapp', '', 18 ); ?><a href="<?php echo esc_url( lazza_wa_link() ); ?>" target="_blank" rel="noopener" dir="ltr">+<?php echo esc_html( lazza_wa_number() ); ?></a></li>
				<?php endif; ?>
				<?php if ( lazza_opt( 'phone' ) ) : ?>
					<li><?php lazza_the_icon( 'phone', '', 18 ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', lazza_opt( 'phone' ) ) ); ?>" dir="ltr"><?php echo esc_html( lazza_opt( 'phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( lazza_opt( 'email' ) ) : ?>
					<li><?php lazza_the_icon( 'mail', '', 18 ); ?><a href="mailto:<?php echo esc_attr( lazza_opt( 'email' ) ); ?>"><?php echo esc_html( lazza_opt( 'email' ) ); ?></a></li>
				<?php endif; ?>
				<li><?php lazza_the_icon( 'pin', '', 18 ); ?><span><?php echo esc_html( lazza_opt( 'address' ) ? lazza_opt( 'address' ) : lazza_opt( 'city' ) . '، تركيا' ); ?></span></li>
				<li><?php lazza_the_icon( 'clock', '', 18 ); ?><span><?php echo esc_html( lazza_opt( 'hours' ) ); ?></span></li>
			</ul>
		</div>
	</div>

	<div class="lz-footer__bottom">
		<div class="lz-container lz-footer__bottom-row">
			<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — جميع الحقوق محفوظة.</p>
			<p class="lz-footer__legal">العلامات التجارية Eti وÜlker وBonucci مملوكة لأصحابها. نحن موزّع جملة مستقل.</p>
		</div>
	</div>
</footer>

<nav class="lz-bottom-nav" aria-label="تنقل سريع">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php lazza_the_icon( 'home', '', 22 ); ?><span>الرئيسية</span></a>
	<button type="button" data-lz-open="lz-drawer" aria-controls="lz-drawer"><?php lazza_the_icon( 'grid', '', 22 ); ?><span>الأقسام</span></button>
	<a class="lz-bottom-nav__main" href="<?php echo esc_url( lazza_page_url( 'quick_order' ) ); ?>"<?php echo $lz_is_qo ? ' aria-current="page"' : ''; ?>><span class="lz-bottom-nav__bubble"><?php lazza_the_icon( 'bolt', '', 26 ); ?></span><span>طلب سريع</span></a>
	<?php if ( $lz_has_wc ) : ?>
		<button type="button" data-lz-open="lz-cart-drawer" aria-controls="lz-cart-drawer"><span class="lz-bottom-nav__cart"><?php lazza_the_icon( 'cart', '', 22 ); ?><span class="lz-cart-count" data-count="<?php echo (int) $lz_count; ?>"><?php echo (int) $lz_count; ?></span></span><span>السلة</span></button>
	<?php endif; ?>
	<?php if ( lazza_wa_number() ) : ?>
		<a href="<?php echo esc_url( lazza_wa_link( 'مرحباً، أرغب بالطلب' ) ); ?>" target="_blank" rel="noopener"><?php lazza_the_icon( 'whatsapp', '', 22 ); ?><span>واتساب</span></a>
	<?php else : ?>
		<a href="<?php echo esc_url( lazza_page_url( 'contact' ) ); ?>"><?php lazza_the_icon( 'phone', '', 22 ); ?><span>تواصل</span></a>
	<?php endif; ?>
</nav>

<?php if ( lazza_wa_number() ) : ?>
	<a class="lz-wa-float" href="<?php echo esc_url( lazza_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener" aria-label="اطلب عبر واتساب"><?php lazza_the_icon( 'whatsapp', '', 30 ); ?><span>اطلب عبر واتساب</span></a>
<?php endif; ?>

<div class="lz-toast" role="status" aria-live="polite" aria-atomic="true"></div>

<?php wp_footer(); ?>
</body>
</html>
