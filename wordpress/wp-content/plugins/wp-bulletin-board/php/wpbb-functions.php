<?php

/**
 * Retrieves the link and image of the create reply and/or create topic buttons
 * 
 * Retrieves the link and image of the create reply and/or create topic buttons depending on current theme choice and where the user is
 *
 * @since 1.0.0
 *
 * @param    int   		$forum	  	The ID of the forum
 * @param	 int 		$subforum	The ID of the subforum
 * @param	 int 		$topic		The ID of the topic
 * @param	 string     $type		String denoting the area the button will appear
 *						
 */
 
function wpbb_buttons($forum, $subforum = NULL, $topic = NULL, $type = 'forum')
{
	$options = get_option('wpbb_theme_options');
	if ($options['theme'] == 'light') {
		$theme = plugins_url().'/wp-bulletin-board/images/light/';
	} else {
		$theme = plugins_url().'/wp-bulletin-board/images/dark/';
	}
	if ($type == 'forum') {
		?>
		<div class="wpbb-topic-buttons">
			<a href="<?php echo add_query_arg(array('forum' => $forum, 'create' => $forum), wpbb_permalink()); ?>">
				<img src="<?php echo $theme; ?>create-topic.png" width="100px" height="30px" />
			</a>
		</div>
		<?php
	} else if ($type == 'subforum') {
		?>
	<div class="wpbb-topic-buttons">
		<a href="<?php echo add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'create' => $subforum), wpbb_permalink()); ?>">
			<img src="<?php echo $theme; ?>create-topic.png" width="100px" height="30px" />
		</a>
	</div>
	<?php
	} else if ($type == 'forum_topic') {
		?>
	<div class="wpbb-topic-buttons"><a href="<?php echo add_query_arg(array('forum' => $forum, 'create' => $forum), wpbb_permalink()); ?>"><img src="<?php echo $theme; ?>create-topic.png" width="100px" height="30px" /></a><a href="<?php echo add_query_arg(array('forum' => $forum, 'topic' => $topic, 'action' => 'reply'), wpbb_permalink()); ?>"><img src="<?php echo $theme; ?>create-reply.png" width="100px" height="30px" /></a></div>
	<?php
	
	} else if ($type == 'subforum_topic') {
	?>
	<div class="wpbb-topic-buttons"><a href="<?php echo add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'create' => $subforum), wpbb_permalink()); ?>"><img src="<?php echo $theme; ?>create-topic.png" width="100px" height="30px" /></a><a href="<?php echo add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'action' => 'reply'), wpbb_permalink()); ?>"><img src="<?php echo $theme; ?>create-reply.png" width="100px" height="30px" /></a></div>
	<?php
	}
}

/**
 * Returns an array containing a topics status buttons (locked and sticky)
 *
 *
 * @since 1.0.0
 *
 * @param    string   	$topic_status		A topics status
 * @return   array   	 					An array containing the locked and sticky buttons or an empty string if false
 *
 */
function wpbb_get_topic_status_buttons($topic_status) {
	$options = get_option('wpbb_theme_options');
	
	if ($options['theme'] == 'light') {
		$theme = plugins_url().'/wp-bulletin-board/images/light/';
	} else {
		$theme = plugins_url()."/wp-bulletin-board/images/dark/";
	}
	
	if (strpos($topic_status, 'locked') !== false) {
		$locked = "<img src='".$theme."locked-topic.png' width='20px' height='20px' />"; 
	} else {
		$locked = "";
	}
	if (strpos($topic_status, 'sticky') !== false) {
		$sticky = "<img src='".$theme."sticky-topic.png' width='20px' height='20px' />";
	} else {
		$sticky = "";
	}
	return array ('locked' => $locked, 'sticky' => $sticky);
}

/**
 * Retrieves a link which the user can click to go back one page or to the main page
 * 
 *
 * @since 1.0.2
 *
 * @param    int   		$forum_id		The ID of the forum
 * @param    int   		$subforum_id 	The ID of the subforum
 * @param    int   		$topic_id 		The ID of the topic
 *						
 */

function wpbb_goback1($where = NULL, $topic_id = NULL) {
	global $forum_page; 
	$page = get_page($forum_page);
	$permalink = get_permalink($page->ID);
	$forum_id = isset($_GET['forum']) ? $_GET['forum'] : NULL;
	$subforum_id = isset($_GET['subforum']) ? $_GET['subforum'] : NULL;
	$topic_id = isset($_GET['topic']) ? $_GET['topic'] : NULL;
	$query_args = array('forum' => $forum_id, 'subforum' => $subforum_id, 'topic' => $topic_id, 'current_page' => 1);
	$count = array();
	foreach ($query_args as $query) {
		if ($query != NULL) {
			$count[] = $query;
		}
	}
	if (count($count) == 1) {
		$url = $permalink;
	} else {
		$url = add_query_arg($query_args, wpbb_permalink());
	}
	switch ($where) {
		case 'forum-index':
		?>
		<div class="wpbb-centered-bold">
			<a href="<?php echo $permalink; ?>"><?php _e('Go back', 'wp-bb'); ?></a>
		</div>
		<?php
		break;
		default:
		?>
		<div class="wpbb-centered-bold">
			<a href="<?php echo $url; ?>"><?php _e('Go back', 'wp-bb'); ?></a>
		</div>
		<?php
		break;
	}
}


