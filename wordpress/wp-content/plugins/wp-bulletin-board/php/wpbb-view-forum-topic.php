<?php

	

	$topic_id = absint($_GET['topic']);
	
	wpbb_check_exists('topic', $topic_id);
	
	$user_id = get_current_user_id();
	
	$forum = absint($_GET['forum']);
	
	// Check user can view this forum first incase user visited topic directly
	$view_permissions = wpbb_user_has_permission($user_id, $forum);
	if ($view_permissions === false) {
		if (wpbb_is_user_logged_in()) {
			wpbb_goback1('forum_topic_denied', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	} else {
		$read_permissions = wpbb_user_has_permission($user_id, $forum, 'read');
		if ($read_permissions === false) {
			if (wpbb_is_user_logged_in()) {
				wpbb_goback1('forum_topic_denied', NULL);
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to read topics in this forum!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		}
	}
	$topic_status = wpbb_get_topic_status($topic_id);
	if (strpos($topic_status, 'locked') !== false) {
		$locked_permissions = wpbb_user_has_permission($user_id, $forum, 'lock');
		// If user does not have lock permissions or is not an admin they cannot view a locked topic!
		if ($locked_permissions === false && !current_user_can('manage_options')) {
			wpbb_goback1();
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view locked topics in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
	
	/*
	*	Mark the topic as read for the user if it is unread
	*/
	
	$is_topic_read = $wpdb->get_var("SELECT `read` FROM ".UN_READ_TABLE." WHERE `author` = $user_id AND `id` = $topic_id;");
	
	if ($is_topic_read !== NULL)
	{
		if ($is_topic_read === 0)
		{
			$mark_topic_read = $wpdb->update(UN_READ_TABLE, array('read' => 1), array('author' => $user_id, 'id' => $topic_id));
		}
	}
	else
	{
		$mark_topic_read_insert = $wpdb->insert(UN_READ_TABLE, array('id' => $topic_id, 'author' => $user_id, 'read' => 1));
	}
	
	/*
	*  	When the user has created a quick reply post
	*/
	
	if (isset($_POST['wpbb-quick-reply-submit']))
	{
		$content = wpbb_strip_tags($_POST['wpbb-quick-reply-content']);
		if (empty($content))
		{
			wpbb_goback1();
			wpbb_error('Reply content cannot be blank');
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
			wpbb_goback1();
			?>
			<div class="wpbb-message-failure">
				<?php printf(__('You must wait %s seconds since your last post before creating another.', 'wp-bb'), $post_cutoff); ?>
			</div>
			<?php
			wpbb_exit();
		}
	
		// Check forum permissions
		$view_permissions = wpbb_user_has_permission($user_id, $forum_id);
		if ($view_permissions === false) {
			wpbb_goback1('forum_topic_denied', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		} else {
			$reply_permissions = wpbb_user_has_permission($user_id, $forum_id, 'reply');
			if ($reply_permissions === false) {
				wpbb_goback1('forum_topic_denied', NULL);
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to reply in this forum!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		}
		$create_post = $wpdb->insert(POST_TABLE, array('author' => $user_id, 'topic' => $topic_id, 'text' => $content));
		$date = date("Y-m-d H:i:s");
		$update_topic_last_reply = $wpdb->update(TOPIC_TABLE, array('last_reply' => $date), array('id' => $topic_id));
		if ($update_topic_last_reply === false) {
			_e('Error: Failed to update the topics last reply. It is likely the topic doesn\'t exist or was deleted', 'wp-bb');
		}
		if ($create_post === false) {
			wpbb_goback1('forum_topic_post_error', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('There was an error submitting a new post, please try again', 'wp-bb'); ?>
			</div>
			<?php
		} else {
			wpbb_goback1('forum_topic_post_success', NULL);
			?>
			<div class="wpbb-message-success">
				<?php _e('Thank you, your post has been posted successfully', 'wp-bb'); ?>
			</div>
			<?php
			// Mark this topic as unread for all users except the current user
			$mark_topic_read_all = $wpdb->update(UN_READ_TABLE, array('read' => 0), array('id' => $topic_id));
			// Mark the topic as read for the poster
			$mark_topic_unread_poster = $wpdb->update(UN_READ_TABLE, array('read' => 1), array('author' => $user_id));						if ($user_roles != 'guest')
			{
				wpbb_update_user_meta($user_id, 'increase');
			}
			wpbb_update_user_lastpost($user_id);
		}
	}
	$title = wpbb_location(NULL, NULL, $topic_id, false, true, false);
	?>
	<h2 class="wpbb-forum-title">
		<?php echo $title['forum_name']." / " . $title['forum'] . " / " . $title['topic']; ?>
	</h2>
	<?php
	$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
	if ($topics) {
		foreach ($topics as $topic) {
			$options = get_option('wpbb_options');
			$posts_per_page = $options['posts_per_page'];
			if ($_GET['current_page'] == 0) {
				$current_page = 1;
				$start = 0;
				$limit = $posts_per_page;
			} else if ($_GET['current_page'] == 1) {
				$current_page = 1;
				$start = 0;
				$limit = $posts_per_page;
			} else {
				$current_page = $_GET['current_page'];
				$start = $current_page * $posts_per_page - $posts_per_page;
				$limit = $start + $posts_per_page;
			}
			$forum = absint($_GET['forum']);
			$topic_id = absint($_GET['topic']);
			$total_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
			if (($total_posts > 0) && ($total_posts > $posts_per_page)) {
				wpbb_pagination($forum, $current_page, $start, $limit, $total_posts, $posts_per_page, false);
			}
			wpbb_buttons($forum, NULL, $topic_id, 'forum_topic');
			$user_posts = get_user_meta($topic->author, 'wpbb_posts', true);
			$signature = get_user_meta($topic->author, 'wpbb_signature', true);
			?>
			<table class="wpbb-table">
				<th>
					<?php _e('Author', 'wp-bb'); ?>
				</th>
				<th>
					<?php _e('Content', 'wp-bb'); ?>
				</th>
				<?php
				if ($current_page == 1) {
			
					$author = get_userdata($topic->author);
					
					?>
					<tr>
						<?php
					
						if ($author === false) {
				
							?>
							<td class="wpbb-topic-profile">
								<?php _e('Guest', 'wp-bb'); ?>
								<br />
								<?php echo get_avatar(0); ?>
							</td>
							<?php
					
						} else {
				
							?>
							<td class="wpbb-topic-profile">
								<a href='<?php echo add_query_arg(array('profile' => $topic->author), wpbb_permalink()); ?>'>
								<?php echo $author->display_name; ?>
								</a> 
								<br /> <?php echo get_avatar($topic->author); ?>
								<br /> <?php printf(__('Posts: %s', 'wp-bb'), $user_posts); ?>
							</td>
							<?php
						}
						?>
						<td>
							<p class="wpbb-topic-and-post-date">
								<a name="wpbb-topic-anchor">#0</a> <?php wpbb_moderate_links('forum', 'topic', $user_id, $forum, NULL, $topic->id, NULL, $topic->created); ?>
							</p>
							<p class="wpbb-topic-and-post-content">
								<?php 
								$content = apply_filters('the_content', $topic->content);
								echo $content = str_replace(']]>', ']]&gt;', $content);
								?>
							</p>
							<hr>
							<p class="wpbb-topic-and-post-signature">
								<?php echo convert_smilies($signature); ?>
							</p>
						</td>
					</tr>
					<?php
				}
				$posts = $wpdb->get_results("SELECT * FROM ".POST_TABLE." WHERE topic = $topic_id ORDER BY created ASC LIMIT $start, $limit;");
				if ($posts) {
					foreach ($posts as $post) {
					$user_posts = get_user_meta($post->author, 'wpbb_posts', true);
					$signature = get_user_meta($post->author, 'wpbb_signature', true);
					$author = get_userdata($post->author);
					?>
					<tr>
						<td class="wpbb-topic-profile">
							<?php 
							if (!$author) {
								_e('Guest', 'wpbb');
								?>
								<br />
								<?php
								echo get_avatar(0);
							} else {	
								?>
								<a href='<?php echo add_query_arg(array('profile' => $post->author), wpbb_permalink()); ?>'><?php echo $author->display_name; ?></a>
								<br /><?php echo get_avatar($post->author); ?>
								<br /><?php printf(__('Posts: %s', 'wp-bb'), $user_posts);
							}
							?>
						</td>
						<td>
							<p class="wpbb-topic-and-post-date">
								<a name="wpbb-post-anchor<?php echo $post->id;?>">#<?php echo $post->id;?></a> <?php wpbb_moderate_links('forum', 'post', $user_id, $forum, NULL, $topic->id, $post->id, $post->created); ?>
							</p>
							<p class="wpbb-topic-and-post-content">
								<?php
								$content = apply_filters('the_content', $post->text);
								echo $content = str_replace(']]>', ']]&gt;', $content);
								?>
							</p>
							<hr>
							<p class="wpbb-topic-and-post-signature"</p>
							<?php echo convert_smilies($signature); ?>
						</td>
					</tr>
					<?php
					}
				}
				?>
				</table>
				</div>
			<?php
		}
		wpbb_buttons($forum, NULL, $topic_id, 'forum_topic');
		if ($options['enable_quick_reply'] == 'yes') {
			?>
			<div class="clear"></div>
			<div class="wpbb-quick-reply">
				<form action='#' method='POST'>
					<div>
						<?php _e('Content', 'wp-bb'); ?>
					</div>
					<div>
						<textarea name='wpbb-quick-reply-content'></textarea>
					</div>
					<div>
						<input type='submit' name='wpbb-quick-reply-submit' value='<?php _e('Quick Reply', 'wp-bb'); ?>' />
					</div>
				</form>
			</div>
			<?php
		}
	}
?>