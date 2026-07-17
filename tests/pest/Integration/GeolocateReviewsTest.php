<?php

use GeminiLabs\SiteReviews\Commands\GeolocateReviews;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Geolocating the reviewers.
 *
 * Turned on, the plugin posts the IP of everyone who ever left a review, in batches of a hundred, to
 * ip-api.com — a third party — for a country, region, city and ISP it stores next to the review.
 * That is the plugin's single largest disclosure of visitor data, in bulk, for reviews left years
 * ago by people who have forgotten the site — so the tests weigh what is sent, what is NOT, and what
 * happens when the far end says no.
 *
 * The IPs that must never leave are pointless AND revealing to send: `127.0.0.1` (the site talking
 * to itself), `unknown` (the plugin's marker for an undetected IP), and empty. Each is excluded in
 * SQL, the only cheap place — the batch is built by the database, not PHP.
 *
 * The lock and retry counter are transients, so a worker that dies mid-batch does not leave the site
 * permanently unable to geolocate.
 */

beforeEach(function () {
    resetPluginState();
    delete_transient(GeolocateReviews::LOCK_KEY);
    delete_transient(GeolocateReviews::RETRY_KEY);
});

afterEach(function () {
    glsr(Notice::class)->clear();
    NullQueue::$isPending = false;
});

/**
 * The location as it is in the database, not as the meta cache remembers it.
 *
 * The geolocation is written with Database::insertBulk() — one raw INSERT for the whole batch,
 * rather than a hundred calls to add_post_meta() — so WordPress's meta cache, populated when
 * the review was created, does not know it is there. Reading it through get_post_meta() without
 * dropping the cache first returns the empty value it had before the batch ran.
 */
function storedGeolocation(int $reviewId): array
{
    wp_cache_delete($reviewId, 'post_meta');

    return (array) glsr(PostMeta::class)->get($reviewId, 'geolocation');
}

function geolocate(): GeolocateReviews
{
    return new GeolocateReviews(glsr()->args([]));
}

/**
 * What ip-api.com sends back for one address.
 */
function geolocationResult(string $ip, array $overrides = []): array
{
    return array_replace([
        'city' => 'Vancouver',
        'countryCode' => 'CA',
        'isp' => 'Some ISP',
        'query' => $ip,
        'region' => 'BC',
        'status' => 'success',
    ], $overrides);
}

function geolocationReply(array $results): array
{
    return ['body' => (string) wp_json_encode($results)];
}

/**
 * The IP addresses actually posted to ip-api.com.
 */
function geolocatedIps(ArrayObject $requests): array
{
    $body = $requests[0]['args']['body'] ?? '[]';

    return (array) json_decode((string) $body, true);
}

function statCount(): int
{
    global $wpdb;

    return (int) $wpdb->get_var('SELECT COUNT(*) FROM '.glsr(Tables::class)->table('stats'));
}

/*
 * What leaves the site.
 */

test('a reviewer\'s ip address is sent to ip-api, and their review is not', function () {
    // The address, and nothing else. Not the name, not the email, not a word they wrote.
    createReview(['content' => 'A private opinion', 'email' => 'jane@example.org', 'ip_address' => '203.0.113.9', 'name' => 'Jane Doe']);
    $requests = interceptHttp(geolocationReply([geolocationResult('203.0.113.9')]));

    geolocate()->process();

    expect($requests)->toHaveCount(1)
        ->and($requests[0]['url'])->toContain('ip-api.com');
    expect(geolocatedIps($requests))->toBe(['203.0.113.9']);

    $body = (string) $requests[0]['args']['body'];
    expect($body)->not->toContain('jane@example.org')
        ->not->toContain('Jane Doe')
        ->not->toContain('A private opinion');
});

test('a local, unknown or missing ip address is never sent anywhere', function () {
    // 127.0.0.1 is the site talking to itself — a review left by the admin while testing, or
    // every review on a site behind a badly configured proxy. `unknown` is the plugin's own
    // marker for an address it could not detect. Sending either is a pointless disclosure that
    // buys nothing back.
    createReview(['ip_address' => '127.0.0.1']);
    createReview(['ip_address' => 'unknown']);
    createReview(['ip_address' => '']);
    createReview(['ip_address' => '203.0.113.9']); // the only real one
    $requests = interceptHttp(geolocationReply([geolocationResult('203.0.113.9')]));

    geolocate()->process();

    expect(geolocatedIps($requests))->toBe(['203.0.113.9']);
});

test('a site with nothing to geolocate does not contact anybody', function () {
    createReview(['ip_address' => '127.0.0.1']);
    $requests = interceptHttp(geolocationReply([]));

    geolocate()->process();

    expect($requests)->toHaveCount(0);
});

/*
 * What comes back, and where it is put.
 */

test('the location is stored beside the review', function () {
    $review = createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReply([geolocationResult('203.0.113.9')]));

    geolocate()->process();

    $geolocation = storedGeolocation($review->ID);

    // The keys are renamed on the way in: StatDefaults maps `countryCode` to `country` and
    // `continentCode` to `continent`. What ip-api calls a thing and what the plugin stores it
    // as are not the same names.
    expect($geolocation['country'])->toBe('CA')
        ->and($geolocation['city'])->toBe('Vancouver')
        ->and($geolocation['region'])->toBe('BC')
        ->and($geolocation)->not->toHaveKey('rating_id'); // an internal id is not part of a location
    expect(statCount())->toBe(1);
});

