=== Site Reviews ===
Contributors: geminilabs, pryley
Donate link: https://www.paypal.me/pryley
Tags: reviews, business reviews, curated reviews, moderated reviews, rating, ratings, business ratings, rating widget, rating shortcode, review widget, reviews shortcode, reviews, simple reviews, site reviews, star rating, star review, submit review, testimonial, user rating, user review, user reviews, wp rating, wp review, wp testimonials
Requires at least: 4.7.0
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 3.0.0-beta
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Site Reviews allows you to easily receive and display reviews for your website, business, or products.

== Description ==

Site Reviews allows your visitors to submit reviews with a 1-5 star rating on your website, similar to the way you would on TripAdvisor or Yelp. You can then display your reviews using the provided widgets or shortcodes.

You can pin your best reviews so that they are always shown first, require approval before new review submissions are published, require visitors to be logged-in in order to write a review, send custom notifications on a new submission, and more. The plugin provides both widgets and shortcodes along with full shortcode documentation.

Various add-ons are available, including those that support syncing your TripAdvisor and Yelp reviews in order to display them on your website.

Follow plugin development on github at: https://github.com/geminilabs/site-reviews-v3/

= Current Features =

- Actively developed and supported
- Ajax pagination of reviews
- Akismet integration to provide spam-validation
- Assign reviews to a Post/Page ID
- Bayesian ranking for posts with assigned reviews (can be used for WP_Query sorting)
- Clean and easy-to-configure interface
- Complete documentation
- Configurable Widgets
- Custom notifications (including Slack support)
- Easy setup and implementation
- Honeypot (spam trap) implemented in the submission form
- Minimal widget styling (tested with all official WP themes)
- Publicly respond to a review
- Relative dates
- Review avatars (gravatar.com)
- Review blacklist
- Review categories
- Rich snippets for reviews (schema.org)
- Shortcode button dropdown in the page editor
- Shortcodes: display reviews in your post content and templates
- Show a summary of your reviews
- Translate any plugin text
- Use Google's Invisible reCAPTCHA on submission forms
- WordPress.org support

== Installation ==

= Minimum plugin requirements =

If your server and website does not meet the minimum requirements shown below, the plugin will automatically deactivate with a notice explaining why.

- WordPress 4.7.0
- PHP 5.6

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

1. https://translate.wordpress.org/projects/wp-plugins/site-reviews/dev
2. Select your language
3. Suggest translations!

= How do I change the order of the Submission Form fields? =
https://github.com/geminilabs/site-reviews/wiki/Custom-Submission-Form-Field-Order

= How do I change the order of the review content? =
https://github.com/geminilabs/site-reviews/wiki/How-to-change-the-order-of-the-parts-of-a-rendered-review

= How do I customise the JSON–LD schema of Site Reviews? =
https://github.com/geminilabs/site-reviews/wiki/How-to-add-additional-values-to-Site-Reviews's-JSON%E2%80%93LD-schema

= How do I disable the star rating on the submission form? =
https://github.com/geminilabs/site-reviews/wiki/How-to-disable-the-star-rating-on-the-submission-form

= How do I limit review submissions to one review per email? =
https://github.com/geminilabs/site-reviews/wiki/How-to-limit-review-submissions-to-one-review-per-email

= How to query and sort pages with assigned reviews by their overall score? =
https://github.com/geminilabs/site-reviews/wiki/How-to-query-and-sort-posts-pages-that-have-assigned-reviews-by-their-ranking

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

= 3.0.0 (2018-08-??) =

- !! Complete rewrite of Site Reviews
- !! Dropped support for legacy web browsers (supports all modern browsers and IE11+)
- !! Dropped support for the twentyten to twentyfourteen themes
- !! Requires PHP 5.6 or greater and WordPress 4.7 or greater
- Added export/Import plugin settings
- Added extended avatar options
- Added form styles to match popular themes and form plugins
- Added Polylang support for multilingual websites
- Added support for Asian languages in excerpt lengths
- Added support for multiple notifications when a review has been submitted
- Improved documentation
- Improved performance for sites with thousands of reviews
- Improved reCAPTCHA compatibility with other plugins
