<?php

use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Compat\Controllers\Controller as CompatController;
use GeminiLabs\SiteReviews\Controllers\DebugController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Assets\CompatScript;
use GeminiLabs\SiteReviews\Modules\Assets\DebugScript;
use GeminiLabs\SiteReviews\Modules\Rating;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The asset commands.
 *
 * The interesting half is not the wp_enqueue_script() call but the inline script with it: the
 * frontend JS reads its whole configuration off a `GLSR` global this command prints — the ajax
 * action and URL, the captcha config, the validation strings, the CSS classes the validator adds.
 * A key renamed here is a silently broken form, so the payload's shape is worth pinning, not just
 * the enqueue.
 */

/**
 * The config as InlineScript::build() prints it.
 */
function printedConfig(string $bundle): array
{
    $printed = [];
    $capture = function ($script, $raw, $config) use (&$printed) {
        $printed = $config;

        return $script;
    };
    add_filter("site-reviews/enqueue/{$bundle}/inline-script", $capture, 10, 3);
    'admin' === $bundle
        ? (new EnqueueAdminAssets())->inlineScript()
        : (new EnqueuePublicAssets())->inlineScript();
    remove_filter("site-reviews/enqueue/{$bundle}/inline-script", $capture, 10);

    return $printed;
}

beforeEach(function () {
    resetPluginState();
    wp_dequeue_script(glsr()->id);
    wp_dequeue_style(glsr()->id);
    wp_deregister_script(glsr()->id);
    wp_deregister_style(glsr()->id);
});

afterEach(function () {
    foreach ([glsr()->id, glsr()->id.'/admin', glsr()->id.'/compat', glsr()->id.'/admin/compat', glsr()->id.'/debug', glsr()->id.'/admin/debug', 'old-addon', 'unrelated'] as $handle) {
        wp_dequeue_script($handle);
        wp_dequeue_style($handle);
        wp_deregister_script($handle);
        wp_deregister_style($handle);
    }
    wp_dequeue_style('wp-color-picker');
    set_current_screen('front');
});

test('the public script and stylesheet are enqueued', function () {
    (new EnqueuePublicAssets())->handle();

    expect(wp_script_is(glsr()->id, 'enqueued'))->toBeTrue()
        ->and(wp_style_is(glsr()->id, 'enqueued'))->toBeTrue();

    // and the inline script goes with it — the JS is useless without its config
    $inline = wp_scripts()->get_data(glsr()->id, 'before');
    expect(implode('', (array) $inline))->toContain('nameprefix:"site-reviews"');
});

test('a site can turn the assets off', function () {
    // Some sites bundle their own build of the plugin's JS and CSS.
    add_filter('site-reviews/assets/js', '__return_false');
    add_filter('site-reviews/assets/css', '__return_false');

    (new EnqueuePublicAssets())->handle();

    expect(wp_script_is(glsr()->id, 'enqueued'))->toBeFalse()
        ->and(wp_style_is(glsr()->id, 'enqueued'))->toBeFalse();
});

test('the frontend is handed the configuration it runs on', function () {
    // The JS-side twin of this list is the fixture in +/scripts/tests/harness.js.
    wp_set_current_user(0);
    $config = (new EnqueuePublicAssets())->config();

    expect(array_keys($config))->toBe([
        'captcha', 'modal', 'nameprefix', 'pagination', 'rating', 'request', 'text', 'validation',
    ]);
    expect($config['request'])->toBe([
        'ajax' => [
            'action' => glsr()->prefix.'public_action',
            'rest' => glsr()->prefix.'rest_request',
            'url' => admin_url('admin-ajax.php'),
        ],
        'nonce' => false,
        'url' => esc_url_raw(rest_url(glsr()->id.'/v1/')),
    ]);
    expect($config['nameprefix'])->toBe(glsr()->id);
    expect(array_keys($config['modal']))->toBe(['wrappedBy']);
    expect($config['modal']['wrappedBy'])->toContain('block'); // the integrations add their own

    expect($config['rating'])->toHaveKeys(['clearable', 'tooltip']);
    expect($config['text'])->toHaveKey('closeModal');
    expect(array_keys($config['validation']))->toBe([
        'field', 'fieldError', 'fieldHidden', 'fieldMessage', 'fieldRequired', 'fieldValid',
        'form', 'formError', 'formMessage', 'formMessageFailed', 'formMessageSuccess',
        'inputError', 'inputValid', 'strings',
    ]);
    expect($config['validation']['fieldError'])->toBe('glsr-field-is-invalid');
    expect($config['validation']['strings'])->toHaveKey('required');
});

