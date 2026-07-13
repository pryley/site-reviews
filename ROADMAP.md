# Roadmap

All proposed features are subject to change and are sorted alphabetically rather than in order of priority.

## Proposed features

- [ ] Add "More Details" modal to addons (ref: Plugins > Add New)
- [ ] Integrate with [Antispam Bee](https://wordpress.org/plugins/antispam-bee/)
- [ ] Rating "0.1" increments?
- [ ] Restrict displayed reviews by empty content (setting only?)
- [ ] Restrict reviews in the admin to those assigned to pages of the current user
- [ ] Review statistics
- [x] Store the review GEO location by IP
- [ ] Use the REST API to submit reviews (ref: Contact Form v7)

## Technical debt

- [ ] **`Database\Tables\TableFields` is unreachable, and the `glsr_fields` table is
  never created.** Every call site is commented out: `TableFields::class` is commented
  out of `Tables::tables()` behind a `// @todo add the fields table`, and the
  `create()` + `addForeignConstraints()` block in `Migrate_6_0_0` is commented out too.
  Both files still `use` the class without using it.

  Because `tables()` is the list that drives `createTables()`, `addForeignConstraints()`,
  `dropForeignConstraints()`, `tablesExist()`, `Install::tables()` and `customTables()`,
  the consequence is total: the table is created on no site, `table|fields` does not even
  resolve as an SQL alias, and nothing anywhere else in the tree mentions `glsr_fields`.
  Its `structure()`, `removeInvalidRows()` and constraint methods have never run.

  An addon *could* add it through the `database/tables` filter, but nothing does.

  **Deliberately left uncovered by the Pest suite** — a test would have to create the
  table by hand, and would then be testing a fixture rather than the plugin. Either
  finish the `@todo` (a plugin change, with a migration) or delete the class; leaving it
  is a trap for the next person reading `Tables::tables()`.

  Found while working through the 0%-coverage list.

- [ ] **Move Polylang and WPML into `/plugin/Integrations`.** They are the only two
  third-party plugins the codebase reaches into from `/plugin/Modules` — everything
  else that talks to somebody else's plugin (WooCommerce, Elementor, Divi, the other
  thirty) lives under `Integrations/`, with its own `Hooks`, its own controller and
  its own `isInstalled()`/version gate. `Modules/Multilingual/{Polylang,Wpml}.php`
  predates that structure and is the odd one out.

  It is not only tidiness. Coverage of `/plugin/Integrations` is measured separately
  and never gated (`phpunit.integrations.xml`), precisely because it depends on code
  that is not in the tree — which is exactly what these two are. Sitting under
  `Modules`, they are counted against the gated figure while being untestable without
  a fake, and they were at **3%** until this batch. The `MultilingualContract` +
  `Multilingual` dispatcher can stay where it is; it is the two implementations that
  belong beside the other thirty.

  Found while fixing `Polylang::getPostId()`, which never translated anything.

