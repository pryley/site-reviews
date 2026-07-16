# Roadmap

Features are subject to change and are sorted alphabetically, not by priority.

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

- [ ] **The asset optimizer assumes the plugin folder is named exactly `site-reviews`.**
  `AbstractAsset::combine()` strips a HARD-CODED `strlen('site-reviews/')` off the end of
  `glsr()->path()` to get the filesystem root, and `file()` (the combined-asset target)
  builds its uploads path from `glsr()->id`. In any other folder the strip removes the
  wrong number of characters and every combined path comes out corrupted — a CI run from
  a folder named `plugin/` produced `.../site-resite-reviews/assets/...` — so the
  combined stylesheet/script is written and served from a path that does not exist: a
  silently broken front end.

  Qualified, twice over: asset optimization is OPT-IN (off by default, enabled by the
  `optimize/css` / `optimize/js` filters), and a wordpress.org install uses the right
  folder name. It bites a renamed folder — a GitHub zip unpacks as `site-reviews-main`,
  `site-reviews-8.1.1`, etc. — with optimization on. Low severity, not a release blocker.

  Fix: derive the trailing segment from the ACTUAL basename of `glsr()->path()` rather
  than assume `glsr()->id`. Found when the GitHub Actions suite ran the plugin from a
  differently-named directory; ci.yml now checks the plugin out as `site-reviews`
  specifically to sidestep it.

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
  INSERT would fail on every review. But `Database::version()` returns `''` when the
  option is missing, and **`version_compare('', '1.1', '<')` is `true`** — an ABSENT
  option is indistinguishable from an ancient schema.

  The consequence is not a fatal, which is what makes it worth writing down: `terms` is
  dropped from `RatingDefaults`, the INSERT never names the column, and MySQL applies the
  schema default — `terms tinyint(1) NOT NULL DEFAULT '1'`. **Every review created while
  the option is missing is stored as having accepted the terms.** Nothing logs it, and
  the value being invented is a record of consent.

  Qualified: it needs `glsr_db_version` empty or absent on a site whose ratings table is
  current — not the normal state, but exactly what a half-failed install, a deleted
  option, or a `dropTables()` without a reinstall leaves behind, and `Install::install()`
  only re-adds the option when the tables already exist.

  Traced and EXECUTED — `tests/pest/Integration/InstallTest.php` pins both directions of
  the shim. Fix: distinguish "no version recorded" from "an old version" rather than
  letting `''` collate below `1.1`. Found because a test deleted the option, and five
  unrelated test files started recording consent nobody gave.

- [ ] **`fetch-paged-reviews` throws a `TypeError` on a request that omits `url`.**
  `NormalizePaginationArgs::normalizePageUrl()` does `Url::path($args->url)`, and `url`
  comes from the raw POST body: `Request` applies no defaults and `get()` returns `null`
  for an absent key, so `Url::path(string $url)` is handed `null`.

  **Severity, having been checked rather than assumed:** NOT a fatal. The route runs
  through `HookProxy`, whose `catch (\Throwable)` swallows and logs it; the request ends
  with an empty response and the sender's "load more" does nothing. No white screen, no
  500, no data leak, no effect on anybody but the sender of the malformed request — the
  plugin's own javascript always sends `url`, which is why nobody has hit it.

  So: a robustness gap and console noise, not a vulnerability. Worth hardening —
  `Url::path((string) $args->url)`, or a `url` default in the pagination Defaults — but a
  tidy-up, not a release blocker. Found while writing
  `tests/pest/Integration/PublicControllerTest.php`; first reported as "a fatal anyone
  can trigger", which was wrong — see the note appended to rule 2 in CLAUDE.md.

- [ ] **`Translation::strings()` memoises into a function-level `static`, which nothing
  can reset.** The first call that finds a non-empty `settings.strings` caches it for the
  rest of the PHP process — beyond the reach of the transaction rollback,
  `wp_cache_flush()`, and `resetGlobalState()`.

  In production this is a per-request cache and harmless. In the suite it is a one-way
  door: `TranslatorTest` can define only ONE set of custom strings, in `beforeEach`, and
  each customised phrase must appear nowhere else in the plugin, because the cache
  outlives the file. Any future test customising a string the plugin actually uses will
  silently get whichever set ran first.

  Options not yet investigated: a static clearable through a documented seam; moving the
  memo into `glsr()->store()` (already cleared by `resetGlobalState()`) — note the
  current comment says it deliberately bypasses the settings pipeline because it runs
  before the settings are initiated, a real constraint to honour; or a container binding.
  **Revisit — worth a fresh pair of eyes.**

