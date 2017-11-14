=== Smart Post Grid ===
Contributors: cactusthemes, lampd
Collaborators: cactusthemes, lampd
Donate link: 
Tags: post, posts shortcode, posts layout, blog shortcode, grid, blog, posts grid
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart Posts Grid is the best plugin to showcase your posts with many pre-built layouts.

== Description ==

Smart Posts Grid is a Blog shortcode to query posts by many conditions and display in many pre-built layouts without any CSS. It just works seamlessly in your theme. 

= Main Features: =

1. Query posts by many conditions
2. 9 Pre-built layouts, without any custom CSS
3. Supports Visual Composer plugin
4. Ajax query posts

= Shortcode =

[s_post_grid post_type='' count='' condition='' order='' cats='' tags='' ids='' layout='' show_category_tag='' filter_style='' show_meta='' column='' id='' title='' title_link='' heading_icon='' heading_bg='' layout='' heading_style='' view_all_text='']

All parameters are optional

* post_type - string - Name of Custom Post Type to query. Default is 'post'
* count - int - Number of posts to query. Default is 10
* condition - select - Condition to query posts. Default is 'latest'. Support 'comment', 'view' (require Top 10 plugin), 'like' (require WTI Like Post plugin), 'random'
* order - select - ASC or DESC
* cats - string - List of category slugs or IDs, separated by comma
* tags - string - List of tag slugs, separated by comma
* ids - string - List of post IDs to query. If this param is used, other params are ignored
* layout - select - Select default layout, from 1 to 9
* show_category_tag - 1/0 - Show category tag on post thumbnail
* filter_style - select - Choose style of category filter. Possible values: 1 (links), 2 (tags), 3 (carousel)
* show_meta - 1/0 - Show post meta information
* column - select - Choose number of columns to display. Possible values: 1, 2, 3, 4, 6
* id - string - CSS ID of the grid
* title - string - Title of the grid
* title_link - URL - Full URL of the title, or the View All button
* heading_icon - string - Name of CSS class for icon in heading
* view_all_text - string - Text of View All link/button

Demo

<a href="http://demo.cactusthemes.com/wp/smart-posts-grid/" title="cactusthemes club demo plugins">http://demo.cactusthemes.com/wp/smart-posts-grid/</a>

== Installation ==

1. Upload `smart-post-grid.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==



== Screenshots ==

1. Layout 1
2. Layout 2
3. Layout 3
4. Layout 4
5. Layout 5
6. Layout 6
7. Layout 7
8. Layout 8
9. Layout 9

== Changelog ==

= 1.0.1 =
* minor bug fixes

= 1.0 =
* First Release