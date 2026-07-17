<?php

use GeminiLabs\SiteReviews\Controllers\NoticeController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Notices\AbstractNotice;
use GeminiLabs\SiteReviews\Notices\GatekeeperNotice;
use GeminiLabs\SiteReviews\Notices\LicenseExpiredNotice;
use GeminiLabs\SiteReviews\Notices\LicenseMissingNotice;
use GeminiLabs\SiteReviews\Notices\LicensePromotedNotice;
use GeminiLabs\SiteReviews\Notices\MigrationNotice;
use GeminiLabs\SiteReviews\Notices\RetiredFreeNotice;
use GeminiLabs\SiteReviews\Notices\RetiredPremiumNotice;
use GeminiLabs\SiteReviews\Notices\UpgradedNotice;
use GeminiLabs\SiteReviews\Notices\WelcomeNotice;
use GeminiLabs\SiteReviews\Notices\WriteReviewNotice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;
use GeminiLabs\SiteReviews\Tests\InteractsWithAjax;
use GeminiLabs\SiteReviews\Tests\NullQueue;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses(InteractsWithAjax::class);

/*
 * The admin notices.
 *
 * A notice decides everything in its CONSTRUCTOR: whether the person may see it, whether this is a
 * screen it belongs on, and whether they have dismissed it — only then does it hook onto
 * admin_notices. So "does it load" and "what does it look like" are separate questions, and a notice
 * that did not load will still draw itself if you call render() by hand — which is why these tests
 * ask has_action() rather than just rendering.
 *
 * Dismissal is per USER, in their meta, not an option: two admins dismiss their own, and one
 * clearing theirs does not clear the other's. It is not always forever — it can expire after an
 * interval (WriteReviewNotice, a month) or on the next version (WelcomeNotice): "not now" vs "never".
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    glsr()->store('notices', []); // the plugin's in-memory store is not rolled back
    onAReviewScreen();
});

afterEach(fn () => set_current_screen('front'));

function onAReviewScreen(): void
{
    set_current_screen('edit-'.glsr()->post_type);
}

/**
 * Whether the notice hooked itself onto admin_notices, which is the only thing that
 * decides whether WordPress will ever draw it.
 */
function noticeLoaded(AbstractNotice $notice): bool
{
    return false !== has_action('admin_notices', [$notice, 'render']);
}

function renderedNotice(AbstractNotice $notice): string
{
    ob_start();
    $notice->render();

    return (string) ob_get_clean();
}

/**
 * What the current user has told to go away, straight out of their meta.
 *
 * Not `(array) get_user_meta(…)`: a user with no meta gets back an empty STRING, and
 * casting that to an array gives [''] rather than []. Which is exactly why the plugin
 * itself runs it through Arr::consolidate().
 */
function dismissedNotices(): array
{
    $dismissed = get_user_meta(get_current_user_id(), AbstractNotice::USER_META_KEY, true);

    return is_array($dismissed) ? $dismissed : [];
}

/*
 * Which notice a site sees.
 */

test('a new site is welcomed, and a site that has just been updated is not', function () {
    // version_upgraded_from is '0.0.0' on a site that has never had an older version of
    // the plugin on it. The two notices are exact opposites, and showing both would be
    // welcoming somebody who has been here for years.
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');

    expect(noticeLoaded(new WelcomeNotice()))->toBeTrue();
    expect(noticeLoaded(new UpgradedNotice()))->toBeFalse();
});

test('a site that has just been updated is told so, and is not welcomed', function () {
    glsr(OptionManager::class)->set('version_upgraded_from', '7.0.0');

    expect(noticeLoaded(new UpgradedNotice()))->toBeTrue();
    expect(noticeLoaded(new WelcomeNotice()))->toBeFalse();
});

test('a notice is only shown on the plugin\'s own screens', function () {
    // admin_notices fires on every admin page there is. A plugin that draws its notice on
    // all of them is a plugin people learn to ignore.
    set_current_screen('edit-post');

    expect(noticeLoaded(new WelcomeNotice()))->toBeFalse();

    onAReviewScreen();

    expect(noticeLoaded(new WelcomeNotice()))->toBeTrue();
});

