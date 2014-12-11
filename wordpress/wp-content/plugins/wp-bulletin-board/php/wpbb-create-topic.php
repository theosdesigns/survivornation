<?php

wpbb_goback1();

$user_id = get_current_user_id();
	
$forum_id = absint($_GET['forum']);
	
wpbb_check_exists('forum', $forum_id);
	
if ((isset($_GET['forum'])) && (!isset($_GET['subforum'])))
{
	$view_permissions = wpbb_user_has_permission($user_id, $forum_id);
	if ($view_permissions === false)
	{
		?>
		<div class="wpbb-message-failure">
			<?php _e('You do not have the required permissions view this forum!', 'wp-bb'); ?>
		</div>
		<?php
		wpbb_exit();
	}
	else
	{
		$create_permissions = wpbb_user_has_permission($user_id, $forum_id, 'post');
		if ($create_permissions === false)
		{
			wpbb_goback1('create_denied', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to create topics in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
}
else
{
	// Check user has permissions to access forum
	$view_permissions = wpbb_user_has_permission($user_id, $forum_id);
	if ($view_permissions === false)
	{
		wpbb_goback1('create_denied', NULL);
		?>
		<div class="wpbb-message-failure">
			<?php _e('You do not have the required permissions view this forum!', 'wp-bb'); ?>
		</div>
		<?php
		wpbb_exit();
	}
	else
	{
		// Check user has permissions to access subforum
		$subforum_id = absint($_GET['subforum']);
		$view_permissions = wpbb_user_has_permission($user_id, $subforum_id);
		if ($view_permissions === false)
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions view this subforum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
		else
		{
			$create_permissions = wpbb_user_has_permission($user_id, $subforum_id, 'post');
			if ($create_permissions === false)
			{
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to create topics in this subforum!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		}
	}
}

$user_roles = wpbb_user_permission($user_id);
		
$wpbb_options = get_option('wpbb_options');
			
// Retrieve the delay between posts
$post_cutoff = $wpbb_options['post_cutoff'];
	
if ($user_roles == 'guest')
{
	$guest_options = get_option('wpbb_guest_options');
	$user_last_post = $guest_options['guest_last_post'];
}
else
{
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
	
// If the current date is less than the delay_passed_date then we should not be allowed to create another topic
// untill that time has passed. 
if ($current_date < $delay_passed_date)
{
	?>
	<div class="wpbb-centered-bold">
		<?php printf(__('You must wait %s seconds since your last topic before creating another.'), $topic_cutoff); ?>
	</div>
	<?php
	wpbb_exit();
}

if (isset($_POST['wpbb-topic-submit']))
{
	$topic_title = wp_strip_all_tags($_POST['wpbb-topic-title']);
	$topic_content = wpbb_strip_tags($_POST['wpbbtopiccontent']);
	if (empty($topic_title))
	{
		wpbb_error('You must enter a topic title');
		wpbb_exit();
	}
	if (empty($topic_content))
	{
		wpbb_error('You must enter some content for your topic');
		wpbb_exit();
	}
	$topic_author = get_current_user_id();
	if ((isset($_GET['forum'])) && (!isset($_GET['subforum'])))
	{
		$forum_id = absint($_GET['forum']);
		$subforum_id = 0;
		$forum_name = true;
	}
	else if (isset($_GET['subforum']))
	{
		$subforum_id = absint($_GET['subforum']);
		$forum_id = 0;
		$forum_name = false;
	}
	// We don't want to strip any tags that we may use in the editor
	$topic_status = "";
	if (isset($_POST['wpbb-topic-status']))
	{
		$topic_status = implode(",", (array) $_POST['wpbb-topic-status']);
	}
	$date = date("Y-m-d H:i:s");
	$data = array(
		'author' => $topic_author,
		'forum' => $forum_id,
		'subforum' => $subforum_id,
		'name' => $topic_title,
		'content' => $topic_content,
		'status' => $topic_status,
		'created' => $date,
		'last_reply' => $date
	);
	$create_topic = $wpdb->insert(TOPIC_TABLE, $data);
	$created_topic_id = $wpdb->insert_id;
	$mark_topic_read = $wpdb->insert(UN_READ_TABLE, array('id' => $wpdb->insert_id, 'author' => $topic_author, 'read' => 1));
	if ($mark_topic_read === false)
	{
		error_log(__('There was an error trying to insert the new topic into the un_read_table and mark it as read', 'wp-bb'));
	}
	$forum_id = $forum_id == true ? $forum_id : $subforum_id;
	$forum_name = $forum_name == true ? 'forum' : 'subforum';
	$is_subforum = false;
	$created_topic_link = add_query_arg(array('forum' => $forum_id, 'topic' => $created_topic_id, 'current_page' => 1), wpbb_permalink());
	if ($subforum_id > 0)
	{
		$is_subforum = true;
		$created_topic_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $subforum_id, 'topic' => $created_topic_id, 'current_page' => 1), wpbb_permalink());
	}
	if ($create_topic === false)
	{
		?>
		<div class="wpbb-message-failure">
			<?php printf(__('There was an error creating a topic in %s ID %s, please try again'), $forum_name, $forum_id); ?>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="wpbb-message-success">
			<?php printf(__('Thank you, your topic was successfully posted to %s ID %s. <a href="%s">View your topic.</a>'), $forum_name, $forum_id, $created_topic_link); ?>
		</div>
		<?php
		if ($user_roles != 'guest') {
			wpbb_update_user_meta($user_id, 'increase');
		}
		wpbb_update_user_lastpost($user_id);
	}
}
	
/* Location Links */
$forum_id = absint($_GET['forum']);
if ((isset($_GET['forum'])) && !(isset($_GET['subforum'])))
{
	$title = wpbb_location($forum_id, NULL, NULL, false, false, false);
	?>
	<h2 class="wpbb-centered-bold">
		<?php printf(__('Creating a topic in %s'), $title['forum']); ?>
	</h2>
	<?php
}
else if (isset($_GET['subforum']))
{
	$subforum_id = absint($_GET['subforum']);
	$title = wpbb_location($forum_id, $subforum_id, true, false, false);

	$forum_link = add_query_arg(array('forum' => $forum_id));
	$subforum_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $subforum_id));
	?>
	<h2 class="wpbb-centered-bold">
		<?php printf(__('Creating a topic in <a href="%s">%s</a> subforum <a href="%s">%s</a>'), $forum_link, $title['forum'], $subforum_link, $title['subforum']); ?>
	</h2>
	<?php
}
/* End of Location Links */
?>
<form action='#' method='POST'>
	<table class="wpbb-table">
		<tr>
			<th><?php _e('Title', 'wp-bb'); ?></th>
			<td>
				<input type='text' name='wpbb-topic-title' />
			</td>
		</tr>
			<th><?php _e('Content', 'wp-bb'); ?></th>
			<td> 
				<?php 
				if ($wp_version <= "3.2")
				{
					the_editor("", "wpbbtopiccontent");
				}
				else
				{
					wp_editor('', 'wpbbtopiccontent');
				}
				?>
			</td>
		</tr>
		<?php 
		$lock_permissions = wpbb_user_has_permission($user_id, $forum_id, 'lock');
		$sticky_permissions = wpbb_user_has_permission($user_id, $forum_id, 'sticky');
		if ($lock_permissions || $sticky_permissions || current_user_can('manage_options'))
		{
			?>
			<tr>
				<th><?php _e('Status', 'wp-bb'); ?></th>
				<td>
				<?php
				if ($lock_permissions || current_user_can('manage_options')) {
					?>
					<input type="checkbox" name="wpbb-topic-status[]" value="locked"/> <?php _e('Locked', 'wp-bb');
				} 
				if ($sticky_permissions || current_user_can('manage_options')) {
					?>
					<input type="checkbox" name="wpbb-topic-status[]" value="sticky"/> <?php _e('Sticky', 'wp-bb');
				}
				?>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<th></th>
			<td><input type="submit" name="wpbb-topic-submit" value="<?php _e('Create Topic', 'wp-bb'); ?>" /></td>
		</tr>
	</table>
</form>