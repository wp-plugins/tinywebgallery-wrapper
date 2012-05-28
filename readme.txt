=== Plugin Name ===
Contributors: mdempfle, Michael Dempfle
Donate link: http://www.tinywebgallery.com
Tags: Tinywebgallery, TWG, iframe, gallery, shortcode, widget, random image
Requires at least: 2.8.6
Tested up to: 3.3.1
Stable tag: 1.8.7

This plugin includes TinyWebGallery as shortcode in an advanced iframe and offers a TWG random image widget.

== Description ==

This plugin includes TinyWebGallery as shortcode in an advanced iframe and offers a TWG random image widget.

= Shortcode for TinyWebGallery =
By entering the shortcode '[twg securitykey=""]' you can include the TinyWebGallery to any page or article. 
The following differences to a normal iframe are implemented:

- Security code: You can only insert the shortcode with a valid security code from the administration.
- Enable/disable the overwrite of default short code settings
- Hide areas of the layout to give the iframe more space (see screenshot) 
- Modify css styles to e.g. change the width of the content area (see screenshot)
- Forward parameters to the iframe 
- Include the css for the lytebox automatically
- Set an optimised TWG skin
- Add the user directory automatically

The following shortcode attributes can be used. Please go to the administration for details:
[twg securitykey="" twg_url="" width="" height="" scrolling="" marginwidth="" marginheight="" 
 frameborder="" skin="" addalbum="" include_lytebox_css="" content_id="" content_styles="" 
 hide_elements="" class="" url_forward_parameter=""]

= Widget for a random image =
Adds a random/most viewed image to your sidebar with the follwing options:
- random/most viewed image
- Include sub dirs
- Styling of the image (3 borders, center, css)
- Direct link to the image or album
- Slideshow with fade effect.
- Can be used multiple times on the same page. Even with slideshow!

= Aministration =  
* See Settings -> TinyWebGallery
* Enables the configuration of the defaults for the iframe
* Direkt link to the TWG administration with automatic login

= Advanced iframe =
The iframe wrapper is also available  as standalone iframe wrapper without the TWG features. The plugin is called 'Advanced iframe' and can be found here:  

== Installation ==

You need to have a running TinyWebGallery (v 1.8.5 recommended) standalone installation running! Recommended is to have TWG and Wordpress on the SAME domain to be able to use the Lytebox!

There are 2 ways to install the TinyWebGallery Wrapper

*Using the Wordpress Admin screen*

1. Click Plugins, Add New
1. Search for TinyWebGallery
1. Install and Activate it
1. Place '[twg securitykey=""]' in your pages or posts or use the widget.

*Using FTP*

1. Upload 'tinywebgallery-wrapper' to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place '[twg securitykey=""]' in your pages or posts or use the widget.

== Frequently Asked Questions ==
*Working with the Shortcodes*

== Screenshots ==
1. Comparison between normal iframe and advanced iframe wrapper. The red areas are modified by the advanced iframe to display the content better.
1. This image shows the difference with an url forward parameter. In the advanced iframe a sub album is shown while the normal iframe still shows the entry screen.
1. Shows 3 different styles of the random image with no border, drop shadow and a simple border.  
1. The basic admin screen to enable standard settings.
1. The advanced admin screen to enable advanced settings like html and css changes.
1. The section to enter the TWG admin details.


== Upgrade Notice ==
= 1.8.7 =
Tested with TWG 1.8.7
Tested with Wordpress 3.3.1
Fixed the typo in the doumentation. In securitykey sometimes the r was missing 

= 1.8.6 =
Improved the reading of settings when the attributes are read.
Addes a new administration setting

= 1.8.5 =
First version.

== Changelog ==
= 1.8.5 =
First version. Wrapper version always matches the version of TinyWebGallery