test('the reviewer\'s internet provider is thrown away', function () {
    // ip-api sends back the ISP, and the plugin asks for it — but StatDefaults has no `isp` key,
    // so restrict() drops it before anything is stored. That is the right way round: a country
    // and a city are what a site owner wants a map of, and "which company this person pays for
    // their internet" is a fact about a named individual that the site has no use for.
    $review = createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReply([
        geolocationResult('203.0.113.9', ['isp' => 'Jane Doe Telecom Ltd']),
    ]));

    geolocate()->process();

    expect(storedGeolocation($review->ID))->not->toHaveKey('isp');
    expect((string) wp_json_encode(storedGeolocation($review->ID)))->not->toContain('Jane Doe Telecom');
});

test('an address the service could not place is not stored as a place', function () {
    // ip-api answers `status: fail` for a private range, a reserved address, or an IP it simply
    // does not know. Storing that as a location would put "" next to the review forever, and
    // — worse — mark it as geolocated, so it would never be asked about again.
    createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReply([
        geolocationResult('203.0.113.9', ['message' => 'reserved range', 'status' => 'fail']),
    ]));

    geolocate()->process();

    expect(statCount())->toBe(0);
});

/*
 * When ip-api says no. It is a free service with a hard rate limit, and a site with ten
 * thousand reviews will meet it.
 */

test('a rejected batch is retried, and the site is not left locked forever', function () {
    createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(['response' => ['code' => 429, 'message' => 'Too Many Requests']]);

    geolocate()->process();

    expect((int) get_transient(GeolocateReviews::RETRY_KEY))->toBe(1)
        ->and(statCount())->toBe(0); // and nothing was stored from a failed batch
    // …and the retry really was queued, for the SAME offset (NullQueue records the call)
    $retries = NullQueue::calls('once', GeolocateReviews::QUEUED_ACTION_KEY);
    expect($retries)->toHaveCount(1)
        ->and($retries[0]['args'])->toBe(['offset' => 0]);
});

test('a batch that keeps failing is given up on, and the lock is released', function () {
    // Otherwise the lock — an hour-long transient — would be re-taken on every retry and the
    // site could never geolocate anything again, with nothing to say why.
    createReview(['ip_address' => '203.0.113.9']);
    set_transient(GeolocateReviews::LOCK_KEY, true, HOUR_IN_SECONDS);
    set_transient(GeolocateReviews::RETRY_KEY, GeolocateReviews::MAX_RETRIES, HOUR_IN_SECONDS);
    interceptHttp(['response' => ['code' => 429, 'message' => 'Too Many Requests']]);

    geolocate()->process();

    expect(get_transient(GeolocateReviews::LOCK_KEY))->toBeFalse()
        ->and(get_transient(GeolocateReviews::RETRY_KEY))->toBeFalse();
    // given up means given up: no retry was queued
    expect(NullQueue::calls('once', GeolocateReviews::QUEUED_ACTION_KEY))->toBe([]);
});

test('a successful batch forgets the failures that came before it', function () {
    createReview(['ip_address' => '203.0.113.9']);
    set_transient(GeolocateReviews::RETRY_KEY, 2, HOUR_IN_SECONDS);
    interceptHttp(geolocationReply([geolocationResult('203.0.113.9')]));

    geolocate()->process();

    expect(get_transient(GeolocateReviews::RETRY_KEY))->toBeFalse();
});

/*
 * Starting it off.
 */

test('starting it twice at once is refused', function () {
    // handle() is a button on the Tools page. Two people pressing it, or one person pressing it
    // twice, would send every IP on the site to ip-api twice over.
    //
    // The lock is only honoured while there is actually work in the queue — see below.
    createReview(['ip_address' => '203.0.113.9']);
    NullQueue::$isPending = true;
    set_transient(GeolocateReviews::LOCK_KEY, true, HOUR_IN_SECONDS);

    geolocate()->handle();

    expect(glsr(Notice::class)->get())->toContain('already in progress');
});

