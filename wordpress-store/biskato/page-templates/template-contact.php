<?php
/**
 * Template Name: تواصل معنا
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="zd-main zd-contact">
	<section class="zd-page-hero">
		<div class="zd-page-hero__media"><?php echo zad_img( 'about-team', '', array( 'sizes' => '100vw', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div class="zd-container zd-page-hero__inner">
			<?php zad_breadcrumbs(); ?>
			<p class="zd-eyebrow zd-eyebrow--light">قسم المبيعات</p>
			<h1 class="zd-page-hero__title"><?php the_title(); ?></h1>
			<div class="zd-page-hero__intro">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>
	</section>

	<div class="zd-container zd-contact__grid">
		<div class="zd-contact__cards">
			<?php if ( zad_wa_number() ) : ?>
				<a class="zd-contact-card zd-contact-card--wa" href="<?php echo esc_url( zad_wa_link( 'مرحباً، أرغب بالتواصل مع قسم المبيعات' ) ); ?>" target="_blank" rel="noopener">
					<small>واتساب المبيعات · الأسرع</small>
					<strong><bdi dir="ltr">+<?php echo esc_html( zad_wa_number() ); ?></bdi></strong>
				</a>
			<?php endif; ?>
			<?php if ( zad_opt( 'phone' ) ) : ?>
				<a class="zd-contact-card" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', zad_opt( 'phone' ) ) ); ?>">
					<small>الهاتف</small>
					<strong><bdi dir="ltr"><?php echo esc_html( zad_opt( 'phone' ) ); ?></bdi></strong>
				</a>
			<?php endif; ?>
			<?php if ( zad_opt( 'email' ) ) : ?>
				<a class="zd-contact-card" href="mailto:<?php echo esc_attr( zad_opt( 'email' ) ); ?>">
					<small>البريد الإلكتروني</small>
					<strong><?php echo esc_html( zad_opt( 'email' ) ); ?></strong>
				</a>
			<?php endif; ?>
			<div class="zd-contact-card">
				<small>العنوان / المستودع</small>
				<strong><?php echo esc_html( zad_opt( 'address' ) ? zad_opt( 'address' ) : zad_opt( 'city' ) . '، تركيا' ); ?></strong>
			</div>
			<div class="zd-contact-card">
				<small>ساعات العمل</small>
				<strong><?php echo esc_html( zad_opt( 'hours' ) ); ?></strong>
			</div>
			<?php if ( zad_opt( 'address' ) ) : ?>
				<div class="zd-map">
					<iframe title="موقعنا على الخريطة" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( zad_opt( 'address' ) ) . '&output=embed' ); ?>"></iframe>
				</div>
			<?php endif; ?>
		</div>

		<div class="zd-form-card" id="zd-form">
			<div class="zd-form-card__head">
				<h2>أرسل رسالة إلى قسم المبيعات</h2>
				<p>لفتح حساب جملة، أو طلب أسعار الكميات، أو ترتيب جدول توريد ثابت. نرد خلال ساعات العمل.</p>
			</div>
			<?php if ( ! zad_form_feedback( 'contact' ) ) : ?>
				<form class="zd-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-zd-form>
					<?php echo zad_form_guard_fields(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="hidden" name="zd_type" value="contact">
					<div class="zd-form__grid">
						<?php
						zad_form_field( 'contact', 'name', array( 'autocomplete' => 'name' ) );
						zad_form_field( 'contact', 'phone', array( 'placeholder' => '05xx xxx xx xx', 'autocomplete' => 'tel' ) );
						zad_form_field( 'contact', 'email', array( 'autocomplete' => 'email', 'dir' => 'ltr' ) );
						zad_form_field( 'contact', 'subject' );
						zad_form_field( 'contact', 'message' );
						?>
					</div>
					<button type="submit" class="zd-btn zd-btn--primary zd-btn--lg zd-btn--block">أرسل الرسالة</button>
				</form>
			<?php endif; ?>
		</div>
	</div>

	<?php zad_render_faqs( 'contact' ); ?>
</main>
<?php
get_footer();
