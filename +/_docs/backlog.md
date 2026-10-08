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

- [x] **The addon-update filters are untested against the real update-server contract.**
  DONE (83f411e3e + live probe): the captures live in `tests/pest/fixtures/updater/`
  (see the README there for scrubbing and provenance) and UpdateControllerTest
  replays them through `licenseServer()`. UpdateController is at 100%.

  The probed contract, executed against the live server: a MISSING licence key gets
  an EMPTY `package` — the plugin's "a valid license key is required" machinery
  works as designed for that case, and is now genuinely tested. A WRONG key,
  however, still gets a phantom `package` (confirmed to refuse with HTTP 401 at
  download time), with the refusal only in `msg` — so a site holding a revoked or
  mistyped key sees a normal update row whose install fails with WordPress's
  generic download error instead of the licence message. `license_check` was empty
  in every probed scenario. Recommended fix is SERVER-side: answer the wrong-key
  case the way the missing-key case is already answered (empty package) — every
  installed plugin version then behaves correctly retroactively. Re-capture the
  invalid fixture and flip its contract-pinning test when that lands.

  2026-08-30: the server fix is written (niftyplugins theme, commits d34f78e1 and
  0ec296a8, NOT yet deployed). An `edd_sl_license_response` filter withholds
  `package` and `download_link` unless check_license() answers `valid`, and adds
  `license_status` (check_license()'s vocabulary, or `missing`) plus
  `license_renewal_url` alongside `expired`. The plugin reads both
  (`Addons\UpdateNotice`, `VersionUpdateDefaults`, `UpdateController`) and falls
  back to the generic message when they are absent, which is what the live
  server still sends. UpdateControllerTest exercises the new contract with
  responses DERIVED from the captured invalid fixture. Still to do, after the
  theme deploys: re-capture `get-version-invalid.json`, capture an expired and a
  site-inactive answer, and replace the derived responses with the captures.

- [x] **The asset optimizer assumes the plugin folder is named exactly `site-reviews`.**
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

  DONE for 8.3.3 (2026-09-29, uncommitted): `combine()` takes the plugins directory from
  `dirname(glsr()->path())`. AssetOptimizationTest runs the plugin from a symlinked
  `site-reviews-renamed` folder; the test fails against the old strip.

- [x] **An empty `glsr_db_version` is treated as an ANCIENT database, and silently
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

  DONE in `84de3e31`: `migration.php` returns early when no version is recorded.
  InstallTest pins both directions (14 pass, 2026-09-29).

- [x] **`fetch-paged-reviews` throws a `TypeError` on a request that omits `url`.**
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

  Executed 2026-09-29: a logged-out request without `url` got `200 text/html` with body `0`
  (admin-ajax's default), and the TypeError was logged. DONE for 8.3.3 (uncommitted):
  `Url::path((string) $args->url)`; a missing url gives links to the home page. Covers both
  writers of the paged args (`FetchPagedReviews`, `CreateReview::reloadedReviews()`).
  PublicControllerTest has the test; it throws the TypeError against the old code.

- [x] **`Translation::strings()` memoises into a function-level `static`, which nothing
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

  DONE for 8.3.3 (2026-09-29, uncommitted): the memo is a `$strings` property on the
  Translation singleton (Provider.php:24), so a live site keeps one cache per request.
  Rejected first: Reflection cannot write a method static (no setter; executed on PHP 8.5),
  Pest has no per-test process isolation for closure tests, and a fake subclass would test
  the fake. TranslatorTest swaps in a fresh instance per test and restores the original.

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

- [ ] **Move Polylang and WPML into `/plugin/Integrations`, in the full integration shape.**
  They are the only two third-party plugins the codebase reaches into from `/plugin/Modules`;
  everything else lives under `Integrations/` with its own `Hooks`, controller and
  `isInstalled()`/version gate. Decided 2026-09-29: move them into that full shape, not a file
  move. That needs a re-evaluation of how `Modules\Multilingual` is used, because the
  dispatcher builds the class name from the `settings.general.multilingual` value
  (`'GeminiLabs\SiteReviews\Modules\Multilingual\\'.ucfirst($integration)`). No addon
  references the `Modules\Multilingual\*` classes, so no alias is needed.

  Coverage of `/plugin/Integrations` is measured separately and never gated, because it
  depends on code not in the tree — which is what these two are. Found while fixing
  `Polylang::getPostId()`, which never translated anything.

- [x] **`NoticeController::dismissNotice()` will construct any class the browser names.**
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

  DONE for 8.3.3 (2026-09-29, uncommitted): the guard is
  `is_subclass_of($notice, AbstractNotice::class)`. No capability check: `dismiss()` writes
  only the caller's own user meta. Premium `FormWidthsNotice` and the `FlaggedNotice`s extend
  AbstractNotice and use this route. NoticeTest proves nothing is constructed
  (`ConstructorProbe`) and that a notice declared outside the plugin still dismisses.

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

- [x] **Reevaluate the `Helper` class, starting with how request input is read.** There
  are now several ways: `Helper::filterInput()` (POST, falling back to `$_POST`),
  `Helper::input()` (GET or POST by `INPUT_*` constant), `Helper::filterInputArray()`,
  and raw `filter_input()` calls — still present in
  `Integrations\WooCommerce\Controllers\ProductController` (`orderby`, `$shortcode`) and
  `Integrations\Bricks\Commands\AbstractSearchCommand` (`include`, `search`). The raw
  calls are the problem: `filter_input()` reads the SAPI's copy of the request, which a
  non-web process (WP-CLI, the Pest suite) does not have, so it returns null there
  whatever the superglobals hold — not just untestable, unreachable outside a web
  request. Settle on one way to read input and move everything onto it.

  CLOSED 2026-09-29. Rule 9 in CLAUDE.md settled the testability half: the suite shadows
  `filter_input()` per namespace. Moving everything onto a fallback helper would change live
  behaviour, because a superglobal fallback honours values another plugin injected into
  `$_GET`/`$_POST`. `Helper::input()` already falls back when `filter_input()` returns false
  (the key is not in the SAPI's table); that is accepted as it is.

- [x] **The WooCommerce-compatibility nets: decided 2026-09-02.** The WooCommerce
  11.0.0 audit (2026-08-06, traced against a v11 checkout; gatekeeper ceiling bumped
  to 12.0) had to be done almost entirely by hand, because the suite only proves the
  half Site Reviews owns. Six candidate nets came out of it, in value-per-cost order.
  The decision per net:

  1. **Accepted with 4, post-release, as one task** (its own item below). The
     touchpoint inventory (~60 items: extended classes and methods, consumed hooks,
     options, template names, route keys, markup selectors) is written as the
     manifest that the net-4 script reads, so the doc cannot age separately from
     the check that uses it.
  2. **Accepted, post-release** (its own item below). `make stubs:update` for the
     free upstreams, then `make analyse`, on a weekly CI schedule. Already proven:
     the woocommerce stub regenerated at 11.0.0 and phpstan came back clean.
  3. **Rejected.** A ThirdParty smoke test that class-loads every integration class
     extending a third-party parent checks the boundary phpstan already checks on
     regenerated stubs (net 2), and the real-WooCommerce suite (net 5) loads and
     dispatches the `BlocksApi`/`RestApi`/`AdminApi` subclasses for real. The other
     extenders (the Bricks elements, the ProfilePress field and directory themes,
     the Elementor product-rating widget, the Gutenberg blocks) are phpstan-covered
     through their stubs.
  4. **Accepted with 1** (see above). A `tests/bin` script greps a real WooCommerce
     tree for every manifest touchpoint; manually triggered per WC release. Hooks
     live in function BODIES, which the stub generator strips — a removed hook is
     invisible to 2 and 5; this is the only automated net that sees it.
  5. **Implemented 2026-09-01.** Real WooCommerce in its OWN wp-env instance
     (`tests/woocommerce/`, port 8894, `make test:woocommerce`; a per-push CI
     job uploads its clover under the `woocommerce` Codecov flag). 22 executed
     tests cover the top paths: rating aggregation through a real `WC_Product`
     (its meta, the lookup-table row, the filtered getters), the `wc/v3`, Store
     API and wc-analytics review routes via `rest_do_request()`, the product
     page's reviews tab, rating template and `comments_template` override, and
     the verified-owner check against real orders. Its own instance rather than
     WooCommerce in the main one, so real WC hooks do not run under every
     existing test. Not covered there: the Gatekeeper — wp-env names the
     directory `woocommerce.latest-stable`, so `woocommerce/woocommerce.php`
     does not exist in that instance and the Gatekeeper reports WooCommerce as
     not installed (CI installs it under `woocommerce/`; no test may depend on
     either answer). Bears on 3: this suite loads and dispatches the WooCommerce
     `BlocksApi`/`RestApi`/`AdminApi` subclasses for real, which is part of why 3
     is rejected.
  6. **Accepted as the standing process, per WooCommerce major:** a changelog and
     dev-notes read plus a semantic trace. Nets 2–5 only watch touchpoints the plugin
     already consumes; WooCommerce also breaks the integration by ADDING surfaces.
     The v11 audit's two forward-looking findings — the `customer_review_request`
     beta that inserts comment reviews around Site Reviews, and the Product Reviews
     block's inner-blocks render path that bypasses `comments_template()` — would
     have passed every one of nets 1–5.

- [ ] **WooCommerce touchpoint manifest and grep script (nets 1 and 4).** Write the
  inventory from `plugin/Integrations/WooCommerce`: extended classes and overridden
  methods, consumed `woocommerce_*` hooks, options, template names, route keys,
  markup selectors. A `tests/bin` script greps a WooCommerce checkout for every entry
  and reports the ones that are gone. Run it per WooCommerce release.

- [ ] **Weekly stubs and phpstan CI job (net 2).** A scheduled workflow: `make
  stubs:update` for the free upstreams (premium sources are local zips and are
  skipped), then `make analyse`. Catches removed symbols and signature drift between
  audits.

- [x] **`Addon::posts()` has no status argument, so premium's Emails repeats its query.**
  From premium `.claude/TASKS.md` E47. `Addon::posts()` (`plugin/Addons/Addon.php:221`)
  hard-codes `'post_status' => 'publish'`. `EmailForm::options()` in premium
  (`plugin/Features/Forms/Emails/EmailForm.php`) runs the same `Database::posts()` query with
  `draft`, `future`, `pending`, `private` and `publish`, so it cannot call the core method.
  A status argument on `Addon::posts()`, defaulting to `publish`, lets premium share it.

  CLOSED 2026-09-29: no change. The shared query is `Database::posts()`, which premium already
  calls; `Addon::posts()` is the publish-only shortcut for dropdowns. A new public parameter
  every addon inherits would buy about four lines in premium.

- [ ] **A custom review editor must design its autosave; the REST autosave route was
  deliberately removed.** 2026-08-20 (REST controller evaluation):
  `RegisterPostType` now calls `remove_post_type_support('site-review', 'autosave')`,
  because core implies the feature from `editor` support and the implied REST route
  was broken — `WP_REST_Autosaves_Controller::create_item` fed the base-class
  `prepare_item_for_database()` `WP_Error` into the save unchecked, so a POST
  returned 200 and wrote an EMPTY autosave revision (executed, wp-env WP 7.0.2).
  Classic-editor autosave is unaffected: its pipeline (heartbeat → `wp_autosave()`
  → `wp_create_post_autosave()`) never checks the feature.

  Two things for the editor work to know:

  1. Re-adding the support flag is not enough. The core route captures post-table
     fields only (title/content/excerpt); a review is also a `glsr_ratings` row,
     assigned data, and Forms-addon custom fields. Either implement
     `RestReviewController::prepare_item_for_database()` AND decide what an
     autosave preserves beyond post fields, or give the editor its own plugin-style
     draft route. The gap is loud, not silent: an editor autosaving against the
     core route today gets 404 `rest_no_route` on the first attempt.
  2. Removal declares "do not autosave reviews" (core: "'autosave' support needs to
     be explicitly removed if not desired") while the classic pipeline keeps
     autosaving only because it predates the feature and was never retrofitted. If
     a future WordPress honours the declaration there, classic autosave for
     reviews stops — mild (lost crash recovery on the edit screen), but worth a
     dev-note check on major-version bumps.

- [x] **Carry the form signature as JSON instead of a serialized payload.** The
  signature is now read with `unserialize($value, ['allowed_classes' => false])`
  in `Request::signedValues()`, so a request can no longer restore an object. That
  closes the injection, but the payload is still PHP-serialized, and the rule
  worth reaching is that nothing arriving with a request goes near `unserialize()`
  at all.

  Deferred out of the 8.3.0 security release on purpose: signatures already sit
  inside rendered and cached pages, so the read path needs a transition that
  accepts both formats for at least one release before `signForm()` switches to
  `wp_json_encode()`. Doing that under time pressure is how a cached page starts
  refusing every submission on it.

  `Form::signForm()` writes the payload; `Request::signedValues()` is the only
  reader, so the change is two methods plus the transitional branch.

  CLOSED 2026-09-29. The injection needed a key an attacker could compute; `75fc7b39` derives
  the key from `wp_salt('nonce')`, and `allowed_classes => false` stays. Secretbox
  authenticates the payload, so only a holder of the site key can reach `unserialize()`.
  The PHP manual's condition for unserializing externally stored data is that nobody else can
  modify it; this meets it. A key holder could forge a JSON signature as easily. Note for
  any future work: `Request::set()` also re-signs, so the writers are `Form::signForm()` and
  `Request::set()`.

- [x] **Deleting a network site left `wp_N_glsr_ratings` and `wp_N_posts` behind.**
  Executed 2026-09-29 (4 of 4 sites). Since 7.0.0 (`f69aea1e`) `filterDropTables()` returned
  the tables in reverse registry order, so `ratings` came before the `assigned_*` tables that
  point at it. MySQL refused that DROP, `ratings` then blocked core's `posts`, and core ignores
  a failed DROP. DONE for 8.3.3 (uncommitted): `ratings` goes after every other custom table,
  and a `wp_uninitialize_site:5` callback drops the site's foreign keys before core's drops at
  10. The multisite suite deletes a real site and finds no table left; it fails against the
  old code. Site deletion ignores `delete_data_on_uninstall`, as core drops the site's own
  tables unconditionally, and the filter names only tables in `Tables::tables()`.

  No automatic cleanup of networks that already have leftovers. For support, list them
  (replace `wp_` with the real prefix), then drop only the `glsr_` tables; `wp_N_posts`
  belongs to WordPress and is the admin's decision:

  ```sql
  SELECT TABLE_NAME FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME REGEXP '^wp_[0-9]+_glsr_'
    AND CAST(SUBSTRING_INDEX(SUBSTRING(TABLE_NAME, 4), '_', 1) AS UNSIGNED)
        NOT IN (SELECT blog_id FROM wp_blogs);
  ```

- [x] **Deleting a site where Site Reviews is active on that site alone leaves every table.**
  Executed 2026-09-29 on the multisite instance: Site Reviews active only on site 15 (not
  network-active, not active on the main site), deleted with `wp site delete` from the main
  site. The plugin is not loaded in that request, so neither deletion hook runs. Left behind:
  every `wp_15_glsr_*` table, plus `wp_15_posts` and `wp_15_terms`, which the plugin's foreign
  keys block (MySQL: "Cannot delete or update a parent row"). The site's Action Scheduler
  tables stay too, as they do for any plugin that bundles it. No plugin code runs, so no hook
  can fix it; the only lever is the schema (foreign keys onto core tables).

  ACCEPTED 2026-09-29: documented here, no code change. Removing the foreign keys onto core
  tables would need its own research under rule 9.

- [x] **Uninstall with "delete all data" leaves `glsr_stats` and `glsr_tmp` behind.**
  Executed 2026-09-29: `glsr_uninstall_all_delete_tables()` in `uninstall.php` names four
  tables (`assigned_users`, `assigned_terms`, `assigned_posts`, `ratings`). After it ran on a
  throwaway site, `glsr_stats` and `glsr_tmp` remained. Deactivation drops the foreign keys
  first, so the `ratings` drop succeeds. Addon tables registered through `database/tables`
  (premium `actions_log`) are not named either.

  DONE for 8.3.3 (2026-09-29, uncommitted): `stats` (before `ratings`) and `tmp` are in the
  list. The multisite suite runs the real function on a throwaway site and finds no
  `glsr_` table; it fails against the old list. Addon tables stay each addon's own
  uninstall job, since `uninstall.php` runs without the plugin loaded.

- [ ] **REST `author` means three different things.** Executed 2026-09-30 with a probe
  through the REST routes as an administrator. The schema declares `author` as the user ID
  (`ReviewSchema.php`, `'type' => 'integer'`), and a GET returns `user_id` under `author`
  (`PrepareReviewData.php:63`). But:
  - create with `author` and a `name`: the name is kept, `author` is ignored, and `author_id`
    becomes the requesting user;
  - create with `author` and no `name`: `CreateReviewDefaults` maps `author` to `name`, so the
    reviewer's name is stored as the user ID (`"238439"`);
  - update with `author`: nothing changes, and the response is 200.

  `ReviewPermissions.php:163` checks permission for `author`, so reattribution looks intended.
  `RestApiTest` "an editor of others' posts may reattribute a review" asserts only the 200.
  Options when this is picked up: map REST `author` to `author_id` and `post_author` behind
  the edit-others check, or make `author` read-only on write.

- [ ] **`release.sh` creates the wordpress.org SVN tag and no git tag.** Raised 2026-10-08
  (Paul: "I normally manually add the tag but sometimes I forget so it should be done
  automatically in the release script"). The script copies trunk to `tags/{version}` and
  commits to SVN (`release.sh:128`, `:143`); it runs no `git tag` and no `git push`. `origin`
  has no `v8.3.0` and no `v8.3.1` (`git ls-remote --tags origin`, 2026-10-08).

  To build: after the SVN commit succeeds, create `v{version}` on the commit the release was
  built from and push it, only when that tag exists neither locally nor on `origin`. The tags
  from `v8.3.2` on are annotated.

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

- [ ] **Admin-route notices are lost on reload without javascript: move Post/Redirect/Get
  into the Router.** Executed 2026-09-29 on the dev site. The Router dispatches admin POST
  routes on `admin_init` and returns; what happens next depends on the screen:
  - Tools: 302. `MenuController::processPageActions()` calls Action Scheduler's
    `process_actions()`, which redirects whenever `_wp_http_referer` is posted. Every Tools
    route loses its notice.
  - Reviews list and edit-tags: 302 (core `edit.php` redirects on `_wp_http_referer`).
  - Settings and Documentation: 200, the notice renders in the same request.

  Only a visitor without javascript meets this: every form has `data-ajax-click`.

  Greenfield design, decided 2026-09-29: after `$this->post('admin', $request)` returns, the
  Router calls `Notice::store()`, `wp_safe_redirect(wp_get_referer())` and exits. Every admin
  form posts `_wp_http_referer` through `wp_nonce_field()`. The notice then shows once on
  every screen, the ad-hoc `store()` calls go (`AdminController::approveReview`,
  `ToolsController::importSettings`), and a refresh no longer re-posts a repair. Download
  routes exit in the controller, before the redirect.

  Audit before building it, because it changes every admin POST route, the addons' included:
  the core admin routes, premium `toggle-feature`, and `import-{post_type}` in premium,
  alerts, forms and themes. Premium's `AbstractImportController` renders
  `glsr(Notice::class)->get()` on its importer page, which would read the flashed notices
  after the redirect (traced, not executed).

- [x] **`installOnNewSite()` can fatal in site-creation flows that never load
  `wp-admin/includes/plugin.php`.** CLOSED 2026-09-29, executed: it cannot happen. Since
  WP 6.8, `wp-settings.php` requires `plugin.php` unconditionally before plugins load
  (#62244), and the plugin requires 6.8. WP-CLI `site create`, a plain `wp_insert_site()` and
  `wp-activate.php` all had the function and created the tables. One side effect: a site
  created through `wp-activate.php` gets its tables on its first normal page load, because
  `Application::init()` returns early under `wp_installing()`.

- [ ] **A restore that empties `glsr_ratings` rebuilds blank rows, and hides the loss (8.3.4).**
  Executed 2026-09-29. `Database::isMigrationNeeded()` is true when published reviews exist and
  the table has zero approved rows; `Migrate::run()` then takes `runAll()`.
  - Rows deleted: `MigrateReviews` rebuilds from post meta. A 6.x+ review has no legacy meta,
    so each row gets `rating 0`, empty name, email and IP, and `terms 1`.
    `isMigrationNeeded()` turns false and nothing tells the owner.
  - Rows present, `is_approved = 0`, post published ("a restored posts-only backup" in
    MigrateTest): nothing changes, `runAll()` repeats hourly, and MigrationNotice stays.
  - Some rows deleted (traced): nothing triggers; those reviews vanish from the front end,
    which reads `FROM table|ratings`.

  Design, decided 2026-09-29:
  - Rebuild a missing row from (1) legacy meta, any key `parseRatings()` maps to a rating
    column (`_rating`, `_author`, `_email`, `_avatar`, `_ip_address`, `_url`, `_terms`,
    `_pinned`, `_review_type`), or (2) a `_submitted` holding a rating column. `CreateReview`
    writes `_submitted` on every path since ~7.1 (`0c477074`); it is empty for admin-created
    reviews and deleted by the privacy eraser; it holds the original submission.
    `terms = !empty($submitted['terms'])`, never RatingDefaults' default `true`.
  - `isMigrationNeeded()` is true only while a rebuildable review lacks a row.
  - Out of sync: set `is_approved` from post status, the rule `ReviewController` applies.
  - After a `_submitted` rebuild, a dismissible notice gives the count and says later edits,
    pins and verified badges were not recovered. Reviews with no source get a persistent
    notice until every published review has a row.
  - Trace whether `_verified` meta can restore `is_verified`.

