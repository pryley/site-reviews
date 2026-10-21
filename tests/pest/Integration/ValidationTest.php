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

/*
 * The validator machinery itself.
 */

test('a custom validator answers isValid from its filter', function () {
    $validator = new CustomValidator(new Request([]));
    expect($validator->isValid())->toBeTrue();

    add_filter('site-reviews/validate/custom', '__return_false');
    expect($validator->isValid())->toBeFalse();
});

test('a validator that does not exist, or is not a validator, is logged and skipped', function () {
    $form = new \GeminiLabs\SiteReviews\Modules\Validator\ValidateForm();

    expect($form->validators(['\GeminiLabs\NoSuch\Validator']))->toBe([])
        ->and($form->validators([Helper::class]))->toBe([]) // real class, wrong contract
        ->and($form->validators([CustomValidator::class]))->toBe([CustomValidator::class]);
});

test('review limits can be narrowed to matching assignments', function () {
    // Limiting by email with assignment narrowing: the same person may review
    // DIFFERENT things, but not the same thing twice.
    glsr(OptionManager::class)->set('settings.forms.limit', 'email');
    glsr(OptionManager::class)->set('settings.forms.limit_assignments', ['assigned_posts', 'assigned_terms', 'assigned_users']);
    $postId = \GeminiLabs\SiteReviews\Tests\createPost();
    $termId = \GeminiLabs\SiteReviews\Tests\createTerm(['taxonomy' => glsr()->taxonomy]);
    $userId = createUser();
    \GeminiLabs\SiteReviews\Tests\createReview([
        'email' => 'limited@example.org',
        'assigned_posts' => [$postId],
        'assigned_terms' => [$termId],
        'assigned_users' => [$userId],
    ]);

    $sameThing = new ReviewLimitsValidator(new Request([
        'email' => 'limited@example.org',
        'assigned_posts' => [$postId],
        'assigned_terms' => [$termId],
        'assigned_users' => [$userId],
    ]));
    expect($sameThing->isValid())->toBeFalse(); // already reviewed this

    $somethingElse = new ReviewLimitsValidator(new Request([
        'email' => 'limited@example.org',
        'assigned_posts' => [\GeminiLabs\SiteReviews\Tests\createPost()],
    ]));
    expect($somethingElse->isValid())->toBeTrue(); // a different page is a different review
});

test('a limit keyed on a value the visitor left blank lets the review through', function () {
    // Limiting by email on a form where email is optional and empty: nothing to
    // limit on. (The field is always PRESENT in a form submission — a request
    // without it at all would be a hand-crafted one.)
    glsr(OptionManager::class)->set('settings.forms.limit', 'email');
    // An earlier visitor also left the email blank. A blank value is not an identity, so that
    // review does not count against this one.
    \GeminiLabs\SiteReviews\Tests\createReview(['email' => '']);

    expect((new ReviewLimitsValidator(new Request(['email' => ''])))->isValid())->toBeTrue();
});

test('the akismet request body drops what akismet cannot read', function () {
    // buildUrlQuery: booleans become 0/1, arrays and blanks are dropped entirely.
    $validator = new \GeminiLabs\SiteReviews\Modules\Validator\AkismetValidator(new Request([]));
    $query = \GeminiLabs\SiteReviews\Tests\protectedMethod(
        \GeminiLabs\SiteReviews\Modules\Validator\AkismetValidator::class, 'buildUrlQuery'
    )->invoke($validator, ['flag' => false, 'list' => ['a'], 'blank' => '', 'ok' => 'yes']);

    expect($query)->toContain('flag=0')
        ->and($query)->toContain('ok=yes')
        ->and($query)->not->toContain('list')
        ->and($query)->not->toContain('blank');
});

test('the honeypot hash verifies its own form and nobody else\'s', function () {
    $honeypot = glsr(\GeminiLabs\SiteReviews\Modules\Honeypot::class);
    $hash = $honeypot->hash('my-form');

    expect($honeypot->verify($hash, 'my-form'))->toBeTrue()
        ->and($honeypot->verify($hash, 'another-form'))->toBeFalse()
        ->and($honeypot->verify('forged', 'my-form'))->toBeFalse();
});

