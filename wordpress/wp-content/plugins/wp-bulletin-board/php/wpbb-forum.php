<?php

/*
	Checks if the plugin is deactivated
*/

// Since we're not in the admin area we must require the plugin.php file
require_once(ABSPATH.'wp-admin/includes/plugin.php');

// Check if the plugin is active
$plugin_active = is_plugin_active('wp-bulletin-board/wp-bb.php'); // Since the directory and filename shouldn't change

// If plugin is not active display error message
if ($plugin_active == false) {
	// This is the only exception
	?>
	<div style="text-align:center;font-weight:bold;color:red;">
		<?php
			_e('The plugin is deactivated. Please reactivate the plugin to continue using WPBB or delete this page', 'wp-bb');
			wpbb_exit();
		?>
	</div>
	<?php
}
	
// Load WPBB Options
$wpbb_options = get_option('wpbb_options');

// Load WPBB Facebook Options
$wpbb_facebook_options = get_option('wpbb_facebook_options');

$wpbb_theme_options = get_option('wpbb_theme_options');


// Sets the default timezone used by all date/time functions in a script depending on server configuration option
if (ini_get('date.timezone.'))
{
	$timezone = ini_get('date.timezone');
	date_default_timezone_set($timezone);
}

global $wpdb;

$wpdb->show_errors();

define('CATEGORIES_TABLE', $wpdb->prefix.'wpbb_categories');
define('TOPIC_TABLE', $wpdb->prefix.'wpbb_topics');
define('POST_TABLE', $wpdb->prefix.'wpbb_posts');
define('MESSAGES_TABLE', $wpdb->prefix.'wpbb_messages');
define('UN_READ_TABLE', $wpdb->prefix.'wpbb_topics_unread');

// The WP-BB plugin url on wordpress - only used if show footer option is set to yes
define('WPBB_URL', 'http://wordpress.org/extend/plugins/wp-bulletin-board/');


function wpbb_exit()
{
	get_header();
	get_footer();
	exit();
}

// Guests cannot access the forum if allow_guests is set to no
if (($wpbb_options['allow_guests'] == 'no')
	&& (!is_user_logged_in())
	&& (!isset($_GET['method']))
	&& (!isset($_GET['state'])))
	{
	if ($wpbb_facebook_options['allow_facebook'] == 'yes')
	{
		require_once('facebook/wpbb-facebook.php');
	}
	require_once('wpbb-functions.php');
	?>
	<div class="wpbb-centered-bold">
		<?php 
			wpbb_is_user_logged_in(); 
		?>
	</div>
	<?php
}

if ($wpbb_options['maintenance_mode'] == 'on')
{
	if (current_user_can('manage_options'))
	{
		?>
		<div class="wpbb-message-warning">
		<?php _e('The board is currently in maintenance mode. To turn it off change Maintenance Mode in Settings -> WPBB Settings to "Off"', 'wp-bb'); ?>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="wpbb-message-warning">
			<?php echo $wpbb_options['maintenance_message']; ?>
		</div>
		<?php
		if ($wpbb_facebook_options['allow_facebook'] == 'yes')
		{
			require_once('facebook/wpbb-facebook.php');
		}
		require_once('wpbb-functions.php');
		wpbb_footer();
		wpbb_exit();
	}
}

/* 
	Loads all functions 
*/

require_once('wpbb-functions.php');

/*
	Checks a page supplied exists
*/

wpbb_page_exists();

/*
	Require all Facebook functionality
*/

if ($wpbb_facebook_options['allow_facebook'] == 'yes')
{
	require_once('facebook/wpbb-facebook.php');
}


/*
	Twitter
*/

$wpbb_twitter_options = get_option('wpbb_twitter_options');

if ($wpbb_twitter_options['allow_twitter'] == 'yes')
{
	require_once('twitter/wpbb-twitter.php');
}



$permalink_options = get_option('permalink_structure');

/*
	Using default permalink settings
*/


