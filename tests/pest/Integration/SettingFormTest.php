<?php

use GeminiLabs\SiteReviews\Modules\Html\SettingField;
use GeminiLabs\SiteReviews\Modules\Html\SettingForm;
use GeminiLabs\SiteReviews\Premium\Host\Application as SettingHostAddon;
use GeminiLabs\SiteReviews\Premium\HostedThing\Application as SettingHostedAddon;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\unregisterAddons;

require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-premium-host/plugin/Hooks.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Application.php');
require_once glsr()->path('tests/pest/fixtures/site-reviews-hosted-addon/plugin/Hooks.php');

/*
 * The settings page form. The field-by-field rendering is pinned in
 * SettingFieldTest; this is the FORM's own logic — the addon tab grouping,
 * which only exists once an addon has registered settings.
 */

beforeEach(fn () => resetPluginState());

/**
 * Registers settings under a group the free plugin ships none of. The settings
 * config is memoized on the Application singleton before any test runs, so the
 * 'settings' filter can no longer reach it; the injection edits the memo and
 * hands back a restorer.
 */
function withInjectedSettings(array $fields): Closure
{
    $property = new ReflectionProperty(glsr(), 'settings');
    $property->setAccessible(true);
    $original = $property->getValue(glsr());
    $settings = $original;
    foreach ($fields as $key => $field) {
        $settings[$key] = wp_parse_args($field, ['default' => '', 'sanitizer' => 'text']);
    }
    $property->setValue(glsr(), $settings);

    return fn () => $property->setValue(glsr(), $original);
}

test('addon settings are grouped into one sub-tab per addon, alphabetically', function () {
    $restore = withInjectedSettings([
        'settings.addons.zeta.enabled' => ['label' => 'Enabled', 'type' => 'text'],
        'settings.addons.alpha.enabled' => ['label' => 'Enabled', 'type' => 'text'],
    ]);
    try {
        $form = new SettingForm(['addons' => 'Addons']);
        $data = protectedMethod(SettingForm::class, 'templateDataForAddons')->invoke($form, 'addons');

        expect(array_keys($data['settings']))->toBe(['alpha', 'zeta'])
            ->and($data['subsubsub'])->toBe(['Alpha', 'Zeta'])
            ->and($data['settings']['alpha'])->toContain('settings.addons.alpha.enabled');
    } finally {
        $restore();
    }
});

test('integration settings are grouped the same way', function () {
    $restore = withInjectedSettings([
        'settings.integrations.myintegration.enabled' => ['label' => 'Enabled', 'type' => 'text'],
    ]);
    try {
        $form = new SettingForm(['integrations' => 'Integrations']);
        $data = protectedMethod(SettingForm::class, 'templateDataForIntegrations')->invoke($form, 'integrations');

        // the woken integrations (WooCommerce et al) append their own names on
        // the integration/subsubsub filter, so the assertion is containment
        expect($data['subsubsub'])->toContain('Myintegration')
            ->and($data['settings']['myintegration'])->toContain('settings.integrations.myintegration.enabled');
    } finally {
        $restore();
    }
});

test('a raw field keeps its own id instead of the hashed one', function () {
    // Setting ids are hashed because the path-based id is unwieldy; a raw field
    // is built verbatim and its id must survive as given.
    $form = new SettingForm([]);
    $field = new SettingField(['name' => 'x', 'type' => 'hidden', 'id' => 'my-verbatim-id', 'is_raw' => true]);

    protectedMethod(SettingForm::class, 'normalizeFieldId')->invoke($form, $field);

    expect($field->id)->toBe('my-verbatim-id');
});

test('a host addon\'s settings display on the addons tab, not under its own slug', function () {
    // A host mounts its settings under its own slug (settings.{hostSlug}.*), but the settings
    // page has no tab named after the host — its fields belong on the addons tab, which the
    // installed premium plugin relabels.
    glsr()->register(SettingHostAddon::class);
    glsr()->register(SettingHostedAddon::class, glsr(SettingHostAddon::class));
    try {
        $form = new SettingForm([]);
        $field = $form->field('settings.premium-host.hosted-thing.color', ['type' => 'text']);

        expect($field->group)->toBe('addons');
    } finally {
        unregisterAddons(SettingHostAddon::ID, SettingHostedAddon::ID);
    }
});
