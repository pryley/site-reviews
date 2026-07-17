<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Validator\FriendlycaptchaV2Validator;
use GeminiLabs\SiteReviews\Modules\Validator\FriendlycaptchaValidator;
use GeminiLabs\SiteReviews\Modules\Validator\HcaptchaValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ProcaptchaValidator;
use GeminiLabs\SiteReviews\Modules\Validator\RecaptchaV2InvisibleValidator;
use GeminiLabs\SiteReviews\Modules\Validator\RecaptchaV3Validator;
use GeminiLabs\SiteReviews\Modules\Validator\TurnstileValidator;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The CAPTCHA gate.
 *
 * Seven services, one abstract class. Each subclass just knows an API URL, which settings hold its
 * keys, and its error codes — so CaptchaValidatorAbstract holds nearly all the behaviour, and most
 * of this file is about it. It is the last thing between a spambot and the reviews table, wrong two
 * ways: letting a bot through (rubbish) or turning a real person away (worse — they had something to
 * say and the owner never knows). Both are tested.
 *
 * The state machine (see the CAPTCHA_ constants):
 *
 *   DISABLED  none configured. Everybody passes. NOT a failure.
 *   EMPTY     enabled, but no token sent. The script did not run, or a bot skipped it.
 *   FAILED    the service was unreachable, or keys are missing. "Refresh and retry."
 *   INVALID   the service was reached and said no. "Verification failed."
 *   VALID     the service said yes.
 *
 * Only DISABLED and VALID pass. Everything else — including "could not reach Google" — rejects: a
 * CAPTCHA that fails OPEN when the service is down is one a spammer disables by taking it down.
 */

beforeEach(function () {
    resetPluginState();
});

/**
 * Turns a CAPTCHA on, with keys, exactly as the settings screen would.
 */
function enableCaptcha(string $integration, array $options = []): void
{
    glsr(OptionManager::class)->set('settings.forms.captcha.integration', $integration);
    glsr(OptionManager::class)->set('settings.forms.captcha.usage', 'all');
    foreach ($options as $path => $value) {
        glsr(OptionManager::class)->set("settings.forms.{$path}", $value);
    }
}

function turnstile(array $request = []): TurnstileValidator
{
    return new TurnstileValidator(new Request(wp_parse_args($request, [
        '_captcha' => 'a-token-from-the-browser',
        'ip_address' => '203.0.113.9',
    ])));
}

/**
 * What the service said. `success` is the field every one of them speaks.
 */
function captchaReply(array $body): array
{
    return ['body' => (string) wp_json_encode($body)];
}

function failedValidation(object $validator): bool
{
    $validator->validate();

    return !$validator->isValid();
}

/*
 * When nothing is configured.
 */

test('a site with no captcha lets everybody through, and asks nobody', function () {
    // DISABLED is not a failure. Most sites run without a CAPTCHA and their reviews must not
    // all be rejected — and nothing may be sent anywhere.
    $requests = interceptHttp(captchaReply(['success' => true]));

    expect(failedValidation(turnstile()))->toBeFalse()
        ->and($requests)->toHaveCount(0);
});

test('a captcha that only applies to guests does not challenge a logged-in person', function () {
    enableCaptcha('turnstile', ['turnstile.key' => 'a-key', 'turnstile.secret' => 'a-secret']);
    glsr(OptionManager::class)->set('settings.forms.captcha.usage', 'guest');
    wp_set_current_user(\GeminiLabs\SiteReviews\Tests\createUser());
    $requests = interceptHttp(captchaReply(['success' => false]));

    expect(failedValidation(turnstile()))->toBeFalse() // even though the service would say no
        ->and($requests)->toHaveCount(0);
});

/*
 * When it is on.
 */

