=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: best reviews, business ratings, business reviews, curated reviews, moderated reviews, rating widget, rating, ratings shortcode, review widget, reviews login, reviews shortcode, reviews, simple reviews, site reviews, star rating, star review, submit review, testimonial, user rating, user review, user reviews, wp rating, wp review, wp testimonials
Requires at least: 4.0.0
Tested up to: 5.0
Requires PHP: 5.4
Stable tag: 2.17.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews is a WordPress plugin which allows you to easily receive and display reviews for your website and business.

== Description ==

**ATTENTION: This plugin requires your server to be running a minimum of PHP v5.4 and WordPress 4.0.0. The plugin will NOT work If your server does not meet these minimum requirements.**

Site Reviews is a plugin that allows your visitors to submit site reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp, and then allows you to display them using a widget or shortcode.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and more. The plugin provides both widgets and shortcodes along with full shortcode documentation.

Add-ons are being developed to support syncing your TripAdvisor and Yelp reviews in order to display them locally on your website, as well as Post/Page/CPT/Comment ratings/reviews.

The plugin [roadmap](https://github.com/geminilabs/site-reviews/blob/master/ROADMAP.md) includes tentative upcoming features.

Follow plugin development on [GitHub](https://github.com/geminilabs/site-reviews/).

= Current Features =

- Actively developed and supported
- Ajax pagination of reviews
- Akismet integration to provide spam-validation
- Assign reviews to a Post/Page ID
- Autocomplete AssignedTo metabox
- Bayesian ranking for posts with assigned reviews (can be used for WP_Query sorting)
- Blacklist reviewers
- Clean and easy-to-configure user interface
- Configurable Widgets
- Custom notifications (including Slack support)
- Easy setup and implementation
- Filter reviews by rating
- Helper functions to easily access review meta
- Honeypot (spam trap) implemented in the submission form
- jQuery is NOT required!
- Logging
- MCE shortcode button dropdown
- Minimal widget styling (tested with all official WP themes)
- Publicly respond to a review
- Relative dates option
- Review avatars (gravatar.com)
- Review categories
- Review pagination
- Reviews Summary shortcode: [site_reviews_summary]
- Rich snippets for reviews (schema.org)
- RTL support
- Shortcodes: Display reviews in your post content and templates
- Supports Internet Explorer 9 and 10 by using polyfill.io
- Translate any plugin text
- Use Google's Invisible reCAPTCHA on submission forms
- WordPress.org support
- WP Filter Hooks

== Installation ==

= Minimum plugin requirements =

- WordPress 4.0.0
- PHP 5.4

= Automatic installation =

Log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Site Reviews" and click Search Plugins. Once you have found the plugin you can view details about it such as the point release, rating and description. You can install it by simply clicking "Install Now".

= Manual installation =

Download the Site Reviews plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How do I use Site Reviews? =
Please watch the [Getting Started with Site Reviews Screencast](https://github.com/geminilabs/site-reviews/wiki/Getting-Started-with-Site-Reviews) for a quick step-by-step visual on how to get started with Site Reviews.

= How do I send you a translation of Site Reviews in my language? =
It’s super easy to contribute a translation in your language.

1. Visit [Translating WordPress](https://translate.wordpress.org/projects/wp-plugins/site-reviews/dev)
2. Select your language
3. Suggest translations!

= How do I change the order of the Submission Form fields? =
[Custom Submission Form Field Order](https://github.com/geminilabs/site-reviews/wiki/Custom-Submission-Form-Field-Order)

= How do I change the order of the review content? =
[How to change the order of the parts of a rendered review](https://github.com/geminilabs/site-reviews/wiki/How-to-change-the-order-of-the-parts-of-a-rendered-review)

= How do I customise the JSON–LD schema of Site Reviews? =
[How to add additional values to Site Reviews JSON–LD schema](https://github.com/geminilabs/site-reviews/wiki/How-to-add-additional-values-to-Site-Reviews-JSON%E2%80%93LD-schema)

= How do I disable the star rating on the submission form? =
[How to disable the star rating on the submission form](https://github.com/geminilabs/site-reviews/wiki/How-to-disable-the-star-rating-on-the-submission-form)

= How do I limit review submissions to one review per email? =
[How to limit review submissions to one review per email](https://github.com/geminilabs/site-reviews/wiki/How-to-limit-review-submissions-to-one-review-per-email)

= How to query and sort pages with assigned reviews by their overall score? =
[How to query and sort posts pages that have assigned reviews by their ranking](https://github.com/geminilabs/site-reviews/wiki/How-to-query-and-sort-posts-pages-that-have-assigned-reviews-by-their-ranking)

= I need help with something else =
All documentation can be found in the "Get Help" page of the plugin. You can also visit the [Site Reviews Wiki](https://github.com/geminilabs/site-reviews/wiki) for tips on advanced usage.

== Screenshots ==

1. A view of the All Reviews page

2. A view of the Edit Review page

3. A view of the MCE shortcode dropdown button

4. A view of the Site Reviews &gt; Settings &gt; General page

5. A view of the Site Reviews &gt; Settings &gt; Translations page

6. A view of the Site Reviews &gt; Get Help &gt; Documentation tab

7. A view of the Site Reviews &gt; Get Help &gt; System Info tab

8. A view of the Recent Site Reviews widget settings

9. A view of the Submit a Site Review widget settings

10. How the Recent Site Reviews widget/shortcode looks like using the Twenty Sixteen WordPress theme

11. How the Submit a Site Review widget/shortcode looks like using the Twenty Sixteen WordPress theme

12. How the Site Reviews Summary shortcode looks like using the Twenty Sixteen WordPress theme

13. How the Slack notifications look like

14. Add-Ons are being built to extend the functionality on the Site Reviews plugin

== Changelog ==

= 2.17.1 (2018-12-12) =
- Fixed WordPress 5.0 compatibility

= 2.17.0 (2018-10-31) =
- Added a v3.0 notice to PHP 5.4 users to inform of upcoming changes
- Changed polyfill.io to use the minimfied version of the script
- Fixed tinymce button

= 2.16.6 (2018-08-25) =
- Fixed bug that allowed possible namespace collisions with other plugin/themes

= 2.16.5 (2018-08-21) =
- Allow links in custom translations

= 2.16.4 (2018-08-21) =
- Added "site-reviews/async-scripts" and "site-reviews/defer-scripts" filter hooks
- Added documentation on how to disable the polyfill.io script if it is not needed (the polyfill.io script is used to add browser support for Internet Explorer 9 and 10).

= 2.16.3 (2018-08-06) =
- Fixed compatibility for WordPress 4.0-4.6
- Fixed general compatibility with IE 11
- Fixed Slack notification links

= 2.16.1 (2018-07-14) =
- Added additional schema settings for LocalBusiness and Product
- Fixed star rating control on some mobile devices

= 2.15.11 (2018-07-09) =
- Fixed a compatibility issue with [Speed Booster Pack](https://wordpress.org/plugins/speed-booster-pack/)

= 2.15.10 (2018-07-05) =
- Disabled the nonce ajax check from the submission form if the user is not logged in. This should solve some conflicts with other plugins and themes which perform custom caching or speed optimizations
- Updated the activation check in Site Reviews which checks for minimum system requirements

= 2.15.9 (2018-06-27) =
- Fixed a possible jQuery conflict with other plugins

= 2.15.8 (2018-06-12) =
- Adjusted validation strings

= 2.15.7 (2018-06-08) =
- Fixed a compatibility issue with [Page Builder by SiteOrigin](https://wordpress.org/plugins/siteorigin-panels/)

= 2.15.6 (2018-05-31) =
- Allow to hide the avatar if it is enabled

= 2.15.5 (2018-05-28) =
- Fixed compatibility with [Elementor](https://wordpress.org/plugins/elementor/)
- Fixed en_US language header

= 2.15.4 (2018-05-23) =
- Added schema options to not set a default value
- Strip tags and shortcodes from the schema description when a theme has incorrectly disabled the WordPress "the_excerpt" filters

= 2.15.3 (2018-05-18) =
- Fixed a possible cross-site scripting vulnerability

= 2.15.2 (2018-05-14) =
- Fixed the schema to link to any Woocommerce product schema

= 2.15.1 (2018-05-11) =
- Fixed accessibility for the star-rating widget. You can now TAB to the rating field and use the arrow keys to set the rating.

= 2.15.0 (2018-05-03) =
- Added RTL (right-to-left) support
- Fixed submission form id

= 2.14.3 (2018-04-29) =
- Fixed submission date for newly-submitted unapproved reviews

= 2.14.2 (2018-04-28) =
- Fixed the [site_reviews_form] "id" option

= 2.14.1 (2018-04-20) =
- Added a [site_reviews_summary] "show_if_empty" attribute (see shortcode documentation for details)
- Fixed the shortcode dropdown button when the visual editor is disabled

= 2.14.0 (2018-03-25) =
- Added a loading indicator on the submit button
- Added a ".glsr-loading" class to the form when submitting
- Fixed custom post_status labels

= 2.13.2 (2018-03-13) =
- [feature] Average rating values are stored in posts that have assigned reviews (can be used for query sorting)
- Fixed non-ajax form submissions

= 2.12.5 (2018-02-16) =
- Added a [site_reviews_summary] "text" attribute (see shortcode documentation for details)
- Fixed star-ratings not rendering correctly from an ajax request

= 2.12.3 (2018-02-11) =
- Fixed schema DateTime error
- Fixed system info plugin settings formatting

= 2.12.2 (2018-02-10) =
- [feature] Review blacklist
- Notification errors are now logged
- Removed "site-reviews/notification/template-tags" filter hook (use "site-reviews/email/compose" instead)
- Show the reviewer's IP address in the details metabox
- Fixed plugin notices
- Fixed review rendering to check for an assigned_to value
- Fixed Slack notifications
- Fixed the post ranking calculation on new review submissions

= 2.11.3 (2018-01-31) =
- Fixed plugin session management

= 2.11.2 (2018-01-30) =
- [feature] Added an autocomplete searchbox in the Assigned To metabox
- [feature] Added Akismet integration to provide spam-validation
- Added a default subject to the mailto link in the details metabox
- Added a link to the WordPress user who submitted the review in the details metabox
- Changed author name default to "Anonymous"
- Fixed a possible error from occuring when the user has manually edited the database
- Fixed internal IP detection

= 2.10.4 (2018-01-26) =
- Fixed the form error message CSS
- Fixed the Invisible reCaptcha plugin integration when multiple HTML forms exist on the same page

= 2.10.3 (2018-01-17) =
- Changed the notification title/subject to show the title of the post a review is assigned to.

= 2.10.2 (2018-01-10) =
- Fixed javascript error (oops!)

= 2.10.1 (2018-01-09) =
- Empty author name defaults to "Anonymous"
- Fixed review pagination on static frontpage

= 2.10.0 (2018-01-08) =
- [feature] Added option to show a link of the assigned post in reviews
- Fixed bug when rendering links of invalid assigned post IDs
- Fixed regeneration of "show more" links
- Fixed review pagination for non-hierarchical post types
- Tweaked styles for twentyfifteen theme
- Tweaked the scroll-to-top functionality of ajax pagination

= 2.9.5 (2018-01-02) =
- Added "offset" option to [site_reviews] shortcode

= 2.9.4 (2018-01-02) =
- Added "site-reviews/local/review/submitted/message" filter hook to modify the successful submission message.
- Added a new helper function for logging variables: `glsr_log()`

= 2.9.3 (2018-01-01) =
- Fix required fields setting

= 2.9.2 (2017-12-13) =
- Fix escaped unicode characters in JSON-LD
- Fix PHP 7.2 compatibility
- Fix white-space for `<br>` in reviews

= 2.9.1 (2017-12-08) =
- Fix "read more" links with ajax pagination

= 2.9.0 (2017-12-08) =
- [feature] Assign a post ID to multiple reviews in bulk
- [feature] Bayesian ranking values are stored in posts that have assigned reviews (can be used for query sorting)
- Fix CSS for themes that do not implement the `.screen-reader-text` class
- Fix System Info PHP error when detecting server IP address

= 2.8.4 (2017-11-16) =
- Added "site-reviews/enqueue/localize" filter hook so that the "ajaxpagination" selector array for fixed elements can be modified.

= 2.8.3 (2017-11-15) =
- Fix a PHP type error when a shortcode contains no arguments

= 2.8.2 (2017-11-15) =
- Added ajax pagination automatic scrolling and loader animation

= 2.8.1 (2017-11-15) =
- Fix ajax pagination when the HTMLElement class attribute has a trailing space

= 2.8.0 (2017-11-13) =
- [feature] Allow ajax pagination of reviews

= 2.7.4 (2017-11-11) =
- Fix activation notice for unsupported PHP and WordPress versions
- Fix database upgrade for people using Site Reviews v2.1.0 or earlier
- Fix deletion of plugin for unsupported PHP versions

= 2.7.3 (2017-11-09) =
- Fix "assign_to" and "assigned_to" widget options

= 2.7.2 (2017-10-31) =
- Fix compatibilty with some themes that use javascript to modify form elements
- Remove recaptcha key/secret from the systemlog

= 2.7.1 (2017-10-24) =
- Fix [site_reviews_summary] class attribute

= 2.7.0 (2017-10-24) =
- Added option to show a register link when login is required to submit a review
- Added "site-reviews/rendered/review-form/login-register" hook

= 2.6.2 (2017-10-19) =
- Only build rating schema for reviews with a rating
- Prevent HTML from being generated for empty review fields
- Remove obsolete schema meta tags

= 2.6.1 (2017-10-19) =
- Added "site-reviews/validation/rules" hook

= 2.6.0 (2017-10-17) =
- [feature] Set whether or not a field is required
- Added "site-reviews/rendered/review" hook
- Added post_id of review to 'site-reviews/local/review/create' hook

= 2.5.2 (2017-08-21) =
- Fix plugin localization

= 2.5.1 (2017-08-10) =
- Added "site-reviews/validate/review/submission" hook

= 2.5.0 (2017-08-08) =
- [feature] Added a Honeypot (spam trap) to the submission form
- Fix Translator to use UTF-8 encoding when converting html entities

= 2.4.5 (2017-08-07) =
- Fix Translator to correctly handle htmlentities in plugin strings

= 2.4.3 (2017-07-29) =
- Fix a possible Translator bug
- Fix "Assigned To" input from updating page on Enter key
- Fix "hide_response" from showing unnecessarily with the TMCE button [site_reviews] shortcode
- Show plugin settings in system info

= 2.4.1 (2017-07-22) =
- Fix the schema URL for a page
- Update screenshots

= 2.4.0 (2017-07-05) =
- [feature] Publicly respond to a review
- Allow multi-line reviews

= 2.3.2 (2017-07-02) =
- Fix a possible translation error from occurring

= 2.3.1 (2017-06-30) =
- Fix hooks documentation

= 2.3.0 (2017-06-26) =
- [feature] Reviews Summary shortcode: [site_reviews_summary]
- [feature] Relative dates option
- [feature] Rich snippets for reviews (schema.org)
- [feature] Translate any plugin text
- [changed] The default minimum rating for displaying reviews has been changed to 1 (instead of 5)
- Added "show more" links to review excerpts
- Extended "assign_to" and "assigned_to" attributes in the widgets and shortcodes to accept "post_id" as a value which automatically equals the current post ID
- Removed "Submission Form" custom text options (replaced by the new Translation options)
- Fix tinymce shortcode dialog tooltips

= 2.2.3 (2017-05-07) =
- Added option to change submit button text

= 2.2.2 (2017-05-06) =
- Added JS event that is triggered on form submission response (site-reviews/after/submission)
- Fix form submission without ajax

= 2.2.1 (2017-05-06) =
- Added hook that runs immediately after a review has successfully been submitted (site-reviews/local/review/submitted)
- Use new IP detection when submitting a review

= 2.2.0 (2017-05-03) =
- [feature] use Google's Invisible reCAPTCHA on submission forms

= 2.1.8 (2017-04-19) =
- Fix [site_reviews] shortcode pagination option
- Fix possible JS race condition which breaks the star rating functionality

= 2.1.6 (2017-04-02) =
- Fix the category feature to work properly when a user was not logged in
- Corectly remove the "create_site-review" capability

= 2.1.3 (2017-04-01) =
- Changed capability requirement to "edit_others_pages"

= 2.1.1 (2017-03-21) =
- Fixed a bug causing reviews to not load correctly introduced by v2.1.0 (sorry!)

= 2.1.0 (2017-03-19) =

- [feature] Assign reviews to a page/post
- [deprecated] The 'post_id' review key is deprecated in favour of 'ID' in reviews returned from the `glsr_get_review()` and `glsr_get_review()` functions
- Added hook that runs immediately after a review has been created

= 2.0.4 (2017-03-09) =

- Fix WordPress customizer compatibility (see: https://codex.wordpress.org/Function_Reference/get_current_screen#Usage_Restrictions)

= 2.0.3 (2017-03-09) =

- Fix incorrect plugin update check

= 2.0.2 (2017-01-24) =

- Added hook to filter metabox details

= 2.0.1 (2017-01-23) =

- Prevent the taxonomy object from containing recursion

= 2.0.0 (2017-01-12) =

- [feature] Helper functions to easily access review meta
- [feature] MCE shortcode button dropdown
- [feature] Review avatars (gravatar.com)
- [feature] Review categories
- [breaking] Changed internal widget/shortcode hook names
- [breaking] Changed shortcode variables
- [breaking] Consolidated all plugin settings into a single setting variable
- Ajaxified approve/unapprove
- Custom Published/Pending labels
- New settings page for reviews
- Removed "site-reviews/reviews/excerpt_length" filter hook
- Removed "site-reviews/reviews/use_excerpt" filter hook

= 1.2.2 (2017-01-06) =

- Added hook to change excerpt length
- Added hook to disable the excerpt and instead display the full review content

= 1.2.1 (2016-12-28) =

- Fix PHP 5.4 compatibility regression

= 1.2.0 (2016-12-27) =

- [feature] Send notifications to Slack
- Fix notifications to use the email template setting

= 1.1.1 (2016-12-05) =

- Remove ".star-rating" class on frontend which conflicts with the woocommerce plugin CSS
- Added hooks to modify rendered fields/partials HTML

= 1.1.0 (2016-11-16) =

- [feature] Pagination
- [breaking] Changed internal widget hook names
- [breaking] Changed text-domain to "site-reviews"
- Set post_meta defaults when creating a review
- [addon support] Display read-only reviews
- [addon support] Display widget link options (conditional field)
- [addon support] Show all review types by default in widgets and shortcodes

= 1.0.4 (2016-11-14) =

- use the logged-in user's display_name by default instead of "Anonymous" when submitting reviews
- Fix shortcodes to insert in the_content correctly

= 1.0.3 (2016-11-09) =

- Updated plugin description
- Fix plain-text emails
- Fix inconsistencies with plugin settings form fields
- Fix internal add-on integration code

= 1.0.2 (2016-10-24) =

- Set widget and settings defaults
- Fix PHP error that is thrown when settings have not yet been saved to DB

= 1.0.0 (2016-10-21) =

- Initial plugin release
