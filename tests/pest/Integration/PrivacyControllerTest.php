<?php

use GeminiLabs\SiteReviews\Controllers\PrivacyController;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\ReviewManager;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The GDPR export and erasure.
 *
 * WordPress has a built-in "export/erase this person's personal data" tool, and every plugin holding
 * personal data must answer it. Site Reviews holds a lot: a review carries a name, email, IP, and
 * often a geolocation of that IP — none in wp_posts, so core cannot find it without being told.
 *
 * The stakes are asymmetric: missing something on EXPORT is a failure to comply; missing it on
 * ERASURE is that AND a lie, since the admin screen says "removed" either way. So most of this is
 * about what is left behind. The eraser has two modes, chosen by a filter:
 *
 *   erase-all (default)   the reviews are DELETED, permanently.
 *   erase-all = false     the reviews stay, anonymised — for a site that would lose its star rating
 *                         if one person asked to be forgotten, who is still forgotten.
 *
 * The controller reads that filter IN ITS CONSTRUCTOR, so it is constructed after the filter is
 * added — as WordPress does it, building the erasers list on demand.
 */

beforeEach(function () {
    resetPluginState();
});

afterEach(function () {
    set_current_screen('front');
});

/**
 * The review as it is in the database right now, not as the cache remembers it.
 */
function storedReview(int $reviewId): \GeminiLabs\SiteReviews\Review
{
    return glsr(ReviewManager::class)->get($reviewId, $bypassCache = true);
}

/**
 * A column of the ratings row, straight out of the database.
 *
 * The Review MODEL is not the place to ask whether personal data has been erased: it
 * renames `name` to `author` and substitutes "Anonymous" for an empty one, so a review with
 * nothing held about its author still answers "Anonymous" when asked for a name. That is
 * right for display and useless for this. The row is the truth.
 */
function storedRating(int $reviewId, string $column): string
{
    global $wpdb;
    $table = glsr(\GeminiLabs\SiteReviews\Database\Tables::class)->table('ratings');

    return (string) $wpdb->get_var(
        $wpdb->prepare("SELECT `{$column}` FROM {$table} WHERE review_id = %d", $reviewId)
    );
}

function eraserFor(string $email, bool $eraseAll = true): array
{
    add_filter('site-reviews/personal-data/erase-all', fn () => $eraseAll);

    return (new PrivacyController())->erasePersonalDataCallback($email);
}

function exportFor(string $email): array
{
    return (new PrivacyController())->exportPersonalDataCallback($email);
}

/**
 * The exported fields of the first review, as name => value.
 */
function exportedFields(array $export, int $index = 0): array
{
    $data = $export['data'][$index]['data'] ?? [];

    return array_combine(
        array_column($data, 'name'),
        array_column($data, 'value')
    );
}

/*
 * The export.
 */

test('everything the plugin holds about a person comes back', function () {
    $review = createReview([
        'content' => 'The room was lovely.',
        'email' => 'jane@example.org',
        'ip_address' => '203.0.113.9',
        'name' => 'Jane Doe',
        'title' => 'A lovely stay',
    ]);

    $export = exportFor('jane@example.org');
    $fields = exportedFields($export);

    expect($fields)->toBe([
        'Review Title' => 'A lovely stay',
        'Review Content' => 'The room was lovely.',
        'Name' => 'Jane Doe',
        'Email' => 'jane@example.org',
        'IP Address' => '203.0.113.9',
    ]);
    expect($export['data'][0]['item_id'])->toBe("site-review-{$review->ID}")
        ->and($export['done'])->toBeTrue();
});

test('only that person\'s reviews come back', function () {
    createReview(['email' => 'jane@example.org', 'title' => 'Jane\'s review']);
    createReview(['email' => 'someone.else@example.org', 'title' => 'Somebody else\'s review']);

    $export = exportFor('jane@example.org');

    expect($export['data'])->toHaveCount(1)
        ->and(exportedFields($export)['Review Title'])->toBe('Jane\'s review');
});

