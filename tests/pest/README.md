# Site Reviews Tests

Pest 4 against a REAL WordPress — no `WP_UnitTestCase`, no polyfills. It is the
plugin's test suite and its only test runner.

## Why it does not use WordPress's test framework

Core's framework is pinned to PHPUnit 9 and Pest 4 needs PHPUnit 12, so the
suite boots WordPress itself (`bootstrap.php` requires `wp-load.php`). The two
things it needs from core's framework live in `Support/`: the post/term/user
factories and the admin-ajax harness. Isolation comes from a DB transaction per
test instead of core's rollback.

Two lint-only tools live in their own composer projects, installed on first
use by the target that needs them:

- `+/tools/wp-since` (`make check`) — it pins an older toolchain than Pest.
- `+/tools/phpcs` (`make compat`) — PHPCompatibility is required as a git
  branch (`dev-develop`), and the wp-env container has no git, so Composer
  cannot install it there. It runs on the host instead.

That leaves the root `composer.json` with only stable, dist-installable
packages, so it installs cleanly inside the container.

## Layout

- `Unit/` — logic against the booted WordPress: helpers, casts, sanitizers,
  encryption, the field/form HTML builders. Creates no posts, terms or users.
- `Integration/` — the database: the review manager, the query builder, the
  migrations, ajax review submissions. Gutenberg lives here too — it is
  WordPress, not a third party.
- `ThirdParty/` — the 30 integrations that need somebody else's plugin or theme
  to do anything. Kept apart because they are excluded from the coverage gate;
  see Coverage.
- `Import/` — the CSV import, and **the last suite declared in `phpunit.xml`**.
  It has to be: `ProcessCsvFile` and `ImportManager` `define('WP_IMPORTING')`, a
  constant cannot be unset, and nineteen places in the plugin read it — an
  import must not generate an avatar per review, email a verification per
  review, geolocate every IP, or flush the page cache a thousand times. All
  correct during an import, all wrong everywhere else. Declared last, there is
  nothing after it to poison. `ThirdParty/CacheTest` asserts `WP_IMPORTING` is
  NOT defined, so moving it fails loudly rather than quietly.
- `Support/` — autoloaded by composer (`autoload-dev`): `helpers.php` (the
  factories plus `resetPluginState()`), `InteractsWithAjax` (the admin-ajax
  harness), `SubmitsReviews` (the review-submission harness) and `MockClass`.
- `mu-plugins/` — loads the integration stubs and disables the deprecated
  v5–v8 fallbacks. It has to be an mu-plugin: `deprecated.php` registers those
  fallbacks on `plugins_loaded`, which is already too late for `bootstrap.php`.
  It is inert unless `GLSR_UNIT_TESTS` is defined, so it does not affect
  ordinary web requests to the same install.

Every test — in every suite — runs inside a DB transaction that rolls
back (see `Pest.php`). The plugin's settings live in the options table, so
even a field-building test writes to the database.

**A transaction cannot isolate a test that ends it**, and two things end one: DDL
(MySQL commits implicitly on `CREATE`/`ALTER`/`DROP TABLE`) and an explicit
`START TRANSACTION`. Everything written up to that point becomes permanent and the
`ROLLBACK` finds nothing to undo. Autocommit is off, so a fresh transaction opens
straight after and the rest of the test still rolls back — the leak is always
*partial*, and surfaces elsewhere: a later run in another file dying with "Sorry,
that username already exists", because the leaked user is still there and
`user_login` is unique.

`Pest.php` catches this. Every test writes a sentinel row inside its transaction
and checks, after the `ROLLBACK`, whether the row survived. If it did, the test
committed, and it is failed by name. `bootstrap.php` also purges before the first
test, so a run that crashes out cannot poison the next one.

Four tests do it legitimately and say so with `commitsTransaction()`, which purges
the leaked rows instead of failing:

- all of `Import/` — `TableTmp::create()`/`drop()` are DDL, and an import cannot
  happen without them. That suite also cleans up in its own `afterEach`, with an
  explicit `COMMIT` so the rollback cannot take the cleanup with it.
- `ExportImportTest` ×2 and `ToolsAjaxTest` ×1, the three tests that reach
  `Migrate::runAll()` (from `ImportSettings` and from `MigratePlugin`).
  `Migrate_5_25_0/MigrateReviews` wraps each of its four passes in
  `Database::beginTransaction()`/`finishTransaction()`, which on InnoDB is a
  literal `START TRANSACTION` and `COMMIT`. The migrations are idempotent in
  effect, not in isolation.

## Running

Requires Docker + Node. Everything runs inside wp-env: the composer
dependencies are installed in the container (PHP 8.3, see `.wp-env.json`).

    make test:install       # once: starts wp-env + composer update inside it
    make test               # all four suites
    make test:unit          # fast feedback loop
    make test:integration
    make test:thirdparty    # the integrations
    make test:import        # the CSV import (runs last: it defines WP_IMPORTING)
    make test:coverage      # the PLUGIN, gated at 80% (restarts wp-env with Xdebug)

