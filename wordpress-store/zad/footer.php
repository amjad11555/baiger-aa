<?php
/**
 * التذييل بأسلوب Kalles (خمسة أعمدة على خلفية فاتحة) + شريط الطلبية + شريط التنقل السفلي للجوال.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_has_wc   = class_exists( 'WooCommerce' );
$zd_is_qo    = is_page_template( 'page-templates/template-quick-order.php' );
$zd_checkout = $zd_has_wc && ( is_cart() || is_checkout() );
$zd_count    = $zd_has_wc && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$zd_lines    = $zd_has_wc && WC()->cart ? count( WC()->cart->get_cart() ) : 0;
$zd_phone    = (string) zad_opt( 'phone' );
$zd_shop     = $zd_has_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$zd_account  = $zd_has_wc ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
?>

<footer class="zd-footer">
	<div class="zd-container zd-footer__grid">
		<div class="zd-footer__brand">
			<?php zad_logo( 'light', true ); ?>
			<p>شركة جملة وتوزيع للحلويات والتسالي التركية. نورّد للسوبرماركت والبقالات والموزعين في <?php echo esc_html( zad_opt( 'city' ) ); ?> وجميع الولايات، ونصدّر بالحاويات.</p>
			<ul class="zd-footer__contact">
				<li><?php echo esc_html( zad_opt( 'address' ) ? zad_opt( 'address' ) : zad_opt( 'city' ) . '، تركيا' ); ?></li>
				<?php if ( zad_opt( 'email' ) ) : ?>
					<li><a href="mailto:<?php echo esc_attr( zad_opt( 'email' ) ); ?>"><?php echo esc_html( zad_opt( 'email' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( $zd_phone ) : ?>
					<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $zd_phone ) ); ?>"><bdi dir="ltr"><?php echo esc_html( $zd_phone ); ?></bdi></a></li>
				<?php endif; ?>
				<li><?php echo esc_html( zad_opt( 'hours' ) ); ?></li>
			</ul>
			<?php $zd_socials = zad_socials(); ?>
			<?php if ( $zd_socials ) : ?>
				<ul class="zd-socials">
					<?php foreach ( $zd_socials as $zd_net => $zd_url ) : ?>
						<li><a href="<?php echo esc_url( $zd_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ucfirst( $zd_net ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="zd-footer__col" aria-label="الأقسام">
			<h2 class="zd-footer__title">الأقسام</h2>
			<ul>
				<?php foreach ( zad_categories() as $zd_slug => $zd_cat ) : ?>
					<li><a href="<?php echo esc_url( zad_cat_url( $zd_slug ) ); ?>"><?php echo esc_html( $zd_cat['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<nav class="zd-footer__col" aria-label="الشركة">
			<h2 class="zd-footer__title">الشركة</h2>
			<ul>
				<li><a href="<?php echo esc_url( zad_page_url( 'about' ) ); ?>">عن زاد</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'brands' ) ); ?>">العلامات</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'export' ) ); ?>">التصدير والجملة الدولية</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'contact' ) ); ?>">تواصل مع المبيعات</a></li>
			</ul>
		</nav>

		<nav class="zd-footer__col" aria-label="روابط مفيدة">
			<h2 class="zd-footer__title">روابط مفيدة</h2>
			<ul>
				<li><a href="<?php echo esc_url( zad_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'special_request' ) ); ?>">طلب توريد خاص</a></li>
				<li><a href="<?php echo esc_url( zad_page_url( 'delivery' ) ); ?>">التوريد والدفع والإرجاع</a></li>
				<li><a href="<?php echo esc_url( $zd_account ); ?>">حسابي وطلبياتي</a></li>
			</ul>
		</nav>

		<div class="zd-footer__col zd-footer__col--news">
			<h2 class="zd-footer__title">قائمة الأسعار الأسبوعية</h2>
			<p>أرسل لنا اسم متجرك، ونرسل لك قائمة الأسعار والعروض الجديدة كل أسبوع.</p>
			<?php if ( zad_wa_number() ) : ?>
				<a class="zd-btn zd-btn--dark zd-btn--block" href="<?php echo esc_url( zad_wa_link( 'مرحباً، أرغب بالاشتراك في قائمة الأسعار الأسبوعية.' . "\n" . 'اسم المتجر / الشركة:' ) ); ?>" target="_blank" rel="noopener">اشترك عبر واتساب</a>
			<?php else : ?>
				<a class="zd-btn zd-btn--dark zd-btn--block" href="<?php echo esc_url( zad_page_url( 'contact' ) ); ?>">اشترك في القائمة</a>
			<?php endif; ?>
			<p class="zd-footer__pay">الدفع عند الاستلام · تحويل بنكي · فاتورة نظامية</p>
		</div>
	</div>

	<div class="zd-footer__bottom">
		<div class="zd-container zd-footer__bottom-row">
			<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> للتجارة. جميع الحقوق محفوظة.</p>
			<p class="zd-footer__legal">العلامات التجارية المذكورة ملك لأصحابها، وزاد موزّع جملة مستقل.</p>
		</div>
	</div>
</footer>

<?php if ( $zd_has_wc && ! $zd_is_qo && ! $zd_checkout ) : ?>
<div class="zd-orderbar<?php echo $zd_count ? ' is-visible' : ''; ?>" data-zd-orderbar aria-live="polite">
	<button type="button" class="zd-orderbar__cart" data-zd-open="zd-cart-drawer" aria-controls="zd-cart-drawer" aria-label="عرض الطلبية">
		<span class="zd-orderbar__info">
			<span class="zd-orderbar__lines">الطلبية: <b data-zd-lines><?php echo (int) $zd_lines; ?></b> صنف · <b data-zd-cartons><?php echo (int) $zd_count; ?></b> كرتونة</span>
			<?php if ( zad_show_prices() ) : ?>
				<span class="zd-orderbar__total"><span class="zd-cart-total"><?php echo wp_kses_post( WC()->cart ? WC()->cart->get_cart_subtotal() : '' ); ?></span></span>
				<?php
				$zd_min  = (float) zad_opt( 'min_order' );
				$zd_left = $zd_min > 0 && WC()->cart ? $zd_min - ( (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax() ) : 0;
				?>
				<span class="zd-orderbar__min" data-zd-min<?php echo $zd_left > 0 && $zd_count ? '' : ' hidden'; ?>>باقي <b data-zd-min-left><?php echo wp_kses_post( $zd_left > 0 ? wc_price( $zd_left ) : '' ); ?></b> للحد الأدنى</span>
			<?php endif; ?>
		</span>
	</button>
	<a class="zd-orderbar__go" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-zd-checkout>إتمام الطلب</a>
</div>
<?php endif; ?>

<nav class="zd-mbar" aria-label="تنقل سريع">
	<a href="<?php echo esc_url( $zd_shop ); ?>"><?php zad_the_icon( 'grid', '', 22 ); ?><span>المتجر</span></a>
	<button type="button" data-zd-open="zd-search-panel" aria-controls="zd-search-panel"><?php zad_the_icon( 'search', '', 22 ); ?><span>البحث</span></button>
	<button type="button" data-zd-open="zd-wish-drawer" aria-controls="zd-wish-drawer"><span class="zd-mbar__ico"><?php zad_the_icon( 'heart', '', 22 ); ?><span class="zd-bubble" data-zd-wish-count hidden>0</span></span><span>المحفوظة</span></button>
	<?php if ( $zd_has_wc ) : ?>
		<button type="button" data-zd-open="zd-cart-drawer" aria-controls="zd-cart-drawer"><span class="zd-mbar__ico"><?php zad_the_icon( 'bag', '', 22 ); ?><span class="zd-bubble zd-cart-count" data-count="<?php echo (int) $zd_count; ?>"><?php echo (int) $zd_count; ?></span></span><span>الطلبية</span></button>
	<?php endif; ?>
	<a href="<?php echo esc_url( $zd_account ); ?>"><?php zad_the_icon( 'user', '', 22 ); ?><span>حسابي</span></a>
</nav>

<aside class="zd-floats" aria-label="اختصارات">
	<?php if ( zad_wa_number() && ! $zd_is_qo && ! $zd_checkout ) : ?>
		<a class="zd-wa-float" href="<?php echo esc_url( zad_wa_link( 'مرحباً، أرغب بالتواصل مع قسم المبيعات في ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener" aria-label="تحدث مع المبيعات عبر واتساب"><?php zad_the_icon( 'whatsapp', '', 26 ); ?></a>
	<?php endif; ?>
	<button type="button" class="zd-totop" data-zd-totop aria-label="العودة إلى أعلى الصفحة" hidden></button>
</aside>

<div class="zd-toast" role="status" aria-live="polite" aria-atomic="true"></div>

<?php wp_footer(); ?>
</body>
</html>
