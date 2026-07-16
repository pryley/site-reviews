<?php

use function GeminiLabs\SiteReviews\Tests\protectedMethod;

/*
 * Which integrations the stubs actually switch on.
 *
 * The mu-plugin requires tests/stubs on muplugins_loaded, before the plugins load, so each
 * integration's isInstalled() runs against the stubbed symbols and its hooks register for real. If a
 * stub loses a symbol the integration silently goes dark and its coverage to zero — these assertions
 * turn that into a failure. Every row below was traced twice: through the integration's isInstalled()
 * body, and through the stub that satisfies it.
 */

function integrationIsInstalled(string $integration): bool
{
    $hooks = "GeminiLabs\SiteReviews\Integrations\\{$integration}\Hooks";

    return protectedMethod($hooks, 'isInstalled')->invoke(glsr($hooks));
}

test('the stubs switch the integration on', function (string $integration) {
    expect(integrationIsInstalled($integration))->toBeTrue();
})->with([
    // integration => the symbols isInstalled() requires, and the stub declaring them
    'Avada' => ['Avada'],                   // FusionBuilder(), FUSION_BUILDER_VERSION — fusion-builder.php
    'Breakdance' => ['Breakdance'],         // Breakdance\Elements\Element + 7 functions — breakdance.php
    'BuddyBoss' => ['BuddyBoss'],           // bp_displayed_user_id() — buddyboss.php
    'Elementor' => ['Elementor'],           // Elementor\Plugin — elementor.php
    'LPFW' => ['LPFW'],                     // LPFW() + class LPFW — lpfw.php
    'MultilingualPress' => ['MultilingualPress'], // ACTION_ADD_SERVICE_PROVIDERS, resolve() — multilingualpress.php
    'MyCred' => ['MyCred'],                 // myCRED_Hook, myCRED_Core, MYCRED_DEFAULT_TYPE_KEY — mycred.php
    'ProfilePress' => ['ProfilePress'],     // PPRESS_VERSION_NUMBER + 3 functions — profilepress.php
    'SureCart' => ['SureCart'],             // SureCart, sc_get_product() — surecart.php
    'UltimateMember' => ['UltimateMember'], // UM() + 4 functions — ultimate-member.php
    'WLPR' => ['WLPR'],                     // Wlpr\App\{Helpers\Loyalty,Helpers\Point,Models\PointAction} — wlpr.php
    'WPBakery' => ['WPBakery'],             // WPBakeryShortCode, vc_map(), WPB_VC_VERSION — wpbakery.php
    'WPLoyalty' => ['WPLoyalty'],           // Wlr\App\…\{EarnCampaign,Woocommerce,ProductReview,Referral} — wp-loyalty-rules.php
    'WooCommerce' => ['WooCommerce'],       // WooCommerce, WC() — woocommerce.php
]);

test('the integration stays dormant', function (string $integration) {
    expect(integrationIsInstalled($integration))->toBeFalse();
})->with([
    // Each of these is a deliberate gap rather than an oversight — see below.
    'Bricks' => ['Bricks'],                       // a theme, not a plugin
    'Divi' => ['Divi'],                           // a theme, not a plugin
    'Flatsome' => ['Flatsome'],                   // a theme, not a plugin
    'GamiPress' => ['GamiPress'],                 // the stub declares no GAMIPRESS_VER
    'JetWooBuilder' => ['JetWooBuilder'],         // no stub
    'SASWP' => ['SASWP'],                         // no stub
    'SchemaPro' => ['SchemaPro'],                 // no stub
    'WooRewards' => ['WooRewards'],               // the stub declares neither class it looks for
    'YoastSEO' => ['YoastSEO'],                   // no stub
]);

test('the page builders that ship as a theme are dormant because their theme is not active', function () {
    // Bricks, Divi and Flatsome are themes, not plugins: their isInstalled() asks
    // wp_get_theme(get_template()) for a TextDomain (Bricks, Flatsome) or a Name
    // (Divi). A stub cannot satisfy that — only a theme on disk can — so these
    // three cannot be woken by adding symbols, and their Controllers are only
    // reachable by calling them directly (see TransformerTest).
    $template = wp_get_theme(get_template());

    expect($template->get('TextDomain'))->not->toBe('bricks');
    expect($template->get('TextDomain'))->not->toBe('flatsome');
    expect($template->get('Name'))->not->toBe('Divi');
});

test('the one stub that can never be loaded stays out', function () {
    // The plugin bundles Action Scheduler (vendors/woocommerce/action-scheduler),
    // so loading the stub on top of it would be a redeclaration fatal. It is the
    // only entry left in the mu-plugin's exclusion list, and this is what says so.
    expect(class_exists('ActionScheduler'))->toBeTrue(); // the bundled one
    expect((new \ReflectionClass('ActionScheduler'))->getFileName())
        ->toContain('vendors/woocommerce/action-scheduler');
});

test('multilingualpress closes its own gate rather than dying', function () {
    // The stub declares resolve() but cannot return a service, so version() gets
    // null back and ->version() on it raises an \Error. Both guards in
    // MultilingualPress\Hooks catch \Throwable, so an unreadable version is
    // treated as an unsupported one: the integration reports itself installed,
    // fails the version gate, and raises its notice — which, unlike every other
    // integration, it prints itself on the network admin screen.
    expect(integrationIsInstalled('MultilingualPress'))->toBeTrue();

    ob_start();
    do_action('network_admin_notices');
    $notices = (string) ob_get_clean();

    expect($notices)->toContain('Update MultilingualPress to version 5.0.1 or higher');
});

test('lpfw registers but stays disabled', function () {
    // LPFW() returns null from the stub, so isEnabled() cannot read the option name
    // it gates on and comes back false — the safe direction, and quietly: it reads
    // that name defensively rather than walking two properties off whatever LPFW()
    // hands back. This test is what would catch the warnings coming back, because
    // phpunit.xml fails on one.
    expect(integrationIsInstalled('LPFW'))->toBeTrue();

    $hooks = 'GeminiLabs\SiteReviews\Integrations\LPFW\Hooks';
    expect(protectedMethod($hooks, 'isEnabled')->invoke(glsr($hooks)))->toBeFalse();

    // and so it registers none of its three hooks. The filter below is the one only
    // LPFW hooks: site-reviews/review/approved would prove nothing, since WLPR and
    // WPLoyalty hook it too and they ARE enabled.
    expect(has_filter('lpfw_get_point_earn_source_types'))->toBeFalse();
});

test('the integrations that declare no isInstalled always register', function (string $integration) {
    // These six hook onto WordPress itself or onto the plugin's own hooks — there
    // is no third party to detect — so they inherit IntegrationHooks::isInstalled(),
    // which returns true, and register unconditionally in Hooks::run(). Their code
    // runs on every site, which makes them the cheapest coverage in
    // plugin/Integrations.
    //
    // RankMath and SEOPress also inherit that true, but they gate on isEnabled()
    // instead: they only register when they have been nominated as the schema
    // owner in the settings (see SeoSchemaTest).
    expect(integrationIsInstalled($integration))->toBeTrue();
})->with([
    'Cache' => ['Cache'],
    'CloudFlare' => ['CloudFlare'],
    'DuplicatePage' => ['DuplicatePage'],
    'DuplicatePost' => ['DuplicatePost'],
    'Flywheel' => ['Flywheel'],
    'Gutenberg' => ['Gutenberg'],
]);
