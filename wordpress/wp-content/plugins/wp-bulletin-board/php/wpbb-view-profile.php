<?php


$user_id = wpbb_is_user_logged_in();


/*
 	Viewing a Profile
*/
	
$profile_id = absint($_GET['profile']);

$user = get_user_by('id', $profile_id);

if ($profile_id == $user_id)
{
	?>
	<h1 id="wpbb-h-1" class="wpbb-centered-bold">
		<?php _e('My Profile', 'wp-bb'); ?>
	</h1>
	<?php
}
else
{
	?>
	<h1 id="wpbb-h-1" class="wpbb-centered-bold">
		<?php printf(__('Viewing %s\'s Profile'), $user->display_name); ?>
	</h1>
	<?php
}

$roles = wpbb_get_user_roles($user);

$posts = get_user_meta($profile_id, 'wpbb_posts', true);
	
$signature = get_user_meta($profile_id, 'wpbb_signature', true);
	
if ($user) {
	if (isset($_POST['wpbb-signature-submit'])) {	
		$signature = wpbb_strip_tags($_POST['wpbb-signature']);	
		$add_user_meta = update_user_meta($profile_id, 'wpbb_signature', $signature);
		if ($add_user_meta === false) {	
			?>
			<div class="wpbb-message-failure">
				<?php _e('There was an error saving your signature, please try again', 'wp-bb'); ?>
			</div>
			<?php
		}
	}
}
?>

<div>
	<table id="wpbb-profile-table">
		<tr valign=top>
			<td id="wpbb-profile-table-name-avatar">
				<?php echo $user->display_name; ?> 
				<br />
				<?php echo get_avatar($profile_id); ?>
				<br />
				<a href="<?php echo add_query_arg(array('message' => $profile_id), wpbb_permalink()); ?>">
					<?php _e('Message', 'wp-bb');?>
				</a>
			</td>
			<td>
				<table id="wpbb-profile-table-stats">
					<tr>
						<th colspan=2>
							<b>Profile</b>
						</th>
					</tr>
					<tr>
						<td>
							<b>Role:</b>
						</td>
						<td>
							<?php echo ucfirst($roles); ?>
						</td>
					</tr>
						<td>
							<b>Posts:</b>
						</td>
						<td>
							<?php echo $posts; ?>
						</td>
					</tr>
					<?php
					if (!empty($user->user_url)) {
						?>
						<tr>
							<td>
								<b>Website:</b>
							</td>
							<td>
								<?php echo $user->user_url; ?>
							</td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td>
							<b>Signature:</b>
						</td>
						<td>
							<?php echo convert_smilies($signature); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table> 
</div>
			
<div class="clear"></div>
<div class="clear"></div>

<?php
if ($profile_id == $user_id) {
	?>
	<div id="wpbb-profile-signature">
		<form method="POST" action="#">
			<div>
				<h2><?php _e('Signature', 'wp-bb'); ?></h2>
			</div> 
			<div>
				<textarea name="wpbb-signature"><?php echo $signature; ?></textarea>
			</div>
			<div>
				<input type="submit" name="wpbb-signature-submit" value="<?php _e('Save Signature', 'wp-bb'); ?>" />
			</div>
		</form>
	</div>
	<?php
}

?>