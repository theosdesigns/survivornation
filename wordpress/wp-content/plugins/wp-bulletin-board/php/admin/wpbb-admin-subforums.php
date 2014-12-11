<?php

global $wpdb;

?>

<div class='wrap'>

<div id='icon-plugins' class='icon32'></div>

<h2><?php _e('Subforums', 'wp-bb'); ?></h2>

<?php

/*
	Creating a Subforum
*/

if (count($_GET) == 1) {
	?>
	<form action ="" method="POST" id="admin-create-forum">
		<h3><?php _e('Create a Subforum', 'wp-bb'); ?></h3>
			<table class="form-table">
				<tr>
					<th><?php _e('Name', 'wp-bb'); ?></th>
					<td>
						<input type="text" name="wpbbsubforumname" id="wpbbsubforumname" maxlength="30" size="30" />
						<p class="description"><?php _e('Subforum name (max 30 characters)', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Description', 'wp-bb'); ?></th>
					<td>
						<textarea name="wpbbsubforumdescription" cols="30" rows="2"></textarea>
						<p class="description"><?php _e('The subforum description, shown below the subforum name on the forum index (max 200 characters)', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Forum', 'wp-bb'); ?></th>
					<td>
						<select name="wpbbsubforumforumname" id="wpbbsubforumforumname">
						<?php
						$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum > 0 && subforum = 0;");
						if ($forums) {
							foreach ($forums as $forum) {
							?>
							<option value="<?php echo $forum->id; ?>"><?php echo $forum->name;?></option>
							<?php
							}
						}
						?>
						</select>
						<p class="description"><?php _e('The forum to place this subforum into', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Copy Permissions', 'wp-bb'); ?></th>
					<td>
						<select name="wpbbforums">
						<option value="None">None</option>
						<?php 
						$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum > 0 || subforum > 0 LIMIT 10;");
						if ($forums) {
							foreach ($forums as $forum) {
								if ($forum->forum > 0) {
									?>
									<option value="<?php echo $forum->id; ?>"><?php echo $forum->name;?></option>
									<?php
								} else if ($forum->subforum > 0) {
									?>
									<option value="<?php echo $forum->id; ?>"><?php printf(__('%s [Subforum]'), $forum->name); ?></option>
									<?php
								}
							}
						}
						?>
						</select>
						<p class="description"><?php _e('Copy permissions from another forum or subforum or enter subforum permissions manually below', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Permissions', 'wp-bb'); ?></th>
					<td>
					<table class='widefat'>
						<th></th>
						<th><a href="#" id="wpbbadvancedpermissionstableview"><?php _e('View', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstableread"><?php _e('Read', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablepost"><?php _e('Post', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablereply"><?php _e('Reply', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstableedit"><?php _e('Edit', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablelock"><?php _e('Lock', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablesticky"><?php _e('Sticky', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstabledelete"><?php _e('Delete', 'wp-bb'); ?></a></th>
						<?php
						$roles = wpbb_admin_get_all_roles();
						if ($roles) {
							foreach ($roles as $role) {
								?>
								<tr>
								<td>
									<?php 
									if ($role == 'guest') {
										?>
										<strong><?php _e('Guest', 'wp-bb'); ?></strong>
										<?php
									} else {
										echo ucfirst($role);
									}
									?>
								</td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>view' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>view' /></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>read' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>read' /></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>post' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>post' /></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>reply' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>reply'/></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>edit' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>edit' /></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>lock'  type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>lock' /></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>sticky' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>sticky' /></td>
								<td><input name='wpbbadvancedpermissionstable<?php echo $role;?>delete' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role;?>delete' /></td>
								</tr>
								<?php
							}
						}
						?>
					</table>
				</td>
			</td>
		</tr>
		<tr>
			<th><?php _e('Order', 'wp-bb'); ?></th>
			<td>
				<input type="number" name="wpbbsubforumorder" id="wpbbsubforumorder" maxlength="255" size="25" />
				<p class="description"><?php _e('The order in which the subforum is placed', 'wp-bb'); ?></p>
			</td> 				
		</tr>
		<tr>
			<td><input type="submit" name="submit" class="button-primary" value="<?php _e('Create Subforum', 'wp-bb'); ?>" /></td>
		</tr>
	</table>
</form>
<?php } ?>
</div>
<?php

/*
	Submitting a new Subforum
*/
	
if (isset($_POST['submit'])) {

	if(!empty($_POST['wpbbsubforumname']) || !empty($_POST['wpbbsubforumorder'])) {
	
		$subforum_name = wpbb_admin_strip_tags($_POST['wpbbsubforumname']);
		
		$subforum_desc = wpbb_admin_strip_tags($_POST['wpbbsubforumdescription']);

		$subforum_forum = absint($_POST['wpbbsubforumforumname']);
		
		if (isset($_POST['wpbbforumorder'])) {
			$order = absint($_POST['wpbbforumorder']);
		} else {
			$order = 1;
		}
		
		// Copied permissions from another forum
		if ((isset($_POST['wpbbforums']))
			&& ($_POST['wpbbforums'] != 'None')) {
		
			$forum_id = absint($_POST['wpbbforums']);
		
			$forums_permissions = $wpdb->get_row("SELECT `view`, `read`, `post`, `reply`, `edit`, `lock`, `delete`, `sticky` FROM ".CATEGORY_TABLE." WHERE id = $forum_id;");
			
			if ($forums_permissions) {
			
				$view_permissions = $forums_permissions->view;				
				$read_permissions = $forums_permissions->read;
				$post_permissions = $forums_permissions->post;
				$reply_permissions = $forums_permissions->reply;
				$edit_permissions = $forums_permissions->edit;
				$lock_permissions = $forums_permissions->lock;
				$delete_permissions = $forums_permissions->delete;
				$sticky_permissions = $forums_permissions->sticky;
			}
		
		} else {
		
			$roles = wpbb_admin_get_all_roles();
		
			if ($roles) {
		
				$permissions = array('view' => array(), 'read' => array(), 'post' => array(), 'reply' => array(), 'edit' => array(), 'lock' => array(), 'delete' => array(), 'sticky' => array());
			
				foreach ($roles as $role) {
			
					if (isset($_POST['wpbbadvancedpermissionstable'.$role.'view'])) {
						$permissions['view'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'read'])) {
						$permissions['read'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'post'])) {
						$permissions['post'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'reply'])) {
						$permissions['reply'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'edit'])) {
						$permissions['edit'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'lock'])) {
						$permissions['lock'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'delete'])) {
						$permissions['delete'][] = $role;
					} if (isset($_POST['wpbbadvancedpermissionstable'.$role.'sticky'])) {
						$permissions['sticky'][] = $role;
					}
				}
			
				$view_permissions = implode(",", $permissions['view']);
				$read_permissions = implode(",", $permissions['read']);
				$post_permissions = implode(",", $permissions['post']);
				$reply_permissions = implode(",", $permissions['reply']);
				$edit_permissions = implode(",", $permissions['edit']);
				$lock_permissions = implode(",", $permissions['lock']);
				$delete_permissions = implode(",", $permissions['delete']);
				$sticky_permissions = implode(",", $permissions['sticky']);
			}
		}
		
		$data = array(
			'name' => $subforum_name,
			'description' => $subforum_desc,
			'forum' => 0,
			'subforum' => $subforum_forum,
			'view' => $view_permissions,
			'read' => $read_permissions,
			'post' => $post_permissions,
			'reply' => $reply_permissions,
			'edit' => $edit_permissions,
			'lock' => $lock_permissions,
			'delete' => $delete_permissions,
			'sticky' => $sticky_permissions,
			'order' => $order
		);
		
		$create_subforum = $wpdb->insert(CATEGORY_TABLE, $data);
		
		if ($create_subforum === false) {
			
			?>
			<div id='message' class='error'>
				<?php
				_e('Error creating subforum. It is likely your roles haven\'t been updated (WPBB -> Tools -> Refresh Roles)', 'wp-bb');
				?>
			</div>
			<?php
		
		} else {
			
			?>
			<div id='message' class='updated'>
				<?php
				_e('Subforum created successfully', 'wp-bb');
				?>
			</div>
			<?php
		
		}
	
	} else {
		
		?>
		<div id='message' class='error'>
			<?php
			_e('Please enter a name for the subforum!', 'wp-bb');
			?>
		</div>
		<?php
	
	}

}


/*
	Editing a Subforum
*/

if (isset($_GET['edit'])) {

		$id = absint($_GET['id']);
		
		$type = $wpdb->get_row("SELECT * FROM ".CATEGORY_TABLE." WHERE id = $id;");
		
		?>
		
		<h3><?php _e('Edit Subforum', 'wp-bb'); ?></h3>
		
		<form method="POST" action="">
				<table class="form-table">
					<tr>
						<th><?php _e('Name', 'wp-bb'); ?></th>
						<td>
							<input type="text" name="wpbbsubforumname" maxlength="30" size="30" value="<?php echo $type->name;?>" />
							<p class="description"><?php _e('Subforum name (max 30 characters)', 'wp-bb'); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php _e('Description', 'wp-bb'); ?></th>
						<td>
							<?php
							if ($wp_version == "3.2")
							{
								the_editor("", "wpbbsubforumdescription");
							}
							else
							{
								wp_editor($type->description, 'wpbbsubforumdescription');
							}
							?>
							<p class="description"><?php _e('The subforum description, shown below the subforum name on the forum index (max 200 characters)', 'wp-bb'); ?></p>
						</td>
					</tr>
					<tr>
					<th><?php _e('Forum', 'wp-bb'); ?></th>
					<td>
						<select name="wpbbsubforumforumname">
						<?php
						$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum > 0 && subforum = 0;");
						$this_subforum_forum = $wpdb->get_var("SELECT subforum FROM ".CATEGORY_TABLE." WHERE id = $id;");
						if ($forums) {
							foreach ($forums as $forum) {
							?>
							<option value="<?php echo $forum->id; ?>" <?php selected($forum->id, $this_subforum_forum);?> ><?php echo $forum->name;?></option>
							<?php
							}
						}
						?>
						</select>
						<p class="description"><?php _e('The forum to place the subforum into', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Copy Permissions', 'wp-bb'); ?></th>
					<td>
						<select name="wpbbforums">
						<option value="None">None</option>
						<?php 
						$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum > 0 || subforum > 0 LIMIT 10;");
						if ($forums) {
							foreach ($forums as $forum) {
								if ($forum->forum > 0) {
									?>
									<option value="<?php echo $forum->id; ?>"><?php echo $forum->name;?></option>
									<?php
								} else if ($forum->subforum > 0) {
									?>
									<option value="<?php echo $forum->id; ?>"><?php printf(__('%s [Subforum]'), $forum->name); ?></option>
									<?php
								}
							}
						}
						?>
						</select>
						<p class="description"><?php _e('Copy permissions from another forum or subforum or enter subforum permissions manually below', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Permissions', 'wp-bb'); ?></th>
					<td>
					<table class='widefat'>
						<th></th>
						<th><a href="#" id="wpbbadvancedpermissionstableview"><?php _e('View', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstableread"><?php _e('Read', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablepost"><?php _e('Post', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablereply"><?php _e('Reply', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstableedit"><?php _e('Edit', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablelock"><?php _e('Lock', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstablesticky"><?php _e('Sticky', 'wp-bb'); ?></a></th>
						<th><a href="#" id="wpbbadvancedpermissionstabledelete"><?php _e('Delete', 'wp-bb'); ?></a></th>
						<?php
						$roles = wpbb_admin_get_all_roles();
						$permissions = $wpdb->get_results("SELECT `view`, `read`, `post`, `reply`, `edit`, `lock`, `delete`, `sticky` FROM ".CATEGORY_TABLE." WHERE id = $id;");
						if ($roles) {
							foreach ($roles as $role) {
								foreach ($permissions as $permission) {
									if (strpos($permission->view, $role) !== false) $view_checked = "checked='yes'"; else $view_checked = "";
									if (strpos($permission->read, $role) !== false) $read_checked = "checked='yes'"; else $read_checked = "";
									if (strpos($permission->post, $role) !== false) $post_checked = "checked='yes'"; else $post_checked = "";
									if (strpos($permission->reply, $role) !== false) $reply_checked = "checked='yes'"; else $reply_checked = "";
									if (strpos($permission->edit, $role) !== false) $edit_checked = "checked='yes'"; else $edit_checked = "";
									if (strpos($permission->lock, $role) !== false) $lock_checked = "checked='yes'"; else $lock_checked = "";
									if (strpos($permission->sticky, $role) !== false) $sticky_checked = "checked='yes'"; else $sticky_checked = "";
									if (strpos($permission->delete, $role) !== false) $delete_checked = "checked='yes'"; else $delete_checked = "";
									break;
								}
								?>
								<tr>
									<td>
										<?php 
											if ($role == 'guest') {
												?>
												<strong><?php _e('Guest', 'wp-bb'); ?></strong>
												<?php
											} else {
												echo ucfirst($role);
											}
										?>
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>view' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>view' <?php echo $view_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>read' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>read' <?php echo $read_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>post' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>post' <?php echo $post_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>reply' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>reply' <?php echo $reply_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>edit' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>edit' <?php echo $edit_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>lock'  type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>lock' <?php echo $lock_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>sticky' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>sticky' <?php echo $sticky_checked; ?> />
									</td>
									<td>
										<input name='wpbbadvancedpermissionstable<?php echo $role; ?>delete' type='checkbox' value='wpbbadvancedpermissionstable<?php echo $role; ?>delete' <?php echo $delete_checked; ?> />
									</td>
								</tr>
								<?php
							}
						}
						?>
					</table></td>
				</tr>
				<tr>
					<th><?php _e('Order', 'wp-bb'); ?></th>
					<td>
						<input type="number" name="wpbbsubforumorder" maxlength="255" size="25" value="<?php echo $type->order; ?>" />
						<p class="description"><?php _e('The order in which the subforum is placed', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>	
					<td>	
						<input type="hidden" name="id" value="<?php echo $type->id;?>"/>
						<input type="submit" name="edit" class="button-primary" value="<?php _e('Save Changes', 'wp-bb'); ?>" />
					</td>
				</tr>
			</table>
		</form>
		<?php
	}
	
/*
	Submitting an Edited Subforum
*/

if (isset($_POST['edit'])) {

	$id = absint($_POST['id']);
	
	$name = wpbb_admin_strip_tags($_POST['wpbbsubforumname']);
	
	$description = wpbb_admin_strip_tags($_POST['wpbbsubforumdescription']);
	
	$forum_id = 0;
			
	$subforum_id = absint($_POST['wpbbsubforumforumname']);
		
	$order = absint($_POST['wpbbsubforumorder']);
	
	if ((isset($_POST['wpbbforums']))
		&& ($_POST['wpbbforums'] != 'None')) {
		
		$forum_id = absint($_POST['wpbbforums']);
		
		$forums_permissions = $wpdb->get_row("SELECT `view`, `read`, `post`, `reply`, `edit`, `lock`, `delete`, `sticky` FROM ".CATEGORY_TABLE." WHERE id = $forum_id;");
			
		if ($forums_permissions) {
			
			$view_permissions = $forums_permissions->view;				
			$read_permissions = $forums_permissions->read;
			$post_permissions = $forums_permissions->post;
			$reply_permissions = $forums_permissions->reply;
			$edit_permissions = $forums_permissions->edit;
			$lock_permissions = $forums_permissions->lock;
			$delete_permissions = $forums_permissions->delete;
			$sticky_permissions = $forums_permissions->sticky;
		}
		
	} else {
	
		$roles = wpbb_admin_get_all_roles();
	
		if ($roles) {
			
			$permissions = array('view' => array(), 'read' => array(), 'post' => array(), 'reply' => array(), 'edit' => array(), 'lock' => array(), 'delete' => array(), 'sticky' => array());
			
			foreach ($roles as $role) {
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'view'])) {
					$permissions['view'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'read'])) {
					$permissions['read'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'post'])) {
					$permissions['post'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'reply'])) {
					$permissions['reply'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'edit'])) {
					$permissions['edit'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'lock'])) {
					$permissions['lock'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'delete'])) {
					$permissions['delete'][] = $role;
				} 
			
				if (isset($_POST['wpbbadvancedpermissionstable'.$role.'sticky'])) {
					$permissions['sticky'][] = $role;
				}
			}
			$view_permissions = implode(",", $permissions['view']);
			$read_permissions = implode(",", $permissions['read']);
			$post_permissions = implode(",", $permissions['post']);
			$reply_permissions = implode(",", $permissions['reply']);
			$edit_permissions = implode(",", $permissions['edit']);
			$lock_permissions = implode(",", $permissions['lock']);
			$delete_permissions = implode(",", $permissions['delete']);
			$sticky_permissions = implode(",", $permissions['sticky']);
		}
	}
					
	$data = array(
		'name' => $name,
		'description' => $description,
		'forum' => $forum_id,
		'subforum' => $subforum_id,
		'view' => $view_permissions,
		'read' => $read_permissions,
		'post' => $post_permissions,
		'reply' => $reply_permissions,
		'edit' => $edit_permissions,
		'lock' => $lock_permissions,
		'delete' => $delete_permissions,
		'sticky' => $sticky_permissions,
		'order' => $order
	);
		
	$update_subforum = $wpdb->update(CATEGORY_TABLE, $data, array('id' => $id));
	
	if ($update_subforum) {
		?>
		<div id='message' class='updated'>
			<?php
				printf(__('Subforum ID %s updated succesfully'), $id);
			?>
		</div>
		<?php
	} else {
		?>
		<div id='message' class='error'>
			<?php
				printf(__('Error updating Subforum ID %s. It is likely your roles haven\'t been updated (WPBB -> Tools -> Refresh Roles)'), $id);
			?>
		</div>
		<?php
	}
}


