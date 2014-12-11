<?php

// Process form
if (isset($_POST['wpbb-settings-submit'])) {

	// Sanitize values
	$maintenance_mode = $_POST['wpbbmaintenancemode'] == 'on' ? 'on' : 'off';
		
	$maintenance_message = wp_strip_all_tags($_POST['wpbbmaintenancemessage']);
		
	$forum_name = wp_strip_all_tags($_POST['wpbbname']);
		
	$allow_guests = $_POST['wpbballowguests'] == 'yes' ? 'yes' : 'no';
		
	$allow_subforums = $_POST['wpbballowsubforums'] == 'yes' ? 'yes' : 'no';
		
	$enable_quick_reply = $_POST['wpbbenablequickreply'] == 'yes' ? 'yes' : 'no';
		
	$topics_per_page = absint($_POST['wpbbtopicsperpage']);
	$posts_per_page = absint($_POST['wpbbpostsperpage']);
		
	if (isset($_POST['wpbbtopiccutoff'])) {
		$topic_cutoff = absint($_POST['wpbbtopiccutoff']);
	}
	
	if (isset($_POST['wpbbpostcutoff'])) {
		$post_cutoff = absint($_POST['wpbbpostcutoff']);
	}

	$enable_posting_to_forum = $_POST['wpposttowpbb'] == 'yes' ? 'yes' : 'no';
		
	$show_footer = $_POST['wpbb-show-footer'] == 'yes' ? 'yes' : 'no';
	
	if (isset($_POST['wpbbposttoforumcalled'])) {
		$post_to_forum = absint($_POST['wpbbposttoforumcalled']);
	}
	
	$old_options = get_option('wpbb_options');
	
	// Create options array
	$new_options = array(
		'maintenance_mode' => $maintenance_mode,
		'maintenance_message' => $maintenance_message,
		'forum_name' => $forum_name,
		'allow_guests' => $allow_guests,
		'allow_subforums' => $allow_subforums,
		'enable_quick_reply' => $enable_quick_reply,
		'topics_per_page' => $topics_per_page,
		'posts_per_page' => $posts_per_page,
		'topic_cutoff' => $topic_cutoff,
		'post_cutoff' => $post_cutoff,
		'post_to_forum' => $enable_posting_to_forum,
		'role_permissions' => $old_options['role_permissions'],
		'show_footer' => $show_footer
	);
	// Update options in DB
	$update_options = update_option('wpbb_options', $new_options);
	
} else if (isset($_POST['wpbb-theme-settings-submit'])) {
		
	if (isset($_POST['wpbb-theme-type'])) {
		$theme = $_POST['wpbb-theme-type'] == 'light' ? 'light' : 'dark';
	}
	
	$update_theme_options = update_option('wpbb_theme_options', array('theme' => $theme));
	
} else if (isset($_POST['wpbb-facebook-settings-submit'])) {
				
	$facebook_support = $_POST['wpbbfacebooksupport'] == 'yes' ? 'yes' : 'no';
		
	$facebook_app_id = absint($_POST['wpbbfacebookappid']);
		
	if (ctype_alnum($_POST['wpbbfacebookappsecretkey'])) {
		$facebook_app_secret_key = $_POST['wpbbfacebookappsecretkey'];
	} else {
		$facebook_app_secret_key = __('Enter your App Secret Key', 'wp-bb');
	}
		
	$facebook_redirect_uri = wp_strip_all_tags($_POST['wpbbfacebookredirecturi']);
		
	(string) $facebook_state = wp_strip_all_tags($_POST['wpbbfacebookstate']);
	
	$faceboook_default_role = strtolower(wp_strip_all_tags($_POST['wpbbfacebookdefaultrole']));
	
	$new_facebook_options = array(
		'allow_facebook' => $facebook_support,
		'facebook_app_id' => $facebook_app_id,
		'facebook_app_secret_key' => $facebook_app_secret_key,
		'facebook_redirect_uri' => $facebook_redirect_uri,
		'facebook_state' => $facebook_state,
		'facebook_default_role' => $faceboook_default_role
	);
		
	$update_facebook_options = update_option('wpbb_facebook_options', $new_facebook_options);
	
} else if (isset($_POST['wpbb-twitter-settings-submit'])) {
		
	$twitter_support = $_POST['wpbbtwittersupport'] == 'yes' ? 'yes' : 'no';
		
	if (isset($_POST['wpbbtwitteraccount'])) {
		$twitter_account = wp_strip_all_tags($_POST['wpbbtwitteraccount']);
	}
		
	$new_twitter_options = array(
		'allow_twitter' => $twitter_support,
		'twitter_account' => $twitter_account,
	);
		
	$update_twitter_options = update_option('wpbb_twitter_options', $new_twitter_options);
}

