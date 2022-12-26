<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Response
{
    public $body;
    public $code;
    public $message;
    public $response;
    public $status;

    public function __construct($request = [])
    {
        $body = json_decode(wp_remote_retrieve_body($request), true);
        $this->body = Cast::toArray($body);
        $this->code = Cast::toInt(wp_remote_retrieve_response_code($request));
        $this->message = Arr::get($body, 'message', wp_remote_retrieve_response_message($request));
        $this->response = Arr::getAs('array', $request, 'http_response');
        $this->status = Arr::get($body, 'status');
        if (is_wp_error($request)) {
            $this->message = $request->get_error_message();
            $this->status = 'error';
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

    public function successful(): bool
    {
        return 'success' === $this->status;
    }
}
