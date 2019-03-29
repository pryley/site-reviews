=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: reviews, ratings, business ratings, business reviews, testimonials, site reviews, star rating, wp rating, wp review, wp testimonials
Requires at least: 4.7.0
Tested up to: 5.1
Requires PHP: 5.6
Stable tag: 3.5.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews allows you to easily receive and display reviews for your website, business, or products.

== Description ==

> Site Reviews requires PHP v5.6 or higher!

Site Reviews allows your visitors to submit reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp.

The plugin provides blocks, shortcodes, and widgets, along with full documentation.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and much more.

[Follow plugin development on Github.](https://github.com/pryley/site-reviews/)

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
- Rating Snippets in Google search results (schema.org JSON-LD markup)
- Relative dates
- Review avatars (gravatar.com)
- Review blacklist
- Review categories
- Review responses
- Reviews summary
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
Use the provided shortcodes, widgets, and blocks on your website. Make sure to view the screenshots above and read the Documentation page included in the plugin menu (once installed).

= I need help with something else =
All documentation can be found in the "Documentation" page of the plugin. If your question is not answered there, please create a new topic in the [WordPress support forum](https://wordpress.org/support/plugin/site-reviews/).

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

= 3.5.4 (2019-03-29) =

- Fixed compatibility with Woocommerce when using the "Product" schema type

= 3.5.3 (2019-03-27) =

- Fixed activation check for PHP versions < 5.4
- Fixed possible error when submitting a review where the submission response is never shown

= 3.5.2 (2019-03-06) =

- Changed supported WP version to 5.1
- Fixed possible compatibility issues with other plugins
- Removed ABSPATH from log entries

= 3.5.1 (2019-03-04) =

- Fixed line-breaks in reviews when the "Enable Excerpts" option is disabled

= 3.5.0 (2019-02-21) =

- Added a "fallback" option to the [site_reviews] shortcode (see documentation)
- Added an "Enable Fallback Text" option in the plugin settings that can be shown by default when there are no reviews to display
- Added informational text to the Site Reviews blocks when all fields are hidden
- Fixed the "Assigned To" metabox search dropdown

= 3.4.0 (2019-02-18) =

- Added ability to assign reviews using "parent_id"

= 3.3.1 (2019-02-17) =

- Fixed bug (introduced in v3.2.6) with submitted reviews incorrectly handling GMT timezones

= 3.3.0 (2019-02-14) =

- Added "glsr_star_rating" helper function (see documentation)

= 3.2.6 (2019-02-13) =

- Fixed review date from changing when approved

= 3.2.5 (2019-02-11) =

- Fixed sort-by-author in reviews table

= 3.2.4 (2019-02-10) =

- Fixed category assignment

= 3.2.3 (2019-02-08) =

- Fixed conflict with comment moderation

= 3.2.2 (2019-02-07) =

- Fixed potential issue with star-ratings

= 3.2.1 (2019-02-05) =

- Added "site-reviews/review/redirect" hook
- Fixed documentation

= 3.2.0 (2019-01-26) =

- Added the ability to render reviews that are fetched with the helper functions (see documentation)
- Fixed a potential PHP error due to plugins or themes incorrectly using [apply_filter](http://developer.wordpress.org/reference/functions/add_filter/)
- Updated documentation

= 3.1.11 (2019-01-24) =

- Changed "submit-button.php" templates for all plugin styles
- Fixed issue with cloudflare IP detection

= 3.1.10 (2019-01-22) =

- Fixed plugin styles
- Fixed potential issue with AJAX requests

= 3.1.9 (2019-01-21) =

- Changed "submit-button.php" template
- Fixed button style
- Fixed documentation for "glsr_create_review" helper function
- Fixed potential PHP error
- Removed "No Title" fallback in "/wp-admin" for reviews with no title (WordPress handles this)

= 3.1.8 (2019-01-17) =

- Changed Translations to allow <span> tags
- Fixed isolation for Block CSS styles
- Fixed potential javascript error with pagination

= 3.1.7 (2019-01-14) =

- Fixed Akismet integration
- Fixed Blacklist IP validation
- Fixed a potential [modsecurity false positive](https://github.com/client9/libinjection/issues/145)

= 3.1.5 (2019-01-08) =

- Fixed custom templating

= 3.1.4 (2019-01-07) =

- Changed plugin shortcodes to allow add-on integration
- Fixed a possible javascript conflict that breaks form validation
- Fixed error when "Slack webhook URL" setting is empty
- Fixed multi-checkbox values in plugin settings
- Fixed possible duplicate star-rating controls
- Fixed rating counts for review categories
- Removed internal "site-reviews/shortcode/hidden-keys" hook

= 3.1.2 (2019-01-03) =

- Fixed compatibility issue with PHP v7.0.x

= 3.1.1 (2019-01-01) =

- Fixed support for Microsoft IIS

= 3.1.0 (2018-12-30) =

- Added compatibility for WP Super Cache plugin
- Added helper function to recalculate ratings
- Added safe method of using plugin functions without having to use `function_exists()` (see documentation)
- Added support for multiple shortcode buttons on the same page (i.e. when using multiple classic wysiwyg editors)
- Changed "minimum rating" range in the block options to allow a rating of "0" (to show reviews with no rating)
- Fixed summary counts when the "Require Approval" option is enabled
- Fixed the "All Reviews" page to update the status counts when a review is approved/unapproved
- Fixed the "Recalculate rating counts" notice

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

- Fixed PHP warning when calculating review counts due to reviews not having their review_type set correctly (possible due to incorrectly importing 3rd-party reviews)

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