test('the inline script is the built file, with the version, the config and the 8.x keys in place of its three names', function () {
    $built = trim(file_get_contents(glsr()->path('assets/scripts/inline-script.js')));
    [$start] = explode('GLSR_VERSION', $built);
    $script = (new EnqueuePublicAssets())->inlineScript();

    // it is a script, so it has to parse: the object keys are unquoted deliberately
    expect($script)
        ->toStartWith($start.'"'.glsr()->version.'",{captcha:[],modal:{wrappedBy:["block"')
        ->toEndWith(',{})}();');
    expect($script)->not->toContain('GLSR_CONFIG');
    expect($script)->not->toContain('GLSR_DEPRECATED');
    expect($script)->not->toContain('GLSR_VERSION');
});

test('a value that holds one of the three names is printed as it is', function () {
    add_filter('site-reviews/assets/config', function (array $config) {
        $config['text']['closeModal'] = 'GLSR_DEPRECATED and GLSR_VERSION';
        return $config;
    });

    expect((new EnqueuePublicAssets())->inlineScript())->toContain('closeModal:"GLSR_DEPRECATED and GLSR_VERSION"');
});

test('a missing inline script is logged and skipped', function () {
    add_filter('site-reviews/path', function ($path, $file) {
        return 'assets/scripts/inline-script.js' === $file ? '/no/such/inline-script.js' : $path;
    }, 10, 2);

    expect((new EnqueuePublicAssets())->inlineScript())->toBe('');
});

test('the captcha keys the script reads are written in its own case', function () {
    // Captcha::config() is also read by PHP under its own names, so only the script's copy is renamed.
    glsr(OptionManager::class)->set('settings.forms.captcha.integration', 'procaptcha');
    glsr(OptionManager::class)->set('settings.forms.captcha.usage', 'all');

    $captcha = (new EnqueuePublicAssets())->config()['captcha'];

    expect($captcha)->toHaveKeys(['captchaType', 'tokenField', 'type'])
        ->not->toHaveKey('captcha_type')
        ->not->toHaveKey('token_field');
    expect($captcha['type'])->toBe('procaptcha');
    expect($captcha['tokenField'])->toBe('procaptcha-response');
});

test('a rest nonce is only offered to logged-in visitors', function () {
    // Anonymous pages are cached, and a cached nonce is a stale nonce: the REST API refuses
    // a stale X-WP-Nonce with a 403 before the permission callback runs. So the anonymous
    // page carries none — and the logged-in page (which caches exclude) carries one, because
    // it is what keeps a logged-in submitter's identity on their review.
    wp_set_current_user(0);
    expect((new EnqueuePublicAssets())->config()['request']['nonce'])->toBeFalse();

    wp_set_current_user(createUser());
    expect((new EnqueuePublicAssets())->config()['request']['nonce'])->toBe(wp_create_nonce('wp_rest'));
    wp_set_current_user(0);
});

test('the config can be filtered before it is printed', function () {
    add_filter('site-reviews/assets/config', function (array $config, string $bundle) {
        if ('public' === $bundle) {
            $config['images'] = ['maxFiles' => 5];
            $config['rating']['clearable'] = true;
        }
        $config['debug'] = 'yes'; // debug and compat have their own filters; this is ignored
        return $config;
    }, 10, 2);

    $public = printedConfig('public');
    expect($public['images'])->toBe(['maxFiles' => 5]);
    expect($public['rating']['clearable'])->toBeTrue();
    expect($public['debug']['enabled'])->toBeFalse();

    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    expect(printedConfig('admin'))->not->toHaveKey('images');
});

