<?php

	$user_id = get_current_user_id();

	$forum = absint($_GET['forum']);
	
	$subforum = absint($_GET['subforum']);
	
	wpbb_check_exists('subforum', $subforum);
	
	
	
	$view_permissions = wpbb_user_has_permission($user_id, $forum);
	if ($view_permissions === false) {
		if (wpbb_is_user_logged_in()) {
			wpbb_goback1('subforum_denied', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions view this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	} else {
		$view_permissions = wpbb_user_has_permission($user_id, $subforum);
		if ($view_permissions === false) {
			if (wpbb_is_user_logged_in()) {
				wpbb_goback1('subforum_denied', NULL);
				?>
				<div class="wpbb-message-failure">
					<?php _e('You do not have the required permissions to view this subforum!', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		}
	}
			
	$subforum = wp_strip_all_tags($_GET['subforum']);
	
	$title = wpbb_location(NULL, $subforum, NULL, true, false, false);

	?>
	<h2 class="wpbb-forum-title">
		<?php echo $title['forum_name']. " / " . $title['forum'] . " / " . $title['subforum']; ?>
	</h2>
	<?php
	// Pagination
	$forum = absint($_GET['forum']);
	$options = get_option('wpbb_options');
	$topics_per_page = $options['topics_per_page'];
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
	$total_topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPIC_TABLE." WHERE subforum = $subforum;");
	wpbb_buttons($forum, $subforum, NULL, 'subforum');
	if (($total_topics > 0) && ($total_topics > $topics_per_page)) {
		wpbb_pagination($subforum, $current_page, $start, $limit, $total_topics, $topics_per_page);
	}
	$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE subforum = $subforum ORDER BY created DESC LIMIT $start, $limit;");
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
			$topics_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
			$author = get_userdata($topic->author);
			$link = add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic->id, 'current_page' => 1), get_permalink());
			$subforum_topic_last_reply = wpbb_get_last_reply(NULL, NULL, $topic->id, 'topic');
			$status = wpbb_get_topic_status_buttons($topic->status);
			$freshness = wpbb_get_topic_freshness($subforum_topic_last_reply);
			
			?>
			<tr>
				<td>
					<?php 
					echo $freshness;
					if ($status['locked'] != "")
					{
						echo $status['locked'];
					}
					if ($status['sticky'] != "")
					{
						echo $status['sticky'];
					}
					?>
			</td>
				<td>
					<a href='<?php echo $link; ?>'><?php echo $topic->name; ?></a>
				</td>
				<td>
					<?php if ($author)
					{
						?>
						<a href='<?php echo add_query_arg(array('profile' => $topic->author), wpbb_permalink()); ?>'><?php echo $author->display_name; ?></a>
						<?php
					}
					else
					{
						_e('Guest', 'wp-bb');
					}
					?>
				</td>
				<td>
					<?php echo $topics_posts; ?>
				</td>
				<td>
					<?php
					if (!$author)
					{
						// No posts in this topic but the author is a guest
						$last_post_author = __('Guest', 'wp-bb');
					}
					else
					{
						$last_post_author = $author->display_name;
					}
					// Means it is a topic, unless otherwise changed below
					$post_anchor_id = 0;
					if ($topics_posts > 0) // Contains posts
					{
						$post_id = $wpdb->get_var("SELECT id FROM ".POST_TABLE." WHERE topic = $topic->id && created = '$subforum_topic_last_reply';");
						// Now the anchor refers to a post
						$post_anchor_id = $post_id;
						$last_post_author_id = $wpdb->get_var("SELECT `author` FROM ".POST_TABLE." WHERE id = $post_id;");
						$last_post_author_name = get_userdata($last_post_author_id);
						if (!$last_post_author_name)
						{
							// Guest
							$last_post_author = __('Guest', 'wp-bb');
						}
						else
						{
							$last_post_author = $last_post_author_name->display_name;
						}
					}
					printf(__("<a href='$link#%s'>$subforum_topic_last_reply by $last_post_author</a>", 'wp-bb'), "wpbb-post-anchor$post_anchor_id");
					?>
				</td>
				<td>
					<?php wpbb_moderate_links('subforum', 'topic', $user_id, $forum, $subforum, $topic->id, NULL, NULL); ?>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	if ($topics) {
		wpbb_buttons($forum, $subforum, NULL, 'subforum');
	}
	?>
	<div class="clear"></div>
	<?php
?>