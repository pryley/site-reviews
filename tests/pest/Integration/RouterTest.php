<?php

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Router;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;
use GeminiLabs\SiteReviews\Tests\InteractsWithExits;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\mutexLock;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\releaseMutexLock;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class, InteractsWithExits::class);

/*
 * The front door.
 *
 * Every non-REST request the plugin handles arrives at one of the Router's six entry points, each a
 * checkpoint: is this really ours, is it nonced, is it the same visitor submitting twice at once.
 * Only then is it turned into a `route/{type}/{action}` action and handed to a controller.
 *
 * The Router is generic — WHICH controller answers WHICH action is decided in the Hooks classes,
 * asserted in HooksTest. So most tests route a synthetic action ('test-route') with a listener of
 * its own: the subject is the checkpoint, and a real controller would only add noise. Two tests take
 * a real route the whole way through to prove the halves meet.
 *
 * The ajax routes end in wp_send_json_error() then wp_die(), so they run inside the ajax harness
 * (InteractsWithAjax::jsonSentBy()); the admin POST route ends in check_admin_referer(), which calls
 * wp_die() itself, so it needs InteractsWithExits.
 */

beforeEach(function () {
    resetPluginState();
    glsr(Console::class)->clear();
    glsr(Notice::class)->clear(); // Notice is a singleton: errors from an earlier test would still be in it
    set_current_screen('front');  // routePublicPostRequest asks glsr()->isAdmin()
});

afterEach(fn () => $this->tearDownAjax());

/**
 * A listener on a route, and everything it was handed.
 *
 * @return \ArrayObject<int, Request>
 */
function recordRoute(string $hook): ArrayObject
{
    $recorded = new ArrayObject();
    add_action("site-reviews/{$hook}", fn ($request) => $recorded->append($request));

    return $recorded;
}

/**
 * The shape of an ajax request as the plugin's own JS sends it: a WordPress
 * `action` naming the router, and the payload under the plugin's id.
 *
 * Request::inputPost() is what reads this back, and it sets `_ajax_request` from
 * the presence of the WordPress action — which is the only thing telling the
 * Router that this arrived over admin-ajax.php rather than as a form POST.
 */
function ajaxPost(array $values, string $type = 'public'): void
{
    $_POST = [
        'action' => glsr()->prefix.$type.'_action',
        glsr()->id => $values,
    ];
    $_REQUEST = $_POST;
}

/**
 * The shape of a plain form POST. The nonce is a top-level field, because
 * check_admin_referer() reads $_REQUEST['_wpnonce'] rather than the payload.
 */
function formPost(array $values, string $nonce = ''): void
{
    $_POST = [glsr()->id => $values];
    if ('' !== $nonce) {
        $_POST['_wpnonce'] = $nonce;
    }
    $_REQUEST = $_POST;
}

/*
 * The ajax checkpoints.
 */

test('an ajax request without an action is refused', function () {
    $this->setUpAjax();
    ajaxPost(['_nonce' => 'whatever']);

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($response['success'])->toBeFalse()
        ->and($response['data']['code'])->toBe(400)
        ->and($response['data']['error'])->toBe('AJAX request must include an action');
});

test('a form post arriving at the ajax router is refused', function () {
    // `_ajax_request` is set by Request::inputPost() only when the WordPress action
    // is one of the two router actions. Without it, this is a form POST that has
    // somehow reached the ajax handler, and the ajax handler is not going to
    // process it — a request has to be what it says it is.
    $this->setUpAjax();
    $_POST = [glsr()->id => ['_action' => 'test-route']]; // no `action`
    $_REQUEST = $_POST;

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($response['data']['code'])->toBe(400)
        ->and($response['data']['error'])->toBe('AJAX request is invalid');
});

test('a failed review submission says something a visitor can act on', function () {
    // The visitor is not the audience for "AJAX request is invalid". Whatever went
    // wrong, the person who filled the form in gets told the form did not go
    // through and who to tell — the technical error is still in the payload for
    // whoever is looking.
    $this->setUpAjax();
    $_POST = [glsr()->id => ['_action' => 'submit-review']];
    $_REQUEST = $_POST;

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($response['data']['message'])->toContain('could not be submitted')
        ->and($response['data']['error'])->toBe('AJAX request is invalid');
});

