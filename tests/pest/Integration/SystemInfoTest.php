<?php

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\SystemInfo;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The system info, which is what people paste into a support forum.
 *
 * That is why it is worth testing. It is a plain-text dump of the site's settings, which hold the
 * webhook URLs anyone can post to a private Slack channel with, and the licence keys someone paid
 * for. purgeSensitiveData() stands between those and a public forum post, and works by NAME:
 * anything under settings.licenses, anything ending in api_key, and anything the settings config
 * declares `secret`. A new secret setting not declared as one gets published, and no other test
 * would notice — which is why the test below loops over the config rather than listing today's
 * secrets.
 *
 * (The settings.licenses.* branch cannot be exercised: those are registered by premium addons, none
 * installed.)
 *
 * Nothing here reaches the network — blockHttpRequests() sees to that, so the "remote post" check
 * reports failure and the host reads as localhost, which makes the report deterministic and
 * assertable.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    // Put WordPress's half of the report back where the plugin expects to find it.
    set_transient(glsr()->prefix.'system_info', wordpressDebugData(), HOUR_IN_SECONDS);
});

/**
 * WordPress's own Site Health data, gathered ONCE for the whole file.
 *
 * The plugin keeps this in a 12-hour transient because gathering it is expensive — it
 * walks every plugin, theme and table on the site, and (via Site Health's media section)
 * shells out to `gs --version` to find the Ghostscript version, which prints
 * "sh: gs: not found" in a container that has no Ghostscript.
 *
 * A transient is an option, and the per-test transaction rolls options back, so each test
 * in this file was rebuilding it from scratch: eight times the cost, and eight complaints
 * from a shell. Gathering it once per process and priming the transient with it is a
 * faithful stand-in for the cache the plugin is actually relying on in production.
 */
function wordpressDebugData(): array
{
    static $data;

    return $data ??= glsr(Cache::class)->getSystemInfo();
}

function systemInfo(): string
{
    return (string) glsr(SystemInfo::class);
}

test('the report has a section for everything somebody is going to be asked about', function () {
    // The sections are dispatched by name (Helper::buildMethodName), so a method that has
    // been renamed does not error — the section silently stops being reported, and the
    // first anybody knows about it is a support thread with a hole in it.
    $info = systemInfo();

    foreach ([
        'Plugin', 'Reviews', 'Action Scheduler', 'Database', 'Server', 'WordPress',
        'Active Plugins', 'Plugin Settings',
    ] as $section) {
        expect($info)->toContain('['.strtoupper($section).']');
    }
});

test('the report says which version of the plugin is being asked about', function () {
    expect(systemInfo())->toContain(glsr()->version);
});

/*
 * The part that matters.
 */

test('every setting the config calls a secret is masked, and none of them are missed', function () {
    // Str::mask() keeps the last eight characters on purpose — enough for somebody to
    // recognise their own key in a support thread, not enough for anybody else to use it.
    // So the assertion is that the WHOLE value never appears.
    $secrets = array_filter(glsr()->settings(),
        fn ($field, $key) => str_starts_with($key, 'settings.licenses.')
            || str_ends_with($key, 'api_key')
            || 'secret' === ($field['type'] ?? ''),
        ARRAY_FILTER_USE_BOTH
    );

    expect($secrets)->not->toBeEmpty(); // otherwise this test asserts nothing at all

    $values = [];
    foreach (array_keys($secrets) as $i => $key) {
        $values[$key] = "supersecretvalue000{$i}";
        glsr(OptionManager::class)->set($key, $values[$key]);
    }

    $info = systemInfo();

    foreach ($values as $key => $value) {
        expect($info)->not->toContain($value);
    }
});

test('a webhook url is not published in a support forum', function () {
    // Named out loud as well as covered by the loop above, because these two are the ones
    // that would actually hurt: the URL IS the credential. Anybody holding it can post
    // into the channel.
    glsr(OptionManager::class)->set('settings.general.notification_slack', 'https://hooks.slack.com/services/T00/B00/abcdefghijklmnop');
    glsr(OptionManager::class)->set('settings.general.notification_discord', 'https://discord.com/api/webhooks/123/qrstuvwxyz012345');

    $info = systemInfo();

    expect($info)->toContain('general.notification_slack') // the setting is still reported
        ->not->toContain('https://hooks.slack.com/services/T00/B00/abcdefghijklmnop')
        ->not->toContain('https://discord.com/api/webhooks/123/qrstuvwxyz012345');
});

test('a setting that is not a secret is reported as it is', function () {
    // The mask is not applied to everything. The point of the file is to say how the site
    // is set up, and a masked report is no use to anybody.
    glsr(OptionManager::class)->set('settings.general.notification_email', 'support@example.org');

    expect(systemInfo())->toContain('support@example.org');
});

/*
 * The counts, which are the first thing anybody looks at.
 */

test('a site with no reviews says so, rather than reporting nothing', function () {
    // An empty line here reads as a bug in the report. "No reviews" is an answer.
    expect(systemInfo())->toContain('Type: local')
        ->toContain('No reviews');
});

test('the reviews are counted, by status and by rating', function () {
    createReview(['rating' => 5]);
    createReview(['rating' => 5]);
    createReview(['rating' => 1, 'is_approved' => false]);

    $info = systemInfo();

    expect($info)->toContain('publish: 2')
        ->toContain('pending: 1')
        ->toContain('Type: local');
});

/*
 * And the shape of it.
 */

test('the report is laid out to be read in a forum post', function () {
    // It is pasted into a code block, so it is padded with dots rather than tabbed —
    // a tab is whatever width the forum feels like, and the columns would not line up.
    expect(systemInfo())->toMatch('/^Version\.+ : /m');
});

