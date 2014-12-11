<?php

if( !class_exists('ManageDonateExtra') ):
	class ManageDonateExtra{

		function ManageDonateExtra() { //constructor
			if( $_GET['page'] == 'DonateExtra' && ( $_GET['doaction'] || $_GET['delete'] ) )
				$this->Actions();
				
			if( $_POST['updatedonor'] )
				$this->Update();
		}

	
		function Actions(){


			global $wpdb;
			$tb = $wpdb->prefix.'donations';
			if( $_GET['action'] == 'delete' || $_GET['delete']):
				if( $_GET['action'] ) $dIDs = $wpdb->escape($_GET['donor']);
				$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=DonateExtra';
				if( $_GET['delete'] ) $dIDs[] = $wpdb->escape($_GET['delete']);
				foreach( $dIDs as $dID ):
					$del = "DELETE FROM $tb WHERE ID = $dID LIMIT 1";
					//echo $del; exit;
					$wpdb->query($del);
					$msg = 2;
				endforeach;
				header("Location:$mngpg&msg=2");
			endif;
		}
		
		function Manage(){

			global $wpdb;
			if( $_GET['edit'] ):
				$this->Edit();
			else:
			$tb = $wpdb->prefix.'donations';
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=DonateExtra';
			$donors = $wpdb->get_results("SELECT * FROM $tb ORDER BY ID ASC");
			if(  $_GET['s'] ):
				$s = $wpdb->escape($_GET['s']);
				$sq = "SELECT * FROM $tb WHERE name LIKE '%$s%' OR email LIKE '%$s%' OR url LIKE '%$s%' OR comment LIKE '%$s%' ORDER BY ID ASC";
				$donors = $wpdb->get_results($sq);
			endif;
			
			?>

            <div class="wrap">
            	<h2><?php _e('Manage Donations', 'dextra');?></h2>
                <form id="donate-filter" action="<?php echo $mngpg;?>" method="get"><input type="hidden" name="page" value="DonateExtra" />
<p class="search-box">
	<label class="screen-reader-text" for="page-search-input"><?php _e('Search Donations:','dextra');?></label>
	<input type="text" id="donate-search-input" name="s" value="" />
	<input type="submit" value="<?php _e('Search Donations','dextra');?>" class="button" />

</p>
<div class="tablenav">


<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e('Bulk Actions', 'dextra');?></option>
<option value="delete"><?php _e('Delete', 'dextra');?></option>
</select>
<input type="submit" value="<?php _e('Apply', 'dextra');?>" name="doaction" id="doaction" class="button-secondary action" />
</div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat page fixed" cellspacing="0">
  <thead>
  <tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" id="donorname" class="manage-column column-donorname" style=""><?php _e('Donor Name', 'dextra');?></th>
	<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Gross Amount', 'dextra');?></th>
	<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Net Amount', 'dextra');?></th>
    <th scope="col" id="comment" class="manage-column column-comment" style=""><?php _e('Comment', 'dextra');?></th>
	<th scope="col" id="date" class="manage-column column-date" style=""><?php _e('Date', 'dextra');?></th>
	<th scope="col" id="txn_id" class="manage-column column-date" style="width: 150px;"><?php _e('TXN ID', 'dextra');?></th>
  </tr>
  </thead>

  <tfoot>
  <tr>

	<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" class="manage-column column-donorname" style=""><?php _e('Donor Name', 'dextra');?></th>
	<th scope="col" class="manage-column column-amount" style=""><?php _e('Gross Amount', 'dextra');?></th>
	<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Net Amount', 'dextra');?></th>
    <th scope="col" class="manage-column column-comment" style=""><?php _e('Comment', 'dextra');?></th>
	<th scope="col" class="manage-column column-date" style=""><?php _e('Date', 'dextra');?></th>
	<th scope="col" id="txn_id" class="manage-column column-date" style=""><?php _e('TXN ID', 'dextra');?></th>
  </tr>
  </tfoot>

  <tbody>
  <?php

  	foreach( $donors as $dn ):
	if( $alt ) $alt = false; else $alt = 'alternate';
	
		?>
        <tr class="<?php echo $alt;?> iedit">
        	<th scope="row" class="check-column"><input type="checkbox" name="donor[]" value="<?php echo $dn->ID;?>" /></th>
            <td class="donorname"><strong><a class="row-title" href="<?php echo $mngpg.'&amp;edit='.$dn->ID;?>" title="<?php _e('Edit', 'dextra'); echo $dn->name;?>"><?php echo $dn->name;?></a></strong>
            	<div class="row-actions">
                	<span class="edit"><a href="<?php echo $mngpg.'&amp;edit='.$dn->ID;?>" title="<?php _e('Edit this Donation', 'dextra');?>"><?php _e('Edit','dextra');?></a> | </span><span class="delete"><a class="submitdelete" title="<?php _e('Delete this Donation','dextra');?>" href="<?php echo $mngpg.'&amp;delete='.$dn->ID;?>"><?php _e('Delete','dextra');?></a> </span>
                </div>
                <div class="hidden" id="inline_<?php echo $dn->ID;?>">
                	<div class="name"><?php echo $dn->name;?></div>
                    <div class="email"><?php echo $dn->email;?></div>
                    <div class="url"><?php echo $dn->url;?></div>
                    <div class="comment"><?php echo $dn->comment;?></div>
                    <div class="diplay"><?php echo $dn->display;?></div>
                    <div class="amount"><?php echo $dn->amount;?></div>
                    <div class="currency"><?php echo $dn->currency;?></div>
                    <div class="date"><?php echo $dn->date;?></div>
                    <div class="user_id"><?php echo $dn->user_id;?></div>
                    <div class="status"><?php echo $dn->status;?></div>
                    <div class="txn_id"><?php echo $dn->txn_id;?></div>
                </div></td>
                <td class="amount"><?php echo $dn->amount.' '.$dn->currency;?></td>
<?php
			
			// Get fee_percent & Fee_cash from DonateExtra options
			$tempoptions = get_option("DonateExtra");
    			foreach ($tempoptions as $key => $option){
        		$myOptions[$key] = $option;
			}
			$fee_percent = $myOptions['paypal_percentage'];
			$fee_cash = $myOptions['paypal_cash'];
	$pre_total = $dn->amount;	
	// Set Fee Rate Variables
	// Paypal's percentage rate per transaction (3.4% in UK)
	// $fee_percent = '3.4';
	// Paypal's set cash amount per transaction (£0.20 in UK)
	// $fee_cash = '0.20';
	
	// Calculate Fees
	$paypal_fee = ((($pre_total / 100) * $fee_percent) + $fee_cash);

	// Calculate Post Total
	$post_total = $pre_total - $paypal_fee;

	// Tidy Up Numbers
	$pre_total = sprintf("%01.2f", $pre_total);
	$paypal_fee = sprintf("%01.2f", $paypal_fee);
	$post_total = sprintf("%01.2f", $post_total);
?>
		<td class="amount"><?php echo $post_total.' '.$dn->currency;?></td>
                <td class="comment"><?php echo $dn->comment;?></td>
                <td class="date"><?php echo $dn->date;?></td>
                <td class="txn_id"><?php echo $dn->txn_id;?></td>
        </tr>
        
        <?php
$total = $total + $dn->amount;
$paypal_total = $paypal_total + $post_total;
	endforeach;
  ?>
  
  </tbody>
  </table>
</br>
<table class="widefat page fixed" cellspacing="0" style="width: 35%">
  <thead>
  <tr>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Gross Total', 'dextra');?></th>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Net Total', 'dextra');?></th>
</tr>
  </thead>

  <tfoot>
  <tr>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Gross Total', 'dextra');?></th>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Net Total', 'dextra');?></th>
