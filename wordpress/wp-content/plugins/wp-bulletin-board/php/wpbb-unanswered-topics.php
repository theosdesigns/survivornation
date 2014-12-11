<?php

	$user_id = wpbb_is_user_logged_in();
	
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
	$total_topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPIC_TABLE." WHERE created = last_reply;");
	$topics = false;
	if (($total_topics > 0) && ($total_topics > $topics_per_page)) {
		wpbb_pagination(NULL, $current_page, $start, $limit, $total_topics, $topics_per_page);
		$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE created = last_reply ORDER by created DESC LIMIT $start, $limit;");
	} else {
		$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE created = last_reply ORDER by created DESC LIMIT $start, $limit;");
	}
	if ($topics) {
		wpbb_goback1('unanswered_topics', NULL);
		?>
		<h1 id="wpbb-h-1" class="wpbb-centered-bold"><?php _e('Unanswered Topics', 'wp-bb'); ?></h1>
		<table class="wpbb-table">
	 		<th><?php _e('Status', 'wp-bb'); ?></th>
			<th><?php _e('Topic', 'wp-bb'); ?></th>
			<th><?php _e('Author', 'wp-bb'); ?></th>
			<th><?php _e('Last Reply', 'wp-bb'); ?></th>
			<th><?php _e('Action', 'wp-bb'); ?></th>
			<?php
			foreach ($topics as $topic) {	 		
		 		$topics_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
		 		if ($topics_posts == 0) {
		 		
					$topic_author_name = wpbb_parse_author_name($topic->author);
					
					$topic_last_post = wpbb_get_last_reply(NULL, NULL, $topic->id, 'topic');
				
					if ($topic->forum > 0 && $topic->subforum == 0) {
					
						$topic_link = "<a href='".add_query_arg(array('forum' => $topic->forum, 'topic' => $topic->id, 'current_page' => 1), wpbb_permalink())."'>".$topic->name."</a>";
						$edit_topic_link = sprintf(__('<a href="%s">Edit</a>'), add_query_arg(array('forum' => $topic->forum, 'topic' => $topic->id, 'action' => 'edit'), get_permalink()));
				
						$lock_topic_link = sprintf(__('<a href="%s">Lock</a>'), add_query_arg(array('forum' => $topic->forum, 'topic' => $topic->id, 'action' => 'lock'), get_permalink()));
				
						$sticky_topic_link = sprintf(__('<a href="%s">Sticky</a>'), add_query_arg(array('forum' => $topic->forum, 'topic' => $topic->id, 'action' => 'sticky'), get_permalink()));
				
						$delete_topic_link = sprintf(__('<a href="%s">Delete</a>'), add_query_arg(array('forum' => $topic->forum, 'topic' => $topic->id, 'action' => 'delete'), get_permalink()));
						
					} else if ($topic->subforum > 0 && $topic->forum == 0) {
					
						// Get the subforums subforum field which the topic belongs to (subforum field will be the ID of the forum its a subforum of)
						$subforum_id = $wpdb->get_var("SELECT subforum FROM ".CATEGORY_TABLE." WHERE id = $topic->subforum;");
						
						// Then we can retrieve the forum id knowing the subforums id
						$forum_id = $wpdb->get_var("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $subforum_id;");
						
						$topic_link = "<a href='".add_query_arg(array('forum' => $forum_id, 'subforum' => $topic->subforum, 'topic' => $topic->id, 'current_page' => 1), wpbb_permalink())."'>".$topic->name."</a>";
						
						$edit_topic_link = sprintf(__('<a href="%s">Edit</a>'), add_query_arg(array('forum' => $forum_id, 'subforum' => $topic->subforum, 'topic' => $topic->id, 'action' => 'edit'), get_permalink()));
				
						$lock_topic_link = sprintf(__('<a href="%s">Lock</a>'), add_query_arg(array('forum' => $topic->forum, 'subforum' => $topic->subforum, 'topic' => $topic->id, 'action' => 'lock'), get_permalink()));
				
						$sticky_topic_link = sprintf(__('<a href="%s">Sticky</a>'), add_query_arg(array('forum' => $topic->forum, 'subforum' => $topic->subforum, 'topic' => $topic->id, 'action' => 'sticky'), get_permalink()));
				
						$delete_topic_link = sprintf(__('<a href="%s">Delete</a>'), add_query_arg(array('forum' => $topic->forum, 'subforum' => $topic->subforum, 'topic' => $topic->id, 'action' => 'delete'), get_permalink()));
						
					}
					
					$status = wpbb_get_topic_status_buttons($topic->status);
					
					$freshness = wpbb_get_topic_freshness($topic_last_post);
					
					
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
						<td width="100px"><?php echo $topic_link;?></td>
						<td>
							<?php if ($topic_author_name !=  "") {
								?>
								<a href='<?php echo add_query_arg(array('profile' => $topic->author), wpbb_permalink()); ?>'><?php echo $topic_author_name; ?></a>
								<?php
							} else {
								_e('Guest', 'wp-bb');
							}
							?>
						</td>
						<td>
							<?php
							// Get the topic ID of the topic with the latest reply in the subforum
				 			$topic_id = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE last_reply = '$topic_last_post';");
				 			if ($topic->subforum > 0)
				 			{
				 				$topic_link = add_query_arg(array('forum' => $topic->forum, 'subforum' => $topic->subforum, 'topic' => $topic_id, 'current_page' => 1), wpbb_permalink());
				 			}
				 			else
				 			{
				 				$topic_link = add_query_arg(array('forum' => $topic->forum, 'topic' => $topic_id, 'current_page' => 1), wpbb_permalink());
				 			}
				 			if ($topic_id)
							{
								$topic_post_count = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic_id;");
				 				if ($topic_post_count > 0) // Contains posts
				 				{
				 				// Get the topic post ID we want
				 					$topic_post_id = $wpdb->get_var("SELECT id FROM ".POST_TABLE." WHERE topic = $topic_id && created = '$topic_last_post';");
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
									printf("<a href='$topic_link#%s'>$topic_last_post by $last_post_author</a>", "wpbb-post-anchor$topic_post_id");
				 				}
				 				else // Topic
				 				{
				 					$topic_author_id = $wpdb->get_var("SELECT `author` FROM ".TOPIC_TABLE." WHERE id = $topic_id;");		
									$topic_author_data = get_userdata($topic_author_id);
									if (!$topic_author_data)
									{
										$last_post_author = __('Guest', 'wp-bb');
									}
									else
									{
										$last_post_author = $topic_author_data->display_name;
									}
									printf("<a href='$topic_link#%s'>$topic_last_post by $last_post_author</a>", "wpbb-post-anchor0");
				 				}
							}
							else
							{
								_e('No new posts', 'wp-bb');
							}
							?>
						</td>
						<td><?php echo $edit_topic_link." ".$lock_topic_link." ".$sticky_topic_link." ".$delete_topic_link; ?></td>
					</tr>
					<?php
				}
			}
			?> </table> <?php
		} else {
			wpbb_goback1('unanswered_topics', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php _e('There are no unanswered topics at this time.', 'wp-bb'); ?>
			</div>
			<?php
		}
	
?>