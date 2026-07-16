<?php

use Faker\Factory;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Modules\Validator\BlacklistValidator;
use GeminiLabs\SiteReviews\Modules\Validator\CustomValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DuplicateValidator;
use GeminiLabs\SiteReviews\Modules\Validator\HoneypotValidator;
use GeminiLabs\SiteReviews\Modules\Validator\PermissionValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ReviewLimitsValidator;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

use GeminiLabs\SiteReviews\Tests\SubmitsReviews;

// SubmitsReviews carries both the ajax harness (InteractsWithAjax) and the faked request state
// the tests read off $this.
uses(SubmitsReviews::class);

beforeEach(function () {
    resetPluginState();
    $this->setUpSubmitsReviews();
});

afterEach(fn () => $this->tearDownAjax());

test('blacklist validation', function () {
    add_filter('site-reviews/validators', fn () => [
        BlacklistValidator::class,
    ]);
    $blacklist = "xxx\n \napple";
    $response = $this->assertJsonSuccess($this->request());
    expect($response->data->review->is_approved)->toBeTrue();
    expect($response->data->review->status)->toEqual('publish');
    expect($response->data->message)->toEqual($this->messageSuccess);
    glsr(OptionManager::class)->set('settings.forms.blacklist.action', 'reject');
    glsr(OptionManager::class)->set('settings.forms.blacklist.entries', $blacklist);
    glsr(OptionManager::class)->set('settings.forms.blacklist.integration', '');
    $this->assertJsonError($this->request(['content' => 'Give me a xxx!!']));
    $this->assertJsonError($this->request(['email' => 'john@apple.com']));
    $this->assertJsonSuccess($this->request(['email' => 'john@microsoft.com']));
    $this->assertJsonError($this->request(['name' => 'Johnxxx Doe']));
    $this->assertJsonError($this->request(['title' => 'This is a xxx title']));
    glsr(OptionManager::class)->set('settings.forms.blacklist.entries', "{$blacklist}\n{$this->ipaddress}");
    $this->assertJsonError($this->request());
    glsr(OptionManager::class)->set('settings.forms.blacklist.integration', 'comments');
    $this->assertJsonSuccess($this->request());
    update_option('disallowed_keys', $blacklist);
    $this->assertJsonError($this->request(['content' => 'Give me a xxx!!']));
    $this->assertJsonError($this->request(['email' => 'john@apple.com']));
    $this->assertJsonError($this->request(['name' => 'Johnxxx Doe']));
    $this->assertJsonError($this->request(['title' => 'This is a xxx title']));
    update_option('disallowed_keys', "{$blacklist}\n{$this->ipaddress}");
    $response1 = $this->assertJsonError($this->request());
    expect($response1->data->message)->toEqual($this->messageFailedBlacklist);
    update_option('disallowed_keys', $blacklist);
    glsr(OptionManager::class)->set('settings.forms.blacklist.action', 'unapprove');
    $response2 = $this->assertJsonSuccess($this->request(['email' => 'john@apple.com']));
    expect($response2->data->review->is_approved)->toBeFalse();
    expect($response2->data->review->status)->toEqual('pending');
});

test('custom validation', function () {
    add_filter('site-reviews/validators', fn () => [
        CustomValidator::class,
    ]);
    $this->assertJsonSuccess($this->request());
    add_filter('site-reviews/validate/custom', function () {
        return $this->messageFailedCustom;
    });
    $response1 = $this->assertJsonError($this->request());
    expect($response1->data->message)->toEqual($this->messageFailedCustom);
    add_filter('site-reviews/validate/custom', '__return_false', 11);
    $response2 = $this->assertJsonError($this->request());
    expect($response2->data->message)->toEqual($this->messageFailed);
});

