<?php
/**
 * حساب التاجر: «حساب جديد» و«لدي حساب».
 *
 * التسجيل برقم الواتساب واسم المحل ومنطقته وموقعه على الخريطة (البريد اختياري)،
 * والدخول برقم الواتساب بأي صيغة أو بالبريد. معالجة التسجيل في inc/customers.php،
 * والدخول يمر بمعالج ووكومرس المعتاد.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Zad\WooCommerce
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification
$zd_next     = isset( $_REQUEST['zad_next'] ) ? sanitize_key( wp_unslash( $_REQUEST['zad_next'] ) ) : '';
$zd_posted   = ! empty( $_POST['zad_register'] );
$zd_get_tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
if ( ! empty( $_POST['login'] ) ) {
	$zd_tab = 'login';
} elseif ( $zd_posted ) {
	$zd_tab = 'register';
} elseif ( in_array( $zd_get_tab, array( 'login', 'register' ), true ) ) {
	$zd_tab = $zd_get_tab;
} else {
	// القادم من إتمام الطلب غالباً زبون جديد؛ وغيره غالباً عائد إلى حسابه.
	$zd_tab = 'checkout' === $zd_next ? 'register' : 'login';
}
$zd_values   = $zd_posted ? zad_customer_posted( 'zad_name' ) : array();
$zd_email    = $zd_posted && isset( $_POST['zad_email'] ) ? sanitize_text_field( wp_unslash( $_POST['zad_email'] ) ) : '';
$zd_username = ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
// phpcs:enable
$zd_base     = wc_get_page_permalink( 'myaccount' );
$zd_tab_url  = static function ( $tab ) use ( $zd_base, $zd_next ) {
	return add_query_arg( array_filter( array( 'tab' => $tab, 'zad_next' => $zd_next ) ), $zd_base );
};

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="zd-auth" data-zd-auth>
	<div class="zd-auth__intro">
		<p class="zd-eyebrow">حساب التاجر</p>
		<h2>حساب واحد لمحلك، وطلبياتك كلها في مكان واحد</h2>
		<ul class="zd-auth__perks">
			<li><?php zad_the_icon( 'tag', '', 20 ); ?><span>أسعار الجملة لكل الأصناف، مع سعر البيع وربحك</span></li>
			<li><?php zad_the_icon( 'bolt', '', 20 ); ?><span>طلبية بضغطات قليلة، و«اطلبها مجدداً» لطلبيتك المعتادة</span></li>
			<li><?php zad_the_icon( 'truck', '', 20 ); ?><span>توصيل إلى باب المحل بموقعه على الخريطة</span></li>
			<li><?php zad_the_icon( 'wallet', '', 20 ); ?><span>الدفع عند الاستلام نقداً أو بالبطاقة</span></li>
		</ul>
		<?php if ( zad_wa_number() ) : ?>
			<p class="zd-auth__help">تحتاج مساعدة في التسجيل؟ <a href="<?php echo esc_url( zad_wa_link( 'مرحباً، أحتاج مساعدة في إنشاء حساب لمحلي.' ) ); ?>" target="_blank" rel="noopener">راسلنا على واتساب</a></p>
		<?php endif; ?>
	</div>

	<div class="zd-auth__card">
		<div class="zd-auth__tabs" role="tablist" aria-label="حساب التاجر">
			<a class="zd-auth__tab<?php echo 'register' === $zd_tab ? ' is-active' : ''; ?>" href="<?php echo esc_url( $zd_tab_url( 'register' ) ); ?>" role="tab" id="zd-tab-register" aria-controls="zd-panel-register" aria-selected="<?php echo 'register' === $zd_tab ? 'true' : 'false'; ?>" data-zd-auth-tab="register">حساب جديد</a>
			<a class="zd-auth__tab<?php echo 'login' === $zd_tab ? ' is-active' : ''; ?>" href="<?php echo esc_url( $zd_tab_url( 'login' ) ); ?>" role="tab" id="zd-tab-login" aria-controls="zd-panel-login" aria-selected="<?php echo 'login' === $zd_tab ? 'true' : 'false'; ?>" data-zd-auth-tab="login">لدي حساب</a>
		</div>

		<div class="zd-auth__panel" id="zd-panel-login" role="tabpanel" aria-labelledby="zd-tab-login"<?php echo 'login' === $zd_tab ? '' : ' hidden'; ?>>
			<h2 class="zd-auth__title">الدخول إلى حسابك</h2>
			<form class="woocommerce-form woocommerce-form-login login zd-form" method="post">
				<?php do_action( 'woocommerce_login_form_start' ); ?>
				<div class="zd-field">
					<label for="username">رقم الواتساب أو البريد <span class="zd-req" aria-hidden="true">*</span></label>
					<input type="text" class="woocommerce-Input input-text" name="username" id="username" autocomplete="username" dir="ltr" value="<?php echo esc_attr( $zd_username ); ?>" required aria-required="true" placeholder="05xx xxx xx xx">
				</div>
				<div class="zd-field">
					<label for="password">كلمة المرور <span class="zd-req" aria-hidden="true">*</span></label>
					<input class="woocommerce-Input input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true">
				</div>
				<?php do_action( 'woocommerce_login_form' ); ?>
				<label class="woocommerce-form__label woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" checked> <span>تذكّرني على هذا الجهاز</span>
				</label>
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<input type="hidden" name="redirect" value="<?php echo esc_url( zad_account_next_url() ); ?>">
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="دخول">دخول</button>
				<p class="zd-auth__lost">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">نسيت كلمة المرور؟</a>
					<?php if ( zad_wa_number() ) : ?>
						إن لم يكن لحسابك بريد، <a href="<?php echo esc_url( zad_wa_link( 'مرحباً، نسيت كلمة مرور حسابي في الموقع. رقم الواتساب المسجّل:' ) ); ?>" target="_blank" rel="noopener">راسلنا على واتساب</a> ونعيد ضبطها لك.
					<?php endif; ?>
				</p>
				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>

		<div class="zd-auth__panel" id="zd-panel-register" role="tabpanel" aria-labelledby="zd-tab-register"<?php echo 'register' === $zd_tab ? '' : ' hidden'; ?>>
			<h2 class="zd-auth__title">أنشئ حساب محلك</h2>
			<p class="zd-auth__sub">دقيقة واحدة، مرة واحدة فقط. الحقول المطلوبة عليها <span class="zd-req">*</span></p>
			<form class="zd-form zd-auth__register" method="post" data-zd-register>
				<?php zad_customer_fields_html( $zd_values, 'zad_name', 'zd-reg' ); ?>
				<div class="zd-form__grid">
					<div class="zd-field">
						<label for="zd-reg-password">كلمة المرور <span class="zd-req" aria-hidden="true">*</span></label>
						<input type="password" class="woocommerce-Input input-text" id="zd-reg-password" name="zad_password" autocomplete="new-password" minlength="6" required aria-required="true" aria-describedby="zd-reg-password-hint">
						<small class="zd-field__hint" id="zd-reg-password-hint">6 أحرف أو أرقام على الأقل.</small>
					</div>
					<div class="zd-field">
						<label for="zd-reg-email">البريد الإلكتروني <span class="zd-field__opt">(اختياري)</span></label>
						<input type="email" id="zd-reg-email" name="zad_email" value="<?php echo esc_attr( $zd_email ); ?>" autocomplete="email" dir="ltr" aria-describedby="zd-reg-email-hint">
						<small class="zd-field__hint" id="zd-reg-email-hint">لاستعادة كلمة المرور وإشعارات الطلبيات.</small>
					</div>
				</div>
				<div class="zd-hp" aria-hidden="true"><label>اترك هذا الحقل فارغاً<input type="text" name="zd_website" tabindex="-1" autocomplete="off"></label></div>
				<?php wp_nonce_field( 'zad-register', 'zad-register-nonce' ); ?>
				<input type="hidden" name="zad_register" value="1">
				<?php if ( $zd_next ) : ?>
					<input type="hidden" name="zad_next" value="<?php echo esc_attr( $zd_next ); ?>">
				<?php endif; ?>
				<button type="submit" class="zd-btn zd-btn--primary zd-btn--lg zd-btn--block">أنشئ حسابي</button>
				<p class="zd-form__privacy">
					<?php
					$zd_privacy = get_privacy_policy_url();
					if ( $zd_privacy ) {
						printf( 'بإنشاء الحساب توافق على <a href="%s">سياسة الخصوصية</a>. نستخدم بياناتك للتوصيل والتواصل بخصوص طلبياتك فقط.', esc_url( $zd_privacy ) );
					} else {
						echo 'نستخدم بياناتك للتوصيل والتواصل بخصوص طلبياتك فقط.';
					}
					?>
				</p>
			</form>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
