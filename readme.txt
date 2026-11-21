=== Umami Analytics Plugin ===
Contributors: makingtheimpact
Tags: analytics, umami, privacy
Requires at least: 5.0
Tested up to: 6.3
Stable tag: 1.1.0
Requires PHP: 7.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily integrate Umami Analytics with your WordPress site.

== Description ==

This plugin allows you to easily add Umami Analytics tracking to your WordPress site. Umami is a simple, fast, privacy-focused alternative to Google Analytics. 

Features:
* Easy setup - just enter your Website ID and Analytics URL
* Supports Umami 1.x (/umami.js) and Umami 2.x/3.x (/script.js) with an explicit version setting
* Respects user privacy - doesn't track logged-in users
* Lightweight and fast
* Compatible with the latest version of WordPress
* Dashboard widget for quick access to Umami Analytics

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/umami-analytics` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Umami Tracking screen to configure the plugin
4. Enter your Umami Website ID and Analytics URL
5. Leave the Umami version set to 1.x until the analytics server is upgraded. Then select 2.x / 3.x and save.
6. Clear any page cache and verify the new /script.js tracker and /api/send collection requests.

== Frequently Asked Questions ==

= Where do I get my Website ID and Analytics URL? =

You can find these in your Umami Analytics dashboard. If you haven't set up Umami yet, visit [umami.is](https://umami.is) to get started.

== Changelog ==

= 1.1.0 =
* Added explicit Umami 1.x or Umami 2.x/3.x tracker selection.
* Existing installations default to Umami 1.x, so deploying the plugin before the server migration does not change tracking.
* Retained existing Website ID and analytics URL settings.

= 1.0.23 =
* Added error handling for missing settings
* Improved internationalization
* Updated compatibility with WordPress 6.3

= 1.0.22 =
* Added dashboard widget for quick access to Umami Analytics
* Added localization support
* Added privacy policy text to the settings page

= 1.0.21 =
* Improved settings page layout and instructions

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.22 =
This version adds a dashboard widget and improves the settings page. Upgrade for a better user experience.

== Screenshots ==

1. Umami Tracking Settings Page
2. View Analytics Page
3. Dashboard Widget