<?php

// Returns user ID
$user_id = wpbb_is_user_logged_in();
	 
$user_lastvisit = get_user_meta($user_id, 'wpbb_lastvisit', true);
	 
$user_currentvisit = get_user_meta($user_id, 'wpbb_currentvisit', true);
		
$options = get_option('wpbb_options');
$topics_per_page = $options['topics_per_page'];
		
if (isset($_GET['current_page']))
{
	if ($_GET['current_page'] == 0)
	{
		$current_page = 1;
		$start = 0;
		$limit = $topics_per_page;
	}
	else if ($_GET['current_page'] == 1)
	{
		$current_page = 1;
		$start = 0;
		$limit = $topics_per_page;
	}
	else
	{
		$current_page = $_GET['current_page'];
		$start = $current_page * $topics_per_page - $topics_per_page;
		$limit = $start + $topics_per_page;
	}
}

//$total_topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPIC_TABLE." WHERE last_reply > '$user_lastvisit' AND last_reply < '$user_currentvisit' AND `read` = 0;");

$total_topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".UN_READ_TABLE." WHERE `author` = $user_id AND `read` = 0;");
		
$topics = false;
		
/*if (($total_topics > 0) && ($total_topics > $topics_per_page))
{
	wpbb_pagination(NULL, $current_page, $start, $limit, $total_topics, $topics_per_page);
	//$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE last_reply > '$user_lastvisit' AND last_reply < '$user_currentvisit' AND `read` = 0 ORDER by last_reply DESC LIMIT $start, $limit;");
}
else
{
	//$topics = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE last_reply > '$user_lastvisit' AND last_reply < '$user_currentvisit' AND `read` = 0 ORDER by last_reply DESC LIMIT $start, $limit;");
}*/