test('a notice is only shown to somebody it is meant for', function () {
    // Each notice names the capability it needs (PermissionDefaults `notices`). The
    // upgrade notice wants update_plugins, which an editor does not have — telling them
    // the plugin has been updated is telling them about something they cannot act on.
    glsr(OptionManager::class)->set('version_upgraded_from', '7.0.0');

    wp_set_current_user(createUser(['role' => 'editor']));
    expect(noticeLoaded(new UpgradedNotice()))->toBeFalse();

    wp_set_current_user(createUser(['role' => 'administrator']));
    expect(noticeLoaded(new UpgradedNotice()))->toBeTrue();
});

/*
 * Being told to go away.
 */

test('a notice that has been dismissed does not come back', function () {
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');

    (new WelcomeNotice())->dismiss();

    expect(dismissedNotices())->toHaveKey('welcome');
    expect(noticeLoaded(new WelcomeNotice()))->toBeFalse();
});

test('one person dismissing a notice does not dismiss it for everybody', function () {
    // It is user meta, not an option, and this is why: an agency administrator clicking
    // "got it" must not take the notice away from the client who has not read it.
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');
    (new WelcomeNotice())->dismiss();

    wp_set_current_user(createUser(['role' => 'administrator']));

    expect(dismissedNotices())->toBe([]);
    expect(noticeLoaded(new WelcomeNotice()))->toBeTrue();
});

test('a notice nobody is keeping track of cannot be dismissed', function () {
    // dismiss() writes nothing for a notice that is not monitored. GatekeeperNotice is
    // one: it takes itself away by deleting its transient, so recording a dismissal
    // nothing ever reads would only grow the user's meta.
    (new GatekeeperNotice())->dismiss();

    expect(dismissedNotices())->toBe([]);
});

test('a notice dismissed for a while comes back after a while', function () {
    // WriteReviewNotice's interval is a month: "not now" rather than "never". The
    // dismissal is stored with the timestamp it was made at, and read back against the
    // interval — so a stored timestamp older than the interval is a dismissal that has
    // run out.
    (new WriteReviewNotice())->dismiss(['timestamp' => current_time('timestamp'), 'version' => '']);
    expect(noticeLoaded(new WriteReviewNotice()))->toBeFalse();

    (new WriteReviewNotice())->dismiss([
        'timestamp' => current_time('timestamp') - (MONTH_IN_SECONDS + 1),
        'version' => '',
    ]);

    expect(noticeLoaded(new WriteReviewNotice()))->toBeTrue();
});

test('a notice dismissed for a version comes back for the next one', function () {
    // WelcomeNotice defers on the version rather than on a clock. Dismissed at 7.0, it is
    // back when the plugin is 8.x — which is the point: the notice is about THIS version.
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');

    (new WelcomeNotice())->dismiss(['version' => '7.0']);
    expect(noticeLoaded(new WelcomeNotice()))->toBeTrue(); // the plugin has moved on since

    (new WelcomeNotice())->dismiss(['version' => glsr()->version('minor')]);
    expect(noticeLoaded(new WelcomeNotice()))->toBeFalse(); // and this is the current one
});

test('the write-review notice waits a week before it asks for anything', function () {
    // Nobody wants to be asked for a five-star review by a plugin they installed forty
    // seconds ago. The first time it would be drawn it silently defers itself instead,
    // backdating the dismissal so that it comes back in a week rather than a month.
    expect(dismissedNotices())->toBe([]);
    $before = current_time('timestamp');

    $notice = new WriteReviewNotice();
    expect(renderedNotice($notice))->toBe(''); // not this time

    // Backdated by exactly MONTH_IN_SECONDS - WEEK_IN_SECONDS, so the month-long defer
    // interval has a week left to run. Pinned within the second or two the test itself takes.
    expect(dismissedNotices())->toHaveKey('write-review');
    $timestamp = dismissedNotices()['write-review']['timestamp'];
    expect($timestamp)->toBeGreaterThanOrEqual($before - (MONTH_IN_SECONDS - WEEK_IN_SECONDS))
        ->toBeLessThanOrEqual(current_time('timestamp') - (MONTH_IN_SECONDS - WEEK_IN_SECONDS));
});

/*
 * What it looks like.
 */

test('a notice is drawn as one, and says which one it is', function () {
    // data-notice is what the dismiss button posts back, so that the right notice is the
    // one dismissed.
    glsr(OptionManager::class)->set('version_upgraded_from', '7.0.0');

    $html = renderedNotice(new UpgradedNotice());

    expect($html)->toContain('glsr-notice')
        ->toContain('data-notice="'.UpgradedNotice::class.'"');
});

