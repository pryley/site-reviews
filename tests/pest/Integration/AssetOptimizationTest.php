<?php

use GeminiLabs\SiteReviews\Modules\Assets\AssetCss;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Combining the plugin's stylesheets into one file.
 *
 * A site with three addons loads four stylesheets and four scripts, and a site owner who cares
 * about their Lighthouse score would rather it loaded one. So the plugin can concatenate its
 * own assets — its own, and its addons' — into a single file in the uploads directory, and
 * serve that instead.
 *
 * It is OFF by default, and that is the first thing tested, because a feature that writes files
 * into wp-content/uploads and rewrites the enqueue table has no business being on for anybody
 * who did not ask for it. It is turned on with a filter, not a setting.
 *
 * The rest is a cache, and caches go wrong in one direction: they serve yesterday's file. The
 * hash is built from the VERSION of the plugin and of every addon, so installing, updating or
 * removing an addon changes it, and the combined file is rebuilt. Get that wrong and a site
 * that just updated an addon serves the old addon's CSS until somebody clears something.
 *
 * `?nocache=1` is the escape hatch for when it does go wrong, and it is read with
 * filter_input() — so none of this could be tested until the suite shadowed it.
 */

beforeEach(function () {
    resetPluginState();
    freshPageLoad();
});

/**
 * The enqueue table as a real request starts with it: the plugin's stylesheet, registered from
 * the plugin's own URL. combine() maps that URL back to a path on disk, so a made-up src would
 * simply be skipped and nothing would be combined.
 *
 * This has to be redone between optimize() calls, and the reason is worth knowing: enqueue()
 * DEREGISTERS the plugin's stylesheet and re-registers it pointing at the combined file in
 * uploads. Run optimize() twice in one process and the second run reads that rewritten
 * registration, cannot map an uploads URL back to a plugin path, and aborts. Production never
 * sees it — every page load is a fresh process, the styles are registered from the plugin's
 * URLs, and optimize() runs once. A test that calls it twice is two page loads, and has to
 * behave like two page loads.
 */
function freshPageLoad(): void
{
    wp_deregister_style(glsr()->id);
    wp_register_style(glsr()->id, glsr()->url('assets/styles/bootstrap.css'), [], glsr()->version);
}

afterEach(function () {
    delete_transient(glsr()->prefix.'optimized_css');
    array_map('unlink', (array) glob(trailingslashit(wp_upload_dir()['basedir']).'site-reviews/assets/*'));
    wp_deregister_style(glsr()->id);
    // The addon list is held in memory on the Application SINGLETON — it is not an option and
    // it does not roll back. Leaving a fake addon in it would change hash() for every asset
    // test that ran afterwards, and glsr()->version for anything that reads the list.
    glsr()->store('addons', []);
});

function optimizationOn(): void
{
    add_filter('site-reviews/optimize/css', '__return_true');
}

function css(): AssetCss
{
    return new AssetCss();
}

function combinedFile(): string
{
    return trailingslashit(wp_upload_dir()['basedir']).'site-reviews/assets/site-reviews.css';
}

/*
 * Off by default.
 */

test('nothing is combined unless the site asked for it', function () {
    $asset = css();

    expect($asset->canOptimize())->toBeFalse()
        ->and($asset->isOptimizationEnabled())->toBeFalse()
        ->and($asset->isOptimized())->toBeFalse();

    $asset->optimize();

    expect(file_exists(combinedFile()))->toBeFalse(); // and nothing was written anywhere
});

test('an unoptimized site is served the original stylesheet, at the plugin version', function () {
    $asset = css();

    expect($asset->url())->toContain('assets/styles/')
        ->and($asset->url())->not->toContain('uploads')
        ->and($asset->version())->toBe(glsr()->version);
});

/*
 * On.
 */

test('the stylesheets are combined into one file in the uploads directory', function () {
    optimizationOn();
    $asset = css();

    expect($asset->canOptimize())->toBeTrue();

    $asset->optimize();

    expect(file_exists(combinedFile()))->toBeTrue()
        ->and(file_get_contents(combinedFile()))->not->toBeEmpty();
});

test('an optimized site is served the combined file, versioned by its hash', function () {
    optimizationOn();
    css()->optimize();
    $asset = css();

    expect($asset->isOptimized())->toBeTrue()
        ->and($asset->url())->toContain('uploads')
        ->and($asset->url())->toEndWith('site-reviews/assets/site-reviews.css')
        ->and($asset->version())->toBe($asset->hash())
        ->and($asset->version())->not->toBe(glsr()->version); // the hash, so the file busts itself
});

/*
 * The cache, which is the part that goes wrong.
 */

test('the file is not rebuilt on every page load', function () {
    optimizationOn();
    css()->optimize();
    file_put_contents(combinedFile(), '/* touched */'); // if it were rebuilt, this would go

    css()->optimize();

    expect(file_get_contents(combinedFile()))->toBe('/* touched */');
});

test('installing or updating an addon rebuilds the file', function () {
    // The hash is built from the version of the plugin AND of every addon. Without that, a site
    // that just updated an addon would go on serving the old addon's CSS — from a file in the
    // uploads directory that nothing thinks to clear.
    optimizationOn();
    css()->optimize();
    $before = css()->hash();

    glsr()->store('addons', ['site-reviews-images' => '2.0.0']); // an addon appears

    $after = css()->hash();
    expect($after)->not->toBe($before);
    expect(css()->isOptimized())->toBeFalse(); // …so the cached file no longer counts

    freshPageLoad(); // the next visitor arrives
    css()->optimize();

    expect(css()->isOptimized())->toBeTrue()             // …and it is rebuilt
        ->and(get_transient(glsr()->prefix.'optimized_css'))->toBe($after);
});

test('nocache throws the combined file away', function () {
    // The escape hatch. Read with filter_input(), so it was unreachable until the suite shadowed
    // it — which is to say this had never once been exercised.
    optimizationOn();
    css()->optimize();
    expect(get_transient(glsr()->prefix.'optimized_css'))->not->toBeFalse();

    $_GET['nocache'] = '1';
    $asset = css();

    expect(get_transient(glsr()->prefix.'optimized_css'))->toBeFalse() // dropped, in the constructor
        ->and($asset->canOptimize())->toBeFalse()                     // and it will not rebuild on this load
        ->and($asset->url())->not->toContain('uploads');              // so the originals are served
});
