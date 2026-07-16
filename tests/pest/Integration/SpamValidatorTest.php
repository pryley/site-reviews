<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Validator\AkismetValidator;
use GeminiLabs\SiteReviews\Modules\Validator\SignatureValidator;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The two validators that ask whether the submission is what it claims to be.
 *
 * Akismet asks someone else: is this spam? Opt-in, needs a key, and when not set up must get out of
 * the way completely — a site with no key must not silently reject every review, nor send anything
 * anywhere.
 *
 * The signature asks the form: are these the values I gave you? Every form carries an encrypted
 * `form_signature` holding the values the SERVER chose (form id, the post reviewed, the
 * assignments). They come back through the browser, where anyone can edit them, and the signature is
 * how the plugin notices — without it a visitor could point a review at any post, or assign it to
 * any user, by editing a hidden input.
 */

beforeEach(function () {
    resetPluginState();
    Akismet::reset();
});

afterEach(function () {
    Akismet::reset();
});

function akismet(array $request = []): AkismetValidator
{
    return new AkismetValidator(new Request(wp_parse_args($request, [
        'content' => 'The room was lovely.',
        'email' => 'jane@example.org',
        'ip_address' => '203.0.113.9',
        'name' => 'Jane Doe',
        'title' => 'A lovely stay',
    ])));
}

/**
 * Turns Akismet on, as a site owner would: the setting, and a key in the Akismet plugin.
 */
function enableAkismet(string $verdict = 'false'): void
{
    glsr(OptionManager::class)->set('settings.forms.akismet', 'yes');
    Akismet::$apiKey = 'a-real-akismet-key';
    Akismet::$verdict = $verdict;
}

/**
 * The payload Akismet was given, decoded back out of the url-encoded body.
 */
function akismetPayload(): array
{
    parse_str(Akismet::$lastRequest, $payload);

    return $payload;
}

/*
 * Akismet, when it is not set up.
 */

test('a site without akismet is not asked, and nothing is rejected', function () {
    // Three ways to be "not set up", and all three must pass the review AND stay silent.
    // Getting this wrong rejects every review on a site that never asked for spam checking.
    expect(akismet()->isValid())->toBeTrue(); // setting off, no key
    expect(Akismet::$lastRequest)->toBe('');

    glsr(OptionManager::class)->set('settings.forms.akismet', 'yes'); // setting on, still no key
    expect(akismet()->isValid())->toBeTrue();
    expect(Akismet::$lastRequest)->toBe('');

    glsr(OptionManager::class)->set('settings.forms.akismet', 'no'); // key, but the setting is off
    Akismet::$apiKey = 'a-real-akismet-key';
    Akismet::$verdict = 'true'; // it WOULD say spam
    expect(akismet()->isValid())->toBeTrue();
    expect(Akismet::$lastRequest)->toBe('');
});

/*
 * Akismet, when it is.
 */

test('a review akismet calls ham is submitted', function () {
    enableAkismet('false');

    expect(akismet()->isValid())->toBeTrue();
    expect(Akismet::$lastRequest)->not->toBe(''); // and it really was asked
});

test('a review akismet calls spam is refused, and the person is told', function () {
    enableAkismet('true');
    $validator = akismet();

    $validator->validate();

    expect($validator->isValid())->toBeFalse();
    expect(glsr()->sessionGet('form_message'))->toContain('flagged as possible spam');
});

test('akismet is given the review, and who wrote it', function () {
    // The payload is Akismet's comment-check shape. `comment_type` is what tells it this is a
    // review and not a blog comment, which is what its model is trained on.
    enableAkismet();

    akismet()->isValid();
    $payload = akismetPayload();

    expect($payload['comment_type'])->toBe('review')
        ->and($payload['comment_author'])->toBe('Jane Doe')
        ->and($payload['comment_author_email'])->toBe('jane@example.org')
        ->and($payload['comment_content'])->toContain('A lovely stay')
        ->and($payload['comment_content'])->toContain('The room was lovely.')
        ->and($payload['user_ip'])->toBe('203.0.113.9')
        ->and($payload['blog'])->toBe(get_option('home'));
});