test('the assignment limits combine with AND even on a loose-assignment site', function () {
    // ReviewController answers the operator from reviews.assignment (loose = OR);
    // the validator's own filter, at a later priority, must force AND for the
    // limits query regardless — "already reviewed ANY of these" would over-block.
    glsr(OptionManager::class)->set('settings.reviews.assignment', 'loose');
    glsr(OptionManager::class)->set('settings.forms.limit', 'email');
    glsr(OptionManager::class)->set('settings.forms.limit_assignments', ['assigned_posts', 'assigned_terms']);
    $seen = new ArrayObject();
    add_filter('site-reviews/query/sql/clause/operator', function ($operator) use ($seen) {
        $seen->append($operator);
        return $operator;
    }, 30); // after the validator's own filter at 20

    (new ReviewLimitsValidator(new Request([
        'email' => 'operator@example.org',
        'assigned_posts' => [\GeminiLabs\SiteReviews\Tests\createPost()],
        'assigned_terms' => [\GeminiLabs\SiteReviews\Tests\createTerm(['taxonomy' => glsr()->taxonomy])],
    ])))->isValid();

    expect($seen->getArrayCopy())->toContain('AND');
});

/*
 * A forged proxy header against the controls that are keyed on the IP address.
 *
 * The site selected X-Forwarded-For and trusts the proxy at 10.0.0.1. A visitor at 203.0.113.9
 * sends a different address in the header with each review. Up to 8.3.4 the header was believed
 * from any sender and read from the left, so each review had a new address and neither the per-IP
 * limit nor the IP blacklist saw the visitor.
 */

/**
 * One review from $peer, the address that connected to the web server, with $header as the
 * X-Forwarded-For header. The other fields are new each time, so only the IP address repeats.
 */
function submitWithProxyHeader($test, string $peer, string $header): object
{
    update_option(glsr()->prefix.'ip_proxy', [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);
    $original = $_SERVER;
    $_SERVER['REMOTE_ADDR'] = $peer;
    $_SERVER['HTTP_X_FORWARDED_FOR'] = $header;
    try {
        return (fn () => $this->performAjaxRequest($this->request([
            'content' => $this->faker->sentence(12),
            'email' => $this->faker->email(),
            'name' => $this->faker->name(),
            'rating' => 5,
            'terms' => 1,
            'title' => $this->faker->sentence(4),
        ])))->call($test);
    } finally {
        $_SERVER = $original;
    }
}

test('a forged proxy header does not get a visitor past the per-ip review limit', function () {
    add_filter('site-reviews/validators', fn () => [
        ReviewLimitsValidator::class,
    ]);
    glsr(OptionManager::class)->set('settings.forms.limit', 'ip_address');

    // The visitor connects to the web server directly, which is not a trusted proxy.
    $first = submitWithProxyHeader($this, '203.0.113.9', '198.51.100.1');
    $second = submitWithProxyHeader($this, '203.0.113.9', '198.51.100.2');

    expect($first->success)->toBeTrue()
        ->and(glsr_get_review($first->data->review->ID)->ip_address)->toBe('203.0.113.9')
        ->and($second->success)->toBeFalse()
        ->and($second->data->message)->toEqual($this->messageFailedReviewLimits);
});

test('a forged first entry does not get a visitor past the per-ip review limit through a trusted proxy', function () {
    add_filter('site-reviews/validators', fn () => [
        ReviewLimitsValidator::class,
    ]);
    glsr(OptionManager::class)->set('settings.forms.limit', 'ip_address');

    // The proxy adds the visitor to the end of the header and keeps what the visitor sent.
    $first = submitWithProxyHeader($this, '10.0.0.1', '198.51.100.1, 203.0.113.9');
    $second = submitWithProxyHeader($this, '10.0.0.1', '198.51.100.2, 203.0.113.9');

    expect($first->success)->toBeTrue()
        ->and(glsr_get_review($first->data->review->ID)->ip_address)->toBe('203.0.113.9')
        ->and($second->success)->toBeFalse()
        ->and($second->data->message)->toEqual($this->messageFailedReviewLimits);
});

test('a forged proxy header does not get a blacklisted visitor past the ip blacklist', function () {
    add_filter('site-reviews/validators', fn () => [
        BlacklistValidator::class,
    ]);
    glsr(OptionManager::class)->set('settings.forms.blacklist.action', 'reject');
    glsr(OptionManager::class)->set('settings.forms.blacklist.entries', '203.0.113.9');
    glsr(OptionManager::class)->set('settings.forms.blacklist.integration', '');

    $response = submitWithProxyHeader($this, '203.0.113.9', '198.51.100.1');

    expect($response->success)->toBeFalse()
        ->and($response->data->message)->toEqual($this->messageFailedBlacklist);
});

