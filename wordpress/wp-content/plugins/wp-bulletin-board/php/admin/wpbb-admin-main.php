<?php

global $wpdb;

if (isset($_GET['increase_order']) && !isset($_GET['decrease_order'])) {

	$type = wp_strip_all_tags($_GET['increase_order']);

	$id = absint($_GET['id']);
	
	$check_type_exists = $wpdb->get_row("SELECT id FROM ".CATEGORY_TABLE." WHERE id = $id;");
	
	if ($check_type_exists !== NULL) {
	
		$order = absint($_GET['order']);
	
		$new_order = $order + 1;
	
		$increase_order = $wpdb->update(CATEGORY_TABLE, array('order' => $new_order), array('id' => $id));
	
		if ($increase_order === false) { // Error
			switch ($type) {
				case 'category':
				?>
				<div id="message" class="error">
					<?php
					printf(__('There was an error increasing Category ID %s order'), $id);
					?>
				</div>
				<?php
				break;
			
				case 'forum':
				?>
				<div id="message" class="error">
					<?php
					printf(__('There was an error increasing Forum ID %s order'), $id);
					?>
				</div>
				<?php
				break;
			
				case 'subforum':
				?>
				<div id="message" class="error">
					<?php
					printf(__('There was an error increasing Subforum ID %s order'), $id);
					?>
				</div>
				<?php
				break;
			
				default:
				echo "<div id='message' class='error'>".__('Error. Please try again.', 'wp-bb')."</div>";
				break;
			}
		} else { // Success
			switch ($type) {
				case 'category':
				?>
				<div id="message" class="updated">
					<?php
					printf(__('Category ID %s was successfully increased in order'), $id);
					?>
				</div>
				<?php
				break;
			
				case 'forum':
				?>
				<div id="message" class="updated">
					<?php
					printf(__('Forum ID %s was successfully increased in order'), $id);
					?>	
				</div>
				<?php
				break;
			
				case 'subforum':
				?>
				<div id="message" class="updated">
					<?php
					printf(__('Subforum ID %s was successfully increased in order'), $id);
					?>
				</div>
				<?php
				break;
			
				default:
				echo "<div id='message' class='error'>".__('Error. Please try again.', 'wp-bb')."</div>";
				break;
			}
		}
	} else {
		
		?>
		<div id="message" class="updated">
			<?php
			printf(__('Error: %s ID %s doesn\'t exist. Please try again.'), ucfirst($type), $id);
			?>
		</div>
		<?php
	}
} 

if (isset($_GET['decrease_order']) && !isset($_GET['increase_order'])) {

	$type = wp_strip_all_tags($_GET['decrease_order']);

	$id = absint($_GET['id']);
	
	$order = absint($_GET['order']);
	
	$new_order = $order - 1;
	
	$decrease_order = $wpdb->update(CATEGORY_TABLE, array('order' => $new_order), array('id' => $id));
	
	if ($decrease_order === false) { // Error
		switch ($type) {
			case 'category':
			?>
			<div id="message" class="error">
				<?php
				printf(__('There was an error decreasing Category ID %s order'), $id);
				?>
			</div>
			<?php
			break;
			
			case 'forum':
			?>
			<div id="message" class="error">
				<?php
				printf(__('There was an error decreasing Forum ID %s order'), $id);
				?>
			</div>
			<?php
			break;
			
			case 'subforum':
			?>
			<div id="message" class="error">
				<?php
				printf(__('There was an error decreasing Subforum ID %s order'), $id);
				?>
			</div>
			<?php
			break;
			
			default:
			echo "<div id='message' class='error'>".__('Error. Please try again.', 'wp-bb')."</div>";
			break;
		}
	} else { // Success
		switch ($type) {
			case 'category':
			?>
			<div id="message" class="updated">
				<?php
				printf(__('Category ID %s was successfully decreased in order'), $id);
				?>
			</div>
			<?php
			break;
			
			case 'forum':
			?>
			<div id="message" class="updated">
				<?php
				printf(__('Forum ID %s was successfully decreased in order'), $id);
				?>
			</div>
			<?php
			break;
			
			case 'subforum':
			?>
			<div id="message" class="updated">
				<?php
				printf(__('Subforum ID %s was successfully decreased in order'), $id);
				?>
			</div>
			<?php
			break;
			
			default:
			echo "<div id='message' class='error'>".__('Error. Please try again.', 'wp-bb')."</div>";
			break;
		}
	}
}
	
