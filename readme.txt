=== Canonical URL for Posts ===
Contributors: blobaugh
Tags: canonical, SEO, search engine optimisation, content syndication, content marketing
Requires at least: 4.0
Tested up to: 4.9
Stable tag: trunk

Creates a metabox with a field that will set the canonical url for the post. Works with all post types.

== Description ==


Creates a metabox with a field that will set the canonical url for the post. Works with all post types.

There is an additional disclaimer option that will display a disclaimer with a link to the original post.

Disclaimer message can be filtered with
apply_filter( 'canonical_disclaimer', $msg, $url, $content, $post );

The disclaimer can be programmatically enabled/disabled with
pply_filters( 'enable_canonical_disclaimer', is_single() )

== Installation ==

This section describes how to install the plugin and get it working.


1. Download and install the plugin. Upload the folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to post editor section, and add the URL. 

== Frequently Asked Questions ==
== Changelog ==

= 1.0 =
* Plugin launch!
