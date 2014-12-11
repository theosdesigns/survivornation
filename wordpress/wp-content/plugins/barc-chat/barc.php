<?php
/*
Plugin Name: Barc Chat
Plugin URI: http://barc.com
Description: Barc Chat provides a simple yet feature rich chat room for your whole community to interact in real-time directly on your site. You can have an unlimited number of users chatting simultaneously, it's completely free and there are no ads!
Version: 0.5.9
Author: Barc Inc.
Author URI: http://barc.com
License: GPLv2
Text Domain: barc
*/

class Barc
{
  const ld = 'barc';

  // version of the plugin, must be updated with header version
  const version = '0.5.9';

  // activation URL, should be changed to live version
  const activation_url = 'http://barc.com/__api/domain/setOwner';

  // barc div element and JS code
  const barc_div = '<div id="barc-container"></div>';
  const barc_js = '
  <script type="text/javascript">
  (function() {
    var b = document.createElement("script"); b.type = "text/javascript"; b.async = true;
    b.src = "//barc.com/js/libs/barc/barc.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(b, s);
  })();
</script>
';

private $plugin_url, $plugin_path;

public function __construct()
{
    // paths
  $this->plugin_url = plugins_url('', __FILE__);
  $this->plugin_path = dirname(__FILE__);

  add_action('plugins_loaded', array(&$this, 'plugins_loaded'));

  if (is_admin())
  {
    add_action('admin_menu', array(&$this, 'admin_menu'));
    add_action('wp_ajax_'.__class__.'_action', array(&$this, 'ajax_action'));
  }
  else
  {
      // apply barc inline chat instead of comments if it's enabled
    $page = get_option(__class__.'_page', 0);
    if ($page == -1)
    {
        // disable post comments
      add_action('pre_comment_on_post', array(&$this, 'pre_comment_on_post'));

        // add filters to handle custom comments from barc
      add_filter('comments_template', array(&$this, 'comments_template'));
      add_filter('comments_number', array(&$this, 'comments_number'));
      add_filter('get_comments_number', array(&$this, 'get_comments_number'));
    }
    else
      if ($page > 0)
        add_filter('the_content', array($this, 'content'), 999);
      else
        add_action('wp_footer', array(&$this, 'footer'), 999);
    }

    add_action('wp_ajax_'.__class__.'_activation', array(&$this, 'ajax_activation'));
    add_action('wp_ajax_nopriv_'.__class__.'_activation', array(&$this, 'ajax_activation'));

    // activation and uninstall hook
    register_activation_hook(__FILE__, array(&$this, 'activation'));
    register_uninstall_hook(__FILE__, array(__class__, 'uninstall'));
  }

  // disable posting of comments
  public function pre_comment_on_post($id)
  {
    exit('This feature is disabled by Barc Chat Plugin.');
  }

  // replace number of comments
  public function comments_number($t)
  {
    return $this->get_comments_number($t);
  }

  public function get_comments_number($t)
  {
    return '';
  }

  // apply barc comment template
  public function comments_template($t)
  {
    global $post;

    if (!(is_singular() && (have_comments() || $post->comment_status == 'open')))
      return;

    return $this->plugin_path.'/comment_template.php';
  }

  // on activation
  public function activation()
  {
    add_option(__class__.'_code', '');
    add_option(__class__.'_activated', 0);
    add_option(__class__.'_page', 0);
    add_option(__class__.'_position', 0);
    add_option(__class__.'_plan',"unpaid");
  }

  // uninstall
  static function uninstall()
  {
    delete_option(__class__.'_code');
    delete_option(__class__.'_activated');
    delete_option(__class__.'_page');
    delete_option(__class__.'_position');
    delete_option(__class__.'_plan');
  }

  public function plugins_loaded()
  {
    // load text domain
    load_plugin_textdomain(self::ld, false, dirname(plugin_basename(__FILE__)).'/languages/');
  }

  // add menu item to the options of the admin menu
  public function admin_menu()
  {
    // add option page
    add_options_page(__('Barc Chat', self::ld), __('Barc Chat', self::ld), 'manage_options', __class__, array(&$this, 'options_page'));
    add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2);

