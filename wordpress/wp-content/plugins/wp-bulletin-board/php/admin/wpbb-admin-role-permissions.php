<?php

if (isset($_POST['wpbb-role-permissions-save']))
{
	$roles = wpbb_admin_get_all_roles();
	$role_array = array();
	if ($roles)
	{
		foreach ($roles as $role)
		{
			if (array_key_exists("wpbbrolepermissions$role", $_POST))
			{
				$role_array[$role] = wp_strip_all_tags($_POST["wpbbrolepermissions$role"]);
			}
		}
	}
	// Retrieve our options, we don't want to overwrite any, we just want to update our role permissions option
	$wpbb_options = get_option("wpbb_options");
	$updated_options = array(
		'maintenance_mode' => $wpbb_options['maintenance_mode'],
		'maintenance_message' => $wpbb_options['maintenance_message'],
		'forum_name' => $wpbb_options['forum_name'],
		'allow_guests' => $wpbb_options['allow_guests'],
		'allow_subforums' => $wpbb_options['allow_subforums'],
		'enable_quick_reply' => $wpbb_options['enable_quick_reply'],
		'topics_per_page' => $wpbb_options['topics_per_page'],
		'posts_per_page' => $wpbb_options['posts_per_page'],
		'topic_cutoff' => $wpbb_options['topic_cutoff'],
		'post_cutoff' => $wpbb_options['post_cutoff'],
		'post_to_forum' => $wpbb_options['post_to_forum'],
		'show_footer' => $wpbb_options['show_footer'],
		'role_permissions' => $role_array,
		'version' => $wpbb_options['version']
	);
	$update_options = update_option('wpbb_options', $updated_options);
	if ($update_options)
	{
		wpbb_admin_success('Role permissions updated successfully');
	}
	else
	{
		wpbb_admin_error('Role permissions were not updated successfully, please try again.');
	}
}

?>

<div class="wrap">
	<form action="#" method="POST">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e('Role Permissions', 'wp-bb'); ?></h2>
		<p class="description">If yes, that role will be able to edit, lock, delete and sticky any topic or post. If no, that role will only be able to edit, lock, delete and sticky their own topics or posts. The roles must have edit, lock, delete or sticky permissions in a forum or subforum for this to take affect.</p>
		<table class="form-table">
			<?php
			$roles = wpbb_admin_get_all_roles();
			$options = get_option("wpbb_options");
			if ($roles)
			{
				foreach ($roles as $role)
				{
					if (isset($options['role_permissions']))
					{
						$role_value = NULL;
						if (array_key_exists($role, $options['role_permissions']))
						{
							$role_value = $options['role_permissions'][$role];
						}
						?>
						<tr>
							<td width="100px"><?php echo ucfirst($role); ?></td>
							<td>
								<select name="wpbbrolepermissions<?php echo $role; ?>">
									<option value="yes" <?php selected($role_value, 'yes');?>><?php _e('Yes', 'wp-bb'); ?></option>
									<option value="no" <?php selected($role_value, 'no');?>><?php _e('No', 'wp-bb'); ?></option>
								</select>
							</td>
						</tr>
						<?php
					}
				}
			}
			?>
			<tr>
				<td>
					<input type="submit" name="wpbb-role-permissions-save" class="button-secondary" value="Save Role Permissions" />
				</td>
			</tr>
		</table>
	</form>
</div>