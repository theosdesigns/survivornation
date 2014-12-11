=== WP Bulletin Board ===
Contributors: codebycarter
Tags: forum, bulletin board, twitter, facebook, discussion, discussion board, messaging, private message, topic, post, pagination, profile, subforum, wpbb
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Bulletin Board (WPBB) is an easy to use forum for your Wordpress site with Facebook and Twitter features.

== Description ==

WPBB is an easy to use forum for your Wordpress site which features Facebook authentication, support for displaying your twitter account on your forum that your members can follow and so much more.

You can safely deactivate the plugin at any time and not lose a thing. The plugin will notify you if it is deactivated which you can disable by removing the page WPBB creates for you. If you decide to delete the plugin at any time, all traces of WPBB are deleted permanently.

= Features =
* Compatibility - Fully compatible with 3.5, 3.4, 3.3, 3.2, 3.1 and 3.0
* Ease of Integration - WPBB works with any theme, you can change your theme anytime and WPBB will automatically update itself.
* Translatable - WPBB can easily be translated into your preferred language!
* Facebook registration - Allow your users to register on your Wordpress site via Facebook. This automatically creates a WP account for your Facebook users which they can then login to after authenticating.
* Twitter - Enter your twitter account in your settings and your users will be able to follow your website / personal account with the click of a button.
* Private Messaging System - Allow users to send messages to each other.
* Member Profiles - All your WP users are automatically  apart of your forum whenever you create it and have a "forum profile" which can be viewed on the forum which shows the users avatar, username, role, posts, website and signature. Signatures are apart of WPBB and allow your users to enter a signature which can be text or images and is displayed below their profile and posts.
* Categories, Forums, Sub forums - Organise your forums into categories and your sub forums into forums. There is no limit to the amount of forums its parent can have, and each are ordered by its appearance in its parent.
* Permissions system - Currently supports the ability to allow or disallow any role (even custom roles!) any specific permission (edit, view, read, post, reply, lock, delete, sticky). WPBB grabs all known roles from your WP site which you can set permissions for manually or copy existing permissions from a previous forum.
* Role Permissions - New in 1.1. Allows you to choose whether a role can only edit, lock, delete and sticky their own topics or everyones.
* Topics - If you have the permissions to do so you can edit, lock, delete, sticky, view, read, post or reply with a topic.
* Posts - You can edit and delete any post and the posts topic affects the posts status. You can create posts in two ways, creating a topic reply with presents the WP editor for improved formatting or you can use the quick reply feature (if it is enabled in your settings).
* Pagination System - Supports a simple pagination system which organises your topics and posts - you can set the max topics/posts per page in your wpbb settings.
* Unread Topics - WPBB collects a list of topics which have been posted when you were offline and displays them for you. You can mark any of them as read once you've read them.
* Unanswered Topics - Similar to the above feature, unanswered topics are ones which haven't yet received any replies.
* Admin area - The WPBB settings integrate with your existing WP admin area where you can easily manage your forums with ease. The WPBB overview allows you to change the order of a category, forum or sub forum, edit it and delete it with a single click. You can also create new categories, forums, sub forums and topics via the admin area.
* Settings - WPBB is jam packed with settings that you can use to customise the forum any way you want to.
* Tools - Tools help make your life easier. Tools such as updating your roles whenever you add, change or remove them and a tool to recreate the forum page if you ever accidentally delete it.

And much much more!

== Installation ==

1. Upload `wp-bulletin-board` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Your newly created forum is viewable by visiting the page it uses. The page will be called "Forum" by default and depending on your permalinks settings will have a url like mywpsite.com?page_id=95 or mywpsite.com/forum etc.

== Frequently asked questions ==

= Where is my Forum? =

Your newly created forum is viewable by visiting the page it uses. The page will be called "Forum" by default and depending on your permalinks settings will have a url like mywpsite.com?page_id=95 or mywpsite.com/forum etc.

= Do you provide free support or installation? =

I always have and will provide free support for any of my plugins. If you do encounter any problems when installing this plugin I'll be glad to assist you as well. 

= If I deactivate or remove the plugin, are there any actions that I need to take? =

None whatsoever. When you deactivate the plugin, WPBB will stop working and will notify you that the plugin is deactivated. This can be disabled by deleting or editing the page that WPBB creates for you. When you reactivate the plugin, WPBB resumes as normal. If you do decide to delete the plugin, everything is removed for you, so do take care that you don't remove the plugin by accident.

= Something doesn't work, what should I do? =

Firstly always download the plugin again as the plugin is constantly updated. It is likely the problem you're having has been fixed already. If that doesn't work, create a topic here: http://wordpress.org/support/plugin/wp-bulletin-board

= How to Setup Facebook Integration =

1. https://developers.facebook.com/apps (register or login)
2. "Create New App"
3. Enter App Name, can be anything you want, don't worry about app namespace or web hosting. Click continue.
4. Facebook should've created the app for you and you'll see at the top the name of your app and the default avatar, next to that there is your App ID and App Secret Key. Enter those into your WPBB facebook settings.
5. You may want to enter a contact email and change any other settings to your liking but leave sandbox mode disabled and don't use the "Hosting URL"
6. Click "Website with Facebook Login" under "Select how your app integrates with Facebook" then enter the URL to your wpbb forum e.g. http://mysite.com/wordpress/forum
7. Save changes

= When I edit and save my forum or subforum, there is an error =

