=== Donate Extra ===
Contributors: Justine Smithies
Donate link: http://www.exousialinux.org/donations/
Tags: donate, donation, donations, recognition, paypal, charity, fundraising, shortcode, forms, widgets
Requires at least: 2.6
Tested up to: 3.4.2
Stable tag: 2.02
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Start accepting donations on your Wordpress enabled site using Paypal.
Use shortcodes or widgets to display a donation wall, total and the form.

== Description ==

[Donate Extra](http://www.exousialinux.org/donate-extra/) is plugin that will allow you to receive donations on your Wordpress enabled site via Paypal.

Features include:

* Sidebar Widgets for the total , wall & the form.
* Recurring Donations.
* Option for users to donate anonymously.
* Display a wall of donators on a page or with a Widget.
* Fully integrated with Paypal's IPN.
* Manage your donations from the Donate Extra Admin section.

== Installation ==

1. Upload the `donate_extra` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new top-level menu called 'Donate Extra' will appear in your administration menu.
1. Set up your options in the Donate Extra settings panel.
1. Activate the included widgets for display in your theme or use the following shortcodes:
1. Put the shortcode `[donateextra]` on your donation page to display the donate form.
1. Put the shortcode `[donatewall]` on your donation page or a seperate page to display the Donation Recognition Wall.
1. Use the shortcode `[donortotal]` to display your running total of donations to date.
1. Turn on Instant Payment Notification in PayPal and add your URL to your PayPal Profile IPN settings.

== Frequently Asked Questions ==

= The hide function works on my shortcode form but not on my widget =
Make sure you set the default location in the settings to SideBar .

= My Net amount & total are all wrong =
Make sure you fill in the sections Paypal percentage & Paypal cash with your countries values.

= Why don't the donations appear on my Recognition Wall =
You may need to add the Instant Payment Notification URL to your PayPal Profile IPN Settings.  This URL can be found on the Donate Extra Settings Panel at the bottom.  Login in to PayPal and go to your Profile Summary, the click on the Instant Payment Notification link under Selling Preferences and Turn on IPN and set the Notification URL from your Donate Extra Settings Panel.  You can also view your IPN History from this page to see if there are other issues.

= What are shortcodes? =

A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.  To use a shortcode, simple enter it on the page or post in your WordPress blog as described below and it will be replaced on the live page with the additional functionality.

= What shortcodes does Donate Extra use? =

`[donateextra]`
This shortcode will display the Donate Extra donation form

`[donorwall]`
This shortcode will display the Donor Recognition Wall. Optional attribute: title is wrapped within a `<h2>` tag. Usage is `[donorwall title='Donor Recognition Wall']`

`[donatetotal]`
This shortcode will display the total donations received. Optional attributes: prefix is the currency symbol (ie. $), suffix is the currency code (ie. USD), type is the english description (ie. U.S. Dollar). Usage is `[donatetotal prefix='true', suffix='true', type='false']`

= What kind of PayPal account will I need? = 
You will need a Premier or Business account.  Personal accounts are primarily for sending payments and may not include the PayPal IPN features this plugin requires.

= How do I adjust the text colors or other styles? =
Here are 2 simple rules you can add to the bottom of your style.css file to adjust the font color of the heading and main text:

`* Donate Extra Quick Styling */
#donate-extra-form . widgettitle, #donate-extra-total . widgettitle, ##donate-extra-wall . widgettitle{
	color: #000000;
}
#donate-extra-form p, #donate-extra-total p, ##donate-extra-wall p{
	color: #333333;
}`

If you need further styling you can use any of the following rules to adjust specific items:
 
`/* Donate Extra Form Widget Styling */
#donate-extra-form .widgettitle{
	color: #000000; 
}
#donate-extra-form p{
	color: #333333;
}
#donate-extra-form label{
}
#donate-extra-form small{
}
/* Donate Extra Total Widget Styling
#donate-extra-total .widgettitle{
}
#donate-extra-total p{
}
/* Donate Extra Wall Widget Styling */
#donate-extra-wall .widgettitle{
}
#donate-extra-wall p{
}
#donate-extra-wall .date{
}
#donate-extra-wall .name{
}
#donate-extra-wall .amount{
}
#donate-extra-wall .comment{
}`


== Screenshots ==

1. Paypal Gross & Net Display
2. Manage Donations
3. Example of Donation Form
4. Example of Recognition Wall
5. Settings Panel

== Changelog ==

**Novemeber 15, 2012 - v2.01**

* Now you can set the PayPal percentage and fee in the admin section and view pre and post totals along with the txn_id.

**Novemeber 09, 2012 - v2.01**

* Fixed layout for form and widget.

**Novemeber 09, 2012 - v2.00**

* removed left behind belahost option that killed Paypal Sandbox testing.
* Fixed Paypal subscriptions.

**October 25, 2012 - v1.99**

* removed belahost option as no longer used.

**October 25, 2012 - v1.98**

* Made submit image display background color as transparent. Also fixed submit button being too far down a page on some themes.

**October 24, 2012 - v1.97**

* Fixed various styling issues.

**October 24, 2012 - v1.96**

* Fixed [donatetotal] shortcode as it used to display the total missing the last 0 after a decimal point so £1.50 ended up as £1.5

**October 24, 2012 - v1.95**

* fixed unexpected character issue on plugin activation. Changed file encoding to UTF-8 without BOM.

**October 23, 2012 - v1.94**

* Updated depreciated register_sidebar_widget & control functions.

**October 23, 2012 - v1.93**

* Updated currency symbols to display correctly after the 1.92 fix

**October 23, 2012 - v1.92**

* Replaced depreciated register_sidebar_widget() & register_widget_control() with the new
  wp_register_sidebar() & wp_register_widget_control()

**October 22, 2012 - v1.91**

* Fixed deciaml point issue on donations.

**October 19, 2012 - v1.9**

* Fixed Paypal IPN Error 404 not found issue.
* Fixed issue where an empty website url stopped the comments being displayed on the wall.
* Fixed some general CSS issues.

**Aug 30, 2011 - v1.8**

* PayPal IPN bugs fixed thanks to: Johan Rufus Lagerström

**Aug 16, 2011 - v1.7**

* Changed PayPal IPN connection to use SSL
* Moved Donor Wall Date above Name for better readability.
* Added prepare database insert to help prevent malicious HTML entries.

**Oct 25, 2009 - v1.6**

* Added Testing options
* Added Donation Management
* Added Menu Icon
* Added IPN URL information

**Jan 26, 2009 - v1.5.4/1.5.5**

* Added missed localisation tags
* Fixed Recognition Wall date/time to show

**Jan 25, 2009 - v1.5.3**

* Fixed MAJOR bug with option for displaying user info, was incorrectly set to always show wall info even when not checked.

**Jan 25, 2009 - v1.5.2**

* Fixed PayPal error when not using recurring donations

**Jan 25, 2009 - v1.5.1**

* Integrated Widgets into main plugin to fix version control issue.  No need to seperately activate widgets.

**Jan 24, 2009 - v1.5**

* Fixed bugs with recurring donations
* Added button image choices
* Allow donors to hide donation amount, but still appear on wall
* Donors can choose the period of donations rather than having it preset. Settings allow selective recurrance options.
* Limit the amount of Donors showing. Pagination coming soon.

**Jan. 23, 2009 - v1.4**

* Added Sidebar Widget Plugin as an alternative to shortcodes.

**Jan. 20, 2009 - v1.3**

* Fixed Donor Wall to allow disabling
* Added Recurring Donation support

**Dec. 7, 2008 - v1.2**

* Altered Paypal IPN script to use `mc_amount` variable instead of `payment_amount`
* Fixed {wall} url replacement - was putting link ID, not actual link in the Thank You email.

**Dec. 7, 2008 - v1.1**

* Replaced testing url in form back to PayPal url.

== Upgrade Notice ==
IPN re-fixed thanks to Johan Rufus Lagerström
