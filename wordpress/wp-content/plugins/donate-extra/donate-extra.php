<?php
/*
Plugin Name: Donate Extra
Plugin URI: http://www.exousialinux.org/donate-extra
Description: Start accepting donations on your Wordpress enabled site using Paypal. Use shortcodes or widgets to display a donation wall, total and the form.
Author: Justine Smithies
Version: 2.02
Author URI: http://www.exousialinux.org

    (email : justine@exousialinux.org)

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
$dextra_db_version = "1.0";
include_once('dextra-widget.php');
include_once('manage-dp.php');
$currency = array( 	'USD' => array( 'type' => __('U.S. Dollar', 'dextra'), 			'symbol' => '$'	),
					'AUD' => array( 'type' => __('Australian Dollar', 'dextra'), 	'symbol' => '$'	),
					'CAD' => array( 'type' => __('Canadian Dollar', 'dextra'), 		'symbol' => '$'	),
					'CHF' => array( 'type' => __('Swiss Franc', 'dextra'), 			'symbol' => 'CHF' ) ,
					'CZK' => array( 'type' => __('Czech Koruna', 'dextra'), 			'symbol' => 'Kč'	),
					'DKK' => array( 'type' => __('Danish Krone', 'dextra'), 			'symbol' => 'kr'	),
					'EUR' => array( 'type' => __('Euro', 'dextra'), 					'symbol' => '€'	),
					'GBP' => array( 'type' => __('Pound Sterling','dextra'), 		'symbol' => '£'	),
					'HKD' => array( 'type' => __('Hong Kong Dollar', 'dextra'), 		'symbol' => '$'	),
					'HUF' => array( 'type' => __('Hungarian Forint', 'dextra'), 		'symbol' => 'Ft'	),
					'ILS' => array( 'type' => __('Israeli New Shekel', 'dextra'), 	'symbol' => '₪'	),
					'JPY' => array( 'type' => __('Japanese Yen', 'dextra'), 			'symbol' => '¥'	),
					'MXN' => array( 'type' => __('Mexican Peso', 'dextra'), 			'symbol' => '$' ),
					'NOK' => array( 'type' => __('Norwegian Krone', 'dextra'), 		'symbol' => 'kr' ),
					'NZD' => array( 'type' => __('New Zealand Dollar', 'dextra'), 	'symbol' => '$'	),
					'PLN' => array( 'type' => __('Polish Zloty', 'dextra'), 			'symbol' => 'zł'	),
					'SEK' => array( 'type' => __('Swedish Krona', 'dextra'), 		'symbol' => 'kr' ),
					'SGD' => array( 'type' => __('Singapore Dollar', 'dextra'), 		'symbol' => '$'	)
				);

wp_enqueue_script('jquery');

if( !class_exists('DonateExtra') ):
	class DonateExtra{
		function DonateExtra() { //constructor
			//ACTIONS
				#Add Settings Panel
				add_action( 'admin_menu', array($this, 'AddPanel') );
				add_action( 'admin_head', array($this, 'icon_css') );
				#Update Settings on Save
				if( $_POST['action'] == 'dextra_update' )
					add_action( 'init', array($this,'SaveSettings') );
				#Save Default Settings
					add_action( 'init', array($this, 'DefaultSettings') );
				#Uninstall Donate Extra
				if( $_POST['action'] == 'dextra_delete' )
					add_action( 'init', array($this,'UninstallDP') );
				#Comment Box Limit
					add_action( 'wp_head', array($this, 'TextLimitJS') );
			//SHORTCODES
				#Add Form Shortcode
				add_shortcode('donateextra', array($this, 'DonatePage') );
				#Add Wall Shortcode
				add_shortcode('donorwall', array($this, 'DonorWall') );
				#Add Total Donations Count Shortcode
				add_shortcode('donatetotal', array($this, 'DonateTotal') );
			//LOCALIZATION
				#Place your language file in the plugin folder and name it "wpfrom-{language}.mo"
				#replace {language} with your language value from wp-config.php
				load_plugin_textdomain( 'dextra', '/wp-content/plugins/donate-extra' );
			//INSTALL TABLE
				#Runs the database installation for the wp_donations table
				register_activation_hook( __FILE__, array($this, 'DonateExtraInstall') );

		}

		function AddPanel(){
				global $manageDP;
			add_menu_page( __("Donate Extra",'dextra'), __("Donate Extra",'dextra'), 10, 'DonateExtra', array($manageDP, 'Manage'), 'div' );
			add_submenu_page( 'DonateExtra', 'Settings', 'Settings', 10, 'donateextraSettings', array($this, 'Settings') );
		}
		function icon_css(){
			echo '<style type="text/css">
			#toplevel_page_DonateExtra div.wp-menu-image {
			  background:transparent url("'.trailingslashit(get_option('siteurl')).'wp-content/plugins/donate-extra/dextra-menu.png") no-repeat center -32px;
			}
			#toplevel_page_DonateExtra:hover div.wp-menu-image, #toplevel_page_DonateExtra.current div.wp-menu-image, #toplevel_page_DonateExtra.wp-has-current-submenu div.wp-menu-image {
			  background:transparent url("'.trailingslashit(get_option('siteurl')).'wp-content/plugins/donate-extra/dextra-menu.png") no-repeat center 0px;
			}
			</style>';

		}

		function DefaultSettings () {
			$default = array(
								'paypal_email'		=> get_option('admin_email'),
								'paypal_currency'	=> 'USD',
								'testing_mode'		=> 1,
								'paypal_percentage'	=> 3.4,
								'paypal_cash'	=> 0.20,
								'donate_desc'		=> 'Donation to '.get_option('blogname'),
								'default_value'		=> 10,
								'button_img'		=> 1,
								'custom_button'		=> 'http://',
								'subscribe'			=> array('1'),
								'duration'			=> 1,
								'enable_wall'		=> 1,
								'wall_url'			=> get_option('blogname'),
								'wall_max'			=> 0,
								'ty_msg'			=> 'Thanks for the donation! You rule!',
								'enable_ty'			=> 1,
								'ty_name'			=> get_option('blogname'),
								'ty_email'			=> get_option('admin_email'),
								'ty_subject'		=> 'Thank you from '.get_option('blogname'),
								'ty_emailmsg'			=> "{donor},\n Thank you for your support of ".get_option('blogname').". Your donation of {amount} was truly outstanding!  If you opted for recognition, your name and comments have been posted on the <a href='{donorwall}'>Donor Wall</a>, so all can see how truly great and noble you really are.  Thanks again!\n\n".get_option('blogname')
							);
			if( !get_option('DonateExtra') ): #Set Defaults if no values exist
				add_option( 'DonateExtra', $default );
			else: #Set Defaults if new value does not exist
				$dextra = get_option( 'DonateExtra' );
				foreach( $default as $key => $val ):
					if( !$dextra[$key] ):
						$dextra[$key] = $val;
						$new = true;
					endif;
				endforeach;
				if( $new )
					update_option( 'DonateExtra', $dextra );
			endif;
		}

		function SaveSettings(){
			check_admin_referer('dextra-update-options');
			$update = get_option( 'DonateExtra' );
			$update["paypal_email"] = $_POST['paypal_email'];
			$update["paypal_currency"] = $_POST['paypal_currency'];
			$update["paypal_percentage"] = $_POST['paypal_percentage'];
			$update["paypal_cash"] = $_POST['paypal_cash'];
			$update["testing_mode"] = $_POST['testing_mode'];
			$update["donate_desc"] = $_POST['donate_desc'];
			$update["default_value"] = $_POST['default_value'];
			$update["button_img"] = $_POST['button_img'];
			$update["custom_button"] = $_POST['custom_button'];
			$update["subscribe"] = $_POST['subscribe'];
			$update["duration"] = $_POST['duration'];
			$update["enable_wall"] = $_POST['enable_wall'];
			$update["wall_url"] = $_POST['wall_url'];
			$update["wall_max"] = $_POST['wall_max'];
			$update["ty_msg"] = $_POST['ty_msg'];
			$update['enable_ty'] = $_POST['enable_ty'];
			$update["ty_subject"] = $_POST['ty_subject'];
			$update["ty_email"] = $_POST['ty_email'];
			$update["ty_name"] = $_POST['ty_name'];
			$update["ty_emailmsg"] = $_POST['ty_emailmsg'];
			$update["IPN_email"] = $_POST['IPN_email'];
			update_option( 'DonateExtra', $update );
			$_POST['notice'] = __('Settings Saved', 'dextra');
		}

		function CurrencySelect($sel){
			global $currency;
			foreach( $currency as $key => $cur ):
				$output .= "<option value='$key'";
				if( $sel == $key ) $output .= " selected='selected'";
				$output .= ">".$cur['type']."</option>\n";
			endforeach;
			return $output;
		}

		function PageSelect($sel){
			global $wpdb;
			$pages = $wpdb->get_results("SELECT ID, post_status, post_title FROM $wpdb->posts WHERE post_type='page' ORDER BY post_status DESC, menu_order ASC, post_title ASC");
			$output .= '<option value="sidebar"';
			if( $sel == 'sidebar' ) $output .= ' selected="selected"';
			$output .= '>SideBar</option>'."\n";

			foreach( $pages as $page ):
				if( $page->post_status == 'draft') $draft = '{draft} '; else $draft = '';
				$output .= '<option value="'.$page->ID.'"';
				if( $sel == $page->ID ) $output .= ' selected="selected"';
				$output .= '>'.$draft.$page->post_title.'</option>'."\n";
			endforeach;
			return $output;
		}

		function RecurringSelect($sel=false){
			$values = array('1'=>'Once', 'D'=>'Daily', 'W'=>'Weekly', 'M'=>'Monthly', 'Y'=>'Yearly');
			foreach($values as $key=>$label):
				$output .= "<option value='$key'";
				if($sel == $key) $output .= " selected='selected'";
				$output .= ">$label</option>\n";
			endforeach;
			return $output;
		}
		function RecurringArray($sel=false, $name=false){
			$values = array('1'=>__('Only Once (no recurring)','dextra'), 'D'=>__('Daily','dextra'), 'W'=>__('Weekly','dextra'), 'M'=>__('Monthly','dextra'), 'Y'=>__('Yearly','dextra'));
			foreach($values as $key=>$label):
				$output .= "<label><input name='".$name."[]' value='$key'";
				if(is_array($sel)):
					if(in_array($key, $sel)) $output .= " checked='checked'";
				endif;
				$output .= "type='checkbox' /> $label </label> \n";
			endforeach;
			return $output;
		}
		function TestingSelect($sel=false){
			$values = array('1'=>'Live with PayPal', '2'=>'Testing with PayPal Sandbox');
			foreach( $values as $key=>$label):
				$output .= "<option value='$key'";
				if( $sel == $key ) $output .= " selected='selected'";
				$output .= ">$label</option>\n";
			endforeach;
			return $output;
		}

		function Settings(){
			$dextra = get_option( 'DonateExtra' );
			if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
			?>
             <div class="wrap">
            	<h2><?php _e('Donate Extra Settings', 'dextra')?></h2>

                <form method="post" action="">
                	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'dextra-update-options'); ?>
                    <table class="form-table">
                        <tbody>
                        	<tr valign="top">
                       			 <th scope="row"><label for="paypal_email"><?php _e('PayPal Email Address', 'dextra');?></label></th>
                        		<td><input type="text" name="paypal_email" id="paypal_email" value="<?php echo stripslashes($dextra['paypal_email']);?>" /><br>
<small><?php _e('This is the address associated with your PayPal account.','dextra');?></small></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="paypal_currency"><?php _e('PayPal Currency', 'dextra');?></label></th>
                        		<td><select name="paypal_currency" id="paypal_currency">
									<?php echo $this->CurrencySelect($dextra['paypal_currency']);?>
                                    </select></td>
                        	</tr>
<tr valign="top">
                       			 <th scope="row"><label for="paypal_percentage"><?php _e('Paypal fees percentage', 'dextra');?></label></th>
                        		<td><input type="text" name="paypal_percentage" id="paypal_percentage" value="<?php echo stripslashes($dextra['paypal_percentage']);?>" /></td>
                        	</tr>
</tr>
<tr valign="top">
                       			 <th scope="row"><label for="paypal_cash"><?php _e('Paypal fees cash', 'dextra');?></label></th>
                        		<td><input type="text" name="paypal_cash" id="paypal_cash" value="<?php echo stripslashes($dextra['paypal_cash']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="testing_mode"><?php _e('Testing Mode', 'dextra');?></label></th>
                        		<td><select name="testing_mode" id="testing_mode">
									<?php echo $this->TestingSelect($dextra['testing_mode']);?>
                                    </select></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="IPN_email"><?php _e('Send IPN results', 'dextra');?></label></th>
                        		<td><label><input type="checkbox" name="IPN_email" id="IPN_email" value="1"
									<?php if($dextra['IPN_email']) echo 'checked="checked"';?> /> Enable IPN debugging results to be sent to you via email</label></td>
                        	</tr>
                        	<tr valign="top">
                       			 <th scope="row"><label for="donate_desc"><?php _e('Donation Description', 'dextra');?></label></th>
                        		<td><input type="text" name="donate_desc" id="donate_desc" value="<?php echo stripslashes($dextra['donate_desc']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="default_value"><?php _e('Default Donation Value', 'dextra');?></label></th>
                        		<td><input type="text" name="default_value" id="default_value" value="<?php echo stripslashes($dextra['default_value']);?>" /></td>
                        	</tr>
                        	<tr valign="top">
                       			 <th scope="row"><label for="button_img"><?php _e('Button Image', 'dextra');?></label></th>
                        		<td><label><input type="radio" name="button_img" id="button_img" value="1" <?php if($dextra['button_img'] == 1) echo 'checked="checked"';?> /> <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" /></label>
                        		<label><input type="radio" name="button_img" value="2" <?php if($dextra['button_img'] == 2) echo 'checked="checked"';?> /> <img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" /></label>
                        		<label><input type="radio" name="button_img" value="3" <?php if($dextra['button_img'] == 3) echo 'checked="checked"';?> /> <img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" /></label>
                        		<br />
                        		<label><input type="radio" name="button_img" value="4" <?php if($dextra['button_img'] == 4) echo 'checked="checked"';?> /> <?php _e('Custom Image URL','dextra');?></label> <input type="text" name="custom_button" id="custom_button" value="<?php echo stripslashes($dextra['custom_button']);?>" /></td>
                        	</tr>
                        	<tr valign="top">
                       			 <th scope="row"><label for="subscribe"><?php _e('Enable Recurring Donations', 'dextra');?></label></th>
                        		<td><?php echo $this->RecurringArray($dextra['subscribe'], 'subscribe');?>
                        			</td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="enable_wall"><?php _e('Enable Recognition Wall', 'dextra');?></label></th>
                        		<td><input type="hidden" name="enable_wall" value="2" /><input type="checkbox" name="enable_wall" id="enable_wall" value="1" <?php if( $dextra['enable_wall'] == 1) echo 'checked="checked"';?> /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="wall_url"><?php _e('Donation/Recognition Wall Location', 'dextra');?></label></th>
                        		<td><select name="wall_url" id="wall_url">
									<?php echo $this->PageSelect($dextra['wall_url']);?>
                                    </select><br><small><?php _e('The default location of your Donation/Recognition Page that contains the <code>[donateextra]</code> shortcode.','dextra');?></small></td>
                        	</tr>
                        	<tr valign="top">
                       			 <th scope="row"><label for="wall_max"><?php _e('Maximum Donors on Wall', 'dextra');?></label></th>
                        		<td><input type="text" name="wall_max" id="wall_max" value="<?php echo stripslashes($dextra['wall_max']);?>" /><br><small><?php _e('Enter 0 to show all donors. <em>Pagination coming soon</em>','dextra');?></small></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_msg"><?php _e('Website Thank You Message', 'dextra');?></label></th>
                        		<td><textarea name="ty_msg" id="ty_msg" cols="40" rows="10" style="width:80%;height:150px;"><?php echo stripslashes($dextra['ty_msg']);?></textarea></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="enable_ty"><?php _e('Enable Thank You Email', 'dextra');?></label></th>
                        		<td><input type="hidden" name="enable_ty" value="2" /><input type="checkbox" name="enable_ty" id="enable_ty" value="1" <?php if( $dextra['enable_ty'] == 1) echo 'checked="checked"';?> /></td>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_name"><?php _e('Thank You From Name', 'dextra');?></label></th>
                        		<td><input type="text" name="ty_name" id="ty_name" value="<?php echo stripslashes($dextra['ty_name']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_email"><?php _e('Thank You Email Address', 'dextra');?></label></th>
                        		<td><input type="text" name="ty_email" id="ty_email" value="<?php echo stripslashes($dextra['ty_email']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_subject"><?php _e('Thank You Email Subject', 'dextra');?></label></th>
                        		<td><input type="text" name="ty_subject" id="ty_subject" value="<?php echo stripslashes($dextra['ty_subject']);?>" /></td>
                        	</tr>
                            <tr valign="top">
                       			 <th scope="row"><label for="ty_emailmsg"><?php _e('Thank You Email Message', 'dextra');?></label></th>
                        		<td><textarea name="ty_emailmsg" id="ty_emailmsg" cols="40" rows="10" style="width:80%;height:150px;"><?php echo stripslashes($dextra['ty_emailmsg']);?></textarea><br>
<strong><?php _e('Replacement Codes', 'dextra');?></strong> <small><?php _e('Email Message Only', 'dextra');?></small><br /><?php _e('<code>{donor}</code> = Donor Name<br \><code>{amount}</code> = Donation Amount<br \><code>{donorwall}</code> = Donor Wall URL','dextra');?></td>
                        	</tr>
                         </tbody>
                     </table>
                    <p class="submit"><input name="Submit" value="<?php _e('Save Changes','dextra');?>" type="submit" />
                    <input name="action" value="dextra_update" type="hidden" /></p>
                </form>
                 <h2><?php _e('Shortcodes', 'dextra');?></h2>
               <p><code>[donateextra]</code><br /><?php _e('This shortcode will display the Donate Extra donation form', 'dextra'); ?></p>
               <p><code>[donorwall]</code><br /><?php _e('This shortcode will display the Donor Recognition Wall. <em>Optional attribute:</em> <code>title</code> is wrapped within a <code>&lt;h2&gt;</code> tag.  Usage is <code>[donorwall title=\'Donor Recognition Wall\']', 'dextra'); ?></p>
               <p><code>[donatetotal]</code> <br /><?php _e('This shortcode will display the total donations received. <em>Optional attributes:</em> <code>prefix</code> is the currency symbol (ie. $), <code>suffix</code> is the currency code (ie. USD), <code>type</code> is the english description (ie. U.S. Dollar). Usage is <code>[donatetotal prefix=\'1\', suffix=\'1\', type=\'0\']</code>. 1 will show, 0 will hide.', 'dextra'); ?></p>
               <h2><?php _e('Instant Payment Notification URL', 'dextra');?></h2>
               <p><code><?php echo str_replace(ABSPATH, trailingslashit(get_option('siteurl')), dirname(__FILE__)).'/paypal.php';?></code><br /><?php _e('This is your IPN Notification URL.  If you have issues with your site receiving your PayPal payments, be sure to manually set this URL in your PayPal Profile IPN settings.  You can also view your ', 'dextra');?> <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_display-ipns-history"><?php _e('IPN History on PayPal','dextra');?></a></p>
                <h2><?php _e('Uninstall Donate Extra Tables and Options', 'dextra'); ?></h2>
                <form method="post" action="">
                	<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field( 'dextra-delete'); ?>
                    <p><?php _e('<strong>WARNING:</strong> Uninstalling the Donate Extra tables and option settings will remove all donation data related to this plugin.  This data will not be recoverable.','dextra');?></p>
                    <p class="submitdelete" style="text-align:center"><input name="Submit" value="<?php _e('Uninstall Donate Extra','dextra');?>" type="submit" /><input name="action" value="dextra_delete" type="hidden" /></p>


            </div>
            <?php
		}

		function DonateTotal($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'prefix' => true, 'suffix' => true, 'type' => false ), $atts ) );
			$dextra = get_option( 'DonateExtra' );
			$table = $wpdb->prefix . 'donations';
			$donors = $wpdb->get_results("SELECT amount FROM $table WHERE status='Completed'");
			$total = '';
			foreach( $donors as $donor ):
				$totala = $total + $donor->amount;
				$total = sprintf("%01.2f", $totala);
			endforeach;
			if( !$total ) $total = '0';
			$thecur = $dextra['paypal_currency'];
			$symbol = $currency[$thecur]['symbol'];
			$thetype = $currency[$cur]['type'];
			if( $prefix ) $output .= $symbol;
			$output .= $total;
			if( $suffix ) $output .= ' '.$thecur;
			if( $type ) $output .= ' '.$type;
			return $output;
		}

		function DonorWall($atts=false) {
			global $wpdb, $currency;
			extract( shortcode_atts( array( 'title' => '' ), $atts ) );
			$dextra = get_option( 'DonateExtra' );
			$table = $wpdb->prefix . 'donations';
			if($dextra['wall_max'] > 0)
				$limit = "ORDER BY ID DESC, display ASC, amount DESC, name ASC LIMIT ".$dextra['wall_max'];
			else
				$limit = "ORDER BY display ASC, amount DESC, name ASC";
			$donors = $wpdb->get_results("SELECT * FROM $table WHERE status='Completed' AND display!=0 $limit");
			//print_r($donors);
			$output .= '<div id="donorwall">';
			if( $donors && $title )
				$output .= '<h2>'.$title.'</h2>';
			foreach( $donors as $donor ):
				$symbol = $currency[$donor->currency]['symbol'];
				if($donor->display == 1) $donation = '(<span class="amount">'.$symbol.number_format($donor->amount, 2, '.', ',').' <small class="currency">'.$donor->currency.'</small></span>)';
				else $donation = '';

				$date = strtotime($donor->date);
				$datetime = date('M j, Y \a\t g:i a', $date);
				$output .= '<div class="donorbox"><p><small class="date time"><a href="#donor-'.$donor->ID.'">'.$datetime.'</a></small><br /><cite><strong><a href="'.$donor->url.'" rel="external" class="name url">'.$donor->name.'</a></strong> '.$donation.'</cite> '.__('Said:','dextra').'<blockquote class="comment">'.nl2br($donor->comment).'</blockquote></p></div>';
			endforeach;
			$output .= '</div>';
			return $output;
		}

		function DonatePage($atts=false) {
			global $currency, $user_ID;
			get_currentuserinfo();
			$dextra = get_option( 'DonateExtra' );
			$repeat = array('D'=>'Days', 'W'=>'Weeks', 'M'=>'Months', 'Y'=>'Years');
			if( isset($_GET['thankyou']) )
				$thankyou = $dextra['ty_msg'];
			if( $thankyou ):
				$output = '<p class="donate_ty">'.nl2br(stripslashes($thankyou)).'</p>';
			else:
			$cur = $dextra['paypal_currency'];
			$symbol = $currency[$dextra['paypal_currency']]['symbol'];
			$notify = $notify = get_option('siteurl') . '/wp-content/plugins/donate-extra/paypal.php';
			$img_urlz = array( '1'=>'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif', '2'=>'https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif', '3'=>'https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif', '4'=>$dextra['custom_button']);
			$button = $img_urlz[$dextra['button_img']];
			if( $dextra['wall_url'] == 'sidebar') $wall = get_option('siteurl');
			else $wall = get_permalink($dextra['wall_url']);
			if( strpos($wall, '?') === false )
				$tyurl = $wall.'?thankyou=true';
			else
				$tyurl = $wall.'&amp;thankyou=true';

			$verifyurlz = array( '1' => 'https://www.paypal.com/cgi-bin/webscr', '2'=> 'https://www.sandbox.paypal.com/cgi-bin/webscr');

			$output = '<form id="donateextraform" style="float: left;" action="'.$verifyurlz[$dextra['testing_mode']].'" method="post">';

				$output .='<input type="hidden" id="cmd" name="cmd" value="_donations">
			<p class="donate_amount"><label for="amount">'.__('Donation Amount', 'dextra').':</label><br /><input type="text" name="amount" id="amount" value="'.$dextra['default_value'].'" /> <small>('.__('Currency: ','dextra').$cur.')</small></p>';


			if( in_array('D',$dextra['subscribe']) || in_array('W',$dextra['subscribe']) || in_array('M',$dextra['subscribe']) || in_array('Y',$dextra['subscribe']) ):
				$output .= '
<input type="hidden" name="a3" id="a3" value="" />
<p class="donate_recur" style="width: 270px;"><label for="recur">Donation:</label>
Every
<input name="p3" id="p3" value="'.$dextra['duration'].'" type="text" style="width:10px;" />
<select name="t3" id="t3">';
 			if( in_array('1', $dextra['subscribe']))
 				$output .= '<option value="0">Do not repeat</option>';
 			if( in_array('D', $dextra['subscribe']))
 				$output .= '<option value="D">Day(s)</option>';
 			if( in_array('W', $dextra['subscribe']))
 				$output .= '<option value="W">Week(s)</option>';
 			if( in_array('M', $dextra['subscribe']))
 				$output .= '<option value="M">Month(s)</option>';
 			if( in_array('Y', $dextra['subscribe']))
 				$output .= '<option value="Y">Year(s)</option>';
$output .= '</select> for
<input type="hidden" name="src" id="src" value="'.$dplus['duration'].'">
<input name="srt" id="srt" value="'.$dplus['duration'].'" type="text" style="width:20px;" > times <small>("0" means no end date)</small>
<input type="hidden" name="sra" value="1">
</p>';
			endif;

$siteurl = get_option('siteurl');


			if( $dextra['enable_wall'] == 1 ):
				$output .= '
			<p class="recognition_wall"><label><input type="checkbox" id="recognize" name="recognize" value="1" /> '.__('Put my Donation on the Recognition Wall','dextra').'</label></p>
			<div id="wallinfo">
			<p class="show_onwall" id="wallops"><label for="show_onwall" >'.__('Show on Wall', 'dextra').':</label><br /><select name="item_number"style="margin-left: 0px !important;">
				<option value="0:'.$user_ID.'">'.__('Do not show any information','dextra').'</option>
				<option value="1:'.$user_ID.'">'.__('Amount, User Details &amp; Comments','dextra').'</option>
				<option value="2:'.$user_ID.'">'.__('User Details &amp; Comments Only','dextra').'</option>
			</select></p>
			<p class="donor_name"><label for="donor_name">'.__('Name', 'dextra').':</label><br /><input type="text" name="on0" id="donor_name" /></p>
			<p class="donor_email"><label for="donor_email">'.__('Email', 'dextra').':</label><br /><input type="text" name="os0" id="donor_email" /></p>
			<p class="donor_url"><label for="donor_url">'.__('Website', 'dextra').':</label><br /><input type="text" name="on1" size="30" id="donor_url" value="' . $siteurl . '" /></p>
			<p  class="donor_comment"><label for="donor_comment">'.__('Comments', 'dextra').':</label><br /><textarea name="os1" id="donor_comment" rows="4" cols="45"></textarea><br /><span id="charinfo">'.__('Write your comment within 199 characters.','dextra').'</span> </p></div>';
			endif;

			$output .= '
<div style="height: 0px;">
<input type="hidden" name="notify_url" value="'.$notify.'">
<input type="hidden" name="item_name" value="'.$dextra['donate_desc'].'">
<input type="hidden" name="business" value="'.$dextra['paypal_email'].'">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" name="rm" value="2">
<input type="hidden" name="return" value="'.$tyurl.'">
<input type="hidden" name="currency_code" value="'.$dextra['paypal_currency'].'">
<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted"></div>
<p class="submit"><input type="image" src="'.$button.'" style="background-color: transparent;" border="0" name="submit" alt="">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"></p>
</form>';
			endif;
			return $output;
		}


		function TextLimitJS(){
			global $currency, $user_ID;
			get_currentuserinfo();
			$dextra = get_option( 'DonateExtra' );
			if( $dextra['wall_url'] == 'sidebar') $wall = get_option('siteurl');
			else $wall = get_permalink($dextra['wall_url']);
			$wall = str_replace(get_option('siteurl'), '', $wall);
			echo '<!-- WALL='.$wall.' -->';
			if( $_SERVER['REQUEST_URI'] == $wall || $dextra['wall_url'] == 'sidebar' ):
		?>
<?php /*<script type="text/javascript" src="<?php echo trailingslashit(get_option('siteurl'));?>wp-includes/js/jquery/jquery.js"></script>*/ ?>
<script type="text/javascript">
function limitChars(textid, limit, infodiv)
{
	var text = jQuery('#'+textid).val();
	var textlength = text.length;
	if(textlength > limit)
	{
		jQuery('#' + infodiv).html('<?php _e("You cannot write more then '+limit+' characters!","dextra");?>');
		jQuery('#'+textid).val(text.substr(0,limit));
		return false;
	}
	else
	{
		jQuery('#' + infodiv).html('<?php _e("You have '+ (limit - textlength) +' characters left.","dextra");?>');
		return true;
	}
}
function displayVals() {
      var t3 = jQuery("#t3").val();
      var amount = jQuery("#amount").val();
      if(t3 != 0){
	    jQuery('#a3').val(amount);
	    jQuery('#p3').val(1);
		  jQuery('#src').val(1);
		   jQuery('#srt').val(2);
		jQuery('#cmd').val('_xclick-subscriptions')
	  }else{
	  	jQuery('#a3').val(0);
	  	jQuery('#p3').val(0);
		jQuery('#src').val(0);
		jQuery('#srt').val(0);
	  	jQuery('#cmd').val('_donations');
	  }
	  if( !t3 ) jQuery('#cmd').val('_donations');

}

