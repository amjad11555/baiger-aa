<?php
/**
 * Template Name: تواصل معنا
 *
 * @package Lazza
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="lz-main lz-contact">
	<section class="lz-page-hero lz-page-hero--contact">
		<div class="lz-container">
			<?php lazza_breadcrumbs(); ?>
			<span class="lz-kicker lz-kicker--light"><?php lazza_the_icon( 'phone', '', 16 ); ?> نحن هنا لخدمة بقالتك</span>
			<h1 class="lz-page-hero__title"><?php the_title(); ?></h1>
			<div class="lz-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>

	<div class="lz-container lz-contact__grid">
		<div class="lz-contact__cards">
			<?php if ( lazza_wa_number() ) : ?>
				<a class="lz-contact-card lz-contact-card--wa" href="<?php echo esc_url( lazza_wa_link( 'مرحباً، لدي استفسار' ) ); ?>" target="_blank" rel="noopener">
					<span class="lz-contact-card__icon"><?php lazza_the_icon( 'whatsapp', '', 28 ); ?></span>
					<span><strong>واتساب (الأسرع)</strong><small dir="ltr">+<?php echo esc_html( lazza_wa_number() ); ?></small></span>
				</a>
			<?php endif; ?>
			<?php if ( lazza_opt( 'phone' ) ) : ?>
				<a class="lz-contact-card" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', lazza_opt( 'phone' ) ) ); ?>">
					<span class="lz-contact-card__icon"><?php lazza_the_icon( 'phone', '', 26 ); ?></span>
					<span><strong>الهاتف</strong><small dir="ltr"><?php echo esc_html( lazza_opt( 'phone' ) ); ?></small></span>
				</a>
			<?php endif; ?>
			<?php if ( lazza_opt( 'email' ) ) : ?>
				<a class="lz-contact-card" href="mailto:<?php echo esc_attr( lazza_opt( 'email' ) ); ?>">
					<span class="lz-contact-card__icon"><?php lazza_the_icon( 'mail', '', 26 ); ?></span>
					<span><strong>البريد الإلكتروني</strong><small><?php echo esc_html( lazza_opt( 'email' ) ); ?></small></span>
				</a>
			<?php endif; ?>
			<div class="lz-contact-card">
				<span class="lz-contact-card__icon"><?php lazza_the_icon( 'pin', '', 26 ); ?></span>
				<span><strong>العنوان / المستودع</strong><small><?php echo esc_html( lazza_opt( 'address' ) ? lazza_opt( 'address' ) : lazza_opt( 'city' ) . '، تركيا' ); ?></small></span>
			</div>
			<div class="lz-contact-card">
				<span class="lz-contact-card__icon"><?php lazza_the_icon( 'clock', '', 26 ); ?></span>
				<span><strong>ساعات العمل</strong><small><?php echo esc_html( lazza_opt( 'hours' ) ); ?></small></span>
			</div>
			<?php if ( lazza_opt( 'address' ) ) : ?>
				<div class="lz-map">
					<iframe title="موقعنا على الخريطة" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( lazza_opt( 'address' ) ) . '&output=embed' ); ?>"></iframe>
				</div>
			<?php endif; ?>
		</div>

		<div class="lz-form-card" id="lz-form">
			<div class="lz-form-card__head">
				<h2>أرسل لنا رسالة</h2>
				<p>للطلبات الكبيرة، الاستفسارات، أو الشراكات.</p>
			</div>
			<?php if ( ! lazza_form_feedback( 'contact' ) ) : ?>
				<form class="lz-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-lz-form>
					<?php echo lazza_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="lz_type" value="contact">
					<div class="lz-form__grid">
						<?php
						lazza_form_field( 'contact', 'name', array( 'autocomplete' => 'name' ) );
						lazza_form_field( 'contact', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
						lazza_form_field( 'contact', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
						lazza_form_field( 'contact', 'subject' );
						lazza_form_field( 'contact', 'message' );
						?>
					</div>
					<button type="submit" class="lz-btn lz-btn--primary lz-btn--lg lz-btn--block"><?php lazza_the_icon( 'send', '', 20 ); ?> إرسال</button>
				</form>
			<?php endif; ?>
		</div>
	</div>

	<?php lazza_render_faqs( 'contact' ); ?>
</main>
<?php
get_footer();
