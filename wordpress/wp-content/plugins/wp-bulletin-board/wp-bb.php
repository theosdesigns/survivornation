<?php

/*
	Plugin Name: WPBB
	Plugin URI: http://wordpress.org/extend/plugins/wp-bulletin-board/
	Description: An easy to use Bulletin board for Wordpress with Facebook and Twitter integration.
	Version: 1.1.4
	Author: Jay Carter
	Author URI: http://codebycarter.com
	License: GPLv2 or later
*/

/*  Copyright 2012  Jay Carter  (email : me@codebycarter.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $wpdb;

// Sets the default timezone used by all date/time functions in a script depending on server configuration option
if (ini_get('date.timezone.'))
{
	$timezone = ini_get('date.timezone');
	date_default_timezone_set($timezone);
}

// Define wpbb plugin directory
if (!defined('WPBB_DIR'))
{
	define('WPBB_DIR', plugin_dir_path(__FILE__));
}
// Contains categories, forums and subforums
if (!defined('CATEGORY_TABLE'))
{
	define('CATEGORY_TABLE', $wpdb->prefix.'wpbb_categories');
}
// Contains all topics whether in a category, forum or subforum
if (!defined('TOPICS_TABLE'))
{
	define('TOPICS_TABLE', $wpdb->prefix.'wpbb_topics');
}
// Contains all posts inside a specific topic
if (!defined('POSTS_TABLE'))
{
	define('POSTS_TABLE', $wpdb->prefix.'wpbb_posts');
}
// Contains all user messages
if (!defined('MESSAGE_TABLE'))
{
	define('MESSAGE_TABLE', $wpdb->prefix.'wpbb_messages');
}
// Contains all topics for each user and whether they are read or not
if (!defined('UNREAD_TABLE'))
{
	define('UNREAD_TABLE', $wpdb->prefix.'wpbb_topics_unread');
}
// Define current version
if (!defined('WPBB_VERSION'))
{
	define('WPBB_VERSION', 'v1.1.1');
}
// Define plugin url (wp plugin page)
if (!defined('WPBB_PLUGIN_URL'))
{
	define('WPBB_PLUGIN_URL', 'http://wordpress.org/support/plugin/wp-bulletin-board');
}

/*
	Internationalisation and localisation
*/

add_action('plugins_loaded', 'wpbb_load_language');

if (!function_exists('wpbb_load_language'))
{
	function wpbb_load_language()
	{
		load_plugin_textdomain('wp-bb', false, dirname(plugin_basename(__FILE__)).'/languages/');
	}
}

/*
	Hooks
*/

register_activation_hook(__FILE__, 'wpbb_activate');
register_uninstall_hook(__FILE__, 'wpbb_uninstall');

/*
	Actions
*/

add_action('admin_menu', 'wpbb_admin_pages');
add_action('wp_login', 'wpbb_user_currentvisit', 10, 2);
add_action('wp_logout', 'wpbb_user_lastvisit');
add_action('switch_theme', 'wpbb_create_template_file');
add_action('wp_enqueue_scripts', 'wpbb_register_styles');

$options = get_option('wpbb_options');
if ($options['post_to_forum'] == 'yes')
{
	add_action('add_meta_boxes', 'wpbb_create_metaboxes'); // Metaboxes
	add_action('save_post', 'wpbb_post_saved'); // Wordpress post saved
}

/*
	All WPBB Admin (Non Page or DB related) Functions
	
	Also Contains Widget Related Functions
*/

require_once('php/admin/wpbb-admin-functions.php');

/*
	Runs when plugin is activated
*/

if (!function_exists('wpbb_activate'))
{
	function wpbb_activate()
	{
		// Create all neccessary database tables
		wpbb_create_db_table();
		// Create options in DB for our settings
		wpbb_create_options();
		// Create the user meta for all users currently registered
		wpbb_setup_user_meta();
		// Creates the Forum page
		wpbb_create_page();
	}
}

/*
	Runs when a plugin is deleted, not deactivated
*/