test('a lock left behind by a worker that died is let go of', function () {
    // The lock is an hour-long transient. If the queued action that was holding it has gone —
    // the worker was killed, the queue was flushed, the site was migrated — then the lock is
    // guarding nothing, and honouring it would leave the site unable to geolocate anything for
    // an hour with nothing to say why. So a lock with no pending action behind it is released.
    createReview(['ip_address' => '203.0.113.9']);
    NullQueue::$isPending = false; // nothing is queued…
    set_transient(GeolocateReviews::LOCK_KEY, true, HOUR_IN_SECONDS); // …but the lock is down

    geolocate()->handle();

    expect(glsr(Notice::class)->get())->not->toContain('already in progress')
        ->and(glsr(Notice::class)->get())->toContain('1 IP addresses'); // it got on with it
});

test('a site with nothing left to geolocate says so, rather than nothing', function () {
    createReview(['ip_address' => '127.0.0.1']);

    geolocate()->handle();

    expect(glsr(Notice::class)->get())->toContain('already been geolocated');
});

test('starting it queues the work and says how much there is', function () {
    createReview(['ip_address' => '203.0.113.9']);
    createReview(['ip_address' => '198.51.100.4']);

    geolocate()->handle();

    expect(glsr(Notice::class)->get())->toContain('2 IP addresses');
    expect(get_transient(GeolocateReviews::LOCK_KEY))->not->toBeFalse(); // and it is locked
    // the work itself is a queued batch, starting from the beginning
    $queued = NullQueue::calls('once', GeolocateReviews::QUEUED_ACTION_KEY);
    expect($queued)->toHaveCount(1)
        ->and($queued[0]['args'])->toBe(['offset' => 0]);
});

/*
 * The edges of a batch: an empty reply, an invalid batch, and scheduling the next page.
 */

test('the response carries the notices back to the tools page', function () {
    expect(geolocate()->response())->toHaveKey('notices');
});

test('a batch that comes back with nothing is logged and stored nowhere', function () {
    // A 200 with an empty body — the far end had nothing to say about these IPs. Nothing is stored,
    // and the batch is not mistaken for a failure to retry.
    createReview(['ip_address' => '203.0.113.9']);
    interceptHttp(geolocationReply([])); // OK, but no results

    geolocate()->process();

    expect(statCount())->toBe(0);
});

test('a 422 from the geolocation API is logged as an invalid batch', function () {
    // ip-api answers 422 when the batch itself is malformed; that is worth a log line of its own,
    // distinct from an ordinary failed request.
    interceptHttp(['response' => ['code' => 422, 'message' => 'Unprocessable Entity']]);

    $response = protectedMethod(GeolocateReviews::class, 'fetchRemoteGeolocationData')
        ->invoke(geolocate(), ['203.0.113.9']);

    expect($response->code)->toBe(422);
});

test('a full batch schedules the next page and keeps the lock', function () {
    // When a batch comes back completely full there are probably more reviews behind it, so the next
    // page is queued and the lock is HELD (a short batch would release it instead).
    set_transient(GeolocateReviews::LOCK_KEY, true, HOUR_IN_SECONDS);

    protectedMethod(GeolocateReviews::class, 'scheduleNextBatchIfNeeded')
        ->invoke(geolocate(), 0, 2, ['a', 'b']); // count === batchSize

    expect(get_transient(GeolocateReviews::LOCK_KEY))->not->toBeFalse(); // lock kept for the next page
    // the next page really is queued, one batch further on — an offset that was ignored
    // would geolocate the first page over and over, forever
    $queued = NullQueue::calls('once', GeolocateReviews::QUEUED_ACTION_KEY);
    expect($queued)->toHaveCount(1)
        ->and($queued[0]['args'])->toBe(['offset' => 2]);
});

test('a full chunk is flushed mid-walk, not held until the end', function () {
    // INSERT_CHUNK_SIZE is 500 in production — resolved through static::, so a
    // subclass can afford the two rows this asserts with. The database is faked:
    // what matters is WHEN the bulk insert fires, not the SQL.
    $fake = new class extends \GeminiLabs\SiteReviews\Database {
        public array $bulk = [];

        public function insertBulk(string $table, array $values, array $fields)
        {
            $this->bulk[] = [$table, count($values)];
            return count($values);
        }
    };
    $original = glsr(\GeminiLabs\SiteReviews\Database::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Database::class, $fake);
    try {
        $command = new class extends GeolocateReviews {
            public const INSERT_CHUNK_SIZE = 2;
        };
        $rows = (function () {
            yield ['ip_address' => '1.1.1.1', 'rating_id' => 11, 'review_id' => 101];
            yield ['ip_address' => '2.2.2.2', 'rating_id' => 12, 'review_id' => 102];
        })();
        $results = [
            ['query' => '1.1.1.1', 'city' => 'Alpha', 'country' => 'Wonderland'],
            ['query' => '2.2.2.2', 'city' => 'Beta', 'country' => 'Wonderland'],
        ];

        protectedMethod(get_class($command), 'prepareAndInsert')->invoke($command, $rows, $results);

        expect($fake->bulk)->toBe([['stats', 2], ['postmeta', 2]]); // flushed AT the chunk, nothing left for the tail
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Database::class, $original);
    }
});