test('the class on a notice says what kind it is, and whether it can be dismissed', function () {
    // WordPress draws the × from the `is-dismissible` class. Putting it on a notice that
    // records no dismissal would be giving somebody a button that does nothing but hide
    // the notice until the page reloads.
    $classAttr = fn (AbstractNotice $notice) => protectedMethod(get_class($notice), 'classAttr')
        ->invoke($notice);

    expect($classAttr(new WelcomeNotice()))       // a popup, and not dismissible
        ->toContain('glsr-notice-popup')
        ->not->toContain('is-dismissible');

    expect($classAttr(new GatekeeperNotice()))    // an ordinary error notice, dismissible
        ->toContain('notice notice-error')
        ->toContain('is-dismissible');
});

test('only one popup is drawn at a time', function () {
    // Two popups on one screen is two modal dialogs on top of each other. The first one
    // to render marks itself, and the rest stand down.
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');
    $welcome = new WelcomeNotice();  // popup, priority 0
    (new WriteReviewNotice())->dismiss([
        'timestamp' => current_time('timestamp') - (MONTH_IN_SECONDS + 1),
        'version' => '',
    ]);
    $writeReview = new WriteReviewNotice(); // popup, priority 50

    expect(renderedNotice($welcome))->not->toBeEmpty();
    expect(renderedNotice($writeReview))->toBe('');
});

/*
 * The controller behind them.
 */

test('every notice the plugin ships is given the chance to load', function () {
    // The notices are found by reading the directory, so a new one is picked up by
    // existing. Nothing registers them by name, and nothing has to be remembered.
    glsr()->store('notices', []);
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');

    glsr(NoticeController::class)->adminNotices();

    $loaded = glsr()->retrieveAs('array', 'notices');

    expect($loaded)->toHaveKey('welcome')
        ->and($loaded)->not->toHaveKey('upgraded'); // it looked, and decided not to
    expect($loaded['welcome']['type'])->toBe('popup');
});

test('a notice can be dismissed from the page it is on', function () {
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');

    glsr(NoticeController::class)->dismissNotice(new Request(['notice' => WelcomeNotice::class]));

    expect(dismissedNotices())->toHaveKey('welcome');
});

test('a notice can be dismissed over ajax, and the dismissal is reported', function () {
    // The ajax twin of the route above — the same dismissal, wrapped in wp_send_json_success so the
    // notice's own "dismiss" button can fire it without a page reload.
    glsr(OptionManager::class)->set('version_upgraded_from', '0.0.0');
    $this->setUpAjax();

    try {
        $response = $this->jsonSentBy(fn () => glsr(NoticeController::class)->dismissNoticeAjax(
            new Request(['notice' => WelcomeNotice::class])
        ));
    } finally {
        $this->tearDownAjax();
    }

    expect($response['success'])->toBeTrue()
        ->and(dismissedNotices())->toHaveKey('welcome');
});

test('a notice dismissed "for now" is dismissed for now, not for the version', function () {
    // The two buttons on the write-review popup: "remind me later" clears the version so
    // that only the interval holds it back, and "no thanks" leaves the version in place.
    glsr(NoticeController::class)->dismissNotice(new Request([
        'dismiss' => 'interval',
        'notice' => WriteReviewNotice::class,
    ]));

    expect(dismissedNotices()['write-review']['version'])->toBe('');
});

test('the write-review popup carries its icon and cannot be closed by the X', function () {
    // It is a popup, not a banner: it has an icon rather than a heading, and the corner dismiss is
    // turned off so the person answers its buttons ("remind me later" / "no thanks") rather than
    // flicking it away — which is what its own dismiss handling relies on.
    $notice = glsr(WriteReviewNotice::class);

    expect((fn () => $this->data())->call($notice))->toHaveKey('icon')
        ->and((fn () => $this->isDismissible())->call($notice))->toBeFalse();
});

test('a dismissal for something that is not a notice dismisses nothing', function () {
    // Nothing is recorded, which is the behaviour that matters here — but note HOW.
    // dismissNotice() guards on class_exists() alone, so a class name the browser made up
    // is refused, while a class name that happens to exist is CONSTRUCTED, through the
    // container, by reflection. WP_Query survives it only because WP_Query has a __call()
    // that shrugs at an unknown method; another class would raise an Error.
    //
    // See the note in ROADMAP.md: this route is in Router::unguardedAdminActions(), so it
    // takes no nonce, and any logged-in user can reach it.
    glsr(NoticeController::class)->dismissNotice(new Request(['notice' => 'NotAClass']));
    glsr(NoticeController::class)->dismissNotice(new Request(['notice' => 'WP_Query']));

    expect(dismissedNotices())->toBe([]);
});