if (!function_exists('wpbb_uninstall'))
{
	function wpbb_uninstall()
	{
		global $wpdb;
		delete_option('wpbb_options');
		delete_option('wpbb_theme_options');
		delete_option('wpbb_facebook_options');
		delete_option('wpbb_twitter_options');
		delete_option('wpbb_guest_options');
		wpbb_delete_user_meta();
		wpbb_delete_post_meta();
		$wpdb->query("DROP TABLE ".CATEGORY_TABLE.", ".TOPICS_TABLE.", ".POSTS_TABLE.", ".MESSAGE_TABLE.", ".UNREAD_TABLE.";");
	}
}

if (!function_exists('wpbb_delete_user_meta'))
{
	function wpbb_delete_user_meta()
	{
		global $wpdb;
		$delete_user_meta = $wpdb->query("DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key LIKE 'wpbb_%';");
	}
}

if (!function_exists('wpbb_delete_post_meta'))
{
	function wpbb_delete_post_meta()
	{
		global $wpdb;
		$post_meta = $wpdb->prefix.'postmeta';
		$key = '_wp_page_template';
		$value = 'wpbb-template.php';
		$post_id = $wpdb->get_var("SELECT post_id FROM $post_meta WHERE meta_key = '$key' AND meta_value = '$value';");
		// Delete the meta data
		delete_post_meta($post_id, $key, $value);
		// Delete the page
		wp_delete_post($post_id, true);
	}
}

/*
	Setup WPBB database tables
*/

if (!function_exists('wpbb_create_db_table'))
{
	function wpbb_create_db_table()
	{
		global $wpdb;
		$options = get_option('wpbb_options');
		if ($options)
		{
			wpbb_refresh_roles();
		}
		$roles = wpbb_admin_get_all_roles('string', true);
		$perms = "SET($roles) NOT NULL";
		$create_category_query = "CREATE TABLE ".CATEGORY_TABLE." (
  			`id` TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  			`name` VARCHAR(30) NOT NULL,
  			`description` VARCHAR(200) NOT NULL,
  			`forum` TINYINT(1) NOT NULL,
  			`subforum` TINYINT(1) NOT NULL,
  			`view` $perms,
  			`read` $perms,
  			`post` $perms,
  			`reply` $perms,
  			`edit` $perms,
  			`lock` $perms,
  			`delete` $perms,
  			`sticky` $perms,
  			`order` TINYINT(1) UNSIGNED NOT NULL,
  			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
  		";
  	
 		$create_topic_query = "CREATE TABLE ".TOPICS_TABLE." (
  			`id` INT(1) NOT NULL AUTO_INCREMENT,
  			`author` SMALLINT(1) NOT NULL,
  			`name` VARCHAR(35) NOT NULL,
  			`content` TEXT NULL,
  			`forum` TINYINT(1) NOT NULL,
  			`subforum` TINYINT(1) NOT NULL,
  			`status` SET('locked', 'sticky') NOT NULL,
  			`created` DATETIME NOT NULL,
  			`last_reply` DATETIME NOT NULL,
  			`read` TINYINT(1) NOT NULL DEFAULT '0',
  			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
  		";
  		$create_post_query = "CREATE TABLE ".POSTS_TABLE." (
  			`id` INT(1) NOT NULL AUTO_INCREMENT,
  			`author` SMALLINT(1) NOT NULL,
  			`topic` INT(1) NOT NULL,
  			`text` TEXT NOT NULL,
  			`created` TIMESTAMP NOT NULL,
  			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
  		";
  	
  		$create_messages_query = "CREATE TABLE ".MESSAGE_TABLE." (
  			`id` INT(1) NOT NULL AUTO_INCREMENT,
  			`to` INT(1) NOT NULL,
  			`from` INT(1) NOT NULL,
  			`subject` TINYTEXT NOT NULL,
  			`content` TEXT NOT NULL,
  			`read` TINYINT(1) NOT NULL,
  			`sent` DATETIME NOT NULL,
  			PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		";
	
		$create_topic_unread_query = "CREATE TABLE ".UNREAD_TABLE." (
			`id` INT(1) NOT NULL,
  			`author` INT(1) NOT NULL,
			`read` INT(1) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		";
	  
  		$create_category_table = $wpdb->query($create_category_query);
 		$create_topic_table = $wpdb->query($create_topic_query);
 		$create_post_table = $wpdb->query($create_post_query);
 		$create_message_table = $wpdb->query($create_messages_query);
 		$create_topic_unread_table = $wpdb->query($create_topic_unread_query);
 	
 		if ($create_category_table === false)
 		{
 			$create_category_table_err = __('There was an error creating the table wpbb_categorie', 'wp-bb');
			error_log($create_category_table_err);
 		} 
 		if ($create_topic_table === false)
 		{
 			$create_topic_table_err = __('There was an error creating the table wpbb_topics', 'wp-bb');
 			error_log($create_topic_table_err);
 		}
 		if ($create_post_table === false)
 		{
 			$create_post_table_err = __('There was an error creating the table wpbb_posts', 'wp-bb');
			error_log($create_post_table_err);
 		} 
 		if ($create_message_table === false)
 		{
 			$create_message_table_err = __('There was an error creating the table wpbb_messages', 'wp-bb');
 			error_log($create_message_table_err);
 		}
 		if ($create_topic_unread_table === false)
 		{
 			$create_topic_unread_table_err = __('There was an error creating the table wpbb_topics_unread', 'wp-bb');
 			error_log($create_topic_unread_table_err);
 		}
	}
}