test('a valid token passes', function () {
    enableCaptcha('turnstile', ['turnstile.key' => 'a-key', 'turnstile.secret' => 'a-secret']);
    $requests = interceptHttp(captchaReply(['success' => true]));

    expect(failedValidation(turnstile()))->toBeFalse();

    // and the service was asked the right question
    $body = (array) $requests[0]['args']['body'];
    expect($requests[0]['url'])->toBe(TurnstileValidator::API_URL)
        ->and($body['response'])->toBe('a-token-from-the-browser')
        ->and($body['secret'])->toBe('a-secret')
        ->and($body['remoteip'])->toBe('203.0.113.9');
});

test('a token the service rejects fails, and the person is told to try again', function () {
    enableCaptcha('turnstile', ['turnstile.key' => 'a-key', 'turnstile.secret' => 'a-secret']);
    interceptHttp(captchaReply(['success' => false, 'error-codes' => ['invalid-input-response']]));

    $validator = turnstile();

    expect(failedValidation($validator))->toBeTrue();
    expect(glsr()->sessionGet('form_message'))->toContain('verification failed');
});

test('no token at all is refused, without asking the service', function () {
    // A bot that posts the form directly sends no token. There is nothing to verify and no
    // reason to spend a request finding that out.
    enableCaptcha('turnstile', ['turnstile.key' => 'a-key', 'turnstile.secret' => 'a-secret']);
    $requests = interceptHttp(captchaReply(['success' => true]));

    expect(failedValidation(turnstile(['_captcha' => ''])))->toBeTrue()
        ->and($requests)->toHaveCount(0);
});

test('a captcha service that is down rejects the review rather than waving it through', function () {
    // THE SECURITY-CRITICAL DIRECTION. If an unreachable service meant "pass", then a spammer
    // could disable the CAPTCHA on every site in the world by taking Cloudflare offline —
    // or, more cheaply, by making the site's own outbound requests fail.
    enableCaptcha('turnstile', ['turnstile.key' => 'a-key', 'turnstile.secret' => 'a-secret']);
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    $validator = turnstile();

    expect(failedValidation($validator))->toBeTrue();
    // and the message says what to DO, which is different from "you failed"
    expect(glsr()->sessionGet('form_message'))->toContain('failed to load')
        ->and(glsr()->sessionGet('form_message'))->toContain('refresh the page');
});

test('the visitor is not made to wait while the plugin retries a broken captcha service', function () {
    // Api retries a 429 or a 5xx with a backoff — about a second, then 1.6, then 2.6, then
    // 4.1 — which is right for a licence check, where nobody is waiting, and wrong here: the
    // CAPTCHA is verified inside the submit-review request, with a person watching a spinner.
    //
    // One attempt. Their retry is pressing the button again.
    enableCaptcha('turnstile', ['turnstile.key' => 'a-key', 'turnstile.secret' => 'a-secret']);
    $requests = interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    turnstile()->validate();

    expect($requests)->toHaveCount(1);
});

test('a captcha with no keys fails rather than silently passing everything', function () {
    // Somebody turned the CAPTCHA on and never pasted the keys in. The service says no, and
    // the plugin must not read that as "the visitor failed" — it is the SITE that is broken,
    // and the message says so.
    enableCaptcha('turnstile'); // no key, no secret
    interceptHttp(captchaReply(['success' => false, 'error-codes' => ['missing-input-secret']]));

    $validator = turnstile();

    expect(failedValidation($validator))->toBeTrue();
    expect(glsr()->sessionGet('form_message'))->toContain('failed to load');
});

test('the secret key is never written to the console in the clear', function () {
    // The failure is logged with the request that caused it, so that a site owner can see
    // WHICH key was wrong — and the console is dumped into support threads and forum posts.
    enableCaptcha('turnstile', [
        'turnstile.key' => '0x4AAAAAAAsitekeyvalue',
        'turnstile.secret' => '0x4AAAAAAAsecretkeyvalue',
    ]);
    interceptHttp(captchaReply(['success' => false, 'error-codes' => ['invalid-input-secret']]));

    turnstile()->validate();

    $console = glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->get();
    expect($console)->not->toContain('0x4AAAAAAAsecretkeyvalue')
        ->and($console)->not->toContain('0x4AAAAAAAsitekeyvalue')
        ->and($console)->toContain('invalid-input-secret'); // but the reason is there
});

