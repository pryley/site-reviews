<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\UpdateLock;
use GeminiLabs\SiteReviews\Notices\AbstractNotice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\restRequest;
use function GeminiLabs\SiteReviews\Tests\swapInstance;

/*
 * The plugin/ routes the Tools page calls (RestPluginController), driven through rest_do_request().
 *
 * REST runs outside wp-admin, where is_admin() is false and glsr()->hasPermission() passes for
 * everyone, so each route's permission callback checks the capability itself and refuses with a
 * glsr_ code, which the script keeps as the answer instead of trying admin-ajax.
 */

const PLUGIN_NS = '/site-reviews/v1/plugin';

beforeEach(function () {
    resetPluginState();
    $GLOBALS['wp_rest_server'] = new WP_REST_Server();
    do_action('rest_api_init', $GLOBALS['wp_rest_server']);
});

afterEach(function () {
    unset($GLOBALS['wp_rest_server']);
});

test('registers the plugin routes', function () {
    $routes = $GLOBALS['wp_rest_server']->get_routes();
    foreach ([
        'console', 'console/level', 'ip-address', 'ip-address/proxy', 'migrations', 'permissions',
        'rollback', 'system-info', 'tables/(?P<table>[a-zA-Z0-9_]+)/engine',
    ] as $route) {
        expect($routes)->toHaveKey(PLUGIN_NS.'/'.$route);
    }
});

test('every plugin route refuses an author with a glsr_ code', function () {
    wp_set_current_user(createUser(['role' => 'author']));
    foreach ([
        ['DELETE', '/console'],
        ['GET', '/console'],
        ['GET', '/ip-address'],
        ['GET', '/system-info'],
        ['POST', '/migrations'],
        ['POST', '/rollback', ['version' => '8.0.0']],
        ['PUT', '/console/level', ['level' => 1]],
        ['PUT', '/ip-address/proxy'],
        ['PUT', '/permissions'],
        ['PUT', '/tables/glsr_ratings/engine'],
    ] as $call) {
        [$method, $route, $params] = $call + [2 => []];
        $response = restRequest($method, PLUGIN_NS.$route, $params);
        expect($response->get_status())->toBe(403, $route);
        expect($response->get_data()['code'])->toBe('glsr_forbidden', $route);
    }
});

test('the console is read, its level is changed and it is cleared', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr_log()->error('something went wrong');

    $fetched = restRequest('GET', PLUGIN_NS.'/console');
    expect($fetched->get_status())->toBe(200);
    expect($fetched->get_data()['console'])->toContain('something went wrong');
    expect($fetched->get_data()['notices'])->toContain('Console reloaded');

    $level = restRequest('PUT', PLUGIN_NS.'/console/level', ['level' => Console::NOTICE]);
    expect($level->get_status())->toBe(200);
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe(Console::NOTICE);

    $cleared = restRequest('DELETE', PLUGIN_NS.'/console');
    expect($cleared->get_status())->toBe(200);
    expect($cleared->get_data()['console'])->toContain('Console is empty');
    expect(glsr(Console::class)->get())->toContain('Console is empty');
});

test('a console level that does not exist is refused by the route and by the command', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $before = (int) get_option(Console::LOG_LEVEL_KEY);

    expect(restRequest('PUT', PLUGIN_NS.'/console/level', ['level' => 'loud'])->get_status())->toBe(400); // not an integer
    $refused = restRequest('PUT', PLUGIN_NS.'/console/level', ['level' => 99]);
    expect($refused->get_status())->toBe(400);
    expect($refused->get_data()['notices'])->toContain('could not be changed');
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe($before);
});

test('the IP address is detected and the proxy is configured', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));

    $detected = restRequest('GET', PLUGIN_NS.'/ip-address');
    expect($detected->get_status())->toBe(200);
    expect($detected->get_data())->toHaveKey('notices');

    $configured = restRequest('PUT', PLUGIN_NS.'/ip-address/proxy', [
        'proxy_http_header' => 'HTTP_X_FORWARDED_FOR',
        'trusted_proxies' => "10.0.0.1\n10.0.0.2",
    ]);
    expect($configured->get_status())->toBe(200);
    expect($configured->get_data()['notices'])->not->toBeEmpty();
});

test('the migrations run', function () {
    // The pending-migrations path, which bootstrap has already run: no DDL, no commit. The hard
    // re-run (all=true) commits its transaction and is ToolsControllerTest's.
    wp_set_current_user(createUser(['role' => 'administrator']));

    $response = restRequest('POST', PLUGIN_NS.'/migrations');
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['notices'])->not->toBeEmpty();
    expect(restRequest('POST', PLUGIN_NS.'/migrations', ['all' => 'maybe'])->get_status())->toBe(400); // not a boolean
});

test('the permissions are repaired, with a notice that asks for a reload', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));

    $response = restRequest('PUT', PLUGIN_NS.'/permissions');
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['notices'])->toContain('reload the page');
});

test('a table that is not MyISAM is not converted, and the answer says so', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));

    $response = restRequest('PUT', PLUGIN_NS.'/tables/glsr_ratings/engine');
    expect($response->get_status())->toBe(400);
    expect($response->get_data()['notices'])->toContain('does not use the MyISAM engine');
});

