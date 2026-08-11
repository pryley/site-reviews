# Import dedupe: three defects and their proposed fixes

Written for an adversarial review. Nothing here has been implemented — the three
proposals are unexecuted. Baseline is `7be1d1c03` on `develop` (8.2.1 released).

## For the reviewer

Your job is to falsify, not to confirm. Every claim below carries a label:

- **MEASURED** — a probe was run and its output is quoted. Re-run it; the scripts
  are in the appendix. Absolute milliseconds come from wp-env's MySQL on one
  laptop and are not a real site's numbers. The RATIO is the claim.
- **TRACED** — read from the source, with `file:line`. Re-read it; the line
  numbers are from the baseline commit and drift.
- **ASSUMED** — not established. These are listed together at the end and are the
  most likely place for this document to be wrong.

**The free repo is not the whole picture.** Premium
(`/Users/me/Code/GeminiLabs/site-reviews-premium`) and the standalone addons
extend the importer through documented filters, and they write to tables the free
plugin only creates. Any claim of the form "nothing uses X" is worthless unless it
was grepped there too. This document already got that wrong once — an earlier
revision called `glsr_tmp` an unused table on the strength of a grep over
`/plugin` alone. See the corrected finding below, and treat it as the worked
example of how this review goes wrong.

Specific things worth attacking, in the order I would attack them:

1. The benchmark in B depends on the data distribution I seeded. My first seed
   made every postmeta row a `_submitted_hash` and produced a completely
   different query plan (full table scan) from my second, realistic seed
   (index-driven, `rows=3000`). Both are in the appendix. Seed a third
   distribution and see whether the ratio survives.
2. C claims the shared temp file is already broken for concurrent runs. That is
   traced, not measured. Reproduce it, or show why it cannot happen.
3. A rests on "a post ID of 0 or less cannot match a row". Find the install where
   that is false before accepting it.
4. Every proposal is judged against `CLAUDE.md` rule 8 — the plugin serves the
   sites it runs on, not the suite. If any of them buys test convenience with a
   live site's behaviour, say so.

Commands are in `CLAUDE.md`. Probes run with:

    npx @wordpress/env run cli --env-cwd=wp-content/plugins/site-reviews php <file>.php

## The flow all three sit in

TRACED, end to end:

1. `views/pages/tools/general/import-reviews.php:117` — the submit button carries
   `data-per_page="50"`.
2. `+/scripts/admin/import.js` — four stages. Stage 1 uploads and stages the CSV,
   stage 2 imports reviews, stage 3 imports attachments, stage 4 cleans up.
   Stage 2 splits the work into `ceil(total / per_page)` pages and runs them
   through `pLimit` at `import.js:117`: **4 concurrent requests** when
   `per_page !== 1`.
3. Router → `site-reviews/route/ajax/import-reviews` →
   `ToolsController::importReviewsAjax()` (`:251`) → `ImportReviews`, which reads
   `per_page` and `page` straight from the request
   (`Commands/ImportReviews.php:17-18`): `limit = max(1, per_page)`,
   `offset = limit * (max(1, page) - 1)`. There is no server-side ceiling on
   `per_page`.
4. `ImportManager::import($limit, $offset)` → `records()` → `prepare()` (creates
   `glsr_tmp`) and a **lazy** League CSV `Statement` with offset/limit over the
   staged temp CSV → per record: `new Request` → `new CreateReview` →
   `submitted()` → skip if nothing was submitted → `importedReview($hash)` →
   `ReviewManager::create()`. Each record then calls the
   `site-reviews/import/review/attachments` filter, on both the created and the
   skipped path — that filter is premium's seam, and `glsr_tmp` is its queue (see
   the corrected finding at the end).
5. `Router::mutexActions()` (`Router.php:196`) contains **only** `submit-review`,
   so those 4 page requests are not serialised.

Two facts the three defects share:

- `CreateReview::submitted()` (`Commands/CreateReview.php:165`) returns
  `['submitted' => $values, 'submitted_hash' => md5(maybe_serialize($values))]`,
  where `$values` is the request minus guarded keys minus empties
  (`Defaults/SubmittedFieldsDefaults.php`, `DefaultsAbstract::filter()`).
  `CreateReview::meta():115` merges that into the review's meta unconditionally,
  so **every** review stores `_submitted_hash`.