if (($total_topics > 0) && ($total_topics > $topics_per_page))
{
	wpbb_pagination(NULL, $current_page, $start, $limit, $total_topics, $topics_per_page);
}
$topics = $wpdb->get_results("SELECT * FROM ".UN_READ_TABLE." WHERE `author` = $user_id AND `read` = 1;");
if ($topics)
{
	wpbb_goback1();
	wpbb_error('There are no unread topics at this time');
	wpbb_exit();
}
$topics = $wpdb->get_results("SELECT * FROM ".UN_READ_TABLE." WHERE `author` != $user_id");
if ($topics)
{
	 wpbb_goback1('unread-topics', NULL);
	 ?>
	 <h1 id="wpbb-h-1" class="wpbb-centered-bold"><?php _e('Unread Topics Since Last Visit', 'wp-bb'); ?></h1>
	 <table class="wpbb-table">
	 	<th><?php _e('Status', 'wp-bb'); ?></th>
		<th><?php _e('Topic', 'wp-bb'); ?></th>
		<th><?php _e('Author', 'wp-bb'); ?></th>
		<th><?php _e('Posts', 'wp-bb'); ?></th>
		<th><?php _e('Last Post', 'wp-bb'); ?></th>
		<th><?php _e('Action', 'wp-bb'); ?></th>
	 	<?php
	 	// Used for the last post
	 	$_forum_id = 0;
	 	$_subforum_id = 0;
		foreach ($topics as $topic)
		{
			$topic_name = $wpdb->get_var("SELECT name FROM ".TOPIC_TABLE." WHERE id = $topic->id;");
			$topic_status = $wpdb->get_var("SELECT status FROM ".TOPIC_TABLE." WHERE id = $topic->id;");
			$topic_status_buttons = wpbb_get_topic_status_buttons($topic_status);
			$topic_forum = $wpdb->get_var("SELECT forum FROM ".TOPIC_TABLE." WHERE id = $topic->id;");
			$topic_subforum = $wpdb->get_var("SELECT subforum FROM ".TOPIC_TABLE." WHERE id = $topic->id;");
			$topic_author_name = wpbb_parse_author_name($topic->author);
			if ($topic_author_name == "")
			{
				// Guest
				$topic_author_name = __('Guest', 'wp-bb');
			}
			$topics_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
			$topic_last_post = wpbb_get_last_reply(NULL, NULL, $topic->id, 'topic');
			$freshness = wpbb_get_topic_freshness($topic_last_post);
			// Forum topic
			if ($topic_forum > 0 && $topic_subforum == 0)
			{
				$topic_link = "<a href='".add_query_arg(array('forum' => $topic_forum, 'topic' => $topic->id, 'current_page' => 1), wpbb_permalink())."'>".$topic_name."</a>";
				$edit_topic_link = sprintf(__('<a href="%s">Edit'), add_query_arg(array('forum' => $topic_forum, 'topic' => $topic->id, 'action' => 'edit'), get_permalink()));
				$lock_topic_link = sprintf(__('<a href="%s">Lock'), add_query_arg(array('forum' => $topic_forum, 'topic' => $topic->id, 'action' => 'lock'), get_permalink()));
				$sticky_topic_link = sprintf(__('<a href="%s">Sticky'), add_query_arg(array('forum' => $topic_forum, 'topic' => $topic->id, 'action' => 'sticky'), get_permalink()));
				$delete_topic_link = sprintf(__('<a href="%s">Delete'), add_query_arg(array('forum' => $topic_forum, 'topic' => $topic->id, 'action' => 'delete'), get_permalink()));
				$mark_read_link = sprintf(__('<a href="%s">Mark Read'), add_query_arg(array('forum' => $topic_forum, 'topic' => $topic->id, 'action' => 'markread'), get_permalink()));
				$_forum_id = $topic_forum;
			}
			// Subforum topic
			else if ($topic_subforum > 0 && $topic_forum == 0)
			{
				// Get the subforums subforum field which the topic belongs to 
				// (subforum field will be the ID of the forum its a subforum of)
				$subforum_id = $wpdb->get_var("SELECT subforum FROM ".CATEGORY_TABLE." WHERE id = $topic_subforum;");
				// Then we can retrieve the forum id knowing the subforums id
				$forum_id = $wpdb->get_var("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $subforum_id;");
				$topic_link = "<a href='".add_query_arg(array('forum' => $forum_id, 'subforum' => $topic_subforum, 'topic' => $topic->id, 'current_page' => 1), wpbb_permalink())."'>".$topic_name."</a>";
				$edit_topic_link = sprintf(__('<a href="%s">Edit'), add_query_arg(array('forum' => $forum_id, 'subforum' => $topic_subforum, 'topic' => $topic->id, 'action' => 'edit'), get_permalink()));	
				$lock_topic_link = sprintf(__('<a href="%s">Lock'), add_query_arg(array('forum' => $forum_id, 'subforum' => $topic_subforum, 'topic' => $topic->id, 'action' => 'lock'), get_permalink()));
				$sticky_topic_link = sprintf(__('<a href="%s">Sticky'), add_query_arg(array('forum' => $forum_id, 'subforum' => $topic_subforum, 'topic' => $topic->id, 'action' => 'sticky'), get_permalink()));
				$delete_topic_link = sprintf(__('<a href="%s">Delete'), add_query_arg(array('forum' => $forum_id, 'subforum' => $topic_subforum, 'topic' => $topic->id, 'action' => 'delete'), get_permalink()));
				$mark_read_link = sprintf(__('<a href="%s">Mark Read'), add_query_arg(array('forum' => $forum_id, 'subforum' => $topic_subforum, 'topic' => $topic->id, 'action' => 'markread'), get_permalink()));
				$_subforum_id = $topic_subforum;
			}
			?>
			<tr>
				<td>
					<?php
					echo $freshness;
					if ($topic_status_buttons['locked'] != "") echo $topic_status_buttons['locked'];
					if ($topic_status_buttons['sticky'] != "") echo $topic_status_buttons['sticky'];
					?>
				</td>
				<td><?php echo $topic_link; ?></td>
				<td><?php printf("<a href='%s'>$topic_author_name</a>", $author_profile_link = add_query_arg(array('profile' => $topic->author), wpbb_permalink())); ?></td>
				<td><?php echo $topics_posts; ?></td>
				<td>
					<?php
					// Get the topic ID of the topic with the latest reply in the subforum
				 		$topic_id = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE last_reply = '$topic_last_post';");
				 		if ($_subforum_id > 0)
				 		{
				 			$topic_link = add_query_arg(array('forum' => $_forum_id, 'subforum' => $_subforum_id, 'topic' => $topic_id, 'current_page' => 1), wpbb_permalink());
				 		}
				 		else
				 		{
				 			$topic_link = add_query_arg(array('forum' => $_forum_id, 'topic' => $topic_id, 'current_page' => 1), wpbb_permalink());
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
				<td>
				<?php echo $edit_topic_link." ".$lock_topic_link." ".$sticky_topic_link." ".$delete_topic_link." ".$mark_read_link; ?>
				</td>
			</tr>
			<?php
	 	}
	 ?>
	 </table>
	 <?php
}
else
{
	wpbb_goback1('unread-topics', NULL);
 	?>
 	<div class="wpbb-message-failure">
 		<?php _e('There are no unread topics at this time', 'wp-bb'); ?>
 	</div>
 	<?php
}
?>