test('the notices on a review screen are caught before wordpress moves them', function () {
    // WordPress hoists admin notices to the top of the page with JS, and they flicker
    // where they were first printed on the way. The pair of injections wraps them in a
    // hidden div so that they do not — and only on the plugin's own screens, because
    // leaving an unclosed <div> on somebody else's would break their page.
    ob_start();
    glsr(NoticeController::class)->injectBeforeNotices();
    glsr(NoticeController::class)->injectAfterNotices();
    $ours = (string) ob_get_clean();

    expect($ours)->toBe('<div id="glsr-notice-catcher"></div>');

    set_current_screen('edit-post');

    ob_start();
    glsr(NoticeController::class)->injectBeforeNotices();
    glsr(NoticeController::class)->injectAfterNotices();

    expect((string) ob_get_clean())->toBe('');
});

/*
 * The three notices about addons: two that warn, and one that sells.
 *
 * All three are driven by a register in the container rather than by a setting, and each register
 * is filled at boot by Application::register() as the addons announce themselves:
 *
 *   retired                an addon that has been RETIRED — its features are now in the free
 *                          plugin, and leaving it active will conflict with them.
 *   site-reviews-premium   the old separately-sold premium addons, superseded by the bundled
 *                          Premium plugin. Registered, then turned away.
 *   licensed               the paid addons that are installed. If it is EMPTY, this is a free
 *                          site, and the promotion banner is the one thing that shows.
 *
 * The two retirement notices are the only notices in the plugin that are NOT dismissible and that
 * appear OUTSIDE the review screens — on the dashboard, the plugins page and the updates page. Both
 * are deliberate: a retired addon actively breaks the site it is installed on, and the person who
 * needs to hear that may have no reason to open the reviews screen for weeks.
 */

/**
 * A retired addon, as Application::register() records one: the addon's CLASS, not its name — the
 * view reads ::ID and ::NAME off it. Optionally active, which is the only state it can be nagged
 * about, because the nag is a button that deactivates it.
 */
function retiredAddon(string $register, bool $isActive = true): void
{
    'retired' === $register
        ? glsr()->store('retired', [TestAddon::ID => TestAddon::class])
        : glsr()->store('site-reviews-premium', [TestAddon::class]);
    if ($isActive) {
        update_option('active_plugins', [sprintf('%1$s/%1$s.php', TestAddon::ID)]);
    }
}

test('a retired free addon is reported, wherever the person happens to be', function () {
    // On the plugins screen — where they are about to be, because that is where you go to
    // deactivate the thing you have just been told to deactivate. And it is named, with a button
    // that does it for them: "an addon has been merged" is not an instruction anybody can act on.
    retiredAddon('retired');
    set_current_screen('plugins');

    $notice = new RetiredFreeNotice();

    expect(noticeLoaded($notice))->toBeTrue();
    expect(renderedNotice($notice))
        ->toContain(TestAddon::NAME)
        ->toContain('has been merged into Site Reviews')
        ->toContain('Deactivate '.TestAddon::NAME);
});

test('a retired addon that is already deactivated is not nagged about', function () {
    // Registered but not active — which is the state of somebody who has already done what they
    // were told. The notice loads (the register still holds it) and then has nothing to say.
    retiredAddon('retired', isActive: false);

    expect(renderedNotice(new RetiredFreeNotice()))
        ->not->toContain('has been merged into Site Reviews');
});

test('a retirement notice cannot be dismissed, because the problem does not go away', function () {
    // Every other notice in the plugin is dismissible. This one is not: dismissing it would hide
    // a live conflict, and the site would keep misbehaving with nothing on screen to say why.
    retiredAddon('retired');

    expect(renderedNotice(new RetiredFreeNotice()))
        ->not->toContain('is-dismissible')
        ->not->toContain('notice-dismiss');
});

test('a site with no retired addons — which is nearly all of them — sees nothing', function () {
    // The register is empty on any site that is up to date, and the notice must not even hook
    // itself on. This runs on every admin page load of every install.
    expect(glsr()->retrieveAs('array', 'retired'))->toBe([]);

    expect(noticeLoaded(new RetiredFreeNotice()))->toBeFalse();
});

