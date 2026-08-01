<?php
/**
 * Footer.
 *
 * @package baigr-blog
 */
?>
</main><!-- #content -->

<footer class="site-footer">
	<div class="container">
		<div class="grid">

			<div class="col-brand reveal">
				<span class="brand"><?php bloginfo( 'name' ); ?><span class="dot">.</span></span>
				<p class="tagline">تسويق ونموّ مبني على الذكاء الاصطناعي — نساعد المتاجر والعلامات في الخليج والشرق الأوسط وتركيا على النموّ بنتائج تُقاس.</p>
				<div class="footer-social">
					<a href="https://wa.me/905378573181" aria-label="واتساب" rel="noopener">واتساب</a>
					<a href="https://instagram.com/" aria-label="انستقرام" rel="noopener">إنستقرام</a>
					<a href="mailto:team@baigr.com" aria-label="البريد">بريد</a>
				</div>
			</div>

			<nav class="reveal" aria-label="روابط سريعة">
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

		<div class="bottom">
			<span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — جميع الحقوق محفوظة.</span>
			<a class="to-top" href="#content">↑ العودة للأعلى</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
