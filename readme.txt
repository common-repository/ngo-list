=== NGO-list ===
Contributors: George Bredberg
Donate link: https://ngo-portal.org/donera/
Tags: list network sites, multisite, site, portal, NGO
Requires at least: 3.0.1
Tested up to: 4.6
Stable tag: 1.3.4
License GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

List network sites together with external sites and event-sites
Uses link manager to list external sites and event-sites together with public sites on a network. 

== Description ==

Shows a list of installed, public, sub-sites in wordpress.
Using Link-Manager to hold links for event-pages and external pages, this plugin gives a full list of all member NGO:s.
Link-Manager has to have two link categories "Extern" and "Evenemangssida". You add your external member sites, and your event-pages to these categories to get them listed.  
  
Using the shortcode [site-list] in a page or post or widget gives you a list of all sites on the portal.
Don't forget to mark event-pages as not public in Multi-site-site-list-shortcode settings, not to get them listed among proper sites.  
  
Using the shortcode [search-site] you will get a responsive searchfield where you can search any member site and click on the link to get there.

This plugin is intended to be used in a portal for NGO:s where you can have some members that have their website on the portal, and some has there own web-site together with a tiny version of a web-site on the portal where they can add events to be shown in a mutual portal calendar. Then you can link to there real site using the link group external sites.

See documentation on [https://ngo-portal.org](https://ngo-portal.org) for more information about NGO-portal and [GitHub](https://github.com/joje47/NGO-portal) for the code and documentation about this plugin.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. The plugins creates two link categories that holds the links for external sites and event-sites. Add your external site- members and event-site members to those groups. If you remove any of those categories you will get a warning in backoffice.
4. If you like to create your own image to signal an external site, matching your theme, you could put it in your /theme_folder/images/ico_extern.png Otherwise the plugin will use the image in ngo-list/images/ico_extern.png

== Frequently Asked Questions ==

= Could the groups Evenemangssida and Extern be renamed? =

They could. This plugin is made to be used in a Swedish setting. If you need to rename these groups do a search and replace of those names on the file ngo-list.php
The plugin it self is translated to English though.

== Changelog ==
= 1.3.4 =
* Latest stable release
- Changed language base from Swedish to English to make translation easier.
- Made the category names translatable
Be noticed; since the category names can now be translated I instead use a default slug (extern and eventpage). If you are upgrading this plugin you have to add your sites to the categories with these slugs, Otherwise they won't be listed. v1.3.3 and older used slugs (and names) in Swedish.
The categories get's there names when activating this plugin, but can be changed at will, as long as the slug remains either extern or eventpage.

= 1.3.3 =
Changed vocabulary (webpage to website) and hide the headlines for category external links and event-page if empty.

= 1.3.2 =
Changed donation link (important stuff ;) )

= 1.3.1 =
Initial release to the Wordpress repositorium
