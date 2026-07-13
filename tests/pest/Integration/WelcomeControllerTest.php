<?php

use GeminiLabs\SiteReviews\Controllers\WelcomeController;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Welcome page — the one a person sees the first time they activate the plugin.
 *
 * It is a dashboard page that is deliberately REGISTERED AND THEN HIDDEN, and that contradiction
 * is the whole of this controller. WordPress will only run a page's callback if the page is in the
 * menu: the privilege check in wp-admin/admin.php looks the slug up in $_registered_pages, and a
 * page that was never added is a "You do not have sufficient permissions" screen. But nobody wants
 * a permanent "Site Reviews" entry under Dashboard.
 *
 * So it is added on `admin_menu` and taken back off on `admin_init` — which runs AFTER the menu is
 * built but BEFORE the privilege check — leaving a page that works when linked to and does not
 * appear in the sidebar. The controller's own comment says as much, and removeSubMenu() and
 * restorePageTitle() exist only to clean up after the trick: removing the page also removes the
 * title WordPress would have shown, so the title is put back by hand.
 *
 * Get the order wrong and the failure is not subtle: the About link 403s.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('dashboard');
    $GLOBALS['submenu'] = [];
    $GLOBALS['menu'] = [];
    $GLOBALS['_registered_pages'] = [];
});

afterEach(function () {
    set_current_screen('front');
    unset($GLOBALS['submenu'], $GLOBALS['menu'], $GLOBALS['_registered_pages'], $GLOBALS['title']);
});

function welcomeSlug(): string
{
    return glsr()->id.'-welcome';
}

/**
 * What the welcome page printed, with the tutorials the API replied with.
 */
function renderedWelcomePage(array $videos = []): string
{
    interceptHttp(['body' => (string) wp_json_encode(['data' => ['videos' => $videos]])]);
    glsr(WelcomeController::class)->restorePageTitle(); // WordPress does this on load-dashboard_page_…
    ob_start();
    glsr(WelcomeController::class)->renderPageCallback();

    return (string) ob_get_clean();
}

/*
 * Registered, then hidden.
 */

test('the page is registered under the dashboard, so that its callback may be run at all', function () {
    // Not cosmetic. wp-admin/admin.php refuses to run the callback of a page that is not
    // registered — the page has to EXIST before it can be hidden.
    //
    // The registration is read back with core's OWN reader, get_plugin_page_hook(), rather than by
    // looking for a literal 'dashboard_page_…' key: that prefix is $admin_page_hooks['index.php'],
    // which only wp-admin/menu.php sets, and wp-admin/menu.php does not run in this process. So
    // the literal hookname here would be an artifact of the test environment, while the question
    // that actually matters — "would WordPress run the callback?" — is the one this asks.
    glsr(WelcomeController::class)->registerPage();

    expect(array_column($GLOBALS['submenu']['index.php'] ?? [], 2))->toContain(welcomeSlug())
        ->and(get_plugin_page_hook(welcomeSlug(), 'index.php'))->not->toBeEmpty();
});

test('it asks for the welcome permission, not for an administrator', function () {
    // add_submenu_page() does not merely record the capability, it refuses to add the page at all
    // when the current user lacks it. So the capability the plugin asks for has to be one its own
    // permission map actually grants — a typo here removes the page for everybody, silently.
    glsr(WelcomeController::class)->registerPage();

    $page = current(array_filter(
        $GLOBALS['submenu']['index.php'] ?? [],
        fn ($item) => welcomeSlug() === $item[2]
    ));

    expect($page[1])->toBe(glsr()->getPermission('welcome'));
});

test('and then it is taken back out of the dashboard menu', function () {
    // The other half. Without this, every site gets a "Site Reviews" item under Dashboard, forever.
    glsr(WelcomeController::class)->registerPage();
    expect(array_column($GLOBALS['submenu']['index.php'], 2))->toContain(welcomeSlug());

    glsr(WelcomeController::class)->removeSubMenu();

    expect(array_column($GLOBALS['submenu']['index.php'], 2))->not->toContain(welcomeSlug());
    // …and this is the assertion the whole controller exists for: the page is GONE from the menu
    // and STILL routable. remove_submenu_page() only unsets the $submenu entry — it leaves the
    // action attached to the page's hook, which is what wp-admin/admin.php looks for.
    expect(get_plugin_page_hook(welcomeSlug(), 'index.php'))->not->toBeEmpty();
});

test('removing the page takes its title with it, so the title is put back by hand', function () {
    // get_admin_page_title() reads the title off the menu entry. Once the entry is gone there is
    // nothing to read, and the page renders with a blank <h1>.
    global $title;

    glsr(WelcomeController::class)->restorePageTitle();

    expect($title)->toBe(sprintf('Welcome to %s', glsr()->name));
});

/*
 * The About link.
 */

test('an About link is added to the plugin row on the plugins screen', function () {
    // How anybody reaches the page a second time. It is the only link to it anywhere.
    $links = glsr(WelcomeController::class)->filterActionLinks(['deactivate' => '<a href="#">Deactivate</a>']);

    expect($links)->toHaveKey('welcome')
        ->and($links['welcome'])->toContain(welcomeSlug())
        ->and($links['welcome'])->toContain('About')
        ->and($links)->toHaveKey('deactivate'); // and the ones already there are kept
});

/*
 * The page itself.
 */

test('the page renders its four tabs, and the version somebody is about to be asked for', function () {
    $rendered = renderedWelcomePage();

    expect($rendered)->toContain('Getting Started')
        ->toContain('What&#039;s New')
        ->toContain('Upgrade Guide')
        ->toContain('Support')
        ->toContain(glsr()->version); // the "Version x.y.z" badge — the first thing support asks for
});

test('an addon can add a tab of its own', function () {
    // `addon/welcome/tabs`. The premium addons put their own getting-started content here.
    add_filter('site-reviews/addon/welcome/tabs', fn ($tabs) => ['getting-started' => 'An Addon Tab']);

    expect(renderedWelcomePage())->toContain('An Addon Tab');
});

test('the tutorial videos come from the API, and are restricted before they reach the view', function () {
    // The data is fetched over HTTP from a server this plugin does not control, and then handed
    // straight to a view that echoes it. TutorialDefaults::restrict() is the only thing between
    // the two — so the assertion that matters is the one about the key that was not asked for.
    $rendered = renderedWelcomePage([
        [
            'duration' => '3:24',
            'id' => 'dQw4w9WgXcQ',
            'title' => 'How to add reviews',
            'onerror' => '<script>alert(1)</script>', // a key VideoDefaults does not know
        ],
    ]);

    expect($rendered)->toContain('dQw4w9WgXcQ')
        ->toContain('How to add reviews')
        ->toContain('3:24')
        ->and($rendered)->not->toContain('alert(1)');
});

test('the page still renders when the API says nothing at all', function () {
    // The API is somebody else's server, and it is down sometimes. A welcome page that fatalled
    // because it could not fetch a video list would be the first thing a new user ever saw.
    $rendered = renderedWelcomePage([]);

    expect($rendered)->toContain('Getting Started')
        ->and($rendered)->not->toContain('glsr-videos'); // the video block is simply absent
});
