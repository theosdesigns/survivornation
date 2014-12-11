<?php

global $wpdb;

?>
<div class="wrap">

<div id="icon-plugins" class="icon32"></div>


<h2><?php _e('Categories', 'wp-bb'); ?></h2>

<?php
/* 
	This code is run when you submit a new category
*/

if (isset($_POST['submit'])) {
	
	if(empty($_POST['wpbbcategoryname'])) {
		?>
		<div id='message' class='error'>
			<?php 
			_e('You must enter a category name!', 'wp-bb');
			exit();
			?>
		</div>
		<?php
	}
		
	$category_name = wp_strip_all_tags($_POST['wpbbcategoryname']);
		
	$order = absint($_POST['wpbbcategoryorder']);

	$data = array(
		'name' => $category_name,
		'forum' => 0,
		'subforum' => 0,
		'view' => '',
		'read' => '',
		'post' => '',
		'reply' => '',
		'edit' => '',
		'lock' => '',
		'delete' => '',
		'sticky' => '',
		'order' => $order
	);
	
	
	$create_category = $wpdb->insert(CATEGORY_TABLE, $data);
		
	if ($create_category === false) {
		?>
		<div id='message' class='error'>
			<?php _e('Error creating category, please try again!', 'wp-bb'); ?>
		</div>
		<?php
	} else {
		?>
		<div id='message' class='updated'>
			<?php _e('Category created successfully', 'wp-bb'); ?>
		</div>
		<?php
	}
} 


/*
	Creating a Category
*/

if (count($_GET) == 1) {
	$heading1 = __('Create a Category', 'wp-bb');
	$create_category = __('Create Category', 'wp-bb');
	?>
	<form action ="" method="POST" id="admin-create-category">
		<h3><?php echo $heading1; ?></h3>
		<table class="form-table">
			<tr>
				<th><?php _e('Name', 'wp-bb'); ?></th>
				<td><input type="text" name="wpbbcategoryname" id="wpbbcategoryname" maxlength="25" size="25" /> <br />
				<p class="description"><?php _e('The name of the category (max 30 characters)', 'wp-bb'); ?></p></td>
			</tr>
			<tr>
				<th><?php _e('Order', 'wp-bb'); ?></th>
				<td>
					<input type="number" name="wpbbcategoryorder" id="wpbbcategoryorder" maxlength="25" size="25" /> <br />
					<p class="description"><?php _e('The order in which the category will appear', 'wp-bb'); ?></p>
				</td> 				
			</tr>
			<tr>
				<td><input type="submit" name="submit" class="button-primary" value="<?php echo $create_category; ?>" /></td>
			</tr>
		</table>
	</form>
<?php
}


if (isset($_POST['edit'])) {
	
	$id = absint($_POST['id']);
	
	$name = wp_strip_all_tags($_POST['wpbbcategoryname']);
	
	$order = absint($_POST['wpbbcategoryorder']);
	
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
		
		// This is a category
		$forum_id = 0;
		$subforum_id = 0;
		
		$view_permissions = implode(",", $permissions['view']);
		$read_permissions = implode(",", $permissions['read']);
		$post_permissions = implode(",", $permissions['post']);
		$reply_permissions = implode(",", $permissions['reply']);
		$edit_permissions = implode(",", $permissions['edit']);
		$lock_permissions = implode(",", $permissions['lock']);
		$delete_permissions = implode(",", $permissions['delete']);
		$sticky_permissions = implode(",", $permissions['sticky']);
					
		$data = array(
			'name' => $name,
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
		
		$update_forum = $wpdb->update(CATEGORY_TABLE, $data, array('id' => $id));
		
		$update_topics = $wpdb->update(TOPICS_TABLE, array('forum' => $forum_id, 'subforum' => $subforum_id), array('forum' => $id, 'subforum' => $id));
		
		if ($update_forum) {
			?>
			<div id='message' class='updated'>
				<?php printf(__('Category ID %d updated successfully', 'wp-bb'), $id); ?>
			</div>
			<?php
		} else {
			?>
			<div id='message' class='error'>
				<?php printf(__('Error updating Category ID %d', 'wp-bb'), $id); ?>
			</div>
			<?php
		}
	}
}