jQuery(function(){
 	jQuery('#donor_comment').keyup(function(){
 		limitChars('donor_comment', 199, 'charinfo');
 	})

 	jQuery("#amount").change(displayVals);
 	jQuery("#t3").change(displayVals);
 	displayVals();

 	var WallOps1 = '<?php echo '<p class="show_onwall" id="wallops"><input type="hidden" name="item_number" value="0:'.$user_ID.'" /></p>';?>';
 	var WallOps2 = '<?php echo '<p class="show_onwall" id="wallops"><label for="show_onwall">'.__('Show on Wall', 'dextra').':</label><br /><select name="item_number" style="margin-left: 0px !important;"><option value="1:'.$user_ID.'">'.__('Amount, User Details &amp; Comments','dextra').'</option><option value="2:'.$user_ID.'">'.__('User Details &amp; Comments Only','dextra').'</option></select></p>';?>';

 	if( jQuery('#recognize').is(':checked') == false){
 		jQuery('#wallinfo').hide();
 		jQuery("#wallops").html(WallOps1);
 	}

 	jQuery("#recognize").click(function(){
 		jQuery("#wallinfo").toggle('slow');
 		if(jQuery('#wallops input').val() == '0:<?php echo $user_ID;?>'){
 			jQuery("#wallops").html(WallOps2);
 		}else{
 			jQuery("#wallops").html(WallOps1);
 		}
 	})


});

