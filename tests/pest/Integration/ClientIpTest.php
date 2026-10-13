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

test('the whitelist filter receives the saved trusted proxies in the format Whip reads', function () {
    // Whip looks a list up by the method it belongs to, and the tool's header is a custom header.
    $received = null;
    add_filter('site-reviews/whip/whitelist', function ($whitelist) use (&$received) {
        $received = $whitelist;
        return $whitelist;
    });

    clientIpFor(['REMOTE_ADDR' => '203.0.113.9'], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.1\n2001:db8::/32\n10.0.0.0/8",
    ]);

    expect($received)->toBe([
        Whip::CUSTOM_HEADERS => [
            Whip::IPV4 => ['10.0.0.1', '10.0.0.0/8'],
            Whip::IPV6 => ['2001:db8::/32'],
        ],
    ]);
});

test('the whitelist filter receives two empty lists when no trusted proxies are saved', function () {
    $received = null;
    add_filter('site-reviews/whip/whitelist', function ($whitelist) use (&$received) {
        $received = $whitelist;
        return $whitelist;
    });

    clientIpFor(['REMOTE_ADDR' => '203.0.113.9']);

    expect($received)->toBe([
        Whip::CUSTOM_HEADERS => [
            Whip::IPV4 => [],
            Whip::IPV6 => [],
        ],
    ]);
});

test('the proxy header is not read when the request comes from an address that is not a trusted proxy', function () {
    // The reported defect: the list was passed to Whip under the header's name, Whip looked it up
    // by method, found nothing, and read the header from anyone. A visitor who reached the web
    // server with the header set chose their own address.
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '1.2.3.4',
        'REMOTE_ADDR' => '203.0.113.9',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($address)->toBe('203.0.113.9');
});

test('the proxy header is not read when no trusted proxies are saved', function () {
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '1.2.3.4',
        'REMOTE_ADDR' => '203.0.113.9',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
    ]);

    expect($address)->toBe('203.0.113.9');
});

test('the proxy header is read when the request comes from inside a trusted range', function (string $peer, string $trusted) {
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7',
        'REMOTE_ADDR' => $peer,
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => $trusted,
    ]);

    expect($address)->toBe('198.51.100.7');
})->with([
    'an IPv4 range' => ['10.2.3.4', '10.0.0.0/8'],
    'an IPv6 address' => ['2001:db8::5', '2001:db8::5'],
    'an IPv6 range' => ['2001:db8::5', "10.0.0.1\n2001:db8::/32"],
    'every IPv4 address' => ['203.0.113.9', '0.0.0.0/0'],
]);

/*
 * Which address of the header is the visitor.
 *
 * A proxy adds the address that connected to it to the end of the header and keeps what was there.
 * So whatever the visitor sent is on the left, and only the entries that trusted proxies added, on
 * the right, can be believed.
 */

test('an address the visitor put in the header is not read', function () {
    // The visitor at 203.0.113.5 sends "X-Forwarded-For: 198.51.100.7". The proxy adds the visitor.
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7, 203.0.113.5',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($address)->toBe('203.0.113.5');
});

test('the trusted proxies in the header are skipped to reach the visitor', function () {
    // Two proxies in a chain: 10.0.0.2 received the visitor and 10.0.0.1 connected to the server.
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7, 203.0.113.5, 10.0.0.2',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.0/8',
    ]);

    expect($address)->toBe('203.0.113.5');
});

test('the first address is read when every address in the header is a trusted proxy', function () {
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '10.0.0.5, 10.0.0.2',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.0/8',
    ]);

    expect($address)->toBe('10.0.0.5');
});

test('an address in the header is read without its port and brackets', function (string $header, string $expected) {
    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => $header,
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.0/8',
    ]);

    expect($address)->toBe($expected);
})->with([
    'IPv4 with a port' => ['203.0.113.5:4711', '203.0.113.5'],
    'IPv6 in brackets with a port' => ['[2001:db8::7]:4711', '2001:db8::7'],
    'IPv6 in brackets' => ['[2001:db8::7]', '2001:db8::7'],
    'IPv6 as it is' => ['2001:db8::7', '2001:db8::7'],
    'a trusted proxy with a port is still skipped' => ['203.0.113.5, 10.0.0.2:4711', '203.0.113.5'],
    'an empty entry is skipped' => ['203.0.113.5, ', '203.0.113.5'],
]);