test('a guarded ajax action without a nonce is refused', function () {
    $this->setUpAjax();
    ajaxPost(['_action' => 'test-route']);
    $recorded = recordRoute('route/ajax/test-route');

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($response['data']['code'])->toBe(400)
        ->and($response['data']['error'])->toBe('AJAX request is missing a nonce')
        ->and($recorded)->toHaveCount(0); // and it got nowhere near a controller
});

test('a guarded ajax action with the wrong nonce is refused', function () {
    // A nonce for a DIFFERENT action is not a nonce for this one. wp_verify_nonce
    // is keyed by the action, which is what stops a nonce lifted from one form
    // being replayed against another route.
    $this->setUpAjax();
    ajaxPost(['_action' => 'test-route', '_nonce' => wp_create_nonce('some-other-route')]);
    $recorded = recordRoute('route/ajax/test-route');

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($response['data']['code'])->toBe(403)
        ->and($response['data']['error'])->toBe('AJAX request failed the nonce check')
        ->and($recorded)->toHaveCount(0);
});

test('a guarded ajax action with its own nonce is routed', function () {
    $this->setUpAjax();
    ajaxPost(['_action' => 'test-route', '_nonce' => wp_create_nonce('test-route'), 'colour' => 'red']);
    $recorded = recordRoute('route/ajax/test-route');

    $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($recorded)->toHaveCount(1);
    expect($recorded[0])->toBeInstanceOf(Request::class)
        ->and($recorded[0]->colour)->toBe('red'); // the payload arrives with it
});

test('an unguarded ajax action is routed without a nonce', function () {
    // A nonce is tied to a session, and a page served from a cache carries whatever
    // nonce the cache captured — quite possibly somebody else's, quite possibly a
    // stale one. So the public actions a visitor must be able to reach on a cached
    // page are unguarded by design; none of them is destructive.
    $this->setUpAjax();
    add_filter('site-reviews/router/public/unguarded-actions', fn ($actions) => [...$actions, 'test-route']);
    ajaxPost(['_action' => 'test-route']); // no nonce
    $recorded = recordRoute('route/ajax/test-route');

    $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($recorded)->toHaveCount(1);
});

test('the unguarded actions are the four a cached page needs, and the one the admin does', function () {
    // Named, because adding to this list is how a nonce check gets removed, and it
    // should not be possible to do that by accident.
    $public = protectedMethod(Router::class, 'unguardedPublicActions')->invoke(glsr(Router::class));
    $admin = protectedMethod(Router::class, 'unguardedAdminActions')->invoke(glsr(Router::class));

    expect($public)->toBe(['approved-review', 'fetch-paged-reviews', 'submit-review', 'verified-review']);
    expect($admin)->toBe(['dismiss-notice']);
});

test('an admin ajax request is nonced, and its errors are shown as an admin notice', function () {
    // The admin router refuses the same request the public one would, and adds the
    // one thing the admin has that the front end does not: a notice, rendered into
    // the response so the admin JS can put it on the page.
    $this->setUpAjax();
    wp_set_current_user(createUser(['role' => 'administrator']));
    ajaxPost(['_action' => 'test-route', '_nonce' => wp_create_nonce('some-other-route')], 'admin');

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routeAdminAjaxRequest());

    expect($response['data']['code'])->toBe(403)
        ->and($response['data']['notices'])->toContain('There was an error')
        ->and($response['data']['notices'])->toContain('reloading'); // with a way out of it
});

test('an admin ajax action with its own nonce is routed', function () {
    $this->setUpAjax();
    wp_set_current_user(createUser(['role' => 'administrator']));
    ajaxPost(['_action' => 'test-route', '_nonce' => wp_create_nonce('test-route')], 'admin');
    $recorded = recordRoute('route/ajax/test-route');

    $this->jsonSentBy(fn () => glsr(Router::class)->routeAdminAjaxRequest());

    expect($recorded)->toHaveCount(1);
});

test('the ajax router reaches the controller that answers the action', function () {
    // The one that proves the two halves meet: the Router's `route/ajax/{action}`
    // and the table in ToolsHooks that puts ToolsController on the other end of it.
    $this->setUpAjax();
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr_log()->error('an error worth clearing');
    ajaxPost(['_action' => 'clear-console', '_nonce' => wp_create_nonce('clear-console')], 'admin');

    $response = $this->jsonSentBy(fn () => glsr(Router::class)->routeAdminAjaxRequest());

    expect($response['success'])->toBeTrue();
    expect(glsr(Console::class)->get())->toContain('Console is empty');
});