test('an old separately-sold premium addon is reported too, and as a warning rather than an error', function () {
    // A different register and a different severity: the old premium addons are superseded, not
    // broken, so this is a warning where the retired-free one is an error. It is also shown on
    // the dashboard — the one screen every administrator does open.
    retiredAddon('site-reviews-premium');
    set_current_screen('dashboard');

    $notice = new RetiredPremiumNotice();

    expect(noticeLoaded($notice))->toBeTrue();
    expect(renderedNotice($notice))
        ->toContain('notice-warning')
        ->toContain(TestAddon::NAME);
});

test('and a site with none of those sees nothing either', function () {
    expect(noticeLoaded(new RetiredPremiumNotice()))->toBeFalse();
});

/*
 * The one that sells.
 */

test('a free site is shown the premium banner', function () {
    // `licensed` is empty, which is what a site with no paid addons looks like — most of them.
    expect(renderedNotice(new LicensePromotedNotice()))
        ->toContain('glsr-notice-banner')
        ->toContain(LicensePromotedNotice::class);
});

test('a site that has already paid is not sold to', function () {
    // The inverse, and the one that matters to the people who have given Paul money: a customer
    // with a licensed addon installed must never see an advertisement for it again.
    glsr()->append('licensed', ['name' => 'Site Reviews: Images'], 'site-reviews-images');

    expect(renderedNotice(new LicensePromotedNotice()))->toBe('');
});

test('the premium page does not advertise premium', function () {
    // canLoad() refuses on any screen whose base ends in `-premium`. An upsell banner across the
    // top of the page that already sells the thing is the plugin talking over itself.
    set_current_screen('site-review_page_glsr-premium');

    expect(noticeLoaded(new LicensePromotedNotice()))->toBeFalse();
});

test('dismissing the premium banner buys three weeks of quiet, not silence forever', function () {
    // deferInterval() is three weeks, so this is a "not now" rather than a "never" — the notice is
    // monitored, the dismissal is stored against the USER, and it lapses. A promotion that could
    // be dismissed permanently would be a promotion nobody ever saw twice; one that could not be
    // dismissed at all would be an advertisement, and this is a plugin, not a billboard.
    $notice = new LicensePromotedNotice();
    expect(noticeLoaded($notice))->toBeTrue();

    $notice->dismiss();

    expect(dismissedNotices())->toHaveKey('license-promoted');
    expect(noticeLoaded(new LicensePromotedNotice()))->toBeFalse(); // and it stays down…

    // …until the interval has passed. Backdating the dismissal is the only way to say "three
    // weeks from now" without waiting three weeks.
    $dismissed = dismissedNotices();
    $dismissed['license-promoted']['timestamp'] = current_time('timestamp') - (4 * WEEK_IN_SECONDS);
    update_user_meta(get_current_user_id(), AbstractNotice::USER_META_KEY, $dismissed);

    expect(noticeLoaded(new LicensePromotedNotice()))->toBeTrue();
});

/*
 * The Gatekeeper notice: the integrations that could not be switched on, and what to do about each.
 * It reads the errors the Gatekeeper left in a transient and, for every plugin, offers the action
 * that would fix it — activate, install or update — to whoever has the capability for it.
 */

function gatekeeperError(string $error, string $name, string $plugin, string $textdomain): array
{
    return [$plugin => [
        'error' => $error,
        'name' => $name,
        'plugin_uri' => "https://example.org/{$textdomain}",
        'textdomain' => $textdomain,
    ]];
}

test('the gatekeeper notice offers to activate, install and update the plugins an integration needs', function () {
    set_current_screen('plugins');
    wp_set_current_user(createUser(['role' => 'administrator'])); // has activate/install/update_plugins
    set_transient(glsr()->prefix.'gatekeeper', array_merge(
        gatekeeperError(Gatekeeper::ERROR_NOT_ACTIVATED, 'Plugin A', 'a/a.php', 'a'),
        gatekeeperError(Gatekeeper::ERROR_NOT_INSTALLED, 'Plugin B', 'b/b.php', 'b'),
        gatekeeperError(Gatekeeper::ERROR_NOT_SUPPORTED, 'Plugin C', 'c/c.php', 'c'),
    ));

    $notice = new GatekeeperNotice(); // canLoad() reads the transient into $this->errors
    $data = (fn () => $this->data())->call($notice);

    expect($data['message'])->toContain('requires the latest version')
        ->and($data['message'])->toContain('Plugin A');
    expect($data['actions'])->toContain('Activate')
        ->and($data['actions'])->toContain('Install')
        ->and($data['actions'])->toContain('Update');
});

