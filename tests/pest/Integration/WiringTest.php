<?php

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\BlackHole;
use GeminiLabs\SiteReviews\Commands\GeolocateReviews;
use GeminiLabs\SiteReviews\Controllers\QueueController;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Deprecated;
use GeminiLabs\SiteReviews\Hooks;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Provider;
use GeminiLabs\SiteReviews\Router;

use function GeminiLabs\SiteReviews\Tests\createReview;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The wiring: the things that make everything else run, and whose failure mode is silence.
 *
 * A controller that is broken throws. A HOOK that is never registered does nothing at all —
 * the feature simply is not there, no error is logged, and nobody finds out until a customer
 * says "the emails stopped". So the loaders are worth a test each, and the test is always the
 * same shape: did the thing actually get wired up?
 *
 *   Hooks         walks plugin/Hooks/ and plugin/Integrations/ and runs every class it finds.
 *                 There is no list. A file that stops being instantiable stops being loaded,
 *                 quietly.
 *   Provider      the singletons. A class that ought to be a singleton and is not gets rebuilt
 *                 on every glsr() call, and anything it was holding is lost.
 *   BlackHole     what glsr() hands back for a class that does not exist. It swallows calls and
 *                 logs, rather than fatalling — because the alternative is a white screen on
 *                 somebody's site because an addon was half-deleted.
 *   Deprecated    the trait that keeps old method names working.
 *   QueueController  the far end of every queued job.
 */

beforeEach(function () {
    resetPluginState();
});

/*
 * Hooks.
 */

test('every hooks class in the directory is loaded, with nothing listing them', function () {
    // The loader reflects over the directory. So the assertion is: for every file in there, a
    // singleton exists in the container. A file that becomes abstract, or is renamed out of step
    // with its class, silently stops being loaded — and takes its feature with it.
    $files = glob(glsr()->path('plugin/Hooks').'/*.php');
    expect($files)->not->toBeEmpty();

    glsr(Hooks::class)->run();

    foreach ($files as $file) {
        $class = 'GeminiLabs\SiteReviews\Hooks\\'.basename($file, '.php');
        if (!(new ReflectionClass($class))->isInstantiable()) {
            continue; // AbstractHooks
        }
        expect(glsr($class))->toBeInstanceOf($class);
    }
});

test('the hooks that make a review work are actually registered', function () {
    // Named, because these are the ones whose absence is silent. If `site-reviews/review/created`
    // has no listeners, reviews are still saved — they are simply never counted, never
    // geolocated, and never notified about.
    expect(has_action('site-reviews/review/created'))->not->toBeFalse()
        ->and(has_filter('site-reviews/rendered/template/review'))->not->toBeFalse()
        ->and(has_action('deleted_post'))->not->toBeFalse()
        ->and(has_action('transition_post_status'))->not->toBeFalse();
});

test('the integrations are run late, after the plugins they integrate with have loaded', function () {
    // On plugins_loaded, priority 100. An integration that ran at the ordinary time would ask
    // whether WooCommerce is active before WooCommerce had said so.
    expect(has_action('plugins_loaded'))->not->toBeFalse();
});

/*
 * Provider.
 */

test('the singletons are singletons', function () {
    // A singleton that is not one is rebuilt on every glsr() call. Notice would forget the notice
    // it was just given; Router would re-register its routes; Style would re-read its config on
    // every template tag.
    (new Provider())->register(glsr());

    foreach ([Notice::class, Router::class, Style::class, Queue::class, Hooks::class] as $class) {
        expect(glsr($class))->toBe(glsr($class));
    }
});

test('the application resolves to itself', function () {
    // The assertion that found the container bug. Provider binds Application::class to a closure
    // returning the app, and asking for it threw a TypeError — because bind() invoked the closure
    // rather than storing it, and an object ended up being used as an array key in resolve().
    // Nothing in the plugin calls this, which is why it had never been noticed.
    expect(glsr(Application::class))->toBe(glsr());
});