</script>
        <?php
			endif;
		}

		function TagReplace($in, $donor, $amount){

			$dextra = get_option( 'DonateExtra' );

			if( $dextra['wall_url'] == 'sidebar') $wall = get_option('siteurl').'#donorwall';
			else $wall = get_permalink($dextra['wall_url']).'#donorwall';
			$out = str_replace('{donor}', $donor, $in);
			$out = str_replace('{amount}', $amount, $out);
			$out = str_replace('{donorwall}', $wall, $out);
			return $out;
		}

		function DonateExtraInstall () {
   			global $wpdb, $dextra_db_version;
			$table_name = $wpdb->prefix . "donations";
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) :
				$sql = "CREATE TABLE $table_name  (
					  ID bigint(20) NOT NULL AUTO_INCREMENT,
					  name tinytext NOT NULL,
					  email VARCHAR(100) NOT NULL,
					  url VARCHAR(200) NOT NULL,
					  comment text NOT NULL,
					  display int(11) NOT NULL DEFAULT 0,
					  amount decimal(10,2) NOT NULL DEFAULT 0,
					  currency VARCHAR(200) NOT NULL,
					  date datetime DEFAULT '000-00-00 00:00:00',
					  user_id bigint(20) NOT NULL DEFAULT 0,
					  status VARCHAR(100) NOT NULL,
					  txn_id VARCHAR(100) NOT NULL,
					  UNIQUE KEY ID (ID)
					);";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				add_option("dextra_db_version", $dextra_db_version);
			endif;
		}


		function UninstallDP() {
			global $wpdb;
			$table_name = $wpdb->prefix . "donations";
			$plugin_file = 'donate-extra/donate-extra.php';
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) :
				$sql = "DROP TABLE $table_name";
				$wpdb->query($sql);
			endif;
			delete_option("dextra_db_version");
			delete_option("DonateExtra");
			$deactivate = wp_nonce_url('plugins.php?action=deactivate&plugin=' . $plugin_file, 'deactivate-plugin_' . $plugin_file);
			$nonce = explode('_wpnonce', $deactivate);
			$nonce = '&_wpnonce'.$nonce[1];
			$location = trailingslashit(get_option('siteurl')).'wp-admin/plugins.php?action=deactivate&plugin=' . $plugin_file. $nonce;
			header( 'Location:'.$location );
		}
	}//END Class DonateExtra
endif;

if( class_exists('DonateExtra') )
	$donateextra = new DonateExtra();

function DonateExtraForm(){
	global $donateextra;
	echo $donateextra->DonatePage();
}
function DonateExtraWall(){
	global $donateextra;
	echo $donateextra->DonorWall();
}
function DonateExtraTotal(){
	global $donateextra;
	echo $donateextra->DonateTotal();
}