/*
	Create WPBB database options with default values
*/

if (!function_exists('wpbb_create_options'))
{
	function wpbb_create_options()
	{
		// Retrieve options from database
		$old_options = get_option('wpbb_options');
		/*
		* 	Add all current roles to an array with a default value of 'yes'
		*	This is for our role permissions
		*/
		$roles = wpbb_admin_get_all_roles();
		$role_array = array();
		if ($roles)
		{
			foreach ($roles as $role)
			{
				$role_array[$role] = 'yes';
			}
		}
		// If options do not exist
		if (!$old_options)
		{
			$maintenance_message = __('This is the message that will be displayed to users when you disable the board. Edit this in your settings.', 'wp-bb');
			$forum_name = __('My WPBB Forum', 'wp-bb');
			$options = array(
				'maintenance_mode' => 'off',
				'maintenance_message' => $maintenance_message,
				'forum_name' => $forum_name,
				'allow_guests' => 'yes',
				'allow_subforums' => 'yes',
				'enable_quick_reply' => 'yes',
				'topics_per_page' => 20,
				'posts_per_page' => 20,
				'topic_cutoff' => 15,
				'post_cutoff' => 15,
				'post_to_forum' => 'yes',
				'show_footer' => 'no',
				'role_permissions' => $role_array,
				'version' => WPBB_VERSION
			);
			$add_wpbb_options = add_option('wpbb_options', $options);
		}
		else
		{
			if (!isset($old_options['version']))
			{
				$options = array(
					'maintenance_mode' => $old_options['maintenance_mode'],
					'maintenance_message' => $old_options['maintenance_message'],
					'forum_name' => $old_options['forum_name'],
					'allow_guests' => $old_options['allow_guests'],
					'allow_subforums' => $old_options['allow_subforums'],
					'enable_quick_reply' => $old_options['enable_quick_reply'],
					'topics_per_page' => $old_options['topics_per_page'],
					'posts_per_page' => $old_options['posts_per_page'],
					'topic_cutoff' => $old_options['topic_cutoff'],
					'post_cutoff' => $old_options['post_cutoff'],
					'post_to_forum' => $old_options['post_to_forum'],
					'show_footer' => $old_options['show_footer'],
					'role_permissions' => $role_array,
					'version' => WPBB_VERSION
				);
				update_option('wpbb_options', $options);
			}
		}
		/* Theme Options */
		$old_theme_options = get_option('wpbb_theme_options');
		if (!$old_theme_options)
		{
			$add_wpbb_theme_options = add_option('wpbb_theme_options', array('theme' => 'light'));
		}
		/* Facebook Options */
		$old_facebook_options = get_option('wpbb_facebook_options');
		if (!$old_facebook_options) {
			$facebook_app_id = __('Enter your Facebook App ID / API Key', 'wp-bb');
			$facebook_app_secret_key = __('Enter your App Secret Key', 'wp-bb');
			$facebook_redirect_uri = __('Enter your URL (Redirect URL)', 'wp-bb');
			$facebook_state = __('Enter a long random string', 'wp-bb');
			$facebook_role = __('Enter the default role for new facebook registrations');
			$facebook_options = array(
				'allow_facebook' => 'no',
				'facebook_app_id' => $facebook_app_id,
				'facebook_app_secret_key' => $facebook_app_secret_key,
				'facebook_redirect_uri' => $facebook_redirect_uri,
				'facebook_state' => $facebook_state,
				'facebook_default_role' => $facebook_role
			);
			$add_wpbb_facebook_options = add_option('wpbb_facebook_options', $facebook_options);
		}
		/* Twitter Options */
		$old_twitter_options = get_option('wpbb_twitter_options');
		if (!$old_twitter_options)
		{
			$twitter_account = __('Enter your website/personal Twitter Account', 'wp-bb');
			$twitter_options = array(
				'allow_twitter' => 'no',
				'twitter_account' => $twitter_account
			);
			$add_wpbb_twitter_options = add_option('wpbb_twitter_options', $twitter_options);
		}
		$old_guest_options = get_option('wpbb_guest_options');
		if (!$old_guest_options)
		{
			$guest_options = array(
				'guest_last_post' => '0000-00-00 00:00:00'
			);
			$add_wpbb_guest_options = add_option('wpbb_guest_options', $guest_options);
		}
	}
}