/*
 * reCAPTCHA v3, which is the odd one: it does not answer yes or no, it answers with a score.
 */

test('recaptcha v3 rejects a human-shaped bot that scores below the threshold', function () {
    // success:true and still a bot. This is the whole point of v3, and a plugin that only
    // read `success` would pass every bot Google has ever seen.
    enableCaptcha('recaptcha_v3', [
        'recaptcha_v3.key' => 'a-key',
        'recaptcha_v3.secret' => 'a-secret',
        'recaptcha_v3.threshold' => 0.5,
    ]);
    interceptHttp(captchaReply(['success' => true, 'score' => 0.1, 'action' => 'submit_review']));

    $validator = new RecaptchaV3Validator(new Request(['_captcha' => 'a-token', 'ip_address' => '203.0.113.9']));

    expect(failedValidation($validator))->toBeTrue();
});

test('recaptcha v3 accepts a score at or above the threshold', function () {
    enableCaptcha('recaptcha_v3', [
        'recaptcha_v3.key' => 'a-key',
        'recaptcha_v3.secret' => 'a-secret',
        'recaptcha_v3.threshold' => 0.5,
    ]);
    interceptHttp(captchaReply(['success' => true, 'score' => 0.5, 'action' => 'submit_review']));

    $validator = new RecaptchaV3Validator(new Request(['_captcha' => 'a-token', 'ip_address' => '203.0.113.9']));

    expect(failedValidation($validator))->toBeFalse();
});

test('recaptcha v3 rejects a token that was earned on a different form', function () {
    // The action binds the token to the thing it was solved for. Without this check, a token
    // harvested from the site's login form would be accepted as a review submission.
    enableCaptcha('recaptcha_v3', [
        'recaptcha_v3.key' => 'a-key',
        'recaptcha_v3.secret' => 'a-secret',
        'recaptcha_v3.threshold' => 0.5,
    ]);
    interceptHttp(captchaReply(['success' => true, 'score' => 0.9, 'action' => 'login']));

    $validator = new RecaptchaV3Validator(new Request(['_captcha' => 'a-token', 'ip_address' => '203.0.113.9']));

    expect(failedValidation($validator))->toBeTrue();
});

/*
 * The seven services. Each knows its own endpoint, its own settings and its own token field,
 * and getting any of those wrong means the CAPTCHA silently never works.
 */

dataset('captchas', [
    // class, integration slug, verification endpoint, the form field the token arrives in
    'turnstile' => [TurnstileValidator::class, 'turnstile', 'https://challenges.cloudflare.com/turnstile/v0/siteverify', 'cf-turnstile-response'],
    'hcaptcha' => [HcaptchaValidator::class, 'hcaptcha', 'https://hcaptcha.com/siteverify', 'h-captcha-response'],
    'recaptcha_v3' => [RecaptchaV3Validator::class, 'recaptcha_v3', 'https://www.google.com/recaptcha/api/siteverify', 'g-recaptcha-response'],
    'recaptcha_v2_invisible' => [RecaptchaV2InvisibleValidator::class, 'recaptcha_v2_invisible', 'https://www.google.com/recaptcha/api/siteverify', 'g-recaptcha-response'],
    'friendlycaptcha' => [FriendlycaptchaValidator::class, 'friendlycaptcha', 'https://api.friendlycaptcha.com/api/v1/siteverify', 'frc-captcha-solution'],
    'friendlycaptcha_v2' => [FriendlycaptchaV2Validator::class, 'friendlycaptcha_v2', 'https://global.frcapi.com/api/v2/captcha/siteverify', 'frc-captcha-response'],
    'procaptcha' => [ProcaptchaValidator::class, 'procaptcha', 'https://api.prosopo.io/siteverify', 'procaptcha-response'],
]);

