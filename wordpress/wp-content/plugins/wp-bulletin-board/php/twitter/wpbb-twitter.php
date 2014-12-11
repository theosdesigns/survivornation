<?php

/*
	Displays Twitter Follow button if twitter is enabled
*/

if (count($_GET) == 0)
{
	?>
	<div class="wpbb-centered">
		<iframe allowtransparency="true" frameborder="0" scrolling="no"
  src="//platform.twitter.com/widgets/follow_button.html?screen_name=<?php echo $wpbb_twitter_options['twitter_account']; ?>"
  style="width:300px; height:20px;">
  		</iframe>
  	</div>
	<?php
}


?>