test('debug mode is off, whatever WP_DEBUG says, until the filter turns it on for a script', function () {
    expect(printedConfig('public')['debug']['enabled'])->toBeFalse();
    glsr(DebugController::class)->enqueuePublicScript();
    (new EnqueuePublicAssets())->enqueueScripts();
    glsr(DebugController::class)->enqueuePublicScript();
    expect(wp_script_is(glsr()->id.'/debug', 'enqueued'))->toBeFalse();

    add_filter('site-reviews/debug/assets', fn ($debug, $bundle) => 'public' === $bundle, 10, 2);
    expect(printedConfig('public')['debug'])->toBe(['enabled' => true]);
    expect((new EnqueuePublicAssets())->inlineScript())->toContain('debug:{"enabled":true}');
    wp_dequeue_script(glsr()->id);
    wp_dequeue_script(glsr()->id.'/compat'); // a dependent that is enqueued counts for the script
    glsr(DebugController::class)->enqueuePublicScript();
    expect(wp_script_is(glsr()->id.'/debug', 'enqueued'))->toBeFalse(); // it only follows the script
    (new EnqueuePublicAssets())->enqueueScripts();
    glsr(DebugController::class)->enqueuePublicScript();
    expect(wp_script_is(glsr()->id.'/debug', 'enqueued'))->toBeTrue();
    expect(wp_scripts()->registered[glsr()->id.'/debug']->deps)->toBe([glsr()->id]);

    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    expect(printedConfig('admin')['debug'])->toBe(['enabled' => false]);
    (new EnqueueAdminAssets())->enqueueScripts();
    glsr(DebugController::class)->enqueueAdminScript();
    expect(wp_script_is(glsr()->id.'/admin/debug', 'enqueued'))->toBeFalse();

    remove_all_filters('site-reviews/debug/assets');
    add_filter('site-reviews/debug/assets', '__return_true');
    glsr(DebugController::class)->enqueueAdminScript();
    expect(wp_script_is(glsr()->id.'/admin/debug', 'enqueued'))->toBeTrue();
    expect(wp_scripts()->registered[glsr()->id.'/admin/debug']->deps)->toBe([glsr()->id.'/admin']);
});

test('each mode follows its setting, and its filter has the last word', function () {
    $compat = glsr(CompatScript::class);
    $debug = glsr(DebugScript::class);
    expect([$compat->isEnabled('public'), $compat->isEnabled('admin')])->toBe([true, true]);
    expect([$debug->isEnabled('public'), $debug->isEnabled('admin')])->toBe([false, false]);

    glsr(OptionManager::class)->set('settings.advanced.compat', 'no');
    glsr(OptionManager::class)->set('settings.advanced.debug', 'yes');
    expect([$compat->isEnabled('public'), $compat->isEnabled('admin')])->toBe([false, false]);
    expect([$debug->isEnabled('public'), $debug->isEnabled('admin')])->toBe([true, true]);
    expect(printedConfig('public'))->toMatchArray(['compat' => false, 'debug' => ['enabled' => true]]);

    add_filter('site-reviews/compat/assets', fn ($compat, $bundle) => 'admin' === $bundle ? true : $compat, 10, 2);
    add_filter('site-reviews/debug/assets', '__return_false');
    expect([$compat->isEnabled('public'), $compat->isEnabled('admin')])->toBe([false, true]);
    expect([$debug->isEnabled('public'), $debug->isEnabled('admin')])->toBe([false, false]);
});

test('the public script is given the address of the debug script, unless debug on request is forbidden', function () {
    $url = glsr()->url('assets/scripts/site-reviews-debug.js').'?ver='.glsr()->version;
    expect(printedConfig('public')['debug'])->toBe(['enabled' => false, 'url' => $url]);

    add_filter('site-reviews/debug/on-request', '__return_false');
    expect(printedConfig('public')['debug'])->toBe(['enabled' => false]);
    remove_filter('site-reviews/debug/on-request', '__return_false');

    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    expect(printedConfig('admin')['debug'])->toBe(['enabled' => false]);
    // the block editor prints the public script too
    expect(printedConfig('public')['debug'])->toBe(['enabled' => false]);
});

