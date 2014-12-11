<?php

	wpbb_goback1();

	$forum = absint($_GET['forum']);
	
	$user_id = get_current_user_id();
	
	// Third parameter is 'view' by default
	$view_permissions = wpbb_user_has_permission($user_id, $forum);
	if ($view_permissions === false) {
		?>
		<div class="wpbb-message-failure">
			<?php _e('You do not have the required permissions to view this forum!', 'wp-bb'); ?>
		</div>
		<?php
		wpbb_exit();
	} else {
		$sticky_permissions = wpbb_user_has_permission($user_id, $forum, 'sticky');
		if ($sticky_permissions === false) {
			?>
			<div class="wpbb-message-failure">
				<?php _e('You do not have the required permissions to sticky topics in this forum!', 'wp-bb'); ?>
			</div>
			<?php
			wpbb_exit();
		}
	}
	
	$topic_id = absint($_GET['topic']);

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
					wpbb_error('You can only sticky your own topics!');
					wpbb_exit();
				}
			}
		}
	}
	
	$topic_status = $wpdb->get_var("SELECT status FROM ".TOPIC_TABLE." WHERE id = $topic_id;");
	
	if (strpos($topic_status, 'sticky') !== false) {
		$old_status = str_replace('sticky', "", $topic_status);
		$unsticky = $wpdb->update(TOPIC_TABLE, array('status' => $old_status), array('id' => $topic_id));
		if ($unsticky !== false) {
			?>
			<div class="wpbb-message-success">
				<?php printf(__('Topic ID %s has been unstickied successfully.'), $topic_id); ?>
			</div>
			<?php
		} else { // Error
			?>
			<div class="wpbb-message-failure">
				<?php printf(__('There was an error trying to unsticky topic ID %s'), $topic_id); ?>
			</div>
			<?php
		}
	} else if (strpos($topic_status, 'sticky') === false) {
		$new_status = 'sticky,'.$topic_status;
		$sticky = $wpdb->update(TOPIC_TABLE, array('status' => $new_status), array('id' => $topic_id));
		if ($sticky !== false) {
			?>
			<div class="wpbb-message-success">
				<?php printf(__('Topic ID %s has been stickied successfully.'), $topic_id); ?>
			</div>
			<?php
		} else { // Error
			?>
			<div class="wpbb-message-failure">
				<?php printf(__('There was an error trying to sticky topic ID %s'), $topic_id); ?>
			</div>
			<?php
		}
	}

?>