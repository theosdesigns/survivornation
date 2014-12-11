<?php

	$forum = absint($_GET['forum']);
	
	wpbb_check_exists('forum', $forum);
	
	$user_id = get_current_user_id();
	
	// Third parameter is 'view' by default
	$view_permissions = wpbb_user_has_permission($user_id, $forum);
	if ($view_permissions === false) {
		if (wpbb_is_user_logged_in()) {
			wpbb_goback1('forum-index', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}

	/*
		Display a forums subforums
	*/
	$title = wpbb_location($forum, NULL, NULL, false, false);
	?>
	<h2 class="wpbb-forum-title">
	<?php echo $title['forum_name']." / " .$title['forum']; ?>
	</h2>
	<?php
	$subforums = $wpdb->get_results("SELECT * FROM ".CATEGORIES_TABLE." WHERE subforum = $forum ORDER BY `order`;");
	if ($subforums) {
		?>
		<table class="wpbb-table">
			<th><?php _e('Subforum', 'wp-bb'); ?></th>
			<th><?php _e('Topics', 'wp-bb'); ?></th>
			<th><?php _e('Posts', 'wp-bb'); ?></th>
			<th><?php _e('Last Post', 'wp-bb'); ?></th>
			<?php
			foreach ($subforums as $subforum) {
				$subforum_content = wpbb_get_topics_posts($subforum->id, 'subforum');
				$subforum_last_reply = wpbb_get_last_reply(NULL, $subforum->id, NULL, 'subforum');
				?>
				<tr>
				<td id="wpbb-subforum-td">
					<a href="<?php echo add_query_arg(array('forum' => $forum, 'subforum' => $subforum->id, 'current_page' => 1), get_permalink()); ?>">
						<?php echo $subforum->name; ?>
					</a>
				</td>
				<td><?php echo $subforum_content['topics']; ?></td>
				<td><?php echo $subforum_content['posts']; ?></td>
				<td>
					<?php
					// Get the topic ID of the topic with the latest reply in the subforum
				 	$topic_id = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE last_reply = '$subforum_last_reply' || created = '$subforum_last_reply';");
				 	$topic_link = add_query_arg(array('forum' => $forum, 'subforum' => $subforum->id, 'topic' => $topic_id, 'current_page' => 1));
				 	if ($topic_id)
					{
						$topic_post_count = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic_id;");
				 		if ($topic_post_count > 0) // Contains posts
				 		{
				 			// Get the topic post ID we want
				 			$topic_post_id = $wpdb->get_var("SELECT id FROM ".POST_TABLE." WHERE topic = $topic_id && created = '$subforum_last_reply';");
				 			$post_author_id = $wpdb->get_var("SELECT `author` FROM ".POST_TABLE." WHERE id = $topic_post_id;");
							$post_author_data = get_userdata($post_author_id);
							if (!$post_author_data)
							{
								// Guest
								$last_post_author = __('Guest', 'wp-bb');
							}
							else
							{
								$last_post_author = $post_author_data->display_name;
							}
							printf("<a href='$topic_link#%s'>$subforum_last_reply by $last_post_author</a>", "wpbb-post-anchor$topic_post_id");
				 		}
				 		else // Topic
				 		{
				 			$topic_author_id = $wpdb->get_var("SELECT `author` FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
							$topic_author_data = get_userdata($topic_author_id);
							if (!$topic_author_data)
							{
								// Guest
								$last_post_author = __('Guest', 'wp-bb');
							}
							else
							{
								$last_post_author = $topic_author_data->display_name;
							}
							printf("<a href='$topic_link#%s'>$subforum_last_reply by $last_post_author</a>", "wpbb-post-anchor0");
				 		}
					}
					else
					{
						_e('No new posts', 'wp-bb');
					}
					?>
				</td>
				</tr>
				<?php
			}
			?>
		</table>
		<div class="clear"></div>
		<?php
	}
	/*
		End display a forums subforums
	*/

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
	$total_topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPIC_TABLE." WHERE forum = $forum;");
	$topics = false;
	if (($total_topics > 0) && ($total_topics > $topics_per_page)) {
		wpbb_pagination($forum, $current_page, $start, $limit, $total_topics, $topics_per_page);
	}
	$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE."  WHERE forum = $forum ORDER BY FIELD(status, 'sticky', 'locked,sticky') DESC, last_reply DESC LIMIT $start, $limit;");
	wpbb_buttons($forum, NULL, NULL, 'forum');
	// If 'sticky' is found in the set field, order by that first otherwise order by last_reply descending. 
	
	if ($topics) {
		?>
		<table class="wpbb-table">
			<th><?php _e('Status', 'wp-bb'); ?></th>
			<th><?php _e('Topic', 'wp-bb'); ?></th>
			<th><?php _e('Author', 'wp-bb'); ?></th>
			<th><?php _e('Posts', 'wp-bb'); ?></th>
			<th><?php _e('Last Reply', 'wp-bb'); ?></th>
			<th><?php _e('Action', 'wp-bb'); ?></th>
			<?php
			foreach ($topics as $topic) {

				$author = get_userdata($topic->author);
		
				$topic_last_post = wpbb_get_last_reply(NULL, NULL, $topic->id, 'topic');
		
				$freshness = wpbb_get_topic_freshness($topic_last_post);
		
				$topics_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
				
				// By default the last post author is the topic author
				if ($author)
				{
					$last_post_author = $author->display_name;
				}
				
				$post_anchor_id = 0;
				if ($topics_posts > 0) // Contains posts
				{
					$post_id = $wpdb->get_var("SELECT id FROM ".POST_TABLE." WHERE topic = $topic->id && created = '$topic_last_post';");
					if ($post_id)
					{
						$post_anchor_id = $post_id;
						$last_post_author_id = $wpdb->get_var("SELECT `author` FROM ".POST_TABLE." WHERE id = $post_id;");
						if ($last_post_author_id == 0)
						{
							// Guest
							$last_post_author = __('Guest', 'wp-bb');
						}
						else
						{
							$last_post_author_name = get_userdata($last_post_author_id);
							if ($last_post_author_name)
							{
								$last_post_author = $last_post_author_name->display_name;
							}
						}
					}
				}
		
				$status = wpbb_get_topic_status_buttons($topic->status);
			
				?>
				<tr>
					<td>
						<?php 
						echo $freshness;
						if ($status['locked'] != "") {
							echo $status['locked'];
						}
						if ($status['sticky'] != "") {
							echo $status['sticky'];
						}
						?>
					</td>
					<td>
						<?php 
						$topic_link = add_query_arg(array('forum' => $forum, 'topic' => $topic->id, 'current_page' => 1), get_permalink());
						?>
						<a href="<?php echo $topic_link; ?>">
							<?php echo $topic->name; ?>
						</a>
					</td>
					<?php
					if ($author === false) {
						?> 
						<td>
							<?php _e('Guest', 'wp-bb'); ?>
						</td> 
						<?php
					} else {
						?>
						<td>
							<?php $author_profile_link = add_query_arg(array('profile' => $topic->author), get_permalink()); ?>
							<a href="<?php echo $author_profile_link; ?>">
								<?php echo $author->display_name; ?>
							</a>
						</td>
						<?php
					}
					?>
					<td><?php echo $topics_posts; ?></td>
					<td><?php printf("<a href='$topic_link#%s'>$topic_last_post by $last_post_author</a>", "wpbb-post-anchor$post_anchor_id"); ?></td>
					<td>
						<?php wpbb_moderate_links('forum', 'topic', $user_id, $forum, NULL, $topic->id, NULL, NULL); ?>
					</td>
				</tr>
			<?php
			}
			?>
		</table>
	<?php
	// Only display the bottom forum buttons if any topics exist
	if ($topics) {
		wpbb_buttons($forum, NULL, NULL, 'forum');
	}
}
?>
<div class="clear"></div>
<?php
?>