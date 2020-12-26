=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: reviews, ratings, testimonials, business reviews, product reviews, stars, star ratings
Tested up to: 5.6
Requires at least: 5.5
Requires PHP: 5.6
Stable tag: 5.4.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews allows you to easily receive and display reviews for your website, business, or products.

== Description ==

Site Reviews allows your visitors to submit reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp.

The plugin provides blocks, shortcodes, and widgets, along with full documentation.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and much more.

[Follow plugin development on Github.](https://github.com/pryley/site-reviews/)

Images adapted from [freepik](https://www.freepik.com).

= Current Features =

- Actively developed and supported
- Asian language support
- Avatars: Provided by the WordPress Gravatar service
- Backup and restore your plugin settings as needed
- Bayesian Ranking: Easily sort pages with assigned reviews by rank (using the bayesian algorithm) in your custom WP_Query
- Blacklist words, phrases, IP addresses, names, and emails
- Categories: Add your own categories and assign reviews to them.
- Developer Friendly: Designed for WordPress developers with over 100 filter hooks and convenient functions
- Documentation: FAQ and documenation for hooks and all shortcodes and functions
- Easy setup and implementation
- Editor Blocks that allow full customisation
- JSON-LD Schema: Display your reviews and ratings in search results
- Multilingual: Integrates with Polylang and WPML and provides easy search/replace translation
- Multisite Support
- Notifications: Send notifications to one or more emails when a review is submitted
- Page Assignment: Assign reviews to Posts, Pages, and Custom Post Types (i.e. Products)
- Pagination: Display a set number of reviews per-page with AJAX
- Relative dates
- Responses: Write responses to reviews
- Restrictions: Require approval before publishing reviews, restrict review submissions to registered users, and limit review submissions by email address, IP address, or username
- Review Summaries: Display a summary of your review ratings from high to low.
- Shortcodes: Configurable shortcodes complete with full documentation
- Slack Integration: Receive notifications in Slack when a review is submitted
- SPAM Protection: Built-in Honeypot protection; integrate with Invisible reCAPTCHA and Akismet
- Styles: Change the review form style to match popular themes and form plugins
- Support: Free premium-level support included on the WordPress.org support forum
- Templates: Use the Site Reviews templates in your theme for full control over the HTML
- Widgets: Configurable widgets for your sidebars

== Installation ==

= Minimum plugin requirements =

- WordPress 4.7.0
- PHP 5.6

If your server and website does not meet the minimum requirements shown below, the plugin will automatically deactivate and a notice will appear explaining why.

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New". In the search field type "Site Reviews" and click Search Plugins. Once you have found the plugin, click "Install Now".

= Manual installation =

Download the Site Reviews plugin and upload it to your server with your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How do I use Site Reviews? =
Use the provided shortcodes, widgets, and blocks on your website. Make sure to also view the screenshots above and read the Help page included in the plugin menu once the plugin has been installed and activated.

= I need help with something else =
All documentation can be found in the "Help" page of the plugin. If your question is not answered there, please create a new topic in the [WordPress support forum](https://wordpress.org/support/plugin/site-reviews/).

== Screenshots ==

1. The "All Reviews" page.

2. The "Edit Review" page.

3. Site Reviews has full support for the new Blocks Editor and includes custom blocks for each of the shortcodes.

4. If you are still using the Classic page editor, you can use the Site Reviews  dropdown button to easily add any of the shortcodes.

5. Site Reviews includes three widgets.

6. Site Reviews includes a Help page with answers to frequently asked questions and complete documentation for all available shortcode options.

7. The "General" settings page.

8. The "Reviews" settings page.

9. The "Submissions" settings page.

10. The "Schema" settings page.

11. Easily add a custom translation for any text in the plugin.

12. Export/import the plugin settings, and perform various other tasks.

13. If you experience any issues with the plugin, the console logger is the first pace to look.

14. View information about the configuration of your server, your website, and the Site Reviews plugin.

15. This is what the Slack notifications look like.

== Changelog ==

= 5.4.2 (2020-12-26) =

- Fixed line breaks in excerpts

= 5.4.1 (2020-12-24) =

- Fixed links in review responses to allow the "rel" attribute
- Happy Holidays! 🎄✨🍾

= 5.4.0 (2020-12-22) =

- Added the "schema_identifier" meta key which can be used to set a custom '@id' schema property with the Custom Fields metabox
- Added the "Woocommerce Reviews" add-on
- Fixed a PHP 8 error with the schema
- Fixed setting fields to use escaped HTML attributes

= 5.3.5 (2020-12-16) =

- Fixed template tag substitution in custom review templates

= 5.3.4 (2020-12-16) =

- Fixed pagination to use the saved "hide" options
- Fixed the import tool, it no longer substitutes empty review name/email fields with those of the logged in user

= 5.3.3 (2020-12-14) =

- Fixed add-on updater

= 5.3.2 (2020-12-13) =

- Added "Site Reviews Premium"
- Added "Review Forms" add-on
- Added debug logging for validation errors
- Added error logging for database table creation errors
- Added support for PHP 8
- Added the Category and Review IDs to the action row in the admin tables
- Added the {{ assigned_posts }}, {{ assigned_users }}, and {{ assigned_terms }} template tags
- Fixed block attributes
- Fixed line-breaks in review excerpts
- Fixed pagination URLs for servers that do not use REQUEST_URI
- Fixed support for older custom fields using assign_to/category as names
- Fixed the Backtrace used when logging entries to the Console
- Fixed the Console on sites that have been duplicated but still have the upload dir cached to the old path
- Improved the block options
- Improved the documentation
- Improved the System Info

= 5.2.3 (2020-11-30) =

- Fixed the PHP multibyte fallback when the iconv extension is missing

= 5.2.2 (2020-11-17) =

- Fixed shortcode examples in documentation; Copy/pasting a shortcode example into the classic editor will now paste as plain text instead of as HTML code.
- Fixed System Info details to always be in English

= 5.2.1 (2020-11-15) =

- Fixed MariaDB support (removed subqueries from the SQL)
- Fixed migration of imported settings
- Fixed the "post__in" and "post__not_in" options of the glsr_get_reviews() helper function

= 5.2.0 (2020-11-06) =

- Added Notification Template tags for assigned categories, posts, and users
- Added Review Assignment setting
- Changed review assignment in SQL queries to use strict assignments by default (it was previously using loose assignments, use the new "Review Assignment" setting to change this back)
- Changed the glsr_create_review function to log validation errors to the plugin console
- Fixed Bulk Editing of reviews that are assigned to post types or users
- Fixed Multibyte String support
- Fixed Multisite compatibility
- Fixed pagination URLs when used on the homepage
- Fixed rating validation when using a custom maximum rating value
- Fixed review limits validation for assigned reviews
- Fixed review migration of invalid 3rd-party reviews (reviews that were previously imported incorrectly)
- Fixed review name and email fallback values to use those of the logged-in user
- Fixed the submission date of reviews, it now uses the timezone offset in the WordPress settings

= 5.1.6 (2020-10-25) =

- Fixed compatibility issue with the Elementor Pro Popups
- Fixed the glsr_create_review helper function validation

= 5.1.4 (2020-10-25) =

- Fixed addons notice styling and placement
- Fixed plugin file paths on IIS Windows servers
- Fixed plugin migrations to work better with the W3 Total Cache plugin
- Fixed strict standard notices in PHP 5.6

= 5.1.0 (2020-10-24) =

- Fixed database integration with WordPress tables that still use the old MyISAM engine
- Fixed the submission date of reviews to account for the timezone
- Improved the plugin migration notice

= 5.0.3 (2020-10-23) =

- Added back the deprecated "count" option on the [site_reviews] shortcode so that it will still work for people who have not yet replaced it with the "display" option.
- Fixed a regression which prevented translations from including a link (i.e. the terms toggle)
- Fixed the terms toggle validation
- Fixed the trustalyze add-on link

= 5.0.0 (2020-10-22) =

- Added "Delete data on uninstall" option to selectively delete plugin data when removing the plugin
- Added "Send Emails From" option to send notifications from a custom email address
- Added a loading="lazy" attribute to avatar images
- Added a new Review Details metabox which allows you to modify review values
- Added a tool to import 3rd-party reviews
- Added a tool to test IP address detection
- Added assigned_posts shortcode option, this replaces the "assign_to" and "assigned_to" options and allows you to assign reviews to multiple Post IDs
- Added assigned_terms shortcode option, this replaces the "category" option and allows you to assign reviews to multiple Categories
- Added assigned_users shortcode option, this allows you to assign reviews to multiple User IDs
- Added [suggested privacy policy content](https://wordpress.org/support/article/wordpress-privacy/#privacy-policy-editing-helper)
- Added the submitted review to the "site-reviews/after/submission" javascript event
- Added [WordPress Personal Data Eraser](https://wordpress.org/support/article/wordpress-privacy/#erase-personal-data-tool) integration
- Added [WordPress Personal Data Exporter](https://wordpress.org/support/article/wordpress-privacy/#export-personal-data-tool) integration
- Added [WordPress Revision](https://wordpress.org/support/article/revisions/) integration
- Changed the assigned_to hide option to assigned_links (i.e. [site_reviews hide="assigned_links"])
- Changed the minimum PHP version to 5.6.20
- Changed the minimum WordPress version to 5.5
- Changed the review limit validation to perform strict checking for assigned posts, categories and users (AND instead of OR)
- Changed the settings to use the WordPress "Disallowed Comment Keys" option by default
- Changed the "site-reviews/rating/average" filter hook argument order (see the Upgrade Guide on the Site Reviews welcome page)
- Fixed compatibility with the Divi theme and Divi Builder plugin
- Fixed compatibility with the Elementor Pro plugin popups
- Fixed compatibility with the GeneratePress Premium plugin
- Fixed compatibility with the Hummingbird Performance plugin
- Fixed compatibility with the Members plugin
- Fixed compatibility with the WP-Optimize plugin
- Fixed compatibility with the WP Super Cache plugin
- Fixed the review summary bars in IE11
- Fixed Welcome page permissions
- Improved console logging
- Improved documentation
- Improved plugin performance with thousands of reviews
- Improved the blocks to visually match the WordPress 5.5 editor style
- Improved the Terms checkbox in the review form to align correctly with the text
- Improved translation settings
- Refreshed the stars SVG images
- Removed the deprecated "count" option from the [site_reviews] ahortcode (use the "display" option instead).
- Removed the "site-reviews/config/forms/submission-form" filter hook (see the Upgrade Guide on the Welcome page)
- Removed the "site-reviews/reviews/reviews-wrapper" filter hook (see the Upgrade Guide on the Welcome page)
- Removed the "site-reviews/submission-form/order" filter hook (see the Upgrade Guide on the Welcome page)
- Removed the glsr_calculate_ratings() helper function
- Removed the Trustalyze integration, it is now an add-on
- Removed tool to calculate rating counts (no longer needed)
- Renamed the glsr_get_rating() helper function to glsr_get_ratings()
- Replaced the assign_to and assigned_to shortcode options with the assigned_posts option
- Replaced the category shortcode option with "assigned_terms" option
- Site Reviews now uses custom database tables, however you may still use the WordPress Export/Import tools as before
- The Translations Settings search results are now restricted to public text that is actually shown on your website, if you would like to change plugin text shown in the WordPress admin, you should use the Loco Translate plugin instead
