<?php
/**
 * الواجهة الرئيسية: حفظ رابط المتجر ومشاركته (بنمط حقل النشرة البريدية).
 *
 * @package Maria
 */

defined( 'ABSPATH' ) || exit;

$mr_url   = home_url( '/' );
$mr_share = sprintf( 'حلويات تركية أصلية بالجملة للبقالات من %1$s: كيك وبسكويت وشيبس وتسالي بسعر الكرتونة 👇 %2$s', get_bloginfo( 'name' ), $mr_url );
?>
<section class="mr-section mr-news" aria-labelledby="mr-news-title">
	<div class="mr-container mr-container--narrow">
		<h2 class="mr-news__title" id="mr-news-title">احفظ رابط ماريا وشاركه مع أصحاب المحلات</h2>
		<div class="mr-news__field">
			<label class="screen-reader-text" for="mr-share-url">رابط المتجر</label>
			<input class="mr-news__input" id="mr-share-url" type="text" value="<?php echo esc_attr( $mr_url ); ?>" readonly dir="ltr">
			<button type="button" class="mr-news__btn" data-mr-copy="<?php echo esc_attr( $mr_url ); ?>" aria-label="نسخ رابط المتجر"><?php maria_the_icon( 'copy', '', 24 ); ?></button>
		</div>
		<p class="mr-news__alt"><a class="mr-btn mr-btn--wa" href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( $mr_share ) ); ?>" target="_blank" rel="noopener"><?php maria_the_icon( 'whatsapp', '', 20 ); ?> مشاركة عبر واتساب</a></p>
	</div>
</section>