/**
 * WPBB version of get_permalink()
 * 
 * Retrieves the wpbb forum page permalink
 *
 * @since 1.0.0
 * 
 * @return   string 	Link to the forum page 
 *						
 */
 
function wpbb_permalink() {
	global $wpdb;
	$post_meta = $wpdb->prefix.'postmeta';
	$page_id = $wpdb->get_var("SELECT post_id FROM $post_meta WHERE meta_key = '_wp_page_template' AND meta_value = 'wpbb-template.php';");
	
	if ($page_id != NULL) {
		$permalink = get_permalink($page_id);
		return $permalink;
	}
}

/**
 * Updates a users wpbb_posts depending on second argument given
 * 
 * This function will update a users wpbb_posts meta key value when they create a new topic / post and can also be used to decrease this value
 * if 'decrease' is given as the second argument e.g. when their topic or post is deleted etc.
 *
 * @since 1.0.0
 *
 * @param    int   		$user_id	The current users ID 
 * @param	 string		$type		'increase' to increase the value or 'decrease' to decrease the value (wpbb_posts meta key)
 *						
 */
function wpbb_update_user_meta($user_id, $type = 'increase') {
	if ($type == 'increase') {
	
		// Get the user meta value for wpbb_posts so we know how many posts they have
		$old_value = get_user_meta($user_id, 'wpbb_posts', true);
		
		$new_value = $old_value + 1;
		
		$add_user_meta = update_user_meta($user_id, 'wpbb_posts', $new_value);
	
		if ($add_user_meta === false) {
			_e('Error: Failed to increase/update the users wpbb_posts user meta data', 'wp-bb');
		}
	} else if ($type == 'decrease') {
		
		// Get the user meta value for wpbb_posts so we know how many posts they have
		$old_value = get_user_meta($user_id, 'wpbb_posts', true);
	
		$new_value = $old_value - 1;
		
		$add_user_meta = update_user_meta($user_id, 'wpbb_posts', $new_value);
	
		if ($add_user_meta === false) {
			_e('Error: Failed to decrease/update the users wpbb_posts user meta data', 'wp-bb');
		}
	}
}

// This function doesn't appear to be being used will need to keep it here to make sure
/*function wpbb_delete_user_meta($user_id) {

	$delete_user_wpbb_post_meta_data = delete_user_meta($user_id, 'wpbb_posts');
	
	if ($delete_user_wpbb_post_meta_data !== true) {
		_e('Error: Failed to delete the users wpbb_posts user meta data', 'wp-bb');
	}
}*/

/**
 * Find out if a topic is considered "fresh" depending on publish / last publish date
 * 
 * This function takes the topics last reply or created value to determine if the topic was published
 * less than 12 hours ago. If so it is considered "fresh" i.e. new otherwise it is considered old.
 * Depending on current theme choice, different folder icons are used to reflect the topics freshness.
 *
 * @since 1.0.0
 *
 * @param    string   	$datetime	A string representation of a date (Y-m-d H:i:s)
 * @return   string 				An image representing freshness depending on theme choice
 *						
 */
function wpbb_get_topic_freshness($datetime) {

	$options = get_option('wpbb_theme_options');

	$template_directory = get_template_directory();

	$current_date = date("Y-m-d H:i:s");
	
	$diff = abs(strtotime($current_date) - strtotime($datetime));
	
	if ($diff > 43200) { // 12 hours
		if ($options['theme'] == 'light') {
			return "<img src='".plugins_url()."/wp-bulletin-board/images/light/old-topic.png' width='20px' height='20px' />";
		} else {
			return "<img src='".plugins_url()."/wp-bulletin-board/images/dark/old-topic.png' width='20px' height='20px' />";
		}
	} else {
		if ($options['theme'] == 'light') {
			return "<img src='".plugins_url()."/wp-bulletin-board/images/light/new-topic.png' width='20px' height='20px' />";
		} else {
			return "<img src='".plugins_url()."/wp-bulletin-board/images/dark/new-topic.png' width='20px' height='20px' />";
		}
	}
}


/**
 * Simple function to strip all tags except the allowed tags we specify that should not be stripped.
 * 
 * 
 * @since 1.0.0
 *
 * @param    string   	$value    The input string
 * $var		 string		$skip	  A list of allowed tags
 * @return   string     		  Returns the stripped string 
 *										 	 
 */
function wpbb_strip_tags($value)
{
	// Strip the content of any backslashes
	$value = stripslashes($value);
	$skip = '<p><b><strong><i><span><blockquote><ol><ul><li><del><em><strong><a><img>';
	return strip_tags($value, $skip);
}

/**
 * Retrieves a topics status value
 * 
 * Retrieves the value of the status field in the wpbb_topics table and returns it for use 
 *
 * @since 1.0.0
 *
 * @param    int    	$topic	     	The topic ID to fetch the status of
 * @return   string     $topic_status	Returns a comma seperated string containing the topics status (locked, sticky) etc	
 */