test('the address is read from the for parameter of the Forwarded header', function (string $header, string $expected) {
    $address = clientIpFor([
        'HTTP_FORWARDED' => $header,
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'forwarded',
        'trusted_proxies' => '10.0.0.0/8',
    ]);

    expect($address)->toBe($expected);
})->with([
    'a token' => ['for=203.0.113.5', '203.0.113.5'],
    'with other parameters' => ['for=203.0.113.5;proto=https;by=10.0.0.1', '203.0.113.5'],
    'after other parameters' => ['proto=https;For=203.0.113.5', '203.0.113.5'],
    'a quoted IPv6 address with a port' => ['for="[2001:db8::7]:4711";proto=https', '2001:db8::7'],
    'the last element that is not a trusted proxy' => ['for=198.51.100.7, for=203.0.113.5, for=10.0.0.2', '203.0.113.5'],
    'an address with no parameter name' => ['203.0.113.5', '203.0.113.5'],
]);

test('the address is unknown when the entry of the visitor is not an address', function (string $header, string $value) {
    // RFC 7239 lets a proxy hide the visitor behind "unknown" or an obfuscated name. Whatever is
    // left of that entry was sent by the visitor.
    $address = clientIpFor([
        $header => $value,
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => strtolower(str_replace('_', '-', substr($header, 5))),
        'trusted_proxies' => '10.0.0.0/8',
    ]);

    expect($address)->toBe('unknown')
        ->and(glsr_log()->get())->toContain('Unable to detect IP address');
})->with([
    'an obfuscated name' => ['HTTP_FORWARDED', 'for=198.51.100.7, for=_hidden'],
    'an element with no for parameter' => ['HTTP_FORWARDED', 'for=198.51.100.7, proto=https;by=10.0.0.2'],
    'a name in X-Forwarded-For' => ['HTTP_X_FORWARDED_FOR', '198.51.100.7, proxy.example.org'],
]);

test('a header added through the whip action is read from the left', function () {
    // Whip keeps its header list private, so ClientIp cannot find the visitor in a header a code
    // snippet adds. The Custom option of the tool is the way to have such a header read correctly.
    add_action('site-reviews/whip', fn ($whip) => $whip->addCustomHeader('HTTP_X_SUCURI_CLIENTIP'));

    $address = clientIpFor([
        'HTTP_X_SUCURI_CLIENTIP' => '198.51.100.7, 203.0.113.5',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($address)->toBe('198.51.100.7');
});

test('the address is unknown when a proxy is trusted and the request has no connecting address', function () {
    // Whip passes the missing address to IpWhitelist::isIpWhitelisted(string), which throws a
    // TypeError. `wp site-reviews ip-address` is such a request.
    $address = clientIpFor(['REMOTE_ADDR' => null], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($address)->toBe('unknown');
});

test('the address is unknown when the connecting address is not an IP address', function () {
    // nginx reports "unix:" for a connection it accepted on a unix socket.
    $address = clientIpFor(['REMOTE_ADDR' => 'unix:'], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '2001:db8::/32',
    ]);

    expect($address)->toBe('unknown');
});

test('a header added through the whip action is not read when no trusted proxies are saved', function () {
    // The FAQ's snippet. Whip keeps one list for all custom headers.
    add_action('site-reviews/whip', fn ($whip) => $whip->addCustomHeader('HTTP_X_SUCURI_CLIENTIP'));

    $address = clientIpFor([
        'HTTP_X_SUCURI_CLIENTIP' => '198.51.100.7',
        'REMOTE_ADDR' => '203.0.113.9',
    ]);

    expect($address)->toBe('203.0.113.9');
});

test('a header added through the whip action is read from a proxy the whitelist filter adds', function () {
    add_action('site-reviews/whip', fn ($whip) => $whip->addCustomHeader('HTTP_X_SUCURI_CLIENTIP'));
    add_filter('site-reviews/whip/whitelist', function ($whitelist) {
        $whitelist[Whip::CUSTOM_HEADERS][Whip::IPV4][] = '203.0.113.0/24';
        return $whitelist;
    });

    $address = clientIpFor([
        'HTTP_X_SUCURI_CLIENTIP' => '198.51.100.7',
        'REMOTE_ADDR' => '203.0.113.9',
    ]);

    expect($address)->toBe('198.51.100.7');
});

test('a header added through the whip action is read from a proxy saved in the tool', function () {
    // The dropdown stays on None for a header it does not list. The saved list still applies.
    add_action('site-reviews/whip', fn ($whip) => $whip->addCustomHeader('HTTP_X_SUCURI_CLIENTIP'));

    $address = clientIpFor([
        'HTTP_X_SUCURI_CLIENTIP' => '198.51.100.7',
        'REMOTE_ADDR' => '203.0.113.9',
    ], [
        'trusted_proxies' => '203.0.113.0/24',
    ]);

    expect($address)->toBe('198.51.100.7');
});

test('ranges the whitelist filter returns are checked before Whip is given them', function () {
    // A callback can return anything. An IPv4 mask above 32 throws inside Whip, and Whip reads
    // an IPv6 range in the IPv4 list as an IPv4 range.
    add_filter('site-reviews/whip/whitelist', fn () => [
        Whip::CUSTOM_HEADERS => [
            Whip::IPV4 => ['10.0.0.1/33', ['not-a-string'], ' 2001:db8::/32 '],
            Whip::IPV6 => 'not-a-list',
        ],
    ]);

    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7',
        'REMOTE_ADDR' => '2001:db8::5',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
    ]);

    expect($address)->toBe('198.51.100.7');
});

