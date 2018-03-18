# Change Log

All notable changes to Site Reviews will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security

## [2.17.1] = 2018-12-12

### Fixed
- Fixed WordPress 5.0 compatibility

## [2.17.0] = 2018-10-31

### Added
- Added a v3.0 notice to PHP 5.4 users to inform of upcoming changes

### Changed
- Changed polyfill.io to use the minimfied version of the script

### Fixed
- Fixed tinymce button

## [2.16.6] = 2018-08-25

### Fixed
- Fixed bug that allowed possible namespace collisions with other plugin/themes

## [2.16.5] = 2018-08-21

### Changed
- Allow links in custom translations

## [2.16.4] = 2018-08-21

### Added
- Added "site-reviews/async-scripts" and "site-reviews/defer-scripts" filter hooks
- Added documentation on how to disable the polyfill.io script if it is not needed (the polyfill.io script is used to add browser support for Internet Explorer 9 and 10).

## [2.16.3] = 2018-08-06

### Fixed
- Fixed compatibility for WordPress 4.0-4.6
- Fixed general compatibility with IE 11
- Fixed Slack notification links

## [2.16.1] = 2018-07-14

### Added
- Added additional schema settings for LocalBusiness and Product

### Fixed
- Fixed star rating control on some mobile devices

## [2.15.11] = 2018-07-09

