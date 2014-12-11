<?php
/*
Plugin Name: Bublaa Forums And Commenting
Plugin URI: http://bublaa.com/#!/bubble/about-bublaa
Description: Adds a Bublaa forum and comments to your WordPress site
Author: Bublaa Team
Version: 2.0
Author URI: http://bublaa.com/#!/bubble/about-bublaa
*/

include_once dirname(__FILE__) . '/functions.php';

// activation
register_activation_hook(__FILE__, array($bublaa, 'activate'));
register_deactivation_hook(__FILE__, array($bublaa, 'deactivate'));

// admin
if ( is_admin() ) {
	add_action('admin_init', array($bublaa, 'admin_notice'));
    add_action('admin_menu', array($bublaa, 'menu'));
	$plugin = plugin_basename(__FILE__);
	add_filter( "plugin_action_links_$plugin", array($bublaa, 'add_settings_link') );
}

// forum
function init_bublaa() {
	global $bublaa;
    $bublaa->init_embedded();
}
add_action('template_redirect', array($bublaa, 'load_template'));
add_action( 'wp_ajax_update-bublaa-bubble', array($bublaa, 'update_bubble_ajax'));

// comments
function init_bublaa_comments() {
	global $bublaa;
	$options = $bublaa->get_options();
	if($options && isset($options["comments_enabled"]) && $options["comments_enabled"]) {
		global $bublaaComments;
		add_filter('comments_number', array($bublaaComments, 'comments_number'));
		add_filter('comments_template', array($bublaaComments, 'load_comments_template'));
		add_action('wp_enqueue_scripts', array($bublaaComments, 'load_scripts'));
	}
}
add_action('template_redirect', 'init_bublaa_comments');

// Activity
function init_bublaa_widgets() {
	if(in_array('bublaa-embeddable-forums/bublaa.php', get_option( 'active_plugins' )))
		register_widget( 'BublaaWidget' );
}
add_action( 'widgets_init', 'init_bublaa_widgets' );

?>
