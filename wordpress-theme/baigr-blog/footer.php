<?php
/**
 * Footer.
 *
 * @package baigr-blog
 */
?>
</main><!-- #content -->

<footer class="site-footer">
	<div class="bgr-container">

		<div class="footer-cta reveal">
			<h2 class="footer-cta__title">جاهز تنقل تسويقك إلى <em>المستوى التالي؟</em></h2>
			<a class="btn btn--solid btn--lg magnetic" href="<?php echo esc_url( BAIGR_SITE ); ?>/">لنعمل معاً <span aria-hidden="true">←</span></a>
		</div>

		<div class="footer-grid">
			<div class="footer-brandcol reveal">
				<span class="brand"><?php bloginfo( 'name' ); ?><span class="dot">.</span></span>
				<p class="tagline">تسويق ونموّ مبني على الذكاء الاصطناعي — نساعد المتاجر والعلامات في الخليج والشرق الأوسط وتركيا على النموّ بنتائج تُقاس.</p>
				<div class="footer-social">
					<a href="https://wa.me/905378573181" aria-label="واتساب" rel="noopener" target="_blank">
						<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.9c0 2.1.55 4.15 1.6 5.95L2 22l4.3-1.68a9.9 9.9 0 0 0 5.74 1.5c5.46 0 9.9-4.45 9.9-9.9C21.94 6.45 17.5 2 12.04 2Zm5.8 14.06c-.24.68-1.4 1.3-1.94 1.34-.5.05-1.13.07-1.82-.11a12.7 12.7 0 0 1-1.65-.61c-2.9-1.26-4.8-4.19-4.94-4.38-.15-.19-1.2-1.6-1.2-3.05 0-1.45.76-2.16 1.03-2.46.27-.3.58-.37.78-.37l.56.01c.18.01.42-.07.66.5.24.58.82 2.01.9 2.16.07.15.12.32.02.51-.1.19-.15.31-.3.48-.15.17-.31.38-.44.51-.15.15-.3.31-.13.6.17.29.76 1.25 1.63 2.03 1.12 1 2.07 1.31 2.36 1.46.29.15.46.12.63-.07.17-.19.73-.85.92-1.14.19-.29.39-.24.66-.15.27.1 1.7.8 1.99.95.29.15.48.22.55.34.07.12.07.68-.17 1.36Z"/></svg>
					</a>
					<a href="https://instagram.com/" aria-label="إنستقرام" rel="noopener" target="_blank">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
					</a>
					<a href="mailto:team@baigr.com" aria-label="البريد الإلكتروني">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
					</a>
				</div>
			</div>

			<nav class="reveal" aria-label="روابط">
				<h4>تصفّح</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">المدوّنة</a></li>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">الموقع الرئيسي</a></li>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">من نحن</a></li>
				</ul>
			</nav>

			<nav class="reveal" aria-label="الخدمات">
				<h4>خدماتنا</h4>
				<ul>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">إعلانات الأداء</a></li>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">بناء العلامة</a></li>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">تصميم المواقع</a></li>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">أتمتة بالذكاء الاصطناعي</a></li>
				</ul>
			</nav>

			<nav class="reveal" aria-label="تواصل">
				<h4>تواصل معنا</h4>
				<ul>
					<li><a href="mailto:team@baigr.com">team@baigr.com</a></li>
					<li><a href="https://wa.me/905378573181" rel="noopener">‏+90 537 857 31 81</a></li>
				</ul>
			</nav>
		</div>

		<div class="footer-bottom">
			<span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — جميع الحقوق محفوظة.</span>
			<a class="to-top" href="#content">↑ العودة للأعلى</a>
		</div>
	</div>

	<div class="footer-mark" aria-hidden="true">BAIGR</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