/*
 * The admin form POST, on admin_init.
 */

test('an admin form post with a valid nonce is routed', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    formPost(['_action' => 'test-route'], wp_create_nonce('test-route'));
    $recorded = recordRoute('route/admin/test-route');

    glsr(Router::class)->routeAdminPostRequest();

    expect($recorded)->toHaveCount(1);
});

test('an admin form post with a bad nonce is stopped dead', function () {
    // check_admin_referer() does not return false — it calls wp_die(). Everything
    // after it in routeAdminPostRequest() is unreachable for a bad nonce, which is
    // the point: there is no path from here to a controller.
    wp_set_current_user(createUser(['role' => 'administrator']));
    formPost(['_action' => 'test-route'], wp_create_nonce('some-other-route'));
    $recorded = recordRoute('route/admin/test-route');

    $message = $this->expectsWpDie(fn () => glsr(Router::class)->routeAdminPostRequest());

    expect($message)->toContain('link you followed has expired'); // wp_nonce_ays()
    expect($recorded)->toHaveCount(0);
});

test('admin_init ignores a request that is not the plugin\'s', function () {
    // routeAdminPostRequest runs on EVERY admin request, so the first thing it does
    // is establish that this one is addressed to the plugin at all. Neither of these
    // may reach check_admin_referer(), which would wp_die() on somebody else's POST.
    $recorded = recordRoute('route/admin/test-route');

    $_POST = ['some_other_plugin' => ['_action' => 'test-route']]; // not ours
    $_REQUEST = $_POST;
    glsr(Router::class)->routeAdminPostRequest();

    ajaxPost(['_action' => 'test-route'], 'admin'); // ours, but it is an ajax request
    glsr(Router::class)->routeAdminPostRequest();

    expect($recorded)->toHaveCount(0);
});

test('an admin form post reaches the controller that answers the action', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr_log()->error('an error worth clearing');
    formPost(['_action' => 'clear-console'], wp_create_nonce('clear-console'));

    glsr(Router::class)->routeAdminPostRequest();

    expect(glsr(Console::class)->get())->toContain('Console is empty');
});

/*
 * The public form POST, on init.
 */

test('a public form post from a visitor is routed without a nonce', function () {
    // Same reasoning as the unguarded ajax actions: the page the form was printed on
    // may have come out of a cache, so a logged-out visitor's nonce cannot be
    // trusted to be their own — and is therefore not asked for.
    formPost(['_action' => 'test-route']);
    $recorded = recordRoute('route/public/test-route');

    glsr(Router::class)->routePublicPostRequest();

    expect($recorded)->toHaveCount(1);
});

test('a public form post from a logged-in user is nonced', function () {
    // A logged-in visitor is not served from the page cache, so their nonce IS their
    // own and can be insisted on.
    wp_set_current_user(createUser(['role' => 'subscriber']));
    formPost(['_action' => 'test-route', '_nonce' => wp_create_nonce('some-other-route')]);
    $recorded = recordRoute('route/public/test-route');

    glsr(Router::class)->routePublicPostRequest();

    expect($recorded)->toHaveCount(0);
    expect(glsr(Console::class)->get())->toContain('nonce check failed for public request');

    formPost(['_action' => 'test-route', '_nonce' => wp_create_nonce('test-route')]);
    glsr(Router::class)->routePublicPostRequest();

    expect($recorded)->toHaveCount(1);
});

test('the public router does not run in the admin', function () {
    // `init` fires in the admin too, and an admin POST is routeAdminPostRequest's to
    // handle — with a nonce check that this one does not make. Running both would
    // route the same request twice, the second time unguarded.
    set_current_screen('edit.php'); // is_admin()
    formPost(['_action' => 'test-route']);
    $recorded = recordRoute('route/public/test-route');

    glsr(Router::class)->routePublicPostRequest();

    expect($recorded)->toHaveCount(0);
});

/*
 * The GET routes.
 *
 * These carry their action and data in one encrypted token — /wp-admin/?glsr_=…
 * for the admin, ?glsr_=… for the front end — so that the link in an email cannot
 * be edited into a link that approves a different review.
 *
 * Request::inputGet() reads that token with filter_input(INPUT_GET), which reads
 * the SAPI's own copy of the query string. A CLI process does not have one and
 * nothing can put anything into it, so the token cannot be planted from a test and
 * these two entry points can only be driven as far as "no token, nothing happens".
 * What they do WITH a token is Router::get(), which is called directly below.
 */

