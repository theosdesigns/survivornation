<?php

$user_id = get_current_user_id();

$subforum = false;

$subforum_id = NULL;

wpbb_goback1();

?> <h2 class="wpbb-centered-bold"> <?php _e('Post Reply', 'wp-bb'); ?> </h2> <?php

if (isset($_GET['forum'])) {

	$forum_id = absint($_GET['forum']);
	$forum = true;
	
	if (isset($_GET['subforum'])) {
	
		$subforum_id = absint($_GET['subforum']);
		$subforum = true;
	}
} 

$topic_id = absint($_GET['topic']);

// Check forum permissions
$view_permissions = wpbb_user_has_permission($user_id, $forum_id);
if ($view_permissions === false) {
	?>
	<div class="wpbb-message-failure">
		<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
	</div>
	<?php
	wpbb_exit();
} else {
	if ($subforum) {
		$view_permissions = wpbb_user_has_permission($user_id, $subforum_id);
		if ($view_permissions === false) {
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view this subforum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	} else {
		$reply_permissions = wpbb_user_has_permission($user_id, $forum_id, 'reply');
		if ($reply_permissions === false) {
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to reply in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
	// Is topic locked? If so deny access to all except admins and users with lock permissions
	$topic_status = wpbb_get_topic_status($topic_id);
	if (strpos($topic_status, 'locked') !== false) {
		$lock_permissions = wpbb_user_has_permission($user_id, $forum_id, 'lock');
		if (!current_user_can('manage_options')) {
			// Not an admin, but has lock permissions?
			if ($lock_permissions == false) {
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to reply to lock topics!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		}
	}
}

// We're good...

if (isset($_POST['wpbb-reply-submit']))
{
	$content = wpbb_strip_tags($_POST['wpbb-reply-content']);
	if (empty($content))
	{
		wpbb_error('You must enter content for your reply');
		wpbb_exit();
	}

	$user_roles = wpbb_user_permission($user_id);
		
	$wpbb_options = get_option('wpbb_options');
			
	// Retrieve the delay between posts
	$post_cutoff = $wpbb_options['post_cutoff'];
	
	if ($user_roles == 'guest') {
		$user_id = 0;
		$guest_options = get_option('wpbb_guest_options');
		$user_last_post = $guest_options['guest_last_post'];
	} else {
		$user_id = get_current_user_id();
		// Get the users last post
		$user_last_post = get_user_meta($user_id, 'wpbb_lastpost', true);
	}
	// Get the current date as a formatted date string
	$current_date = date('Y-m-d H:i:s');
			
	// Convert post cutoff to seconds
	$user_last_post_secs = strtotime($user_last_post);
			
	// Topic cutoff delay in seconds multiplied by the user last post in seconds will be the number of seconds
	// in the future when that delay has passed
	$delay_passed_secs = $user_last_post_secs + $post_cutoff;
			
	// We then convert those seconds to a formatted date string using our seconds as the timestamp
	$delay_passed_date = date('Y-m-d H:i:s', $delay_passed_secs);
		
	$forum_id = absint($_GET['forum']);
		
	// If the current date is less than the delay_passed_date then we should not be allowed to create another topic
	// untill that time has passed. 
	if ($current_date < $delay_passed_date) {
		?>
		<div class="wpbb-message-failure">
			<?php printf(__('You must wait %s seconds since your last post before creating another.'), $post_cutoff); ?>
		</div>
		<?php
		wpbb_exit();
	}
	
	$topic_id = absint($_GET['topic']);
	
	$topic_status = (isset($_POST['wpbb-topic-status'])) ? implode(",", (array) $_POST['wpbb-topic-status']) : "";

	$create_post = $wpdb->insert(POST_TABLE, array('author' => $user_id, 'topic' => $topic_id, 'text' => $content));
	
	$created_post_id = $wpdb->insert_id;
	
	$date = date("Y-m-d H:i:s");
	
	// Update topics last reply and status
	$update_topic_last_reply = $wpdb->update(TOPIC_TABLE, array('last_reply' => $date, 'status' => $topic_status), array('id' => $topic_id));
	
	if ($update_topic_last_reply === false)
	{
		$update_topic_last_reply_err = __('Error: Failed to update the topics last reply. It is likely the topic doesn\'t exist or was deleted', 'wp-bb');
		error_log($update_topic_last_reply_err);
	}
	
	$created_post_link = NULL;
	if ($subforum_id > 0)
	{
		$created_post_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $subforum_id, 'topic' => $topic_id, 'current_page' => 1), wpbb_permalink());
	}
	else
	{
		$created_post_link = add_query_arg(array('forum' => $forum_id, 'topic' => $topic_id, 'current_page' => 1), wpbb_permalink());
	}
	
	if ($create_post === false) {
		?>
		<div class="wpbb-message-error">
			<?php _e('There was an error submitting a new post, please try again', 'wp-bb'); ?>
		</div>
		<?php
	} else {
		?>
		<div class="wpbb-message-success">
			<?php
			printf(__('Thank you, your post has been posted successfully. <a href="%s#%s">View your post.</a>', 'wp-bb'), $created_post_link, "wpbb-post-anchor$created_post_id");
			?>
		</div>
		<?php
		if ($user_roles != 'guest') {
			wpbb_update_user_meta($user_id, 'increase');
		}
		wpbb_update_user_lastpost($user_id);
	}	
}

?>

<table class="wpbb-table">
	<form method="POST" action="#">
		<tr>
			<th><?php _e('Content', 'wp-bb'); ?></th>
			<td> 
				<?php
				if ($wp_version == "3.2")
				{
					the_editor("", "wpbb-reply-content");
				}
				else
				{
					wp_editor('', 'wpbb-reply-content');
				}
				?>
			</td>
		</tr>
		<?php 
		$lock_permissions = wpbb_user_has_permission($user_id, $forum_id, 'lock');
		$sticky_permissions = wpbb_user_has_permission($user_id, $forum_id, 'sticky');
		if ($lock_permissions || $sticky_permissions || current_user_can('manage_options')) {
			?>
			<tr>
				<th><?php _e('Status', 'wp-bb'); ?></th>
				<td>
				<?php
				if ($lock_permissions || current_user_can('manage_options')) {
					$locked = (strpos($topic_status, 'locked') !== false) ? true : false;
					?>
					<input type="checkbox" name="wpbb-topic-status[]" value="locked" <?php echo checked($locked, true, false); ?>/> <?php _e('Locked', 'wp-bb');
				} 
				if ($sticky_permissions || current_user_can('manage_options')) {
					$sticky = (strpos($topic_status, 'sticky') !== false) ? true : false;
					?>
					<input type="checkbox" name="wpbb-topic-status[]" value="sticky" <?php echo checked($sticky, true, false); ?>/> <?php _e('Sticky', 'wp-bb');
				}
				?>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<th></th>
			<td><input type="submit" name="wpbb-reply-submit" value="<?php _e('Post Reply', 'wp-bb'); ?>" /></td>
		</tr>
	</form>
</table>
<?php
?>