<?php

/*
	WPBB Admin Functions
*/

/**
 * Admin version of retrieve a users ID or username depending on arguments provided
 * 
 * Simple function for retrieving a user name from an ID or the authors ID from the name in case you do not know one or the other. 
 *
 * @since 1.0.0
 *
 * @param    int    	$user_id     	The ID of the author whose name you want to retrieve
 * @param 	 string		$user_name	 	The login name of the user you want to retrieve the ID of
 * @return   string|int|false     		Atleast one argument must be provided otherwise function returns false. 
 *									 	String if returning display_name or ID if returning ID.
 */

function wpbb_admin_parse_author_name($user_id, $user_name = NULL) {
	if ($user_name === NULL) {
		if ($user_id === NULL) return false;
		$user_display_name = get_the_author_meta('display_name', $user_id);
		return $user_display_name;
	} else {
		if ($user_id !== NULL) return false;
		$user = get_user_by('login', $user_name);
		if ($user !== false) {
			return $user->ID;
		}
	}
}

/**
 * Admin WPBB version of get_permalink()
 * 
 * Retrieves the wpbb forum page permalink
 *
 * @since 1.0.0
 * 
 * @return   string 	Link to the forum page 
 *						
 */
 
function wpbb_admin_permalink() {
	global $wpdb;
	$post_meta = $wpdb->prefix.'postmeta';
	$page_id = $wpdb->get_var("SELECT post_id FROM $post_meta WHERE meta_key = '_wp_page_template' AND meta_value = 'wpbb-template.php';");
	
	if ($page_id != NULL) {
		$permalink = get_permalink($page_id);
		return $permalink;
	}
}

/**
 * Admin version wpbb_strip_tags.
 * 
 * Simple function to strip all HTML and PHP tags except those we defined
 *
 * @since 1.0.0
 *
 * @param    string    	$value     The string to strip HTML and PHP tags from
 * @return   string 	$value	   The stripped string								
 */

function wpbb_admin_strip_tags($value)
{
	$value = stripslashes($value);
	$skip = '<p><b><strong><i><span><blockquote><ol><ul><li><del><em><strong><a><img>';
	return strip_tags($value, $skip);
}

/**
 * Upon plugin activation, creates wpbb_posts and wpbb_signature user meta
 * 
 * This function is run upon plugin activation and grabs all users from the current blog and creates the neccessary user meta data for them
 *
 * @since 1.0.0
 */

function wpbb_setup_user_meta()
{
	$wordpress_version = get_bloginfo('version');
	if ($wordpress_version == "3.0")
	{
		$users = get_users_of_blog($GLOBALS['blog_id']);
	}
	else
	{
		$users = get_users(array('blog_id' => $GLOBALS['blog_id']));
	}
	
	if ($users != 0) {
	
		foreach ($users as $user) {
	
			$get_wpbb_user_posts_meta = get_user_meta($user->ID, 'wpbb_posts', true);
		
			$get_wpbb_user_signature_meta = get_user_meta($user->ID, 'wpbb_signature', true);
			
			if ($get_wpbb_user_posts_meta === false) {
				// wpbb_posts is set to 0
				$create_wpbb_user_posts_meta = add_user_meta($user->ID, 'wpbb_posts', 0, true);
				if ($create_wpbb_user_posts_meta === false) {
					$create_wpbb_user_posts_meta_failed = __('Failed to create the WPBB wpbb_posts user meta data for '.$user->ID.' on '.__LINE__.' in '.__FILE__, 'wp-bb');
					error_log($create_wpbb_user_posts_meta_failed);
				}
			}
			
			if ($get_wpbb_user_signature_meta === false) {
				// wpbb_signature is set to an empty string
				$create_wpbb_user_signature_meta = add_user_meta($user->ID, 'wpbb_signature', "", true);
				if ($create_wpbb_user_signature_meta === false) {
					$create_wpbb_user_signature_meta_failed = __('Failed to create the WPBB wpbb_signature user meta data for '.$user->ID.' on '.__LINE__.' in '.__FILE__, 'wpbb');
					error_log($create_wpbb_user_signature_meta_failed);
				}
			}
		}
	}
}

/**
 * Retrieves array of roles used in Wordpress
 * 
 * This function grabs a list of all roles that have been created (by a plugin) within Wordpress.
 *
 * @since 1.0.0
 *
 * @param    string|array   $return    Returns array or string of all WP roles in lowercase
 * @param	 bool			$setup	   True if this function is being called apon setup (default NULL)
 * @return   string|array 	$_roles	   An array or string containing all WP roles
 *									
 */

function wpbb_admin_get_all_roles($return = 'array', $setup = NULL) {
	$wp_roles = new WP_Roles();
	$options = get_option('wpbb_options');
	if (isset($options['allow_guests']) && $options['allow_guests'] == 'yes') {
		if (!$wp_roles->get_role('guest')) {
			$guest_role = $wp_roles->add_role('guest', 'Guest', false);
		}
	}
 	$roles = $wp_roles->get_names();
 	$_roles = array();
 	foreach ($roles as $role) {
 		if ($setup) {
 			$_roles[] = strtolower("'".$role."'");
 		} else {
 			$_roles[] = strtolower($role);
 		}
 	}
 	if ($return == 'string') {	
 		return implode(',', $_roles);
 	} else {
 		return $_roles;
 	}
}