test('a field the person never filled in is not exported as blank', function () {
    // An export full of empty rows is worse than useless: it suggests the plugin holds
    // things about them that it does not. `terms` is the field to watch, because its absence
    // and its presence mean different things — and a review left without accepting any terms
    // must not come back saying that terms were accepted.
    createReview(['email' => 'jane@example.org']);

    expect(exportedFields(exportFor('jane@example.org')))->not->toHaveKey('Terms Accepted');
});

test('a name that was never given is exported as Anonymous, and is not a name', function () {
    // ReviewDefaults renames the `name` column to `author`, and Review::__construct() falls
    // back to "Anonymous" when it is empty. So an export can say `Name: Anonymous` while the
    // plugin holds no name at all — which matters, because that is also what an ERASED
    // review says (below). The stored value is the truth; the model shows a placeholder.
    $review = createReview(['email' => 'jane@example.org', 'name' => '']);

    expect(storedRating($review->ID, 'name'))->toBe('') // nothing is held
        ->and(exportedFields(exportFor('jane@example.org'))['Name'])->toBe('Anonymous');
});

test('accepting the terms is exported as WHEN they were accepted', function () {
    // "Terms Accepted: 1" is not a record of consent. The date is.
    $review = createReview(['email' => 'jane@example.org', 'terms' => true]);

    expect(exportedFields(exportFor('jane@example.org'))['Terms Accepted'])
        ->toBe(storedReview($review->ID)->date);
});

test('nobody\'s data comes back for an email nobody used', function () {
    createReview(['email' => 'jane@example.org']);

    $export = exportFor('nobody@example.org');

    expect($export['data'])->toBe([])
        ->and($export['done'])->toBeTrue();
});

/*
 * The erasure. What is left afterwards is the whole point.
 */

test('by default the reviews are deleted, and everything in them with them', function () {
    $review = createReview([
        'email' => 'jane@example.org',
        'ip_address' => '203.0.113.9',
        'name' => 'Jane Doe',
    ]);
    glsr(PostMeta::class)->set($review->ID, 'geolocation', ['country' => 'CA']);

    $result = eraserFor('jane@example.org');

    expect($result['items_removed'])->toBeTrue()
        ->and($result['items_retained'])->toBeFalse()
        ->and($result['done'])->toBeTrue();

    // Gone from the posts table, and — because the ratings table cascades from it — gone
    // from the plugin's own tables too.
    expect(get_post($review->ID))->toBeNull()
        ->and(storedReview($review->ID)->isValid())->toBeFalse();
});

test('somebody else\'s review is not deleted along with it', function () {
    $hers = createReview(['email' => 'jane@example.org']);
    $his = createReview(['email' => 'someone.else@example.org']);

    eraserFor('jane@example.org');

    expect(get_post($hers->ID))->toBeNull()
        ->and(get_post($his->ID))->not->toBeNull();
});

test('a site that keeps its reviews forgets the person instead', function () {
    // THE ONE THAT MATTERS. The review survives — so the site keeps its star rating — and
    // the person is still gone from it. If any of these assertions ever fails, the plugin
    // is telling somebody they have been forgotten when they have not.
    $review = createReview([
        'content' => 'The room was lovely.',
        'email' => 'jane@example.org',
        'ip_address' => '203.0.113.9',
        'name' => 'Jane Doe',
        'rating' => 5,
    ]);
    glsr(PostMeta::class)->set($review->ID, 'geolocation', ['country' => 'CA']);
    glsr(PostMeta::class)->set($review->ID, 'submitted', ['form' => 'data']);
    glsr(PostMeta::class)->set($review->ID, 'submitted_hash', 'a-hash');

    $result = eraserFor('jane@example.org', $eraseAll = false);

    $erased = storedReview($review->ID);
    expect($erased->isValid())->toBeTrue()      // the review is still there
        ->and($erased->rating)->toBe(5)         // and so is the rating it contributes
        ->and($erased->content)->toBe('The room was lovely.');

    // and she is not. Asked of the DATABASE, because the model would answer "Anonymous" for
    // the name whether it had been erased or never given — and the difference between those
    // two is the entire question being asked here.
    expect(storedRating($review->ID, 'email'))->toBe('')
        ->and(storedRating($review->ID, 'name'))->toBe('')
        ->and(storedRating($review->ID, 'ip_address'))->toBe('');
    expect($erased->name)->not->toBe('Jane Doe')
        ->and($erased->email)->toBe('')
        ->and($erased->ip_address)->toBe('');

    // nor is the trail of her that was kept beside it: where she was, what she submitted,
    // and the hash that could be matched back against a submission
    expect(glsr(PostMeta::class)->get($review->ID, 'geolocation'))->toBeEmpty()
        ->and(glsr(PostMeta::class)->get($review->ID, 'submitted'))->toBeEmpty()
        ->and(glsr(PostMeta::class)->get($review->ID, 'submitted_hash'))->toBeEmpty();

    expect($result['items_removed'])->toBeTrue()
        ->and($result['items_retained'])->toBeTrue()
        ->and($result['messages'][0])->toContain('the reviews themselves were not removed');
});