</tr>
  </tfoot> 

  <tbody>
<td class="amount"><?php echo sprintf("%01.2f", $total).' '.$dn->currency;?></td>
<td class="amount"><?php echo sprintf("%01.2f", $paypal_total).' '.$dn->currency;
?></td>
</tr>
</tbody>
  </table>
<?php
			$tempoptions = get_option("DonateExtra");
    			foreach ($tempoptions as $key => $option){
        		$myOptions[$key] = $option;
			}
			$fee_percent = $myOptions['paypal_percentage'];
			$fee_cash = $myOptions['paypal_cash'];
?>
  </table>
</br>
<table class="widefat page fixed" cellspacing="0" style="width: 35%">
  <thead>
  <tr>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('PayPal Percentage', 'dextra');?></th>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('PayPal Cash', 'dextra');?></th>
</tr>
  </thead>

  <tfoot>
  <tr>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('PayPal Percentage', 'dextra');?></th>
<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('PayPal Cash', 'dextra');?></th>
</tr>
  </tfoot> 

  <tbody>
<td class="amount"><?php echo $fee_percent.' %';?></td>
<td class="amount"><?php echo $fee_cash.' '.$dn->currency;?></td>
</tr>
</tbody>
  </table>

  </form>
                
            </div>
            
            <?php
			endif;
		}
		
		function Edit(){
			global $wpdb;
			$tb = $wpdb->prefix.'donations';
			$dID = $_GET['edit'];
			$donor = $wpdb->get_row("SELECT * FROM $tb WHERE ID=$dID");
			?>
            <div class="wrap">
            	<h2><?php _e('Edit Donation Details', 'dextra');?></h2>
                <form method="post" action="">
                    <input type="hidden" name="updatedonor" value="true" /><input type="hidden" name="dID" value="<?php echo $dID;?>" />
                    <table class="form-table">
                    <tbody>
                    	<tr valign="top">
                    		<th scope="row"><label for="name"><?php _e('Donor Name', 'dextra');?></label></th>
                   			<td><input name="name" id="name" value="<?php echo $donor->name;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="email"><?php _e('Donor Email', 'dextra');?></label></th>
                   			<td><input name="email" id="email" value="<?php echo $donor->email;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="url"><?php _e('Donor URL', 'dextra');?></label></th>
                   			<td><input name="url" id="url" value="<?php echo $donor->url;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="comment"><?php _e('Donor Comment', 'dextra');?></label></th>
                   			<td><textarea name="comment" id="comment" cols="45" rows="5"><?php echo $donor->comment;?></textarea></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="display"><?php _e('Display on Recognition Wall', 'dextra');?></label></th>
                   			<td><select name="display" id="display"><option value="0" <?php if(!$donor->display) echo 'selected="selected"';?>>No</option> <option value="1" <?php if($donor->display) echo 'selected="selected"';?>>Yes</option></select></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="amount"><?php _e('Donation Amount', 'dextra');?></label></th>
                   			<td><input name="amount" id="amount" value="<?php echo $donor->amount;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="currency"><?php _e('Donation Currency', 'dextra');?></label></th>
                   			<td><input name="currency" id="currency" value="<?php echo $donor->currency;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="date"><?php _e('Donation Date', 'dextra');?></label></th>
                   			<td><input name="date" id="date" value="<?php echo $donor->date;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="user_id"><?php _e('User', 'dextra');?></label></th>
                   			<td><input name="user_id" id="user_id" value="<?php echo $donor->user_id;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="status"><?php _e('Payment Status', 'dextra');?></label></th>
                   			<td><input name="status" id="status" value="<?php echo $donor->status;?>" class="regular-text" type="text"></td>
                   		</tr>
                    </tbody>
                    </table>
                    <p class="submit">
                    <input name="Submit" class="button-primary" value="<?php _e('Save Changes','dextra');?>" type="submit">
                    </p>

                </form>

            </div>
            <?php
		}
		
		function Update(){
			global $wpdb;
			$tb = $wpdb->prefix.'donations';
			$dID = $_POST['dID'];
			unset($_POST['updatedonor']);
			unset($_POST['dID']);
			unset($_POST['Submit']);
			foreach( $_POST as $key => $val ):
				$update[] = $key." = '".$val."'";
			endforeach;
			$wpdb->query("UPDATE $tb SET ".implode(', ',$update)."WHERE ID=$dID" );
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=DonateExtra';
			header("Location:$mngpg&msg=1");
		}


	}
endif;

if( class_exists('ManageDonateExtra') )
	$manageDP = new ManageDonateExtra();
	
