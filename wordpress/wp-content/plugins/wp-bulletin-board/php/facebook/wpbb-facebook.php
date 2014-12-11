<?php

global $wpdb;

global $forum_page;

$fb_options = get_option('wpbb_facebook_options');

$user_id = get_current_user_id();

if ($user_id !== 0)
{
	// User is logged in
	$user_access_token = get_user_meta($user_id, 'wpbb_facebook_access_token', true);
	if ($user_access_token != "" && is_user_logged_in())
	{
		// User is logged into Facebook
		$logged_into_facebook = true;
		if (count($_GET) == 0)
		{
		?>
		<div class="wpbb-centered">
			<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Flocalhost%2Fwww%2Fwordpress%2Fforum%2F&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:35px;" allowTransparency="true">
			</iframe>
		</div>
		<?php
		}
	}
} 
else
{ 
	/* 
		User is not logged in
		Registering for a Wordpress account through Facebook 
	*/
	if ((isset($_GET['register'])) && (isset($_GET['method'])) && (count($_GET) == 2)) {
	
		$registration_enabled = get_option('users_can_register');
		
		if (!$registration_enabled) {
			?>
			<div class="wpbb-centered">
				<?php
					_e('Sorry, the administrator has disabled user registrations.', 'wp-bb');
					wpbb_exit();
					
				?>
			</div>
			<?php
		}
	
		?>
		<h2 class="wpbb-centered-bold">
		
		<?php _e('Register Using Facebook', 'wp-bb'); ?>
		
		</h2>
		
		<div class="wpbb-centered">
		
		<?php _e('To register on this site using your facebook account all you have to do is fill in the information below and enter a password that will be used to login to your account. Your facebook password will not be used.', 'wp-bb'); ?>
		
		</div>
		
		<?php
		
		$fields = 'name,email,password';

		echo "<iframe src='https://www.facebook.com/plugins/registration?client_id=".$fb_options['facebook_app_id']."&redirect_uri=".$fb_options['facebook_redirect_uri']."&fields=".$fields."'scrolling='auto'frameborder='n'style='border:none'allowTransparency='true'width='100%'height='330'></iframe>";
	
	} else if (isset($_GET['state']) || isset($_GET['code'])) {
	
		$facebook_options = get_option('wpbb_facebook_options');

		// Retrieve Redirect URL
		$wpbb_facebook_redirect_uri = $facebook_options['facebook_redirect_uri'];
	
		if (isset($_GET['state'])) {
		
			$state = wp_strip_all_tags($_GET['state']);
		
			if ($state == $facebook_options['facebook_state']) {
			
				$client_id = $facebook_options['facebook_app_id'];
			
				$redirect_uri = $facebook_options['facebook_redirect_uri'];
			
				$client_secret = $facebook_options['facebook_app_secret_key'];
			
				$code = $_GET['code'];
			
				$url = "https://graph.facebook.com/oauth/access_token?client_id=".$client_id."&redirect_uri=".$redirect_uri."&client_secret=".$client_secret."&code=".$code;
		
				$response = file_get_contents($url);
			
				$para = NULL;
				
				parse_str($response, $para);
		
				if (isset($para['access_token'])) {
					$graph_url = "https://graph.facebook.com/me?fields=id,name,picture,email&access_token=".$para['access_token'];
				
					$facebook_user = json_decode(file_get_contents($graph_url), true);
				
					if ($facebook_user['id'] != 0) { // User is logged into Facebook
						$logged_into_facebook = true;
						if (!isset($facebook_user['email'])) {
							?>
							<div class="wpbb-message-failure">
								<?php
								_e('Could not log you in using Facebook. You must authorize the app to allow it to use your email address for validation', 'wp-bb');
								?>
							</div>
							<?php
							wpbb_exit();
						}
						$email_exists = email_exists($facebook_user['email']);
						if ($email_exists === false) {
							?>
							<div class="wpbb-message-failure">
								<?php
								_e('You must register for an account using Wordpress or Facebook before logging in.', 'wp-bb');
								?>
							</div>
							<?php
							wpbb_exit();
						}
						?>
						<h2 class="wpbb-centered-bold">
							<?php _e('Login to Wordpress', 'wp-bb'); ?>
						</h2>
						<br />
						<div class="wpbb-centered">
							<?php printf(__('Thank you for logging in to Facebook, %s!'), $facebook_user['name']); ?>
							<br /><br />
							<?php _e('Please now enter the password for your Wordpress account you registered using Facebook.', 'wp-bb'); ?><br /><br /><?php
							$forum_page_url = get_page($forum_page);
							$args = array(
        						'echo' => true,
        						'redirect' => $forum_page_url->guid,
        						'remember' => true,
        						'value_username' => $facebook_user['name'],
        						'value_remember' => 1
        					);
        					$user = get_user_by('email', $facebook_user['email']);
        					$access_token = wp_strip_all_tags($para['access_token']);
        					$add_user_facebook_meta = update_user_meta($user->ID, 'wpbb_facebook_access_token', $access_token);				
							wp_login_form($args);
							?>
						</div>
						<?php
					} else {
						_e('Please login to Facebook', 'wp-bb');
					}				
				}
			} else { // Possible CSRF victim
				?>
				<div class="wpbb-message-failure">
					<?php
					_e('There was an error logging you into Facebook. Please try again', 'wp-bb');
					?>
				</div>
				<?php
				wpbb_exit();
			}
		} else { // State was not provided
			?>
			<div class="wpbb-message-failure">
				<?php
				_e('You must provide a state (a random string of variable length) in your Facebook Settings');
				?>
			</div>
			<?php
			wpbb_exit();
		}
	}
}