test('an anonymised review can no longer be found by the email that was on it', function () {
    // The proof that the anonymisation is real rather than cosmetic: ask the plugin the same
    // question again and it has nothing to give.
    createReview(['email' => 'jane@example.org']);

    eraserFor('jane@example.org', $eraseAll = false);

    expect(exportFor('jane@example.org')['data'])->toBe([]);
});

/*
 * The registration. None of the above runs unless WordPress is told the plugin has a
 * horse in this race.
 */

test('the plugin offers wordpress an exporter and an eraser', function () {
    $controller = new PrivacyController();

    $exporters = $controller->filterPersonalDataExporters([]);
    $erasers = $controller->filterPersonalDataErasers([]);

    expect($exporters['site-reviews']['exporter_friendly_name'])->toBe('Site Reviews')
        ->and($exporters['site-reviews']['callback'])->toBeCallable()
        ->and($erasers['site-reviews']['eraser_friendly_name'])->toBe('Site Reviews')
        ->and($erasers['site-reviews']['callback'])->toBeCallable();
});

test('the plugin tells the site what to put in its privacy policy', function () {
    // Suggested policy text, which WordPress shows in the Privacy Policy Guide. The plugin
    // collects an email and an IP address, and the site owner has to disclose that.
    //
    // wp_add_privacy_policy_content() (wp-admin/includes/plugin.php) refuses TWICE over, and
    // both refusals have to be answered or it silently returns and adds nothing:
    //
    //   if (!is_admin())                                   -> _doing_it_wrong(), return
    //   elseif (!doing_action('admin_init')
    //           && !did_action('admin_init'))              -> _doing_it_wrong(), return
    //
    // Because the suggested text is only ever read by the Privacy Policy Guide, which is an
    // admin screen, and a plugin that offers it earlier has offered it to nothing.
    // privacyPolicyContent() is hooked to admin_init for exactly that reason.
    //
    // So: an admin screen (is_admin() reads $current_screen->in_admin() when one is set), and
    // a claim to be inside admin_init — rather than FIRING admin_init, which would run every
    // other callback on it: the installer, the migration scheduler, the addon activations.
    // Pest.php snapshots $wp_actions per test, so the claim does not outlive it.
    set_current_screen('privacy');
    $GLOBALS['wp_actions']['admin_init'] = 1;

    // WP_Privacy_Policy_Content::add() drops anything with an empty name or an empty text, so
    // both halves are worth asserting apart: that the plugin HAS policy text to suggest, and
    // that WordPress took it.
    $content = glsr()->build('partials/privacy-policy');
    expect($content)->toContain('IP address')  // the thing that most needs disclosing
        ->and(glsr()->name)->toBe('Site Reviews');

    (new PrivacyController())->privacyPolicyContent();

    $suggested = WP_Privacy_Policy_Content::get_suggested_policy_text();
    $ours = array_filter($suggested, fn ($item) => 'Site Reviews' === ($item['plugin_name'] ?? ''));

    expect($ours)->not->toBeEmpty();
});
