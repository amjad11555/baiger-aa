<?php
/**
 * التذييل + شريط التنقل السفلي + شريط الطلب العائم.
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_has_wc   = class_exists( 'WooCommerce' );
$mr_is_qo    = is_page_template( 'page-templates/template-quick-order.php' );
$mr_checkout = $mr_has_wc && ( is_cart() || is_checkout() );
$mr_count    = $mr_has_wc && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$mr_lines    = $mr_has_wc && WC()->cart ? count( WC()->cart->get_cart() ) : 0;
?>

<?php if ( ! $mr_is_qo && ! $mr_checkout ) : ?>
<section class="mr-cta" aria-labelledby="mr-cta-title">
	<div class="mr-container">
		<div class="mr-cta__box">
			<div class="mr-cta__text">
				<h2 id="mr-cta-title">جاهز لطلبك القادم؟</h2>
				<p>كل الأصناف في قائمة واحدة. حدّد الكراتين وأرسل الطلب في دقيقة، ونحن نوصله إلى محلّك.</p>
			</div>
			<div class="mr-cta__actions">
				<a class="mr-btn mr-btn--accent mr-btn--lg" href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>"><?php maria_the_icon( 'list', '', 20 ); ?> ابدأ طلبك الآن</a>
				<?php if ( maria_wa_number() ) : ?>
					<a class="mr-btn mr-btn--light mr-btn--lg" href="<?php echo esc_url( maria_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 20 ); ?> اطلب عبر واتساب</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<footer class="mr-footer">
	<div class="mr-container mr-footer__grid">
		<div class="mr-footer__brand">
			<?php maria_logo(); ?>
			<p>موزّع جملة للحلويات والتسالي التركية الأصلية. نخدم البقالات والماركت والمقاصف في <?php echo esc_html( maria_opt( 'city' ) ); ?> وكل تركيا، ونشحن إلى الخارج.</p>
			<?php $mr_socials = maria_socials(); ?>
			<?php if ( $mr_socials ) : ?>
				<ul class="mr-socials">
					<?php foreach ( $mr_socials as $mr_net => $mr_url ) : ?>
						<li><a href="<?php echo esc_url( $mr_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $mr_net ) ); ?>"><?php maria_the_icon( $mr_net, '', 20 ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="mr-footer__col" aria-label="الأقسام">
			<h2 class="mr-footer__title">الأقسام</h2>
			<ul>
				<?php foreach ( maria_categories() as $mr_slug => $mr_cat ) : ?>
					<li><a href="<?php echo esc_url( maria_cat_url( $mr_slug ) ); ?>"><?php echo esc_html( $mr_cat['name'] ); ?> بالجملة</a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="mr-footer__col" aria-label="الشركات">
			<h2 class="mr-footer__title">الشركات</h2>
			<ul>
				<?php foreach ( maria_all_brands() as $mr_b ) : ?>
					<?php if ( $mr_b['count'] ) : ?>
						<li><a href="<?php echo esc_url( $mr_b['url'] ); ?>">منتجات <?php echo esc_html( $mr_b['ar'] ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( maria_page_url( 'brands' ) ); ?>">كل الشركات</a></li>
				<li><a href="<?php echo esc_url( maria_page_url( 'special_request' ) ); ?>">اطلب منتجاً غير متوفر</a></li>
			</ul>
		</nav>

		<nav class="mr-footer__col" aria-label="روابط مهمة">
			<h2 class="mr-footer__title">ماريا</h2>
			<ul>
				<li><a href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>">قائمة الطلب السريع</a></li>
				<li><a href="<?php echo esc_url( maria_page_url( 'export' ) ); ?>">الجملة الدولية والتصدير</a></li>
				<li><a href="<?php echo esc_url( maria_page_url( 'delivery' ) ); ?>">التوصيل والدفع والإرجاع</a></li>
				<li><a href="<?php echo esc_url( maria_page_url( 'about' ) ); ?>">من نحن</a></li>
				<li><a href="<?php echo esc_url( maria_page_url( 'contact' ) ); ?>">تواصل معنا</a></li>
			</ul>
		</nav>

		<div class="mr-footer__col">
			<h2 class="mr-footer__title">تواصل معنا</h2>
			<ul class="mr-footer__contact">
				<?php if ( maria_wa_number() ) : ?>
					<li><?php maria_the_icon( 'whatsapp', '', 18 ); ?><a href="<?php echo esc_url( maria_wa_link() ); ?>" target="_blank" rel="noopener" dir="ltr">+<?php echo esc_html( maria_wa_number() ); ?></a></li>
				<?php endif; ?>
				<?php if ( maria_opt( 'phone' ) ) : ?>
					<li><?php maria_the_icon( 'phone', '', 18 ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', maria_opt( 'phone' ) ) ); ?>" dir="ltr"><?php echo esc_html( maria_opt( 'phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( maria_opt( 'email' ) ) : ?>
					<li><?php maria_the_icon( 'mail', '', 18 ); ?><a href="mailto:<?php echo esc_attr( maria_opt( 'email' ) ); ?>"><?php echo esc_html( maria_opt( 'email' ) ); ?></a></li>
				<?php endif; ?>
				<li><?php maria_the_icon( 'pin', '', 18 ); ?><span><?php echo esc_html( maria_opt( 'address' ) ? maria_opt( 'address' ) : maria_opt( 'city' ) . '، تركيا' ); ?></span></li>
				<li><?php maria_the_icon( 'clock', '', 18 ); ?><span><?php echo esc_html( maria_opt( 'hours' ) ); ?></span></li>
			</ul>
			<ul class="mr-footer__pay" aria-label="طرق الدفع">
				<li><?php maria_the_icon( 'wallet', '', 16 ); ?> الدفع عند الاستلام</li>
				<li><?php maria_the_icon( 'shield', '', 16 ); ?> تحويل بنكي</li>
			</ul>
		</div>
	</div>

	<div class="mr-footer__bottom">
		<div class="mr-container mr-footer__bottom-row">
			<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. جميع الحقوق محفوظة.</p>
			<p class="mr-footer__legal">العلامات التجارية المعروضة ملك لأصحابها، وماريا موزّع جملة مستقل.</p>
		</div>
	</div>
</footer>

<?php if ( $mr_has_wc && ! $mr_is_qo && ! $mr_checkout ) : ?>
<div class="mr-orderbar<?php echo $mr_count ? ' is-visible' : ''; ?>" data-mr-orderbar aria-live="polite">
	<button type="button" class="mr-orderbar__cart" data-mr-open="mr-cart-drawer" aria-controls="mr-cart-drawer" aria-label="عرض السلة">
		<span class="mr-orderbar__icon"><?php maria_the_icon( 'cart', '', 22 ); ?></span>
		<span class="mr-orderbar__info">
			<span class="mr-orderbar__lines"><b data-mr-lines><?php echo (int) $mr_lines; ?></b> صنف · <b data-mr-cartons><?php echo (int) $mr_count; ?></b> كرتونة</span>
			<?php if ( maria_show_prices() ) : ?>
				<span class="mr-orderbar__total"><span class="mr-cart-total"><?php echo wp_kses_post( WC()->cart ? WC()->cart->get_cart_subtotal() : '' ); ?></span></span>
			<?php endif; ?>
		</span>
	</button>
	<a class="mr-orderbar__go" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-mr-checkout>أكمل الطلب <?php maria_the_icon( 'arrow-left', '', 18 ); ?></a>
</div>
<?php endif; ?>

<nav class="mr-bottom-nav" aria-label="تنقل سريع">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>><?php maria_the_icon( 'home', '', 24 ); ?><span>الرئيسية</span></a>
	<button type="button" data-mr-open="mr-drawer" aria-controls="mr-drawer"><?php maria_the_icon( 'menu', '', 24 ); ?><span>الأقسام</span></button>
	<a href="<?php echo esc_url( maria_page_url( 'quick_order' ) ); ?>"<?php echo $mr_is_qo ? ' aria-current="page"' : ''; ?>><?php maria_the_icon( 'list', '', 24 ); ?><span>الطلب السريع</span></a>
	<a href="<?php echo esc_url( maria_page_url( 'quick_order' ) . '#favorites' ); ?>" data-mr-favs-link><span class="mr-bottom-nav__ico"><?php maria_the_icon( 'heart', '', 24 ); ?><span class="mr-fav-count" data-mr-fav-count hidden>0</span></span><span>المفضلة</span></a>
	<?php if ( $mr_has_wc ) : ?>
		<button type="button" data-mr-open="mr-cart-drawer" aria-controls="mr-cart-drawer"><span class="mr-bottom-nav__ico"><?php maria_the_icon( 'cart', '', 24 ); ?><span class="mr-cart-count" data-count="<?php echo (int) $mr_count; ?>"><?php echo (int) $mr_count; ?></span></span><span>سلتي</span></button>
	<?php endif; ?>
</nav>

<div class="mr-floats">
	<?php if ( maria_wa_number() ) : ?>
		<a class="mr-wa-float" href="<?php echo esc_url( maria_wa_link( 'مرحباً، أرغب بالطلب من ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener" aria-label="اطلب عبر واتساب"><?php maria_the_icon( 'whatsapp', '', 34 ); ?></a>
	<?php endif; ?>
	<button type="button" class="mr-totop" data-mr-totop aria-label="العودة إلى أعلى الصفحة" hidden><?php maria_the_icon( 'arrow-up', '', 28 ); ?></button>
</div>

<div class="mr-toast" role="status" aria-live="polite" aria-atomic="true"></div>

<?php wp_footer(); ?>
</body>
</html>
