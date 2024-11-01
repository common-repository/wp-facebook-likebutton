=== Plugin Name ===
Contributors: Fliks GmbH
Donate link: http://maschinendeck.fliks.com
Tags: facebook, like
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: trunk

Adds a facebook-like button to your blog.

== Description ==

Adds a "like" button to your blog. The button can optionally be shown on single posts,
pages or the homepage.

== Installation ==

1. Upload wp_facebook_likebutton to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings -> FacebookLikeButton and configure the plugin.

Optionally you can place the following code in your templates to display the like button:

<?php $wp_fb_likebutton->showButton(); ?>

== Upgrade Notice ==

nothing yet

== Frequently Asked Questions ==

nothing yet

== Screenshots ==

1. Showing the settings page in wordpress adminpanel.
2. Showing the like-button on a single post.

== Changelog ==

= 0.2 =
* added possibility to show like button on homepage.

= 0.3 =
* Bugfix: no facebook meta on homepage.