test('the gatekeeper notice asks only for an update when an integration is merely untested', function () {
    set_current_screen('plugins');
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_transient(glsr()->prefix.'gatekeeper',
        gatekeeperError(Gatekeeper::ERROR_NOT_TESTED, 'Plugin D', 'd/d.php', 'd')
    );

    $notice = new GatekeeperNotice();
    $data = (fn () => $this->data())->call($notice);

    // the "needs an update to work with" wording, and no button — there is no action for "untested"
    expect($data['message'])->toContain('needs an update')
        ->and($data['message'])->toContain('Plugin D')
        ->and($data['actions'])->toBe('');
});

/*
 * The licence banners, which turn on and off with the licence server's answer. That answer is faked
 * here so the tests never leave the machine: the notices only ever read License::status().
 */

function withLicenseStatus(array $status, callable $callback): void
{
    $original = glsr(License::class);
    $fake = new class() extends License {
        public array $fakeStatus = [];

        public function status(): array
        {
            return $this->fakeStatus;
        }
    };
    $fake->fakeStatus = wp_parse_args($status, array_fill_keys(['expired', 'invalid', 'licensed', 'missing', 'premium'], false));
    glsr()->alias(License::class, $fake);
    try {
        $callback();
    } finally {
        glsr()->alias(License::class, $original);
    }
}

test('the licence-expired banner shows once a licence has lapsed, and not before', function () {
    withLicenseStatus(['licensed' => true, 'expired' => true], function () {
        expect(renderedNotice(new LicenseExpiredNotice()))->toContain('glsr-notice');
    });
    withLicenseStatus(['licensed' => true, 'expired' => false], function () {
        expect(renderedNotice(new LicenseExpiredNotice()))->toBe(''); // licensed, nothing expired
    });
    withLicenseStatus(['licensed' => false], function () {
        expect(renderedNotice(new LicenseExpiredNotice()))->toBe(''); // unlicensed, nothing to expire
    });
});

test('the licence-missing banner shows when a key is absent, and not otherwise', function () {
    withLicenseStatus(['licensed' => true, 'missing' => true], function () {
        expect(renderedNotice(new LicenseMissingNotice()))->toContain('glsr-notice');
    });
    withLicenseStatus(['licensed' => true, 'missing' => false], function () {
        expect(renderedNotice(new LicenseMissingNotice()))->toBe(''); // licensed, nothing missing
    });
});

test('the licence-promoted banner is for the UNLICENSED, so a licensed site never sees it', function () {
    withLicenseStatus(['licensed' => true], function () {
        expect(renderedNotice(new LicensePromotedNotice()))->toBe('');
    });
});

test('each version-deferred notice defers to the right granularity', function () {
    // deferVersion is what dismiss() records so a "not this version" dismissal knows when to lapse:
    // the banners come back on the next MINOR, the upgrade popup on the next MAJOR.
    expect((fn () => $this->deferVersion())->call(new LicenseExpiredNotice()))->toBe(glsr()->version('minor'))
        ->and((fn () => $this->deferVersion())->call(new LicenseMissingNotice()))->toBe(glsr()->version('minor'))
        ->and((fn () => $this->deferVersion())->call(new LicensePromotedNotice()))->toBe(glsr()->version('minor'))
        ->and((fn () => $this->deferVersion())->call(new UpgradedNotice()))->toBe(glsr()->version('major'));
});

/*
 * The retired-addon notices, and the migration notice.
 */

test('the retired-addon notices fall back to the plugin screens when off the admin dashboard', function () {
    // isNoticeScreen short-circuits to true on the dashboard/plugins/update-core screens; on the
    // plugin's own screens it defers to the base rule (which is also true there).
    onAReviewScreen();

    expect((fn () => $this->isNoticeScreen())->call(new RetiredFreeNotice()))->toBeTrue()
        ->and((fn () => $this->isNoticeScreen())->call(new RetiredPremiumNotice()))->toBeTrue();
});

