=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: reviews, ratings, business ratings, business reviews, testimonials, site reviews, star rating, wp rating, wp review, wp testimonials
Requires at least: 4.7.0
Tested up to: 5.0
Requires PHP: 5.6
Stable tag: 3.0.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews allows you to easily receive and display reviews for your website, business, or products.

== Description ==

**Please make sure your website meets the minimum plugin requirements: PHP 5.6, WordPress 4.7**

Site Reviews allows your visitors to submit reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp.

The plugin provides blocks, shortcodes, and widgets, along with full documentation.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and much more.

Follow plugin development on github at: https://github.com/geminilabs/site-reviews/

= Current Features =

- Actively developed and supported
- Ajax pagination of reviews
- Akismet integration for spam-validation
- Asian language support
- Assign reviews to a Post/Page ID
- Backup and restore plugin settings
- Bayesian ranking for posts with assigned reviews (can be used for WP_Query sorting)
- Block Editor support with custom configurable blocks
- Clean and easy-to-configure interface
- Complete documentation
- Configurable Widgets
- Custom notifications (including Slack support)
- Easy setup and implementation
- Honeypot (spam trap) implemented in the submission form
- Plugin styles to match popular themes, form plugins, and CSS frameworks
- Polylang integration for multilingual websites
- Relative dates
- Review avatars (gravatar.com)
- Review blacklist
- Review categories
- Review responses
- Reviews summary
- Rich snippets for reviews (schema.org)
- Shortcode button dropdown in the Classic Editor
- Shortcodes: display reviews in your post content and templates
- Templates
- Translate any plugin text
- Use Google's Invisible reCAPTCHA on submission forms
- WordPress.org support

== Installation ==

= Minimum plugin requirements =

If your server and website does not meet the minimum requirements shown below, the plugin will automatically deactivate and a notice will appear explaining why.

- WordPress 4.7.0
- PHP 5.6

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Site Reviews" and click Search Plugins. Once you have found the plugin you can view details about it such as the point release, rating and description. You can install it by simply clicking "Install Now".

= Manual installation =

Download the Site Reviews plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How do I use Site Reviews? =
Use the provided shortcodes, widgets, and blocks on your website. Make sure to view the screenshots below and read the included documentation (once you have installed the plugin).

= I need help with something else =
All documentation can be found in the "Documentation" page of the plugin.

== Screenshots ==

1. A view of the All Reviews page

2. A view of the Edit Review page

3. A view of the Site Reviews &gt; Settings page

4. A view of the Site Reviews &gt; Tools page where you can export/import the plugin settings

5. Site Reviews provides extensive documentation with the plugin

6. Site Reviews supports the Block Editor and includes blocks for each of the shortcodes

7. Site Reviews adds a dropdown in the Classic Editor to easily add any of the shortcodes

8. A view of the Site Reviews widgets

9. This is what the Slack notifications look like

== Changelog ==

= 3.0.5 (2018-12-26) =

- Fixed compatibility with Woocommerce plugin
- Fixed tinymce button, it should only appear on the primary page editor (classic editor)
- Fixed WP pointer dismissal

= 3.0.4 (2018-12-22) =

- Fixed detection of PHP support for IPv6
- Fixed form submissions

= 3.0.3 (2018-12-21) =

- Fixed PHP error preventing plugin activation when the PHP multibyte extension is not installed

= 3.0.2 (2018-12-21) =

- Fixed PHP warning when calculating review counts due to reviews not having their review_type set correctly (possible due to incorrectly importing 3rd-party reviews).

= 3.0.1 (2018-12-21) =

- Fixed "Contact Form 7" style
- Fixed category option in shortcodes
- Updated FAQ

= 3.0.0 (2018-12-20) =

- !! Complete rewrite of Site Reviews
- !! Dropped support for legacy web browsers (supports all modern browsers and IE11+)
- !! Dropped support for the twentyten to twentyfourteen themes
- !! Requires PHP 5.6 or greater and WordPress 4.7 or greater
- Added additional avatar options
- Added Asian language support in excerpt lengths
- Added configurable WordPress 5.0 blocks for the new Block Editor
- Added export/import plugin tools
- Added form styles to match popular themes and form plugins
- Added multiple notification support
- Added plugin templates for easy cutomisation of the shortcode and widget HTML
- Added Polylang support for multilingual websites
- Fixed star-rating compatibility with Woocommerce themes
- Improved documentation
- Improved performance, especially for sites with thousands of reviews
- Improved reCAPTCHA compatibility with other plugins
