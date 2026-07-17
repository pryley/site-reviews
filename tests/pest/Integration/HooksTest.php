<?php

use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The wiring.
 *
 * Thirty classes under plugin/Hooks, each a table of "this controller method answers this WordPress
 * hook" — between them the entire surface the plugin presents to WordPress. They run during
 * bootstrap.php, so they are the most-executed code in the plugin.
 *
 * Re-running them is safe: WP_Hook keys a callback by a unique id from the object and method, so
 * registering the same one twice is a no-op. What makes the table observable is wiping $wp_filter
 * first — Pest.php backs it up and restores it per test (backupHooks/restoreHooks), so a test can
 * empty the hook world, run ONE class, and see exactly what it registered.
 */

beforeEach(fn () => resetPluginState());

/**
 * Everything registered on every hook, right now.
 *
 * @return array<array{0:string,1:mixed,2:int}> [hook name, callback, priority]
 */
function registeredHooks(): array
{
    $registered = [];
    foreach ($GLOBALS['wp_filter'] as $hookName => $wpHook) {
        foreach ($wpHook->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback) {
                $registered[] = [$hookName, $callback['function'], $priority];
            }
        }
    }

    return $registered;
}

test('a hooks class registers hooks, and every one of them points at a method that exists', function (string $class) {
    // A hook table is a list of strings. A typo in a method name is a hook that
    // silently answers nothing — WordPress will happily register a callback to a
    // method that is not there, and only find out when it fires.
    //
    // For the hooks the plugin does not own, HookProxy::proxy() catches this at
    // registration: it builds a ReflectionMethod, which throws if the method is
    // missing or is not public. So calling run() at all is itself the test for those.
    // For the plugin's OWN hooks the callback is registered directly, unchecked, and
    // that is what the method_exists assertion below covers.
    $hooks = "GeminiLabs\SiteReviews\Hooks\\{$class}";
    $GLOBALS['wp_filter'] = []; // Pest.php puts the world back afterwards

    glsr()->singleton($hooks);
    glsr($hooks)->runDeferred();
    glsr($hooks)->run();

    $registered = registeredHooks();
    expect($registered)->not->toBeEmpty(); // a hooks class that hooks nothing is dead code

    foreach ($registered as [$hookName, $callback, $priority]) {
        if (is_array($callback)) { // [$controller, 'method'] — the plugin's own hooks
            expect(method_exists($callback[0], $callback[1]))->toBeTrue();
        } else {
            expect(is_callable($callback))->toBeTrue(); // a HookProxy closure
        }
    }
})->with([
    'AdminHooks', 'DashboardHooks', 'DeactivationHooks', 'EditorHooks', 'FlyoutHooks',
    'GeolocationHooks', 'ImportHooks', 'LicensingHooks', 'ListTableHooks', 'MainHooks',
    'MenuHooks', 'MetaboxHooks', 'NetworkHooks', 'NoticeHooks', 'PrivacyHooks',
    'PublicHooks', 'QueueHooks', 'RestHooks', 'ReviewHooks', 'RevisionHooks',
    'RouterHooks', 'SettingsHooks', 'TaxonomyHooks', 'TinymceHooks', 'ToolsHooks',
    'TranslationHooks', 'UpdateHooks', 'UserHooks', 'VerificationHooks', 'WelcomeHooks',
]);

test('the review post type is registered on init, and the routes on the ajax actions', function () {
    // A spot check that the tables say what they are supposed to say — the two hooks
    // the whole plugin hangs off. MainHooks' entries go through HookProxy (init is
    // WordPress's hook, not the plugin's), so the callback is a closure and cannot be
    // matched by name; the count is what shows they arrived.
    $GLOBALS['wp_filter'] = [];

    glsr()->singleton(GeminiLabs\SiteReviews\Hooks\MainHooks::class);
    glsr(GeminiLabs\SiteReviews\Hooks\MainHooks::class)->run();

    $init = array_filter(registeredHooks(), fn ($hook) => 'init' === $hook[0]);
    // onInit, registerPostMeta, registerPostType, registerReviewTypes,
    // registerShortcodes, registerTaxonomy
    expect($init)->toHaveCount(6);

    $GLOBALS['wp_filter'] = [];
    glsr()->singleton(GeminiLabs\SiteReviews\Hooks\RouterHooks::class);
    glsr(GeminiLabs\SiteReviews\Hooks\RouterHooks::class)->run();

    $hookNames = array_column(registeredHooks(), 0);
    expect($hookNames)
        ->toContain('wp_ajax_'.glsr()->prefix.'public_action')
        ->toContain('wp_ajax_nopriv_'.glsr()->prefix.'public_action')
        ->toContain('wp_ajax_'.glsr()->prefix.'admin_action');
});

