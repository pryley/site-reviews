# Browser tests

`tests/browser/*.mjs` are node scripts that drive a real browser through Playwright against the wp-env site and exit non-zero on a failed assertion. Each one is an admin screen, and each asserts what a user sees on it, never how the admin script does it, so the scripts can be rewritten under them.

    make test:browser                  # every screen, in Chromium and in WebKit
    make test:browser SCREEN=settings  # one screen (npm run test:browser -- settings)
    GLSR_ENGINE=webkit node tests/browser/settings.mjs   # one screen in one engine

`harness.mjs` holds what they share: `launch()`, `login()`, `reporter()` for the PASS, FAIL and INFO lines, `pageErrorsOf()` for a page's uncaught errors, `restRequest()`, `createReview()` and `deleteReview()` (through the REST API as the logged-in administrator), and `wpCli()` for what REST cannot do (a pending status, a term, a setting). `GLSR_ENGINE=chromium|webkit` picks the engine, `GLSR_BROWSER` the Chromium-family binary (Brave by default), and `WP_ENV_PORT` overrides the port `.wp-env.json` names (8890). WebKit is the build Playwright ships: `npx playwright install webkit` after a Playwright update.

Every script creates what it needs and deletes it in a `finally`, so a failed run leaves nothing on the site; a setting it changes is read inside the same `try` and restored in that `finally`, never before the `try`. An INFO line names a behaviour the site cannot show (no colour setting in core, no classic Widgets screen on a block theme, the verify icon until a snippet or addon turns `verification/enabled` on) so the run says what it did not cover.

| Script | Screen | What it asserts |
|---|---|---|
| `reviews-list.mjs` | the review list | the status toggle redraws the row and the counts; the pin and verify toggles; the author filter's dropdown opens, lists and chooses; a Screen Options filter checkbox hides its dropdown and the choice survives a reload; quick edit fills the content and response fields |
| `review-editor.mjs` | the review editor | the assigned posts and users searches find, assign and unassign; the publish box's pinned control edits, saves and cancels; the Edit Details toggle enables the fields; the verification request button where the setting shows it |
| `category-quick-edit.mjs` | the categories screen | quick edit fills Priority from the row |
| `settings.mjs` | Settings | the tabs and their reload; a dependent field shows and hides with its control; a template-editor tag inserts at the cursor; a reset button restores the default; the Strings tab's search adds a row and its delete removes it; the License Key field's show/hide toggle. Nothing is saved |
| `tools.mjs` | Tools | the console reloads, changes level and clears; System Info loads; a tool runs and shows its notice; a card heading toggles and an expand link opens its card; a two-row CSV imports through the progress bar; the rollback dialog opens and closes on Escape |
| `documentation.mjs` | Help & Support | the tabs; a code sample is highlighted; copying a sample gives plain text (Chromium) |
| `welcome.mjs` | Welcome | the tabs; a playlist item loads its video frame |
| `dashboard.mjs` | Dashboard | the widget renders; the admin bundle loads there today; a Gatekeeper notice's dismiss persists where one shows |
| `plugins.mjs` | Plugins | the deactivation dialog opens, takes a reason and closes without deactivating |
| `foreign-screens.mjs` | a post's block editor, the posts list, the Site Editor, the Widgets screen | what each loads of the admin bundle today |

The importer screen (`admin.php?import=site-review-form`) is premium's and is covered when premium is active on the site.