- [ ] **`NoticeController::dismissNotice()` will construct any class the browser names.**
  It guards on `class_exists($notice)` and nothing else, then calls `glsr($notice)`,
  which reflect-constructs the class through the container and resolves its constructor
  arguments. The class name comes straight from `$_POST`, and `dismiss-notice` is in
  `Router::unguardedAdminActions()` — so the route takes **no nonce**, and
  `wp_ajax_glsr_admin_action` puts it within reach of **any logged-in user**, subscriber
  included. There is no capability check either.

  Confirmed by execution, and it is milder than it sounds: `glsr('WP_Query')` builds a
  real `WP_Query` and then `->dismiss()` hits `WP_Query::__call()`, which shrugs and
  returns false — nothing happens. What it costs depends entirely on whether some class
  in the process has a side-effecting constructor and a signature the container can
  resolve, and no such gadget has been demonstrated. So: hardening, not an exploit.

  The shape of the fix is `is_subclass_of($notice, AbstractNotice::class)`, and probably
  a capability check to go with it. Both are decisions, not obvious calls: the route is
  unguarded on purpose (a nonce on a page served from a cache is somebody else's nonce),
  and dismissing a notice is not a destructive act. Covered — as current behaviour, with
  a comment saying so — by `tests/pest/Integration/NoticeTest.php`.

- [ ] **`glsr_assigned_terms` is written with term taxonomy ids, not term ids.**
  `ReviewController::onAfterChangeAssignedTerms()` is hooked to WordPress's
  `set_object_terms`, whose 3rd and 6th arguments are `term_taxonomy_id`s
  ("@param array $tt_ids An array of term taxonomy IDs" — wp-includes/taxonomy.php).
  It passes them straight into `AssignTerms`/`UnassignTerms` →
  `ReviewManager::assignTerm()` → `INSERT INTO glsr_assigned_terms (term_id)`, a
  column with a foreign key onto `wp_terms.term_id` (`TableAssignedTerms`).
  `term_id` and `term_taxonomy_id` are separate AUTO_INCREMENT columns in two
  tables; they are equal on a fresh install and drift apart on an imported,
  migrated or long-lived site.

  Confirmed by execution (`wp eval-file tests/pest/probe-assigned-terms.php`, since
  removed): with a drift of one, `term_id=172 / term_taxonomy_id=173`, the row was
  rejected by the foreign key and the category was **silently not assigned**. Where
  the drift is older the id lands on a term that does exist, and the review is filed
  under the **wrong category** instead.

  This is the only caller of `assignTerm`/`unassignTerm`, so it is the only way rows
  get into that table — including the plugin's own save path
  (`ReviewManager::update()` → `wp_set_object_terms()`). Two things to settle: the
  write path (map the tt_ids, or ignore them and ask
  `wp_get_object_terms($postId, $taxonomy, ['fields' => 'ids'])`), and what to do
  about sites whose rows are already wrong — `RepairReviewRelations` currently only
  prunes invalid rows, it does not rebuild `assigned_terms`.

  The same method also never checks `$taxonomy`, so any other taxonomy registered
  for the review post type writes into `assigned_terms` as well. Same fix.

  `tests/pest/Integration/ReviewControllerTest.php` has a `set_object_terms` test
  that passes only because the two ids are still equal on the wp-env database; it
  says so, and it will need revisiting with the fix.

- [ ] Reevaluate the `Helper` class, starting with how request input is read.
  There are now several ways to do it: `Helper::filterInput()` (POST, falling back to
  `$_POST`), `Helper::input()` (GET or POST, chosen by an `INPUT_*` constant),
  `Helper::filterInputArray()`, and raw `filter_input()` calls — which still remain
  in `Integrations\WooCommerce\Controllers\ProductController` (`orderby`, `$shortcode`)
  and `Integrations\Bricks\Commands\AbstractSearchCommand` (`include`, `search`).
  The raw calls are the problem: `filter_input()` reads the SAPI's own copy of the
  request, which a non-web process (WP-CLI, the Pest suite) does not have, so it
  returns null there no matter what is in the superglobals — the code is not just
  untestable, it is unreachable outside a web request. Settle on one way to read
  input and move everything onto it.

## Upcoming Add-ons

### Functionality

- [ ] Review Discussions
- [ ] Review Importer (from 3rd-party WordPress review plugins)
- [ ] Review Q&A
- [ ] Review Sharing
- [ ] Review Snitch (flag reviews as inappropriate)
- [ ] Review Summaries (single positive/negative ratings, summary styles, etc.)

### Integrations

- [ ] Booking.com Reviews
- [ ] Etsy Reviews
- [ ] Facebook Reviews
- [ ] Google Reviews
- [ ] LearnDash Reviews
- [x] Tripadvisor Reviews (done, but needs an additional service to make it work consistantly)
- [ ] Trustpilot Reviews
- [ ] Yelp Reviews

