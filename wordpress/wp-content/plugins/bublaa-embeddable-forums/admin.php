<h2>Bublaa Settings</h2>

<?php
	global $bublaa;
	if($_POST) {
		$errors = $bublaa->update_options();
		if(empty($errors)) {
			?><div id="message" class="updated below-h2"><p>Settings updated.</p></div><?php
		}
		else {
			?> <div id="message" class="updated below-h2"> <?php
			foreach ($errors as $error) {
				echo "<p>" . $error . "</p>";
			}
			?> </div> <?php
		}
	}
?>


<style>
#bublaa-settings {
	border-top: 1px solid #dfdfdf;
	border-bottom: 1px solid #dfdfdf;
	padding:10px;
	margin: 10px 0;
}
#bublaa-settings label {
	display: inline-block;
	width: 80px;
}

#bublaa-settings input[type=text] {
	width: 150px;
}

#bublaa-settings input[type=checkbox] {
	margin-right: 80px;
}

#bublaa-settings select {
	width: 90px;
}

#bublaa-settings .bublaa_row {
	display: block;
	width: 100%;
	padding: 10px 0;
}

#bublaa-settings .bublaa_group {
	display: block;
	width: 100%;
	margin: 10px;
	padding: 10px;
	border: 1px solid #dfdfdf;
}

#bublaa-settings input[type=submit] {
	font-weight: bold;
	border: 1px solid black;
	padding: 5px;
}
</style>

<?php $options = $bublaa->get_options(); ?>
<form method="POST" id="bublaa-settings">
	<div class="bublaa_group">
		<h3>General</h3>
		<div class="bublaa_row">
		    <label>Name:</label>
		    <input name="bubble"  value="<?php echo $options['bubble'] ?>" type="text" />
		    <?php if(!$options['bubble'] && $options['page_id']){ ?>
		    	<b>Add the name of you forum on bublaa.com!</b> <br/><a href="<?php echo get_page_link($options['page_id']); ?>">Click here to create a new forum or to add an existing one</a>
		    <?php } ?>
			    <br/>Be sure that the name here matches to the name of the forum you have created on <a target="_blank" href="http://www.bublaa.com">bublaa.com</a>.
			    <br/>Your forum will be part of our forum network on bublaa.com.
		</div>
	</div>

	<div class="bublaa_group">
		<h3>Embedding Forum</h3>
		<div class="bublaa_row">
			<label>Page:</label>
			<select name="page_id">
				<?php
					foreach(get_pages() as $page) {
						$selected = ($options['page_id'] == $page->ID) ? 'selected' : '';
						echo "<option value='" . $page->ID . "' " . $selected . ">" . $page->post_title ."</option>";
					}
				 ?>
			</select>
			<br/>The page you want the forum to appear on.
		</div>

		<div class="bublaa_row">
		    <label>Show footer template:</label>
		    <input name="showFooter"  value="false" <?php if(isset($options['showFooter']) && $options['showFooter'] == true) { ?> checked="checked" <?php } ?> type="checkbox" />
		</div>
	</div>

	<div class="bublaa_group">
		<h3>Embedding Comments</h3>
		<div class="bublaa_row">
		    <label>Enable:</label>
		    <input name="comments_enabled" value="false" <?php if(isset($options['comments_enabled']) && $options['comments_enabled'] == true) { ?> checked="checked" <?php } ?> type="checkbox" />
		    	<br/>Bublaa comments will replace your standard commenting system.
		</div>
	</div>

	<div class="bublaa_group">
		<h3>Activity widget</h3>
		<div class="bublaa_row">
		    <a href="widgets.php">Enable and edit widget here.</a>
		    <br/>Use the bublaa activity widget to show the latest forum activity on any of your pages.
		</div>
	</div>

<div class="bublaa_row">
    <input type="submit" value="Save All Changes" /><span class="update-status"></span>
</div>
</form>

<div id="bublaa_help">
	Need more help? Join us at <a target="_blank" href="http://www.bublaa.com/bubble/about-bublaa">our community</a> Email us at info@bublaa.com
</div>