/**
 * Updates the current users wpbb_lastvisit meta key value
 * 
 * When the user logs out, their wpbb_lastvisit meta key is updated with the current timestamp.
 *
 * @since 1.0.0
 *									
 */

function wpbb_user_lastvisit() {
	$user_id = get_current_user_id();
	$date = date("Y-m-d H:i:s");
	$update_user_lastvisit = update_user_meta($user_id, 'wpbb_lastvisit', $date);
}

/**
 * Updates the current users wpbb_currentvisit meta key value
 * 
 * When a user logs in, their wpbb_currentvisit meta key is updated with the current timestamp.
 *
 * @since 1.0.0
 *
 * @param    string   		$user_login 	The user_login property of a WP_User object (the users username)
 * @param	 object			$user   		A WP_User object
 *								
 */

function wpbb_user_currentvisit($user_login = NULL, $user) {
	/**
	 * @todo ... Replace WP_User object with user_id (int)
	 */
	$date = date("Y-m-d H:i:s");
	$update_user_currentvisit = update_user_meta($user->ID, 'wpbb_currentvisit', $date);
}

/**
 * Retrieves a count of total topics and posts from a subforum and/or forum
 * 
 * This function will retrieve a count of the total topics in a subforum and/or forum and if a topic has posts those posts are included in the count too
 *
 * @since 1.0.0
 *
 * @param    int   		$id    	   The ID of the subforum or forum
 * @return   array 	 	$_roles	   An array containing a count of the total topics and posts within a subforum and/or forum
 *						
 */
 
function wpbb_admin_get_topics_posts($id)
{
	global $wpdb;
	$total_topics = $wpdb->get_var("SELECT COUNT(*) as 'Topics' FROM ".TOPICS_TABLE." WHERE forum = $id OR subforum = $id;");
	
	$topics = $wpdb->get_results("SELECT id FROM ".TOPICS_TABLE." WHERE subforum = $id or forum = $id;");
	
	$total_posts = 0;
	
	foreach ($topics as $topic) {
	
		$posts = $wpdb->get_var("SELECT COUNT(*) as 'Posts' FROM ".POSTS_TABLE." WHERE topic = $topic->id;");
		
		$total_posts += $posts;
	
	}
	
	if (!$total_topics) $total_topics = 0;
	if (!$total_posts) $total_posts = 0;
	
	return array('topics' => $total_topics, 'posts' => $total_posts);
}

/**
 * Retrieves a link to a forum or subforum on the WPBB overview page
 * 
 *
 * @since 1.0.0
 *
 * @param    int   			$id    	   The ID of the subforum or forum
 * @param 	 string			$which	   'forum' if referring to a forum, 'subforum' otherwise
 * @return   string|bool 	   		   False if unknown argument provided
 *						
 */
function wpbb_get_forum_link($id, $which = 'forum') {
	global $wpdb;
	$post_meta = $wpdb->prefix.'postmeta';
	$post_table = $wpdb->prefix.'posts';
	$page_id = $wpdb->get_var("SELECT post_id FROM $post_meta WHERE meta_key = '_wp_page_template' AND meta_value = 'wpbb-template.php';");
	//$page_guid = $wpdb->get_var("SELECT guid FROM $post_table WHERE ID = $page_meta;");
	$permalink = get_permalink($page_id);
	if ($which == 'forum') {
		$forum_link = $permalink;
		$forum_link = add_query_arg(array('forum' => $id, 'current_page' => 1), $forum_link);
		return $forum_link;
	} else if ($which == 'subforum') {
		$subforum_subforum = $wpdb->get_var("SELECT subforum FROM ".CATEGORY_TABLE." WHERE id = $id;");
		$forum_id = $wpdb->get_var("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $subforum_subforum;");
		$forum_link = $permalink;
		$forum_link = add_query_arg(array('forum' => $forum_id, 'subforum' => $id, 'current_page' => 1), $forum_link);
		return $forum_link;
	} else {
		return false;
	}
}

/**
 * Simple function that prints an error message and then terminates the script
 * 
 *
 * @since 1.0.3
 *
 * @param    string		$text		The error message to display
 *						
 */
function wpbb_admin_error($text)
{
	?>
	<div id="message" class="error">
		<?php printf(__('%s', 'wp-bb'), $text); ?>
	</div>
	<?php
}

/**
 * Simple function that prints a success message and then terminates the script
 * 
 *
 * @since 1.0.3
 *
 * @param    string		$text		The success message to display
 *						
 */
function wpbb_admin_success($text)
{
	?>
	<div id="message" class="updated">
		<?php printf(__('%s', 'wp-bb'), $text); ?>
	</div>
	<?php
}

/**
 * Simple function that alters the table structure with a new set of roles
 * 
 *
 * @since 1.0.3
 *
 * @param	  bool	$msgs	True if you want to echo error/success messages 
 * @return    bool			False if errors true otherwise
 *						
 */
