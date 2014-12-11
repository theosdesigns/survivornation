<?php

wpbb_goback1();

$forum = absint($_GET['forum']);
	
$user_id = get_current_user_id();
	
$view_permissions = wpbb_user_has_permission($user_id, $forum);
if ($view_permissions === false)
{
	?>
	<div class="wpbb-message-failure">
		<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
	</div>
	<?php
	wpbb_exit();
}
	
$topic_id = absint($_GET['topic']);

$get_topic_read_status = $wpdb->get_var("SELECT `read` FROM ".UN_READ_TABLE." WHERE id = $topic_id AND author = $user_id;");
	
if ($get_topic_read_status == 0)
{
	$mark_topic_read = $wpdb->update(UN_READ_TABLE, array('read' => 1), array('id' => $topic_id, 'author' => $user_id));
	if ($mark_topic_read !== false)
	{
		?>
		<div class="wpbb-message-success">
			<?php printf(__('Successfully marked topic id %s as read'), $topic_id); ?>
		</div>
		<?php
	}	
}
else if ($get_topic_read_status == 1)
{
	$mark_topic_unread = $wpdb->update(UN_READ_TABLE, array('read' => 0), array('id' => $topic_id, 'author' => $user_id));
	if ($mark_topic_unread !== false)
	{
		?>
		<div class="wpbb-message-success">
			<?php printf(__('Successfully marked topic id %s as unread'), $topic_id); ?>
		</div>
		<?php
	}
}
else
{
	?>
	<div class="wpbb-message-failure">
		<?php _e('Error marking topic as read/unread. Please try again', 'wp-bb'); ?>
	</div>
	<?php
	wpbb_exit();
}
?>