<?php

global $wpdb;


if (isset($_POST['submit'])) {

	if (empty($_POST['wpbbtopicname'])) {
		wpbb_admin_error("You must enter a topic name!");
	}
			
	if (empty($_POST['wpbbtopiccontent'])) {
		wpbb_admin_error("The topic content cannot be blank!");
	}
			
	if (empty($_POST['wpbbtopicforumname'])) {
		wpbb_admin_error("You must select a forum or create a forum before creating a topic!");
	}
			
	global $wpdb;
			
	$topic_name = wp_strip_all_tags($_POST['wpbbtopicname']);
			
	if (!empty($_POST['wpbbtopicauthorname'])) {
		
		if (is_numeric($_POST['wpbbtopicauthorname'])) { // ID
			
			$author_id = absint($_POST['wpbbtopicauthorname']);
				
		} else if (is_string($_POST['wpbbtopicauthorname'])) { // Name
			
			$author_name = wp_strip_all_tags($_POST['wpbbtopicauthorname']);
			
			$author_id = wpbb_admin_parse_author_name(NULL, $author_name);
			
		}
			
	} else {
		
		$author_id = absint($_POST['wpbbtopicadminid']);
	}
			
	$topic_content = wpbb_admin_strip_tags($_POST['wpbbtopiccontent']);
			
	$forum_id = absint($_POST['wpbbtopicforumname']);
		
	$forum_type = $wpdb->get_row("SELECT id,forum,subforum FROM ".CATEGORY_TABLE." WHERE id = $forum_id;");
		
	if ($forum_type->forum > 0) {
		$topic_forum = $forum_id;
		$topic_subforum = 0;
	} elseif ($forum_type->subforum > 0) {
		$topic_subforum = $forum_id;
		$topic_forum = 0;
	}
			
	$date = $date = date("Y-m-d H:i:s");
	
	$status = (!empty($_POST['wpbbtopicstatus'])) ? implode(",", $_POST['wpbbtopicstatus']) : '';
			
	$data = array(
		'name' => $topic_name,
		'author' => $author_id,
		'content' => $topic_content,
		'forum' => $topic_forum,
		'subforum' => $topic_subforum,
		'status' => $status,
		'created' => $date,
		'last_reply' => $date
	);

	$create_topic = $wpdb->insert(TOPICS_TABLE, $data);
	if ($create_topic) {
		wpbb_admin_success('Topic created successfully');
	} else {
		wpbb_admin_error('Error creating new topic, please try again!');
	}
}


$admin_id = get_current_user_id();
$admin_userdata = get_userdata($admin_id);
?>
<div class="wrap">
<form action="" method="POST" id="adminCreateTopic">
			<div id="icon-plugins" class="icon32"></div>
			<h2><?php _e('Topics', 'wp-bb'); ?></h2>
			<h3><?php _e('Create a Topic', 'wp-bb'); ?></h3>
			<table class="form-table">
				<tr>
					<th><?php _e('Name', 'wp-bb'); ?></th>
					<td><input type="text" name="wpbbtopicname" id="wpbbtopicname" maxlength="35" size="35" /> <p class="description"><?php _e('Enter topic title (max 35 characters)', 'wp-bb'); ?></p></td>
				</tr>
				<tr>
					<th><?php _e('Author', 'wp-bb'); ?></th>
					<td>
						<input type="hidden" name="wpbbtopicadminid" value="<?php echo $admin_id; ?>" />
						<input type="text" name="wpbbtopicauthorname" value=""/> 
						<p class="description"><?php _e('User ID or Name (leave blank if posting as self)', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Content', 'wp-bb'); ?></th>
					<td>
						
						<?php
						$wordpress_version = get_bloginfo('version');
						if ($wordpress_version == "3.2")
						{
							the_editor("", "wpbbtopiccontent");
						}
						else
						{
							wp_editor("", "wpbbtopiccontent");
						}
						?>
					</td>
				</tr>
				<tr>
					<th><?php _e('Forum', 'wp-bb'); ?></th>
					<td>
						<select name="wpbbtopicforumname" id="wpbbtopicforumname">
						<?php
							$forums = $wpdb->get_results("SELECT * FROM ".CATEGORY_TABLE." WHERE forum > 0 OR subforum > 0");
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
						<p class="description"><?php _e('The forum or subforum to post the topic to', 'wp-bb'); ?></p>
					</td>
				</tr>
				<tr>
					<th><?php _e('Status', 'wp-bb'); ?></th>
					<td>
						<?php _e('Locked', 'wp-bb'); ?> <input type="checkbox" name="wpbbtopicstatus[]" id="wpbbtopicstatus" value="locked"/>
						<?php _e('Sticky', 'wp-bb'); ?> <input type="checkbox" name="wpbbtopicstatus[]" id="wpbbtopicstatus" value="sticky"/>
						<p class="description"><?php _e('If locked, cannot recieve new posts. If sticky, this topic is placed at the top of a forum', 'wp-bb'); ?></p>
					</td> 				
				</tr>
				<tr>
					<td><input type="submit" name="submit" class="button-primary" value="<?php _e('Create Topic', 'wp-bb'); ?>" /></td>
				</tr>
			</table>
	</form>
	</div>
	<?php
?>