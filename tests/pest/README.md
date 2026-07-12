# Site Reviews Tests

Pest 4 against a REAL WordPress — no `WP_UnitTestCase`, no polyfills. This
suite **replaces** the old phpunit suite (`tests/phpunit`, deleted); Pest is
the plugin's only test runner.

## Why it does not use WordPress's test framework

Core's framework is pinned to PHPUnit 9 and Pest 4 needs PHPUnit 12, so the
suite boots WordPress itself (`bootstrap.php` requires `wp-load.php`). The two
things the old tests actually used from core's framework are reimplemented in
`Support/`: the post/term/user factories and the admin-ajax harness. Isolation
comes from a DB transaction per test instead of core's rollback.

Two lint-only tools live in their own composer projects, installed on first
use by the target that needs them:

- `+/tools/wp-since` (`make check`) — it pins an older toolchain than Pest.
- `+/tools/phpcs` (`make compat`) — PHPCompatibility is required as a git
  branch (`dev-develop`), and the wp-env container has no git, so Composer
  cannot install it there. It runs on the host instead.

That leaves the root `composer.json` with nothing but stable, dist-installable
packages, which is what lets it install cleanly inside the container.

## Layout

- `Unit/` — logic against the booted WordPress: helpers, casts, sanitizers,
  encryption, the field/form HTML builders. Creates no posts, terms or users.
- `Integration/` — the database: the review manager, the query builder, the
  migrations, ajax review submissions.
- `Support/` — autoloaded by composer (`autoload-dev`): `helpers.php` (the
  factories plus `resetPluginState()`), `InteractsWithAjax` (the port of
  `WP_Ajax_UnitTestCase`), `SubmitsReviews` (the review-submission harness)
  and `MockClass`.
- `mu-plugins/` — loads the integration stubs and disables the deprecated
  v5–v8 fallbacks. It has to be an mu-plugin: `deprecated.php` registers those
  fallbacks on `plugins_loaded`, which is already too late for `bootstrap.php`.
  It is inert unless `GLSR_UNIT_TESTS` is defined, so it does not affect
  ordinary web requests to the same install.

Every test — Unit and Integration — runs inside a DB transaction that rolls
back (see `Pest.php`). The plugin's settings live in the options table, so
even a field-building test writes to the database.

## Running

Requires Docker + Node. Everything runs inside wp-env: the composer
dependencies are installed in the container (PHP 8.3, see `.wp-env.json`).

    make test:install     # once: starts wp-env + composer update inside it
    make test             # the whole suite
    make test:unit        # fast feedback loop
    make test:integration
    make test:coverage    # restarts wp-env with Xdebug, enforces --min=80

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

## Coverage

The whole of `plugin/` is in scope, `Integrations` included (see above). The
target is **80%**, enforced by `--min=80` on `composer test:coverage`.

## Conventions

- Assertions are `expect()`, with the actual value as the subject.
- The few assertions that carry a custom failure message stay as
  `$this->assertTrue(…, 'message')` — `expect()` cannot carry one.
- Helpers shared by one file are plain functions in that file; Pest test files
  share a single global function namespace, so those names are unique across
  the suite (`buildField`, `buildReviewField`, `buildSettingField`).
- Anything shared by more than one file belongs in `Support/`.