if (isset($_POST['wpbb-confirm-delete-submit'])) {
	
	if ($_POST['wpbb-confirm-delete'] == 'yes') {
		
		$category_id = absint($_POST['wpbb-confirm-delete-id']);
		
		$category_forums = $wpdb->get_results("SELECT id FROM ".CATEGORY_TABLE." WHERE forum = $category_id;");
		
		if ($category_forums) {
			foreach ($category_forums as $forum) {
				$forum_topics = $wpdb->get_results("SELECT id FROM ".TOPICS_TABLE." WHERE forum = $forum_id;");
				if ($forum_topics) {
					foreach ($forum_topics as $topic) {
						$topics_posts = $wpdb->get_results("SELECT id FROM ".POSTS_TABLE." WHERE topic = $topic->id;");
						if ($topics_posts) {
							foreach ($topics_posts as $post) {
								$delete_post = $wpdb->query("DELETE FROM ".POSTS_TABLE." WHERE id = $post->id;");
							}
						}
						$delete_topic = $wpdb->query("DELETE FROM ".TOPICS_TABLE." WHERE id = $topic->id;");
					}
				}
				$forums_subforums = $wpdb->get_results("SELECT id FROM ".CATEGORY_TABLE." WHERE subforum = $forum->id;");
				if ($forums_subforums) {
					foreach ($forums_subforums as $subforum) {
						$subforum_topics = $wpdb->get_results("SELECT id FROM ".TOPICS_TABLE." WHERE subforum = $subforum->id;");
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
					}
					$delete_subforum = $wpdb->query("DELETE FROM ".CATEGORY_TABLE." WHERE id = $subforum->id;");
				}
				$delete_forum = $wpdb->query("DELETE FROM ".CATEGORY_TABLE." WHERE id = $forum->id;");
			}
		}
				
		$delete = $wpdb->query("DELETE FROM ".CATEGORY_TABLE." WHERE id = $category_id;");
		
		if ($delete === false) {
			?>
			<div id='message' class='error'>
				<?php printf(__('There was an error deleting Category ID %d, please try again.', 'wp-bb'),$category_id); ?>
			</div>
			<?php
		} else {
			?>
			<div id='message' class='updated'>
				<?php printf(__('Category ID %d deleted successfully.', 'wp-bb'), $category_id); ?>
			</div>
			<?php
		}
	
	} else if ($_POST['wpbb-confirm-delete'] == 'no') {
		
		wp_redirect('admin.php?page=wpbb_admin');
		
		exit;
	
	}
}
	

if (isset($_GET['delete'])) {
	
	$id = absint($_GET['id']);

	?>
	<h3><?php _e('Confirm Delete Request', 'wp-bb'); ?></h3>

	<?php printf(__('Are you sure you want to delete Category ID %d? WARNING: This will delete all forums and subforums along with any topics and posts contained within them.', 'wp-bb'), $id); ?>
	
	<form method='POST' action='#'>
		<input type='radio' name='wpbb-confirm-delete' value='yes' checked='no' /> <?php _e('Yes', 'wp-bb'); ?>
		<input type='radio' name='wpbb-confirm-delete' value='no' checked='no' /> <?php _e('No', 'wp-bb'); ?>
		<input type='hidden' name='wpbb-confirm-delete-id' value='<?php echo $id; ?>' />
		<input type='submit' name='wpbb-confirm-delete-submit' value='<?php _e('Confirm', 'wp-bb'); ?>' />
	</form>
	<?php
	return;
}
	

if (isset($_GET['edit'])) {
	
	$id = absint($_GET['id']);
		
	$type = $wpdb->get_row("SELECT * FROM ".CATEGORY_TABLE." WHERE id = $id;");
	
	echo "<h3>".__('Edit Category', 'wp-bb')."</h3>";
	
	?>
	<form method="POST" action="">
		<table class="form-table">
			<tr>
				<th><?php _e('Name', 'wp-bb'); ?></th>
				<td>
					<input type="text" name="wpbbcategoryname" maxlength="25" size="25" value="<?php echo $type->name;?>" />
					<p class="description"><?php _e('The name of the category (max 30 characters)', 'wp-bb'); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Order', 'wp-bb'); ?></th>
				<td>
					<input type="number" name="wpbbcategoryorder" maxlength="45" size="25" value="<?php echo $type->order; ?>" />
					<p class="description"><?php _e('The order in which the category will appear', 'wp-bb'); ?></p>
				</td>	
			</tr>
			<tr>
				<td>
					<input type="hidden" name="id" value="<?php echo $type->id;?>"/>
					<input type="submit" name="edit" class="button-primary" value="<?php _e('Confirm Changes', 'wp-bb'); ?>" />	
				</td	
			</tr>	
		</table>
	</form
	<?php
}
?>
</div>