- [ ] **Every integration's `Hooks` class reads as 0% coverage — a measurement artifact,
  not a testing gap.** They run exactly once, on `plugins_loaded:100` during
  `wp-load.php` in `tests/pest/bootstrap.php` — before PHPUnit starts collecting
  coverage. The code runs; nothing counts it.

  `Integrations/Gutenberg/Hooks` was covered by having a test call `run()` explicitly
  (`GutenbergBlockTest`), which works but is thirty copies of the same test waiting to be
  written, and it re-registers already-registered hooks. The same applies to anything
  else that only runs at boot. A real fix is structural — start coverage collection
  earlier, or move the integration boot into something a test can drive — rather than
  one test per integration. **Revisit.**

- [ ] **`Database\Tables\TableFields` is unreachable, and the `glsr_fields` table is
  never created.** Every call site is commented out: `TableFields::class` in
  `Tables::tables()` behind a `// @todo add the fields table`, and the `create()` +
  `addForeignConstraints()` block in `Migrate_6_0_0`. Both files still `use` the class
  without using it.

  Because `tables()` drives `createTables()`, the constraint methods, `tablesExist()`,
  `Install::tables()` and `customTables()`, the consequence is total: the table is
  created on no site, `table|fields` does not resolve as an SQL alias, and nothing else
  in the tree mentions `glsr_fields`. An addon *could* add it through the
  `database/tables` filter, but nothing does.

  **Deliberately left uncovered by the Pest suite** — a test would have to create the
  table by hand and would then be testing a fixture, not the plugin. Either finish the
  `@todo` (a plugin change, with a migration) or delete the class; leaving it is a trap
  for the next person reading `Tables::tables()`. Found while working through the
  0%-coverage list.

- [ ] **`ConvertTableEngine`'s result-0/result-1 branches cannot be tested without
  corrupting the shared database.** Reaching either means making
  `Tables::convertTableEngine()` believe a real plugin table is MyISAM (via the cached
  `{prefix}engine_{table}` option) and letting it run `ALTER TABLE … ENGINE = InnoDB`.
  The ALTER is DDL, so it implicitly COMMITs mid-test; the command's own correcting
  `update_option(…, 'innodb')` then runs in the post-DDL transaction that Pest.php rolls
  back, leaving the table flagged **MyISAM in the committed database**. On the next run —
  in ANOTHER file — ToolsAjaxTest's engine test then genuinely ALTERs `ratings` (commit
  tripwire), and the re-applied foreign keys break the `ratings → assigned_posts` cascade
  that ExportImportTest depends on. Tried and reverted. If these branches must be
  covered, the command needs a seam that does not run live DDL against a shared table
  (e.g. a bindable table-engine service the suite can fake).

