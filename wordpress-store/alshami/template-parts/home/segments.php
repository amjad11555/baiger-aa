<?php
/**
 * الواجهة الرئيسية: لمن نورّد (أربع شرائح بصور).
 *
 * @package AlShami
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="sh-section sh-segments" aria-labelledby="sh-seg-title">
	<div class="sh-container">
		<div class="sh-head">
			<div class="sh-head__text">
				<p class="sh-eyebrow">لمن نورّد</p>
				<h2 class="sh-head__title" id="sh-seg-title">نخدم كل حلقة في سلسلة البيع، من البقالة إلى المستورد</h2>
			</div>
		</div>
		<ul class="sh-segments__grid">
			<?php foreach ( shami_segments() as $sh_seg ) : ?>
				<li class="sh-seg">
					<div class="sh-seg__media"><?php echo shami_img( $sh_seg['image'], '', array( 'sizes' => '(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<h3 class="sh-seg__title"><?php echo esc_html( $sh_seg['title'] ); ?></h3>
					<p class="sh-seg__text"><?php echo esc_html( $sh_seg['text'] ); ?></p>
					<?php if ( ! empty( $sh_seg['page'] ) ) : ?>
						<a class="sh-link" href="<?php echo esc_url( shami_page_url( $sh_seg['page'] ) ); ?>">تفاصيل التصدير</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