- `ProcessCsvFile::REQUIRED_KEYS` is `date, rating` (`:43`), and neither is
  guarded — so every staged row submits at least two values.

---

## A. Every dedupe miss hydrates a review that cannot exist

### Problem

`ImportManager::importedReview()` ends with:

```php
$reviewId = Cast::toInt($reviewId);   // 0 when the lookup found nothing
$review = glsr_get_review($reviewId);
if (!$review->isValid()) {
    return null;
}
```

TRACED: `glsr_get_review()` (`helpers.php:246`) casts to int and calls
`ReviewManager::get()` (`:193`), a two-line passthrough to `Query::review()`
(`Database/Query.php:62`). `Query::review()` reads the cache, and on a miss runs
`dbGetRow($this->queryReviews($reviewId))` — the full review query, which joins
the ratings table — then builds a `Review` from the null result. A miss is never
cached, so it re-queries every time.

An id of `0` cannot match a row, so this query is guaranteed to return nothing.
It runs on every dedupe MISS, which on a fresh import is nearly every row.

MEASURED: 50 calls to `glsr_get_review(0)` = **50 queries, 85.6 ms**.

The importer is not the only caller that hydrates an unvalidated id. TRACED:
`Integrations/WooCommerce/Controllers/RestApi/ProductReviewsController.php:275`
and `:322` (REST parameters), `Integrations/MultilingualPress/RelationSaveHelper.php:303`
(`remotePostId()`, which is 0 when there is no remote post yet),
`Integrations/WooCommerce/Controllers/ExperimentsController.php:32`, `Review.php:210`.

### Proposal

Do NOT add a guard in `ImportManager`. Change `Query::review()` so the fetch is
conditional:

```php
$result = $reviewId > 0
    ? glsr(Database::class)->dbGetRow($this->queryReviews($reviewId), \ARRAY_A)
    : null;
$review = new Review($result);
```

Everything after it is unchanged: `glsr()->action('get/review', $review, $reviewId)`
still fires, and the cache store still gates on `isValid()`.

### Reasoning

- The fact being encoded ("an id of 0 or less cannot identify a post") is a
  property of the lookup, so it belongs beside the query, not at one call site.
  A check in `ImportManager` fixes one caller and leaves the REST and
  MultilingualPress paths paying the same cost.
- `Query::review()` over `ReviewManager::get()`: the manager is a passthrough and
  is not the only possible route to the query. Today nothing else calls
  `Query::review()` directly (TRACED: one call site, `ReviewManager.php:195`), so
  the practical difference is nil and the argument is about where the fact lives.
  A reviewer who prefers the manager should say why the query should be reachable
  without the guard.
- A conditional fetch, not an early `return`. An early return would stop
  `get/review` firing for these calls, which is a public hook contract an addon
  may be on. The returned object is identical either way: `dbGetRow` already
  returns `null` on a miss, and `Review::__construct` (`Review.php:78`) early-returns
  on empty values, so `new Review(null)` is what a miss produces today.
- Deliberately NOT extended to nonzero ids that do not exist: those still query
  every time, and are still not cached. The review may be created later in the
  same request, so caching a miss would be a correctness trap.

### What would make this wrong

- An install where a post can have `ID <= 0`. `wp_posts.ID` is `AUTO_INCREMENT`,
  which starts at 1, but MySQL's `NO_AUTO_VALUE_ON_ZERO` sql_mode permits an
  explicit 0. Check whether any supported host ships that mode AND whether
  WordPress could ever write such a row.
- An addon whose `get/review` handler distinguishes "queried and missed" from
  "not queried". It cannot see the difference through the arguments, but check
  whether anything reads `$wpdb->last_query` or a query filter downstream of it.
- A caller that relies on the query firing for its side effects (a query filter,
  a `posts_where` hook, a query counter).

### Tests

- A zero id and a non-numeric id each return an invalid `Review` and issue no
  database query. Count with `add_filter('query', …)`, which is countable without
  `SAVEQUERIES`.
- `get/review` still fires for a zero id, with an invalid review and the id.
- An existing review still hydrates, cached and uncached.

---

## B. One dedupe query per row, each O(existing reviews)

### Problem

`ImportManager::import()` calls `importedReview()` once per record. The query is:

