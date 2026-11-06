<?php

use GeminiLabs\SiteReviews\Licensing\Check;
use GeminiLabs\SiteReviews\Licensing\Defaults\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Licensing\Verdict;

function check(array $answer): Check
{
    return new Check(glsr(CheckLicenseDefaults::class)->restrict($answer));
}

test('each answer of the licence server resolves to one verdict', function (array $answer, Verdict $verdict) {
    expect(check($answer)->verdict)->toBe($verdict);
})->with([
    'not a key' => [['success' => false, 'license' => 'invalid'], Verdict::Invalid],
    'no success flag' => [['license' => 'valid'], Verdict::Invalid],
    'disabled' => [['success' => true, 'license' => 'disabled'], Verdict::Disabled],
    'another product by name' => [['success' => true, 'license' => 'item_name_mismatch'], Verdict::Mismatch],
    'another product by id' => [['success' => true, 'license' => 'invalid_item_id'], Verdict::Mismatch],
    'expired' => [['success' => true, 'license' => 'expired'], Verdict::Expired],
    'valid' => [['success' => true, 'license' => 'valid'], Verdict::Valid],
    'inactive, activations left' => [['success' => true, 'license' => 'inactive', 'activations_left' => 1], Verdict::Inactive],
    'inactive here, activations left' => [['success' => true, 'license' => 'site_inactive', 'activations_left' => 2], Verdict::Inactive],
    'inactive, unlimited licence' => [['success' => true, 'license' => 'inactive', 'activations_left' => 0, 'license_limit' => 0], Verdict::Inactive],
    'inactive, none left' => [['success' => true, 'license' => 'site_inactive', 'activations_left' => 0, 'license_limit' => 3], Verdict::Exhausted],
    'a status the enum does not know' => [['success' => true, 'license' => 'revoked'], Verdict::Other],
]);

test('no answer is unreachable, whatever the success flag', function () {
    $values = glsr(CheckLicenseDefaults::class)->restrict([]);
    $values['license'] = 'unknown'; // LicenseServer::check() sets it after the defaults run
    $check = new Check($values);

    expect($check->verdict)->toBe(Verdict::Unreachable)
        ->and($check->isReachable())->toBeFalse()
        ->and($check->isValid())->toBeFalse();
});

test('the fields a reader uses are typed, and the rest stay in the array', function () {
    $check = check([
        'success' => true,
        'license' => 'valid',
        'expires' => '2027-01-01 23:59:59',
        'activations_left' => 3,
        'license_limit' => 4,
        'is_premium_license' => true,
        'connect_url' => 'https://niftyplugins.com/connect/',
        'site_count' => 1,
    ]);

    expect($check->premium)->toBeTrue()
        ->and($check->expires)->toBe('2027-01-01 23:59:59')
        ->and($check->lifetime)->toBeFalse()
        ->and($check->activationsLeft)->toBe(3)
        ->and($check->licenseLimit)->toBe(4)
        ->and($check->connectUrl)->toBe('https://niftyplugins.com/connect/')
        ->and($check->hasActivationsLeft())->toBeTrue()
        ->and($check->toArray()['site_count'])->toBe(1);
});

test('a lifetime licence never expires soon, and a dated one does within thirty days', function () {
    $soon = date('Y-m-d H:i:s', time() + 10 * DAY_IN_SECONDS);
    $later = date('Y-m-d H:i:s', time() + 60 * DAY_IN_SECONDS);

    expect(check(['success' => true, 'license' => 'valid', 'expires' => $soon])->expiresSoon())->toBeTrue()
        ->and(check(['success' => true, 'license' => 'valid', 'expires' => $later])->expiresSoon())->toBeFalse()
        ->and(check(['success' => true, 'license' => 'valid', 'expires' => 'lifetime'])->expiresSoon())->toBeFalse()
        ->and(check(['success' => true, 'license' => 'valid', 'expires' => 'lifetime'])->lifetime)->toBeTrue();
});
