<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\GeolocationDefaults;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Geolocation
{
    public const FIELDS = [
        'city',
        'continentCode',
        'countryCode',
        'message',
        'region',
        'status',
    ];
    public const IP_API_URL = 'http://ip-api.com';

    protected Api $api;

    public function __construct()
    {
        $this->api = glsr(Api::class, ['url' => static::IP_API_URL]);
    }

    public function batchLookup(array $ipaddresses): Response
    {
        $data = array_map([glsr(Sanitizer::class), 'sanitizeIpAddress'], $ipaddresses);
        $path = sprintf('/batch?fields=%s', implode(',', static::FIELDS));
        $response = $this->api->post($path, [
            'body' => wp_json_encode($data),
        ]);
        if ($response->successful()) {
            $body = $response->body();
            $response->body = array_map([glsr(GeolocationDefaults::class), 'unguardedRestrict'], $body);
        } else {
            glsr_log()->warning($response);
        }
        return $response;
    }

    public function lookup(string $ipaddress): Response
    {
        $data = [
            'fields' => implode(',', static::FIELDS),
        ];
        $path = sprintf('/json/%s', glsr(Sanitizer::class)->sanitizeIpAddress($ipaddress));
        $response = $this->api->get($path, [
            'body' => $data,
        ]);
        if ($response->successful()) {
            $body = $response->body();
            $response->body = glsr(GeolocationDefaults::class)->unguardedRestrict($body);
        } else {
            glsr_log()->warning($response);
        }
        return $response;
    }
}
