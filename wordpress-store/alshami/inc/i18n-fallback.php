<?php
/**
 * ترجمة احتياطية لأهم نصوص ووكومرس في الواجهة.
 *
 * تعمل فقط إن لم تكن لغة الموقع العربية (مثلاً قبل تنزيل حزمة الترجمة)،
 * لضمان أن يرى التاجر واجهة عربية بالكامل دائماً.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

/**
 * هل نحتاج الترجمة الاحتياطية؟
 *
 * @return bool
 */
function shami_needs_i18n_fallback() {
	static $needs = null;
	if ( null === $needs ) {
		$needs = ( ! is_admin() || wp_doing_ajax() ) && 0 !== strpos( determine_locale(), 'ar' );
	}
	return $needs;
}

/**
 * جدول الترجمة.
 *
 * @return array
 */
function shami_i18n_map() {
	return array(
		'Add to cart'                                   => 'أضف إلى الطلبية',
		'Proceed to checkout'                           => 'إتمام الطلب',
		'Cart totals'                                   => 'ملخص الطلبية',
		'Subtotal'                                      => 'المجموع الفرعي',
		'Subtotal:'                                     => 'المجموع الفرعي:',
		'Update cart'                                   => 'تحديث الطلبية',
		'Apply coupon'                                  => 'تطبيق الكوبون',
		'Coupon code'                                   => 'رمز الكوبون',
		'Coupon:'                                       => 'كوبون:',
		'Enter your coupon code'                        => 'أدخل رمز الكوبون',
		'Have a coupon?'                                => 'لديك كوبون خصم؟',
		'Click here to enter your code'                 => 'اضغط هنا لإدخاله',
		'Billing details'                               => 'بيانات المتجر والتسليم',
		'Billing &amp; Shipping'                        => 'بيانات المتجر والتسليم',
		'Your order'                                    => 'طلبك',
		'Place order'                                   => 'تأكيد الطلب',
		'Cash on delivery'                              => 'الدفع عند الاستلام',
		'Pay with cash upon delivery.'                  => 'ادفع نقداً عند استلام الطلب.',
		'Additional information'                        => 'معلومات إضافية',
		'Order notes'                                   => 'ملاحظات الطلب',
		'Remove %s from cart'                           => 'إزالة %s من الطلبية',
		'Remove item'                                   => 'إزالة المنتج',
		'View cart'                                     => 'عرض الطلبية',
		'Checkout'                                      => 'إتمام الطلب',
		'No products in the cart.'                      => 'الطلبية فارغة حالياً.',
		'Your cart is currently empty.'                 => 'طلبيتك فارغة حالياً.',
		'Return to shop'                                => 'العودة إلى المتجر',
		'Related products'                              => 'منتجات ذات صلة',
		'Default sorting'                               => 'الترتيب الافتراضي',
		'Sort by popularity'                            => 'الأكثر طلباً',
		'Sort by average rating'                        => 'الأعلى تقييماً',
		'Sort by latest'                                => 'الأحدث',
		'Sort by price: low to high'                    => 'السعر: من الأقل للأعلى',
		'Sort by price: high to low'                    => 'السعر: من الأعلى للأقل',
		'Shop order'                                    => 'ترتيب المتجر',
		'Showing the single result'                     => 'عرض النتيجة الوحيدة',
		'Sale!'                                         => 'عرض!',
		'Shipping'                                      => 'التوصيل',
		'Shipping:'                                     => 'التوصيل:',
		'Total'                                         => 'الإجمالي',
		'Total:'                                        => 'الإجمالي:',
		'Product'                                       => 'المنتج',
		'Price'                                         => 'السعر',
		'Quantity'                                      => 'الكمية',
		'Product quantity'                              => 'كمية المنتج',
		'%s quantity'                                   => 'كمية %s',
		'Order received'                                => 'تم استلام الطلب',
		'Order number:'                                 => 'رقم الطلب:',
		'Date:'                                         => 'التاريخ:',
		'Email:'                                        => 'البريد:',
		'Payment method:'                               => 'طريقة الدفع:',
		'Order details'                                 => 'تفاصيل الطلب',
		'Billing address'                               => 'عنوان التوصيل',
		'Free shipping'                                 => 'توصيل مجاني',
		'Local pickup'                                  => 'استلام من المستودع',
		'Flat rate'                                     => 'رسوم توصيل ثابتة',
		'(optional)'                                    => '(اختياري)',
		'optional'                                      => 'اختياري',
		'Country / Region'                              => 'الدولة',
		'Out of stock'                                  => 'نفدت الكمية',
		'In stock'                                      => 'متوفر',
		'%s in stock'                                   => 'متوفر: %s',
		'Search products&hellip;'                       => 'ابحث عن منتج…',
		'Search results: &ldquo;%s&rdquo;'              => 'نتائج البحث عن: «%s»',
		'Shop'                                          => 'المتجر',
		'Home'                                          => 'الرئيسية',
		'Email address'                                 => 'البريد الإلكتروني',
		'Phone'                                         => 'الهاتف',
		'Thank you. Your order has been received.'      => 'شكراً لك، تم استلام طلبك.',
		'Update totals'                                 => 'تحديث المجموع',
		'Note:'                                         => 'ملاحظة:',
		'Pay'                                           => 'ادفع',
		'Actions'                                       => 'إجراءات',
		'Create an account?'                            => 'إنشاء حساب؟',
		'My account'                                    => 'حسابي',
		'Description'                                   => 'الوصف',
		'Reviews'                                       => 'التقييمات',
		'Reviews (%d)'                                  => 'التقييمات (%d)',
		'Category:'                                     => 'القسم:',
		'SKU:'                                          => 'رمز المنتج:',
		'Undo?'                                         => 'تراجع؟',
		'&ldquo;%s&rdquo; has been removed from your cart' => 'تمت إزالة «%s» من الطلبية',
		'&ldquo;%s&rdquo; has been added to your cart.' => 'تمت إضافة «%s» إلى الطلبية.',
		'%s has been added to your cart.'               => 'تمت إضافة %s إلى الطلبية.',
		'Cart updated.'                                 => 'تم تحديث الطلبية.',
		'Please fill in your details above to see available payment methods.' => 'أدخل بياناتك أعلاه لعرض طرق الدفع المتاحة.',
		'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.' => 'عذراً، لا توجد طرق دفع متاحة حالياً. تواصل معنا وسنرتب الطلب معك.',
		'No shipping options were found for %s.'        => 'لا توجد طرق توصيل متاحة لـ %s.',
		'%s is a required field.'                       => 'حقل %s مطلوب.',
		'%s is not a valid phone number.'               => 'رقم %s غير صالح.',
		'Invalid billing email address'                 => 'البريد الإلكتروني غير صالح',
		'Lost your password?'                           => 'نسيت كلمة المرور؟',
		'Login'                                         => 'تسجيل الدخول',
		'Log in'                                        => 'تسجيل الدخول',
		'Register'                                      => 'حساب جديد',
		'Username or email address'                     => 'اسم المستخدم أو البريد',
		'Password'                                      => 'كلمة المرور',
		'Remember me'                                   => 'تذكرني',
		'Dashboard'                                     => 'لوحة الحساب',
		'Orders'                                        => 'الطلبات',
		'Addresses'                                     => 'العناوين',
		'Account details'                               => 'بيانات الحساب',
		'Log out'                                       => 'تسجيل الخروج',
		'Order'                                         => 'الطلب',
		'Date'                                          => 'التاريخ',
		'Status'                                        => 'الحالة',
		'View'                                          => 'عرض',
		'Order again'                                   => 'اطلبه مرة أخرى',
		'Read more'                                     => 'المزيد',
		'Select options'                                => 'اختر الخيارات',
		'Estimated total'                               => 'الإجمالي التقديري',
		'Continue shopping'                             => 'متابعة التسوق',
		'Store'                                         => 'المتجر',
		'Original price was: %s.'                       => 'السعر الأصلي: %s.',
		'Current price is: %s.'                         => 'السعر الحالي: %s.',
		'Awaiting product image'                        => 'صورة المنتج',
		'Shipment'                                      => 'التوصيل',
		'Shipment %s'                                   => 'التوصيل %s',
		'Shipping to %s.'                               => 'التوصيل إلى %s.',
		'Change address'                                => 'تغيير العنوان',
		'Calculate shipping'                            => 'احسب التوصيل',
		'Update'                                        => 'تحديث',
		'Enter your address to view shipping options.'  => 'أدخل عنوانك لعرض خيارات التوصيل.',
		'Shipping options will be updated during checkout.' => 'تُحدّث خيارات التوصيل عند إتمام الطلب.',
		'Shipping costs are calculated during checkout.' => 'تُحسب تكلفة التوصيل عند إتمام الطلب.',
		'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.' => 'نستخدم بياناتك لمعالجة طلبك والتواصل معك بخصوصه فقط، كما هو موضّح في %s.',
		'privacy policy'                                => 'سياسة الخصوصية',
		'Free!'                                         => 'مجاناً',
		'Billing'                                       => 'بيانات المتجر',
		'Shipping address'                              => 'عنوان التوصيل',
		'Note'                                          => 'ملاحظة',
		'Order summary'                                 => 'ملخص الطلب',
		'Placeholder'                                   => 'صورة المنتج',
	);
}