/*
	Create all WPBB admin pages
*/

if (!function_exists('wpbb_admin_pages'))
{
	function wpbb_admin_pages()
	{
		$create_category = __('Create Category', 'wp-bb');
		$create_forum = __('Create Forum', 'wp-bb');
		$create_subforum = __('Create Subforum', 'wp-bb');
		$create_topic = __('Create Topic', 'wp-bb');
		$role_permissions = __('Role Permissions', 'wp-bb');
		$create_tools = __('Tools', 'wp-bb');
		$faq_support = __('FAQ & Support', 'wp-bb');
		$settings = __('WPBB Settings', 'wp-bb');

		// Main WPBB page
		add_object_page('WPBB Overview', 'WPBB', 'manage_options', 'wpbb_admin', 'wpbb_admin_display', '');
	
		// Create/Edit Category
		add_submenu_page('wpbb_admin', 'WPBB Categories', $create_category, 'manage_options', 'wpbb_admin_categories', 'wpbb_admin_categories');
	
		// Create/Edit Forum
		add_submenu_page('wpbb_admin', 'WPBB Forums', $create_forum, 'manage_options', 'wpbb_admin_forums', 'wpbb_admin_forums');
	
		// Create/Edit Subforum
		add_submenu_page('wpbb_admin', 'WPBB Subforums', $create_subforum, 'manage_options', 'wpbb_admin_subforums', 'wpbb_admin_subforums');
	
		// Create/Edit Topics
		add_submenu_page('wpbb_admin', 'WPBB Topics', $create_topic, 'manage_options', 'wpbb_admin_topics', 'wpbb_admin_topics');
		
		// Role permissions
		add_submenu_page('wpbb_admin', 'Role Permissions', $role_permissions, 'manage_options', 'wpbb_admin_role_permissions', 'wpbb_admin_role_permissions');
	
		// Create Tools page
		add_submenu_page('wpbb_admin', 'WPBB Tools', $create_tools, 'manage_options', 'wpbb_admin_tools', 'wpbb_admin_tools');
	
		// FAQ / First Installation Page
		add_submenu_page('wpbb_admin', 'FAQ / Support', $faq_support, 'manage_options', 'wpbb_admin_help', 'wpbb_admin_help');
		
		// Shortcut to WPBB settings - some people don't know they exist
		add_submenu_page('wpbb_admin', 'WPBB Settings', $settings, 'manage_options', 'wpbb_settings_page', 'wpbb_display_settings');
	
		// WPBB Settings
		add_options_page('WPBB Settings', $settings, 'manage_options', 'wpbb_settings_page', 'wpbb_display_settings');
	}
}