test('the visitor\'s cookies and http auth password are never sent to akismet', function () {
    // Akismet is given the whole of $_SERVER, because its model uses the request headers. That
    // is a lot of trust, and three keys are held back by name. A session cookie in that payload
    // would be somebody's logged-in session, leaving the site.
    enableAkismet();
    $_SERVER['HTTP_COOKIE'] = 'wordpress_logged_in_abc=jane%7C1234%7Csecrettoken';
    $_SERVER['HTTP_COOKIE2'] = 'another=cookie';
    $_SERVER['PHP_AUTH_PW'] = 'the-http-auth-password';
    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';

    akismet()->isValid();

    expect(Akismet::$lastRequest)->not->toContain('secrettoken')
        ->and(Akismet::$lastRequest)->not->toContain('the-http-auth-password');
    $payload = akismetPayload();
    expect($payload)->not->toHaveKey('HTTP_COOKIE')
        ->and($payload)->not->toHaveKey('HTTP_COOKIE2')
        ->and($payload)->not->toHaveKey('PHP_AUTH_PW')
        ->and($payload['HTTP_USER_AGENT'])->toBe('Mozilla/5.0'); // but the rest of it does go

    unset($_SERVER['HTTP_COOKIE'], $_SERVER['HTTP_COOKIE2'], $_SERVER['PHP_AUTH_PW'], $_SERVER['HTTP_USER_AGENT']);
});

test('a site can overrule akismet, in either direction', function () {
    // `validate/akismet` is the last word — a site that keeps getting false positives from one
    // of its own customers needs to be able to say so.
    enableAkismet('true'); // spam
    add_filter('site-reviews/validate/akismet', '__return_true');
    expect(akismet()->isValid())->toBeTrue();

    remove_all_filters('site-reviews/validate/akismet');
    enableAkismet('false'); // ham
    add_filter('site-reviews/validate/akismet', '__return_false');
    expect(akismet()->isValid())->toBeFalse();
});

/*
 * The form signature.
 */

/**
 * A submission carrying a signature over the given values, exactly as the form builds it.
 */
function signed(array $signedValues, array $overrides = []): SignatureValidator
{
    $request = new Request(array_merge($signedValues, [
        'form_signature' => glsr(Encryption::class)->encrypt(maybe_serialize($signedValues)),
    ], $overrides));

    return new SignatureValidator($request);
}

test('a submission that comes back as it was sent is accepted', function () {
    $validator = signed([
        'assigned_posts' => '13',
        'form_id' => 'a-form-id',
    ]);

    expect($validator->isValid())->toBeTrue();
});

test('a review cannot be pointed at a post it was not written about', function () {
    // THE POINT OF THE SIGNATURE. `assigned_posts` decides which page the review appears on.
    // It is a hidden input; anybody can edit it. Without the signature, one form on one page
    // would let somebody attach reviews to every page on the site.
    $validator = signed(
        ['assigned_posts' => '13', 'form_id' => 'a-form-id'],
        ['assigned_posts' => '99'] // edited in the browser
    );

    $validator->validate();

    expect($validator->isValid())->toBeFalse();
    expect(glsr()->sessionGet('form_message'))->toContain('refresh the page');
});

test('a signature from a different form is not accepted', function () {
    $validator = signed(
        ['form_id' => 'the-form-they-were-given'],
        ['form_id' => 'a-different-form']
    );

    expect($validator->isValid())->toBeFalse();
});

test('a submission with no signature at all is not accepted', function () {
    // An empty signature decrypts to nothing, which parses to ['form_id' => ''] — and the
    // request's form_id is not empty, so it does not match. A bot posting the bare fields it
    // scraped, without the hidden signature, gets nowhere.
    $validator = new SignatureValidator(new Request(['form_id' => 'a-form-id']));

    expect($validator->isValid())->toBeFalse();
});

test('a signature that has been tampered with is worth nothing', function () {
    // The signature is a sodium secretbox: its MAC fails before any of it is read, so a
    // meddled-with token decrypts to an empty string rather than to a partial payload.
    $validator = new SignatureValidator(new Request([
        'form_id' => 'a-form-id',
        'form_signature' => 'tampered'.glsr(Encryption::class)->encrypt(maybe_serialize(['form_id' => 'a-form-id'])),
    ]));

    expect($validator->isValid())->toBeFalse();
});

test('a site can overrule the signature check', function () {
    $validator = signed(['form_id' => 'a-form'], ['form_id' => 'another-form']);
    add_filter('site-reviews/validate/signature', '__return_true');

    expect($validator->isValid())->toBeTrue();
});
