<?php
/**
 * Template Name: تواصل معنا
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="mr-main mr-contact">
	<section class="mr-page-hero mr-page-hero--contact">
		<div class="mr-container">
			<?php maria_breadcrumbs(); ?>
			<span class="mr-kicker"><?php maria_the_icon( 'phone', '', 16 ); ?> نحن هنا لخدمة بقالتك</span>
			<h1 class="mr-page-hero__title"><?php the_title(); ?></h1>
			<div class="mr-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>

	<div class="mr-container mr-contact__grid">
		<div class="mr-contact__cards">
			<?php if ( maria_wa_number() ) : ?>
				<a class="mr-contact-card mr-contact-card--wa" href="<?php echo esc_url( maria_wa_link( 'مرحباً، لدي استفسار' ) ); ?>" target="_blank" rel="noopener">
					<span class="mr-contact-card__icon"><?php maria_the_icon( 'whatsapp', '', 28 ); ?></span>
					<span><strong>واتساب (الأسرع)</strong><small dir="ltr">+<?php echo esc_html( maria_wa_number() ); ?></small></span>
				</a>
			<?php endif; ?>
			<?php if ( maria_opt( 'phone' ) ) : ?>
				<a class="mr-contact-card" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', maria_opt( 'phone' ) ) ); ?>">
					<span class="mr-contact-card__icon"><?php maria_the_icon( 'phone', '', 26 ); ?></span>
					<span><strong>الهاتف</strong><small dir="ltr"><?php echo esc_html( maria_opt( 'phone' ) ); ?></small></span>
				</a>
			<?php endif; ?>
			<?php if ( maria_opt( 'email' ) ) : ?>
				<a class="mr-contact-card" href="mailto:<?php echo esc_attr( maria_opt( 'email' ) ); ?>">
					<span class="mr-contact-card__icon"><?php maria_the_icon( 'mail', '', 26 ); ?></span>
					<span><strong>البريد الإلكتروني</strong><small><?php echo esc_html( maria_opt( 'email' ) ); ?></small></span>
				</a>
			<?php endif; ?>
			<div class="mr-contact-card">
				<span class="mr-contact-card__icon"><?php maria_the_icon( 'pin', '', 26 ); ?></span>
				<span><strong>العنوان / المستودع</strong><small><?php echo esc_html( maria_opt( 'address' ) ? maria_opt( 'address' ) : maria_opt( 'city' ) . '، تركيا' ); ?></small></span>
			</div>
			<div class="mr-contact-card">
				<span class="mr-contact-card__icon"><?php maria_the_icon( 'clock', '', 26 ); ?></span>
				<span><strong>ساعات العمل</strong><small><?php echo esc_html( maria_opt( 'hours' ) ); ?></small></span>
			</div>
			<?php if ( maria_opt( 'address' ) ) : ?>
				<div class="mr-map">
					<iframe title="موقعنا على الخريطة" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( maria_opt( 'address' ) ) . '&output=embed' ); ?>"></iframe>
				</div>
			<?php endif; ?>
		</div>

		<div class="mr-form-card" id="mr-form">
			<div class="mr-form-card__head">
				<h2>أرسل لنا رسالة</h2>
				<p>للطلبات الكبيرة، الاستفسارات، أو الشراكات.</p>
			</div>
			<?php if ( ! maria_form_feedback( 'contact' ) ) : ?>
				<form class="mr-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-mr-form>
					<?php echo maria_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="mr_type" value="contact">
					<div class="mr-form__grid">
						<?php
						maria_form_field( 'contact', 'name', array( 'autocomplete' => 'name' ) );
						maria_form_field( 'contact', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
						maria_form_field( 'contact', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
						maria_form_field( 'contact', 'subject' );
						maria_form_field( 'contact', 'message' );
						?>
					</div>
					<button type="submit" class="mr-btn mr-btn--primary mr-btn--lg mr-btn--block"><?php maria_the_icon( 'send', '', 20 ); ?> إرسال</button>
				</form>
			<?php endif; ?>
		</div>
	</div>

	<?php maria_render_faqs( 'contact' ); ?>
</main>
<?php
get_footer();