test('PHP answers the same with and without the glsr-debug parameter, and never reads it', function () {
    $without = (new EnqueuePublicAssets())->inlineScript();
    $_GET['glsr-debug'] = '1';
    $_REQUEST['glsr-debug'] = '1';
    $with = (new EnqueuePublicAssets())->inlineScript();
    unset($_GET['glsr-debug'], $_REQUEST['glsr-debug']);
    expect($with)->toBe($without);

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(glsr()->path('plugin'), FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if ('php' === $file->getExtension() && str_contains((string) file_get_contents($file->getPathname()), 'glsr-debug')) {
            throw new Exception($file->getPathname().' names the glsr-debug parameter');
        }
    }
    expect(true)->toBeTrue();
});

test('the pagination url parameter is only offered when the setting is on', function () {
    // The JS reads urlParameter to decide whether paging a review list should push
    // a query arg into the address bar. Off, it must be false and not the name of a
    // query var — otherwise the JS starts writing to the URL on a site that asked it
    // not to.
    glsr(OptionManager::class)->set('settings.reviews.pagination.url_parameter', 'no');
    expect((new EnqueuePublicAssets())->config()['pagination']['urlParameter'])->toBeFalse();

    glsr(OptionManager::class)->set('settings.reviews.pagination.url_parameter', 'yes');
    expect((new EnqueuePublicAssets())->config()['pagination']['urlParameter'])
        ->toBe(glsr()->constant('PAGED_QUERY_VAR'));
});

/*
 * The two filters that received the localized variables before 8.4.0 (Compat\LocalizeFilters).
 */

test('an 8.x localize filter is handed the array it knew', function () {
    $received = [];
    add_filter('site-reviews/enqueue/public/localize', function (array $variables) use (&$received) {
        $received = $variables;
        return $variables;
    });

    (new EnqueuePublicAssets())->inlineScript();

    expect(array_keys($received))->toBe([
        'action', 'addons', 'ajax_pagination', 'ajax_url', 'captcha', 'modal_wrapped_by', 'nameprefix',
        'rest_nonce', 'rest_url', 'stars_config', 'state', 'text', 'url_parameter',
        'validation_config', 'validation_strings', 'version',
    ]);
    expect($received['action'])->toBe(glsr()->prefix.'public_action');
    expect($received['addons'])->toBe([]);
    expect($received['text'])->toBe(['close_modal' => __('Close Modal', 'site-reviews')]);
    expect($received['validation_config'])->toHaveKeys(['field', 'field_error', 'form_message_success'])
        ->not->toHaveKey('strings');
    expect($received['validation_strings'])->toHaveKey('required');
});

test('what an 8.x filter changes in a core value reaches the config', function () {
    add_filter('site-reviews/enqueue/public/localize', function (array $variables) {
        $variables['stars_config']['clearable'] = true;
        $variables['text']['close_modal'] = 'Shut';
        $variables['validation_config']['field_error'] = 'is-wrong';
        $variables['validation_strings']['required'] = 'Needed.';
        $variables['url_parameter'] = 'page-of-reviews';
        return $variables;
    });

    $config = printedConfig('public');

    expect($config['rating']['clearable'])->toBeTrue();
    expect($config['text'])->toBe(['closeModal' => 'Shut']);
    expect($config['validation']['fieldError'])->toBe('is-wrong');
    expect($config['validation']['formError'])->toBe('glsr-form-is-invalid'); // the rest is untouched
    expect($config['validation']['strings']['required'])->toBe('Needed.');
    expect($config['validation']['strings'])->toHaveKey('email');
    expect($config['pagination']['urlParameter'])->toBe('page-of-reviews');
});

