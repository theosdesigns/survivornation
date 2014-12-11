<?php

// Display a "Go back" link
wpbb_goback1();

$is_post = isset($_GET['post']) ? true : false;
	
$forum_id = absint($_GET['forum']);
	
$user_id = get_current_user_id();
	
$topic_id = absint($_GET['topic']);
if (isset($_GET['post']))
{
	$post_id = absint($_GET['post']);
}

$view_permissions = wpbb_user_has_permission($user_id, $forum_id);
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
	$edit_permissions = wpbb_user_has_permission($user_id, $forum_id, 'edit');
	if ($is_post === false)
	{
		$editing_own_topic = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE id = $topic_id AND author = $user_id;");
		// If user does not have edit permissions and is not editing own topic then they are trying to edit someone elses topic!
		if (($edit_permissions === false) && (!$editing_own_topic))
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to edit other peoples topics in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
	else if ($is_post === true)
	{
		$editing_own_post = $wpdb->get_var("SELECT id FROM ".POST_TABLE." WHERE id = $post_id AND author = $user_id;");
		// If user does not have edit permissions and is not editing own post then they are trying to edit someone elses topic!
		if (($edit_permissions === false) && (!$editing_own_post))
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to edit other peoples posts in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
}

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
				wpbb_error('You can only edit your own topics!');
				wpbb_exit();
			}
		}
	}
}
	