/**
 * فلتر الترجمة.
 *
 * @param string $translation النص المترجم.
 * @param string $text        النص الأصلي.
 * @return string
 */
function shami_i18n_gettext( $translation, $text ) {
	if ( ! shami_needs_i18n_fallback() ) {
		return $translation;
	}
	static $map = null;
	if ( null === $map ) {
		$map = shami_i18n_map();
	}
	return isset( $map[ $text ] ) && $translation === $text ? $map[ $text ] : $translation;
}
add_filter( 'gettext_woocommerce', 'shami_i18n_gettext', 10, 2 );

/**
 * فلتر الترجمة مع السياق.
 *
 * @param string $translation المترجم.
 * @param string $text        الأصل.
 * @return string
 */
function shami_i18n_gettext_context( $translation, $text ) {
	return shami_i18n_gettext( $translation, $text );
}
add_filter( 'gettext_with_context_woocommerce', 'shami_i18n_gettext_context', 10, 2 );

/**
 * فلتر الجمع.
 *
 * @param string $translation المترجم.
 * @param string $single      المفرد.
 * @param string $plural      الجمع.
 * @param int    $number      العدد.
 * @return string
 */
function shami_i18n_ngettext( $translation, $single, $plural, $number ) {
	if ( ! shami_needs_i18n_fallback() ) {
		return $translation;
	}
	$map = array(
		'Showing all %1$d result'                     => 'عرض كل المنتجات (%1$d)',
		'Showing all %d result'                       => 'عرض كل المنتجات (%d)',
		'Showing %1$d&ndash;%2$d of %3$d result'      => 'عرض %1$d–%2$d من أصل %3$d منتج',
		'%s item'                                     => '%s منتج',
		'%d item'                                     => '%d منتج',
		'%s product has been added to your cart.'     => 'تمت إضافة %s إلى الطلبية.',
		'%s has been added to your cart.'             => 'تمت إضافة %s إلى الطلبية.',
		'Showing the single result'                   => 'عرض النتيجة الوحيدة',
	);
	return isset( $map[ $single ] ) ? $map[ $single ] : $translation;
}
add_filter( 'ngettext_woocommerce', 'shami_i18n_ngettext', 10, 4 );

