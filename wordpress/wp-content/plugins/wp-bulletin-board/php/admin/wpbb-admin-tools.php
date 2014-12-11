<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h2><?php _e('Tools', 'wp-bb'); ?></h2>
	<table class="form-table">
		<form action="#" method="POST">
			<tr>
				<td>
					<h3><?php _e('Refresh Roles', 'wp-bb'); ?><p class="description">If you've recently changed your WP roles then click "Refresh Roles" so that these roles are registered with WPBB</p></h3>
					<input type="submit" name="wpbb-tools-role-refresh" class="button-secondary" value="Refresh Roles" />
				</td>
			</tr>
		</form>
		<form action="#" method="POST">
			<tr>
				<td>
					<h3><?php _e('Recreate Forum Page', 'wp-bb'); ?><p class="description">Use this to restore the WPBB forum page if it was accidentally deleted or wasn't created</p>
</h3>
					
					<input type="submit" name="wpbb-tools-recreate-page" class="button-secondary" value="Recreate Page" />
				</td>
			</tr>
		</form>
	</table>
</div>

<?php

if (isset($_POST['wpbb-tools-role-refresh']))
{
	 wpbb_refresh_roles(true);
}

if (isset($_POST['wpbb-tools-recreate-page']))
{
	wpbb_admin_recreate_page();
}

?>