if ($is_post === false)
{
	// Form submission
	if (isset($_POST['wpbbtopiceditsubmit']))
	{
		if (empty($_POST['wpbbtopiccontent']) || empty($_POST['wpbbtopicname']))
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You must enter a title and some content for your topic', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();	
		}
		// Sanitize topic title
		$topic_name = wp_strip_all_tags($_POST['wpbbtopicname']);
	
		
		if (isset($_POST['wpbbauthorname']))
		{
			if (is_numeric($_POST['wpbbauthorname']))
			{
				$topic_author = absint($_POST['wpbbauthorname']);
				// If the original author id is not null
				if (isset($_POST['wpbboriginalauthorid']))
				{
					$original_author_id = absint($_POST['wpbboriginalauthorid']);
					// if the new author id is not the same as our original author id, then we can assume it is a new user
					// thus we decrease the old users post count by 1
					if ($topic_author !== $original_author_id)
					{
						$original_author_posts = get_user_meta($original_author_id, 'wpbb_posts', true);
						$new_original_author_posts = intval($original_author_posts) - 1;
						update_user_meta($original_author_id, 'wpbb_posts', $new_original_author_posts);
					}
				}
			}
			else if (is_string($_POST['wpbbauthorname']))
			{
				$topic_author_name = wp_strip_all_tags($_POST['wpbbauthorname']);
				$topic_author = wpbb_parse_author_name(NULL, $topic_author_name);
				// If the original author id is not null
				if (isset($_POST['wpbboriginalauthorid']))
				{
					$original_author_id = absint($_POST['wpbboriginalauthorid']);
					// if the new author id is not the same as our original author id, then we can assume it is a new user
					// thus we decrease the old users post count by 1
					if ($topic_author !== $original_author_id)
					{
						$original_author_posts = get_user_meta($original_author_id, 'wpbb_posts', true);
						$new_original_author_posts = intval($original_author_posts) - 1;
						update_user_meta($original_author_id, 'wpbb_posts', $new_original_author_posts);
					}
				}
			}
		}
		else
		{
			// If nothing was inserted in the author field, use the current user ID (would be old)
			$topic_author = get_current_user_id();
	
			// Else the user will be a guest
			if (!$topic_author)
			{
				// Guest
				$topic_author_author = 0;
			}
		}


		// Increase the new authors post count by 1
		$new_author_posts = get_user_meta($topic_author, 'wpbb_posts', true);
		$new_author_posts = intval($new_author_posts) + 1;
		update_user_meta($topic_author, 'wpbb_posts', $new_author_posts);
		
		$topic_content = wpbb_strip_tags($_POST['wpbbtopiccontent']);
		
		if (($_POST['wpbbtopicforum'] == 'yes') && ($_POST['wpbbtopicsubforum'] == 'no'))
		{
			$topic_forum = absint($_POST['wpbbtopicforumname']);
			// Check category exists otherwise do not allow submission to proceed
			$exists = $wpdb->query("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $topic_forum;");
			if ($exists === false)
			{
				?>
				<div class="wpbb-message-failure">
					<?php _e('That category does not exist. Please make sure you choose a valid category.', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
			$topic_subforum = 0;
		}
		else if (($_POST['wpbbtopicsubforum'] == 'yes') && ($_POST['wpbbtopicforum'] == 'no'))
		{
			$topic_subforum = absint($_POST['wpbbtopicsubforumname']);
			// Check forum exists otherwise do not allow submission to proceed
			$exists = $wpdb->query("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $topic_subforum;");
			if ($exists === false)
			{
				?>
				<div class="wpbb-message-failure">
					<?php _e('That forum does not exist. Please make sure you choose a valid forum.', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
			$topic_forum = 0;
		}
		else if (($_POST['wpbbtopicforum'] == 'no') && ($_POST['wpbbtopicsubforum'] == 'no'))
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You must select a category or forum for the topic to be posted in.', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
		if (isset($_POST['wpbbtopicstatus']))
		{
			$topic_status = implode(",", (array) $_POST['wpbbtopicstatus']);
		}
		else
		{
			$topic_status = "";
		}
		
		$data = array(
			'name' => $topic_name,
			'author' => $topic_author,
			'content' => $topic_content,
			'forum' => $topic_forum,
			'subforum' => $topic_subforum,
			'status' => $topic_status
		);
		$save_topic = $wpdb->update(TOPIC_TABLE, $data, array('id' => $topic_id));
		if ($save_topic !== false)
		{
			?>
			<div class="wpbb-message-success">
				<?php printf(__('Topic ID %s edited successfully'), $topic_id); ?>
			</div>
			<?php
		}
		else
		{
			?>
			<div class="wpbb-message-failure">
				<?php printf(__('There was an error editing topic ID %s'), $topic_id); ?>
			</div>
			<?php
		}
	}
	// End Form Submission
	$topic = $wpdb->get_row("SELECT id, author, name, content, forum, subforum, status FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
	if ($topic != NULL)
	{
		?>
		<table class="wpbb-table">
			<form method='POST' action='#'>
				<?php
					$sticky = (strpos($topic->status, 'sticky') !== false) ? true : false;
					$locked = (strpos($topic->status, 'locked') !== false) ? true : false;
					$topic_author_name = wpbb_parse_author_name($topic->author);
					if (!$topic_author_name)
					{
						$topic_author_name = __('Guest', 'wp-bb');
					}
					?>
					<tr>
						<th><?php _e('Name', 'wp-bb'); ?></th>
						<td>
							<input name='wpbbtopicname' maxlength='45' size='25' value='<?php echo $topic->name; ?>' />
						</td>
					</tr>
					<input type="hidden" name="wpbboriginalauthorid" value="<?php echo $topic->author; ?>" />
					<?php
					if (current_user_can('manage_options'))
					{
						?>
						<tr>
							<th><?php _e('Author', 'wp-bb'); ?></th>
							<td>
								<input name='wpbbauthorname' maxlength='45' size='25' value='<?php echo $topic_author_name; ?>' />
							</td>
						</tr>
						<?php
					}
					else
					{
						?>
						<tr>
							<th><?php _e('Author', 'wp-bb'); ?></th>
							<td>
								<input name='wpbbauthorname' maxlength='45' size='25' value='<?php echo $topic_author_name; ?>' disabled='disabled'/>
							</td>
						</tr>
						<?php
					}
					$is_forum = ($topic->forum > 0) ? true: false;
					$is_subforum = ($topic->subforum > 0) ? true : false;
					// Forums
					?>
					<tr>
						<th><?php _e('Choose Forum', 'wp-bb'); ?></th>
						<td>
							<input type='radio' name='wpbbtopicforum' value='yes' <?php echo checked($is_forum, true, false); ?>/> <?php _e('Yes', 'wp-bb'); ?>
							<input type='radio' name='wpbbtopicforum'  value='no' <?php echo checked($is_forum, false, false); ?> /> <?php _e('No', 'wp-bb'); ?>
							<select name='wpbbtopicforumname'>
								<?php
								$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum > 0 AND subforum = 0;");
								if ($forums !== false)
								{
									foreach ($forums as $forum)
									{
										?>
										<option value='<?php echo $forum->id; ?>' <?php selected($forum->id, $topic->forum); ?>>
											<?php echo $forum->name; ?>
										</option>
										<?php
									}
								}
								else
								{
									?>
									<div class="wpbb-message-failure">
										<?php _e('There are no forums', 'wp-bb'); ?>
									</div>
									<?php
									wpbb_exit();
								}
						// Subforums
						?>
					</td>
				</tr>
				<tr>
					<th><?php _e('Choose Subforum', 'wp-bb'); ?></th>
					<td>
						<input type='radio' name='wpbbtopicsubforum' value='yes' <?php echo checked($is_subforum, true, false); ?>/> <?php _e('Yes', 'wp-bb'); ?>
						<input type='radio' name='wpbbtopicsubforum'  value='no' <?php echo checked($is_subforum, false, false); ?>/> <?php _e('No', 'wp-bb'); ?>
						<select name='wpbbtopicsubforumname'>
						<?php
						$subforums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE subforum > 0 AND forum = 0;");
						if ($subforums !== false) {
							foreach ($subforums as $subforum)
							{
								?>
								<option value='<?php echo $subforum->id; ?>' <?php echo selected($subforum->id, $topic->subforum); ?>>
									<?php echo $subforum->name; ?>
								</option>
								<?php
							}
						}
						else
						{
							?>
							<div class="wpbb-message-failure">
								<?php _e('There are no subforums', 'wp-bb'); ?>
							</div>
							<?php
							wpbb_exit();
						}
						?>
					</td>
				</tr>
				<tr>
					<th><?php _e('Content', 'wp-bb'); ?></th>
					<td>
						<?php
						if ($wp_version <= "3.2")
						{
							the_editor("", "wpbbtopiccontent");
						}
						else
						{
							wp_editor($topic->content, 'wpbbtopiccontent');
						}
						?>
					</td>
				</tr>
				<tr>
					<th><?php _e('Status', 'wp-bb'); ?></th>
					<td>
						<input type='checkbox' name='wpbbtopicstatus[]' value='locked' <?php echo checked($locked, true, false); ?>/> <?php _e('Locked', 'wp-bb'); ?>
						<input type='checkbox' name='wpbbtopicstatus[]' value='sticky' <?php echo checked($sticky, true, false); ?>/> <?php _e('Sticky', 'wp-bb'); ?>
					</td>
				<tr>
					<td>
						<input type='submit' name='wpbbtopiceditsubmit' class='button-primary' value='<?php _e('Save Changes', 'wp-bb'); ?>' />
					</td>
				</tr>
			<?php
		
		?>
		</form>
	</table>
	<?php
	}
	else
	{
		?>
		<div style='text-align:center; font-color:red;'>
			<?php printf(__('There was an error fetching topic ID %s. Please try again.'), $topic_id); ?>
		</div>
		<?php
		wpbb_exit();
	}
}
else if ($is_post === true) 
{
	// It's a post
	if (isset($_POST['wpbbeditpostsubmit']))
	{
		$post_id = absint($_GET['post']);
		// If a topic wasn't chosen or content wasn't entered produce an error
		if (empty($_POST['wpbbposttopic']) || empty($_POST['wpbbpostcontent']))
		{
			?>
			<div class="wpbb-message-failure">
				<?php _e('You must choose a topic for the post and content must not be empty!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
		
		if (isset($_POST['wpbbpostauthor']))
		{
			if (is_numeric($_POST['wpbbpostauthor']))
			{
				$post_author = absint($_POST['wpbbpostauthor']);
				// If the original author id is not null
				if (isset($_POST['wpbboriginalauthorid']))
				{
					$original_author_id = absint($_POST['wpbboriginalauthorid']);
					// if the new author id is not the same as our original author id, then we can assume it is a new user
					// thus we decrease the old users post count by 1
					if ($post_author !== $original_author_id)
					{
						$original_author_posts = get_user_meta($original_author_id, 'wpbb_posts', true);
						$new_original_author_posts = intval($original_author_posts) - 1;
						update_user_meta($original_author_id, 'wpbb_posts', $new_original_author_posts);
					}
				}
			}
			else
			{
				$post_author_name = wp_strip_all_tags($_POST['wpbbpostauthor']);
				$post_author = wpbb_parse_author_name(NULL, $post_author_name);
				// If the original author id is not null
				if (isset($_POST['wpbboriginalauthorid']))
				{
					$original_author_id = absint($_POST['wpbboriginalauthorid']);
					// if the new author id is not the same as our original author id, then we can assume it is a new user
					// thus we decrease the old users post count by 1
					if ($post_author !== $original_author_id)
					{
						$original_author_posts = get_user_meta($original_author_id, 'wpbb_posts', true);
						$new_original_author_posts = intval($original_author_posts) - 1;
						update_user_meta($original_author_id, 'wpbb_posts', $new_original_author_posts);
					}
				}
			}
		}
		else
		{
			// If nothing was inserted in the author field, use the current user ID (would be old)
			$post_author = get_current_user_id();
		
			// Else the user will be a guest
			if (!$post_author)
			{
				// Guest
				$post_author = 0;
			}
		}
	
		
		// Increase the new authors post count by 1 for the posts as well
		$new_author_posts = get_user_meta($post_author, 'wpbb_posts', true);
		$new_author_posts = intval($new_author_posts) + 1;
		update_user_meta($post_author, 'wpbb_posts', $new_author_posts);
			
		$post_content = wpbb_strip_tags($_POST['wpbbpostcontent']);
		
		$post_topic = absint($_POST['wpbbposttopic']);
		
		$updated_date = date("Y-m-d H:i:s");
		
		$data = array( 
			'author' => $post_author,
			'topic' => $post_topic,
			'text' => $post_content,
			'created' => $updated_date
		);
			
		$update_post = $wpdb->update(POST_TABLE, $data, array('id' => $post_id));
		
		$update_topic = $wpdb->update(TOPIC_TABLE, array('last_reply' => $updated_date), array('id' => $post_topic));
			
		$wpdb->show_errors();
				
		if ($update_post === false)
		{ // Db error
			?>
			<div class="wpbb-message-failure">
				<?php _e('There was an error attempting to save your post, please try again.', 'wp-bb'); ?>
			</div>
			<?php
		}
		else
		{ // Success
			?>
			<div class="wpbb-message-success">
				<?php _e('Thank you, your post has been saved successfully', 'wp-bb'); ?>
			</div>
			<?php
		}
	}
	// End post edit submission
		
	// Attempt to retrieve the post from the db
	$get_post = $wpdb->get_results("SELECT * FROM ".POST_TABLE." WHERE id = $post_id;");
		
	// If there wasn't an error...
	if ($get_post !== false)
	{
		?>
		<table class="wpbb-table">
			<form method='POST' action='#'>
				<?php
				foreach ($get_post as $_post) {
				
				$post_author_name = wpbb_parse_author_name($_post->author);
				
				if (current_user_can('manage_options'))
				{
					?>
					<tr>
						<th><?php _e('Author', 'wp-bb'); ?></th>
						<td>
							<input name='wpbbpostauthor' maxlength='45' size='25' value='<?php echo $post_author_name; ?>' />
						</td>
					</tr>
					<?php
				}
				else
				{
					?>
					<tr>
						<th><?php _e('Author', 'wp-bb'); ?></th>
						<td>
							<input name='wpbbpostauthor' maxlength='45' size='25' value='<?php echo $post_author_name; ?>' disabled='disabled' />
						</td>
					</tr>
					<?php
				}
				?>
				<tr>
					<th><?php _e('Topic', 'wp-bb'); ?></th>
					<td>
						<input name='wpbbposttopic' maxlength='45' size='24' value='<?php echo $_post->topic; ?>' />
					</td>
				</tr>
				<tr>
					<th><?php _e('Content', 'wp-bb'); ?></th>
					<td>
						<?php
						if ($wp_version <= "3.2")
						{
							the_editor("", "wpbbpostcontent");
						}
						else
						{
							wp_editor($_post->text, 'wpbbpostcontent');
						}
						?>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type='submit' name='wpbbeditpostsubmit' value='<?php _e('Save Changes', 'wp-bb'); ?>' />
					</td>
				</tr>
				<?php
			}
			?>
			</form>
		</table>
		<?php
		}
		else
		{ // Error
			?>
			<div class="wpbb-message-failure">
				<?php _e('There was an error retrieving the post. Please try again.', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
?>