test('the system info is fetched outside wp-admin', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));

    $response = restRequest('GET', PLUGIN_NS.'/system-info');
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['system_info'])->toContain('Site Reviews');
});

test('the rollback stores the version and answers with the update request of WordPress', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $unlocked = new class extends UpdateLock {
        public function isLocked(string $basename): bool
        {
            return false;
        }
    };

    $response = swapInstance(UpdateLock::class, $unlocked, fn () => restRequest('POST', PLUGIN_NS.'/rollback', ['version' => '8.0.0']));
    expect($response->get_status())->toBe(200);
    expect($response->get_data()['data']['action'])->toBe('update-plugin');
    expect($response->get_data()['data']['plugin'])->toBe(glsr()->basename);
    expect($response->get_data()['url'])->toContain('welcome');
    expect(get_transient(glsr()->prefix.'rollback_version'))->toBe('8.0.0');
});

test('the rollback is refused while the plugin folder is a git repository, and for a version that is not one', function () {
    wp_set_current_user(createUser(['role' => 'administrator'])); // this checkout holds .git, so the real lock applies

    $locked = restRequest('POST', PLUGIN_NS.'/rollback', ['version' => '8.0.0']);
    expect($locked->get_status())->toBe(409);
    expect($locked->get_data()['code'])->toBe('glsr_rollback_locked');
    expect(get_transient(glsr()->prefix.'rollback_version'))->toBeFalse();

    expect(restRequest('POST', PLUGIN_NS.'/rollback', ['version' => 'latest'])->get_status())->toBe(400);
});

/*
 * The notices: a dismissal is the logged-in user's own meta, so the route asks for a login and nothing else.
 */

test('a notice is dismissed by its key, for the logged-in user only', function () {
    wp_set_current_user($userId = createUser(['role' => 'administrator']));
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0'); // the welcome notice shows on a new site

    $response = restRequest('DELETE', PLUGIN_NS.'/notices/welcome');

    expect($response->get_status())->toBe(200);
    expect($response->get_data())->toBe(['key' => 'welcome']);
    $dismissed = get_user_meta($userId, AbstractNotice::USER_META_KEY, true);
    expect($dismissed)->toHaveKey('welcome');
    expect($dismissed['welcome']['version'])->toBe(glsr()->version('minor'));
    expect(get_user_meta(createUser(['role' => 'administrator']), AbstractNotice::USER_META_KEY, true))->toBe('');
});

test('a notice is snoozed until its interval has passed', function () {
    wp_set_current_user($userId = createUser(['role' => 'administrator']));

    $response = restRequest('PUT', PLUGIN_NS.'/notices/write-review', ['until' => 'interval']);

    expect($response->get_status())->toBe(200);
    $dismissed = get_user_meta($userId, AbstractNotice::USER_META_KEY, true);
    expect($dismissed['write-review']['version'])->toBe('');
    expect($dismissed['write-review']['timestamp'])->toBeGreaterThan(0);
    expect(restRequest('PUT', PLUGIN_NS.'/notices/write-review', ['until' => 'forever'])->get_status())->toBe(400);
    expect(restRequest('PUT', PLUGIN_NS.'/notices/write-review')->get_status())->toBe(400);
});

test('a notice dismissal needs a login, and a notice that exists', function () {
    $visitor = restRequest('DELETE', PLUGIN_NS.'/notices/welcome');
    expect($visitor->get_status())->toBe(401);
    expect($visitor->get_data()['code'])->toBe('glsr_not_logged_in');

    wp_set_current_user(createUser(['role' => 'subscriber']));
    $unknown = restRequest('DELETE', PLUGIN_NS.'/notices/not-a-notice');
    expect($unknown->get_status())->toBe(404);
    expect($unknown->get_data()['code'])->toBe('glsr_invalid_notice');
    expect(restRequest('DELETE', PLUGIN_NS.'/notices/GeminiLabs%5CSiteReviews%5CNotices%5CWelcomeNotice')->get_status())->toBe(404); // the key, not the class
});

/*
 * The deactivation feedback of the Plugins screen's dialog.
 */

test('the deactivation feedback is posted home by whoever may deactivate plugins', function () {
    $requests = interceptHttp(); // the feedback is a remote POST; nothing leaves the container
    wp_set_current_user(createUser(['role' => 'administrator']));

    $response = restRequest('POST', PLUGIN_NS.'/feedback', ['details' => 'testing', 'reason' => 'other-reason', 'slug' => glsr()->id, 'version' => glsr()->version]);

    expect($response->get_status())->toBe(200);
    expect($requests)->toHaveCount(1);
    expect(restRequest('POST', PLUGIN_NS.'/feedback')->get_status())->toBe(400); // the reason is required

    wp_set_current_user(createUser(['role' => 'editor']));
    $refused = restRequest('POST', PLUGIN_NS.'/feedback', ['reason' => 'other-reason']);
    expect($refused->get_status())->toBe(403);
    expect($refused->get_data()['code'])->toBe('glsr_forbidden');
});
