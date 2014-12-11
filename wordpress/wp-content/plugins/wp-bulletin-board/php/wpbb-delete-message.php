<?php

wpbb_goback1();

$id = absint($_GET['delete_msg']);
$delete_message = $wpdb->query("DELETE FROM ".MESSAGES_TABLE." WHERE id = $id;");
if ($delete_message === false) {
	?>
	<div class="wpbb-message-failure">
		<?php _e('Error deleting message. Please try again', 'wp-bb'); ?> 
	</div>
	<?php
} else {
	?>
	<div class="wpbb-message-success">
		<?php _e('Thank you. That message has been deleted successfully.', 'wp-bb'); ?>
	</div>
	<?php
}
?>