test('a request with no token is not a routed request', function () {
    $recorded = recordRoute('route/get/public/test-route');

    glsr(Router::class)->routePublicGetRequest();
    glsr(Router::class)->routeAdminGetRequest();

    expect($recorded)->toHaveCount(0);
});

test('a get route is dispatched with the data decoded from its token', function () {
    // A synthetic action, not the real `verify` one: the real one has
    // VerificationController on the other end of it, and that ends in a redirect.
    $recorded = recordRoute('route/get/public/test-route');

    protectedMethod(Router::class, 'get')->invoke(
        glsr(Router::class), 'public', new Request(['action' => 'test-route', 'data' => [123]])
    );

    expect($recorded)->toHaveCount(1);
    expect($recorded[0]->data)->toBe([123]);
});

test('a get token cannot be read or forged without the site\'s keys', function () {
    // The whole reason the GET routes carry a token rather than ?action=approve&id=5:
    // the review id is inside the ciphertext, so an approval link mailed to a
    // moderator cannot be edited into an approval of somebody else's review.
    $token = glsr(Encryption::class)->encryptRequest('approve', [42]);

    expect($token)->not->toContain('approve');
    expect(glsr(Encryption::class)->decryptRequest($token))
        ->toBe(['action' => 'approve', 'data' => ['42']]);

    // and a token that has been meddled with opens as nothing at all, rather than as
    // a partially-decoded request — the secretbox MAC fails before any of it is read.
    expect(glsr(Encryption::class)->decryptRequest('tampered'.$token))->toBe([]);
});

/*
 * The mutex.
 */

/**
 * A submission that gets as far as the mutex. The nonce and `_ajax_request` matter: the
 * mutex is the LAST checkpoint in routePublicAjaxRequest(), so a request that fails any of
 * the earlier ones never reaches it.
 */
function submitReviewAjax(): array
{
    ajaxPost([
        '_action' => 'submit-review',
        '_nonce' => wp_create_nonce('submit-review'),
    ]);

    return test()->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());
}

test('only the review submission is protected against parallel requests', function () {
    // Everything else is cheap enough, or read-only. The list is asserted by name because
    // adding to it makes a route slower for every visitor, and removing from it is how the
    // protection below gets switched off by accident.
    $actions = protectedMethod(Router::class, 'mutexActions')->invoke(glsr(Router::class));

    expect($actions)->toBe(['submit-review']);
});

test('a second submission arriving in the same moment is refused', function () {
    // THE POINT OF THE MUTEX. Two submissions in the same TCP packet would otherwise both
    // clear the duplicate check before either had been written, and the site owner would
    // get the same review twice — which is the cheapest way there is to flood a site.
    $this->setUpAjax();
    releaseMutexLock(); // nothing is holding it: this is the first request

    $first = submitReviewAjax();
    $second = submitReviewAjax(); // and the lock is still down

    expect($first['data']['error'] ?? '')->not->toBe('Parallel AJAX request (possible single-packet attack)');
    expect($second['success'])->toBeFalse()
        ->and($second['data']['code'])->toBe(429)
        ->and($second['data']['error'])->toBe('Parallel AJAX request (possible single-packet attack)');

    // and the visitor is not shown "Parallel AJAX request (possible single-packet attack)"
    expect($second['data']['message'])->toContain('could not be submitted');
});

test('the same visitor may submit again once the lock has gone', function () {
    // The lock is a five-second transient, not a ban. A person who submits a review for
    // one product and then another for the next must not be turned away.
    $this->setUpAjax();
    releaseMutexLock();

    submitReviewAjax();
    releaseMutexLock(); // i.e. five seconds pass

    $again = submitReviewAjax();

    expect($again['data']['error'] ?? '')->not->toBe('Parallel AJAX request (possible single-packet attack)');
});

test('the lock is dropped on a hash of the ip address, not the address itself', function () {
    // The options table is not the place to keep a record of who visited and when.
    $this->setUpAjax();
    releaseMutexLock();

    submitReviewAjax();

    expect(get_transient(mutexLock()))->not->toBeFalse() // it is down
        ->and(mutexLock())->not->toContain(Helper::clientIp());
});

