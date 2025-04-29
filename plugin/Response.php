<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

class Response
{
    public array $body;
    public int $code;
    public bool $error = false;
    public string $message;
    public ?\WP_HTTP_Requests_Response $response;
    public CaseInsensitiveDictionary $headers;

    /**
     * @param array|\WP_Error $request
     */
    public function __construct($request = [])
    {
        if (is_wp_error($request)) {
            $this->body = [];
            $this->code = 0;
            $this->error = true;
            $this->headers = new CaseInsensitiveDictionary([]);
            $this->message = $request->get_error_message();
            $this->response = null;
            glsr_log()->error($this->message);
            return;
        }
        $body = json_decode(wp_remote_retrieve_body($request), true);
        $headers = wp_remote_retrieve_headers($request);
        if (empty($headers)) {
            $headers = new CaseInsensitiveDictionary([]);
        }
        $this->body = Cast::toArray($body);
        $this->code = Cast::toInt(wp_remote_retrieve_response_code($request));
        $this->headers = $headers;
        $this->message = Arr::getAs('string', $this->body, 'message', wp_remote_retrieve_response_message($request));
        $this->response = $request['http_response'] ?? null;
    }

    public function body(): array
    {
        return array_map('maybe_unserialize', $this->body);
    }

    public function data(): array
    {
        $data = Arr::getAs('array', $this->body, 'data');
        return array_map('maybe_unserialize', $data);
    }

    public function failed(): bool
    {
        return !$this->successful();
    }

    public function shouldRetry(): bool
    {
        return 429 === $this->code // Too-Many-Requests
            || $this->code >= 500; // Internal errors
    }

    public function successful(): bool
    {
        return false === $this->error
            && $this->code >= 200
            && $this->code <= 299;
    }
}
