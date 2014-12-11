<?php
	wpbb_goback1();

	// Check user has permissions to delete topics or posts in this forum
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
	else
	{
		$delete_permissions = wpbb_user_has_permission($user_id, $forum, 'delete');
		if ($delete_permissions === false)
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to delete topics in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
	
	$topic_id = absint($_GET['topic']);
	
	$topic_author = $wpdb->get_var("SELECT author FROM ".TOPIC_TABLE." WHERE id = $topic_id;");

	if ($topic_author != NULL)
	{
		if ($topic_author != $user_id)
		{
			$options = get_option('wpbb_options');
			$user = get_user_by('id', $user_id);
			$roles = wpbb_get_user_roles($user);
			if (array_key_exists($roles, $options['role_permissions']))
			{
				if ($options['role_permissions'][$roles] == 'no')
				{
					wpbb_error('You can only delete your own topics!');
					wpbb_exit();
				}
			}
		}
	}
	
	if ((isset($_GET['topic'])) && (!isset($_GET['post'])))
	{ 
		// Topic
		$topic_id = absint($_GET['topic']);
		$changed_mind = false;
		$success = false;
		if (isset($_POST['wpbb-confirm-delete-topic-submit']))
		{
			if ($_POST['wpbb-confirm-delete-topic'] == 'yes')
			{
				// Decrease the topic authors post count then delete the topic
				$topic_author = $wpdb->get_var("SELECT author FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
				$topic_author = intval($topic_author);
				$topic_author_post_count = get_user_meta($topic_author, 'wpbb_posts', true);
				$new_topic_author_post_count = intval($topic_author_post_count) - 1;
				update_user_meta($topic_author, 'wpbb_posts', $new_topic_author_post_count);
				$delete_topic = $wpdb->query("DELETE FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
				// Delete topic success
				if ($delete_topic !== false)
				{
					// Find all the post author ids in the topic and decrease each users post count then delete each post
					$post_authors = $wpdb->get_results("SELECT author FROM ".POST_TABLE." WHERE topic = $topic_id;", ARRAY_A);
					foreach ($post_authors as $post_author)
					{
						$post_author = intval($post_author);
						$post_author_post_count = get_user_meta($post_author, 'wpbb_posts', true);
						$new_post_author_post_count = intval($post_author_post_count) - 1;
						update_user_meta($post_author, 'wpbb_posts', $new_post_author_post_count);
					}
					$delete_posts = $wpdb->query("DELETE FROM ".POST_TABLE." WHERE topic = $topic_id;");
					// Delete posts success
					if ($delete_posts !== false)
					{
						?>
						<div class="wpbb-message-success">
							<?php printf(__('Topic ID %s and all posts were successfully deleted.'), $topic_id); ?>
						</div>
						<?php
						$success = true;
					}
				}
				// Error deleting the topic
				else
				{
					?>
					<div class="wpbb-message-failure">
						<?php printf(__('There was an error attempting to delete topic ID %s and/or its posts'), $topic_id); ?>
					</div>
					<?php
				}
			}
			// User clicked no
			else
			{
				?>
				<div class="wpbb-centered-bold">
					<?php 
						printf(__('You decided not to delete topic ID %s. Click "Go back" to return to the topic.'), $topic_id); 
					?>
				</div>
				<?php
				$changed_mind = true;
			}
		}
		// If user clicked no when asked to confirm delete request
		if ($changed_mind !== true && $success !== true)
		{
			?>
			<h1 id="wpbb-h-1" class="wpbb-centered-bold"><?php _e('Confirm Topic Delete Request', 'wp-bb'); ?></h1>
			<div class="wpbb-message-warning">
				<?php printf(__('Are you sure you want to delete topic ID %s? WARNING: All posts in this topic will be deleted. This proccess cannot be undone!'), $topic_id); ?>
			</div>
			<form method='POST' action='#' style='text-align:center;'>
				<input type='radio' name='wpbb-confirm-delete-topic' value='yes' /> <?php _e('Yes', 'wp-bb'); ?>
				<input type='radio' name='wpbb-confirm-delete-topic' value='no' checked='yes' /> <?php _e('No', 'wp-bb'); ?>
				<input type='submit' name='wpbb-confirm-delete-topic-submit' value='<?php _e('Confirm', 'wp-bb'); ?>' />
			</form>
			<?php
		}
	}
	// Dealing with an individual post deletion and NOT a topic
	else if ((isset($_GET['post']) && (isset($_GET['topic']))))
	{
		$post_id = absint($_GET['post']);
		$changed_mind = false;
		$success = false;
		// Delete post submission
		if (isset($_POST['wpbb-confirm-delete-post-submit']))
		{
			
			if ($_POST['wpbb-confirm-delete-post'] == 'yes')
			{
				$post_author = $wpdb->get_var("SELECT `author` FROM ".POST_TABLE." WHERE topic = $topic_id;");
				// Attempt to delete the post
			 	$delete_post = $wpdb->query("DELETE FROM ".POST_TABLE." WHERE id = $post_id;");
			 	if ($delete_post !== false)
			 	{
			 		// Decrease the topic authors post count only if succeeds
					$post_author = intval($post_author);
					$post_author_post_count = get_user_meta($post_author, 'wpbb_posts', true);
					$new_post_author_post_count = intval($post_author_post_count) - 1;
					update_user_meta($post_author, 'wpbb_posts', $new_post_author_post_count);
					// When the post is deleted, set the next oldest post as the last reply for this topic
					$_topic_id = absint($_GET['topic']);
					$new_forum_last_post = $wpdb->get_var("SELECT max(created) FROM ".POST_TABLE." WHERE topic = $_topic_id;");
					$update_topic = $wpdb->update(TOPIC_TABLE, array('last_reply' => $new_forum_last_post), array('id' => $_topic_id));
			 		// Success
			 		?>
			 		<div class="wpbb-message-success">
			 			<?php printf(__('Post ID %s was successfully deleted.'), $post_id); ?>
			 		</div>
					<?php
					$success = true;
				}
				else
				{
					?>
					<div class="wpbb-message-failure">
						<?php printf(__('There was an error attempting to delete post ID %s, please try again!'), $post_id); ?>
					</div>
					<?php
				}
			}
			else
			{ 
				// Changed mind
				?>
				<div class="wpbb-centered-bold">
					<?php printf(__('You decided not to delete the post ID %s, you will be redirected...'), $post_id); ?>
				</div>
				<?php
				$changed_mind = true;
			}
		}
		// End of delete post submission
		if ($changed_mind !== true && $success !== true)
		{
			?>
			<h2 class="wpbb-centered-bold"><?php _e('Confirm Post Delete Request', 'wp-bb'); ?></h2>
			<div class="wpbb-centered-bold">
				<?php printf(__('Are you sure you want to delete post ID %s ? (WARNING: This proccess cannot be undone!)'), $post_id); ?>
			</div>
			<form method='POST' action='#' style='text-align:center;'>
				<input type='radio' name='wpbb-confirm-delete-post' value='yes' /> <?php _e('Yes', 'wp-bb'); ?>
				<input type='radio' name='wpbb-confirm-delete-post' value='no' checked='yes' /> <?php _e('No', 'wp-bb'); ?>
				<input type='submit' name='wpbb-confirm-delete-post-submit' value='<?php _e('Confirm', 'wp-bb'); ?>' />
			</form>
			<?php
		}
	}
?>