<?php
/**
 * Comments template.
 *
 * @package baigr-blog
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h3><?php echo esc_html( get_comments_number() ); ?> تعليق</h3>
		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 44,
			) );
			?>
		</ol>
		<?php
		the_comments_pagination( array(
			'prev_text' => '→ السابق',
			'next_text' => 'التالي ←',
		) );
		?>
	<?php endif; ?>

	<?php
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments">التعليقات مغلقة.</p>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'        => 'أضف تعليقاً',
		'label_submit'       => 'إرسال التعليق',
		'class_submit'       => 'submit',
		'comment_notes_before' => '',
	) );
	?>
</div>