if (empty($permalink_options))
{ 
	if (count($_GET) == 1)
	{
		require_once('wpbb-index.php');
	}

	/*
		Viewing Inside a Forum
	*/
	if ((isset($_GET['forum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['current_page'])) 
		&& (count($_GET) == 3)) {
		require_once('wpbb-view-forum.php');
	}
	

	/*
		Viewing A Forum Topic
	*/
		
	if ((isset($_GET['forum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['topic']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 4)) {
		require_once('wpbb-view-forum-topic.php');
	}

	/*
		Create Topic (forum & subforum)
	*/

	if ((isset($_GET['create']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['forum']) && isset($_GET['subforum']))
		&& (count($_GET) == 4)) {
		require_once('wpbb-create-topic.php');
	}

	if ((isset($_GET['create']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['forum']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-create-topic.php');
	}

	/*
		Viewing Inside A Subforum
	*/

	if ((isset($_GET['forum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['subforum']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 4)) {
		require_once('wpbb-view-subforum.php');
	}


	/*
		Viewing A Subforum Topic
	*/

	if ((isset($_GET['forum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['subforum']))
		&& (isset($_GET['topic']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 5)) {
		require_once('wpbb-view-subforum-topic.php');
	}

	/*
		Viewing a Profile
	*/

	if ((isset($_GET['profile']))
		&& (isset($_GET['page_id']))
		&& (count($_GET) == 2)) {
		require_once('wpbb-view-profile.php');
	}

	/* 
		Viewing All Messages
	*/

	if ((isset($_GET['messages']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-all-messages.php');
	}
	
	/* 
		Deleting a message
	*/
	
	if ((isset($_GET['messages']) && $_GET['messages'] == 'all')
		&& (isset($_GET['delete_msg']))
		&& (isset($_GET['page_id']))
		&& (count($_GET) > 1 && count($_GET) <= 3)) {
		require_once('wpbb-delete-message.php');
	}

	/*
		Viewing Individual Message
	*/

	if ((isset($_GET['messages']))
		&& (isset($_GET['page_id']))
		&& ($_GET['messages'] == 'all')
		&& ((isset($_GET['view'])) && ($_GET['view']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-view-message.php');
	}

	/* 
		Messaging a User
	*/

	if ((isset($_GET['message']))
		&& (isset($_GET['page_id']))
		&& (count($_GET) == 2)) {
		require_once('wpbb-message-user.php');
	}

	/*
		Edit Request
	*/

	if ((isset($_GET['forum']) || isset($_GET['subforum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['topic']) || isset($_GET['post']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'edit') 
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-edit-topic.php');
	}

	/*
		Lock Request
	*/

	if ((isset($_GET['forum']) || isset($_GET['subforum']))
		&& (isset($_GET['page_id']))	
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'lock') 
		&& ($_GET['action'] != 'edit') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-lock-topic.php');
	}

	/*
		Sticky Request
	*/

	if ((isset($_GET['forum']) || isset($_GET['subforum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'sticky') 
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'edit')))) {
		require_once('wpbb-sticky-topic.php');
	}

	/*
		Delete Request
	*/

	if ((isset($_GET['forum'])  || isset($_GET['subforum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['topic']) || isset($_GET['post']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'delete') 
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'edit')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-delete-topic.php');
	}

	/*
		Mark Read Request
	*/

	if ((isset($_GET['forum'])  || isset($_GET['subforum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'markread') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'edit')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-markread-topic.php');
	}

	/* 
		Reply Request 
	*/

	if ((isset($_GET['forum'])  || isset($_GET['subforum']))
		&& (isset($_GET['page_id']))
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action'])
		&& ($_GET['action'] == 'reply')
		&& ($_GET['action'] != 'markread')
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'lock')
		&& ($_GET['action'] != 'edit')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-reply-topic.php');
	}

	/*
		Unread topics
	*/

	if ((isset($_GET['unread_topics']))
		&& (isset($_GET['page_id']))
		&& ($_GET['unread_topics'] == 'all')
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-unread-topics.php');
	}

	/*
		Unanswered Topics
	*/

	if ((isset($_GET['unanswered_topics']))
		&& (isset($_GET['page_id']))
		&& ($_GET['unanswered_topics'] == 'all')
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-unanswered-topics.php');
	}
	
	// Not default or custom structure

} 
else
{
	if (count($_GET) == 0)
	{
		require_once('wpbb-index.php');
	}

	/*
		Viewing Inside a Forum
	*/
	
	if ((isset($_GET['forum']))
		&& (isset($_GET['current_page'])) 
		&& (count($_GET) == 2)) {
		require_once('wpbb-view-forum.php');
	}
	
	/*
		Viewing A Forum Topic
	*/
		
	if ((isset($_GET['forum']))
		&& (isset($_GET['topic']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-view-forum-topic.php');
	}


	/*
		Create Topic (forum & subforum)
	*/

	if (isset($_GET['create'])
		&& (isset($_GET['forum']) && isset($_GET['subforum']) || isset($_GET['forum']) && !isset($_GET['subforum']))
		&& (count($_GET) == 2 || count($_GET) == 3)) {
			require_once('wpbb-create-topic.php');
	}

	/*
		Viewing Inside A Subforum
	*/

	if ((isset($_GET['forum']))
		&& (isset($_GET['subforum']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 3)) {
		require_once('wpbb-view-subforum.php');
	}


	/*
		Viewing A Subforum Topic
	*/

	if ((isset($_GET['forum']))
		&& (isset($_GET['subforum']))
		&& (isset($_GET['topic']))
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 4)) {
		require_once('wpbb-view-subforum-topic.php');
	}

	/*
		Viewing a Profile
	*/

	if ((isset($_GET['profile']))
		&& (count($_GET) == 1)) {
		require_once('wpbb-view-profile.php');
	}


	/* 
		Viewing All Messages
	*/

	if ((isset($_GET['messages'])) 
		&& ($_GET['messages'] == 'all')
		&& (isset($_GET['current_page']))
		&& (count($_GET) == 2 || count($_GET) == 3)) {
		require_once('wpbb-all-messages.php');
	}
	
	/* 
		Deleting a message
	*/
	
	if ((isset($_GET['messages']))
		&& ($_GET['messages'] == 'all')
		&& (isset($_GET['delete_msg']))
		&& (count($_GET) > 1 && count($_GET) <= 2)) {
		require_once('wpbb-delete-message.php');
	}
	/*
		Viewing Individual Message
	*/

	if ((isset($_GET['messages']))
		&& ($_GET['messages'] == 'all')
		&& ((isset($_GET['view']))
		&& (count($_GET) == 2))) {
		require_once('wpbb-view-message.php');
	}

	/* 
		Messaging a User
	*/

	if ((isset($_GET['message']))
		&& (count($_GET) == 1)) {
		require_once('wpbb-message-user.php');
	}

	/*
		Edit Request
	*/

	if ((isset($_GET['forum']) || isset($_GET['subforum']))
		&& (isset($_GET['topic']) || isset($_GET['post']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'edit') 
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-edit-topic.php');
	}

	/*
		Lock Request
	*/

	if ((isset($_GET['forum']) || isset($_GET['subforum']))	
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'lock') 
		&& ($_GET['action'] != 'edit') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-lock-topic.php');
	}

	/*
		Sticky Request
	*/

	if ((isset($_GET['forum']) || isset($_GET['subforum']))
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'sticky') 
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'edit')))) {
		require_once('wpbb-sticky-topic.php');
	}

	/*
		Delete Request
	*/

	if ((isset($_GET['forum'])  || isset($_GET['subforum']))
		&& (isset($_GET['topic']) || isset($_GET['post']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'delete') 
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'edit')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-delete-topic.php');
	}

	/*
		Mark Read Request
	*/

	if ((isset($_GET['forum'])  || isset($_GET['subforum']))
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action']) 
		&& ($_GET['action'] == 'markread')
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'lock') 
		&& ($_GET['action'] != 'edit')
		&& ($_GET['action'] != 'reply')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-markread-topic.php');
	}

	/* 
		Reply Request 
	*/

	if ((isset($_GET['forum'])  || isset($_GET['subforum']))
		&& (isset($_GET['topic']))
		&& ((isset($_GET['action'])
		&& ($_GET['action'] == 'reply')
		&& ($_GET['action'] != 'markread')
		&& ($_GET['action'] != 'delete')
		&& ($_GET['action'] != 'lock')
		&& ($_GET['action'] != 'edit')
		&& ($_GET['action'] != 'sticky')))) {
		require_once('wpbb-reply-topic.php');
	}

	/*
		Unread topics
	*/

	if ((isset($_GET['unread_topics']))
		&& ($_GET['unread_topics'] == 'all')
		&& (isset($_GET['current_page'])) 
		&& (count($_GET) == 2)) {
		require_once('wpbb-unread-topics.php');
	}

	/*
		Unanswered Topics
	*/

	if ((isset($_GET['unanswered_topics']))
		&& ($_GET['unanswered_topics'] == 'all')
		&& (isset($_GET['current_page'])) 
		&& (count($_GET) == 2)) {
		require_once('wpbb-unanswered-topics.php');
	}
}

wpbb_footer();

?>