if (!function_exists('wpbb_create_template_file'))
{
	function wpbb_create_template_file()
	{
		// Get current theme directory
		$template_directory = get_template_directory();
		// Check if required template file doesn't exist and if true create it
		if (!file_exists($template_directory.'/wpbb-template.php'))
		{
			if (!is_writable($template_directory))
			{
				$chmod = true;
				// Try to chmod folder
				chmod($template_directory, 0755);
			}
			$template_file_path = WPBB_DIR.'/wpbb-template.php';
			$move_template_file = copy($template_file_path, $template_directory.'/wpbb-template.php');
			if (!$move_template_file)
			{
				$move_template_file_failed = sprintf(__('Failed to move required wp-bb template file %s to theme directory %s. Please make sure both the file and directory exist or move the wp-bb template file to your current theme directory manually'), $template_file_path, $template_directory);
				error_log($move_template_file_failed);
			}
		}
	}
}

if (!function_exists('wpbb_create_page'))
{
	function wpbb_create_page()
	{
		// Check the page doesn't already exist (e.g. when reactivating)
		global $wpdb;
		$post_meta = $wpdb->prefix.'postmeta';
		$post_id = $wpdb->get_var("SELECT post_id FROM $post_meta WHERE meta_key = '_wp_page_template' AND meta_value = 'wpbb-template.php';");
		if ($post_id == NULL)
		{
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
			add_post_meta($forum_page, '_wp_page_template', 'wpbb-template.php', true);
		}
		// This function checks if the template file exists before doing anything
		$template = wpbb_create_template_file();
	}
}

/*
	Wordpress post metaboxes
*/

if (!function_exists('wpbb_post_saved'))
{
	function wpbb_post_saved($_post_id)
	{
		if (wp_is_post_revision($_post_id))
		{
			return;
		}
		static $first = true;
		if (!$first) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
  		{
  			return;
  		}
  		$post_metabox_nonce = isset($_POST['wpbb-post-metabox-nonce']);
  		if (!wp_verify_nonce($post_metabox_nonce, plugin_basename(__FILE__)))
  		{
  			return;
  		}
  		$_post_type = isset($_POST['post_type']);
  		if ('post' == $_post_type)
  		{
  			if (!current_user_can('edit_page', $_post_id))
  			{
  				return;
  			}
  		}
 		$post_id = absint($_post_id);
 		$post_data = get_post($post_id, ARRAY_A);
 		$post_to_forum_id = NULL;
 		if (isset($_POST['wpbb-post-to-forum']))
 		{
 			$post_to_forum_id = absint($_POST['wpbb-post-to-forum']);
 		}
 		if ($post_to_forum_id == NULL)
 		{
 			error_log(__('When copying the post to a WPBB forum, the wpbb-post-to-forum was NULL - a forum ID wasn\'t provided'));
 		}
 		$is_forum = NULL;
 		$is_subforum = NULL;
 		global $wpdb;
 		// Check to see if this forum is a forum or a subforum
 		$forum_or_subforum = $wpdb->get_results("SELECT `forum`, `subforum` FROM ".CATEGORY_TABLE." WHERE id = $post_to_forum_id;");
 		if ($forum_or_subforum)
 		{
 			foreach ($forum_or_subforum as $forum)
 			{
 				if ($forum->forum > 0 && $forum->subforum == 0)
 				{
 					// Forum
 					$is_forum = true;
 					$is_subforum = false;
 				}
 				else if ($forum->subforum > 0 && $forum->forum == 0)
 				{
 					// Subforum
 					$is_subforum = true;
 					$is_forum = false;
 				}
 			}
 		}
 		if ($post_data)
 		{
 			$forum_id = ($is_forum) ? $post_to_forum_id : 0;
 			$subforum_id = ($is_subforum) ? $post_to_forum_id : 0;
 			$data = array(
				'name' => $post_data['post_title'],
				'author' => $post_data['post_author'],
				'content' => $post_data['post_content'],
				'forum' => $forum_id,
				'subforum' => $subforum_id,
				'status' => '',
				'created' => $post_data['post_date'],
				'last_reply' => $post_data['post_date']
			);
			$copy_post_to_forum = $wpdb->insert(TOPICS_TABLE, $data);
			if ($copy_post_to_forum === false)
			{
				error_log(__('Failed to copy the wordpress post to the wpbb forum', 'wp-bb'));
			}
			else
			{
				// Update authors post count
				wpbb_admin_update_user_meta($post_data['post_author'], 'increase');
				$first = false;
			}
 		}
 	}
}

