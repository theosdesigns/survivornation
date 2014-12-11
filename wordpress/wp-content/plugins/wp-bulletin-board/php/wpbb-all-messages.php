<?php

$user_id = wpbb_is_user_logged_in();

global $wpdb;

?>
<h1 id="wpbb-h-1" class="wpbb-centered-bold">
	<?php _e('My Messages', 'wp-bb'); ?>
</h1>
	
<a href='<?php echo add_query_arg(array('message' => 'to'), wpbb_permalink()); ?>'>
	<p class="wpbb-centered">
			<?php _e('Compose Message', 'wp-bb'); ?>
	</p>
</a>

	
<?php
if ($user_id != 0) {
	$options = get_option('wpbb_options');
	$topics_per_page = $options['topics_per_page'];
	if (isset($_GET['current_page'])) {
		if ($_GET['current_page'] == 0) {
			$current_page = 1;
			$start = 0;
			$limit = $topics_per_page;
		} else if ($_GET['current_page'] == 1) {
			$current_page = 1;
			$start = 0;
			$limit = $topics_per_page;
		} else {
			$current_page = $_GET['current_page'];
			$start = $current_page * $topics_per_page - $topics_per_page;
			$limit = $start + $topics_per_page;
		}
	}
	$total_msgs = $wpdb->get_var("SELECT COUNT(*) as 'Messages' FROM ".MESSAGES_TABLE." WHERE `to` = $user_id;");
	$msgs = false;
	if (($total_msgs > 0) && ($total_msgs > $topics_per_page)) {
		wpbb_pagination(NULL, $current_page, $start, $limit, $total_msgs, $topics_per_page, true, 'messages');
	}
	$msgs = $wpdb->get_results("SELECT * FROM ".MESSAGES_TABLE." WHERE `to` = $user_id ORDER BY sent DESC LIMIT $start, $limit;");
	if ($msgs) {
		wpbb_goback1('all-messages', NULL);
		?>
		<table class="wpbb-table">
			<th><?php _e('From', 'wp-bb'); ?></th>
			<th><?php _e('Subject', 'wp-bb'); ?></th>
			<th><?php _e('Read', 'wp-bb'); ?></th>
			<th><?php _e('Sent', 'wp-bb'); ?></th>
			<th><?php _e('Action', 'wp-bb'); ?></th>
				<?php
				foreach ($msgs as $msg) {
					$msg_from_name = wpbb_parse_author_name($msg->from);
					?>
					<tr>
						<td>
							<a href='<?php echo add_query_arg(array('profile' => $msg->from), wpbb_permalink()); ?>'>
								<?php printf(__('%s'), $msg_from_name); ?>
							</a>
						</td>
				
						<td>
							<a href='<?php echo add_query_arg(array('messages' => 'all', 'view' => $msg->id), wpbb_permalink()); ?>'>
								<?php printf(__('%s'), $msg->subject); ?>
							</a>
						</td>
						<td>
							<?php printf(__('%s'), $msg->read); ?>
						</td>
						<td>
							<?php printf(__('%s'), $msg->sent); ?>
						</td>
						<td>
							<a href='<?php echo add_query_arg(array('messages' => 'all', 'delete_msg' => $msg->id), wpbb_permalink()); ?>'>
								<?php _e('Delete', 'wp-bb'); ?>
							</a>
							<a href="<?php echo add_query_arg(array('message' => $msg->from), wpbb_permalink()); ?>">
								<?php _e('Reply', 'wp-bb'); ?>
							</a>
						</td
					</tr>
					<?php
				}
				?>
				</table>
			<?php
		} else {
			wpbb_goback1('all-messages', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php printf(__('You do not have any messages. <a href="%s">Compose one?</a>'), add_query_arg(array('message' => 'to'), wpbb_permalink())); ?>
			</div>
		<?php
		}
	}
?>