/*
 * AbstractHooks::hook() — the mechanism every table above is fed through.
 */

class FakeHooksController
{
    use HookProxy;

    public function doSomething(): void
    {
    }

    public function filterSomething($value)
    {
        return $value;
    }

    protected function notPublic(): void
    {
    }
}

class FakeHooks extends AbstractHooks
{
    public array $table = [];
    public ?int $initLevel = null;
    public bool $onInitRan = false;

    public function levelInit(): ?int
    {
        return $this->initLevel;
    }

    public function onInit(): void
    {
        $this->onInitRan = true;
    }

    public function run(): void
    {
        $this->hook(FakeHooksController::class, $this->table);
    }
}

function fakeHooks(array $table = []): FakeHooks
{
    $hooks = new FakeHooks();
    $hooks->table = $table;

    return $hooks;
}

test('a method named filter* becomes a filter, and anything else an action', function () {
    // The only thing that decides which is the method's name. Get it wrong and the
    // callback either never runs or eats the value it was meant to pass through.
    fakeHooks([
        ['filterSomething', 'my_filter'],
        ['doSomething', 'my_action'],
    ])->run();

    expect(has_filter('my_filter'))->not->toBeFalse();
    expect(has_action('my_action'))->not->toBeFalse();
});

test('the priority and the argument count default to 10 and 1', function () {
    fakeHooks([['doSomething', 'my_action']])->run();

    $callbacks = $GLOBALS['wp_filter']['my_action']->callbacks;
    expect(array_keys($callbacks))->toBe([10]);
    expect(reset($callbacks[10])['accepted_args'])->toBe(1);
});

test('the priority and the argument count can be given', function () {
    fakeHooks([['doSomething', 'my_action', 5, 3]])->run();

    $callbacks = $GLOBALS['wp_filter']['my_action']->callbacks;
    expect(array_keys($callbacks))->toBe([5]);
    expect(reset($callbacks[5])['accepted_args'])->toBe(3);
});

test('the plugin hooks its own callbacks directly, and everybody else through the proxy', function () {
    // A hook the plugin fires itself is called with the arguments the plugin passes,
    // so it can be trusted. A hook belonging to WordPress or another plugin cannot:
    // HookProxy wraps it so that a third party passing the wrong thing is a logged
    // error rather than a fatal on somebody's site.
    fakeHooks([
        ['doSomething', glsr()->id.'/my_own_hook'],
        ['doSomething', 'somebody_elses_hook'],
    ])->run();

    $own = reset($GLOBALS['wp_filter'][glsr()->id.'/my_own_hook']->callbacks[10])['function'];
    $theirs = reset($GLOBALS['wp_filter']['somebody_elses_hook']->callbacks[10])['function'];

    expect($own)->toBeArray()                                   // [$controller, 'method']
        ->and($own[1])->toBe('doSomething')
        ->and($theirs)->toBeInstanceOf(Closure::class);         // the proxy
});

test('an entry that names no hook is skipped rather than registered', function () {
    $GLOBALS['wp_filter'] = [];

    fakeHooks([
        ['doSomething'],  // no hook name: nothing to register it to
        [],
    ])->run();

    expect(registeredHooks())->toBe([]);
});

test('the proxy refuses a method that is missing or is not public', function () {
    // Which is why a typo in a hook table for somebody else's hook is caught the
    // moment the plugin boots, rather than the moment the hook fires.
    $controller = new FakeHooksController();

    expect(fn () => $controller->proxy('noSuchMethod'))->toThrow(ReflectionException::class);
    expect(fn () => $controller->proxy('notPublic'))->toThrow(BadMethodCallException::class);
});

