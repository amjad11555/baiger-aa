<?php
/**
 * الواجهة الرئيسية: آلية العمل في ثلاث خطوات بالصور.
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;

$sh_steps = array(
	array(
		'image' => 'step-order',
		'title' => 'اختر الأصناف والكميات',
		'text'  => 'افتح قائمة الأسعار من جوالك وحدد عدد الكراتين لكل صنف. يظهر الإجمالي فوراً، وترسل الطلبية عبر الموقع أو واتساب.',
	),
	array(
		'image' => 'step-pick',
		'title' => 'نجهّز الطلبية ونؤكدها',
		'text'  => 'يراجع فريق المستودع الأصناف والتوفر وتواريخ الصلاحية، ثم نتواصل معك لتأكيد الطلبية وموعد التسليم.',
	),
	array(
		'image' => 'step-deliver',
		'title' => 'نسلّمها إلى متجرك',
		'text'  => 'نوصل داخل إسطنبول خلال 24 إلى 48 ساعة، وإلى باقي الولايات وفق جدول التوزيع. تدفع عند الاستلام أو بالتحويل البنكي.',
	),
);
?>
<section class="sh-section sh-section--paper sh-process" aria-labelledby="sh-process-title">
	<div class="sh-container">
		<div class="sh-head sh-head--center">
			<div class="sh-head__text">
				<p class="sh-eyebrow">آلية العمل</p>
				<h2 class="sh-head__title" id="sh-process-title">من الطلبية إلى رف متجرك في ثلاث خطوات</h2>
			</div>
		</div>
		<ol class="sh-process__list">
			<?php foreach ( $sh_steps as $sh_i => $sh_step ) : ?>
				<li class="sh-step">
					<div class="sh-step__media"><?php echo shami_img( $sh_step['image'], '', array( 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<span class="sh-step__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $sh_i + 1 ) ); ?></span>
					<h3 class="sh-step__title"><?php echo esc_html( $sh_step['title'] ); ?></h3>
					<p class="sh-step__text"><?php echo esc_html( $sh_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