test('a whitelist filter that returns no custom header list leaves no trusted proxies', function () {
    add_filter('site-reviews/whip/whitelist', fn () => ['x-forwarded-for' => [Whip::IPV4 => ['203.0.113.9']]]);

    $address = clientIpFor([
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7',
        'REMOTE_ADDR' => '203.0.113.9',
    ], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => '203.0.113.9',
    ]);

    expect($address)->toBe('203.0.113.9');
});

test('the tool lists the proxy headers a reverse proxy commonly sends', function () {
    expect(array_keys(glsr(ClientIp::class)->headers()))->toBe([
        'cf-connecting-ip',
        'client-ip',
        'forwarded',
        'forwarded-for',
        'incap-client-ip',
        'x-cluster-client-ip',
        'x-forwarded',
        'x-forwarded-for',
        'x-real-ip',
    ]);
});

test('a custom header name is saved in the form the listed headers have', function (string $entered, string $saved) {
    expect(glsr(ClientIp::class)->normalizeHeader($entered))->toBe($saved);
})->with([
    'as the proxy documents it' => ['X-Sucuri-ClientIP', 'x-sucuri-clientip'],
    'as PHP shows it in $_SERVER' => ['HTTP_X_SUCURI_CLIENTIP', 'x-sucuri-clientip'],
    'with underscores and padding' => [' x_real_ip ', 'x-real-ip'],
    'with characters a header name cannot have' => ['X Client: IP', 'xclientip'],
    'empty' => ['', ''],
]);

test('a custom header saved in the tool is read from a trusted proxy', function () {
    $address = clientIpFor([
        'HTTP_X_SUCURI_CLIENTIP' => '198.51.100.7',
        'REMOTE_ADDR' => '10.0.0.1',
    ], [
        'proxy_http_header' => 'x-sucuri-clientip',
        'trusted_proxies' => '10.0.0.1',
    ]);

    expect($address)->toBe('198.51.100.7');
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

test('a trusted proxies list keeps a range whose mask fits the address', function (string $range) {
    expect(glsr(ClientIp::class)->ranges($range))->toBe([$range]);
})->with([
    '10.0.0.0/8',
    '10.0.0.1/32',
    '0.0.0.0/0', // every IPv4 address, when that is what the site owner means
    '2001:db8::/32',
    '2001:db8::1/128',
    '::/0',
]);

test('a trusted proxies list drops a range whose mask is not usable', function (string $range) {
    // Whip casts the mask to an integer without checking it. "abc" and "" become 0, which is
    // every address; an IPv4 mask above 32 is a negative bit shift, which throws.
    expect(glsr(ClientIp::class)->ranges($range))->toBe([]);
})->with([
    '10.0.0.1/abc',
    '10.0.0.1/',
    '10.0.0.1/33',
    '10.0.0.1/-1',
    '10.0.0.1/8/8',
    '10.0.0.1/ 8',
    '2001:db8::1/abc',
    '2001:db8::1/',
    '2001:db8::/129',
]);

test('a saved range with an IPv4 mask above 32 does not stop the address being worked out', function () {
    // Whip builds its range objects when it is constructed, so this line threw on every lookup,
    // and with it every review submission, on a site that had saved it.
    $address = clientIpFor(['REMOTE_ADDR' => '203.0.113.9'], [
        'proxy_http_header' => 'x-forwarded-for',
        'trusted_proxies' => "10.0.0.1/33\n10.0.0.2",
    ]);

    expect($address)->toBe('203.0.113.9');
});