It is likely that you are updating a forum with roles that no longer exist. Go to your admin dashboard -> WPBB -> Tools -> Refresh Roles. Use this tool whenever you add, change or remove roles. Roles are like the ranks or capabilities that users have, e.g. subscriber, author, administrator, etc.

== Screenshots ==

1. Forum Index on the twentyeleven theme and using light wpbb layout
2. Successfully creating a new topic on the twenty eleven theme and using light wpbb layout
3. Viewing a Topic on the twentyeleven theme and using light wpbb layout
4. Insufficient Permissions to view a forum on the twenty eleven theme and using light wpbb layout
5. WPBB Settings (some aren't viewable in the screenshot)
6. WPBB Admin Overview
7. Forum Index on the Planetemo by Lea theme and using dark wpbb layout

== Changelog ==

= 1.1.4 =
* Fixes missing private message delete file

= 1.1.3 =
* Update to fix missing files required by plugin without users needing to modify plugin manually.

= 1.1.2 =
* Fixes issue with theme integration
* Anything you can display or create in the WP dashboard editor will now be available in the WPBB editor, e.g. shortcodes, etc.
* Fixes some strings not setup for translation correctly
* If a guest cannot access a forum, displays a login / register message instead of insufficient permissions error.

= 1.1.1 =
* Fixes bug where topic/post last reply is not updating after editing or deleting a post or topic and incorrect last post author name on the forum view.
* Fixes bug with incorrect username when editing a post/topic when not an administrator
* Fixes guest compatibility with last post feature

= 1.1.0 =
* Role permissions added which allow you to set whether a role can edit, lock, delete or sticky only their own topics or posts or everyones.
* Now compatible with wordpress versions 3.0, 3.1, 3.2, 3.3 and 3.5
* Fixes error in topic post editor
* (un)read topics are now used per user instead of globally as it should be
* Removed unneccessary forum prefix setting as you can include 'forum' in your own title
* Fixed post count not changing when deleting topics and/or posts and when editing topics and posts.
* Fixes a number of bugs translators encountered when trying to translate wpbb
* Clicking the search box now clears the textbox, more javascript will be added in the future for those that use it
* Added useful descriptions next to all the settings in the wpbb settings.
* New tool added which allows you to recreate your forums page if it was accidentally deleted or didn't create for some reason
* Last post/reply will now take you to that topic or post
* You can no longer create empty topics, posts or messages or give them empty titles
* You can now go straight to your newly created topic or post when creating a topic or reply.
* Updated FAQ
* Minor bug fixes and typos fixed.

= 1.0.4 =
* Fixes permission bug.
* Fixes language loading.
* Removes twitter error message for now until I've found a better solution as it occasionally failed regardless of whether username exists.
* Updated .POT file to v1.0.4 (1 difference from v1.0.3) for translators

= 1.0.3 =
* Fixed duplicate go back link when replying to a topic
* Fixed being able to reply to a locked topic, now only excludes admins and lock permission granted users
* Users now have the option to lock or sticky a topic when creating a reply (if they have required permissions)
* Fixed being able to choose topic status regardless of permission when creating a topic
* Fixed being able to edit another users signature from the view profile page
* Fixed bug where you couldn't delete a forum or subforum via the admin area
* Permissions bug should now be fixed.
* Now when deleting a category it affects everything inside it so if deleting a category you delete all forums/subforums/topics/posts within that category, if a subforum only that subforum, topics and posts etc.
* Creating a topic and posting a reply now correctly checks the user has waited the required delay between posting
* If enabled you will be able to set permissions for guests for forums and the posting delay works for guests as well
* Moderator links e.g. edit, lock, delete etc are now only displayed if you have the required permissions.
* Other, minor bug fixes

= 1.0.2 =
* Fixed pagination from displaying when there is only 1 page
* Fixed being able to view or post to non-existent forums/subforums/topics
* View message layout now resembles view topic / post layout with some adjustments
* Ability to reply to a message from the view message page
* Fixed smilies from not displaying correctly in topic, post, profile and messages signature
* Signature textarea is now more consistent across different themes
* Fixed topics status from displaying incorrectly when editing a topic
* Facebook and Twitter are disabled by default to prevent error messages
* Fixed compose message link under default permalinks
* Fixed delete message link
* Fixed inability to use ID when editing topic, post author and when composing a message.
* Improved maintenance message and reminder styling.
* Updated links on FAQ & Support
* You can now choose the default role a new facebook registration has in your settings
* Fixed registering facebook accounts if registration is disabled

= 1.0.1 =
* Minor bug fixes
* Fixed broken view message link
* Fixed table layout when a category doesn't have any forums
* Fixed broken link in message which is displayed to logged out users
* Fixed some layout issues due to CSS files not loading early enough, also added some css options for links and changed default visited link colour to blue
* Added pagination to unread topics, unanswered topics and messages
* Rewrote wpbb_goback() function - now correctly takes you to the previous page or forum index depending on current location (should be implemented on all pages)
* Fixed images not displaying in topics/posts
* Added the plugin url to the script which is used in the footer if enabled
* Fixed image links which were 404ing
* Fixed custom permalinks issue when viewing sub forum or topic
* Fixed CSS file paths, updated readme
* Fixed incorrect folder issue
* Updated readme
* Fixed screenshots from not appearing on plugin page
* Removed error_reporting() function from generating errors about other plugins

= 1.0.0 =
* Initial release