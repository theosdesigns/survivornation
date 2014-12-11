<?php

class Bublaa {
    /**
     * Default plugin options
     * @return array
     */
    function defaults() {
        return array(
            "page_id" => null,
            "bubble" => "",
            "showFooter" => false,
            "comments_enabled" => true,
            "page_changed" => false
        );
    }

    /**
     * Inits all the plugin options and creates the default page for the plugin
     * @return void
     */
    function activate()
    {
        $options = array();

        // create a default page 'forum'
        $options["page_id"] = $this->create_default_page();
        $options["page_changed"] = true;

        add_option('bublaa-plugin-options', array_merge($this->defaults(), $options));
    }


    /**
     * @return void
     */
    function admin_notice(){
        $options = $this->get_options();
        if(!$options["page_changed"])
            return;
        $page = get_page($options['page_id']);
        if(is_null($page))
            return;

        echo '<div class="updated"><p>Bublaa created a new page for your forum: <a href="' . get_page_link($page->ID) . '">' . $page->post_title . '</a></p></div>';
        $options["page_changed"] = false;
        update_option('bublaa-plugin-options', $options);
    }

    /**
     * Removes all the saved plugin options and the page form database.
     * @return void
     */
    function deactivate()
    {
        $options = $this->get_options();
        if(isset($options["page_id"])) {
            wp_delete_post( $options["page_id"], true );
        }
        delete_option('bublaa-plugin-options');
    }


    /**
     * Adds bublaa to wordpress dashboard
     * @return void
     */
    function menu()
    {
        // Create menu tab
        add_menu_page('Bublaa Plugin Settings', 'Bublaa', 'edit_pages', "bublaa", array($this,'admin'), 'http://bublaa.com/favicon.ico');
    }