test('a hooks class with a level runs its deferred callback on that hook', function () {
    // levelInit() is how a hooks class says "call me on init, at this priority"
    // without hooking a controller.
    $hooks = fakeHooks();
    $hooks->initLevel = 7;

    $hooks->runDeferred();

    expect(has_action('init', [$hooks, 'onInit']))->toBe(7);
});

test('a hooks class with no level defers nothing', function () {
    $GLOBALS['wp_filter'] = [];
    $hooks = fakeHooks(); // initLevel is null

    $hooks->runDeferred();

    expect(registeredHooks())->toBe([]);
});

/*
 * The Hooks orchestrator (plugin/Hooks.php) and the base-class defaults.
 *
 * The orchestrator scans directories for its classes, so its failure modes are filesystem
 * shapes: a directory that is not there, and a file whose class is not. Both are driven
 * through the site-reviews/path filter — the same seam an unusual install layout would hit.
 */

function redirectPluginPath(string $from, string $to): Closure
{
    $filter = fn ($path, $file) => $file === $from ? $to : $path;
    add_filter('site-reviews/path', $filter, 10, 2);

    return $filter;
}

test('a missing hooks directory registers nothing, quietly', function () {
    $GLOBALS['wp_filter'] = [];
    redirectPluginPath('plugin/Hooks', '/nowhere/at/all');
    redirectPluginPath('plugin/Integrations', '/nowhere/at/all/either');

    (new GeminiLabs\SiteReviews\Hooks())->run();

    expect(array_keys($GLOBALS['wp_filter']))->toBe(['site-reviews/path']); // nothing new arrived
});

test('a hooks file with no class in it is logged, not fatal', function () {
    // What half an upload looks like: the file survived, its class did not.
    redirectPluginPath('plugin/Hooks', glsr()->path('tests/pest/fixtures/hooks-broken'));
    redirectPluginPath('plugin/Integrations', '/nowhere/at/all');

    (new GeminiLabs\SiteReviews\Hooks())->run();

    expect(glsr(GeminiLabs\SiteReviews\Modules\Console::class)->get())->toContain('NotAClass');
});

test('an integration directory with no Hooks class is logged, not fatal', function () {
    // runIntegrations() directly: run() stops before it when the Hooks directory is missing.
    redirectPluginPath('plugin/Integrations', glsr()->path('tests/pest/fixtures/integrations-broken'));

    (new GeminiLabs\SiteReviews\Hooks())->runIntegrations();

    expect(glsr(GeminiLabs\SiteReviews\Modules\Console::class)->get())->toContain('BrokenIntegration');
});

test('the base class answers for the subclasses that override nothing', function () {
    // Every default a subclass may lean on: the empty run()/onInit()/onPluginsLoaded(),
    // and option() reading through the settings helper.
    $hooks = new class() extends AbstractHooks {
    };

    $hooks->run();
    $hooks->onInit();
    $hooks->onPluginsLoaded();

    expect($hooks->levelInit())->toBeNull()
        ->and($hooks->levelPluginsLoaded())->toBeNull();
    expect($hooks->option('reviews.date.format', 'default'))->toBeString();
});

test('the rest routes stand down while wordpress is installing', function () {
    // rest_preload_api_request runs during WooCommerce updates, when half of WordPress
    // is not there to answer.
    $GLOBALS['wp_filter'] = [];
    wp_installing(true);
    try {
        glsr(GeminiLabs\SiteReviews\Hooks\RestHooks::class)->run();
    } finally {
        wp_installing(false);
    }

    expect(registeredHooks())->toBe([]);
});

test('the tinymce button can be switched off by filter', function () {
    $GLOBALS['wp_filter'] = [];
    add_filter('site-reviews/register/tinymce', '__return_false');

    glsr(GeminiLabs\SiteReviews\Hooks\TinymceHooks::class)->run();

    expect(has_action('media_buttons'))->toBeFalse();
});