```sql
SELECT p.ID
FROM table|posts AS p
INNER JOIN table|postmeta AS pm ON (pm.post_id = p.ID)
WHERE 1=1
  AND p.post_type = %s
  AND pm.meta_key = '_submitted_hash'
  AND pm.meta_value = %s
```

`postmeta.meta_value` is not indexed and cannot usefully be — so the engine drives
from the review post type and probes meta per review.

MEASURED, on a seed of 3,000 reviews carrying a hash inside 63,196 postmeta rows
(a realistic key mix — 20 filler meta rows per review):

```
EXPLAIN: table=p  type=ref        key=type_status_date  rows=3000
EXPLAIN: table=pm type=ref|filter key=post_id|meta_key  rows=1 (5%)
page of 50: per-row = 609.9 ms (50 queries) | batched = 12.4 ms (1 query) | 49.4x
```

`rows=3000` is every review on the site, per lookup. The cost is therefore
O(rows imported × reviews already present), and it grows DURING the import as each
created review enlarges the scan set.

MEASURED, the same code path in a real import of 21 records (20 importable, 1
submitting nothing): **20 dedupe SELECTs**, plus 20 unrelated SELECTs that
`update_post_meta()` issues to check whether the key exists before writing it.
The row that submits nothing costs zero queries — `7be1d1c03` moved that check
above the lookup.

### Proposal

Batch the lookup per chunk of the page. In `ImportManager`:

```php
// import(): walk the lazy reader ONCE, buffering
$buffer = [];
foreach ($this->records($limit, $offset) as $values) {
    $buffer[] = $values;
    if (static::LOOKUP_CHUNK === count($buffer)) {
        $this->importRecords($buffer, $result);
        $buffer = [];
    }
}
if (!empty($buffer)) {
    $this->importRecords($buffer, $result);
}
```

```php
public function importedReviewIds(array $submittedHashes): array
{
    if (empty($submittedHashes)) {
        return [];
    }
    $hashes = implode("','", array_map('esc_sql', $submittedHashes));
    $sql = "
        SELECT pm.meta_value AS submitted_hash, MIN(p.ID) AS review_id
        FROM table|posts AS p
        INNER JOIN table|postmeta AS pm ON (pm.post_id = p.ID)
        WHERE p.post_type = %s
        AND pm.meta_key = '_submitted_hash'
        AND pm.meta_value IN ('{$hashes}')
        GROUP BY pm.meta_value
    ";
    $sql = glsr(Query::class)->sql($sql, glsr()->post_type);
    $results = glsr(Database::class)->dbGetResults($sql, 'ARRAY_A');
    return array_column(Arr::consolidate($results), 'review_id', 'submitted_hash');
}
```

```php
protected function importRecords(array $records, array &$result): void
{
    $items = [];
    foreach ($records as $values) {
        $request = new Request($values);
        $command = new CreateReview($request);
        $submitted = $command->submitted();
        if (empty($submitted['submitted'])) {
            ++$result['skipped'];
            continue;
        }
        $items[] = [$request, $command, $submitted['submitted_hash']];
    }
    $reviewIds = $this->importedReviewIds(array_unique(array_column($items, 2)));
    foreach ($items as [$request, $command, $hash]) {
        $review = empty($reviewIds[$hash]) ? null : glsr_get_review($reviewIds[$hash]);
        if ($review?->isValid()) {
            $result['attachments'] += glsr()->filterInt('import/review/attachments', 0, $request, $review, false);
            ++$result['skipped'];
            continue;
        }
        if ($review = glsr(ReviewManager::class)->create($command)) {
            $reviewIds[$hash] = $review->ID; // a twin later in this chunk must still match
            $result['attachments'] += glsr()->filterInt('import/review/attachments', 0, $request, $review, true);
            ++$result['imported'];
            continue;
        }
        ++$result['skipped'];
    }
}
```

`importedReview(string $hash)` stays as the single-hash entry point and delegates,
so there is one SQL string in the class rather than two.

### Reasoning

- **Buffered, not materialised whole.** `records()` returns a lazy result whose
  `where` callbacks re-run if it is iterated twice — the bug that produced the
  `chunkBy` regression, and the reason the loop reads it exactly once. Buffering
  bounds memory without a second pass.
- **`LOOKUP_CHUNK`, not a `per_page` cap.** The JS computes page offsets from the
  `per_page` IT sent (`ImportReviews.php:18`). A server-side cap on `limit` would
  make each page read fewer rows than the client's offsets assume and silently
  drop every row in between. Chunking bounds memory and leaves the paging
  arithmetic untouched. Default page is 50, so normally one chunk per page.
