<?php
/*
Plugin Name: Big Bio Box
Plugin URI: http://ypraise.com/2011/12/wordpress-plugin-image-backlink-generator/
Description: Adds a new bio box to user profile with tinymce and relaces the bio on the author page but keeps author boxes on posts to small bio.
Version: 1.1
Author: Kevin Heath
Author URI: http://ypraise.com
License: GPL
*/
/*  Copyright 2011  Kevin Heath  (email : kevin@ypraise.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** text format for big bio box */

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );


function save_extra_user_profile_fields( $user_id ) {
 
if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
 
update_user_meta( $user_id, 'bigbiobox', $_POST['bigbiobox'] );

}

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );


function extra_user_profile_fields( $user ) { ?>

	<h3>Full profile description:</h3>
<table class="form-table"><tr>
<th><label for="bigbiobox"><?php _e('Full profile description'); ?></label></th>
	<td>
	<?php  
	$settings = array('wpautop' => false, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15'  );	
	wp_editor(html_entity_decode(get_the_author_meta( 'bigbiobox', $user->ID ) ), 'bigbiobox', $settings); ?>	
	<br />
	<span class="bigbiobox"><?php _e('Add you life history or a lot more about you. This will show on your author profile page.'); ?></span>
	</td></tr>
	</table>
<?php }

function author_bigbio() {
if (is_author($author)){
 the_author_meta( 'bigbiobox' );
 
}

elseif (is_single()){
 the_author_meta( 'user_description' );
}
}

add_filter('the_author_description', 'author_bigbio');

?>