<?php

wpbb_goback1('view-message', NULL);

$user_id = get_current_user_id();
	
if (isset($_GET['view'])) {
	$message_id = absint($_GET['view']);

	
$get_message = $wpdb->get_results("SELECT `id`, `from`, `subject`, `content`, `read`, `sent` FROM ".MESSAGES_TABLE." WHERE id = $message_id AND `to` = $user_id ORDER BY sent DESC;");
		
if ($get_message) {
	?>
	<table class="wpbb-table">
		<th><?php _e('From', 'wp-bb'); ?></th>
		<th><?php _e('Message', 'wp-bb'); ?></th>
		<?php
		foreach ($get_message as $message) {
			// Mark message as read
			if ($message->read == 0) {	
				$wpdb->update(MESSAGES_TABLE, array('read' => 1), array('id' => $message->id));	
			}	
			$message_from_name = wpbb_parse_author_name($message->from);
			$message_from_avatar = get_avatar($message->from);
			$signature = get_user_meta($message->from, 'wpbb_signature', true);
			$reply_to_message = "<a href=".add_query_arg(array('message' => $message->from), wpbb_permalink()).">".__('Reply', 'wp-bb')."</a>";
			$delete_message = "<a href=".add_query_arg(array('messages' => 'all', 'delete_msg' => $message->id), wpbb_permalink()).">".__('Delete', 'wp-bb')."</a>";
			?>
			<tr>
				<td class="wpbb-topic-profile">
					<a href='<?php echo add_query_arg(array('profile' => $message->from), wpbb_permalink()); ?>'>
						<?php echo $message_from_name; ?>
					</a>
					<br /><?php echo $message_from_avatar; ?>
				</td>
				<td>
					<p class="wpbb-message-subject-date">
						<strong><?php printf(__('( Subject: %s )'), $message->subject); ?></strong>
						<?php echo  $message->sent . " " . $reply_to_message . " " . $delete_message; ?>
					</p>
					
					<p class="wpbb-topic-and-post-content">
						
						<?php echo convert_smilies($message->content); ?>
					</p>
					<hr>
					<p class="wpbb-topic-and-post-signature">
						<?php echo convert_smilies($signature); ?>
					</p>
				</td>
			</tr>
			<?php
		}
	?>
	</table>
	<?php
} else {
	wpbb_goback1('view-message-error', NULL);
	?>
	<div class="wpbb-centered">
		<?php _e('You can only view your own messages!', 'wp-bb'); ?>
	</div>
	<?php
}
}
?>