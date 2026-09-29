<?php
/**
 * «بيانات المحل»: تعديل الاسم واسم المحل ورقم الواتساب والمنطقة والعنوان والموقع، والبريد، وكلمة المرور.
 *
 * يُرسل إلى معالج ووكومرس المعتاد (save_account_details)، وتتحقق inc/customers.php من حقول المحل وتحفظها.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Zad\WooCommerce
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Missing
if ( ! empty( $_POST['save_account_details'] ) ) {
	$zd_values = zad_customer_posted( 'account_first_name' );
} else {
	$zd_p      = zad_customer_profile( $user->ID );
	$zd_values = array(
		'name'     => $zd_p['name'],
		'shop'     => $zd_p['shop'],
		'wa_raw'   => $zd_p['wa'] ? zad_format_phone( $zd_p['wa'] ) : '',
		'district' => $zd_p['district'],
		'city'     => 'other' === $zd_p['district'] ? $zd_p['city'] : '',
		'address'  => $zd_p['address'],
		'lat'      => null !== $zd_p['lat'] ? $zd_p['lat'] : '',
		'lng'      => null !== $zd_p['lng'] ? $zd_p['lng'] : '',
	);
}
// phpcs:enable

do_action( 'woocommerce_before_edit_account_form' );
?>

<form class="woocommerce-EditAccountForm edit-account zd-form" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> data-zd-register>

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<fieldset class="zd-form__section">
		<legend>بيانات المحل</legend>
		<?php zad_customer_fields_html( $zd_values, 'account_first_name', 'zd-acc' ); ?>
	</fieldset>

	<fieldset class="zd-form__section">
		<legend>البريد الإلكتروني <span>اختياري</span></legend>
		<div class="zd-field">
			<label for="account_email">البريد الإلكتروني</label>
			<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" dir="ltr" value="<?php echo esc_attr( $user->user_email ); ?>" aria-describedby="account_email_hint">
			<small class="zd-field__hint" id="account_email_hint">لاستعادة كلمة المرور وإشعارات الطلبيات.</small>
		</div>
	</fieldset>

	<?php do_action( 'woocommerce_edit_account_form_fields' ); ?>

	<fieldset class="zd-form__section">
		<legend>تغيير كلمة المرور <span>اتركها فارغة إن لم ترد تغييرها</span></legend>
		<div class="zd-form__grid">
			<div class="zd-field zd-field--wide">
				<label for="password_current">كلمة المرور الحالية</label>
				<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="current-password">
			</div>
			<div class="zd-field">
				<label for="password_1">كلمة المرور الجديدة</label>
				<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="new-password">
			</div>
			<div class="zd-field">
				<label for="password_2">أعد كتابة كلمة المرور الجديدة</label>
				<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="new-password">
			</div>
		</div>
	</fieldset>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<input type="hidden" name="account_last_name" value="">
		<input type="hidden" name="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>">
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="zd-btn zd-btn--primary zd-btn--lg" name="save_account_details" value="حفظ">حفظ التغييرات</button>
		<input type="hidden" name="action" value="save_account_details">
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
