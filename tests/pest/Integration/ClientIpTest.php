<?php

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\ClientIp;
use GeminiLabs\Vectorface\Whip\Whip;

uses()->group('plugin');

/**
 * The address ClientIp works out for a request. $server is merged over $_SERVER for the call (a
 * null value removes the key) and $_SERVER is put back afterwards; $setting is the saved
 * Configure IP Address Detection tool.
 */
function clientIpFor(array $server, array $setting = []): string
{
    update_option(glsr()->prefix.'ip_proxy', wp_parse_args($setting, [
        'proxy_http_header' => '',
        'trusted_proxies' => '',
    ]));
    $original = $_SERVER;
    $_SERVER = array_filter(array_merge($_SERVER, $server), fn ($value) => !is_null($value));
    try {
        return glsr(ClientIp::class)->address();
    } finally {
        $_SERVER = $original;
    }
}

test('the address is the connecting address when no proxy header is selected', function () {
    // A proxy header the tool was not told about is not read, whatever it holds.
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7',
        'REMOTE_ADDR' => '203.0.113.9',
    ]);

    expect($address)->toBe('203.0.113.9');
});

test('the address is read from the selected proxy header', function () {
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($address)->toBe('198.51.100.7');
});

test('the address is unknown, and logged, when the request has no connecting address', function () {
    // WP-CLI and cron runs of the PHP binary have no REMOTE_ADDR.
    $address = clientIpFor(['REMOTE_ADDR' => null]);

    expect($address)->toBe('unknown')
        ->and(glsr_log()->get())->toContain('Unable to detect IP address');
});

test('the helper returns the address ClientIp works out', function () {
    $original = $_SERVER['REMOTE_ADDR'] ?? null;
    $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
    try {
        expect(Helper::clientIp())->toBe('203.0.113.9');
    } finally {
        $_SERVER['REMOTE_ADDR'] = $original;
    }
});

test('the whip action receives the Whip instance', function () {
    // The FAQ's snippet adds its custom header to this object.
    $received = null;
    add_action('site-reviews/whip', function ($whip) use (&$received) {
        $received = $whip;
    });

    clientIpFor(['REMOTE_ADDR' => '203.0.113.9']);

    expect($received)->toBeInstanceOf(Whip::class);
});

test('the whitelist filter is applied on every lookup', function () {
    $received = null;
    add_filter('site-reviews/whip/whitelist', function ($whitelist) use (&$received) {
        $received = $whitelist;
        return $whitelist;
    });

    clientIpFor(['REMOTE_ADDR' => '203.0.113.9']);

    expect($received)->toBeArray();
});

test('a trusted proxies list is read one line at a time, whatever ends the lines', function () {
    // A browser posts a textarea with CRLF line endings; a list typed into the database or passed
    // to the filter may use LF, or a lone CR.
    $clientIp = glsr(ClientIp::class);

    expect($clientIp->ranges("10.0.0.1\r\n10.0.0.2\r\n10.0.0.0/8\r\n2001:db8::1\r\n10.0.0.3"))
        ->toBe(['10.0.0.1', '10.0.0.2', '10.0.0.0/8', '2001:db8::1', '10.0.0.3'])
        ->and($clientIp->ranges("10.0.0.1\n10.0.0.0/8\r2001:db8::/32"))
        ->toBe(['10.0.0.1', '10.0.0.0/8', '2001:db8::/32']);
});

test('a trusted proxies list drops blank lines, padding, and lines that are not an address', function () {
    $ranges = glsr(ClientIp::class)->ranges(" 10.0.0.1 \n\nnot-an-address\n10.0.0.0/8\n");

    expect($ranges)->toBe(['10.0.0.1', '10.0.0.0/8']);
});

test('a trusted proxies list saved with a stray carriage return is read without it', function () {
    // Up to 8.3.4 the tool split the posted list on LF only, so every line but the last kept its
    // CR. A plain address with a CR failed validation and was dropped; a CIDR range passed,
    // because only the part before the slash was validated, and was saved with the CR attached.
    $ranges = glsr(ClientIp::class)->ranges("10.0.0.0/8\r\n10.0.0.3");

    expect($ranges)->toBe(['10.0.0.0/8', '10.0.0.3']);
});