test('each service is only enabled when it is the one that was chosen', function (string $class, string $integration) {
    // One CAPTCHA at a time. If two answered isEnabled() the form would render two widgets
    // and verify against the wrong service.
    enableCaptcha($integration);
    expect((new $class(new Request([])))->isEnabled())->toBeTrue();

    enableCaptcha('turnstile' === $integration ? 'hcaptcha' : 'turnstile');
    expect((new $class(new Request([])))->isEnabled())->toBeFalse();
})->with('captchas');

test('each service knows its own endpoint and its own token field', function (
    string $class, string $integration, string $apiUrl, string $tokenField
) {
    // Get either of these wrong and the CAPTCHA does not fail loudly — it fails SILENTLY.
    // The wrong token field means an empty token, which is CAPTCHA_EMPTY, which turns every
    // genuine visitor away with "verification failed" and never says why.
    expect($class::API_URL)->toBe($apiUrl);

    enableCaptcha($integration);
    $config = (new $class(new Request([])))->config();

    expect($config['token_field'])->toBe($tokenField)
        ->and($config['type'])->not->toBeEmpty()   // the frontend switches on this
        ->and($config['class'])->not->toBeEmpty(); // and hangs the widget on this
})->with('captchas');

test('each service tells the browser where to fetch its widget', function (string $class, string $integration) {
    // `module` and `nomodule` are the modern and legacy script tags. Not every service ships
    // both — Procaptcha is module-only, Turnstile and the Googles are nomodule-only — so what
    // matters is that there is SOMETHING to load. A service with no script renders no widget,
    // and a form with no widget cannot produce a token.
    enableCaptcha($integration);
    $urls = (new $class(new Request([])))->config()['urls'];

    expect(array_filter($urls))->not->toBeEmpty()
        ->and(array_keys($urls))->each->toBeIn(['module', 'nomodule']);
})->with('captchas');

/*
 * The verification round-trip, run against EVERY service rather than only Turnstile.
 *
 * The tests above prove each subclass knows its endpoint, its token field and its keys. What they
 * do not do is make it actually verify anything — and everything a subclass overrides lives on
 * that path: requestBody() (each service names its fields differently), siteKey()/siteSecret()
 * (each reads its own settings), errors()/errorCodes() (each has its own vocabulary), and for
 * Friendlycaptcha v2, isTokenValid() itself.
 *
 * So a subclass could be wired to the wrong settings keys and pass every test above. It would send
 * an empty secret to the right URL, be told no, and turn away every genuine visitor on the site —
 * which is the failure that costs a site owner reviews they never learn they lost.
 */

/**
 * A validator for a service, with a token in hand.
 */
function captchaValidator(string $class, array $request = []): object
{
    return new $class(new Request(wp_parse_args($request, [
        '_captcha' => 'a-token-from-the-browser',
        'ip_address' => '203.0.113.9',
    ])));
}

/**
 * What each service ACTUALLY replies, per its own documentation — not a generic {"success":true}.
 *
 * This is the part a shared mock would get wrong, and Procaptcha is the proof: its siteverify does
 * not send a `success` field at all, it sends {"status":"ok","verified":true}, and its validator
 * overrides responseBody() to read exactly that. A test that mocked {"success":true} for every
 * service would have Procaptcha reading `verified` off an array that has no such key, failing, and
 * the test would say the CAPTCHA was working.
 *
 * reCAPTCHA v3 is the other one with a shape of its own: success alone is not enough, the score
 * has to clear the threshold and the action has to be the one the form asked for.
 *
 * @return array{0: array, 1: array} the documented success body, and the documented failure body
 */
