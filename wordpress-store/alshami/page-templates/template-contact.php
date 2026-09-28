<?php
/**
 * Template Name: تواصل معنا
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="sh-main sh-contact">
	<section class="sh-page-hero">
		<div class="sh-page-hero__media"><?php echo shami_img( 'about-team', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="sh-container sh-page-hero__inner">
			<?php shami_breadcrumbs(); ?>
			<p class="sh-eyebrow sh-eyebrow--light">قسم المبيعات</p>
			<h1 class="sh-page-hero__title"><?php the_title(); ?></h1>
			<div class="sh-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>

	<div class="sh-container sh-contact__grid">
		<div class="sh-contact__cards">
			<?php if ( shami_wa_number() ) : ?>
				<a class="sh-contact-card sh-contact-card--wa" href="<?php echo esc_url( shami_wa_link( 'مرحباً، أرغب بالتواصل مع قسم المبيعات' ) ); ?>" target="_blank" rel="noopener">
					<small>واتساب المبيعات · الأسرع</small>
					<strong><bdi dir="ltr">+<?php echo esc_html( shami_wa_number() ); ?></bdi></strong>
				</a>
			<?php endif; ?>
			<?php if ( shami_opt( 'phone' ) ) : ?>
				<a class="sh-contact-card" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', shami_opt( 'phone' ) ) ); ?>">
					<small>الهاتف</small>
					<strong><bdi dir="ltr"><?php echo esc_html( shami_opt( 'phone' ) ); ?></bdi></strong>
				</a>
			<?php endif; ?>
			<?php if ( shami_opt( 'email' ) ) : ?>
				<a class="sh-contact-card" href="mailto:<?php echo esc_attr( shami_opt( 'email' ) ); ?>">
					<small>البريد الإلكتروني</small>
					<strong><?php echo esc_html( shami_opt( 'email' ) ); ?></strong>
				</a>
			<?php endif; ?>
			<div class="sh-contact-card">
				<small>العنوان / المستودع</small>
				<strong><?php echo esc_html( shami_opt( 'address' ) ? shami_opt( 'address' ) : shami_opt( 'city' ) . '، تركيا' ); ?></strong>
			</div>
			<div class="sh-contact-card">
				<small>ساعات العمل</small>
				<strong><?php echo esc_html( shami_opt( 'hours' ) ); ?></strong>
			</div>
			<?php if ( shami_opt( 'address' ) ) : ?>
				<div class="sh-map">
					<iframe title="موقعنا على الخريطة" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( shami_opt( 'address' ) ) . '&output=embed' ); ?>"></iframe>
				</div>
			<?php endif; ?>
		</div>

		<div class="sh-form-card" id="sh-form">
			<div class="sh-form-card__head">
				<h2>أرسل رسالة إلى قسم المبيعات</h2>
				<p>لفتح حساب جملة، أو طلب أسعار الكميات، أو ترتيب جدول توريد ثابت. نرد خلال ساعات العمل.</p>
			</div>
			<?php if ( ! shami_form_feedback( 'contact' ) ) : ?>
				<form class="sh-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-sh-form>
					<?php echo shami_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="sh_type" value="contact">
					<div class="sh-form__grid">
						<?php
						shami_form_field( 'contact', 'name', array( 'autocomplete' => 'name' ) );
						shami_form_field( 'contact', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
						shami_form_field( 'contact', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
						shami_form_field( 'contact', 'subject' );
						shami_form_field( 'contact', 'message' );
						?>
					</div>
					<button type="submit" class="sh-btn sh-btn--primary sh-btn--lg sh-btn--block">أرسل الرسالة</button>
				</form>
			<?php endif; ?>
		</div>
	</div>

	<?php shami_render_faqs( 'contact' ); ?>
</main>
<?php
get_footer();