$mywpbb = get_option('wpbb_options');
?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>
		<?php printf('<a href="%s">%s', wpbb_admin_permalink(), $mywpbb['forum_name']); ?>
		<a href="admin.php?page=wpbb_admin_categories" class="add-new-h2"><?php _e('New Category', 'wp-bb'); ?></a>
		<a href="admin.php?page=wpbb_admin_forums" class="add-new-h2"><?php _e('New Forum', 'wp-bb'); ?></a>
		<a href="admin.php?page=wpbb_admin_subforums" class="add-new-h2"><?php _e('New Subforum', 'wp-bb'); ?></a>
		<a href="admin.php?page=wpbb_admin_topics" class="add-new-h2"><?php _e('New Topic', 'wp-bb'); ?></a>
		<a href="admin.php?page=wpbb_admin_tools" class="add-new-h2"><?php _e('Tools', 'wp-bb'); ?></a>
	</h2>
	<?php
	$admin_url = admin_url()."/admin.php?page=wpbb_admin";
	$categories = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum = 0 AND subforum = 0 ORDER BY `order`;");
	if ($wpdb->num_rows === 0) {
		?>
		<div class="wpbb-centered">
			<?php _e('There are no categories, forums or subforums yet. Why not start by creating one?', 'wp-bb'); ?>
		</div>
		<?php
	}
	if($categories === false) {
		?>
		<div class="wpbb-message-failure">
			<?php _e('There was an error retrieving the categories', 'wp-bb'); ?>
		</div>
		<?php 
	}
	if ($categories) {
		?>
		<table class="widefat">
			<th><?php _e('Name', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Category', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Forum', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Subforum', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Topics', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Posts', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Order', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<th><?php _e('Action', 'wpbb-admin-overview', 'wp-bb'); ?></th>
			<?php
			foreach ($categories as $category) {
				$category_increase_order = add_query_arg(array('increase_order' => 'category', 'id' => $category->id, 'order' => $category->order), $admin_url);
				$category_decrease_order = add_query_arg(array('decrease_order' => 'category', 'id' => $category->id, 'order' => $category->order), $admin_url);
				$category_edit_link = 'admin.php?page=wpbb_admin_categories&edit=category&id='.$category->id.'';
				$category_delete_link = 'admin.php?page=wpbb_admin_categories&delete=category&id='.$category->id.'';
				?>
				<tr>
					<th>
						<?php echo $category->name; ?>
					</th>
					<th>
						<input type="checkbox"name="wpbb-category" checked="no" disabled="disabled" />
					</th>
					<th colspan='4'>
					</th>
					<th>
						<?php echo $category->order; ?>
					</th>
					<th>
						<a href="<?php echo $category_increase_order; ?>" class="button"><?php _e('Increase Order', 'wp-bb'); ?></a>
						<a href="<?php echo $category_decrease_order; ?>" class="button"><?php _e('Decrease Order', 'wp-bb'); ?></a>
						<a href="<?php echo $category_edit_link; ?>" class="button"><?php _e('Edit', 'wp-bb'); ?></a>
						<a href="<?php echo $category_delete_link; ?>" class="button"><?php _e('Delete', 'wp-bb'); ?></a>
					</th>
				</tr>
				<?php
				$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum = $category->id ORDER BY `order`;");
				if ($forums) {
					foreach ($forums as $forum) {
						$forum_link = wpbb_get_forum_link($forum->id);
						$forum_content = wpbb_admin_get_topics_posts($forum->id);
						$forum_increase_order = add_query_arg(array('increase_order' => 'forum', 'id' => $forum->id, 'order' => $forum->order), $admin_url);
						$forum_decrease_order = add_query_arg(array('decrease_order' => 'forum', 'id' => $forum->id, 'order' => $forum->order), $admin_url);
						$forum_edit_link = 'admin.php?page=wpbb_admin_forums&edit=forum&id='.$forum->id.'';
						$forum_delete_link = 'admin.php?page=wpbb_admin_forums&delete=forum&id='.$forum->id.'';
						?>
						<tr>
							<td>
								<a href="<?php echo $forum_link; ?>"><?php echo $forum->name; ?></a>
							</td>
							<td>
							</td>
							<td>
								<input type="checkbox" name="wpbb-forum" checked="yes" disabled="disabled" /></td>
							<td>
							</td>
							<td>
								<?php echo $forum_content['topics']; ?>
							</td>
							<td>
								<?php echo $forum_content['posts']; ?>
							</td>
							<td>
								<?php echo $forum->order; ?>
							</td>
							<td>
								<a href='<?php echo $forum_increase_order; ?>' class='button'><?php _e('Increase Order', 'wp-bb'); ?></a>
								<a href='<?php echo $forum_decrease_order; ?>' class='button'><?php _e('Decrease Order', 'wp-bb'); ?></a>
								<a href='<?php echo $forum_edit_link; ?>' class='button'><?php _e('Edit', 'wp-bb'); ?></a>
								<a href='<?php echo $forum_delete_link; ?>' class='button'><?php _e('Delete', 'wp-bb'); ?></a>
							</td>
						</tr>
						<?php
						$subforums = $subforums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE subforum = $forum->id ORDER BY `order`;");
						if ($subforums) {
							foreach ($subforums as $subforum) {
								$subforum_link = wpbb_get_forum_link($subforum->id, 'subforum');
								$subforum_content = wpbb_admin_get_topics_posts($subforum->id);
								$subforum_increase_order = add_query_arg(array('increase_order' => 'subforum', 'id' => $subforum->id, 'order' => $subforum->order), $admin_url);
								$subforum_decrease_order = add_query_arg(array('decrease_order' => 'subforum', 'id' => $subforum->id, 'order' => $subforum->order), $admin_url);
								$subforum_edit_link = 'admin.php?page=wpbb_admin_subforums&edit=subforum&id='.$subforum->id.'';
								$subforum_delete_link = 'admin.php?page=wpbb_admin_subforums&delete=subforum&id='.$subforum->id.'';
								?>
								<tr>
								<td>
									<a href='<?php echo $subforum_link; ?>'><?php echo $subforum->name; ?></a>
								</td>
								<td>
								</td>
								<td>
								</td>
								<td>
									<input type='checkbox' name='wpbb-forum' checked='yes' disabled='disabled' /></td>
								<td>
									<?php echo $subforum_content['topics']; ?></td>
								<td>
									<?php echo $subforum_content['posts']; ?></td>
								<td>
									<?php echo $subforum->order; ?></td>
								<td>
									<a href='<?php echo $subforum_increase_order; ?>' class='button'><?php echo __('Increase Order', 'wp-bb'); ?></a>
									<a href='<?php echo $subforum_decrease_order; ?>' class='button'><?php echo __('Decrease Order', 'wp-bb'); ?></a>
									<a href='<?php echo $subforum_edit_link; ?>' class='button'><?php echo __('Edit', 'wp-bb'); ?></a>
									<a href='<?php echo $subforum_delete_link; ?>' class='button'><?php echo __('Delete', 'wp-bb'); ?></a></td>
								</tr>
								<?php
							}
						}
					}
				}
			}
		?>
		</table>
		<?php
	}
?>