function wpbb_refresh_roles($msgs = false) {
	global $wpdb;
	$wp_roles = wpbb_admin_get_all_roles('string', true);
	$permissions = array('view', 'read', 'post', 'reply', 'edit', 'lock', 'delete', 'sticky');
	$errors = 0;
	foreach ($permissions as $permission) {
		$update_table_structure = $wpdb->query("ALTER TABLE ".CATEGORY_TABLE." MODIFY  `$permission` SET($wp_roles);");
		if ($update_table_structure === false) {
			$errors++;
		}
	}
	if ($msgs) {
		if ($errors) {
			wpbb_admin_error('Failed to refresh roles, please try again or make sure you have made changes to the roles');
		} else {
			wpbb_admin_success('Refreshed roles successfully.');
		}
	}
}

/**
 * Removes any trace of the old page and recreates it
 *
 * @since 1.0.5
 *
 *						
 */

function wpbb_admin_recreate_page()
{
	global $wpdb;
	$errors = array();
	$post_meta = $wpdb->prefix.'postmeta';
	$post_id = $wpdb->get_var("SELECT post_id FROM $post_meta WHERE meta_key = '_wp_page_template' AND meta_value = 'wpbb-template.php';");
	if ($post_id != NULL)
	{
		$post_table = $wpdb->prefix.'posts';
		// Post may still exist e.g. if in trash
		$remove_from_post_table = $wpdb->query("DELETE FROM $post_table WHERE ID = $post_id;");
		if ($remove_from_post_table === false)
		{
			error_log(__('Received a MySQL error: Recreating the forum page, failed to delete the post from the post table', 'wp-bb'));
		}
		// Remove the post's metadata
		$remove_from_postmeta_table = $wpdb->query("DELETE FROM $post_meta WHERE meta_key = '_wp_page_template' AND meta_value = 'wpbb-template.php';");
		if ($remove_from_postmeta_table === false)
		{
			error_log(__('Received a MySQL error: Recreating the forum page, failed to remove the post\'s meta from the postmeta table'));
		}
	}
	// Create the page
	$wpbb_page_title = __('Forums', 'wp-bb');
	$link = site_url().'/'.strtolower($wpbb_page_title);
	$page = array(
		'post_status' => 'publish',
		'post_title' => $wpbb_page_title,
  		'comment_status' => 'closed',
  		'post_type' => 'page'
	);
	global $forum_page;
	$forum_page = wp_insert_post($page, false);
	if ($forum_page != 0 && !is_wp_error($forum_page))
	{
		// Add the post's metadata
		$add_postmeta = add_post_meta($forum_page, '_wp_page_template', 'wpbb-template.php', true);
		if ($add_postmeta)
		{
			wpbb_admin_success('Page was recreated successfully.');
		}
		else
		{
			$errors[] = 'Failed to add the post\'s postmeta to the postmeta table';
		}
	}
	else
	{
		$errors[] = 'Failed to insert the post into the post table';
	}
	if ($errors)
	{
		foreach ($errors as $error)
		{
			wpbb_admin_error($error);
		}
	}
}

/**
 * [Admin Version] Updates a users wpbb_posts depending on second argument given
 * 
 * This function will update a users wpbb_posts meta key value when they create a new topic / post and can also be used to decrease this value
 * if 'decrease' is given as the second argument e.g. when their topic or post is deleted etc.
 *
 * @since 1.1.0
 *
 * @param    int   		$user_id	The current users ID 
 * @param	 string		$type		'increase' to increase the value or 'decrease' to decrease the value (wpbb_posts meta key)
 *						
 */
function wpbb_admin_update_user_meta($user_id, $type = 'increase')
{
	if ($type == 'increase')
	{
		// Get the user meta value for wpbb_posts so we know how many posts they have
		$old_value = get_user_meta($user_id, 'wpbb_posts', true);
		$new_value = $old_value + 1;
		$add_user_meta = update_user_meta($user_id, 'wpbb_posts', $new_value);
		if ($add_user_meta === false)
		{
			_e('Error: Failed to increase/update the users wpbb_posts user meta data', 'wp-bb');
		}
	}
	else if ($type == 'decrease')
	{
		// Get the user meta value for wpbb_posts so we know how many posts they have
		$old_value = get_user_meta($user_id, 'wpbb_posts', true);
		$new_value = $old_value - 1;
		$add_user_meta = update_user_meta($user_id, 'wpbb_posts', $new_value);
		if ($add_user_meta === false)
		{
			_e('Error: Failed to decrease/update the users wpbb_posts user meta data', 'wp-bb');
		}
	}
}


// This will possibly be implemented in v1.0.1

/*
	Widget Related
*/

/*function wpbb_dashboard_widget() {
	$my_wpbb_stats = __('My WPBB Stats', 'wp-bb'); 
	wp_add_dashboard_widget('wpbb_widget', $my_wpbb_stats, 'wpbb_display_dashboard_widget');
}

function wpbb_display_dashboard_widget() {
	?>
	<table class="widefat">
			<tr row="top">
			</tr>
		</table>
	<?php
}*/

?>