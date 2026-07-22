<?php

use GeminiLabs\SiteReviews\Migrations\Migrate_6_11_0;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * A WooCommerce option rename: the review-reminder settings moved to a key that
 * says what they are. Both options belong to WooCommerce's own settings API, so
 * this migration is two option names and nothing else.
 */

beforeEach(fn () => resetPluginState());

test('the reminder settings move to the new option name', function () {
    update_option(oldReminderKey(), ['enabled' => 'yes', 'delay' => 14]);

    expect((new Migrate_6_11_0())->run())->toBeTrue();

    expect(get_option(newReminderKey()))->toBe(['enabled' => 'yes', 'delay' => 14])
        ->and(get_option(oldReminderKey()))->toBeFalse();
});

test('settings already under the new name are not overwritten', function () {
    // A site that has saved the settings since upgrading has the newer values;
    // the stale option is left where it is rather than written over them.
    update_option(oldReminderKey(), ['enabled' => 'no']);
    update_option(newReminderKey(), ['enabled' => 'yes']);

    expect((new Migrate_6_11_0())->run())->toBeTrue();

    expect(get_option(newReminderKey()))->toBe(['enabled' => 'yes'])
        ->and(get_option(oldReminderKey()))->toBe(['enabled' => 'no']);
});

test('a site that never had the reminder settings gets neither option', function () {
    expect((new Migrate_6_11_0())->run())->toBeTrue();

    expect(get_option(newReminderKey()))->toBeFalse()
        ->and(get_option(oldReminderKey()))->toBeFalse();
});

function oldReminderKey(): string
{
    return 'woocommerce_glsr_reminder_settings';
}

function newReminderKey(): string
{
    return 'woocommerce_glsr_customer_review_reminder_settings';
}
