=== Plugin Name ===
Contributors: azuliadesigns
Donate link: http://timtrott.co.uk/wordpress-folding-category-list/
Tags: categories, folding, category list, category management, category navigation, widget, sidebar, expanding, focal
Requires at least: 2.8.0
Tested up to: 4.4
Stable tag: 2.0.7
Date: 06/02/2016

Enhanced Folding Category List for Wordpress 2.8+

== Description ==
As your blog gets more and more posts you will inevitably create more categories to keep them organised. Eventually your categories will take up more room on your screen and scroll across many pages. I work on one blog which has over 350 categories and with this number the standard Wordpress category list is the height of three or four screens - any content below this is lost!

Folding Category List for Wordpress is a plug-in that will only show the top level categories and the current sub-categories. This will save a lot of space on your sidebars for other widgets. Additionally, you can animate the menu as either a drop down horizontal menu or an accordion style vertical menu, both with cool jQuery animations.

= Main Features =

  * jQuery Animation: Use Suckerfish or Superfish to animate drop down menus! - js included in plugin, just activate from control panel!
  * jQuery Animation: Use Simple jQuery Accordion to animate vertical menus! - js included in plugin, just activate from control panel!
  * SEO friendly: Add nofollow and descriptions to links.
  * Multi-widget: Have as many lists as you need.
  * Multi-category: If posts are in multiple categories, you can still control which one to expand, or expand all of them.
  * International: Plugin is compatible with translation plugins (qTranslate).
  * Control: Change order by category name, id, description, post count or use My Category Order plugin for total control.
  * Pages: Expand category list on pages using custom fields.
  * Style: There are lots of different options for CSS tags allowing designers to create unique menus.

= Configuration =
This plugin can be configured via a new settings page which can be found under Settings->FoCaL. All the settings are now documented and explained on this screen.

More details can be found in on the plugin homepage: http://timtrott.co.uk/wordpress-folding-category-list/

== Changelog ==
For full version history and older versions please refer to the plugin homepage.

  * Fixed bug with WordPress 3.5

== Installation ==
The folding category plugin is very easy to install and use.

1. Upload files to your /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit Settings->FoCaL and configure settings
4. Add the widget to the sidebar from Appearance->Widgets
5. Save changes
6. For more in-depth installation, customisation and CSS examples please visit the widget homepage.


= Upgrade Notice =
Since multi-widget administration has been overhauled, it may be necessary to reconfigure settings definitions should the plugin not be able to automatically do this for you. In most cases all you have to do is simply "save" the widget in the widgets screen and this will be enough to update the database.

If you have used the PHP code to add the widget to your page, you will have to make a slight amendment. The parameter "number" in the $instance array has been renamed to "definition". Please refer to the plugin page for detail on PHP usage.

The change was required as the old system was a bit backwards, having to select/create a "setting definition" from the widget and configure it in the admin page. This has been reversed, so that now you create and configure from the admin screen and just select the appropriate one from the widget.

Sorry for the inconvenience this may cause, but it'll be better in the long run and a requirement for some things I have in the pipeline for this plugin.


== Frequently Asked Questions ==

= I can't delete a setting profile = 
Try deleting any unused widgets from the "inactive widgets" section of the widgets dashboard and try again.

= How do I get multiple widgets? = 

Wordpress 2.8+
Simply drag the focal icon to your sidebar(s) as needed.

Wordpress 2.3 - 2.7
Below your available widgets section, you should see a section asking "How many Folding 
Category widgets would you like?". Simply change the number in the box and click save. 
For some reason it does not always add extra widgets on the first attempt, but it works 
fine the second time.

= I sorted the list by X, it's not working =

You need to Rebuild the Cache for changes to take effect.

= I upgraded but receive various php errors =

Data structures have changed to increase performance and may be incompatible with the 
latest version. Please Rebuild the Cache to resolve the errors. You need only do it once.

= The navigation is not updated / showing old data = 

Ensure that "Automatically rebuild cache" is enabled or you manually click "Build Cache".

= I get a Fatal error =

Allowed memory size of 33554432 bytes exhausted (tried to allocate 274388 bytes)

In some rare cases Wordpress is unable to update its internal cache object and crashes out. 
Please see http://wordpress.org/support/topic/161173?replies=3 for progress on this issue.

== Screenshots ==
Please see the plugin homepage below for screenshots and demo sites:
http://timtrott.co.uk/wordpress-folding-category-list/