test('the migration notice stays down when nothing needs migrating', function () {
    // A freshly-migrated database with no queued migration has nothing to announce.
    onAReviewScreen();

    expect((fn () => $this->canLoad())->call(new MigrationNotice()))->toBeFalse();
});

test('the migration notice loads while a migration is already queued', function () {
    onAReviewScreen();
    NullQueue::$isPending = true;

    try {
        expect((fn () => $this->canLoad())->call(new MigrationNotice()))->toBeTrue();
    } finally {
        NullQueue::$isPending = false;
    }
});

/*
 * The controller's two odd jobs: the "activate it for me" link some notices carry, and
 * surviving a broken Notices directory.
 */

test('the notice activation link activates the plugin and goes back where it came from', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $_GET = ['action' => 'activate', 'plugin' => 'hello.php', 'trigger' => 'notice'];
    $_REQUEST['_wpnonce'] = wp_create_nonce('activate-plugin_hello.php');
    $redirect = function ($location) {
        throw new Exception("redirected|{$location}");
    };
    add_filter('wp_redirect', $redirect);
    $outcome = '';
    try {
        glsr(NoticeController::class)->activatePlugin();
    } catch (Exception $e) {
        $outcome = $e->getMessage();
    } finally {
        remove_filter('wp_redirect', $redirect);
        deactivate_plugins('hello.php', true);
        $_GET = [];
        unset($_REQUEST['_wpnonce']);
    }

    expect($outcome)->toStartWith('redirected|');
    // the option write rolls back; what matters is the redirect fired INSTEAD of a wp_die
});

test('an activation link for a plugin that is not there dies with the reason', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $_GET = ['action' => 'activate', 'plugin' => 'nope/nope.php', 'trigger' => 'notice'];
    $_REQUEST['_wpnonce'] = wp_create_nonce('activate-plugin_nope/nope.php');
    add_filter('wp_die_handler', fn () => function ($message) {
        throw new Exception('died|'.(is_scalar($message) ? $message : ''));
    });
    $outcome = '';
    try {
        glsr(NoticeController::class)->activatePlugin();
    } catch (Exception $e) {
        $outcome = $e->getMessage();
    } finally {
        $_GET = [];
        unset($_REQUEST['_wpnonce']);
    }

    expect($outcome)->toStartWith('died|');
});

test('a link that is not the activation link is nobody\'s business', function () {
    $_GET = ['action' => 'activate', 'plugin' => 'hello.php']; // no trigger=notice
    glsr(NoticeController::class)->activatePlugin();
    $_GET = [];

    expect(is_plugin_active('hello.php'))->toBeFalse(); // untouched
});

test('a broken notices directory is logged, not a broken admin_head', function () {
    add_filter('site-reviews/path',
        fn ($path, $file) => 'plugin/Notices' === $file ? '/nowhere/at/all' : $path, 10, 2);

    glsr(NoticeController::class)->adminNotices();

    expect(glsr(GeminiLabs\SiteReviews\Modules\Console::class)->get())->toContain('nowhere');
});

test('the gatekeeper notice stays quiet off the admin, without caps, and for an empty error', function () {
    // off the plugin screens it does not even look
    set_current_screen('front');
    expect(has_action('admin_notices', [new GatekeeperNotice(), 'render']))->toBeFalse();

    // a person who cannot fix a problem is not offered the buttons for it
    set_current_screen('plugins');
    wp_set_current_user(createUser(['role' => 'subscriber']));
    set_transient(glsr()->prefix.'gatekeeper', array_merge(
        gatekeeperError(Gatekeeper::ERROR_NOT_ACTIVATED, 'Plugin A', 'a/a.php', 'a'),
        gatekeeperError(Gatekeeper::ERROR_NOT_SUPPORTED, 'Plugin C', 'c/c.php', 'c'),
        ['broken/broken.php' => ['name' => 'No Error Key']], // a malformed transient entry
    ));

    $notice = new GatekeeperNotice();
    $data = (fn () => $this->data())->call($notice);

    expect(trim($data['actions']))->toBe('') // no activate, no update
        ->and($data['message'])->not->toContain('No Error Key'); // the malformed entry is skipped

    set_current_screen('front');
});

