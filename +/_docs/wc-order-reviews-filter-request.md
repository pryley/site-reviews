# Customer review request: allow plugins to short-circuit review storage

**Type:** Enhancement
**Component:** Customer review request (`customer_review_request` feature)

## Is your feature request related to a problem?

The Customer review request feature hard-couples review *collection* to review *storage*. `SubmissionHandler::process_rows()` writes straight to the comments table — `wp_update_comment()` on the edit-in-place branch, `wp_insert_comment()` plus comment meta on the insert branch — with no filterable step between "row validated" and "comment written".

Plugins that replace comment-based product reviews with their own storage (Site Reviews, and other review plugins in the same category) currently have exactly one integration point: the post-insert `woocommerce_review_order_submitted` action. That allows convert-after-the-fact, but the comment row has already been created, so every review submitted through the feature exists twice — once in the plugin's storage, once as a comment that exists only as the feature's own bookkeeping (`_review_order_id` eligibility lookups, edit-in-place matching, and `maybe_mark_order_complete()` all query it).

## Describe the solution you'd like

Two filters, following the `pre_*` short-circuit convention (`pre_http_request`, `pre_insert_term`):

**1. Short-circuit the write.** In `SubmissionHandler::process_rows()`, before either write branch:

```php
$pre = apply_filters( 'woocommerce_review_order_pre_handle_review', null, $row, $existing, $order );
if ( null !== $pre ) {
    $results[ $row_index ] = $pre; // plugin stored the review; $pre is the per-row result
    continue;
}
```

Where `$row` carries the validated payload (product/variation id, rating, text, author name/email/IP, moderation decision) and `$existing` is the matched existing review (`WP_Comment|null`). Returning `null` proceeds exactly as today.

**2. Delegate the "existing review" lookup.** The feature answers "has this order item been reviewed?" by querying comments keyed on `_review_order_id` (`ItemEligibility::decide()`, and the count in `SubmissionHandler::maybe_mark_order_complete()`). A plugin that short-circuits storage never creates those comments, so without a companion filter the feature would re-ask forever and never mark an order complete:

```php
$pre = apply_filters( 'woocommerce_review_order_item_review', null, $item, $order );
// null: query comments as today; false: no review; WP_Comment|array: the review
```

Together with the existing `woocommerce_review_order_eligible_items` filter (10.9.0), these three points would let a storage-replacing plugin adopt the feature completely: WooCommerce keeps the email scheduling, the landing page, auth, and validation; the plugin supplies storage and the already-reviewed answers.

## Describe alternatives you've considered

- **Convert on `woocommerce_review_order_submitted`** (current approach): works, but dual-writes every review and leaves ledger-only comment rows behind.
- **Replacing the `wp_ajax` handler**: reimplements nonce/order-key auth, row validation, and the response contract the page JS expects — version-coupled to internals with no compatibility guarantee.
- **Deleting the comment after conversion**: breaks eligibility, edit-in-place, and completion tracking, which all read the comment back.

## Additional context

References are to WooCommerce 11.0.0: `src/Internal/OrderReviews/SubmissionHandler.php` (write branches ~L225–L272, completion count ~L338–L350) and `src/Internal/OrderReviews/ItemEligibility.php` (`decide()` ~L251, lookups ~L196–L207, ~L481). `_review_order_id` is written in exactly one place (SubmissionHandler) and read in three — all inside the feature — so the two filters above cover its entire surface.