test('what an 8.x filter adds is printed as a key of its own, outside the config', function () {
    add_filter('site-reviews/enqueue/public/localize', function (array $variables) {
        // InlineScript strips the quotes only from object keys matching [a-zA-Z]+
        $variables['addons'] = ['myaddon' => ['version' => '1.0']];
        $variables['my_snippet'] = 1;
        $variables['config'] = 'must not replace GLSR.config';
        return $variables;
    });

    $script = (new EnqueuePublicAssets())->inlineScript();

    expect($script)
        ->toEndWith(',{addons:{myaddon:{version:"1.0"}},"my_snippet":1})}();');
    expect(printedConfig('public'))->not->toHaveKey('addons')
        ->not->toHaveKey('my_snippet');
});

test('with the compat layer off, the 8.x filters are not run', function () {
    $ran = false;
    add_filter('site-reviews/enqueue/public/localize', function (array $variables) use (&$ran) {
        $ran = true;
        $variables['addons'] = ['myaddon' => []];
        return $variables;
    });
    add_filter('site-reviews/compat/assets', '__return_false');

    $script = (new EnqueuePublicAssets())->inlineScript();

    expect($ran)->toBeFalse();
    expect($script)->not->toContain('GLSR[')->toContain('"compat":false');
});

test('the compat script follows each script unless the filter turns it off for that script', function () {
    (new EnqueuePublicAssets())->enqueueScripts();
    expect(wp_script_is(glsr()->id.'/compat', 'enqueued'))->toBeTrue();
    expect(wp_scripts()->registered[glsr()->id.'/compat']->deps)->toBe([glsr()->id]);
    expect(wp_scripts()->registered[glsr()->id.'/compat']->src)->toBe(glsr()->url('assets/scripts/site-reviews-compat.js'));

    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    add_filter('site-reviews/compat/assets', fn ($compat, $bundle) => 'public' === $bundle, 10, 2);
    (new EnqueueAdminAssets())->enqueueScripts();
    expect(wp_script_is(glsr()->id.'/admin/compat', 'enqueued'))->toBeFalse();
    expect(printedConfig('admin')['compat'])->toBeFalse();
    expect(printedConfig('public')['compat'])->toBeTrue();
});

test('a script that depends on the public script is made to depend on the compat script', function () {
    // Review Images 5.0.4 reads GLSR.Utils while it is parsed, and is not deferred.
    (new EnqueuePublicAssets())->enqueueScripts();
    wp_enqueue_script('old-addon', 'https://example.org/addon.js', [glsr()->id], '1.0', true);
    wp_enqueue_script('unrelated', 'https://example.org/unrelated.js', ['jquery'], '1.0', true);

    glsr(CompatScript::class)->addToDependents();
    glsr(CompatScript::class)->addToDependents();

    expect(wp_scripts()->registered['old-addon']->deps)->toBe([glsr()->id, glsr()->id.'/compat']);
    expect(wp_scripts()->registered['unrelated']->deps)->toBe(['jquery']);
    expect(wp_scripts()->registered[glsr()->id.'/compat']->deps)->toBe([glsr()->id]);

    ob_start();
    wp_scripts()->do_items(false, 1);
    $html = (string) ob_get_clean();
    $script = strpos($html, 'site-reviews.js');
    $compat = strpos($html, 'site-reviews-compat.js');
    $addon = strpos($html, 'addon.js');
    expect($script)->toBeLessThan($compat);
    expect($compat)->toBeLessThan($addon);
    // the dependent is not deferred, so neither script before it is
    expect(preg_match_all('/<script[^>]*\sdefer[\s>=]/', $html))->toBe(0);
});

test('without a dependent the script and the compat script are deferred, in that order', function () {
    // An inline script after a script stops WordPress deferring it; the Elementor integration adds one.
    global $wp_filter;
    $hook = 'site-reviews/enqueue/public/inline-script/after';
    $callbacks = $wp_filter[$hook] ?? null;
    unset($wp_filter[$hook]);

    (new EnqueuePublicAssets())->enqueueScripts();
    glsr(CompatScript::class)->addToDependents();

    ob_start();
    wp_scripts()->do_items(false, 1);
    $html = (string) ob_get_clean();
    if ($callbacks) {
        $wp_filter[$hook] = $callbacks;
    }

    expect(preg_match_all('/<script[^>]*\sdefer[\s>=]/', $html))->toBe(2);
    expect(strpos($html, 'site-reviews.js'))->toBeLessThan(strpos($html, 'site-reviews-compat.js'));
});

