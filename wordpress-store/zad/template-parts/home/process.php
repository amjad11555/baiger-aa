<?php
/**
 * الواجهة الرئيسية: آلية العمل في ثلاث خطوات بالصور.
 *
 * @package Zad
 */

defined( 'ABSPATH' ) || exit;

$zd_steps = array(
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
<section class="zd-section zd-process" aria-labelledby="zd-process-title">
	<div class="zd-container">
		<?php zad_section_head( 'آلية العمل', 'من الطلبية إلى رف متجرك في ثلاث خطوات', 'zd-process-title' ); ?>
		<ol class="zd-process__list">
			<?php foreach ( $zd_steps as $zd_i => $zd_step ) : ?>
				<li class="zd-pstep">
					<div class="zd-pstep__media"><?php echo zad_img( $zd_step['image'], '', array( 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<span class="zd-pstep__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $zd_i + 1 ) ); ?></span>
					<h3 class="zd-pstep__title"><?php echo esc_html( $zd_step['title'] ); ?></h3>
					<p class="zd-pstep__text"><?php echo esc_html( $zd_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