/**
 * فلتر الجمع مع السياق.
 *
 * @param string $translation المترجم.
 * @param string $single      المفرد.
 * @param string $plural      الجمع.
 * @param int    $number      العدد.
 * @return string
 */
function shami_i18n_ngettext_context( $translation, $single, $plural, $number ) {
	return shami_i18n_ngettext( $translation, $single, $plural, $number );
}
add_filter( 'ngettext_with_context_woocommerce', 'shami_i18n_ngettext_context', 10, 4 );

/**
 * أسماء الأشهر في التواريخ المعروضة للعميل (صفحة الشكر، حسابي) حين تكون لغة الموقع غير العربية.
 *
 * @param string $date   التاريخ المنسّق.
 * @param string $format صيغة التاريخ.
 * @return string
 */
function shami_i18n_date( $date, $format ) {
	if ( ! shami_needs_i18n_fallback() || ! preg_match( '/[FM]/', $format ) ) {
		return $date;
	}
	$en = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
	$ar = array( 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر' );
	$date = str_replace( $en, $ar, $date );
	$date = str_replace( array_map( static fn( $m ) => substr( $m, 0, 3 ), $en ), $ar, $date );
	return str_replace( ',', '،', $date );
}
add_filter( 'date_i18n', 'shami_i18n_date', 10, 2 );
