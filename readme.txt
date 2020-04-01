=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: reviews, ratings, testimonials, business reviews, product reviews, stars, star ratings
Tested up to: 5.4
Requires at least: 4.7.0
Requires PHP: 5.6
Stable tag: 4.5.5
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews allows you to easily receive and display reviews for your website, business, or products.

== Description ==

Site Reviews allows your visitors to submit reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp.

The plugin provides blocks, shortcodes, and widgets, along with full documentation.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and much more.

[Follow plugin development on Github.](https://github.com/pryley/site-reviews/)

= Current Features =

- Actively developed and supported
- Asian language support
- Avatars: Provided by the WordPress Gravatar service
- Backup and restore your plugin settings as needed
- Bayesian Ranking: Easily sort pages with assigned reviews by rank (using the bayesian algorithm) in your custom WP_Query
- Blacklist words, phrases, IP addresses, names, and emails
- Blockchain Validation: Verify your reviews on the Blockchain with Trustalyze
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
- Styles: Change the submission form style to match popular themes and form plugins
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

= 4.5.5 (2020-03-31) =

- Fixed PHP error thrown when upgrading from < v4.4.0

= 4.5.4 (2020-03-30) =

- Updated the author column on the "All Reviews" page to link to the author if they exist

= 4.5.3 (2020-03-29) =

- Fixed translation filters to only load if translations exist
- Updated Trustalyze integration notice

= 4.5.2 (2020-03-20) =

- Fixed unicode character support

= 4.5.1 (2020-03-19) =

- Fixed addon license setting
- Fixed Divi plugin style
- Fixed form submission on IE 11

= 4.5.0 (2020-02-24) =

- Fixed compatibility with misbehaving plugins that break the Settings tabs
- Fixed potential errors when upgrading from versions prior to 4.3.8
- Fixed the "Recalculate Summary Counts" tool to work correctly after pages with assigned reviews are cloned
- Updated the Rebusify integration as they have rebranded to [Trustalyze](https://trustalyze.com?ref=105)

= 4.4.0 (2020-02-12) =

- Added an invitation to try the unreleased beta version of the Images add-on
- Added custom capabilities which can be added to your user roles
- Fixed compatibility with Fusion Builder
- Fixed plugin migrations
- Updated the Common Problems and Solutions section on the Help page
- Updated the FAQ page

= 4.3.8 (2020-02-07) =

- Fixed a possible cross-site scripting vulnerability in the submission form

= 4.3.7 (2020-01-31) =

- Added permission validation to the submission form
- Fixed schema to not show if there are no reviews or ratings

= 4.3.2 (2020-01-28) =

- Added migration tool for cases when auto-migration is not triggered when upgrading to version 4

= 4.3.1 (2020-01-14) =

- Fixed summary percentage bars when rating count is empty

= 4.3.0 (2020-01-11) =

- Added update functionality for add-ons
- Fixed assigned_to links
- Fixed avatar regeneration
- Fixed review helper functions
- Fixed shortcode defaults
- Fixed stars when MAX_RATING is greater than 5
- Fixed summary rating calculation
- Fixed validation of multi-value fields
- Updated the version requirement checks

= 4.2.9 (2019-12-06) =

- Added "site-reviews/review-limits/validate" filter hook
- Updated the "Translations" settings page

= 4.2.8 (2019-12-05) =

- Fixed generated ID attribute used in forms to adhere to the HTML5 spec
- Fixed WPML integration
- Fixed PHP warning thrown when searching reviews in wp-admin

= 4.2.7 (2019-11-15) =

- Changed "site-reviews/rating/average" hook
- Fixed review limits by email and IP address

= 4.2.5 (2019-11-13) =

- Fixed documentation for glsr_create_review()
- Fixed pagination when WordPress is installed in a subdirectory

= 4.2.4 (2019-11-10) =

- Added "glsr-is-valid" class to form fields on successful validation
- Fixed CSS conflicts in the WordPress Admin
- Fixed IE11 compatibility
- Fixed plugin settings
- Fixed upgrade migrations from version 2

= 4.2.3 (2019-11-01) =

- Improved the javascript used with the submission form

= 4.2.2 (2019-10-31) =

- Fixed a potential 503 HTTP error triggered when a shortcode with the schema option enabled is used inside "the_content" hook

= 4.2.0 (2019-10-29) =

- Added WordPress v5.3 compatibility
- Changed "site-reviews/support/deprecated" filter hook to "site-reviews/support/deprecated/v4"
- Fixed pagination of reviews on static front page
- Fixed performance issues related to IP Address detection
- Fixed System Info when ini_get() function is disabled
- Rebuilt the WordPress Editor Blocks

= 4.1.1 (2019-10-17) =

- Added "site-reviews/support/deprecated/v4" filter hook
- Fixed potential SSL error when fetching Cloudflare IP ranges
- Optimised translation usage

= 4.1.0 (2019-10-16) =

- Added "Email", "IP Address", and "Response" columns for the reviews table
- Changed [site_reviews] "count" option name to "display" (i.e. [site_reviews display=10])
- Changed glsr_get_reviews() "count" option name to "per_page" (i.e. glsr_get_reviews(['per_page' => 10]))</li>
- Fixed column sorting on the reviews table
- Fixed pagination links from triggering in the editor block
- Fixed pagination with hidden review fields
- Updated the "Common Problems and Solutions" help section

= 4.0.7 (2019-10-10) =

- Fixed a possible HTML5 validation issue in the plugin settings

= 4.0.6 (2019-10-09) =

- Changed the parameter order of an internal hook
- Fixed plugin uninstall
- Fixed translations for default text that include a HTML link

= 4.0.5 (2019-10-07) =

- Fixed email template tags

= 4.0.4 (2019-10-07) =

- Fixed IP address detection for servers that do not support IPv6
- Fixed pagination when using the default count of 5 reviews per page
- Fixed plugin migration on update
- Fixed possible PHP compatibility issues

= 4.0.0 (2019-10-06) =

- Added Multisite support
- Added product schema price options
- Added proxy header support for IP detection
- Added [Rebusify integration](https://trustalyze.com?ref=105) to sync reviews to the blockchain
- Added setting to choose the name format of the review author
- Added setting to choose which blacklist to use
- Added setting to limit review submissions
- Added widget icons in the WordPress customizer
- Added WPML integration for summary counts
- Changed category assignment to one-per-review
- Fixed badge counter in menu when reviews are approved/unapproved
- Fixed overriding star styles on the "Add plugin" page
- Fixed per-page limit in the Reviews block
- Fixed PHP 7.2 support
- Fixed review counts
- Fixed review menu counts from changing when approving/unapproving comments
- Fixed review revert button
- Fixed star-rating CSS when using the helper function
- Fixed upgrade process when updating to a major plugin version
- Improved ajax pagination
- Improved documentation
- Improved email failure logging
- Improved internal console usage
- Improved system info
- Removed $_SESSION usage
- Updated FAQs
- Updated plugin hooks
- Updated templates
