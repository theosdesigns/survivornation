<?php

wpbb_goback1();

if (isset($_POST['wpbb-message-submit'])) {
	if (isset($_POST['wpbb-message-to'])) {
		if (is_numeric($_POST['wpbb-message-to'])) {
			$to_id = absint($_POST['wpbb-message-to']);
			$to_name = wpbb_parse_author_name($to_id);
		} else {
			$to_name = wp_strip_all_tags($_POST['wpbb-message-to']);
			$to_id = wpbb_parse_author_name(NULL, $to_name);
		}
			
		$check_id_exists = get_user_by('id', $to_id);
			
		if ($check_id_exists === false) {
			?>
			<div class="wpbb-message-failure">
				<?php
				_e("Sorry, that user doesn't exist. Please try again", "wp-bb");
				?>
			</div>
			<?php
			wpbb_exit();
		}
			
		$from = get_current_user_id();
		$subject = wp_strip_all_tags($_POST['wpbb-message-subject']);
		if (empty($subject))
		{
			wpbb_error('You must enter a subject for your message');
			wpbb_exit();
		}
		$content = wpbb_strip_tags($_POST['wpbb-message-content']);
		if (empty($content))
		{
			wpbb_error('You must enter a message');
			wpbb_exit();
		}
		$sent = date("Y-m-d H:i:s");
		$data = array('to' => $to_id, 'from' => $from, 'subject' => $subject, 'content' => $content, 'sent' => $sent);
		$send_message = $wpdb->insert(MESSAGES_TABLE, $data);
		if ($send_message === false) {
			?>
			<div class="wpbb-message-failure">
				<?php _e('There was an error sending the message. Please try again', 'wp-bb'); ?>
			</div>
			<?php
		} else {
			?>
			<div class="wpbb-message-success">
				<?php printf(__('Message to %s (%s) sent successfully.'), $to_name, $to_id); ?>
			</div>
			<?php
		}
	}
}
	
$message_id = absint($_GET['message']);

if ($message_id != 'to') {
	
	$message_id = absint($_GET['message']);
}
	
?> <h2 class="wpbb-centered-bold"><?php _e('Compose Message', 'wp-bb'); ?></h2> <?php

$message_username = wpbb_parse_author_name($message_id);
	
?>
<table class="wpbb-table">
	<form method='POST' action='#'>
		<tr>
			<th><?php _e('To', 'wp-bb'); ?></th>
			<td>
				<input type='text' name='wpbb-message-to' value='<?php echo $message_username; ?>' />
			</td>
		</tr>
		<tr>
			<th><?php _e('Subject', 'wp-bb'); ?></th>
			<td>
				<input type='text' name='wpbb-message-subject' value='' />
			</td>
		</tr>
		<tr>
			<th><?php _e('Content', 'wp-bb'); ?></th>
			<td>
					<?php 
					if ($wp_version <= "3.2")
					{
						the_editor("", "wpbb-message-content");
					}
					else
					{
						wp_editor("", 'wpbb-message-content');
					} 
					?>
			</td>
		</tr>
		<tr>
			<th></th>
			<td>
				<input type='submit' name='wpbb-message-submit' value='<?php _e('Send Message', 'wp-bb'); ?>' />
			</td>
		</tr>
	</form>
</table>
<?php
?>