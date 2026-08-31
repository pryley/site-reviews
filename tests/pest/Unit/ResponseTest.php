<?php

use GeminiLabs\SiteReviews\Response;
use GeminiLabs\SiteReviews\Tests\UnserializeProbe;

/*
 * Response wraps a wp_remote_* result. Some APIs send values as serialized PHP inside the
 * JSON (EDD-style update servers do this for sections and banners), so body() and data()
 * unserialize what looks serialized. Those bytes came over the network — ip-api.com is
 * reached over plain HTTP — so an object must never come back out of them: unserialize()
 * runs __wakeup() before any consumer gets to look. Arrays and scalars are all any consumer
 * wants (every one feeds the result through a Defaults::restrict() or reads scalar keys).
 */

/**
 * A wp_remote_* result carrying $body, as Response's constructor receives it.
 */
function remoteResult(string $body): array
{
    return ['body' => $body, 'response' => ['code' => 200, 'message' => 'OK']];
}

test('a serialized object in a JSON string value is never restored', function () {
    // The JSON parses; one of its values happens to be a serialized object.
    UnserializeProbe::reset();
    $json = wp_json_encode(['success' => true, 'hostname' => serialize(new UnserializeProbe())]);

    $body = (new Response(remoteResult($json)))->body();

    expect(UnserializeProbe::$awoken)->toBeFalse()
        ->and($body['hostname'])->not->toBeInstanceOf(UnserializeProbe::class)
        ->and($body['success'])->toBeTrue(); // the rest of the body is unaffected
});

test('a raw body that is a serialized object is never restored', function () {
    // Not JSON at all: the constructor wraps the raw text as ['result' => ...].
    UnserializeProbe::reset();

    $body = (new Response(remoteResult(serialize(new UnserializeProbe()))))->body();

    expect(UnserializeProbe::$awoken)->toBeFalse()
        ->and($body['result'])->not->toBeInstanceOf(UnserializeProbe::class);
});

test('a serialized object under data is never restored either', function () {
    // data() maps over body['data'], the other place a value is unserialized.
    UnserializeProbe::reset();
    $json = wp_json_encode(['data' => ['x' => serialize(new UnserializeProbe())]]);

    $data = (new Response(remoteResult($json)))->data();

    expect(UnserializeProbe::$awoken)->toBeFalse()
        ->and($data['x'])->not->toBeInstanceOf(UnserializeProbe::class);
});

test('a serialized array still decodes, in the body and under data', function () {
    // The legitimate case: refusing objects must not refuse the arrays the APIs send.
    $sections = ['changelog' => '<p>Fixed things.</p>', 'description' => 'A plugin.'];
    $json = wp_json_encode([
        'sections' => serialize($sections),
        'data' => ['banners' => serialize(['low' => 'https://example.org/b.png'])],
    ]);
    $response = new Response(remoteResult($json));

    expect($response->body()['sections'])->toBe($sections)
        ->and($response->data()['banners'])->toBe(['low' => 'https://example.org/b.png']);
});

test('values that are not serialized pass through untouched', function () {
    $json = wp_json_encode(['status' => 'success', 'query' => '203.0.113.9', 'nested' => ['a' => 1]]);

    $body = (new Response(remoteResult($json)))->body();

    expect($body)->toBe(['status' => 'success', 'query' => '203.0.113.9', 'nested' => ['a' => 1]]);
});