function captchaBodies(string $integration): array
{
    return match ($integration) {
        // https://docs.prosopo.io/en/basics/verify-users/
        'procaptcha' => [
            ['status' => 'ok', 'verified' => true],
            ['status' => 'ok', 'verified' => false, 'error' => 'invalid-input-secret'],
        ],
        // https://developers.google.com/recaptcha/docs/v3
        'recaptcha_v3' => [
            ['success' => true, 'score' => 0.9, 'action' => 'submit_review'],
            ['success' => false, 'error-codes' => ['invalid-input-secret']],
        ],
        // https://developer.friendlycaptcha.com/docs/v1/api/siteverify — `errors`, not `error-codes`
        'friendlycaptcha' => [
            ['success' => true],
            ['success' => false, 'errors' => ['secret_missing']],
        ],
        // https://developer.friendlycaptcha.com/docs/v2/api/siteverify — a single `error` object
        'friendlycaptcha_v2' => [
            ['success' => true],
            ['success' => false, 'error' => ['error_code' => 'auth_required']],
        ],
        // hCaptcha, Turnstile and reCAPTCHA v2 all speak `success` + `error-codes`.
        default => [
            ['success' => true],
            ['success' => false, 'error-codes' => ['invalid-input-secret']],
        ],
    };
}

/**
 * The settings group each service keeps its keys under — which is NOT always its own name:
 *
 *   recaptcha_v2_invisible  reads forms.recaptcha.*        (shared with the other reCAPTCHA v2)
 *   friendlycaptcha_v2      reads forms.friendlycaptcha.*  (shared with Friendly Captcha v1)
 *
 * Both are deliberate — upgrading from v1 to v2 must not make somebody re-enter their keys — and
 * both are exactly the kind of thing a rename would quietly break, leaving the service reading an
 * empty secret and turning away every visitor on the site.
 */
function captchaKeyOptions(string $integration): array
{
    $group = match ($integration) {
        'recaptcha_v2_invisible' => 'recaptcha',
        'friendlycaptcha_v2' => 'friendlycaptcha',
        default => $integration,
    };

    return [
        "{$group}.key" => 'a-site-key',
        "{$group}.secret" => 'a-secret-key',
    ];
}

test('every service accepts a token its own server has approved', function (string $class, string $integration) {
    enableCaptcha($integration, captchaKeyOptions($integration));
    [$ok] = captchaBodies($integration);
    $requests = interceptHttp(captchaReply($ok));

    expect(failedValidation(captchaValidator($class)))->toBeFalse();
    expect($requests)->toHaveCount(1); // and it really asked
})->with('captchas');

test('every service sends its secret and the token to its own endpoint', function (
    string $class, string $integration, string $apiUrl
) {
    // The assertion that catches a subclass reading the wrong settings. A secret that came back
    // EMPTY here would still verify against a mocked server saying yes — and would reject every
    // visitor on a real one.
    enableCaptcha($integration, captchaKeyOptions($integration));
    [$ok] = captchaBodies($integration);
    $requests = interceptHttp(captchaReply($ok));

    captchaValidator($class)->validate();

    // The secret does not always travel in the body: Friendly Captcha v2 authenticates with an
    // X-API-Key HEADER and sends JSON. So look at the whole request rather than at the body — what
    // matters is that the service found its own key and secret, not where it put them.
    $sent = wp_json_encode([
        'body' => $requests[0]['args']['body'] ?? [],
        'headers' => $requests[0]['args']['headers'] ?? [],
    ]);

    expect($requests[0]['url'])->toBe($apiUrl);
    expect($sent)
        ->toContain('a-secret-key')             // it found its own secret…
        ->toContain('a-token-from-the-browser'); // …and sent the visitor's token
})->with('captchas');

test('no service makes the visitor wait while it retries a broken server', function (
    string $class, string $integration
) {
    // ONE attempt, for every service. The CAPTCHA is verified inside the submit-review request,
    // with a person watching a spinner — so Api's backoff (about 1s, then 1.6, then 2.6, then 4.1)
    // is right for a licence check, where nobody is waiting, and wrong here. Their retry is
    // pressing the button again.
    //
    // The abstract passes max_retries => 1. A subclass that overrides requestArgs() — which
    // Friendly Captcha v2 must, because v2 authenticates with a header — can drop it without
    // anything failing, and did. The visitor pays for that in seconds.
    enableCaptcha($integration, captchaKeyOptions($integration));
    $requests = interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    captchaValidator($class)->validate();

    expect($requests)->toHaveCount(1);
})->with('captchas');