- [ ] **`Rollback::rollback()` is deliberately left uncovered — the offline suite
  structurally can't drive it, and it is not a plugin defect.** It is an admin-screen
  render wrapper: `require_once` of `wp-admin/admin-header.php`, a real
  `\Plugin_Upgrader_Skin`, the upgrade, then `admin-footer.php`. Three walls, none in the
  plugin:

  1. `admin-header.php` pulls in `admin.php` unless `WP_ADMIN` is defined, and the suite
     runs with `WP_ADMIN` undefined / `is_admin()` false on purpose (see the note in
     `InteractsWithAjax` about admin includes fataling third-party code). `admin.php`
     also runs `auth_redirect()`, which in the cookieless CLI process redirects and
     `exit`s — killing the run.
  2. `define('WP_ADMIN', true)` would get past (1), but a constant can't be unset, so it
     poisons `is_admin()` for every later test — a worse leak than one uncovered method.
  3. The real `Plugin_Upgrader_Skin` closes the output buffers — exactly why
     `RollbackTest` drives `PluginUpgrader::rollback()` through a silent skin subclass
     instead.

  The one behaviour that matters — that it downloads
  `https://downloads.wordpress.org/plugin/site-reviews.{version}.zip` — is already
  asserted by that test. The uncovered lines are admin chrome plus trivial
  `$title`/`$parent_file`/nonce/url assignments. A real fix would extract the upgrade
  call from the render — a plugin change for marginal coverage; leaving it is fine.
  Traced statically 2026-07-15; not executed (the render path can't run offline).

- [ ] **Move Polylang and WPML into `/plugin/Integrations`.** They are the only two
  third-party plugins the codebase reaches into from `/plugin/Modules` — everything else
  lives under `Integrations/` with its own `Hooks`, controller and
  `isInstalled()`/version gate. `Modules/Multilingual/{Polylang,Wpml}.php` predates that
  structure.

  Not only tidiness: coverage of `/plugin/Integrations` is measured separately and never
  gated (`phpunit.integrations.xml`), precisely because it depends on code not in the
  tree — which is exactly what these two are. Under `Modules` they count against the
  gated figure while being untestable without a fake, and they were at **3%** until this
  batch. The `MultilingualContract` + `Multilingual` dispatcher can stay; the two
  implementations belong beside the other thirty. Found while fixing
  `Polylang::getPostId()`, which never translated anything.

- [ ] **`NoticeController::dismissNotice()` will construct any class the browser names.**
  It guards on `class_exists($notice)` and nothing else, then calls `glsr($notice)`,
  which reflect-constructs the class and resolves its constructor arguments. The class
  name comes straight from `$_POST`; `dismiss-notice` is in
  `Router::unguardedAdminActions()`, so the route takes **no nonce** and
  `wp_ajax_glsr_admin_action` puts it within reach of **any logged-in user**, subscriber
  included. No capability check either.

  Confirmed by execution, and milder than it sounds: `glsr('WP_Query')` builds a real
  `WP_Query`, `->dismiss()` hits `__call()`, which shrugs and returns false. The cost
  depends on some class in the process having a side-effecting constructor with a
  resolvable signature, and no such gadget has been demonstrated. So: hardening, not an
  exploit.

  The shape of the fix is `is_subclass_of($notice, AbstractNotice::class)`, probably with
  a capability check. Both are decisions, not obvious calls: the route is unguarded on
  purpose (a nonce on a cached page is somebody else's nonce), and dismissing a notice is
  not destructive. Covered — as current behaviour, with a comment saying so — by
  `tests/pest/Integration/NoticeTest.php`.

- [ ] **`glsr_assigned_terms` is written with term taxonomy ids, not term ids.**
  `ReviewController::onAfterChangeAssignedTerms()` is hooked to `set_object_terms`, whose
  3rd and 6th arguments are `term_taxonomy_id`s (wp-includes/taxonomy.php). It passes
  them straight through `AssignTerms`/`UnassignTerms` → `ReviewManager::assignTerm()` →
  `INSERT INTO glsr_assigned_terms (term_id)` — a column with a foreign key onto
  `wp_terms.term_id`. The two ids are separate AUTO_INCREMENT columns: equal on a fresh
  install, drifting apart on an imported, migrated or long-lived site.

  Confirmed by execution (`wp eval-file tests/pest/probe-assigned-terms.php`, since
  removed): with a drift of one (`term_id=172 / term_taxonomy_id=173`), the row was
  rejected by the foreign key and the category **silently not assigned**. With older
  drift the id lands on a term that does exist and the review is filed under the **wrong
  category**.

  This is the only caller of `assignTerm`/`unassignTerm`, so it is the only way rows get
  into that table — including the plugin's own save path (`ReviewManager::update()` →
  `wp_set_object_terms()`). Two things to settle: the write path (map the tt_ids, or ask
  `wp_get_object_terms($postId, $taxonomy, ['fields' => 'ids'])`), and sites whose rows
  are already wrong — `RepairReviewRelations` only prunes invalid rows, it does not
  rebuild `assigned_terms`. The method also never checks `$taxonomy`, so any other
  taxonomy on the review post type writes into `assigned_terms` too; same fix.

  `ReviewControllerTest` has a `set_object_terms` test that passes only because the ids
  are still equal on the wp-env database; it says so, and it will need revisiting with
  the fix.

- [ ] **Reevaluate the `Helper` class, starting with how request input is read.** There
  are now several ways: `Helper::filterInput()` (POST, falling back to `$_POST`),
  `Helper::input()` (GET or POST by `INPUT_*` constant), `Helper::filterInputArray()`,
  and raw `filter_input()` calls — still present in
  `Integrations\WooCommerce\Controllers\ProductController` (`orderby`, `$shortcode`) and
  `Integrations\Bricks\Commands\AbstractSearchCommand` (`include`, `search`). The raw
  calls are the problem: `filter_input()` reads the SAPI's copy of the request, which a
  non-web process (WP-CLI, the Pest suite) does not have, so it returns null there
  whatever the superglobals hold — not just untestable, unreachable outside a web
  request. Settle on one way to read input and move everything onto it.

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
- [x] Tripadvisor Reviews (done, but needs an additional service to make it work consistently)
- [ ] Trustpilot Reviews
- [ ] Yelp Reviews