    // enquee scripts and styles on options page
    add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue'));
  }

  // add shortcut to the options page in the installed plugins area
  public function filter_plugin_actions($l, $file)
  {
    $settings_link = '<a href="options-general.php?page='.__class__.'">'.__('Settings').'</a>';
    array_unshift($l, $settings_link);
    return $l;
  }

  // enqueue scripts and styles
  public function admin_enqueue($hook)
  {
    if ($hook != 'settings_page_Barc') return;

    wp_enqueue_style(__class__.'_styles', $this->plugin_url.'/css/styles.css', array(), self::version, 'all');

    wp_enqueue_script('jquery');
    wp_enqueue_script(__class__, $this->plugin_url.'/options.js', array('jquery'), self::version, false);
    wp_localize_script(__class__, __class__, array(
      'action_url' => admin_url('admin-ajax.php?action='.__class__.'_action'),
      'text' => array(
        'ajax_error' => __('An error occurred during the AJAX request, please try again later.', self::ld),
        'page_not_selected' => __('Please select a page.', self::ld)
        )
      ));
  }

  // options page
  function options_page()
  {
    // get list of pages
    $pages = get_pages(array(
      'sort_order' => 'ASC',
      'sort_column' => 'post_title',
      'hierarchical' => 1,
      'child_of' => 0,
      'parent' => -1,
      'offset' => 0,
      'post_type' => 'page',
      'post_status' => 'publish,private,future'
      ));

    $class = __class__;

    // get hostname
    $host = parse_url(get_site_url(), PHP_URL_HOST);
    $activated = get_option(__class__.'_activated', false);
    $username = self::strip(get_option(__class__.'_code', ''));
    $position = get_option(__class__.'_position', 0);
    $paymentPlan = get_option(__class__.'_plan', '');

    require_once $this->plugin_path.'/options.php';
  }

  // ajax action handler
  public function ajax_action()
  {
    header("Content-Type: application/json");

    $a = isset($_POST['a'])?$_POST['a']:false;

    switch($a)
    {
      case 'save_page':
      update_option(__class__.'_page', isset($_POST['post_id'])?$_POST['post_id']:0);
      break;

      case 'save_position':
      update_option(__class__.'_position', isset($_POST['position'])?$_POST['position']:0);
      break;

      case 'save_payment':
      console.log("UPDATED PLAN");
      update_option(__class__.'_plan', strtolower(trim(isset($_POST['plan'])?$_POST['plan']:'')));
      break;

      case 'save_code':
        // $activation_code is username
      $activation_code = stripslashes(strtolower(trim(isset($_POST['code'])?$_POST['code']:'')));

      update_option(__class__.'_code', $activation_code);

        // try to get username from activation code and send activation request
      $r = wp_remote_post(self::activation_url, $a = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(
          'Content-Type' => 'application/json'
          ),
        'body' => json_encode(array('userName' => $activation_code, 'url' => admin_url('admin-ajax.php?action='.__class__.'_activation'))),
        'cookies' => array()
        ));


      if (!is_wp_error($r) && $r['response']['code'] == 200)
      {
        update_option(__class__.'_activated', true);
        echo json_encode(array('status' => 1));
        exit;
      }


      update_option(__class__.'_activated', false);
      echo json_encode(array('status' => 2));
      exit;
    }

    echo json_encode(array());
    exit;
  }

  // show activation code, that's all
  public function ajax_activation()
  {
    echo "<script>var __v='barc-user-".get_option(__class__.'_code', '')."';</script>".PHP_EOL;
    exit;
  }

  // output code to the footer
  public function footer()
  {
    if (get_option(__class__.'_page', 0) == 0)
      echo self::barc_div.self::barc_js;
  }

  public function content($content)
  {
    global $post;

    if (!is_page() || get_option(__class__.'_page', 0) != $post->ID)
      return $content;

    $position = get_option(__class__.'_position', 0);

    return ($position == 1?$content:'').self::barc_div.self::barc_js.($position == 0?$content:'');
  }

  // helper strip function
  static function strip($t)
  {
    return htmlentities($t, ENT_COMPAT, 'UTF-8');
  }
}

new Barc();