/* 
	Deleting a Subforum (After Confirmation)
*/

if (isset($_POST['wpbb-confirm-delete-submit'])) {
	
	if ($_POST['wpbb-confirm-delete'] == 'yes') {
		
		$subforum_id = absint($_POST['wpbb-confirm-delete-id']);
		
		$subforum_exists = $wpdb->get_var("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $subforum_id;");
		
		if ($subforum_exists === NULL) {
			?>
			<div id="message" class="error">
			<?php
			printf(__('Subforum ID %s does not exist.'), $subforum_id);
			?>
			</div>
			<?php
			exit();
		}
		
		$subforum_topics = $wpdb->get_results("SELECT id FROM ".TOPICS_TABLE." WHERE subforum = $subforum_id;");
		
		if ($subforum_topics) {
			foreach ($subforum_topics as $topic) {
				$topics_posts = $wpdb->get_results("SELECT id FROM ".POSTS_TABLE." WHERE topic = $topic->id;");
				if ($topics_posts) {
					foreach ($topics_posts as $post) {
						$delete_post = $wpdb->query("DELETE FROM ".POSTS_TABLE." WHERE id = $post->id;");
					}
				}
				$delete_topic = $wpdb->query("DELETE FROM ".TOPICS_TABLE." WHERE id = $topic->id;");
			}
		}
				
		$delete = $wpdb->query("DELETE FROM ".CATEGORY_TABLE." WHERE id = $subforum_id;");
		
		if ($delete === false) {
			?>
			<div id="message" class="error">
			<?php
				printf(__('There was an error deleting Subforum ID %s. Please try again'), $subforum_id);
			?>
			</div>
			<?php
		} else {
			?>
			<div id="message" class="updated">
			<?php
			printf(__('Subforum ID %s deleted successfully.'), $subforum_id);
			?>
			</div>
			<?php
		}
	
	} else if ($_POST['wpbb-confirm-delete'] == 'no') {
		
		wp_redirect('admin.php?page=wpbb_admin');
	
		exit;
	}
}

/*
	Confirming a Delete Subforum request
*/

if (isset($_GET['delete'])) {
	
	$id = absint($_GET['id']);
	
	?>
	
	<h2><?php _e('Confirm Delete Request', 'wp-bb'); ?></h2>
	
	<h3><?php _e('Delete Subforum', 'wp-bb'); ?></h3>
	
	<?php printf(__('Are you sure you want to delete Subforum ID %s? WARNING: This will delete all topics and posts within the subforum.'), $id); ?>
		
	<form method='POST' action='#'>
		
	<input type='radio' name='wpbb-confirm-delete' value='yes' checked='no' /> <?php _e('Yes', 'wp-bb'); ?>

	<input type='radio' name='wpbb-confirm-delete' value='no' checked='no' /> <?php _e('No', 'wp-bb'); ?>
		
	<input type='hidden' name='wpbb-confirm-delete-id' value='<?php echo $id; ?>' />
		
	<input type='submit' name='wpbb-confirm-delete-submit' value='<?php _e('Confirm', 'wp-bb'); ?>' />
		
	</form>
	
	<?php
	
	return;
}

?>