test('the script that the after filter adds runs after the compat script', function () {
    add_filter('site-reviews/enqueue/public/inline-script/after', fn () => 'GLSR.Utils;');
    (new EnqueuePublicAssets())->enqueueScripts();
    expect(wp_scripts()->get_data(glsr()->id.'/compat', 'after'))->toContain('GLSR.Utils;');
    expect(wp_scripts()->get_data(glsr()->id, 'after'))->toBeFalsy();
});

test('with compat mode off no script is made to depend on anything, and the after filter follows the script', function () {
    add_filter('site-reviews/compat/assets', '__return_false');
    add_filter('site-reviews/enqueue/public/inline-script/after', fn () => 'GLSR.Util;');
    (new EnqueuePublicAssets())->enqueueScripts();
    wp_enqueue_script('old-addon', 'https://example.org/addon.js', [glsr()->id], '1.0', true);
    glsr(CompatScript::class)->addToDependents();

    expect(wp_script_is(glsr()->id.'/compat', 'registered'))->toBeFalse();
    expect(wp_scripts()->registered['old-addon']->deps)->toBe([glsr()->id]);
    expect(wp_scripts()->get_data(glsr()->id, 'after'))->toContain('GLSR.Util;');
});

test('the canvas of the block editor is given the compat script, and the debug script in debug mode', function () {
    // The canvas gets a WP_Scripts of its own, with what the page registered and nothing enqueued.
    $canvas = function (): array {
        global $wp_scripts;
        $page = $wp_scripts;
        $wp_scripts = new WP_Scripts();
        $wp_scripts->registered = $page->registered;
        glsr(CompatController::class)->enqueueCanvasScript();
        glsr(DebugController::class)->enqueueCanvasScript();
        $queue = $wp_scripts->queue;
        $wp_scripts = $page;
        return $queue;
    };
    add_filter('site-reviews/debug/assets', '__return_true');
    (new EnqueuePublicAssets())->enqueueScripts();

    expect($canvas())->toBe([]); // a public page prints them itself

    set_current_screen('edit-page');
    expect($canvas())->toBe([glsr()->id.'/compat', glsr()->id.'/debug']);
});

test('the inline stylesheet has its star urls substituted in', function () {
    // inline-styles.css ships with placeholders — :star-full and friends — and the
    // config swaps in the real URLs. A placeholder that survives is a CSS custom
    // property pointing at url(:star-full), which loads nothing and shows no stars.
    $styles = (new EnqueuePublicAssets())->inlineStyles();

    expect($styles)->toContain('--glsr-star-full:url(')
        ->toContain(glsr()->url('assets/images/stars/default/star-full.svg'))
        ->not->toContain('url(:star-full)'); // the placeholder is gone
});

/*
 * The admin assets.
 */

test('the admin assets are not loaded on a screen that has nothing to do with reviews', function () {
    // handle() is hooked to admin_enqueue_scripts, which fires on EVERY admin page.
    // Loading the plugin's admin bundle on somebody else's screen is how plugins
    // earn their reputation.
    set_current_screen('options-general.php');

    $command = new EnqueueAdminAssets();
    $command->handle();

    expect($command->successful())->toBeFalse()
        ->and(wp_script_is(glsr()->id.'/admin', 'enqueued'))->toBeFalse();
});

test('the admin assets are loaded on the review list table', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);

    (new EnqueueAdminAssets())->handle();

    expect(wp_script_is(glsr()->id.'/admin', 'enqueued'))->toBeTrue()
        ->and(wp_style_is(glsr()->id.'/admin', 'enqueued'))->toBeTrue();

    // the colour picker comes with them: the settings page uses it
    expect(wp_style_is('wp-color-picker', 'enqueued'))->toBeTrue();
});