    /**
     * Loads the admin panel
     * @return void
     */
    function admin() {
        if (!current_user_can('edit_pages'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        include_once dirname(__FILE__) . '/admin.php';
    }

    /**
     * Creates a page for the forum.
     * @return int page_id
     */
    function create_default_page() {
        $possible_names = array('forum', 'bublaa forum', 'discussions', 'bublaa discussions', 'bublaa');
        $selected_name = 'bublaa page';
        foreach ($possible_names as $name) {
            if(is_null(get_page_by_title($name))) {
                $selected_name = $name;
                break;
            }
        }

        // Create post object
        $_p = array();
        $_p['post_title'] = $selected_name;
        $_p['post_content'] = "";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        return wp_insert_post( $_p );
    }

    /**
     * Prints the markup to initialize bublaa forum
     * @return void
     */
    function init_embedded (){
        $options = $this->get_options();

        $host = "http://bublaa.com";

        $notFound = "embeddedNotFound";
        $saveNewBubbleToWordpress = "false";
        if(current_user_can('edit_pages') && (!isset($options["bubble"]) || empty($options["bubble"]))) {
            // backbone route for wp admin to create a new bubble - NOT MOBILE OPTIMIZED
            $notFound = "embeddedCreateNew";
            // function to save bublaa plugin option 'bubble' via ajax
            $saveNewBubbleToWordpress = "
                function saveToWordpress (name) {
                    var request = new XMLHttpRequest(),
                        wpAdminUrl ='" . admin_url( 'admin-ajax.php' ) ."'
                        params = 'action=update-bublaa-bubble&name=' + name;

                    request.open('POST', wpAdminUrl);

                    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                    request.onreadystatechange = function() {
                        if(request.readyState == 4 && request.status == 200) {
                            // alert(request.responseText);
                        }
                    }

                    request.send(params);
                }
            ";
        }

        // final markup to init bublaa
        echo "
            <div id='bublaa'></div>
            <div id='wp_bublaa_form_container' style='display:none;'></div>
            <style type='text/css'>
                #bublaa
                {
                    width: 100%;
                }
            </style>
            <script type='text/javascript'>
                window.bublaa = {
                    config : {
                        bubble     : '" . $options["bubble"] ."',
                        noBubbleRoute : '". $notFound . "',
                        bubbleCreatedSuccess : " . $saveNewBubbleToWordpress . "
                    }
                };

                (function() {
                    var b = document.createElement('script'); b.type = 'text/javascript'; b.async = true;
                    b.src = '" . $host ."/dist/plugins.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(b, s);
                })();
            </script>
            <noscript>Your browser does not support JavaScript!</noscript>
        ";
    }

    /**
     * Loads the bublaa forum template if on the right page
     * @return void
     */
    function load_template() {
        global $post;
        if($post->post_type == 'page') {
            $options = $this->get_options();
            if($options["page_id"] == $post->ID) {
                include_once dirname(__FILE__) . '/bublaa-template.php';
                exit;
            }
        }
    }

    /**
     * Returns all the bublaa settings. Handles migrating from older plugin version incase some option fields have changed.
     * @return Array bublaa plugin settings
     */
    function get_options() {
        $changed = false;
        $options = get_option('bublaa-plugin-options');


        // do some migrating from older versions of the plugin
        if(isset($options["page_name"])) {
            $pages = get_pages();
            $mb = function_exists("mb_strtolower");

            if(!isset($options["page_id"])) {
                foreach ($pages as $page) {
                    if (
                            (
                                $mb && ( mb_strtolower($page->post_title) == mb_strtolower($options["page_name"]) || mb_strtolower($page->post_name) == mb_strtolower($options["page_name"]))
                            ) ||
                            (
                                strtolower($page->post_title) == strtolower($options["page_name"]) || strtolower($page->post_name) == strtolower($options["page_name"])
                            )
                        ) {
                        $options["page_id"] = $page->ID;
                        $changed = true;
                    }
                }
                if(!isset($options["page_id"]))
                    $options["page_id"] = $this->create_default_page();
            }
            unset($options["page_name"]);
        }

        $newOptions = array_merge($this->defaults(), $options);

        if($changed)
            update_option('bublaa-plugin-options', $newOptions);

        return $newOptions;
    }

    function update_bubble_ajax () {
        if (!current_user_can('edit_pages'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        if(!isset($_POST["name"]) || !is_string($_POST["name"]) || strlen($_POST["name"]) < 2) {
            wp_die( __('Invalid parameter: name') );
        }

        $options = $this->get_options();
        $options["bubble"] = $_POST["name"];

        update_option('bublaa-plugin-options', $options);

        // generate the response
        $response = json_encode( array( 'success' => true ) );

        // response output
        header( "Content-Type: application/json" );
        echo $response;

        exit;
    }

    /**
     * Saves the the posted data
     * @return Array errors
     */
    function update_options() {
        if (!current_user_can('edit_pages'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        $errors = array();

        $data = $this->defaults();

        $data["bubble"] = $_POST["bubble"];

        if(isset($_POST["showFooter"]) && $_POST["showFooter"] == true) {
            $data["showFooter"] = true;
        }
        else {
            $data["showFooter"] = false;
        }

        if(isset($_POST["page_id"])) {
            $page_id_error = true;
            $page_id = $_POST["page_id"];
            $pages = get_pages();
            foreach ($pages as $page) {
                if ($page->ID == $page_id) {
                    $data["page_id"] = $page_id;
                    $page_id_error = false;
                }
            }
            if($page_id_error)
                array_push($errors, "Page with the ID \"$page_id\" not found! ");
        }

        if(isset($_POST["comments_enabled"]) && $_POST["comments_enabled"] == true) {
            $data["comments_enabled"] = true;
        }
        else {
            $data["comments_enabled"] = false;
        }

        update_option('bublaa-plugin-options', $data);

        return $errors;
    }

    function add_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=bublaa">Settings</a>';
        array_push( $links, $settings_link );
        return $links;
    }
}
$bublaa = new Bublaa();

/*
Activity
*/
class BublaaWidget extends WP_Widget {
    /**
     * Constructor
     */
    function BublaaWidget() {
        $widget_ops = array( 'height' => 600 );
        parent::__construct( false, 'Bublaa - Latest Activity', $widget_ops );
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        //Strip tags from title and name to remove HTML
        if(is_numeric( $new_instance['height'] ))
            $instance['height'] = $new_instance['height'];

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'height' => '600' ) );
        $height = $instance['height'];

        echo "<p>
                <label for='" . $this->get_field_id('height') . "'>Height:
                    <input class='bublaa_sidebar_height' id='" . $this->get_field_id('height') . "' name='" . $this->get_field_name('height') . "' type='text' value='" . esc_attr($height) . "' />
                </label>
            </p>";
    }

    function widget($args, $instance) {

        // reuse my plugin's code
        global $bublaa;
        $options = $bublaa->get_options();

        if(!$options['bubble'])
            return;

        $widgetOptions = $this->widget_options;

        $host = "http://bublaa.com";

        // final markup to init bublaa
        echo "
            <div id='bublaa-sidebar'></div>
            <style>
                    #bublaa-sidebar {
                        height: " . $widgetOptions['height'] ."px;
                        width: 100%;
                        min-height: 300px;
                        max-height: 600px;
                    }
            </style>
            <script type='text/javascript'>
                window.bublaa = {
                    config : {
                        bubble     : '" . $options["bubble"] ."',
                        forumUrl: '" . get_page_link($options['page_id']) ."'
                    }
                };

                (function() {
                    var b = document.createElement('script'); b.type = 'text/javascript'; b.async = true;
                    b.src = '" . $host ."/dist/plugins.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(b, s);
                })();
            </script>
        ";
    }
}
/**
 * Comments
 */
class BublaaComments {
    /**
     * Comments
     * @return void
     */
    function load_comments_template() {
        return dirname(__FILE__) . '/bublaa-comments-template.php';
    }

    function comments_number($text) {
        global $post;
        global $bublaa;
        $options = $bublaa->get_options();
        if(isset($options["bubble"])) {
            return "<span style='display:none;' class='bublaa-comments-count' data-forum='" . $options["bubble"] . "' data-id='" . $post->ID . "'/>";
        }
        return;

    }

    function load_scripts() {
        wp_enqueue_script('bublaa-comments-count', 'http://bublaa.com/dist/plugins.js');
    }
}
$bublaaComments = new BublaaComments();