- **The live map is the correctness story.** `$reviewIds[$hash] = $review->ID`
  preserves what the per-row query does today: the second of two identical rows in
  one page matches the review the first row just created. Without that line the
  change starts producing the duplicates the method exists to prevent.
- **`$items` is a list, not keyed by hash.** Duplicate rows share a hash; keying
  by it would silently drop one.
- **`GROUP BY` with `MIN(p.ID)`.** Today `dbGetVar` takes whichever row the engine
  returns first when several reviews share a hash. This makes the winner
  deterministic. It is a behaviour change, and a reviewer should decide whether
  "oldest wins" is the right rule.
- **`esc_sql` on the `IN()` list**, per `CLAUDE.md` Key Pattern 6. The values are
  md5 output — 32 hex characters, generated server-side — so nothing user-supplied
  reaches the statement and no `%` can reach `prepare()`.
- **Fix A is absorbed.** Hydration happens only on a hit, so the importer needs no
  guard of its own once A lands.

### What would make this wrong

- The benchmark's seed. My FIRST seed made every postmeta row a `_submitted_hash`
  and produced a different plan entirely (`type=ALL`, full scan of postmeta,
  56 ms vs 1.5 ms, 37x). Both seeds favour batching, but by different mechanisms.
  A distribution where they do not would sink this.
- An `IN()` list of 50 hashes might plan differently at other table sizes, or hit
  a `max_allowed_packet` ceiling at a very large `LOOKUP_CHUNK`.
- `array_column($items, 2)` on a list of arrays with integer keys — confirm it
  behaves as intended for the third element.
- Whether `dbGetResults(..., 'ARRAY_A')` returns `[]` or `null` on no rows
  (`Arr::consolidate` is there for that, but check it).
- If a hash maps to a review that was deleted between the batch query and the
  loop, `isValid()` sends it down the create path. Confirm that is right.

### Tests

- Two identical rows in ONE page: one imported, one skipped. This fails without
  the live-map line and is the regression guard for the whole change.
- Dedupe SELECT count is 1 per chunk for a 20-row import, counted with a `query`
  filter matching the statement.
- A hash pointing at a deleted review re-imports rather than skipping.
- The existing "the same csv imported twice does not make the reviews twice" must
  stay green untouched — it is the cross-run guard.

---

## C. Concurrent page requests, and the shared temp file underneath

### Problem, part one: the race

TRACED: stage 2 runs up to 4 page requests at once (`import.js:117`) and nothing
serialises them (`Router::mutexActions()` is `['submit-review']` only). Two rows
with identical submitted values, landing in DIFFERENT pages whose requests
overlap, both hash the same, both miss the lookup, and both create a review.

Preconditions: the same review must appear twice in ONE CSV, in rows far enough
apart to land in different pages, and those pages must overlap in time. Rows that
are duplicates within one page are caught today, because the loop is sequential
and the second row's query sees the first row's review.

### Problem, part two: the shared temp file

TRACED and worse. `ImportManager::tempFilePath()` (`:115-125`) returns a FIXED
path: `uploads/site-reviews/temp/import.csv`, one per site. So two import runs —
an admin double-submitting, two tabs, two admins — do not merely race over
duplicate rows. The second run's stage 1 overwrites the staged CSV that the first
run's pages are still reading, and `flush()`/`unlinkTempFile()` in either run
deletes it under the other. This is independent of the race and is not fixed by
anything proposed for it.

### Proposal

Two separate changes.

**C1 — dedupe at staging.** `ProcessCsvFile` sees the whole file, in one request,
with no concurrency by construction. As it stages each row, compute the row's
submitted hash, keep a set, and drop a row whose hash it has already staged. The
duplicate never reaches two pages, so the race has nothing to race over, and the
dropped rows are counted and explained at stage 1 where the user already sees
skipped-row reasons.

**C2 — a run lock.** A transient taken at stage 1, with an expiry longer than one
page request, released at stage 4 and by the cleanup command, with stage 1
refusing with a clear notice while one is held.

### Reasoning

- C1 removes the CAUSE (duplicate rows in one file) at the only point in the
  pipeline that is single-threaded and already walking every row, rather than
  policing the symptom in the concurrent part.