### Fixed
- Fixed a compatibility issue with [Speed Booster Pack](https://wordpress.org/plugins/speed-booster-pack/)

## [2.15.10] = 2018-07-05

### Changed
- Disabled the nonce ajax check from the submission form if the user is not logged in. This should solve some conflicts with other plugins and themes which perform custom caching or speed optimizations
- Updated the activation check in Site Reviews which checks for minimum system requirements

## [2.15.9] = 2018-06-27

### Fixed
- Fixed a possible jQuery conflict with other plugins

## [2.15.8] = 2018-06-12

### Changed
- Adjusted validation strings

## [2.15.7] = 2018-06-08

### Fixed
- Fixed a compatibility issue with [Page Builder by SiteOrigin](https://wordpress.org/plugins/siteorigin-panels/)

## [2.15.6] = 2018-05-31

### Added
- Allow to hide the avatar if it is enabled

## [2.15.5] = 2018-05-28

### Fixed
- Fixed compatibility with [Elementor](https://wordpress.org/plugins/elementor/)
- Fixed en_US language header

## [2.15.4] = 2018-05-23

### Added
- Added schema options to not set a default value

### Fixed
- Strip tags and shortcodes from the schema description when a theme has incorrectly disabled the WordPress "the_excerpt" filters

## [2.15.3] = 2018-05-18

### Security
- Fixed a possible cross-site scripting vulnerability

## [2.15.2] = 2018-05-14

### Fixed
- Fixed the schema to link to Woocommerce product schema

## [2.15.1] = 2018-05-11

### Fixed
- Fixed accessibility for the star-rating widget. You can now TAB to the rating field and use the arrow keys to set the rating.

## [2.15.0] = 2018-05-03

### Added
- Added RTL (right-to-left) support

### Fixed
- Fixed submission form id

## [2.14.3] = 2018-04-29

### Fixed
- Fixed submission date for newly-submitted unapproved reviews

## [2.14.2] = 2018-04-28

### Fixed
- Fixed the [site_reviews_form] "id" option

## [2.14.1] = 2018-04-20

### Added
- Added a [site_reviews_summary] "show_if_empty" attribute (see shortcode documentation for details)

### Fixed
- Fixed the shortcode dropdown button when the visual editor is disabled

## [2.14.0] = 2018-03-25

### Added
- Added a loading indicator on the submit button
- Added a ".glsr-loading" class to the form when submitting

### Fixed
- Fixed custom post_status labels

## [2.13.2] = 2018-03-13

### Added
- [feature] Average rating values are stored in posts that have assigned reviews (can be used for query sorting)

### Fixed
- Fixed non-ajax form submissions

## [2.12.5] = 2018-02-16

### Added
- Added a [site_reviews_summary] "text" attribute (see shortcode documentation for details)

### Fixed
- Fixed star-ratings not rendering correctly from an ajax request

## [2.12.3] = 2018-02-11

### Fixed
- Fixed schema DateTime error
- Fixed system info plugin settings formatting

## [2.12.2] = 2018-02-10

### Added
- [feature] Review blacklist
- Notification errors are now logged
- Show the reviewer's IP address in the details metabox

### Removed
- Removed "site-reviews/notification/template-tags" filter hook (use "site-reviews/email/compose" instead)

### Fixed
- Fixed plugin notices
- Fixed review rendering to check for an assigned_to value
- Fixed Slack notifications
- Fixed the post ranking calculation on new review submissions

## [2.11.3] = 2018-01-31

### Fixed
- Fixed plugin session management

## [2.11.2] = 2018-01-30

### Added
- [feature] Added an autocomplete searchbox in the Assigned To metabox
- [feature] Added Akismet integration to provide spam-validation
- Added a default subject to the mailto link in the details metabox
- Added a link to the WordPress user who submitted the review in the details metabox

### Changed
- Changed author name default to "Anonymous"

### Fixed
- Fixed a possible error from occuring when the user has manually edited the database
- Fixed internal IP detection

## [2.10.4] = 2018-01-26

### Fixed
- Fixed the form error message CSS
- Fixed the Invisible reCaptcha plugin integration when multiple HTML forms exist on the same page

## [2.10.3] = 2018-01-17

### Changed
- Changed the notification title/subject to show the title of the post a review is assigned to.

## [2.10.2] = 2018-01-10

### Fixed
- Fixed javascript error (oops!)

## [2.10.1] = 2018-01-09

### Added
- Empty author name defaults to "Anonymous"

### Fixed
- Fixed review pagination on static frontpage

## [2.10.0] = 2018-01-08

### Added
- [feature] Added option to show a link of the assigned post in reviews

### Fixed
- Fixed bug when rendering links of invalid assigned post IDs
- Fixed regeneration of "show more" links
- Fixed review pagination for non-hierarchical post types

### Changed
- Tweaked styles for twentyfifteen theme
- Tweaked the scroll-to-top functionality of ajax pagination

## [2.9.5] = 2018-01-02

### Added
- Added "offset" option to [site_reviews] shortcode

## [2.9.4] = 2018-01-02

### Added
- Added "site-reviews/local/review/submitted/message" filter hook to modify the successful submission message.
- Added a new helper function for logging variables: `glsr_log()`

## [2.9.3] = 2018-01-01

### Fixed
- Fix required fields setting

## [2.9.2] = 2017-12-13

### Fixed
- Fix escaped unicode characters in JSON-LD
- Fix PHP 7.2 compatibility
- Fix white-space for `<br>` in reviews

## [2.9.1] = 2017-12-08

### Fixed
- Fix "read more" links with ajax pagination

## [2.9.0] = 2017-12-08

### Added
- [feature] Assign a post ID to multiple reviews in bulk
- [feature] Bayesian ranking values are stored in posts that have assigned reviews (can be used for query sorting)

### Fixed
- Fix CSS for themes that do not implement the `.screen-reader-text` class
- Fix System Info PHP error when detecting server IP address

## [2.8.4] = 2017-11-16

### Added
- Added "site-reviews/enqueue/localize" filter hook so that the "ajaxpagination" selector array for fixed elements can be modified.

## [2.8.3] = 2017-11-15

### Fixed
- Fix a PHP type error when a shortcode contains no arguments

## [2.8.2] = 2017-11-15

### Added
- Added ajax pagination automatic scrolling and loader animation

## [2.8.1] = 2017-11-15

### Fixed
- Fix ajax pagination when the HTMLElement class attribute has a trailing space

## [2.8.0] = 2017-11-13

### Added
- [feature] Allow ajax pagination of reviews

## [2.7.4] = 2017-11-11

### Fixed
- Fix activation notice for unsupported PHP and WordPress versions
- Fix database upgrade for people using Site Reviews v2.1.0 or earlier
- Fix deletion of plugin for unsupported PHP versions

## [2.7.3] = 2017-11-09

### Fixed
- Fix "assign_to" and "assigned_to" widget options

## [2.7.2] = 2017-10-31

### Fixed
- Fix compatibilty with some themes that use javascript to modify form elements

### Security
- Remove recaptcha key/secret from the systemlog

## [2.7.1] = 2017-10-24

### Fixed
- Fix [site_reviews_summary] class attribute

## [2.7.0] = 2017-10-24

### Added
- Added option to show a register link when login is required to submit a review
- Added "site-reviews/rendered/review-form/login-register" hook

## [2.6.2] = 2017-10-19

### Fixed
- Only build rating schema for reviews with a rating
- Prevent HTML from being generated for empty review fields

### Removed
- Remove obsolete schema meta tags

## [2.6.1] = 2017-10-19

### Added
- Added "site-reviews/validation/rules" hook

## [2.6.0] = 2017-10-17

### Added
- [feature] Set whether or not a field is required
- Added "site-reviews/rendered/review" hook
- Added post_id of review to 'site-reviews/local/review/create' hook

## [2.5.2] = 2017-08-21

### Fixed
- Fix plugin localization

## [2.5.1] = 2017-08-10

### Added
- Added "site-reviews/validate/review/submission" hook

## [2.5.0] = 2017-08-08

### Added
- [feature] Added a Honeypot (spam trap) to the submission form

### Fixed
- Fix Translator to use UTF-8 encoding when converting html entities

## [2.4.5] = 2017-08-07

### Fixed
- Fix Translator to correctly handle htmlentities in plugin strings

## [2.4.3] = 2017-07-29

### Fixed
- Fix a possible Translator bug
- Fix "Assigned To" input from updating page on Enter key
- Fix "hide_response" from showing unnecessarily with the TMCE button [site_reviews] shortcode

### Changed
- Show plugin settings in system info

## [2.4.1] = 2017-07-22

### Changed
- Update screenshots

### Fixed
- Fix the schema URL for a page

## [2.4.0] = 2017-07-05

### Added
- [feature] Publicly respond to a review

### Changed
- Allow multi-line reviews

## [2.3.2] = 2017-07-02

### Fixed
- Fix a possible translation error from occurring

## [2.3.1] = 2017-06-30

### Fixed
- Fix hooks documentation

## [2.3.0] = 2017-06-26

### Added
- [feature] Reviews Summary shortcode: [site_reviews_summary]
- [feature] Relative dates option
- [feature] Rich snippets for reviews (schema.org)
- [feature] Translate any plugin text
- Extended "assign_to" and "assigned_to" attributes in the widgets and shortcodes to accept "post_id" as a value which automatically equals the current post ID
- Review excerpts now have a "show more" link

### Changed
- The default minimum rating for displaying reviews has been changed to 1 (instead of 5)

### Removed
- removed "Submission Form" custom text options (replaced by the new Translation options)

### Fixed
- Fix tinymce shortcode dialog tooltips

## [2.2.3] = 2017-05-07

### Added
- Added option to change submit button text

## [2.2.2] = 2017-05-06

### Added
- Added JS event that is triggered on form submission response (site-reviews/after/submission)

### Fixed
- Fix form submission without ajax

## [2.2.1] = 2017-05-06

### Added
- Added hook that runs immediately after a review has successfully been submitted (site-reviews/local/review/submitted)

### Changed
- use new IP detection when submitting a review

## [2.2.0] = 2017-05-03

### Added
- [feature] use Google's Invisible reCAPTCHA on submission forms

## [2.1.8] = 2017-04-19

### Fixed
- Fix [site_reviews] shortcode pagination option
- Fix possible JS race condition which breaks the star rating functionality

## [2.1.6] = 2017-04-02

### Fixed
- Fix the category feature to work properly when a user was not logged in
- Corectly remove the "create_site-review" capability

## [2.1.3] = 2017-04-01

### Changed
- Changed capability requirement to "edit_others_pages"

## [2.1.1] = 2017-03-21

### Fixed
- Fixed a bug causing reviews to not load correctly introduced by v2.1.0 (sorry!)

## [2.1.0] = 2017-03-19

### Added
- [feature] Assign reviews to a page/post
- Added hook that runs immediately after a review has been created

### Deprecated
- The 'post_id' review key is deprecated in favour of 'ID' in reviews returned from the `glsr_get_review()` and `glsr_get_review()` functions

## [2.0.4] - 2017-03-09

### Fixed
- Fix WordPress customizer compatibility (see: [`get_current_screen()` usage restrictions](https://codex.wordpress.org/Function_Reference/get_current_screen#Usage_Restrictions))

## [2.0.3] - 2017-03-09

### Fixed
- Fix incorrect plugin update check

## [2.0.2] - 2017-01-24

### Added
- Added hook to filter metabox details

## [2.0.1] - 2017-01-23

### Fixed
- Prevent the taxonomy object from containing recursion

## [2.0.0] - 2017-01-12

### Added
- [feature] Helper functions to easily access review meta
- [feature] MCE shortcode button dropdown
- [feature] Review avatars (gravatar.com)
- [feature] Review categories
- Ajaxified approve/unapprove
- Custom Published/Pending labels
- New settings page for reviews

### Changed
- [breaking] Changed internal widget/shortcode hook names
- [breaking] Changed shortcode variables
- [breaking] Consolidated all plugin settings into a single setting variable

### Removed
- Removed "site-reviews/reviews/excerpt_length" filter hook
- Removed "site-reviews/reviews/use_excerpt" filter hook

## [1.2.2] - 2017-01-06

### Added
- Added hook to change excerpt length
- Added hook to disable the excerpt and instead display the full review content

## [1.2.1] - 2016-12-28

### Fixed
- Fix PHP 5.4 compatibility regression

## [1.2.0] - 2016-12-27

### Added
- [feature] Send notifications to Slack

### Fixed
- Fix notifications to use the email template setting

## [1.1.1] - 2016-12-05

### Added
- Added hooks to modify rendered fields/partials HTML

### Changed
- Remove ".star-rating" class on frontend which conflicts with the woocommerce plugin CSS

## [1.1.0] - 2016-11-16

### Added
- [feature] Pagination
- [addon support] Display read-only reviews
- [addon support] Display widget link options (conditional field)

### Changed
- [breaking] Changed internal widget hook names
- [breaking] Changed text-domain to "site-reviews"
- [addon support] Show all review types by default in widgets and shortcodes

### Fixed
- Set post_meta defaults when creating a review

## [1.0.4] - 2016-11-14

### Changed
- Use the logged-in user's display_name by default instead of "Anonymous" when submitting reviews

### Fixed
- Fix shortcodes to insert in the_content correctly

## [1.0.3] - 2016-11-09

### Added
- [addon support] Internal add-on integration

### Changed
- Updated plugin description

### Fixed
- Do not wrap hidden form inputs with HTML
- Fix plain-text emails
- Fix form field values with a falsey attribute
- Prevent a possible infinite recursion loop when setting default settings

## [1.0.2] - 2016-10-24

### Changed
- Set widget and settings defaults

### Fixed
- Fix PHP error that is thrown when settings have not yet been saved to DB

## [1.0.1] - 2016-10-21

### Fixed
- Fix WP screenshots.

## [1.0.0] - 2016-10-21
- Initial release