test('the admin script carries the admin ajax action', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);

    $config = (new EnqueueAdminAssets())->config();

    expect(array_keys($config))->toBe(['filters', 'nameprefix', 'nonce', 'rating', 'request', 'text', 'urls']);
    expect($config['request'])->toBe([
        'ajax' => [
            'action' => glsr()->prefix.'admin_action',
            'rest' => glsr()->prefix.'rest_request',
            'url' => admin_url('admin-ajax.php'),
        ],
        'nonce' => wp_create_nonce('wp_rest'),
        'url' => esc_url_raw(rest_url(glsr()->id.'/v1/')),
    ]);
    expect($config['nonce'])->toHaveKey('toggle-pinned');
    expect($config['rating'])->toBe(['max' => Rating::max(), 'min' => Rating::min()]);
    expect($config['text'])->toHaveKeys(['cancel', 'importError', 'systemInfo500', 'systemInfoError']);

    // the classic editor's shortcode button adds its subject through the config filter
    expect(printedConfig('admin')['tinymce'])->toHaveKeys(['plugins', 'required']);
});

test('what an 8.x filter adds to the admin nonces reaches the config', function () {
    // Review Forms 3.1.2 writes nonce[...] and addons[...] here and reads the nonce through GLSR.nonce.
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('edit-'.glsr()->post_type);
    add_filter('site-reviews/enqueue/admin/localize', function (array $variables) {
        $variables['addons']['site-reviews-forms'] = ['options' => []];
        $variables['nonce']['metabox-details'] = 'abc123';
        $variables['text']['import_error'] = 'Too big.';
        return $variables;
    });

    $config = printedConfig('admin');

    expect($config['nonce']['metabox-details'])->toBe('abc123');
    expect($config['nonce'])->toHaveKey('toggle-pinned');
    expect($config['text']['importError'])->toBe('Too big.');
    expect($config['text'])->not->toHaveKey('import_error');
    expect($config['tinymce'])->toHaveKeys(['plugins', 'required']);
    expect((new EnqueueAdminAssets())->inlineScript())
        ->toEndWith(',{addons:{"site-reviews-forms":{options:[]}}})}();');
});

test('the admin assets load on the review import screen', function () {
    // The importer runs on a bare admin.php page (base "admin"), identified only by ?import=<type>.
    set_current_screen('admin');
    $_GET['import'] = glsr()->post_type;

    try {
        $command = new EnqueueAdminAssets();
        expect((fn () => $this->isCurrentScreen())->call($command))->toBeTrue();
    } finally {
        unset($_GET['import']);
        set_current_screen('front');
    }
});

test('the admin assets stay out of the Customizer preview', function () {
    // The Customizer preview is an iframe of the front end rendered inside wp-admin; loading the
    // admin bundle there would fight the preview. A previewing manager is stood up without its heavy
    // constructor — only is_preview() matters to is_customize_preview().
    require_once ABSPATH.'wp-includes/class-wp-customize-manager.php';
    $manager = (new \ReflectionClass(\WP_Customize_Manager::class))->newInstanceWithoutConstructor();
    $previewing = new \ReflectionProperty(\WP_Customize_Manager::class, 'previewing');
    $previewing->setAccessible(true);
    $previewing->setValue($manager, true);
    $original = $GLOBALS['wp_customize'] ?? null;
    $GLOBALS['wp_customize'] = $manager;

    try {
        $command = new EnqueueAdminAssets();
        expect((fn () => $this->isCurrentScreen())->call($command))->toBeFalse();
    } finally {
        if (null === $original) {
            unset($GLOBALS['wp_customize']);
        } else {
            $GLOBALS['wp_customize'] = $original;
        }
    }
});

test('a missing inline stylesheet is logged and skipped rather than left to fatal', function () {
    // inlineStyles() reads the file through the path filter; pointing it at a file that is not there
    // exercises the guard that logs and returns nothing instead of feeding file_get_contents(false).
    add_filter('site-reviews/path', function ($path, $file) {
        return 'assets/styles/inline-styles.css' === $file ? '/no/such/inline-styles.css' : $path;
    }, 10, 2);

    expect((new EnqueuePublicAssets())->inlineStyles())->toBe('');
});