function wpbb_get_facebook_login() {
	$facebook_options = get_option('wpbb_facebook_options');

	if (!is_user_logged_in()) {
	
		// Retrieve App ID / API Key
		$wpbb_facebook_app_id = $facebook_options['facebook_app_id'];

		// Retrieve Redirect URL
		$wpbb_facebook_redirect_uri = $facebook_options['facebook_redirect_uri'];
			
		// Retrieve Facebook State (Random string)
		$wpbb_facebook_state = $facebook_options['facebook_state'];
	
		$url = '';
		
		$scope = '&scope=email';
		

		if ($wpbb_facebook_state != "") {
			$state = '&state='.$wpbb_facebook_state;
		} else {
			$state = '';
		}

		$url = 'https://www.facebook.com/dialog/oauth?client_id='.$wpbb_facebook_app_id.'&redirect_uri='.$wpbb_facebook_redirect_uri.''.$scope.''.$state;
		return $url;
	}
}


if (isset($_POST['signed_request'])) {
	
	// Returns JSON object
	$data = parse_signed_request($_POST['signed_request'], $fb_options['facebook_app_secret_key']);
	
	$username = $data['registration']['name'];
	$password = $data['registration']['password'];
	$email = $data['registration']['email'];
	$facebook_options = get_option('wpbb_facebook_options');
	$role = $facebook_options['facebook_default_role'];
	
	//$create_user = wp_create_user($username, $password, $email);
	$create_user = wp_insert_user(array('user_login' => $username, 'user_pass' => $password, 'user_email' => $email, 'role' => $role));
	
	if (is_wp_error($create_user)) {
		$errors = $create_user->get_error_messages();
		foreach ($errors as $err) {
			wpbb_goback1('facebook-register-uname-exists', NULL);
			?>
			<div class="wpbb-message-failure">
				<?php
				echo $err;
				?>
			</div>
			<?php
		}
		wpbb_exit();
	} else { 
		?>
		<div class="wpbb-message-success">
			<?php
			_e('Thank you for registering. Please login with your Facebook username and password you just created', 'wp-bb');
			?>
		</div>
		<div class="wpbb-centered">
			<?php
			wp_login_form();
			?>
		</div>
		<?php
		wpbb_exit();
	}
}

function parse_signed_request($signed_request, $secret) {

	list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
	
	// decode the data
	$sig = base64_url_decode($encoded_sig);
	$data = json_decode(base64_url_decode($payload), true);
	if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		$unkown_algorithm = __('Unknown algorithm. Expected HMAC-SHA256', 'wp-bb');
		error_log($unkown_algorithm);
		return null;
	}
	
	// check sig
	$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	if ($sig !== $expected_sig) {
		$bad_json_sig = __('Bad Signed JSON signature!', 'wp-bb');
		error_log($bad_json_sig);
		return null;
	}
	return $data;
}

function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
}
?>