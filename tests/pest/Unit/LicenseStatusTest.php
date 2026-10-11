<?php

use GeminiLabs\SiteReviews\Defaults\Updater\CheckLicenseDefaults;
use GeminiLabs\SiteReviews\Licensing\LicenseStatus;

test('each answer of the licence server resolves to one status', function (array $answer, string $status) {
    expect(LicenseStatus::of(glsr(CheckLicenseDefaults::class)->restrict($answer)))->toBe($status);
})->with([
    'not a key' => [['success' => false, 'license' => 'invalid'], LicenseStatus::INVALID],
    'no success flag' => [['license' => 'valid'], LicenseStatus::INVALID],
    'disabled' => [['success' => true, 'license' => 'disabled'], LicenseStatus::DISABLED],
    'another product by name' => [['success' => true, 'license' => 'item_name_mismatch'], LicenseStatus::MISMATCH],
    'another product by id' => [['success' => true, 'license' => 'invalid_item_id'], LicenseStatus::MISMATCH],
    'expired' => [['success' => true, 'license' => 'expired'], LicenseStatus::EXPIRED],
    'valid' => [['success' => true, 'license' => 'valid'], LicenseStatus::VALID],
    'inactive, activations left' => [['success' => true, 'license' => 'inactive', 'activations_left' => 1], LicenseStatus::INACTIVE],
    'inactive here, activations left' => [['success' => true, 'license' => 'site_inactive', 'activations_left' => 2], LicenseStatus::INACTIVE],
    'inactive, unlimited licence' => [['success' => true, 'license' => 'inactive', 'activations_left' => 0, 'license_limit' => 0], LicenseStatus::INACTIVE],
    'inactive, none left' => [['success' => true, 'license' => 'site_inactive', 'activations_left' => 0, 'license_limit' => 3], LicenseStatus::EXHAUSTED],
    'a status the enum does not know' => [['success' => true, 'license' => 'revoked'], LicenseStatus::OTHER],
]);

test('no answer is unreachable, whatever the success flag', function () {
    $check = glsr(CheckLicenseDefaults::class)->restrict([]);
    $check['license'] = 'unknown'; // Updater::checkLicense() sets it after the defaults run
    expect(LicenseStatus::of($check))->toBe(LicenseStatus::UNREACHABLE);
});
