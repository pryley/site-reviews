<?php

use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Request;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Request object: the plugin's sanitized view of an HTTP request. Two pieces matter
 * beyond what Arguments already does:
 *
 *   inputGet()        the tokenized GET request — the approve/verify links in emails carry
 *                     their whole payload in one encrypted `glsr_` parameter.
 *   set()             a request that carries a form signature (the encrypted record of the
 *                     hidden fields, see Form::signForm) must RE-SIGN itself when one of the
 *                     signed values is changed, or the changed request would fail validation.
 */

beforeEach(function () {
    resetPluginState();
    $_GET = [];
});

afterEach(function () {
    $_GET = [];
});

test('a tokenized GET request unpacks its action and payload', function () {
    // What an email's approve link looks like when it lands: everything in one parameter.
    $_GET[glsr()->prefix] = glsr(Encryption::class)->encryptRequest('approve', ['123']);

    $request = Request::inputGet();

    expect($request->action)->toBe('approve')
        ->and($request->data)->toBe(['123']);
});

test('a GET request without a token is empty, and a garbage token too', function () {
    expect(Request::inputGet()->isEmpty())->toBeTrue();

    $_GET[glsr()->prefix] = 'not-a-real-token';
    expect(Request::inputGet()->isEmpty())->toBeTrue();
});

test('changing a signed value re-signs the form signature', function () {
    // The signature is the tamper-proof record of the hidden fields. Code that legitimately
    // changes one of them (an addon adjusting assigned_posts) must leave a signature that
    // still matches, or the submission is rejected as tampered.
    $signed = ['assigned_posts' => '17', 'form_id' => 'abc123'];
    $request = new Request([
        'assigned_posts' => '17',
        'form_id' => 'abc123',
        'form_signature' => glsr(Encryption::class)->encrypt(maybe_serialize($signed)),
    ]);

    $request->set('assigned_posts', '42');

    expect($request->assigned_posts)->toBe('42');
    $resigned = maybe_unserialize($request->decrypt('form_signature'));
    expect($resigned['assigned_posts'])->toBe('42')  // the signature agrees with the change
        ->and($resigned['form_id'])->toBe('abc123'); // and the rest of it is untouched
});

test('changing an unsigned value leaves the signature alone', function () {
    $signed = ['form_id' => 'abc123'];
    $signature = glsr(Encryption::class)->encrypt(maybe_serialize($signed));
    $request = new Request(['form_signature' => $signature]);

    $request->set('rating', '5'); // not part of the signature

    expect($request->cast('form_signature', 'string'))->toBe($signature);
});

test('a fallback can be lazy', function () {
    // get() runs closures given as fallbacks — Arguments::get() does not — so an expensive
    // default is only computed when the key is actually missing.
    $request = new Request(['present' => 'value']);

    expect($request->get('present', fn () => 'never-computed'))->toBe('value');
    expect($request->get('absent', fn () => 'computed'))->toBe('computed');
});