if (!function_exists('wpbb_display_metaboxes'))
{
	function wpbb_display_metaboxes()
	{
		global $wpdb;
		wp_nonce_field(plugin_basename(__FILE__), 'wpbb-post-metabox-nonce');
		?>
		<div id="minor-publishing-actions">
			<p class="description">Send a copy of this post to one of your WPBB forums or subforums or check "None" or disable this in your WPBB Settings</p>
			<select name="wpbb-post-to-forum">
				<!-- Default -->
				<option value="0">None</option>
				<?php
				// All forums and subforums
				$forums = $wpdb->get_results("SELECT `id`, `name` FROM ".CATEGORY_TABLE." WHERE forum > 0 || subforum > 0;");
				if ($forums)
				{
					foreach ($forums as $forum)
					{
						?>
						<option value="<?php echo $forum->id; ?>"><?php echo $forum->name;?></option>
						<?php
					}
				}
				?>
			</select>
		</div>
		<?php
	}
}

if (!function_exists('wpbb_create_metaboxes'))
{
	function wpbb_create_metaboxes()
	{
		add_meta_box('wpbb-post-to-forum-meta-box', __('Post to WPBB Forum', 'wp-bb'), 'wpbb_display_metaboxes', 'post', 'side', 'high', NULL);
	}
}

/*
	Register and loads the style and javascript files depending on settings
*/

if (!function_exists('wpbb_register_styles'))
{
	function wpbb_register_styles()
	{
		$wpbb_theme_options = get_option('wpbb_theme_options');
		if ($wpbb_theme_options['theme'] == 'light')
		{
			wp_register_style('wpbb-light-style', plugins_url('wp-bulletin-board/css/wpbb-light-style.css'));
			wp_enqueue_style('wpbb-light-style');
		}
		else
		{
			wp_register_style('wpbb-dark-style', plugins_url('wp-bulletin-board/css/wpbb-dark-style.css'));
			wp_enqueue_style('wpbb-dark-style');
		}
		wp_enqueue_script('jquery');
		wp_register_script('wpbb-search', plugins_url('wp-bulletin-board/js/wpbb-search.js'), 'jquery');
		wp_enqueue_script('wpbb-search');
	}
}

/*
	WPBB Overview - Displays all Categories, Forums and Subforums
	
	This is like viewing the forums except in the administration area where you can edit, change the order and delete any category, forum or subforum with ease
*/

if (!function_exists('wpbb_admin_display'))
{
	function wpbb_admin_display()
	{
		require_once('php/admin/wpbb-admin-main.php');
	}
}



/*
	Create & Edit Category page
*/

if (!function_exists('wpbb_admin_categories'))
{
	function wpbb_admin_categories()
	{
		require_once('php/admin/wpbb-admin-categories.php');
	}
}

/*
	Create & Edit Forum page
*/

if (!function_exists('wpbb_admin_forums'))
{
	function wpbb_admin_forums()
	{
		require_once('php/admin/wpbb-admin-forums.php');
	}
}

/*
	Create & Edit Subforum page
*/

if (!function_exists('wpbb_admin_subforums'))
{
	function wpbb_admin_subforums()
	{
		require_once('php/admin/wpbb-admin-subforums.php');
	}
}

/*
	Create & Edit Topics page
*/

if (!function_exists('wpbb_admin_topics'))
{
	function wpbb_admin_topics()
	{
		require_once('php/admin/wpbb-admin-topics.php');
	}
}

/* 
	Role Permissions
*/

if (!function_exists('wpbb_admin_role_permissions'))
{
	function wpbb_admin_role_permissions()
	{
		require_once('php/admin/wpbb-admin-role-permissions.php');
	}
}

/* 
	Tools page
*/

if (!function_exists('wpbb_admin_tools'))
{
	function wpbb_admin_tools()
	{
		require_once('php/admin/wpbb-admin-tools.php');
	}
}

/*
	FAQ / Support page
*/

if (!function_exists('wpbb_admin_help'))
{
	function wpbb_admin_help()
	{
		require_once('php/admin/wpbb-admin-faq-support.php');
	}
}

/*
	WPBB Settings page
*/

if (!function_exists('wpbb_display_settings'))
{
	function wpbb_display_settings()
	{
		require_once('php/admin/wpbb-admin-settings.php');
	}
}

?>
