<?php

// This just returns the name of the forum (set in wpbb settings) and the link to the page
$title = wpbb_location(NULL, NULL, NULL, false, false, true);
	
$user_id = get_current_user_id();

if (isset($_POST['wpbb-search-submit'])) {

	if (!$user_id) {
		wpbb_is_user_logged_in();
		wpbb_exit();
	}
		
	$search_criteria = wp_strip_all_tags($_POST['wpbb-search']);
		
	$search = $wpdb->get_results("SELECT * FROM ".TOPIC_TABLE." WHERE name = '$search_criteria' ORDER BY last_reply DESC;");
	
	if ($wpdb->num_rows === 0) {
		wpbb_goback1('search-results', NULL);
		?>
		<div class="wpbb-message-failure">
			<?php
				_e("Couldn't find any topics that matched your criteria. Please try again.", "wp-bb");
				wpbb_exit();
			?>
		</div>
		<?php
	}
	
	if ($search !== false) {
		
		?>
		<div class="wpbb-centered-bold">
			<a href="<?php echo wpbb_permalink(); ?>">Go back</a>
		</div>
		<h2 class="wpbb-centered-bold"><?php _e('Search Results', 'wp-bb'); ?></h2>
		<table class="wpbb-table">
			
		<th><?php _e('Status', 'wp-bb'); ?></th>
		<th><?php _e('Topic', 'wp-bb'); ?></th>
		<th><?php _e('Author', 'wp-bb'); ?></th>
		<th><?php _e('Posts', 'wp-bb'); ?></th>
		<th><?php _e('Last Reply', 'wp-bb'); ?></th>
		<th><?php _e('Action', 'wp-bb'); ?></th>
		
		<?php
		foreach ($search as $found) {
		
			$topics_posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $found->id;");
				
			$topic_author_name = wpbb_parse_author_name($found->author);
				
			$topic_last_post = wpbb_get_last_reply(NULL, NULL, $found->id, 'topic');
			
			$freshness = wpbb_get_topic_freshness($topic_last_post);
			
			$edit_topic_link = sprintf(__('<a href="%s">Edit</a>'), add_query_arg(array('forum' => $found->forum, 'topic' => $found->id, 'action' => 'edit'), get_permalink()));
			$lock_topic_link = sprintf(__('<a href="%s">Lock</a>'), add_query_arg(array('forum' => $found->forum, 'topic' => $found->id, 'action' => 'lock'), get_permalink()));
			$sticky_topic_link = sprintf(__('<a href="%s">Sticky</a>'), add_query_arg(array('forum' => $found->forum, 'topic' => $found->id, 'action' => 'sticky'), get_permalink()));
			$delete_topic_link = sprintf(__('<a href="%s">Delete</a>'), add_query_arg(array('forum' => $found->forum, 'topic' => $found->id, 'action' => 'delete'), get_permalink()));
			
			$status = wpbb_get_topic_status_buttons($found->status);
			
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
					<a href='<?php echo add_query_arg(array('forum' => $found->forum, 'topic' => $found->id, 'current_page' => 1)); ?>'><?php echo $found->name; ?></a>
				</td>
				<td>
					<a href='<?php echo add_query_arg(array('profile' => $found->author)); ?>'><?php echo $topic_author_name; ?></a></td>
				<td>
					<?php echo $topics_posts; ?>
				</td>
				<td>
					<?php echo $topic_last_post; ?>
				</td>
				<td>
					<?php echo $edit_topic_link." ".$lock_topic_link." ".$sticky_topic_link." ".$delete_topic_link; ?>
				</td>
			</tr>
			<?php
			}
			?>
			</table>
			<?php
			wpbb_exit();
			
		} else {
			?>
			<div class="wpbb-message-failure">
				<?php _e("There was an error retrieving a list of topics. Please try again.", "wp-bb"); ?>
			</div>
			<?php
			
		}
	}
	?>
	<p class="wpbb-centered">
		<a href='<?php echo add_query_arg(array('profile' => $user_id), wpbb_permalink()); ?>'><?php _e('My Profile', 'wp-bb'); ?></a>
		| <a href='<?php echo add_query_arg(array('unread_topics' => 'all', 'current_page' => 1), wpbb_permalink()); ?>'><?php _e('Unread Topics', 'wp-bb'); ?></a>
		| <a href='<?php echo add_query_arg(array('unanswered_topics' => 'all', 'current_page' => 1), wpbb_permalink()); ?>'><?php _e('Unanswered Topics', 'wp-bb'); ?></a> 
		| <a href='<?php echo add_query_arg(array('messages' => 'all', 'current_page' => 1), wpbb_permalink()); ?>'><?php _e('My Messages', 'wp-bb'); ?></a>
	</p>
	
	<div id="wpbb-search">
		<form method='POST' action='#'>
			<input type='text' id='wpbb-search-input' name='wpbb-search' value='<?php _e('Enter Search', 'wp-bb'); ?>' />
			<input type='submit' name='wpbb-search-submit' value='<?php _e('Search', 'wp-bb'); ?>' />
		</form>
	</div>
	
	<?php
	// Echo the forum name set in wpbb settings
	?><h2 class="wpbb-forum-title"><?php echo $title; ?></h2>

	<?php
	// Grab wpbb options (for use with allowing subforums to be displayed on index if set to yes)
	$options = get_option('wpbb_options');
	
	// Grab all categories from the database
	$categories = $wpdb->get_results("SELECT `id`, `name`, `order` FROM ".CATEGORIES_TABLE." WHERE forum = 0 AND subforum = 0 ORDER BY `order`;");
	// If there are no categories, display a nice message informing our admin to create some
	if (empty($categories))
	{
		?>
		<div class="wpbb-message-warning">
			<?php
			printf(__('You haven\'t created any categories yet. Create a category to define what type of forums you will have inside the category and then you can create forums and subforums inside that category, e.g. a category called Sports and two forums; one called Football and one called Rugby. You then might create two subforums; one called International Teams and the other called Premier League Teams. You can get started by creating a category <a href="%s">here</a>', 'wp-bb'), admin_url().'admin.php?page=wpbb_admin_categories');
			?>
		</div>
		<?php
	}
	if ($categories)
	{
		// Create our table
		?>
		<table class="wpbb-table">
		<?php
		foreach ($categories as $category)
		{
			?>
			<th><?php echo $category->name; ?></th>
			<th><?php _e('Topics', 'wp-bb'); ?></th>
			<th><?php _e('Posts', 'wp-bb'); ?></th>
			<th><?php _e('Last Post', 'wp-bb'); ?></th>
			<?php
			// Grab all forums which belong to the current category
			$forums = $wpdb->get_results("SELECT `id`, `name`, `description`, `order` FROM ".CATEGORIES_TABLE." WHERE forum = $category->id ORDER BY `order`;");
			if (!$forums)
			{
				?> <tr><td></td><td></td><td></td><td></td></tr><?php
			}
			if ($forums)
			{
				foreach ($forums as $forum)
				{
					// Get total amount of topics/posts in a forum (this doesn't include subforum topics/posts yet, look below)
					$forum_content = wpbb_get_topics_posts($forum->id);
					// Get the latest reply from all topics in the forum and order by DESC.
					$forum_last_reply = wpbb_get_last_reply($forum->id, NULL, NULL);
					?>
					<tr class="wpbb-forum-table-tr">
					<td id="wpbb-forum-index-table-forum-td">
						<a href='<?php echo add_query_arg(array('forum' => $forum->id, 'current_page' => 1)); ?>'><?php echo $forum->name; ?></a>
						<p><?php echo stripslashes($forum->description); ?></p>
						<?php
						// Grab all subforums which belong to the current forum
						if ($options['allow_subforums'] == 'yes')
						{
							$subforums = $wpdb->get_results("SELECT `id`, `name`, `description`, `order` FROM ".CATEGORIES_TABLE." WHERE subforum = $forum->id ORDER BY `order`;");
							if ($subforums)
							{
								_e('Subforums: ', 'wp-bb');
								$i = 0; $len = count($subforums);
								foreach ($subforums as $subforum)
								{
									// Search subforum for topics and posts
									$subforum_content = wpbb_get_topics_posts($subforum->id, 'subforum');
									// If there are any, add them to the forums total topic and post count
									$forum_content['topics'] += $subforum_content['topics'];
									$forum_content['posts'] += $subforum_content['posts'];
									// End of subforum topic/post search
									// Get the latest reply from all topics in the subforum (if any) then find the max between the forum and subforum
									$subforum_last_reply = wpbb_get_last_reply(NULL, $subforum->id, NULL, 'subforum');
									$forum_last_reply = max($forum_last_reply, $subforum_last_reply);
									// End of subforum latest reply
									if ($i == $len - 1)
									{
										?>
										<a href="<?php echo add_query_arg(array('forum' => $forum->id, 'subforum' => $subforum->id, 'current_page' => 1)); ?>"><?php echo $subforum->name; ?></a>
										<?php
									}
									else
									{
										?>
										<a href="<?php echo add_query_arg(array('forum' => $forum->id, 'subforum' => $subforum->id, 'current_page' => 1)); ?>"><?php echo $subforum->name; ?></a>,
										<?php
									}
									$i++;
								}
							}
				 		}
				 		// Get the topic ID of the topic with the latest reply in the forum (including subforums)
				 		$topic_id = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE last_reply = '$forum_last_reply' OR created = '$forum_last_reply';");
				 		$topic_link = NULL;
				 		if ($topic_id)
				 		{
				 			$topic_is_subforum = $wpdb->get_var("SELECT subforum FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
				 			$topic_link = NULL;
				 			if ($topic_is_subforum > 0) // Ok, topic is in a subforum
				 			{
				 				$topic_link = add_query_arg(array('forum' => $forum->id, 'subforum' => $subforum->id, 'topic' => $topic_id, 'current_page' => 1));
				 			}
				 			else
				 			{
				 				$topic_link = add_query_arg(array('forum' => $forum->id, 'topic' => $topic_id, 'current_page' => 1));
				 			}
				 		}
					?>
					</td>
					<td><?php echo $forum_content['topics']; ?></td>
					<td><?php echo $forum_content['posts']; ?></td>
					<td>
						<?php
						if ($topic_link && $topic_id)
						{
							$topic_post_count = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic_id;");
							if ($topic_post_count > 0)
							{
								$topic_post_id = $wpdb->get_var("SELECT id FROM ".POST_TABLE." WHERE topic = $topic_id && created = '$forum_last_reply';");
								if ($topic_post_id)
								{
									// Contains posts
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
								printf("<a href='$topic_link#%s'>$forum_last_reply by $last_post_author</a>", "wpbb-post-anchor$topic_post_id");
								}
							}
							else
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
								printf("<a href='$topic_link#%s'>$forum_last_reply by $last_post_author</a>", "wpbb-post-anchor0");
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
			}
		}
		?> </table> <?php
	}

?>