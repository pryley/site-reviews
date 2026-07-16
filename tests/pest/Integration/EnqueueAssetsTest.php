<?php

use GeminiLabs\SiteReviews\Commands\EnqueueAdminAssets;
use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;

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

beforeEach(function () {
    resetPluginState();
    wp_dequeue_script(glsr()->id);
    wp_dequeue_style(glsr()->id);
    wp_deregister_script(glsr()->id);
    wp_deregister_style(glsr()->id);
});

afterEach(function () {
    foreach ([glsr()->id, glsr()->id.'/admin'] as $handle) {
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
    expect(implode('', (array) $inline))->toContain('GLSR.action=');
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
    // This string IS the contract with the frontend JS. Every key below is read by
    // it, and a rename here breaks the form with no error anywhere.
    $script = (new EnqueuePublicAssets())->inlineScript();

    expect($script)
        ->toContain('GLSR.action="'.glsr()->prefix.'public_action"')  // the ajax action
        ->toContain('GLSR.ajax_url="'.admin_url('admin-ajax.php').'"')
        ->toContain('GLSR.nameprefix="'.glsr()->id.'"')
        ->toContain('GLSR.version="'.glsr()->version.'"')
        ->toContain('GLSR.validation_strings=')
        ->toContain('GLSR.validation_config=')
        ->toContain('GLSR.captcha=')
        ->toContain('GLSR.stars_config=');

    // it is a script, so it has to parse: the object keys are unquoted deliberately
    expect($script)->toStartWith('window.hasOwnProperty("GLSR")');
});

test('the inline script can be filtered before it is printed', function () {
    add_filter('site-reviews/enqueue/public/localize', function (array $variables) {
        // NB: buildInlineScript() strips the quotes from object keys, but only
        // from keys matching [a-zA-Z]+ — a hyphen in the key would keep them.
        $variables['addons'] = ['myaddon' => ['version' => '1.0']];

        return $variables;
    });

    expect((new EnqueuePublicAssets())->inlineScript())
        ->toContain('GLSR.addons={myaddon:{version:"1.0"}}');
});

test('the pagination url parameter is only offered when the setting is on', function () {
    // The JS reads url_parameter to decide whether paging a review list should push
    // a query arg into the address bar. Off, it must be false and not the name of a
    // query var — otherwise the JS starts writing to the URL on a site that asked it
    // not to.
    glsr(GeminiLabs\SiteReviews\Database\OptionManager::class)
        ->set('settings.reviews.pagination.url_parameter', 'no');
    expect((new EnqueuePublicAssets())->inlineScript())->toContain('GLSR.url_parameter=false');

    glsr(GeminiLabs\SiteReviews\Database\OptionManager::class)
        ->set('settings.reviews.pagination.url_parameter', 'yes');
    expect((new EnqueuePublicAssets())->inlineScript())
        ->toContain('GLSR.url_parameter="'.glsr()->constant('PAGED_QUERY_VAR').'"');
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

    expect((new EnqueueAdminAssets())->inlineScript())
        ->toContain('GLSR.action="'.glsr()->prefix.'admin_action"')
        ->toContain('GLSR.nonce=');
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