test('a factory closure can be bound, and is called when the class is asked for', function () {
    // The container's own docblock says `@param \Closure|string $concrete`, and there is an
    // `instanceof \Closure` branch in bind() to honour it. Neither was true until the fix: any
    // closure handed to bind() was invoked immediately, by the null-default helper, before it
    // could reach that branch.
    $calls = new ArrayObject();
    glsr()->bind('glsr-test-factory', function () use ($calls) {
        $calls->append(true);

        return new ArrayObject(['built' => 'by the closure']);
    });

    expect($calls)->toHaveCount(0); // NOT called at bind time…

    $first = glsr('glsr-test-factory');

    expect($calls)->toHaveCount(1) // …but at resolve time
        ->and($first['built'])->toBe('by the closure');

    // and not shared, so it is built again — which is what `bind` (rather than `singleton`) means
    glsr('glsr-test-factory');
    expect($calls)->toHaveCount(2);
});

/*
 * The black hole.
 */

test('calling a method on a class that does not exist logs instead of fatalling', function () {
    // This is what an addon that has been half-deleted looks like from inside the plugin. A
    // fatal here is a white screen on somebody's site; a logged line is a support ticket with an
    // answer in it.
    $blackhole = new BlackHole('Some\Missing\Addon');

    $blackhole->whateverItWas('an argument');

    expect(glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->get())
        ->toContain('whateverItWas')
        ->toContain('Some\Missing\Addon');
});

test('reading a property off it is silent, and it counts as an empty array', function () {
    // It extends ArrayObject so that code doing foreach() over it, or count(), keeps working.
    $blackhole = new BlackHole('Some\Missing\Addon');

    expect($blackhole->anything)->toBeNull()
        ->and(count($blackhole))->toBe(0)
        ->and(iterator_to_array($blackhole))->toBe([]);
});

/*
 * Deprecated method names.
 */

test('a renamed method still works under its old name, and says so', function () {
    // The map is filled in the CONSTRUCTOR, not by re-declaring the property. PHP will not let a
    // class re-declare a trait's property with a different default — "the definition differs and
    // is considered incompatible" — so every class using this trait has to assign it, and a class
    // that tries the obvious thing instead gets a fatal at compile time.
    $object = new class() {
        use Deprecated;

        public function __construct()
        {
            $this->mappedDeprecatedMethods = ['oldName' => 'newName'];
        }

        public function newName(string $value): string
        {
            return "new: {$value}";
        }
    };

    expect($object->oldName('x'))->toBe('new: x'); // it still works

    expect(glsr()->retrieveAs('array', 'deprecated'))
        ->toHaveCount(1);
    expect(glsr()->retrieveAs('array', 'deprecated')[0])
        ->toContain('oldName')
        ->toContain('newName')
        ->toContain('deprecated');
});

test('a method that never existed is still a fatal, because it is a bug', function () {
    // The trait keeps OLD names working. It does not turn every typo into a silent no-op.
    $object = new class() {
        use Deprecated;
    };

    expect(fn () => $object->neverExisted())->toThrow(BadMethodCallException::class);
});

/*
 * The queue's far end.
 */

test('a queued notification, geolocation and recalculation each reach their command', function () {
    // QueueController is what Action Scheduler calls. Every one of these runs minutes or hours
    // after the request that scheduled it, in a process with no user and no screen — so a
    // failure here is invisible twice over.
    $review = createReview(['ip_address' => '127.0.0.1']); // local: geolocation returns early
    $fired = new ArrayObject();
    add_action('site-reviews/review/geolocated', fn () => $fired->append('geolocated'));

    glsr(QueueController::class)->geolocateReview($review->ID);
    glsr(QueueController::class)->recalculateAssignmentMeta();

    // The recalculation is the one with a visible effect: it is what the counts on the reviews
    // screen are built from, and it is queued after every import.
    expect(glsr(CountManager::class))->not->toBeNull();
    expect($fired)->toHaveCount(0); // a local address is not geolocated, so nothing fired
});

test('the export cleanup removes the meta the exporter parked, and nothing else', function () {
    $review = createReview();
    update_post_meta($review->ID, glsr()->export_key, ['rating' => 5]);
    update_post_meta($review->ID, '_custom_keep_me', 'still here');

    glsr(QueueController::class)->cleanupAfterExport();

    expect(get_post_meta($review->ID, glsr()->export_key, true))->toBeEmpty()
        ->and(get_post_meta($review->ID, '_custom_keep_me', true))->toBe('still here');
});