test('every service rejects a token its own server has refused', function (string $class, string $integration) {
    enableCaptcha($integration, captchaKeyOptions($integration));
    [, $refused] = captchaBodies($integration);
    interceptHttp(captchaReply($refused));

    expect(failedValidation(captchaValidator($class)))->toBeTrue();
})->with('captchas');

test('every service refuses when its own server cannot be reached', function (string $class, string $integration) {
    // Fail CLOSED. A CAPTCHA that waved reviews through when the service was down would be a
    // CAPTCHA a spammer could switch off by taking the service down.
    enableCaptcha($integration, captchaKeyOptions($integration));
    interceptHttp(['response' => ['code' => 500, 'message' => 'Internal Server Error']]);

    expect(failedValidation(captchaValidator($class)))->toBeTrue();
})->with('captchas');

test('every service refuses when the form sent no token at all', function (string $class, string $integration) {
    // CAPTCHA_EMPTY, and it never asks the service — which is the point: a bot that skips the
    // widget entirely must not cost the site an HTTP request per submission.
    enableCaptcha($integration, captchaKeyOptions($integration));
    [$ok] = captchaBodies($integration);
    $requests = interceptHttp(captchaReply($ok));

    expect(failedValidation(captchaValidator($class, ['_captcha' => ''])))->toBeTrue();
    expect($requests)->toHaveCount(0);
})->with('captchas');

test('every service explains itself when its own server sends an error code', function (
    string $class, string $integration
) {
    // errorCodes() is a per-service dictionary, and it is only ever read here. An error the
    // service sends and the plugin cannot name is logged as a bare code, which is a support
    // ticket nobody can answer. The expected explanations are HARDCODED from each service's
    // errorCodes() — reading the dictionary at runtime would let a lost mapping change both
    // sides of the assertion. Procaptcha's responseBody() does not map through errorCodes(),
    // so its fixture code is logged bare.
    enableCaptcha($integration, captchaKeyOptions($integration));
    [, $refused] = captchaBodies($integration);
    interceptHttp(captchaReply($refused));

    $explanations = [
        'friendlycaptcha' => 'Your secret key is missing.',
        'friendlycaptcha_v2' => 'You forgot to set the X-API-Key header.',
        'hcaptcha' => 'Your secret key is invalid or malformed.',
        'procaptcha' => 'invalid-input-secret',
        'recaptcha_v2_invisible' => 'The secret parameter is invalid or malformed.',
        'recaptcha_v3' => 'The secret parameter is invalid or malformed.',
        'turnstile' => 'Secret key is invalid or expired: check your secret key in the Cloudflare dashboard',
    ];

    expect(failedValidation(captchaValidator($class)))->toBeTrue();
    expect(glsr(\GeminiLabs\SiteReviews\Modules\Console::class)->get())->toContain($explanations[$integration]);
})->with('captchas');

test('a prosopo rejection is logged with its named diagnostics, not just the raw code', function () {
    // The error codes prosopo sends are terse; errorCodes() names them, and a
    // missing site key is flagged alongside whatever the service answered.
    enableCaptcha('procaptcha'); // deliberately no site key configured
    $validator = new ProcaptchaValidator(new Request(['ip_address' => '203.0.113.9']));
    $response = new \GeminiLabs\SiteReviews\Response();
    $response->body = ['status' => 'x', 'verified' => false, 'error' => 'sitekey_invalid'];
    $response->code = 200;

    $body = \GeminiLabs\SiteReviews\Tests\protectedMethod(ProcaptchaValidator::class, 'responseBody')
        ->invoke($validator, $response);

    expect($body['success'])->toBeFalse()
        ->and($body['errors'])->toBe([
            'sitekey_invalid' => 'Your site key is likely invalid.',
            'sitekey_missing' => 'Your site key is missing.',
        ]);
});