- Cost of C1 is a `Request` + `CreateReview` per row at staging to get the hash,
  plus a set of 32-character strings (~4 MB per 100k rows). A database-side
  alternative — a `hash char(32)` column with a UNIQUE index and `INSERT IGNORE` —
  would need `glsr_tmp`, which is NOT free to change: it is premium's attachment
  queue (see the corrected finding below), so the column is a migration
  coordinated across two products. Start with the in-memory set.
- C2 is needed regardless of C1, because the shared temp file breaks concurrent
  runs on its own.
- C2 must NOT reuse the router mutex. That is a 5-second per-IP transient that
  REJECTS a parallel request rather than queueing it (`Router.php:161-178`), so
  adding `import-reviews` to `mutexActions()` would fail three of every four page
  requests and break the import outright.

### What would make this wrong

- Whether the race is reachable in practice: it needs duplicate rows in one file
  AND overlapping pages. Measure the window rather than assuming it — instrument
  two concurrent page requests over a file with a planted duplicate.
- C1 changes what stage 1 reports: rows that would previously have been imported
  (as duplicates of each other) are now dropped before they reach a page. Confirm
  the counts still add up for the user, and that `response()['total']` still
  matches what stage 2 will process.
- C2's expiry: too short and a slow import unlocks mid-run; too long and a
  crashed import locks the tool out. Decide the value and how it is cleared.
- Whether two runs are worth defending at all, versus documenting it.
- Whether a WP-CLI or addon caller drives `ImportReviews` without stage 1, which
  would never take the lock.

### Tests

- A CSV with the same row twice, split across two pages, imports once.
- Stage 1 refuses while a run lock is held, and the notice says why.
- The lock is released by stage 4 and by the cleanup command.

---

## Corrected finding: what `glsr_tmp` is actually for

An earlier revision of this document claimed `glsr_tmp` was created, dropped and
never used, on the strength of a grep over the free plugin's `/plugin` only. That
was wrong, and the error is left here on purpose as the worked example of the
failure mode this review exists to catch.

TRACED, in premium: `glsr_tmp` is the **attachment queue for stage 3**, and the
free plugin's two attachment filters are the seam.

- `Premium/Features/Images/Controllers/ImportController::filterImportRemoteImages()`
  hooks `site-reviews/import/review/attachments` — the filter
  `ImportManager::import()` calls per record — and inserts one row per review that
  carries images: `type = 'attachment'`, `data = maybe_serialize([$reviewId => $images])`.
  Its return value is the count the free plugin adds to `$result['attachments']`.
- `filterAttachRemoteImages()` hooks `site-reviews/import/reviews/attachments` —
  the filter `ImportManager::importAttachments()` calls — and reads those rows back
  with `SELECT data FROM table|tmp WHERE type = 'attachment' LIMIT {$offset}, {$limit}`,
  then attaches the images.

The standalone Review Images addon ships the same code under
`GeminiLabs\SiteReviews\Addon\Images\Controllers` (`dist/site-reviews-images/`);
the only differences are the namespace and the text domain.

So the free plugin owns the table's lifecycle and premium owns its contents. Three
consequences for this spec:

1. **C1 cannot casually add a `UNIQUE hash` column.** The table now holds
   attachment rows whose hash would be empty, and it is a schema the free plugin
   defines but a premium feature writes. Any column change is a migration
   coordinated across two products. The in-memory set is the cheaper route, and
   the burden is on whoever wants the column to show the coordination is worth it.
2. **B must keep the per-record filter calls exactly as they are**, in order and
   with the same arguments, on both the created and the skipped path. The sketch
   in B does; check it again with premium's signature in view.
3. **Its DDL is still what forces `commitsTransaction()`** on the whole Import
   suite. That part of the earlier claim stands.

Two things I noticed while correcting this and have NOT traced. They are questions
for the reviewer, not findings:

- `filterAttachRemoteImages()` paginates with `LIMIT {$offset}, {$limit}` and no
  `ORDER BY`. Whether MySQL can return overlapping or missed rows across stage 3's
  pages is unestablished.
