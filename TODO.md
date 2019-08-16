# TODO
- [x] Fix: pagination with ajax. We can't use the current method as it does not take into account page DOM modifications with 3rd-party JS
- [x] Add: filter Rating::MAX_RATING const so max rating can be changed
- [x] Add: configurable minimum logging-level
- [x] Add: multisite support (fix System network plugins)
- [x] Add: email domain check for WordPress settings email
- [x] Fix: query that performs the review count does not account for duplicate "review_type" meta data
- [x] Fix: conditional fields are broken in settings
- [x] Remove star-rating helper CSS dependency on .glsr-default parent class
- [x] Update badge counter in menu when reviews are approved/unapproved
- [x] Blacklist option (or documentation) to use the WordPress comment blacklist

- [ ] Store user ID to review / allow viewing all reviews by author
- [ ] Custom classes to track form submission status (i.e. is-spam, has-failed, nonce-failed, etc.)
- [ ] Add option to remove session support

- [ ] Add a notice similar to Polylang: "It seems that you have been using Site Review for some time. I hope that you love it! I would be thrilled if you could give us a [5 stars rating](...)."
- [ ] Add custom post_type permissions
- [ ] Add reCAPTCHA v3
- [ ] Add version rollback feature (ref: WP Rocket)
- [ ] Fallback to Defaults when settings do not exist
- [ ] Fix CSS class should reflect the selected plugin style (i.e. .glsr-minimal)
- [ ] Test counts when deleting multiple reviews
- [ ] Use REST API for ajax calls and remove admin-ajax.php dependency on front-end (cf7)

// hide meta fields
"is_protected_meta" filter return true for any custom field you want to hide.
add_filter('is_protected_meta', 'my_is_protected_meta_filter', 10, 2);
function my_is_protected_meta_filter($protected, $meta_key) {
    return $meta_key == 'test1' ? true : $protected;
}