Pest needs `--test-directory=tests/pest` (it is where it looks for `Pest.php`);
the composer scripts pass it, so always go through `composer test` / `make`.

## Running against another install

Point `WP_ROOT` at any WordPress with the plugin active and a DISPOSABLE
database (the tests write to it between transaction boundaries; never point
this at a real site). `tests/pest/mu-plugins` must be reachable as an
mu-plugin — symlink it into `wp-content/mu-plugins`:

    WP_ROOT=/path/to/wordpress vendor/bin/pest --test-directory=tests/pest --testsuite=Unit

## Integrations, and the stubs

`tests/stubs` holds signature-only stubs (php-stubs style — real class and
function signatures, empty bodies) for the third-party plugins Site Reviews
integrates with. They are consumed by **both** phpstan and this suite: the test
mu-plugin requires them on `muplugins_loaded`, before the plugins load, so by
the time Site Reviews boots, `class_exists('WooCommerce')` and friends are all
true and **every integration in `plugin/Integrations` is active while the tests
run**. That is not theoretical — it is how the pre-init translation in
`IntegrationHooks::notify()` was caught: the Breakdance stub made the
integration look installed, its version check failed, and it translated on
`plugins_loaded`.

`plugin/Integrations` is therefore IN the coverage scope.

### What the stubs can and cannot test

Because the bodies are empty, the stubs support exactly one half of an
integration — the half Site Reviews owns:

- **Yes:** hook registration and `isInstalled()` gating; the version gate and
  its notice; the pure transformers (`Divi`, `Elementor`, `Flatsome`,
  `WPBakery`, `Avada` — array in, array out); the SEO schema controllers
  (`RankMath`, `YoastSEO`, `SEOPress`) which filter schema arrays; the `Cache`
  integration's purge dispatch, which is guarded by `function_exists`.
- **No:** anything that consumes a *return value* from the third party.
  `wc_get_product()` returns `null` from a stub, so `WooCommerce\ProductController::rating()`
  has nothing to read. Those paths need the real plugin installed in wp-env
  (`"plugins": ["woocommerce"]` in `.wp-env.json`) — deliberately not done yet.

When writing integration tests, say which of the two a test is, and do not fake
a return value into a stub to reach the second kind: a test that asserts against
a body we invented proves nothing about the real plugin.

### The integrations the stubs leave dark

A stub only wakes an integration if it satisfies that integration's
`isInstalled()` AND its version gate. Five currently do not, and the stubs are
NOT to be hand-edited to fix that — they are generated from the upstream source,
so waking these means regenerating the stub from a newer release:

- **Avada** (`FUSION_BUILDER_VERSION` 3.11.7, needs 3.12.0), **Breakdance**
  (2.3.0-rc.2, needs 2.5.0) and **WPBakery** (7.9.0, needs 8.0) are installed but
  fail the version gate, so they register no hooks and take the `notify()` path
  on every boot.
- **GamiPress** declares no `GAMIPRESS_VER`, and **WooRewards** declares neither
  `\LWS_WooRewards` nor `\LWS\WOOREWARDS\Core\Trace`.

One stub can never be loaded: **action-scheduler**, because the plugin bundles
Action Scheduler itself (`vendors/woocommerce/action-scheduler`) and the stub
would redeclare it. See `tests/pest/mu-plugins` for that list and its reasons —
it is two entries long, and both are traced.

Bricks, Divi and Flatsome are themes, not plugins: their `isInstalled()` asks
`wp_get_theme(get_template())`, which no stub can answer. They would need the
theme on disk in wp-env.

`ActiveIntegrationsTest` asserts all of the above, so it is the thing that will
tell you when a regenerated stub has changed the picture.

## Coverage

`phpunit.xml`, gated at **80%** (`composer test:coverage`). It covers everything
in `plugin/` except the third-party integrations, which its `<source>` block
excludes. `Integrations/Gutenberg` is in: Gutenberg is WordPress, so every line
of it runs on every site.

The other 30 integrations are left out of the gate on purpose: ~8,000 statements
of adapter code tested against signature-only stubs, which reach the hook
registration, the version gate and the pure transformers but stop at the first
line that reads a value back from the third party. Holding that to the gate would
punish the plugin for code it cannot exercise without the real plugins installed.
What would cover them is installing real plugins into `.wp-env.json` (the
mu-plugin already drops the stub for any plugin that is really there), not writing
more tests.

A ThirdParty test that exercises the plugin's own code still counts towards the
number, which is right — the line ran.

## Conventions

- Assertions are `expect()`, with the actual value as the subject.
- The few assertions that carry a custom failure message stay as
  `$this->assertTrue(…, 'message')` — `expect()` cannot carry one.
- Helpers shared by one file are plain functions in that file; Pest test files
  share a single global function namespace, so those names are unique across
  the suite (`buildField`, `buildReviewField`, `buildSettingField`).
- Anything shared by more than one file belongs in `Support/`.
