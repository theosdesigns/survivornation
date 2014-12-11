=== Delete Me ===
Contributors: cmc3215
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L5VY6QDSAAZUL
Tags: delete, user delete, delete profile, delete own account, unregister, user unregister, unsubscribe, user unsubscribe, user management, multisite
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.4
License: GPL2 http://www.gnu.org/licenses/gpl-2.0.html

Allow users with specific WordPress roles to delete themselves from the `Users -> Your Profile` subpanel or anywhere Shortcodes can be used.

== Description ==

Allow users with specific WordPress roles to delete themselves from the `Users -> Your Profile` subpanel or anywhere Shortcodes can be used using the Shortcode `[plugin_delete_me /]`.
Settings for this plugin are found on the `Settings -> Delete Me` subpanel. Multisite and Network Activation supported.

**How it works:**

* A user clicks the delete link, which by default says `Delete Profile`, but can be changed.

* User is asked to confirm they want to delete themselves, [OK] or [Cancel].

* If [OK] the user is deleted along with all their Posts, Links, and (optionally) Comments.

* Deleted user is redirected to the landing page URL, which by default is your homepage, but can be changed.

**Settings available:**

* Select specific WordPress roles (e.g. Subscriber) you want to allow to delete themselves using Delete Me.

* `class` and `style` attributes of the delete link.

* `<a>` tag clickable content of the delete link.

* Javascript confirm text.

* Landing page URL.

* Enable or disable delete link on the `Users -> Your Profile` subpanel.

* Multisite: Delete user from Network if they no longer belong to any other Network Sites after deletion from current Site.

* Delete comments.

* E-mail notification when a user deletes themselves.

== Installation ==

1. Install automatically in WordPress on the `Plugins -> Add New` subpanel or upload the `delete-me` folder to the `/wp-content/plugins/` directory.

2. Activate the plugin on the `Plugins` panel in WordPress.

3. Go to the `Settings -> Delete Me` subpanel. Select the WordPress roles you want to allow to delete themselves using Delete Me and save changes.

4. The delete link will be placed automatically on the `Users -> Your Profile` subpanel for roles you allow, but if you have a Post or Page you'd like the delete link to appear on just copy and paste the Shortcode `[plugin_delete_me /]` there. A custom PHP template can use the Shortcode like so `<?php echo do_shortcode( '[plugin_delete_me /]' ); ?>`

== Frequently Asked Questions ==

= What happens to Posts, Links, and (optionally) Comments belonging to a deleted user? =

Most Post types and Comments are moved to Trash. Links are always deleted permanently.

= Does this plugin support WordPress Multisite? =

Yes, Network Activation and single Site activation are both supported. Users and their content will only be deleted from the Site they delete themselves from, other Network Sites will be unaffected.

= Is it possible for a user to delete anyone but themselves? =

No, the user deleted is the currently logged in user, period.

= What does the Shortcode display when the user is not logged in or their role is not allowed to delete themselves? =

Nothing when using the self-closing Shortcode tag (i.e. `[plugin_delete_me /]`). However, when the opening and closing Shortcode tags are used (i.e. `[plugin_delete_me]`Content`[/plugin_delete_me]`) the content inside the tags will appear instead of the delete link.

= Where is a user sent after deleting themselves? =

The `Settings -> Delete Me` subpanel lets you enter any URL you'd like to redirect deleted users to, set to homepage by default.

= Is there a confirmation before the user deletes themselves? =

Yes, users must confirm by Javascript dialog box [OK] or [Cancel].

= May I be notified of users who delete themselves and what was deleted? =

Yes. The `Settings -> Delete Me` subpanel has a setting called "E-mail Notification", just check the box and save changes.

== Screenshots ==

1. `Users -> Your Profile` subpanel.
2. Post or Page using the Shortcode.
3. `Settings -> Delete Me` subpanel.

== Changelog ==

= 1.4 =

* Release date: 04/24/2013
* Added setting to enable or disable the delete link on the `Users -> Your Profile` subpanel.
* Added an uninstall.php file. This enables removal of the plugin capabilities and settings when you "Delete" the plugin from the `Plugins` panel in WordPress.
* Fixed possible PHP Warning: missing argument 2 `$wpdb->prepare()` on Multisite installations using WordPress 3.5+
* Fixed possible PHP Fatal error: undefined function `is_plugin_active_for_network()` on Multisite installations when adding a new Site from outside the WordPress Admin pages.
* Consolidated scripts to reduce the number of files used and the total plugin filesize.

= 1.3 =

* Release date: 04/23/2013
* Added setting to customize Javascript confirm text.

= 1.2 =

* Release date: 02/07/2013
* WordPress 3.4 now required.
* Added Multisite and Network Activation support.
* Added setting for Multisite to delete user from Network if user no longer belongs to any Network Sites.
* Added setting to delete comments.
* Edited e-mail notification to list the number of comments deleted.
* Edited Javascript delete warning to include "Comments" in the warning if the setting "Delete Comments" is checked.

= 1.1 =

* Release date: 04/11/2011
* Added setting for detailed e-mail notification when user deletes themselves.
* Fixed undefined function errors for wp_delete_post and wp_delete_link when user has Posts or Links.

= 1.0 =

* Release date: 04/09/2011
* Initial release.

== Upgrade Notice ==

= 1.4 =

This version added a setting to enable or disable the delete link on the `Users -> Your Profile` subpanel. Noncritical stability improvements were also added. See Changelog for details.

= 1.3 =

This version added a setting to customize the text on the Javascript confirm dialog box.

= 1.2 =

This version supports Multisite and Network Activation. A setting to delete comments was also added. See Changelog for details.

= 1.1 =

This version contains a critical fix, please update immediately! See Changelog for details.

= 1.0 =

Initial release.