- The free plugin calls `import/review/attachments` on the **skipped** path too,
  with the already-imported review, so re-running an import appears to re-queue
  images for reviews that already have them. Premium registers that filter with
  **three** accepted arguments (`Features/Images/Hooks.php:77`), so it never
  receives the fourth — the boolean the free plugin passes to say whether the
  review was just created — and therefore cannot tell the two paths apart even if
  it wanted to. Whether `Uploader::attachRemoteImages()` is idempotent is untraced.

## Rejected alternatives

- **A guard in `ImportManager` for the zero id** — fixes one caller, leaves REST
  and MultilingualPress paying it.
- **`pLimit(1)` in the JS** — removes the in-run race by giving up the
  concurrency, does nothing about two tabs, treats the symptom.
- **`GET_LOCK` per hash around check-and-create** — serialises only colliding
  rows, but depends on connection behaviour that varies by host, and a leaked lock
  stalls the import.
- **`import-reviews` in `mutexActions()`** — see C2 above; it rejects rather than
  queues.
- **A UNIQUE index on the hash in a plugin-owned table** — the only thing that
  makes the create itself atomic against any concurrency, and it would also remove
  B's O(reviews) scan entirely. Excluded here because it is a migration plus a
  backfill of every existing review, and it moves where the hash lives. It belongs
  in its own piece of work, and a reviewer who thinks B is a workaround for a
  missing index should argue for it.

## Assumptions inventory

Attack these first.

1. A post ID of 0 or less cannot match a row (A).
2. No addon calls `ImportManager::importedReview()`; its signature changed in
   `7be1d1c03`. Asserted by Paul, not verified against addon source.
3. The `get/review` action must keep firing for a zero id (A) — conservative, not
   established.
4. Buffering `LOOKUP_CHUNK` records is memory-safe for any row width (B).
5. The JS is the only client of these endpoints, so `per_page` is normally 50 (B,
   C2).
6. `esc_sql` on md5 output is sufficient for the `IN()` list (B).
7. Within-page dedupe currently works, i.e. today's per-row query catches a twin
   later in the same page. Traced from the loop's sequential order, not measured.
8. Benchmark ratios hold across data distributions and MySQL versions (B).
9. `MIN(p.ID)` — "oldest wins" — is the right rule when several reviews share a
   hash (B).
10. Duplicate rows within one CSV are a real user scenario worth engineering for
    (C1). If they are not, C1 is unnecessary and only C2 matters.
11. That premium's `Features/Images` is the ONLY consumer of `glsr_tmp` and of the
    two attachment filters. Grepped across the premium repo, which found one
    writer and one reader, plus the standalone build of the same file. Other
    addons that are not checked out here were not searched.
12. That no other premium feature hooks the review importer. TRACED, not assumed:
    a grep of the premium repo for `site-reviews/import/` returns the two
    attachment filters (`Features/Images/Hooks.php:76-77`) and one settings-import
    action (`Features/Notifications/Hooks.php:23`), which belongs to the settings
    importer, not this pipeline. Repeat the grep against any addon not checked out
    here.

## Appendix: probe scripts

Each was run inside wp-env against the dev database and cleans up after itself.
Re-run them before trusting any number above.

### A — the wasted hydrate

```php
<?php
require_once '/var/www/html/wp-load.php';
global $wpdb;
$t = microtime(true); $before = $wpdb->num_queries;
for ($i = 0; $i < 50; ++$i) { glsr_get_review(0); }
printf("glsr_get_review(0) x50: %.1f ms, %d queries\n", (microtime(true) - $t) * 1000, $wpdb->num_queries - $before);
// output: glsr_get_review(0) x50: 85.6 ms, 50 queries
```

### B — per-row versus batched, with a realistic meta mix