function wpbb_get_topic_status($topic_id) {
	global $wpdb;
	$topic_status = $wpdb->get_var("SELECT status FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
	if ($topic_status !== false) {
		return $topic_status;
	}
}

/**
 * Retrieves a user name or user ID depending on arguments supplied
 * 
 * Simple function for retrieving a user name from an ID or the authors ID from the name in case you do not know one or the other. 
 *
 * @since 1.0.0
 *
 * @param    int    	$user_id     The ID of the author whose name you want to retrieve
 * @param 	 string		$user_name	 The login name of the user you want to retrieve the ID of
 * @return   string/int/false        Atleast one argument must be provided otherwise function returns false. 
 *									 String if returning display_name or ID if returning ID.
 */

function wpbb_parse_author_name($user_id, $user_name = NULL) {
	if ($user_name === NULL) {
		if ($user_id === NULL) return false;
		$user_display_name = get_the_author_meta('display_name', $user_id);
		return $user_display_name;
	} else {
		if ($user_id !== NULL) return false;
		$user = get_user_by('login', $user_name);
		if ($user) {
			return $user->ID;
		}
	}
}

/**
 * Retrieves a list of all Wordpress roles (including any created using a plugin)
 * 
 *
 * @since 1.0.0
 *
 * @param    string|array	$return		Whether to return a string or array of WP roles	 
 * @return   string|array 	$_roles		A string or array containing all WP roles
 *						
 */
function wpbb_get_all_roles($return = 'array') {
	$wp_roles = new WP_Roles();
	$options = get_option('wpbb_options');
	if ($options['allow_guests'] == 'yes') {
		if (!$wp_roles->get_role('guest')) {
			$guest_role = $wp_roles->add_role('guest', 'Guest', false);
		}
	}
 	$roles = $wp_roles->get_names();
 	$_roles = array();
 	foreach ($roles as $role) {
 		
 		$_roles[] = strtolower($role);
 	}
 	if ($return == 'string') {	
 		return implode(',', $_roles);
 	} else {
 		return $_roles;
 	}
}

/**
 * Returns only those keys (roles) who match the wp keys (roles)
 * 
 * This function only returns those roles which are available in WP and WPBB (not custom roles that may be used by other plugins but not for the purpose of this plugin)
 *
 *
 * @since 1.0.3
 *
 * @param    string	 $key	The key to check
 * @return   bool		    True if role matches, false otherwise
 *						
 */
function wpbb_filter_roles($key) {
	$wp_roles = wpbb_get_all_roles(); 
	return (in_array($key, $wp_roles));
}

function wpbb_user_permission($user_id) {
	global $wpdb;
	$user_roles = get_user_meta($user_id, $wpdb->prefix.'capabilities', true);
	// Guest
	if (empty($user_roles)) {
		$options = get_option('wpbb_options');
		// Is guest functionality enabled?
		if ($options['allow_guests']) {
			$user_role_string = 'guest';
			return $user_role_string;
		}
	} else { // Not a guest
		$user_role_keys = array_keys($user_roles);
		$user_role_keys_filtered = array_filter($user_role_keys, "wpbb_filter_roles"); // < PHP 5.3.0 support
		$user_role_string = implode(",", $user_role_keys_filtered);
		$user_role_string = strtolower($user_role_string);
		return $user_role_string;
	}
}

/**
 * Checks whether the current user ID has permission to do something
 * 
 * This function will check if the user ID $user_id can do $what in forum $forum_id.
 * Note that a forums permissions take precendence over its subforums
 * Permissions: view, read, post, reply, edit, lock, delete, sticky
 *
 *
 * @since 1.0.0
 *
 * @param    int   		$user_id	The current users ID
 * @param    int   		$forum_id	The forum ID
 * @param    string   	$what		String representing the permission to check the user has in a subforum and/or forum
 * @return   bool 	 				True if user has permission, false otherwise.
 *						
 */
 
function wpbb_user_has_permission($user_id, $forum_id, $what = 'view')
{
	global $wpdb;
	$user_roles = get_user_meta($user_id, $wpdb->prefix.'capabilities', true);
	if (!empty($user_roles))
	{
		$user_role_keys = array_keys($user_roles);
		$user_role_keys_filtered = array_filter($user_role_keys, "wpbb_filter_roles"); // < PHP 5.3.0 support
		$user_role_string = implode(",", $user_role_keys_filtered);
		$user_role_string = strtolower($user_role_string);
	}
	else
	{ 
		// User is considered a guest
		$options = get_option('wpbb_options');
		// Is guest functionality enabled?
		if ($options['allow_guests'])
		{
			$user_role_string = 'guest';
			
		}
		else
		{ 
			// Show them the door...
			wpbb_goback1();
			?>
			<div class="wpbb-centered-bold">
				<?php printf(__('You must <a href="%s">Login</a> or <a href="%s">Register</a> to be able to view the forum!', 'wp-bb'), wp_login_url(), site_url('wp-login.php?action=register')); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
	$forum_permissions = $wpdb->get_results("SELECT `view`, `read`, `post`, `reply`, `edit`, `lock`, `delete`, `sticky` FROM ".CATEGORIES_TABLE." WHERE id = $forum_id;");
	foreach ($forum_permissions as $forum_permission)
	{
		switch ($what)
		{
			case 'view':
				if (strpos($forum_permission->view, $user_role_string) !== false) return true; else return false; 
				break;
			case 'read':
				if (strpos($forum_permission->read, $user_role_string) !== false) return true; else return false; 
				break;
			case 'post':
				if (strpos($forum_permission->post, $user_role_string) !== false) return true; else return false; 
				break;
			case 'reply':
				if (strpos($forum_permission->reply, $user_role_string) !== false) return true; else return false; 
				break;
			case 'edit':
				if (strpos($forum_permission->edit, $user_role_string) !== false) return true; else return false; 
				break;
			case 'lock':
				if (strpos($forum_permission->lock, $user_role_string) !== false) return true; else return false; 
				break;
			case 'delete':
				if (strpos($forum_permission->delete, $user_role_string) !== false) return true; else return false; 
				break;
			case 'sticky':
				if (strpos($forum_permission->sticky, $user_role_string) !== false) return true; else return false; 
				break;
		}
	}
}

/**
 * Simple forum pagination
 * 
 * Currently paginates topics within a subforum and forum and posts within a topic
 *
 * @since 1.0.0
 *
 * @param    int   		$forum				The forum ID
 * @param    int   		$page				The current page ID
 * @param    			$start				Unused
 * @param       		$limit				Unused
 * @param    int   		$total_topics		Total topics per forum page (set in settings)
 * @param    int   		$topics_per_page	Topics per page (set in settings)
 * @param    	   		$topics				Unused
 *						
 */
function wpbb_pagination($forum, $page, $start, $limit, $total_topics, $topics_per_page, $topics = true, $where = NULL) {

	/**
 	* @todo ... Remove $start, $limit, $topics from this function and anywhere its called - they are no longer used
	*/

	// First page link will always be 1
	$first_page = 1;
	
	// This'll take the user to page 1
	$first_page_link = add_query_arg(array('forum' => $forum, 'current_page' => $first_page));
	
	// If current page is 1, prev page is current page
	if ($page === 1) {
	
		$prev_page_link = add_query_arg(array('forum' => $forum, 'current_page' => 1)); 
		
	} else { // Otherwise prev page is current page - 1
	
		$prev_page_link = add_query_arg(array('forum' => $forum, 'current_page' => $page - 1));
		
	}
	
	// Total pages is total number of topics divided by amount of topics per page
	$total_pages = $total_topics / $topics_per_page;
	
	// If total pages is a float, round up to the next highest integer
	if (is_float($total_pages)) {
		$total_pages = ceil($total_pages);
	}
	
	// Total pages should always be atleast 1
	if ($total_pages === 0) $total_pages = 1;
	
	
	// Next page is current page + 1
	$next_page_number = $page + 1;
	
	// However if next page is more than total pages, return last page number
	if ($next_page_number > $total_pages) {
	
		$next_page_number = $page;
		
		$next_page_link = add_query_arg(array('forum' => $forum, 'current_page' => $next_page_number));
		
	} else { // Otherwise next page is current page +1
	
		$next_page_link = add_query_arg(array('forum' => $forum, 'current_page' => $next_page_number));
		
	}
	
	// If there is atleast 1 topic, begin pagination 
	if ($total_topics > 0) {
	
		?>
	
		<div class="wpbb-centered">
	
		<div>
	
		<?php 
			if ($where == 'messages') {
				$where = 'messages';
			} else {
				$where = 'topics';
			}
			// Displays 1 to topics per page set in settings and number of total pages
		?>
		
		<span class="wpbb-bold">
			<?php printf(__('Displaying 1-%s %s of %s pages', 'wp-bb'), $topics_per_page, $where, $total_pages); ?>
		</span> 
		
		<br />
		
		<?php // If total pages is more than 1 we may need a link to return to the first page 
		if ($total_pages > 1) {
			?>
			<a href='<?php echo $first_page_link; ?>'>&laquo; (<?php _e('first page', 'wp-bb'); ?>)</a>
			<?php
		}
		?>
		
		<a href='<?php echo $prev_page_link; ?>'><?php _e('Prev Page', 'wp-bb'); ?></a>
	
		<?php // Loop through the total pages echoing links to individual pages
		for ($i = 1; $i < $total_pages + 1; $i++) {
			if ($i == $page) { 
				?>
				<span><?php echo $i; ?></span>
				<?php
			//} else if (($page > $i) && ($page < $i * $page)) {
			} else if ($i < ($page + 10)) {
				$forum_link = add_query_arg(array('forum' => $forum, 'current_page' => $i));
				?>
				<a href='<?php echo $forum_link; ?>'><?php echo $i; ?></a>
				<?php
			}
		}
		
		?>
		<a href='<?php echo $next_page_link; ?>'><?php _e('Next Page', 'wp-bb'); ?></a>
		
		<?php
	
		$last_page_link = add_query_arg(array('forum' => $forum, 'current_page' => $total_pages));

		if ($total_pages > 1) {
			?>
			<a href='<?php echo $last_page_link; ?>'>(<?php _e('last page', 'wp-bb'); ?>) &raquo;</a> </div>
			<?php
		}
		?>
		<div class="clear"></div>
		</div>
		<?php
		
	}
}

/**
 * Get a forum, subforum or topics last reply
 * 
 * Retrieves the 'created' date from a topic unless it has posts in which case it uses 'last_reply'
 * If we are gathering this information about a subforum then we grab all those topics and find the max value
 * If we are gathering this information about a forum we grab that info aswell and find the max value
 *
 * @since 1.0.0
 *
 * @param    int   		$forum_id		The forum ID
 * @param    int   		$subforum_id	The subforum ID 
 * @param    int   		$topic_id		The topic ID
 * @param    string   	$which			Whether we are referring to a forum, subforum or topic (sometimes one or more)
 * @return   string 					A string representation of a date (Y-m-d H:i:s)
 *						
 */
 
function wpbb_get_last_reply($forum_id = NULL, $subforum_id = NULL, $topic_id = NULL, $which = 'forum') {
	global $wpdb;
	if (($forum_id === NULL) && ($subforum_id == NULL) && ($topic_id === NULL)) {
		_e('Atleast one of the first three arguments passed to wpbb_get_last_reply() must not be NULL', 'wp-bb');
	}
	$forum_last_post = "";
	if ($which == 'forum') {
		$forum_last_topic = $wpdb->get_var("SELECT max(last_reply) FROM ".TOPIC_TABLE." WHERE forum = $forum_id;");
		$forum_topic_id = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE last_reply = '$forum_last_topic';");
		if ($forum_topic_id > 0) {
			$forum_last_post = $wpdb->get_var("SELECT max(created) FROM ".POST_TABLE." WHERE topic = $forum_topic_id;");
		}
		$forum_last_reply = max($forum_last_topic, $forum_last_post);
		if ($forum_last_reply === NULL) {
			return "0000-00-00 00:00:00";
		} else {
			return $forum_last_reply;
		}
	} else if ($which == 'subforum') {
		$subforum_last_topic = $wpdb->get_var("SELECT max(last_reply) FROM ".TOPIC_TABLE." WHERE subforum = $subforum_id;");
		$subforum_topic_id = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE last_reply = '$subforum_last_topic';");
		// Default value
		$subforum_last_post = '0000-00-00 00:00:00';
		if ($subforum_topic_id > 0) {
			$subforum_last_post = $wpdb->get_var("SELECT max(created) FROM ".POST_TABLE." WHERE topic = $subforum_topic_id;");
		}
		$subforum_last_reply = max($subforum_last_topic, $subforum_last_post);
		if ($subforum_last_reply === NULL) {
			return "0000-00-00 00:00:00";
		} else {
			return $subforum_last_reply;
		}
	} else if ($which == 'topic') {
		$topic_last_post = $wpdb->get_var("SELECT max(created) FROM ".POST_TABLE." WHERE topic = $topic_id;");
		if ($topic_last_post === NULL) {
			$topic_created = $wpdb->get_var("SELECT created FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
			return $topic_created;
		} else {
			return $topic_last_post;
		}
	}
}

/**
 * Returns array containing count of topics and posts within a forum or subforum
 * 
 *
 * @since 1.0.0
 *
 * @param    int   		$forum_id	The forum or subforum ID
 * @param 	 string		$where		String representing if we are referring to a forum or subforum
 * @return   array 	 				Array containing count of topics and posts
 *						
 */
function wpbb_get_topics_posts($forum_id, $where = 'forum') {
	global $wpdb;
	$total_posts = 0;
	if ($where == 'forum') { // Getting topics and posts from a forum
		$topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPIC_TABLE." WHERE forum = $forum_id;");
		$topic_id = $wpdb->get_results("SELECT id FROM ".TOPIC_TABLE." WHERE forum = $forum_id;");
		foreach ($topic_id as $topic) {
			$posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
			$total_posts += $posts;
		}
	} else if ($where == 'subforum') { // Otherwise it's a subforum
		$topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPIC_TABLE." WHERE subforum = $forum_id;");
		$topic_id = $wpdb->get_results("SELECT id FROM ".TOPIC_TABLE." WHERE subforum = $forum_id;");
		foreach ($topic_id as $topic) {
			$posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POST_TABLE." WHERE topic = $topic->id;");
			$total_posts += $posts;
		}
	}
	if ($total_posts === NULL) $total_posts = 0;
	return array('topics' => $topics, 'posts' => $total_posts);
}

/**
 * Retrieves the current location e.g. Forum / Subforum / Topic (Viewing Topic)
 * 
 * Retrieves the link and name of the path to the users current location in the script
 *
 * @since 1.0.0
 *
 * @param    int   		$forum_id 		The forum ID
 * @param    int   		$subforum_id	The subforum ID
 * @param    int   		$topic_id 		The topic ID
 * @param    bool    	$subforum 		True if referring to a subforum
 * @param    bool    	$topic			True if referring to a topic
 * @param    bool   	$index			True if you just want to return the main forum name
 * @return   array 	 					String if returning the main forum name, array containing the name (includes link) of the forum, subforum or topic (one or more)
 *						
 */

function wpbb_location($forum_id = NULL, $subforum_id = NULL, $topic_id = NULL, $subforum = false, $topic = true, $index = false) {
	/**
 	* @todo ... Improve this function
 	*/
	global $wpdb;
	// For lack of better method access the forum_page global we created earlier
	global $forum_page;
	// Get page ID
	$page = get_page($forum_page);
	$permalink = get_permalink($page->ID);
	// Retrieve WPBB options - only works with board name atm
	$mywpbb_options = get_option('wpbb_options');
	// Display board name (including link/guid)
	$mywpbb = "<a href=".$permalink.">".$mywpbb_options['forum_name']."</a>";
	// If we just want the board name then just return that
	if ($index) {
		return $mywpbb;
	}
	
	// If using a topic ID to fetch its location (link and name)
	if ($topic) {
		if ($subforum) {
			// Sanitize forum, subforum and topic IDs from GET superglobal
			$forum_id = absint($_GET['forum']);
			$subforum_id = absint($_GET['subforum']);
			$topic_id = absint($_GET['topic']);
			// Get topic name from the ID being viewed
			$topic_name = $wpdb->get_var("SELECT name FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
			// Get the ID of the subforum which this topic ID belongs to
			$subforum_id = $wpdb->get_var("SELECT subforum FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
			// With that ID we can retrieve the subforum name
			$subforum_name = $wpdb->get_var("SELECT name FROM ".CATEGORIES_TABLE." WHERE id = $subforum_id;");
			// We also need the name of the subforum from the subforum ID
			$subforum = $wpdb->get_var("SELECT subforum FROM ".CATEGORIES_TABLE." WHERE id = $subforum_id;");
			// Then we retrieve the name of the forum using a subforums subforum field as its ID
			$forum_name = $wpdb->get_var("SELECT name FROM ".CATEGORIES_TABLE." WHERE id = $subforum;");
			$forum_link = add_query_arg(array('forum' => $forum_id, 'current_page' => 1), get_permalink());
			$subforum_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $subforum_id, 'current_page' => 1), get_permalink());
			$topic_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $subforum_id, 'topic' => $topic_id, 'current_page' => 1), get_permalink());
			$forum_name = "<a href='".$forum_link."'>".$forum_name."</a>";
			$subforum_name = "<a href='".$subforum_link."'>".$subforum_name."</a>";
			$topic_name = "<a href='".$topic_link."'>".$topic_name."</a>";
			// Create the array that will be returned containing this info
			$title = array('forum_name' => $mywpbb, 'forum' => $forum_name, 'subforum' => $subforum_name, 'topic' => $topic_name);
		} else { // It's a forum
			// Sanitize forum and topic IDs from GET superglobal
			$forum_id = absint($_GET['forum']);
			$topic_id = absint($_GET['topic']);
			// Get topic name from topic ID we're looking at
			$topic_name = $wpdb->get_var("SELECT name FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
			// Get the ID of the forum where this topic belongs
			$topic_forum = $wpdb->get_var("SELECT forum FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
			// Create the link, ommiting NULL because it will need the forum arg aswell to access the topic
			$topic_link = add_query_arg(array('forum' => $forum_id, 'topic' => $topic_id, 'current_page' => 1), get_permalink());
			// Create the topic name string consisting of a link to the topic and its name
			$topic_name = "<a href='".$topic_link."'>".$topic_name."</a>";
			// Get the name of the forum where the ID matches the ID in the topics 'forum' field
			$forum_name = $wpdb->get_var("SELECT name FROM ".CATEGORIES_TABLE." WHERE id = $topic_forum;");
			// Create the forum query arg, pass NULL so that it removes any other args
			$forum_link = add_query_arg(array('forum' => $forum_id, 'current_page' => 1), get_permalink());
			// Then create the forum name string consisting of its link and name
			$forum_name = "<a href='".$forum_link."'>".$forum_name."</a>";
			// Create the array that will be returned containing this info
			$title = array('forum_name' => $mywpbb, 'forum' => $forum_name, 'topic' => $topic_name);
		}
		// Return array to the script
		return $title;
	} else { // Dealing with a forum or subforum
		if (($forum_id != '') && ($subforum_id == '')) { // Forum
			// Get the forum name from the ID being viewed
			$forum_name = $wpdb->get_var("SELECT name FROM ".CATEGORIES_TABLE." WHERE id = $forum_id;");
			// Create the query arg for the forum ID
			$forum_link = add_query_arg(array('forum' => $forum_id, 'current_page' => 1), get_permalink());
			// Create forum name string consisting of its link and name.
			$forum_name = "<a href='".$forum_link."'>".$forum_name."</a>";
			// Return array consisting of the board name and the forum name being viewed
			return $title = array('forum_name' => $mywpbb, 'forum' => $forum_name);
		} else if ($subforum_id != '') { // Subforum
			// Get what forum the subforum belongs to
			$subforum = $wpdb->get_var("SELECT subforum FROM ".CATEGORIES_TABLE." WHERE id = $subforum_id;");
			// Get the forum whose ID matches the subforums subforum field
			$forum_id = $wpdb->get_var("SELECT id FROM ".CATEGORIES_TABLE." WHERE id = $subforum;");
			// Get the name of the forum from the forums ID
			$forum_name = $wpdb->get_var("SELECT name FROM ".CATEGORIES_TABLE." WHERE id = $subforum;");
			// Create the query arg, remove subforum query arg
			$forum_link = add_query_arg(array('forum' => $forum_id, 'current_page' => 1), get_permalink());
			// Create the forum name string consisting of a link with a url to the forum and the forum name
			$forum_name = "<a href='".$forum_link."'>".$forum_name."</a>";
			// Select the subforums name where ID equals to the current ID being viewed
			$subforum_name = $wpdb->get_var("SELECT name FROM ".CATEGORIES_TABLE." WHERE id = $subforum_id;");
			// Setup the subforum query arg, remove forum query arg
			$subforum_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $subforum_id, 'current_page' => 1), get_permalink());
			// Create subforum name string consisting of a link with a url to the subforum and the subforum name
			$subforum_name = "<a href='".$subforum_link."'>".$subforum_name."</a>";
			// Return the board name, forum name, subforum name as an array
			return $title = array('forum_name' => $mywpbb, 'forum' => $forum_name, 'subforum' => $subforum_name);
		}
	}
}

