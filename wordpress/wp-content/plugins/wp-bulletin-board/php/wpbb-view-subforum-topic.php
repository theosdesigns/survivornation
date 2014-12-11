<?php

	$topic_id = absint($_GET['topic']);
	
	wpbb_check_exists('topic', $topic_id);
	
	// Third parameter is 'view' by default
	$user_id = get_current_user_id();
	$forum = absint($_GET['forum']);
	// For the create topic link
	$subforum = absint($_GET['subforum']);
	
	// Check user can view this forum first incase user visited topic directly
	$view_permissions = wpbb_user_has_permission($user_id, $forum);
	if ($view_permissions === false) {
		if (wpbb_is_user_logged_in()) {
			wpbb_goback1('subforum_topic_denied', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	} else {
		// Check they can access the subforum, too
		$subforum = absint($_GET['subforum']);
		$view_permissions = wpbb_user_has_permission($user_id, $subforum);
		if ($view_permissions === false) {
			if (wpbb_is_user_logged_in()) {
				wpbb_goback1('subforum_topic_denied', NULL);
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to view topics in this subforum!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		} else {
			// Check read permissions in subforum
			$read_permissions = wpbb_user_has_permission($user_id, $subforum);
			if ($read_permissions === false) {
				if (wpbb_is_user_logged_in()) {
					wpbb_goback1('subforum_topic_denied', NULL);
					?>
					<div class="wpbb-message-failure">
						<?php _e('You do not have the required permissions to read topics in this subforum', 'wp-bb'); ?>
					</div>
					<?php
					wpbb_exit();
				}
			}
		}
	}
	// Is topic locked? If so deny access to all except admins and users with lock permissions
	$topic_status = wpbb_get_topic_status($topic_id);
	if (strpos($topic_status, 'locked') !== false) {
		$lock_permissions = wpbb_user_has_permission($user_id, $forum, 'lock');
		if (!current_user_can('manage_options')) {
			// Not an admin, but has lock permissions?
			if ($lock_permissions == false) {
				wpbb_goback1();
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to reply to lock topics!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		}
	}

	// Quick reply submission
	if (isset($_POST['wpbb-quick-reply-submit']))
	{
		$content = wp_strip_all_tags($_POST['wpbb-quick-reply-content']);
		if (empty($content))
		{
			wpbb_goback1();
			wpbb_error('Reply content cannot be blank');
			wpbb_exit();
		}
		// Check user has permissions to reply to the topic
	
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
		
		$forum_id = absint($_GET['forum']);
		$subforum_id = absint($_GET['subforum']);
	
		// Check forum permissions
		$view_permissions = wpbb_user_has_permission($user_id, $forum_id);
		if ($view_permissions === false) {
			wpbb_goback1('subforum_topic_quickreply_denied', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		} else {
			// Check subforum permissions
			$view_permissions = wpbb_user_has_permission($user_id, $subforum_id);
			if ($view_permissions === false) {
				wpbb_goback1('subforum_topic_quickreply_denied', NULL);
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to view this subforum!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			} else {
				$reply_permissions = wpbb_user_has_permission($user_id, $subforum_id, 'reply');
				if ($reply_permissions === false) {
					wpbb_goback1('subforum_topic_quickreply_denied', NULL);
					?>
					<div class="wpbb-message-failure">
						<?php _e('You do not have the required permissions to reply in this subforum!', 'wp-bb'); ?>
					</div>
					<?php
					wpbb_exit();
				}
			}
		}
		$create_post = $wpdb->insert(POST_TABLE, array('author' => $user_id, 'topic' => $topic_id, 'text' => $content));
		$date = date("Y-m-d H:i:s");
		$update_topic_last_reply = $wpdb->update(TOPIC_TABLE, array('last_reply' => $date), array('id' => $topic_id));
		if ($update_topic_last_reply === false) {
			_e('Error: Failed to update the topics last_reply. It is likely the topic doesn\'t exist or was deleted', 'wp-bb');
		}
		if ($create_post === false) {
			wpbb_goback1('subforum_topic_createpost_error', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('There was an error submitting a new post, please try again', 'wp-bb'); ?>
			</div>
			<?php
		} else {
			wpbb_goback1('subforum_topic_post_success', NULL);
			?>
			<div class="wpbb-message-success">
				<?php _e('Thank you, your post has been posted successfully', 'wp-bb'); ?>
			</div>
			<?php
			if ($user_roles != 'guest') {
				wpbb_update_user_meta($user_id, 'increase');
			}
			wpbb_update_user_lastpost($user_id);
		}
	}



	$title = wpbb_location(NULL, NULL, $topic_id, true);
	?>
	<h2 class="wpbb-forum-title">
		<?php echo $title['forum_name']. " / " . $title['forum'] . " / " . $title['subforum'] . " / " . $title['topic']; ?>
	</h2>
	<?php
	wpbb_buttons($forum, $subforum, $topic_id, 'subforum_topic');
	?> <div class="clear"></div><?php
	$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
	if ($topics) {
		?>
		<div class="wpbb-table-div">
		<table class="wpbb-table">
			<th><?php _e('Author', 'wp-bb'); ?></th>
			<th><?php _e('Content', 'wp-bb'); ?></th>
			<?php
			foreach ($topics as $topic) {
				$author = get_userdata($topic->author);
				$signature = get_user_meta($topic->author, 'wpbb_signature', true);
				// Pagination
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
				$total_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
				if (($total_posts > 0) && ($total_posts > $posts_per_page)) {
					wpbb_pagination($forum, $current_page, $start, $limit, $total_posts, $posts_per_page, false);
				}
				if ($current_page == 1) {
					?>
					<tr>
						<td class="wpbb-topic-profile">
							<?php if ($author) {
								?>
								<a href='<?php echo add_query_arg(array('profile' => $topic->author), wpbb_permalink()); ?>'>
									<?php 
									echo $author->display_name; 
									$id = $topic->author;
									?>
								</a>
								<?php
							} else {
								_e('Guest', 'wp-bb');
								$id = 0;
							}
							?>
							<br /><?php echo get_avatar($id); ?>
							<br /> <?php
							if ($author)
							{
								$user_posts = get_user_meta($topic->author, 'wpbb_posts', true);
								printf(__('Posts: %s', 'wp-bb'), $user_posts); 
							}
							?>
						</td>
						<td>
							<p class="wpbb-topic-and-post-date"> 
								<a name="wpbb-topic-anchor">#0</a> <?php wpbb_moderate_links('subforum', 'topic', $user_id, $forum, $subforum, $topic->id, NULL, $topic->created); ?>
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
				// End of Pagination
				if ($posts) {
					foreach ($posts as $post) {
						$author = get_userdata($post->author);
						$signature = get_user_meta($post->author, 'wpbb_signature', true);
						$user_posts = get_user_meta($post->author, 'wpbb_posts', true);
						?>
						<tr>
							<td class="wpbb-topic-profile">
								<?php 
								if ($author) {
									?>
									<a href='<?php echo add_query_arg(array('profile' => $post->author), wpbb_permalink()); ?>'>
										<?php 
											echo $author->display_name;
											$id = $post->author;
										?>
									</a>
									<?php
								} else {
									_e('Guest', 'wpbb'); 
									$id = 0;
								} 
								?>
								<br />
								<?php echo get_avatar($id); ?>
								<br /> <?php if ($author) printf(__('Posts: %s', 'wp-bb'), $user_posts); ?>
							</td>
							<td>
								<p class="wpbb-topic-and-post-date">
									<a name="wpbb-post-anchor<?php echo $post->id;?>">#<?php echo $post->id;?></a> <?php wpbb_moderate_links('subforum', 'post', $user_id, $forum, $subforum, $topic->id, $post->id, $post->created);; ?>
								</p>
								<p class="wpbb-topic-and-post-content">
									<?php
									$content = apply_filters('the_content', $post->text);
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
				}
				?>
				</table>
				</div>
			<?php
		}
		wpbb_buttons($forum, $subforum, $topic_id, 'subforum_topic');
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