=== Prerender and Prefetch ===
Contributors: frantorres
Plugin Name: Prerender and Prefetch
Donate link: http://frantorres.es/prerender-and-prefetch-wp-plugin/
Plugin URI: http://frantorres.es/prerender-and-prefetch-wp-plugin/
Author: FranTorres
Author URI: http://frantorres.es/
Requires at least: 3.1
Tested up to: 3.4.2
Version: 0.94
Stable tag: 0.94
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: prerender, prefetch, preload, speed, load, page

Puts Prerender and Prefetch tag in the page. Allowing compatible navigators to do a pre-load of the page you figure the visitor is going to go.

== Description ==

¿What is [Prerender](https://developers.google.com/chrome/whitepapers/prerender) and [Prefetch](http://en.wikipedia.org/wiki/Link_prefetching)? Nice question. It's a new-navigators technique (ok i'm a liar, Mozilla do it from 2003!) that loads in background the next page you believe the visitor is going to visit.

This plugin puts the required metatag in your WordPress pages, based on settings you can change, allowing those compatible navigators to do a pre-load of the next page. When the visitor try to visit that page Boom! it just appears without need to wait for it!

= Testing Prerender and Prefetch Support in your navigator =
You can [test here Chrome's prerender](http://prerender-test.appspot.com/) with any page.

= Install and after install =
*When installing, remember to set the server's load limit on settings.
*This is a plugin in development, feel free to ask questions in "Support" section and colaborate with it.

== Installation ==

1. Upload the plugin to your plugins directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the server's maximum load (because of pre-load can increase the server's load, you have to configure a limit where Prerender and Prefetch will be off)
4. Enjoy! Go fast!

== Frequently Asked Questions ==

= What is Prerender and Prefetch  =

They are techniques to do a preload in background of another page, the logical use to this is load the page is going most-probably to be the next page the visitor is going to go. So the visitor don't have to wait for the load of that page, it was already loaded in background and just shows up!.


== Screenshots ==

1. Settings menu, remember to set the "Server load to stop" parameter
2. When you go from one page to another that is prerender it takes like... ¿300ms? it's just a blink (Test done with Chrome's Browser with prerender activated in browser and in page, configured for that link to being prerendered (be in blog page->post number 1), and with the plugin not limited by server load.)

== Changelog ==

= 0.93 =

* Initial working version
