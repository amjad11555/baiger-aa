<?php
/**
 * دعوة ختامية + التذييل + شريط الطلبية العائم.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_has_wc   = class_exists( 'WooCommerce' );
$sh_is_qo    = is_page_template( 'page-templates/template-quick-order.php' );
$sh_checkout = $sh_has_wc && ( is_cart() || is_checkout() );
$sh_count    = $sh_has_wc && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$sh_lines    = $sh_has_wc && WC()->cart ? count( WC()->cart->get_cart() ) : 0;
$sh_phone    = (string) shami_opt( 'phone' );
?>

<?php if ( ! $sh_is_qo && ! $sh_checkout ) : ?>
<section class="sh-cta" aria-labelledby="sh-cta-title">
	<div class="sh-cta__media"><?php echo shami_img( 'cta-docks', '', array( 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<div class="sh-container sh-cta__inner">
		<p class="sh-eyebrow sh-eyebrow--light">حسابات الجملة</p>
		<h2 class="sh-cta__title" id="sh-cta-title">توريد منتظم لمتجرك يبدأ بطلبية واحدة</h2>
		<p class="sh-cta__text">اختر أصنافك من قائمة الأسعار وحدد الكميات بالكرتونة، أو تحدث مع قسم المبيعات لترتيب جدول توريد ثابت وأسعار كميات تناسب حجم عملك.</p>
		<div class="sh-cta__actions">
			<a class="sh-btn sh-btn--accent sh-btn--lg" href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">افتح قائمة الأسعار</a>
			<?php if ( shami_wa_number() ) : ?>
				<a class="sh-btn sh-btn--outline-light sh-btn--lg" href="<?php echo esc_url( shami_wa_link( 'مرحباً، أرغب بفتح حساب جملة لدى ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener">تحدث مع المبيعات</a>
			<?php else : ?>
				<a class="sh-btn sh-btn--outline-light sh-btn--lg" href="<?php echo esc_url( shami_page_url( 'contact' ) ); ?>">تواصل مع المبيعات</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<footer class="sh-footer">
	<div class="sh-container sh-footer__grid">
		<div class="sh-footer__brand">
			<?php shami_logo( 'dark', true ); ?>
			<p>شركة تجارة جملة وتوزيع للحلويات والتسالي التركية. نورّد للسوبرماركت والبقالات والموزعين في <?php echo esc_html( shami_opt( 'city' ) ); ?> وجميع الولايات، ونصدّر بالحاويات إلى الأسواق العربية وأوروبا.</p>
			<?php $sh_socials = shami_socials(); ?>
			<?php if ( $sh_socials ) : ?>
				<ul class="sh-socials">
					<?php foreach ( $sh_socials as $sh_net => $sh_url ) : ?>
						<li><a href="<?php echo esc_url( $sh_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ucfirst( $sh_net ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<nav class="sh-footer__col" aria-label="التشكيلة">
			<h2 class="sh-footer__title">التشكيلة</h2>
			<ul>
				<?php foreach ( shami_categories() as $sh_slug => $sh_cat ) : ?>
					<li><a href="<?php echo esc_url( shami_cat_url( $sh_slug ) ); ?>"><?php echo esc_html( $sh_cat['title'] ); ?></a></li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( shami_page_url( 'quick_order' ) ); ?>">قائمة أسعار الجملة</a></li>
			</ul>
		</nav>

		<nav class="sh-footer__col" aria-label="الشركات">
			<h2 class="sh-footer__title">العلامات</h2>
			<ul>
				<?php foreach ( shami_all_brands() as $sh_b ) : ?>
					<?php if ( $sh_b['count'] ) : ?>
						<li><a href="<?php echo esc_url( $sh_b['url'] ); ?>"><?php echo esc_html( $sh_b['ar'] ); ?> <span lang="tr">· <?php echo esc_html( $sh_b['latin'] ); ?></span></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( shami_page_url( 'brands' ) ); ?>">كل العلامات</a></li>
			</ul>
		</nav>

		<nav class="sh-footer__col" aria-label="الشركة">
			<h2 class="sh-footer__title">الشركة</h2>
			<ul>
				<li><a href="<?php echo esc_url( shami_page_url( 'about' ) ); ?>">عن الشامي</a></li>
				<li><a href="<?php echo esc_url( shami_page_url( 'export' ) ); ?>">التصدير والجملة الدولية</a></li>
				<li><a href="<?php echo esc_url( shami_page_url( 'special_request' ) ); ?>">طلب توريد خاص</a></li>
				<li><a href="<?php echo esc_url( shami_page_url( 'delivery' ) ); ?>">التوريد والدفع والإرجاع</a></li>
				<li><a href="<?php echo esc_url( shami_page_url( 'contact' ) ); ?>">تواصل معنا</a></li>
			</ul>
		</nav>

		<div class="sh-footer__col sh-footer__col--contact">
			<h2 class="sh-footer__title">قسم المبيعات</h2>
			<dl class="sh-footer__contact">
				<?php if ( $sh_phone ) : ?>
					<div><dt>الهاتف</dt><dd><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $sh_phone ) ); ?>"><bdi dir="ltr"><?php echo esc_html( $sh_phone ); ?></bdi></a></dd></div>
				<?php endif; ?>
				<?php if ( shami_wa_number() ) : ?>
					<div><dt>واتساب</dt><dd><a href="<?php echo esc_url( shami_wa_link() ); ?>" target="_blank" rel="noopener"><bdi dir="ltr">+<?php echo esc_html( shami_wa_number() ); ?></bdi></a></dd></div>
				<?php endif; ?>
				<?php if ( shami_opt( 'email' ) ) : ?>
					<div><dt>البريد</dt><dd><a href="mailto:<?php echo esc_attr( shami_opt( 'email' ) ); ?>"><?php echo esc_html( shami_opt( 'email' ) ); ?></a></dd></div>
				<?php endif; ?>
				<div><dt>العنوان</dt><dd><?php echo esc_html( shami_opt( 'address' ) ? shami_opt( 'address' ) : shami_opt( 'city' ) . '، تركيا' ); ?></dd></div>
				<div><dt>الدوام</dt><dd><?php echo esc_html( shami_opt( 'hours' ) ); ?></dd></div>
			</dl>
			<p class="sh-footer__pay">الدفع عند الاستلام · تحويل بنكي · فاتورة نظامية</p>
		</div>
	</div>

	<div class="sh-footer__bottom">
		<div class="sh-container sh-footer__bottom-row">
			<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> للتجارة. جميع الحقوق محفوظة.</p>
			<p class="sh-footer__legal">العلامات التجارية المذكورة ملك لأصحابها، والشامي موزّع جملة مستقل.</p>
		</div>
	</div>
</footer>

<?php if ( $sh_has_wc && ! $sh_is_qo && ! $sh_checkout ) : ?>
<div class="sh-orderbar<?php echo $sh_count ? ' is-visible' : ''; ?>" data-sh-orderbar aria-live="polite">
	<button type="button" class="sh-orderbar__cart" data-sh-open="sh-cart-drawer" aria-controls="sh-cart-drawer" aria-label="عرض الطلبية">
		<span class="sh-orderbar__info">
			<span class="sh-orderbar__lines">الطلبية: <b data-sh-lines><?php echo (int) $sh_lines; ?></b> صنف · <b data-sh-cartons><?php echo (int) $sh_count; ?></b> كرتونة</span>
			<?php if ( shami_show_prices() ) : ?>
				<span class="sh-orderbar__total"><span class="sh-cart-total"><?php echo wp_kses_post( WC()->cart ? WC()->cart->get_cart_subtotal() : '' ); ?></span></span>
			<?php endif; ?>
		</span>
	</button>
	<a class="sh-orderbar__go" href="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-sh-checkout>إتمام الطلب</a>
</div>
<?php endif; ?>

<?php if ( shami_wa_number() && ! $sh_is_qo && ! $sh_checkout ) : ?>
	<a class="sh-wa-float" href="<?php echo esc_url( shami_wa_link( 'مرحباً، أرغب بالتواصل مع قسم المبيعات في ' . get_bloginfo( 'name' ) ) ); ?>" target="_blank" rel="noopener" aria-label="تحدث مع المبيعات عبر واتساب"><?php shami_the_icon( 'whatsapp', '', 30 ); ?></a>
<?php endif; ?>

<div class="sh-toast" role="status" aria-live="polite" aria-atomic="true"></div>

<?php wp_footer(); ?>
</body>
</html>
