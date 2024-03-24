<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Response
{
    public array $body;
    public int $code;
    public bool $error = false;
    public string $message;
    public array $response;

    /**
     * @param array|\WP_Error $request
     */
    public function __construct($request = [])
    {
        $body = json_decode(wp_remote_retrieve_body($request), true);
        $this->body = Cast::toArray($body);
        $this->code = Cast::toInt(wp_remote_retrieve_response_code($request));
        $this->message = Arr::getAs('string', $body, 'message', wp_remote_retrieve_response_message($request));
        $this->response = Arr::getAs('array', $request, 'http_response');
        if (is_wp_error($request)) {
            $this->error = true;
            $this->message = $request->get_error_message();
            glsr_log()->error($this->message);
        }
    }

    public function data(): array
    {
        return Arr::getAs('array', $this->body, 'data');
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