test('a route that is not in the mutex list is never locked', function () {
    // fetch-paged-reviews is what pagination calls, and a visitor clicking through pages
    // faster than the lock expires must not be told they are attacking the site.
    $this->setUpAjax();
    add_filter('site-reviews/router/public/unguarded-actions', fn ($actions) => [...$actions, 'test-route']);
    $recorded = recordRoute('route/ajax/test-route');

    ajaxPost(['_action' => 'test-route']);
    $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());
    ajaxPost(['_action' => 'test-route']);
    $this->jsonSentBy(fn () => glsr(Router::class)->routePublicAjaxRequest());

    expect($recorded)->toHaveCount(2);
    expect(get_transient(mutexLock()))->toBeFalse(); // no lock was ever taken
});

test('how long the lock is held can be filtered', function () {
    // A site behind a proxy that presents one IP for every visitor would lock its whole
    // audience out of each other's submissions, so the expiration is a knob.
    $this->setUpAjax();
    releaseMutexLock();
    add_filter('site-reviews/router/mutex/expiration', fn () => 60);

    submitReviewAjax();

    $timeout = (int) get_option('_transient_timeout_'.mutexLock());
    expect($timeout)->toBeGreaterThan(time() + 30);
});

/*
 * What every route announces.
 */

test('an action nobody answers is logged', function () {
    // A request for a route that does not exist is a typo in the admin JS, or an
    // addon that has been deactivated with its markup still on the page. Either way
    // the request does nothing, and the console is the only place that can say so.
    //
    // The question cannot be asked after the fact: do_action_ref_array() increments
    // $wp_actions BEFORE it looks for callbacks, so did_action() comes back 1 whether
    // anything was listening or not — which is why Router::isRouted() asks
    // has_action() beforehand instead.
    formPost(['_action' => 'nobody-answers-this']);

    glsr(Router::class)->routePublicPostRequest();

    expect(glsr(Console::class)->get())
        ->toContain('Unknown public router POST request: nobody-answers-this');
    // and the hook is not fired at all: there is nothing to dispatch to, and firing it
    // would only announce a route that does not exist through site-reviews/action.
    expect(did_action('site-reviews/route/public/nobody-answers-this'))->toBe(0);
});

test('an action that is answered is not logged', function () {
    formPost(['_action' => 'test-route']);
    recordRoute('route/public/test-route');

    glsr(Router::class)->routePublicPostRequest();

    expect(glsr(Console::class)->get())->not->toContain('Unknown public router');
});

test('a get request for a route that does not exist is logged', function () {
    protectedMethod(Router::class, 'get')->invoke(
        glsr(Router::class), 'public', new Request(['action' => 'nobody-answers-this'])
    );

    expect(glsr(Console::class)->get())
        ->toContain('Unknown public router GET request: nobody-answers-this');
});

test('every routed request is announced before it is dispatched', function () {
    // `route/request` is how an addon sees a request it has no route of its own for
    // — it fires for every request the Router accepts, ajax and form alike, and is
    // told which hook is about to be fired.
    // It fires BEFORE the router asks whether anything is listening, which is what
    // lets an addon register its own route from inside it (Router::isRouted).
    $announced = new ArrayObject();
    add_action('site-reviews/route/request', fn ($request, $hook) => $announced->append($hook), 10, 2);

    formPost(['_action' => 'test-route']);
    $recorded = recordRoute('route/public/test-route');
    glsr(Router::class)->routePublicPostRequest();

    expect($announced->getArrayCopy())->toBe(['route/public/test-route']);
    expect($recorded)->toHaveCount(1);
});

test('an addon can register its route from the announcement, and still be routed', function () {
    // The order the two hooks fire in is a contract: route/request, then the check,
    // then the dispatch. Register the route any later and the request is logged as
    // unknown and dropped.
    $recorded = new ArrayObject();
    add_action('site-reviews/route/request', function () use ($recorded) {
        add_action('site-reviews/route/public/late-route', fn ($request) => $recorded->append($request));
    });

    formPost(['_action' => 'late-route']);
    glsr(Router::class)->routePublicPostRequest();

    expect($recorded)->toHaveCount(1);
    expect(glsr(Console::class)->get())->not->toContain('Unknown public router');
});
