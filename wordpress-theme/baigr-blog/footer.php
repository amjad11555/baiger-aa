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
			<div class="reveal">
				<span class="brand"><?php bloginfo( 'name' ); ?><span class="dot">.</span></span>
				<p class="tagline">تسويق ونموّ مبني على الذكاء الاصطناعي للخليج والشرق الأوسط وتركيا.</p>
			</div>
			<div class="reveal">
				<h4>روابط</h4>
				<ul>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">الموقع الرئيسي</a></li>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">المدوّنة</a></li>
					<li><a href="<?php echo esc_url( BAIGR_SITE ); ?>/">خدماتنا</a></li>
				</ul>
			</div>
			<div class="reveal">
				<h4>تواصل</h4>
				<ul>
					<li><a href="mailto:team@baigr.com">team@baigr.com</a></li>
					<li><a href="https://wa.me/905378573181" rel="noopener">واتساب</a></li>
				</ul>
			</div>
		</div>
		<div class="bottom">
			<span>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — جميع الحقوق محفوظة.</span>
			<span>صُمّم بعناية في الخليج.</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