test('default validation', function () {
    add_filter('site-reviews/validators', fn () => [
        DefaultValidator::class,
    ]);
    glsr(OptionManager::class)->set('settings.forms.required', ['rating', 'title', 'content', 'name', 'email', 'terms']);
    $response1 = $this->assertJsonError($this->request());
    $response2 = $this->assertJsonSuccess($this->request([
        'content' => $this->faker->sentence(),
        'email' => $this->faker->email(),
        'name' => $this->faker->name(),
        'rating' => 5,
        'terms' => 1,
        'title' => $this->faker->sentence(),
    ]));
    expect((array) $response1->data->errors)->toHaveCount(6);
    expect($response1->data->message)->toEqual($this->messageFailedValidation);
    expect($response2->data->message)->toEqual($this->messageSuccess);
});

test('duplicate validation', function () {
    add_filter('site-reviews/validators', fn () => [
        DuplicateValidator::class,
    ]);
    $request = $this->request([
        'content' => $this->faker->sentence(),
    ]);
    glsr(OptionManager::class)->set('settings.forms.prevent_duplicates', 'yes');
    $response1 = $this->assertJsonSuccess($request);
    $response2 = $this->assertJsonError($request);
    glsr(OptionManager::class)->set('settings.forms.prevent_duplicates', 'no');
    $response3 = $this->assertJsonSuccess($request);
    expect($response1->data->message)->toEqual($this->messageSuccess);
    expect($response2->data->message)->toEqual($this->messageFailedDuplicate);
    expect($response3->data->message)->toEqual($this->messageSuccess);
});

test('multiple validation', function () {
    $this->assertJsonError($this->request());
});

test('honeypot validation', function () {
    add_filter('site-reviews/validators', fn () => [
        HoneypotValidator::class,
    ]);
    $formId = 'glsr-12345678';
    $honeypotHash = glsr(Honeypot::class)->hash($formId);
    $response1 = $this->assertJsonError($this->request());
    $response2 = $this->assertJsonError($this->request(['form_id' => $formId, $honeypotHash => 'x']));
    $response3 = $this->assertJsonSuccess($this->request(['form_id' => $formId, $honeypotHash => '']));
    expect($response1->data->message)->toEqual($this->messageFailedHoneypot);
    expect($response2->data->message)->toEqual($this->messageFailedHoneypot);
    expect($response3->data->message)->toEqual($this->messageSuccess);
});

test('permission validation', function () {
    add_filter('site-reviews/validators', fn () => [
        PermissionValidator::class,
    ]);
    $response1 = $this->assertJsonSuccess($this->request());
    glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
    $response2 = $this->assertJsonError($this->request());
    wp_set_current_user(createUser([
        'role' => 'editor',
    ]));
    $response3 = $this->assertJsonSuccess($this->request([
        '_nonce' => wp_create_nonce('submit-review'),
    ]));
    expect($response1->data->message)->toEqual($this->messageSuccess);
    expect($response2->data->message)->toEqual($this->messageFailedPermission);
    expect($response3->data->message)->toEqual($this->messageSuccess);
});

test('review limits validation', function () {
    add_filter('site-reviews/validators', fn () => [
        ReviewLimitsValidator::class,
    ]);
    $this->assertJsonSuccess($this->request());
    glsr(OptionManager::class)->set('settings.forms.limit', 'ip_address');
    $this->assertJsonError($this->request());
    glsr(OptionManager::class)->set('settings.forms.limit', 'email');
    $request = $this->request([
        'email' => $this->faker->email(),
    ]);
    $this->assertJsonSuccess($request);
    $this->assertJsonError($request);
    glsr(OptionManager::class)->set('settings.forms.limit_whitelist.email', $request['email']);
    $this->assertJsonSuccess($request);
    glsr(OptionManager::class)->set('settings.forms.limit', 'username');
    $this->assertJsonSuccess($this->request());
    wp_set_current_user(createUser([
        'role' => 'editor',
        'user_login' => 'test_user',
    ]));
    $nonce = wp_create_nonce('submit-review');
    $this->assertJsonSuccess($this->request(['_nonce' => $nonce]));
    $this->assertJsonError($this->request(['_nonce' => $nonce]));
    $this->assertJsonError($this->request(['_nonce' => $nonce]));
});