/**
 * Updates a users (including guests) last post date
 * 
 * When a user creates a topic or posts a reply / quick reply, this function updates their wpbb_lastpost meta key value
 * As of 1.0.3, updates a guests last post as well
 *
 * @since 1.0.3
 *
 * @param    int   	$user_id	The user ID
 *						
 */
function wpbb_update_user_lastpost($user_id) {
	$date = date("Y-m-d H:i:s");
	if ($user_id == 0) {
		update_option('wpbb_guest_options', array('guest_last_post' => $date));
	} else {
		$update_user_lastpost = update_user_meta($user_id, 'wpbb_lastpost', $date);
	}
}

/**
 * WPBB version of is_user_logged_in(). Displays error message or returns user ID
 * 
 * Displays a login/register (wordpress/facebook) message if no user ID is found otherwise returns the user ID
 *
 * @since 1.0.2
 *
 * @return 	int	 $user_id	Returns the user ID if the current user is logged in
 *						
 */
function wpbb_is_user_logged_in() {
	$options = get_option('wpbb_options');
	$facebook_options = get_option('wpbb_facebook_options');
	$user_id = get_current_user_id();
	if ($user_id === NULL || $user_id === 0) {
		if ($facebook_options['allow_facebook'] == 'yes') {
			$facebook_register_url = add_query_arg(array('register' => '', 'method' => 'facebook'), wpbb_permalink());
			$facebook_login_url = wpbb_get_facebook_login();
			?>
			<div class="wpbb-centered-bold">
				<a href="<?php echo $facebook_login_url; ?>"><?php _e('Login using Facebook', 'wp-bb'); ?></a>
				<?php _e('or', 'wp-bb'); ?>
				<a href="<?php echo $facebook_register_url; ?>"><?php _e('Register using Facebook', 'wp-bb'); ?></a>
			</div>
			<?php
		}
		?>
		<div class="wpbb-centered-bold">
			<?php printf(__('You must <a href="%s">Login</a> or <a href="%s">Register</a> to be able to view the forum!', 'wp-bb'), wp_login_url(), site_url('wp-login.php?action=register')); ?>
		</div>
		<?php
		wpbb_exit();
	} else {
		return $user_id;
	}
}
/**
 * Checks whether a forum, subforum or topic exists depending on arguments provided
 * 
 *
 * @since 1.0.2
 *
 * @param	string	$what		Must be one of three: 'forum', 'subforum' or 'topic' - refers to what you are checking exists. Default is forum.
 * @param	int		$what_id	Must be set and an integer otherwise won't return anything
 * @return 	int	 	$user_id	Returns the user ID if the current user is logged in
 *						
 */
