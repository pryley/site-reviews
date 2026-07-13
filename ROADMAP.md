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

- [ ] **An empty `glsr_db_version` is treated as an ANCIENT database, and silently
  fabricates a record of consent.** `migration.php` registers, for the life of every
  request:

  ```php
  function glsr_migration_5_9_db_version_1_1(array $values) {
      if (version_compare(glsr(Database::class)->version(), '1.1', '<')) {
          unset($values['terms']);
      }
      return $values;
  }
  add_filter('site-reviews/defaults/rating', 'glsr_migration_5_9_db_version_1_1');
  ```

  The intent is right: a pre-1.1 database has no `terms` column, and naming one in the
  INSERT would fail on every review. But `Database::version()` reads the option live and
  returns `''` when it is missing, and **`version_compare('', '1.1', '<')` is `true`** —
  so an ABSENT option is indistinguishable from an ancient schema.

  The consequence is not a fatal, which is what makes it worth writing down. `terms` is
  dropped from `RatingDefaults`, so `Database::insert('ratings', …)` never names the
  column, so MySQL applies the schema default — which is
  `terms tinyint(1) NOT NULL DEFAULT '1'`. **Every review created while the option is
  missing is stored as having accepted the terms.** Nothing logs it, nothing shows it,
  and the value being invented is a record of consent.

  Qualified: it needs `glsr_db_version` to be empty or absent on a site whose ratings
  table is current. That is not the normal state — but it is exactly the state a
  half-failed install, a deleted option, or a `dropTables()` without a reinstall leaves
  behind, and `Install::install()` only re-adds the option when the tables already exist.

  Traced and EXECUTED — `tests/pest/Integration/InstallTest.php` pins both directions of
  the shim. A fix would be to distinguish "no version recorded" from "an old version"
  rather than letting `''` collate below `1.1`.

  Found because a test deleted the option, and five unrelated test files started
  recording consent nobody gave.

- [ ] **`Translation::strings()` memoises into a function-level `static`, which nothing
  can reset.** `static $strings;` … `if (empty($strings))` — so the first call that finds
  a non-empty `settings.strings` caches it for the rest of the PHP process. No
  transaction rollback, no `wp_cache_flush()`, and nothing in the suite's
  `resetGlobalState()` can reach it.

  In production this is a per-request cache and harmless. In the suite it is a one-way
  door: `tests/pest/Integration/TranslatorTest.php` can only define ONE set of custom
  strings, in `beforeEach`, and every phrase it customises has to be one that appears
  nowhere else in the plugin — because the cache outlives the file and would otherwise
  change what later tests see. Any future test that needs to customise a string the
  plugin actually uses will silently get whichever set of strings ran first.

  There must be a better answer than working around it. Options not yet investigated: a
  static that can be cleared through a documented seam; moving the memo into
  `glsr()->store()` (which `resetGlobalState()` already clears) — note that the current
  comment says it deliberately bypasses the settings pipeline because it runs before the
  settings are initiated, so that constraint is real and has to be honoured; or a
  container binding. **Revisit — worth a fresh pair of eyes.**

- [ ] **Every integration's `Hooks` class reads as 0% coverage, and it is a measurement
  artifact, not a testing gap.** They run exactly once, on `plugins_loaded` at priority
  100, during `wp-load.php` in `tests/pest/bootstrap.php` — which is before PHPUnit
  starts collecting coverage. The code runs; nothing counts it.

  `Integrations/Gutenberg/Hooks` was covered by having a test call `run()` explicitly
  (`tests/pest/ThirdParty/GutenbergBlockTest.php`), which works but is thirty copies of
  the same test waiting to be written, and it re-registers hooks that are already
  registered.

  The same is true of anything else that only runs at boot. A real fix would be
  structural — start coverage collection earlier, or move the integration boot into
  something a test can drive — rather than one test per integration. **Revisit.**

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