/*
 * The corners: fabricated queue counts, addons, sqlite, and a hosting lookup.
 */

test('the action counts read differently for none, one, and many', function () {
    $fakeQueue = new class extends \GeminiLabs\SiteReviews\Modules\Queue {
        public function actionCounts(): array
        {
            return [
                'complete' => ['count' => 5, 'latest' => '2026-02-01 00:00:00', 'oldest' => '2025-01-01 00:00:00'],
                'pending' => ['count' => 1, 'latest' => '2026-03-01 00:00:00', 'oldest' => '2026-03-01 00:00:00'],
            ];
        }
    };
    $original = glsr(\GeminiLabs\SiteReviews\Modules\Queue::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Modules\Queue::class, $fakeQueue);
    try {
        $section = glsr(SystemInfo::class)->sectionActionScheduler();

        expect($section['Actions (complete)'])->toBe('5 (latest: 2026-02-01 00:00:00, oldest: 2025-01-01 00:00:00)')
            ->and($section['Actions (pending)'])->toBe('1 (latest: 2026-03-01 00:00:00)')
            ->and($section['Actions (failed)'])->toBe(0);
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Modules\Queue::class, $original);
    }
});

test('an installed addon reports its name and version', function () {
    glsr()->alias('glsr-fake-addon', (object) ['name' => 'Fake Addon', 'version' => '1.2.3']);
    glsr()->store('addons', ['glsr-fake-addon' => '1.2.3']);
    try {
        expect(glsr(SystemInfo::class)->sectionAddons())->toBe(['Fake Addon' => '1.2.3']);
    } finally {
        glsr()->store('addons', []); // in-memory on the singleton; it does not roll back
    }
});

test('on sqlite the database section is the engine and its version, nothing else', function () {
    $fakeTables = new class extends \GeminiLabs\SiteReviews\Database\Tables {
        public function isSqlite(): bool
        {
            return true;
        }
    };
    $original = glsr(\GeminiLabs\SiteReviews\Database\Tables::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Database\Tables::class, $fakeTables);
    try {
        expect(array_keys(glsr(SystemInfo::class)->sectionDatabase()))
            ->toBe(['Database Engine', 'Database Version']);
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Database\Tables::class, $original);
    }
});

test('the string ids are left out of the settings section', function () {
    // Custom translations are stored with generated ids that mean nothing in a
    // support thread; the strings themselves are still reported.
    glsr(OptionManager::class)->set('settings.strings', [['id' => 'the-generated-id', 's1' => 'Custom text']]);

    $settings = glsr(SystemInfo::class)->sectionSettings();

    expect($settings)->not->toHaveKey('strings.0.id')
        ->and($settings)->toHaveKey('strings.0.s1');
});

test('a hosted site names its host, and an unreachable lookup admits it does not know', function () {
    // The report is deterministic locally because the suite blocks HTTP; a real
    // site is not local, so stand down the local check and intercept the lookup.
    add_filter('site-reviews/is-local-server', '__return_false');
    // Answer ONLY the geolocation lookup: the same section also calls
    // Helper::serverIp(), which must keep hitting the suite's HTTP block.
    add_filter('pre_http_request', function ($pre, $args, $url) {
        if (false === strpos((string) $url, 'ip-api.com')) {
            return $pre;
        }
        return [
            'body' => (string) wp_json_encode(['status' => 'success', 'isp' => 'Example ISP', 'query' => '93.184.216.34']),
            'cookies' => [],
            'filename' => null,
            'headers' => [],
            'http_response' => null,
            'response' => ['code' => 200, 'message' => 'OK'],
        ];
    }, 10, 3);

    $server = glsr(SystemInfo::class)->sectionServer();

    expect($server['Hosting Provider'])->toBe('Example ISP (93.184.216.34)');
});

test('and with no lookup to be had, the host is unknown', function () {
    // With nothing intercepting, the suite's HTTP block answers with a WP_Error.
    add_filter('site-reviews/is-local-server', '__return_false');
    delete_transient(\GeminiLabs\SiteReviews\Geolocation::RATE_LIMIT_KEY);

    expect(glsr(SystemInfo::class)->sectionServer()['Hosting Provider'])->toBe('unknown');
});

test('ratings that did not import as arrays are reported as an error, not a crash', function () {
    $fakeQuery = new class extends \GeminiLabs\SiteReviews\Database\Query {
        public function ratings(array $args = []): array
        {
            return ['local' => 'not-an-array']; // the shape bad imports produce
        }
    };
    $original = glsr(\GeminiLabs\SiteReviews\Database\Query::class);
    glsr()->alias(\GeminiLabs\SiteReviews\Database\Query::class, $fakeQuery);
    try {
        $section = glsr(SystemInfo::class)->sectionReviews();

        expect($section['Type: local'] ?? 'No reviews')->toBe('No reviews');
    } finally {
        glsr()->alias(\GeminiLabs\SiteReviews\Database\Query::class, $original);
    }
});

test('a server with ini_get disabled says so instead of guessing', function () {
    // Hardened hosts disable ini_get(); the armed function_exists shadow reproduces that,
    // and the report prints the admission rather than an empty value support would misread.
    \GeminiLabs\SiteReviews\Tests\armFailingFunction('function_exists');
    try {
        $value = GeminiLabs\SiteReviews\Tests\protectedMethod(
            GeminiLabs\SiteReviews\Modules\SystemInfo::class, 'ini'
        )->invoke(glsr(GeminiLabs\SiteReviews\Modules\SystemInfo::class), 'display_errors');
    } finally {
        \GeminiLabs\SiteReviews\Tests\disarmFailingFunctions();
    }
    expect($value)->toBe('ini_get() is disabled.');
});