function wpbb_check_exists($what = 'forum', $what_id) {
	if ((isset($what)) && (is_numeric($what_id))) {
		global $wpdb;
		if ($what == 'topic') {
			$topic_exists = $wpdb->get_var("SELECT id FROM ".TOPIC_TABLE." WHERE id = $what_id;");
			if ($topic_exists === NULL) {
				wpbb_goback1('forum-index');
				?>
				<div class="wpbb-message-failure">
					<?php _e('Sorry, that topic doesn\'t exist. Please try again.', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit();
			}
		} else if ($what == 'forum' || $what == 'subforum') {
			$exists = $wpdb->get_var("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $what_id;");
			if ($exists === NULL) {
				wpbb_goback1();
				$type = ($what == 'subforum') ? 'subforum' : 'forum';
				?>
				<div class="wpbb-message-failure">
					<?php _e('Sorry, that '.$type.' doesn\'t exist. Please try again.', 'wp-bb'); ?>
				</div>
				<?php
				wpbb_exit('forum-index');
			}
		}
	}
}

/**
 * Displays the WPBB powered by footer if it is enabled in settings
 *
 *
 * @since 1.0.2
 *
 *
 */
function wpbb_footer() {
	$options = get_option('wpbb_options');
	if ($options['show_footer'] == 'yes') {
		?>
		<div class="wpbb-forum-footer">
			<p>Powered by <a href="<?php echo WPBB_PLUGIN_URL; ?>">WPBB</a> <?php echo WPBB_VERSION; ?></p>
		</div>
		<?php
	}
}

/**
 * Checks whether the $_GET key/value pair(s) (page) exists
 *
 *
 * @since 1.0.2
 *
 *
 */
function wpbb_page_exists() {	
	$allowed_get_values = array(
		'forum', 'subforum', 'topic', 'post', 'current_page', 'action', 'create', 
		'profile', 'unread_topics', 'unanswered_topics', 
		'message', 'messages', 'view', 'delete_msg',
		'code', 'state', 'register', 'method', 'login', 
		'page_id'
	);

	foreach ($_GET as $get => $value) {
		if (!in_array($get, $allowed_get_values)) {
			wpbb_goback1();
			?>
			<div class="wpbb-message-failure">
				<?php _e('Sorry the page you requested could not be found. Please try again.', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
}

function wpbb_moderate_links($where = 'forum', $which = 'topic', $user_id, $forum, $subforum, $topic, $post, $created) {
	if ($which == 'topic') {
		$edit_permissions = wpbb_user_has_permission($user_id, $forum, 'edit');
		$lock_permissions = wpbb_user_has_permission($user_id, $forum, 'lock');
		$sticky_permissions = wpbb_user_has_permission($user_id, $forum, 'sticky');
		$delete_permissions = wpbb_user_has_permission($user_id, $forum, 'delete');
		$edit_topic_link = $lock_topic_link = $sticky_topic_link = $delete_topic_link = NULL;
		if ($where == 'forum') {
			if ($edit_permissions) {
				$edit_topic_link = sprintf(__('<a href="%s">Edit</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'topic' => $topic, 'action' => 'edit'), wpbb_permalink()));
			}
			if ($lock_permissions) {
				$lock_topic_link = sprintf(__('<a href="%s">Lock</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'topic' => $topic, 'action' => 'lock'), wpbb_permalink()));
			}
			if ($sticky_permissions) {
				$sticky_topic_link = sprintf(__('<a href="%s">Sticky</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'topic' => $topic, 'action' => 'sticky'), wpbb_permalink()));
			}
			if ($delete_permissions) {
				$delete_topic_link = sprintf(__('<a href="%s">Delete</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'topic' => $topic, 'action' => 'delete'), wpbb_permalink()));
			}
		} elseif ($where == 'subforum') {
			if ($edit_permissions) {
				$edit_topic_link = sprintf(__('<a href="%s">Edit</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'action' => 'edit'), wpbb_permalink()));
			}
			if ($lock_permissions) {	
				$lock_topic_link = sprintf(__('<a href="%s">Lock</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'action' => 'lock'), wpbb_permalink()));
			}	
			if ($sticky_permissions) {	
				$sticky_topic_link = sprintf(__('<a href="%s">Sticky</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'action' => 'sticky'), wpbb_permalink()));
			}	
			if ($delete_permissions) {	
				$delete_topic_link = sprintf(__('<a href="%s">Delete</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'action' => 'delete'), wpbb_permalink()));
			}
		}
		echo $edit_topic_link . " " . $lock_topic_link . " " . $sticky_topic_link . " " . $delete_topic_link . " " . $created;
	} elseif ($which == 'post') {
		$edit_permissions = wpbb_user_has_permission($user_id, $forum, 'edit');
		$delete_permissions = wpbb_user_has_permission($user_id, $forum, 'delete');
		$edit_post_link = $delete_post_link = NULL;
		if ($where == 'forum') {
			if ($edit_permissions) {
				$edit_post_link = sprintf(__('<a href="%s">Edit</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'topic' => $topic, 'post' => $post, 'action' => 'edit'), wpbb_permalink()));
			}
			if ($delete_permissions) {
				$delete_post_link = sprintf(__('<a href="%s">Delete</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'topic' => $topic, 'post' => $post, 'action' => 'delete'), wpbb_permalink()));
			}
		}
		elseif ($where == 'subforum') {
			if ($edit_permissions) {
				$edit_post_link = sprintf(__('<a href="%s">Edit</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'post' => $post, 'action' => 'edit'), wpbb_permalink()));
			}
			if ($delete_permissions) {
				$delete_post_link = sprintf(__('<a href="%s">Delete</a>', 'wp-bb'), add_query_arg(array('forum' => $forum, 'subforum' => $subforum, 'topic' => $topic, 'post' => $post, 'action' => 'delete'), wpbb_permalink()));
			}
		}
		echo $edit_post_link . " " . $delete_post_link . " " . $created;
	}
}

/**
 * Retrieves the current users roles
 * 
 *
 * @since 1.0.5
 *
 * @param	object		$user		A WP_User object
 * @return 	string	 	$roles		String delimited list of roles 
 *						
 */

function wpbb_get_user_roles($user)
{
	$wp_version = get_bloginfo('version');
	if ($wp_version <= "3.2")
	{
		return implode(",", array_keys($user->wp_capabilities));
	}
	else
	{
		return implode(",", $user->roles);
	}
}

/**
 * Simple function that prints an error message and then terminates the script
 * 
 *
 * @since 1.0.5
 *
 * @param    string		$text		The error message to display
 *						
 */
function wpbb_error($text)
{
	?>
	<div class="wpbb-message-failure">
		<?php printf(__('%s', 'wp-bb'), $text); ?>
	</div>
	<?php
}

/**
 * Simple function that prints a success message and then terminates the script
 * 
 *
 * @since 1.0.5
 *
 * @param    string		$text		The success message to display
 *						
 */
function wpbb_success($text)
{
	?>
	<div class="wpbb-message-success">
		<?php printf(__('%s', 'wp-bb'), $text); ?>
	</div>
	<?php
}


?>