$options = get_option('wpbb_options');
// Get theme options
$theme_options = get_option('wpbb_theme_options');
// Get facebook options
$facebook_options = get_option('wpbb_facebook_options');
// Get twitter options
$twitter_options = get_option('wpbb_twitter_options');

?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div> 
	<h2><?php _e('My WPBB', 'wp-bb'); ?></h2>
	<h3><?php _e('Forum Settings', 'wp-bb'); ?></h3>
	<table class="form-table">
		<form method="POST" action="#">
			<tr>
				<th>
					<label for="wpbbactive"><?php _e('Maintenance Mode', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbbmaintenancemode" value="on" <?php checked($options['maintenance_mode'], 'on');?>> <?php _e('On', 'wp-bb'); ?>
					<input type="radio" name="wpbbmaintenancemode" value="off" <?php checked($options['maintenance_mode'], 'off');?>> <?php _e('Off', 'wp-bb'); ?>
					<p class="description">If enabled this will display the message below to all users and they won't be able to do anything in the forum. Useful for upgrades, cleaning up, moving host, etc. Admins are still allowed to view the forum as usual but a message is displayed on the forum to warn them that maintenance mode is enabled.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbactivemessage"><?php _e('Maintenance Message', 'wp-bb'); ?></label>
				</th>
				<td>
					<textarea name="wpbbmaintenancemessage" cols="50" rows="2"><?php echo $options['maintenance_message']; ?></textarea>
					<p class="description">The message you want to display to all users when you have enabled maintenance mode i.e. disabled the forum.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbname"><?php _e('Forum Name', 'wp-bb'); ?></label>
				</th>
				<td>
					<input maxlength="32" size="25" name="wpbbname" value="<?php if($options['forum_name']) echo $options['forum_name'];?>" />
					<p class="description">Your WPBB forum name which is displayed on every page.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbballowguests"><?php _e('Enable Guest Permissions', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbballowguests" value="yes" <?php checked($options['allow_guests'], 'yes');?>/> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpbballowguests" value="no" <?php checked($options['allow_guests'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">If enabled you will be able to set guest permissions in every forum and subforum. Disable if you want to force guests to login or register.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbballowsubforums"><?php _e('Dislay subforums on Index', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbballowsubforums" value="yes" <?php checked($options['allow_subforums'], 'yes');?>/> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpbballowsubforums" value="no" <?php checked($options['allow_subforums'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">If enabled and you have a subforum inside a forum, you will be able to see it displayed below the forum on the first WPBB page. If disabled, any subforums within forums are not shown on the first page.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbenablequickreply"><?php _e('Enable Quick Reply', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbbenablequickreply" value="yes" <?php if (isset($options['enable_quick_reply'])) checked($options['enable_quick_reply'], 'yes');?>/> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpbbenablequickreply" value="no" <?php if (isset($options['enable_quick_reply'])) checked($options['enable_quick_reply'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">If enabled, a quick reply box is displayed at the bottom of a topic for everyone. Enabled by default.</p>
				</td>
			</tr>
			<?php 
			$topics_per_page = (!isset($options['topics_per_page'])) ? 20 : $options['topics_per_page'];
			$posts_per_page = (!isset($options['posts_per_page'])) ? 20 : $options['posts_per_page'];
			?>
			<tr>
				<th>
					<label for="wpbbtopicsperpage"><?php _e('Topics per page', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="number" name="wpbbtopicsperpage" value="<?php echo $topics_per_page; ?>" />
					<p class="description">The amount of topics to display on a single page inside a forum or subforum</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbpostsperpage"><?php _e('Posts per page', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="number" name="wpbbpostsperpage" value="<?php echo $posts_per_page; ?>" />
					<p class="description">The amount of posts to display on a single page inside a topic</p>
				</td>
			</tr>
			<?php
			$topic_cutoff_value = (!isset($options['topic_cutoff'])) ? 15 : $options['topic_cutoff'];
			$post_cutoff_value = (!isset($options['post_cutoff'])) ? 15 : $options['post_cutoff'];
			?>
			<tr>
				<th>
					<label for="wpbbtopiccutoff"><?php _e('Delay between Topic creations', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="number" name="wpbbtopiccutoff" min="0" value="<?php echo $topic_cutoff_value; ?>" />
					<p class="description">The amount of time in seconds everyone must wait before being allowed to create a new topic. Prevents spamming.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbpostcutoff"><?php _e('Delay between Post creations', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="number" name="wpbbpostcutoff" min="0" value="<?php echo $post_cutoff_value; ?>" />
					<p class="description">The amount of time in seconds everyone must wait before being allowed to create a new post. Prevents spamming.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpposttowpbb"><?php _e('Enable Posting to Forum', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpposttowpbb" value="yes" <?php checked($options['post_to_forum'], 'yes');?>/> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpposttowpbb" value="no" <?php checked($options['post_to_forum'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">If you want to enable the ability to also choose what forum you want to send a copy of your wordpress post to in Posts -> Add New</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbb-show-footer"><?php _e('Show Footer', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbb-show-footer" value="yes" <?php if (isset($options['show_footer'])) checked($options['show_footer'], 'yes');?>/> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpbb-show-footer" value="no" <?php if (isset($options['show_footer'])) checked($options['show_footer'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">Displays a "powered by" message at the bottom of your forum.</p>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="wpbb-settings-submit" class="button-primary" value="<?php _e('Save Settings', 'wp-bb'); ?>" />
				</td>
			</tr>
		</form>
	</table>
	<h3><?php _e('Theme Settings', 'wp-bb'); ?></h3>
	<table class="form-table">
		<form method="POST" action="#">
			<tr>
				<th>
					<label for="wpbb-theme-type">Theme</label>
				</th>
				<td>
					<input type="radio" name="wpbb-theme-type" value="light" <?php checked($theme_options['theme'], 'light');?> /> <?php _e('Light', 'wp-bb'); ?>
					<input type="radio" name="wpbb-theme-type" value="dark" <?php checked($theme_options['theme'], 'dark');?>/> <?php _e('Dark', 'wp-bb'); ?>
					<p class="description">The dark theme uses slightly darker CSS colors and images. You can use either setting depending on your theme and your liking so we recommend experimenting with this.</p>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="wpbb-theme-settings-submit" class="button-primary" value="<?php _e('Save Theme Settings', 'wp-bb'); ?>" />
				</td>
			</tr>
		</form>
	</table>
	<h3><?php _e('Facebook Settings', 'wp-bb'); ?></h3>
	<table class='form-table'>
		<form method='POST' action='#'>
			<tr>
				<th>
					<label for="wpbbfacebooksupport"><?php _e('Enable Facebook Support', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbbfacebooksupport" value="yes" <?php checked($facebook_options['allow_facebook'], 'yes');?> /> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpbbfacebooksupport" value="no" <?php checked($facebook_options['allow_facebook'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">This must be enabled before you can change the settings below. If enabled, allows your users to signup using facebook.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbfacebookappid"><?php _e('My App ID / API Key', 'wp-bb'); ?></label>
				</th>
				<td>
					<?php
					// App ID
					$facebook_app_id_value = (isset($facebook_options['facebook_app_id'])) ? $facebook_options['facebook_app_id'] : "Enter your App ID / API Key";
					// App secret key
					$facebook_app_secret_key_value = (isset($facebook_options['facebook_app_secret_key'])) ? $facebook_options['facebook_app_secret_key'] : "Enter your App Secret Key";
					// Redirect URL
					$facebook_redirect_uri_value = (isset($facebook_options['facebook_redirect_uri'])) ? $facebook_options['facebook_redirect_uri'] : "http://www.yoursite.com";
					// Facebook state
					$facebook_state_value = (isset($facebook_options['facebook_state'])) ? $facebook_options['facebook_state'] : 'Enter a unique string (letters and numbers only';
					// Facebook default role
					$facebook_default_role = (isset($facebook_options['facebook_default_role'])) ? $facebook_options['facebook_default_role'] : 'Enter a default role for new Facebook registrations';
					?>
					<input type='text' name='wpbbfacebookappid' value="<?php echo $facebook_app_id_value; ?>"/>
					<p class="description">Required. You can get this from your facebook app settings.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbfacebookappsecretkey"><?php _e('App Secret Key', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="text" name="wpbbfacebookappsecretkey" value="<?php echo $facebook_app_secret_key_value; ?>" />
					<p class="description">Required. You can get this from your facebook app settings.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbfacebookredirecturi"><?php _e('Site URL (Redirect URL)', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type='text' name="wpbbfacebookredirecturi" value="<?php echo $facebook_redirect_uri_value; ?>"/>
					<p class="description">Must match your facebook redirect URL. It is the link to your WPBB forum.</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbfacebookstate"><?php _e('CSRF Protection', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="text" name="wpbbfacebookstate" value="<?php echo $facebook_state_value; ?>" />
					<p class="description">This is required. Enter a random alphanumeric string (letters and numbers only)</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wpbbfacebookdefaultrole"><?php _e('Default Role', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="text" name="wpbbfacebookdefaultrole" value="<?php echo strtolower($facebook_default_role); ?>"/>
					<p class="description">Required. Enter a role or leave the textbox blank.</p>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="wpbb-facebook-settings-submit" class="button-primary" value="<?php _e('Save Facebook Settings', 'wp-bb'); ?>" /></td>
			</tr>
		</form>
	</table>
	<h3><?php _e('Twitter Settings', 'wp-bb'); ?></h3>
	<table class='form-table'>
		<form method='POST' action='#'>
			<tr>
				<th>
					<label for="wpbbtwittersupport"><?php _e('Enable Twitter Support', 'wp-bb'); ?></label>
				</th>
				<td>
					<input type="radio" name="wpbbtwittersupport" value="yes" <?php if (isset($twitter_options['allow_twitter'])) checked($twitter_options['allow_twitter'], 'yes');?>/> <?php _e('Yes', 'wp-bb'); ?>
					<input type="radio" name="wpbbtwittersupport" value="no" <?php if (isset($twitter_options['allow_twitter'])) checked($twitter_options['allow_twitter'], 'no');?>/> <?php _e('No', 'wp-bb'); ?>
					<p class="description">Enable to change settings below. If enabled, displays a button on your forum that your users can click on to visit your twitter profile, message you, follow you, etc.</p>
				</td>
			</tr>
			<tr>
			<tr>
				<th>
					<label for="wpbbtwitteraccount"><?php _e('Twitter Account', 'wp-bb'); ?></label>
				</th>
				<td>
					<?php
						if (isset($twitter_options['twitter_account'])) {
							$twitter_account = $twitter_options['twitter_account'];
						} else {
							$twitter_account =  _e('Enter your websites Twitter account', 'wp-bb');
						}
					?>
					<input type="text" name="wpbbtwitteraccount" value="<?php echo $twitter_account; ?>"/>
					<p class="description">Enter your twitter username here. Get this from twitter when you signup.</p>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="wpbb-twitter-settings-submit" class="button-primary" value="<?php _e('Save Twitter Settings', 'wp-bb'); ?>" /></td>
			</tr>
		</form>
	</table>
	</div>
	<?php
?>