```php
<?php
require_once '/var/www/html/wp-load.php';
global $wpdb;
$type = glsr()->post_type;
$wpdb->query("START TRANSACTION");
for ($i = 0; $i < 3000; $i += 500) {
    $posts = [];
    for ($j = $i; $j < $i + 500; ++$j) { $posts[] = $wpdb->prepare("(%s,%s,%s,%s,%s,%s,%s)", 'PROBE', 'PROBE-'.$j, 'publish', $type, '2024-01-01 00:00:00', '2024-01-01 00:00:00', ''); }
    $wpdb->query("INSERT INTO {$wpdb->posts} (post_content,post_title,post_status,post_type,post_date,post_date_gmt,post_excerpt) VALUES ".implode(',', $posts));
}
$ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s", 'PROBE-%'));
foreach (array_chunk($ids, 500) as $chunk) {
    $meta = [];
    foreach ($chunk as $id) {
        $meta[] = $wpdb->prepare("(%d,%s,%s)", $id, '_submitted_hash', md5('probe'.$id));
        for ($k = 0; $k < 20; ++$k) { $meta[] = $wpdb->prepare("(%d,%s,%s)", $id, "_filler_{$k}", 'x'); }
    }
    $wpdb->query("INSERT INTO {$wpdb->postmeta} (post_id,meta_key,meta_value) VALUES ".implode(',', $meta));
}
$wpdb->query("COMMIT");
$sql = "SELECT p.ID FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON (pm.post_id = p.ID)
        WHERE 1=1 AND p.post_type = %s AND pm.meta_key = '_submitted_hash' AND pm.meta_value = %s";
foreach ($wpdb->get_results($wpdb->prepare("EXPLAIN {$sql}", $type, md5('nope')), ARRAY_A) as $row) {
    printf("EXPLAIN: table=%s type=%s key=%s rows=%s\n", $row['table'], $row['type'], $row['key'] ?? 'NULL', $row['rows']);
}
$hashes = array_map(fn ($n) => md5('miss'.$n), range(1, 50));
$t = microtime(true);
foreach ($hashes as $h) { $wpdb->get_var($wpdb->prepare($sql, $type, $h)); }
$perRow = (microtime(true) - $t) * 1000;
$in = implode(',', array_map(fn ($h) => "'".esc_sql($h)."'", $hashes));
$t = microtime(true);
$wpdb->get_results($wpdb->prepare("SELECT pm.meta_value, p.ID FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON (pm.post_id = p.ID)
    WHERE p.post_type = %s AND pm.meta_key = '_submitted_hash' AND pm.meta_value IN ({$in})", $type));
$batched = (microtime(true) - $t) * 1000;
printf("page of 50: per-row = %.1f ms | batched = %.1f ms | %.1fx\n", $perRow, $batched, $perRow / max($batched, 0.001));
$wpdb->query("DELETE pm FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE p.post_title LIKE 'PROBE-%'");
$wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_title LIKE 'PROBE-%'");
// output: EXPLAIN p ref type_status_date rows=3000 | pm ref|filter post_id|meta_key rows=1
//         page of 50: per-row = 609.9 ms | batched = 12.4 ms | 49.4x
```

The FIRST version of this probe seeded only `_submitted_hash` rows (5,000 in a
5,196-row postmeta table). It reported `EXPLAIN table=pm type=ALL key=NULL
rows=5196` and 56.0 ms versus 1.5 ms (37x) — a full table scan rather than the
index-driven plan above. Keep both in mind when judging the ratio.

### B — query counts through the real code path

```php
<?php
define('SAVEQUERIES', true);
require_once '/var/www/html/wp-load.php';
use GeminiLabs\SiteReviews\Database\ImportManager;
$im = glsr(ImportManager::class);
$rows = ["date,rating,ip_address"];
for ($i = 1; $i <= 20; ++$i) { $rows[] = "2024-01-{$i},5,127.0.0.1"; }
$rows[] = ",,127.0.0.2"; // submits nothing: empties dropped, ip_address is guarded
file_put_contents($im->tempFilePath(), implode("\n", $rows)."\n");
global $wpdb;
$before = count($wpdb->queries);
$result = $im->import(50, 0);
$dedupe = $meta = 0;
foreach (array_slice($wpdb->queries, $before) as $q) {
    $sql = ltrim($q[0]);
    if (!str_contains($sql, '_submitted_hash') || !str_starts_with(strtoupper($sql), 'SELECT')) continue;
    str_contains($sql, 'INNER JOIN') ? ++$dedupe : ++$meta;
}
printf("imported=%d skipped=%d | dedupe SELECTs=%d, postmeta existence SELECTs=%d\n",
    $result['imported'], $result['skipped'], $dedupe, $meta);
$im->flush();
$im->unlinkTempFile();
$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->posts} WHERE post_type = %s", glsr()->post_type));
// output: imported=20 skipped=1 | dedupe SELECTs=20, postmeta existence SELECTs=20
```

Note the second counter: 20 of those SELECTs are `update_post_meta()` checking
whether the key exists before writing it. They are WordPress's, not this code's,
and no proposal here removes them.
