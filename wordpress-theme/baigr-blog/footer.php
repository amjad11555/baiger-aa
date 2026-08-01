<?php
/**
 * Footer template.
 *
 * @package baigr-blog
 */
?>
</main><!-- #content -->

<footer class="site-footer">
	<div class="cols">
		<div>
			<div class="brand"><span style="width:12px;height:12px;border-radius:50%;background:var(--gold);display:inline-block"></span> <?php bloginfo( 'name' ); ?></div>
			<p style="max-width:340px;color:#b7b1a4">تسويق ونموّ مبني على الذكاء الاصطناعي للخليج والشرق الأوسط وتركيا.</p>
		</div>
		<div>
			<h4>روابط</h4>
			<ul>
				<li><a href="https://baigr.com/">الموقع الرئيسي</a></li>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">المدوّنة</a></li>
				<li><a href="https://baigr.com/">خدماتنا</a></li>
			</ul>
		</div>
		<div>
			<h4>تواصل</h4>
			<ul>
				<li><a href="mailto:team@baigr.com">team@baigr.com</a></li>
				<li><a href="https://wa.me/905378573181">واتساب</a></li>
			</ul>
		</div>
	</div>
	<div class="footer-bottom">
		© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — جميع الحقوق محفوظة.
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
