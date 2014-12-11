<?php

?>

<div id="wrap">

	<div id="icon-plugins" class="icon32"></div>

		<h2><?php _e('FAQ & Support', 'wp-bb'); ?></h2>
		
		<p><?php _e('Welcome to the FAQ and Support section.', 'wp-bb'); ?></p>
		
		<h3><?php _e('FAQ', 'wp-bb'); ?></h3>
		
		<dl>
			<dt>
				<b>
					<?php _e('Where is my Forum?', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php printf(__('Your forum URL is accessible from <a href="%s">%s</a>. When WPBB is activated, by default it creates a page entitled \'Forum\' with a template called wpbb-template. You can change the title anytime you want by editing its page.'), wpbb_admin_permalink(), wpbb_admin_permalink()); ?>
			</dd>
			<dt>
				<b>
					<?php _e('What is a Category?', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php _e('In WPBB, a category can be described the same way as any category which holds related types of items or information. Forums and subforums within a category can be totally unrelated, whether deliberately or accidental though the idea is to keep forums and subforums within a category that are to do with the category itself. When creating a category you have the option to name the category and give it an order. The order affects the position in which it will appear. 0 means it will appear first, 1 means second, etc. Example: I create a category named News with the order of 0. I will probably create forums for this category called News & Announcements and another called Feedback. As a second category I use the name General with the order of 1. This category will contain forums such as Offtopic, Gaming, Introductions, Programming, etc. If the order is the same, the categories will be ordered alphabetically.', 'wp-bb'); ?>
			</dd>
			<dt>
				<b>
					<?php _e('What is a Forum?', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php _e('To create a forum you must have atleast 1 category. A forum will be placed inside the category and organised by the order you specified or change later. A forum (more specifically its name and description) should relate to the category but do not always have to. With a forum you have more options to choose from. You can enter a description which is displayed on the index page and gives your users more information about what the forum is about, any rules that may apply to that forum etc. You can now also choose permissions for you forum either by copying a forums permissions (if another forum/subforum exists) or manually enter permissions for your forum. You can change these permissions at any time. You\'ll also have to specify an order or leave it blank. A forums order determines the forums position within a category (0 is first, 1 is second, etc)', 'wp-bb'); ?></dd>
			<dt>
				<b>
					<?php _e('What is a Subforum?', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php _e('A subforum is similar to a forum in that it can be placed in side its parent. A subforum can be placed into a Forum. Perhaps you have created a Category called Sports and a forum called Football and then you may need to create subforums for football teams etc. A subforum has the same options available to it as a forum does: name, description, copy permissions, advanced permissions and order. A subforums order determines the subforums position within a forum (0 is first, 1 is second, etc)', 'wp-bb'); ?></dd>
			<dt>
				<b>
					<?php _e('Permissions', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php _e('Permissions are what rights or abilities a certain user with a certain role has. You can enter the permissions for forums and subforums. There is one gotcha: Forum permissions take precendence over subforums i.e you must be able to do something in the forum to be able to do the same thing in the subforum, e.g. If subscribers can view a forum but not the subforum, they cannot access the subforum.', 'wp-bb'); ?>
			</dd>
			<dt>
				<b>
					<?php _e('How to Setup Facebook Integration', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php _e('1. https://developers.facebook.com/apps (register or login)
2. Create New App
3. Enter App Name, can be anything you want, don\'t worry about app namespace or web hosting. Click continue.
4. Facebook should\'ve created the app for you and you\'ll see at the top the name of your app and the default avatar, next to that there is your App ID and App Secret Key. Enter those into your WPBB facebook settings.
5. You may want to enter a contact email and change any other settings to your liking but leave sandbox mode disabled and don\'t use the "Hosting URL"
6. Click "Website with Facebook Login" under "Select how your app integrates with Facebook" then enter the URL to your wpbb forum e.g. http://mysite.com/wordpress/forum
7. Save changes)', 'wp-bb'); ?>
			</dd>
			<dt>
				<b>
					<?php _e('When I edit and save my forum or subforum, there is an error', 'wp-bb'); ?>
				</b>
			</dt>
			<dd>
				<?php _e('It is likely that you are updating a forum with roles that no longer exist. Go to your admin dashboard -> WPBB -> Tools -> Refresh Roles. Use this tool whenever you add, change or remove roles. Roles are like the ranks or capabilities that users have, e.g. subscriber, author, administrator, etc.', 'wp-bb'); ?>
			</dd>
		</dl>
		
		<h3><?php _e('Support', 'wp-bb'); ?></h3>
		
		<dl>
			<dt><b><?php _e('Support Forums', 'wp-bb'); ?></b></dt>
			<dd>http://wordpress.org/support/plugin/wp-bulletin-board</dd>
			<dt><b><?php _e('Author Email', 'wp-bb'); ?></b></dt>
			<dd>me@codebycarter.com</dd>
			<dt>
				<b>
					<?php 
						_e('Your version', 'wp-bb'); 
					?>
				</b>
			</dt>
			<dd>
				<?php 
					echo WPBB_VERSION;
				?>
			</dd>
		</dl>
		
</div>

	
	

<?php

?>