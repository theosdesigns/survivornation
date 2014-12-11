<?php
// File called by class?
if ( isset( $this ) == false || get_class( $this ) != 'plugin_delete_me' ) exit;

// Deactivate
if ( $network_wide ) {
	
	// Network deactivation
	$blog_ids = $this->wpdb->get_col( "SELECT `blog_id` FROM " . $this->wpdb->blogs );
	
	foreach ( $blog_ids as $blog_id ) {
		
		switch_to_blog( $blog_id );
		$this->option = $this->fetch_option();
		
		if ( ! empty( $this->option ) && $this->option['settings']['uninstall_on_deactivate'] == true ) {
			
			// Remove capability
			foreach ( $this->wp_roles->role_objects as $role ) $role->remove_cap( $this->info['cap'] );
			
			// Delete option
			delete_option( $this->info['option'] );
			
		}
		
		restore_current_blog();
		
	}
	
} else {
	
	// Site deactivation
	if ( $this->option['settings']['uninstall_on_deactivate'] == true ) {
		
		// Remove capability
		foreach ( $this->wp_roles->role_objects as $role ) $role->remove_cap( $this->info['cap'] );
		
		// Delete option
		delete_option( $this->info['option'] );
		
	}
	
}
