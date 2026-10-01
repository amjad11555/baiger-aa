<?php
/**
 * الواجهة الرئيسية: نص تعريفي للسيو (مختصر مع «اقرأ المزيد»).
 *
 * يكتب بلغة صاحب المحل العربي في إسطنبول: ما يبحث عنه فعلاً («جملة اسطنبول»، «تاجر جملة بسكويت»،
 * أسماء الأحياء، والأسماء التركية للأصناف).
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_site  = get_bloginfo( 'name' );
$zd_city  = zad_opt( 'city' );
$zd_count = (int) wp_count_posts( 'product' )->publish;
$zd_count = $zd_count >= 20 ? (int) floor( $zd_count / 10 ) * 10 : $zd_count;
$zd_eti   = function_exists( 'zad_brand_discount' ) ? zad_brand_discount( 'eti' ) : 0;
$zd_ulk   = function_exists( 'zad_brand_discount' ) ? zad_brand_discount( 'ulker' ) : 0;
?>
<section class="zd-section zd-seo-home" aria-labelledby="zd-seo-title">
	<div class="zd-container zd-container--narrow zd-prose">
		<h2 id="zd-seo-title">تاجر جملة كيك وبسكويت وشيبس في <?php echo esc_html( $zd_city ); ?> لأصحاب المحلات العربية</h2>
		<p><strong><?php echo esc_html( $zd_site ); ?></strong> مورّد جملة في <?php echo esc_html( $zd_city ); ?> يخدم <strong>البقالات والماركتات ومحلات الحلويات العربية</strong>: <?php echo esc_html( $zd_count ); ?> صنفاً من <strong>الكيك والبسكويت والشيبس والشوكولاتة والسكاكر والعلكة والعصائر</strong>، من <strong>إيتي (Eti)</strong> و<strong>أولكر (Ülker)</strong> و<strong>بونجو (Bonucci)</strong> و<strong>الوان (Elvan)</strong> و<strong>شولين (Şölen)</strong> وغيرها. تطلب بالكرتونة من جوالك، ونوصل مجاناً إلى باب محلك، وتدفع نقداً عند الاستلام.</p>
		<details class="zd-more">
			<summary>المزيد عن <?php echo esc_html( $zd_site ); ?></summary>
			<h3>أسعار جملة حقيقية، وخصم دائم على إيتي وأولكر</h3>
			<p>أسعارنا مخصصة لأصحاب المحلات فقط، لذلك تظهر فور فتح حساب مجاني.<?php if ( $zd_eti > 0 || $zd_ulk > 0 ) : ?> وعلى كل أصناف <?php echo $zd_eti > 0 ? esc_html( 'إيتي خصم دائم ' . ( 0 + $zd_eti ) . '%' ) : ''; ?><?php echo ( $zd_eti > 0 && $zd_ulk > 0 ) ? ' و' : ''; ?><?php echo $zd_ulk > 0 ? esc_html( 'أولكر خصم دائم ' . ( 0 + $zd_ulk ) . '%' ) : ''; ?> يُحسب تلقائياً في السلة.<?php endif; ?> الحد الأدنى للطلبية <?php echo (int) zad_min_cartons(); ?> كرتونة مشكّلة من أي أصناف تختارها.</p>
			<h3>نوصل إلى كل مناطق إسطنبول</h3>
			<p>توصيل مجاني خلال 24 إلى 48 ساعة إلى المحلات في <strong>الفاتح وإسنيورت وباشاك شهير وباغجلار وسلطان غازي وإسنلر وأفجلار وزيتون بورنو وكوتشوك تشكمجة وبيليك دوزو</strong>، وإلى الجانب الآسيوي في <strong>أسكودار وعمرانية وبنديك وسلطان بيلي</strong> وكل أحياء المدينة. نسلّم الكراتين عند الباب أو نرتّبها على رفوفك.</p>
			<h3>كيف تدفع؟</h3>
			<p>داخل إسطنبول: <strong>نقداً عند الاستلام فقط</strong>، بلا دفع مسبق. للطلبات خارج إسطنبول أو خارج تركيا: <strong>تحويل إلى حسابنا البنكي الرسمي</strong>، ونرسل لك بيانات الحساب مع تأكيد الطلبية. فاتورة نظامية مع كل طلبية.</p>
			<h3>ابحث بالاسم الذي تعرفه</h3>
			<p>تجد كل صنف باسمه العربي والتركي كما هو مكتوب على العبوة: <strong>براوني (Browni)</strong> و<strong>بوب كيك (Popkek)</strong> و<strong>بورشاك (Burçak)</strong> و<strong>بيسكريم (Biskrem)</strong> و<strong>هاللي (Halley)</strong> و<strong>كراكس (Crax)</strong>، مع عدد القطع في الكرتونة. وإن لم تجد صنفاً تحتاجه، أرسل <strong>طلب توريد خاص</strong> ونؤمّنه لك.</p>
			<p class="zd-note"><?php echo esc_html( $zd_site ); ?> موزّع جملة مستقل، والعلامات التجارية المذكورة ملك لأصحابها.</p>
		</details>
	</div>
</section>
