=== WP-REST-API Menus ===
Contributors: Jeff Cicero
Donate link: https://amorphouswebsolutions.com/plugins
Tags: rest, api, wp-api, menu, menus, json, endpoints
Requires at least: 4.7.0
Tested up to: 4.9.4
Requires PHP: 5.6
Stable tag: 4.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds menu endpoints to core WP REST API.

== Description ==

This plugin adds "routes" or "endpoints" to WP REST API that allows for retrieval of
menu data as JSON. Updated port of "WP-REST-API V2 Menus" by Claudio La Barbera (http://www.claudiolabarbera.com)
which worked well for the REST API V2 plugin before it became part of core.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `wp-rest-api-menus` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Currently there is no UI, but two new routes are created.

Example usage:
// Get all registered menus
https://yourwpsite.com/wp-json/wp-menus/v1/menus

// Get contents of a registered menu by its "slug". When assigning a menu a location in
// /wp-admin/nav-menus.php?action=locations the slug is the name of the menu in lowercase
// and without any spaces like a post slug. If your menu name is Main Menu:
https://yourwpsite.com/wp-json/menus/v1/wp-menus/main-menu

== Changelog ==

= 1.0 =
* Requires PHP 5.6x at the very least.