test('one banner per page load, and a standalone notice needs the page to itself', function () {
    $banner = new class extends AbstractNotice {
        protected string $type = 'banner';

        public function canRenderPublic(): bool
        {
            return $this->canRender();
        }

        protected function canLoad(): bool
        {
            return false; // never hook; canRender is driven directly
        }
    };
    try {
        // another banner already rendered on this page load: stand down
        glsr()->store('notices', ['other' => ['type' => 'banner', 'rendered' => true]]);
        expect($banner->canRenderPublic())->toBeFalse();

        // a banner rendered but of another TYPE does not block a popup
        glsr()->store('notices', ['other' => ['type' => 'banner', 'rendered' => true]]);
        $popup = new class extends AbstractNotice {
            protected string $type = 'popup';

            public function canRenderPublic(): bool
            {
                return $this->canRender();
            }

            protected function canLoad(): bool
            {
                return false;
            }
        };
        expect($popup->canRenderPublic())->toBeTrue();

        // standalone (an addon's takeover page): exactly one plain notice allowed
        $standalone = new class extends AbstractNotice {
            public function canRenderPublic(): bool
            {
                return $this->canRender();
            }

            protected function canLoad(): bool
            {
                return false;
            }

            protected function isStandalone(): bool
            {
                return true;
            }
        };
        glsr()->store('notices', ['a' => ['type' => 'notice']]);
        expect($standalone->canRenderPublic())->toBeTrue();
        glsr()->store('notices', ['a' => ['type' => 'notice'], 'b' => ['type' => 'notice']]);
        expect($standalone->canRenderPublic())->toBeFalse();
    } finally {
        glsr()->store('notices', []);
    }
});

test('a popup notice keeps off the editor, premium screens, and sites that disabled the flyout', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $popup = fn () => new class extends AbstractNotice {
        protected string $type = 'popup';

        public function screenAllowed(): bool
        {
            return $this->isNoticeScreen();
        }
    };

    // the screens must belong to the review post type, or the first gate answers
    // instead of the popup rules (which is what an earlier draft of this test got wrong)
    set_current_screen(glsr()->post_type); // the review EDITOR: base is 'post'
    expect($popup()->screenAllowed())->toBeFalse(); // never over the editor

    set_current_screen('edit-'.glsr()->post_type);
    $originalBase = get_current_screen()->base;
    get_current_screen()->base = 'reviews_page_site-reviews-premium';
    expect($popup()->screenAllowed())->toBeFalse(); // never over the premium pitch
    get_current_screen()->base = $originalBase; // the screen object is memoized

    expect($popup()->screenAllowed())->toBeTrue();

    add_filter('site-reviews/flyoutmenu/enabled', '__return_false');
    expect($popup()->screenAllowed())->toBeFalse(); // the popup rides the flyout menu

    set_current_screen('front');
});

test('a monitored notice with no deferral version defers to none', function () {
    wp_set_current_user(createUser(['role' => 'administrator']));
    $notice = new class extends AbstractNotice {
        protected function canLoad(): bool
        {
            return false;
        }

        protected function isMonitored(): bool
        {
            return true;
        }
    };

    $notice->dismiss();

    $dismissed = get_user_meta(get_current_user_id(), AbstractNotice::USER_META_KEY, true);
    $key = array_key_first($dismissed);
    expect($dismissed[$key]['version'])->toBe(''); // the base deferVersion
});

test('a licence banner stands down when another banner already rendered', function () {
    set_current_screen('edit-'.glsr()->post_type);
    wp_set_current_user(createUser(['role' => 'administrator']));
    try {
        glsr()->store('notices', ['other' => ['type' => 'banner', 'rendered' => true]]);
        withLicenseStatus(['licensed' => true, 'missing' => true], function () {
            expect(renderedNotice(new LicenseMissingNotice()))->toBe('');
        });
        withLicenseStatus(['licensed' => false], function () {
            expect(renderedNotice(new LicensePromotedNotice()))->toBe('');
        });
    } finally {
        glsr()->store('notices', []);
        set_current_screen('front');
    }
});

test('the plugin-health notices show in the network admin too', function () {
    // set_current_screen('plugins-network') really is a network-admin screen,
    // even on a single site (probed: in_admin('network') answers true).
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen('plugins-network');
    try {
        $screenAllowed = fn ($notice) => (fn () => $this->isNoticeScreen())->call($notice);

        expect($screenAllowed(new GatekeeperNotice()))->toBeTrue()
            ->and($screenAllowed(new RetiredFreeNotice()))->toBeTrue()
            ->and($screenAllowed(new RetiredPremiumNotice()))->toBeTrue();
    } finally {
        set_current_screen('front');
    }
});
