<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

abstract class AbstractCommand implements CommandContract
{
    protected bool $result = true;

    public function fail(): void
    {
        $this->result = false;
    }

    public function pass(): void
    {
        $this->result = true;
    }

    public function referer(): string
    {
        return Url::home();
    }

    public function request(): ?Request
    {
        if (isset($this->request) && is_a($this->request, Request::class)) {
            return $this->request;
        }
        return null;
    }

    public function response(): array
    {
        return [];
    }

    public function sendJsonResponse(): void
    {
        $data = $this->response();
        if ($this->successful()) {
            wp_send_json_success($data);
        }
        if (empty($data['notices'])) {
            glsr(Notice::class)->addError(
                sprintf(_x('Something went wrong, check the <a href="%s">Site Reviews &rarr; Tools &rarr; Console</a> page for errors.', 'admin-text', 'site-reviews'),
                    glsr_admin_url('tools', 'console')
                )
            );
            $data['notices'] = glsr(Notice::class)->get();
        }
        wp_send_json_error($data);
    }

    public function successful(): bool
    {
        return $this->result;
    }
}
