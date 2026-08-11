# Import dedupe: implementation spec

> IMPLEMENTED 2026-08-11 (all four parts, same day as the review). The suite,
> phpstan and the PHP-floor compat check pass; see the working tree diff.

Follows the adversarial review of `import-dedupe-spec.md`, run 2026-08-11 against
baseline `7be1d1c03` (wp-env: WordPress 7.0.2, PHP 8.3.33, MySQL sql_mode without
`NO_AUTO_VALUE_ON_ZERO`). Every measured claim in the review spec was re-executed;
probe scripts and outputs are in the appendix. Labels follow the review spec:
MEASURED (probe run, output quoted), TRACED (read from source with file:line).

## Review verdict

The four attacks the review spec requested, and their outcomes:

1. **B's benchmark distribution.** MEASURED across three seeds. The ratio holds:
   46.5x (200 reviews), 49.7x (3,000 reviews), 48.8x (10,000 reviews). Both plans
   scan every review per lookup; batching pays that cost once per page instead of
   once per row, so the ratio tracks the page size (50), not the data shape.
   Attack failed; B's premise stands.
2. **C's shared temp file.** MEASURED, all three mechanisms:
   - Run 1 read page 1, the file was overwritten (what run 2's stage 1 does),
     run 1's page 2 returned run 2's rows.
   - A row was inserted into `glsr_tmp`, `flush()` ran, the table was gone —
     with premium active this destroys run 1's stage-3 attachment queue.
   - Two `CreateReview` commands both checked `importedReview()` before either
     created; both creates succeeded; two reviews shared one `_submitted_hash`.
     Nothing in the schema prevents the duplicate.
   The interleaving was hand-sequenced in one process. True HTTP concurrency was
   not exercised; the mechanism needs no timing assumption, only the absence of a
   lock, which is TRACED (`Router.php:196` lists only `submit-review`).
3. **A's "ID of 0 or less cannot match a row".** No counterexample found.
   `wp_insert_post()` accepts an explicit ID only via `import_id`, gated by
   `!empty()` (`wp-includes/post.php:4917`, the running copy), so 0 never passes.
   `AUTO_INCREMENT` starts at 1. `NO_AUTO_VALUE_ON_ZERO` is absent from default
   sql_mode (MEASURED here) and would still need a writer that bypasses WordPress.
   Assumption 1 survives.
4. **Rule 8.** No proposal buys test convenience with live behaviour. All three
   change production for production's benefit. The one live-behaviour cost found
   is in C1's exact-hash variant (below): it would fire `review/request` twice
   per row. The recommended variant avoids it.

## Corrections to the review spec

These do not sink any proposal, but the implementation must account for them.

1. **A's "other callers" list is overstated.** TRACED: of the five cited call
   sites, only two hydrate an unvalidated id.
   `ProductReviewsController.php:275` sits inside an existing `$reviewId > 0`
   guard. `ExperimentsController.php:32` is gated by `Review::isReview($objectId)`,
   so the post exists. `Review.php:210` (`isEditable`) is gated the same way.
   The genuine unguarded sites are `RelationSaveHelper.php:303` and `:311`
   (MultilingualPress, id 0 whenever no remote post exists) and
   `ProductReviewsController.php:322` (route regex `[\d]+` admits a crafted
   `/reviews/0` from an authenticated user). The "fix it beside the query"
   argument stands on those two; the breadth claim does not.
2. **C part two is understated.** The second run's stage 1 does not only
   overwrite the CSV: `ProcessCsvFile::process()` first calls
   `ImportManager::flush()` (`:111`), which DROPS `glsr_tmp` — premium's
   attachment queue for the first run's stage 3 — then `unlinkTempFile()`
   (`:112`). MEASURED (appendix, probe C). The lock must be taken before either.
3. **B's buffering rationale cites the wrong mechanism.** `records()` sets no
   `where` callbacks; those belong to stage 1. The real constraint is that
   `Statement::process()` returns a lazy iterator over the reader's single
   stream (TRACED, `vendors/thephpleague/csv/Statement.php:319-348`). Same
   conclusion — read it exactly once — different reason.
4. **C1's hash is coupled to the write-time formatters.** Stage 1 applies
   `formatRecord()` and `EscapeFormula` as Writer formatters, so the staged file
   differs from the records the staging loop sees. MEASURED: `EscapeFormula` is
   idempotent, and distinct inputs collide post-escape (`=x` and `'=x` both
   write as `'=x`). A staging hash computed pre-escape misses those twins.
   Worse, premium Images hooks `review/request` and mutates the UNGUARDED
   `images` field exactly when `WP_IMPORTING` is defined
   (`Features/Images/Controllers/ImportController.php:79-86`), so stage 2's hash
   already reflects a listener mutation that a bare staging hash cannot see.
   See C1's two variants below.
5. **Cleanup is conditional.** `ImportReviewsCleanup::handle()` flushes and
   unlinks only when `imported > 0` (TRACED). The C2 lock release must not sit
   behind that condition. The JS abort path still reaches stage 4 (the aborted
   `AbortController` is discarded before the stage-4 fetch), but a closed tab
   never does — the lock needs an expiry.
6. **Established, no longer assumed.** Premium Authors hooks
   `site-reviews/get/review` (`Features/Authors/Hooks.php:53-54`); both handlers
   run on every fire with no `isValid()` gate. The action must keep firing for
   invalid ids, exactly as proposal A preserves. Assumption 3 is now TRACED.
7. **Verified mechanics** (MEASURED, appendix probe misc): `array_column($items, 2)`
   selects the third element of list-shaped triples; `wpdb::get_results()`
   returns `[]` (not null) on no rows; `esc_sql()` is a no-op on md5 output;
   `array_column(..., 'review_id', 'submitted_hash')` returns review ids as
   STRINGS — harmless here (`empty()` check, then `glsr_get_review()` casts).

## A. Conditional fetch in `Query::review()`

As proposed in the review spec, unchanged. `plugin/Database/Query.php:69`:

```php
$result = $reviewId > 0
    ? glsr(Database::class)->dbGetRow($this->queryReviews($reviewId), \ARRAY_A)
    : null;
$review = new Review($result);
```

`get/review` still fires (premium Authors depends on it — correction 6), the
cache store still gates on `isValid()`, and `new Review(null)` is what a miss
builds today (`Review.php:78-86` early-returns on empty values).

Tests (Integration suite):

- A zero id and a negative id each return an invalid `Review` and issue no
  database query (count with `add_filter('query', ...)`).
- `get/review` fires for a zero id with the invalid review and the id.
- An existing review still hydrates, cached and uncached.

## B. Batched dedupe lookup in `ImportManager`

As proposed in the review spec, with these implementation notes:

- Land A first. B hydrates only on a hit, but a hash pointing at a review
  deleted mid-import still reaches `glsr_get_review()`; A is the guard for
  every other caller regardless.
- `const LOOKUP_CHUNK = 50;` at the top of the class. One chunk per default
  page; memory is bounded by 50 buffered CSV rows.
- Add `use GeminiLabs\SiteReviews\Helpers\Arr;` to `ImportManager`.
- Method order (visibility, then alphabetical): `importedReviewIds()` goes
  after `importedReview()`; `importRecords()` is protected and goes below the
  public methods.
- `importedReview(string $hash)` stays as the single-hash entry point and
  delegates to `importedReviewIds([$hash])`, so one SQL string remains.
- The per-record `import/review/attachments` calls keep their order and their
  four arguments on both paths — premium registers with three accepted args and
  must keep receiving the same first three (correction to nothing; verified
  against `Features/Images/Hooks.php:77`).
- The live map line (`$reviewIds[$hash] = $review->ID;`) is the regression
  guard for twins inside one chunk. The test below fails without it.
- A hash mapping to a review that was deleted between the batch query and the
  loop goes down the create path. That matches today's behaviour: the per-row
  JOIN also stops matching once the posts row is gone.
- `MIN(p.ID)` (oldest wins) replaces the engine-arbitrary row choice.
  DECIDED (Paul, 2026-08-11): oldest wins. MEASURED at zero cost — 13.10 ms
  grouped vs 13.11 ms plain on the 3,000-review seed (appendix). The plan gains
  `Using temporary; Using filesort`, but the temporary table holds only the
  rows that survive the join filter (at most one per matched hash), not the
  3,000-row scan both plans share.

Tests (Import suite):

- Two identical rows in ONE page: one imported, one skipped (fails without the
  live map).
- Dedupe SELECT count is 1 per chunk for a 20-row import (a `query` filter
  matching the statement; the 20 `update_post_meta()` existence SELECTs are
  WordPress's and stay).
- A hash pointing at a deleted review re-imports rather than skipping.
- The existing "same csv imported twice" test stays green untouched.

## C2. Run lock

Taken before the destructive part of stage 1, which is its very first act
(correction 2). Design:

- Transient `glsr()->prefix.'import_lock'`, value `time()`, expiry 120 seconds.
- `ProcessCsvFile::handle()` checks and takes the lock BEFORE `process()` runs
  `flush()`/`unlinkTempFile()`. Held lock → error notice ("an import is already
  running, try again in two minutes"), `fail()`, return.
- Each `ImportManager::import()` and `importAttachments()` call re-arms the
  lock (same expiry). A live run never lapses; a crashed or closed-tab run
  unlocks in at most 120 seconds. No page request is expected to exceed that —
  the slowest measured page here is under 2 seconds; a site where a page
  exceeds 120 seconds re-arms on the next request anyway and only risks a
  second run starting between two very slow pages.
- Released unconditionally in `ImportReviewsCleanup::handle()` — NOT behind the
  `imported > 0` gate (correction 5) — and on every `ProcessCsvFile` failure
  path that currently calls `unlinkTempFile()`.
- Do NOT reuse `Router::mutexActions()` — it rejects parallel requests
  (`Router.php:161-178`), which would fail three of every four page requests.
- Lock scope is stage 1 only. Stages 2-4 re-arm but never check: the
  destructive acts (flush, unlink, overwrite) all live in stage 1 and cleanup,
  so locking admission is sufficient, and page requests of the running import
  cannot block themselves.
- A WP-CLI or addon caller that drives `ImportReviews` directly never takes the
  lock; it also never runs stage 1, so it destroys nothing. Document, don't
  defend.

Tests (Import suite):

- Stage 1 refuses while a lock is held, and the notice says why.
- Stage 1 proceeds when the held lock has expired.
- Cleanup releases the lock even with `imported = 0`.
- A failed stage 1 (bad file) releases the lock.

## C1. Dedupe at staging — bare hash

The single-threaded stage 1 drops rows whose submitted hash it has already
staged, so duplicates never reach two concurrent pages. The race then has
nothing to race over. The complication is which hash (correction 4).

DECIDED (Paul, 2026-08-11): variant 1, on measured cost. Per staged row under
`WP_IMPORTING`, zero queries either way (appendix): bare = 0.168 ms, exact =
3.504 ms — 21x. Stage 1 is ONE request, so on a 100k-row file exact adds
~350 seconds inside it (past typical gateway timeouts) and fires
`review/request` per row a second time, running premium's listeners twice.
Bare adds ~17 seconds per 100k rows and fires nothing.

**Variant 1 — bare hash (chosen).** In `process()`, move `formatRecord()`
from the Writer's formatters into the insert generator, apply
`EscapeFormula::escapeRecord()` there too (idempotent — the Writer applying it
again is harmless), and hash:

```php
$values = (new Request($record))->toArray();
$hash = md5(maybe_serialize(glsr(SubmittedFieldsDefaults::class)->filter($values)));
```

Catches every byte-identical staged row — the actual duplicate-export case.
Cost per row: one `Request` and one md5; no hooks fire. Residual: two rows that
differ raw but become identical only through a `review/request` listener
mutation (premium Images sanitizing `images` URLs) or through `Request`
sanitization are NOT caught at staging and can still race across overlapping
pages. That residual pair must differ raw yet sanitize equal, land in different
pages, and overlap in time.

**Variant 2 — exact hash (rejected).** Build a full `CreateReview` per staged
row and use `submitted()['submitted_hash']`. Byte-exact against stage 2, closes
the race completely — but MEASURED at 3.5 ms per row in the single stage-1
request, and fires the public `review/request` action twice per row (once at
staging, once at stage 2), running premium's listeners twice. That is a
live-behaviour change outside this plugin's own code (rule 8 cost) on top of
the timeout risk.

Counting: a dropped row increments the stage-1 `skipped` counter, its reason
joins `$this->errors` ("duplicate row"), and `total` excludes it — the JS
computes stage-2 pages from `total`, so the arithmetic stays consistent
(`import.js:45,118`).

Tests (Import suite):

- A CSV with the same row twice: stage 1 stages it once, reports one skipped.
- A CSV with the row twice split across two `import()` pages imports once.
- Stage-1 `total` matches the rows stage 2 will read.

## Order of work

1. A — standalone, smallest, other proposals lean on it.
2. B — absorbs A on the hit path; carries the live-map regression test.
3. C2 — independent of B; guards the temp file and `glsr_tmp` immediately.
4. C1 — bare-hash variant, decided above.

## Decisions

Asked interactively on 2026-08-11; both answered by Paul.

1. **B: `MIN(p.ID)` (oldest wins)** when several reviews share a hash —
   accepted, on the zero-cost measurement.
2. **C1: bare hash** — accepted, on the 21x per-row cost measurement and the
   `review/request` double-fire the exact variant would add. The residual
   (rows that differ raw but sanitize identical can still race across
   overlapping pages) is accepted and documented.

## Appendix: probes added by this review

All run inside wp-env via
`npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews php <file>.php`.
Each cleans up after itself; the b2 and C probes delete only the posts they
created (diff of pre/post review ids), not every review on the dev site.

### Probe A rerun

Review spec's script, verbatim. Output:

    glsr_get_review(0) x50: 97.6 ms, 50 queries

### Probe B reruns, three distributions

Review spec's script parameterised by `(reviews, filler-meta-per-review)`:

    ( 3000, 20): EXPLAIN p ref type_status_date rows=3000  | per-row = 608.0 ms | batched = 12.2 ms | 49.7x
    (  200, 20): EXPLAIN p ref type_status_date rows=200   | per-row =  37.9 ms | batched =  0.8 ms | 46.5x
    (10000,  5): EXPLAIN p ref type_status_date rows=10100 | per-row = 1709.1 ms | batched = 35.0 ms | 48.8x

### Probe B real code path rerun

Review spec's script with the safe cleanup. Output matches the spec exactly:

    imported=20 skipped=1 | dedupe SELECTs=20, postmeta existence SELECTs=20

### Probe C — the three mechanisms

```php
<?php
require_once '/var/www/html/wp-load.php';
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database\ImportManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Request;
global $wpdb;
$preexisting = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", glsr()->post_type));
$im = glsr(ImportManager::class);

// 1. run 2's stage 1 overwrite corrupts run 1's later pages
$fileA = "date,rating,name\n";
for ($i = 1; $i <= 4; ++$i) { $fileA .= "2024-01-0{$i},5,RUN-A-{$i}\n"; }
file_put_contents($im->tempFilePath(), $fileA);
$page1 = [];
foreach ($im->records(2, 0) as $values) { $page1[] = $values['name']; }
$fileB = "date,rating,name\n2024-02-01,4,RUN-B-1\n2024-02-02,4,RUN-B-2\n2024-02-03,4,RUN-B-3\n";
file_put_contents($im->tempFilePath(), $fileB);
$page2 = [];
foreach ($im->records(2, 2) as $values) { $page2[] = $values['name']; }
printf("run1 page1: %s | run1 page2 (after overwrite): %s\n", implode(',', $page1), implode(',', $page2) ?: '(empty)');

// 2. flush() drops glsr_tmp under the first run
$im->prepare();
$table = glsr(GeminiLabs\SiteReviews\Database\Tables::class)->table('tmp');
$wpdb->insert($table, ['type' => 'attachment', 'data' => maybe_serialize([123 => ['img.jpg']])]);
$rowsBefore = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
$im->flush();
$existsAfter = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
printf("glsr_tmp rows before flush: %s | table exists after flush: %s\n", $rowsBefore, var_export($existsAfter, true));

// 3. the dedupe race, interleaved by hand: both requests check before either creates
$values = ['date' => '2024-03-01', 'rating' => '5', 'name' => 'RACE-TWIN'];
$cmdA = new CreateReview(new Request($values));
$cmdB = new CreateReview(new Request($values));
$hash = $cmdA->submitted()['submitted_hash'];
$missA = $im->importedReview($hash);
$missB = $im->importedReview($hash);
$im->markImporting();
$reviewA = glsr(ReviewManager::class)->create($cmdA);
$reviewB = glsr(ReviewManager::class)->create($cmdB);
$count = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID
     WHERE p.post_type = %s AND pm.meta_key = '_submitted_hash' AND pm.meta_value = %s",
    glsr()->post_type, $hash
));
printf("race: missA=%s missB=%s | reviews sharing the hash after both create: %s\n",
    var_export(null === $missA, true), var_export(null === $missB, true), $count);

// cleanup
$im->flush();
$im->unlinkTempFile();
$created = array_diff(
    $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", glsr()->post_type)),
    $preexisting
);
foreach ($created as $id) { wp_delete_post((int) $id, true); }
```

Output:

    run1 page1: RUN-A-1,RUN-A-2 | run1 page2 (after overwrite): RUN-B-3
    glsr_tmp rows before flush: 1 | table exists after flush: NULL
    race: missA=true missB=true | reviews sharing the hash after both create: 2

### Probe misc — PHP and wpdb mechanics

```php
<?php
require_once '/var/www/html/wp-load.php';
global $wpdb;
$items = [
    [new stdClass(), new stdClass(), 'hash-aa'],
    [new stdClass(), new stdClass(), 'hash-bb'],
    [new stdClass(), new stdClass(), 'hash-aa'],
];
var_export(array_column($items, 2));                    // ['hash-aa','hash-bb','hash-aa']
var_export($wpdb->get_results("SELECT ID FROM {$wpdb->posts} WHERE 1=0", ARRAY_A)); // []
var_export(md5('x') === esc_sql(md5('x')));             // true
var_export(array_column([
    ['submitted_hash' => 'aaa', 'review_id' => '5'],
    ['submitted_hash' => 'bbb', 'review_id' => '9'],
], 'review_id', 'submitted_hash'));                     // ['aaa' => '5', 'bbb' => '9'] — strings
var_export($wpdb->get_var('SELECT @@sql_mode'));        // no NO_AUTO_VALUE_ON_ZERO
```

### Probe MIN — GROUP BY + MIN(p.ID) versus the plain IN list

Same seed as probe B (3,000 reviews, 20 filler meta each). 50-hash IN list,
25 hits + 25 misses, warmed, 20 timed runs each:

    EXPLAIN plain  : p ref type_status_date rows=3000 | pm ref|filter post_id|meta_key rows=1
    EXPLAIN grouped: p ref type_status_date rows=3000 extra=Using temporary; Using filesort | pm ref|filter rows=1
    plain  : 13.11 ms per query
    grouped: 13.10 ms per query

### Probe C1 cost — bare versus exact hash per staged row

2,000 realistic rows (name, email, ~270-char content), `markImporting()` first
so both variants take the import paths (`avatar()` skips generation). Premium
was NOT active; the exact variant fires `review/request` per row, so premium
listeners would add to its cost on a live site.

```php
// bare
$values = (new Request($row))->toArray();
$hash = md5(maybe_serialize(glsr(SubmittedFieldsDefaults::class)->filter($values)));
// exact
$hash = (new CreateReview(new Request($row)))->submitted()['submitted_hash'];
```

    bare : 336.6 ms for 2000 rows = 0.168 ms/row | 0 queries
    exact: 7008.9 ms for 2000 rows = 3.504 ms/row | 0 queries
    per 100k rows: bare 16.8 s | exact 350.4 s   (stage 1 is a single request)

### Probe escape — EscapeFormula idempotence and collision

```php
<?php
require_once '/var/www/html/wp-load.php';
$e = new GeminiLabs\League\Csv\EscapeFormula();
$r1 = $e->escapeRecord(['=x', '-x', '@x', 'normal', "'=x"]);
$r2 = $e->escapeRecord($r1);
echo json_encode($r1), "\n";        // ["'=x","'-x","'@x","normal","'=x"]
echo var_export($r1 === $r2, true); // true (idempotent)
```

`=x` and `'=x` both stage as `'=x`: distinct raw rows can collide post-escape,
so a pre-escape staging hash misses twins that stage 2 will consider identical.
