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

    public function hasRequest(): bool
    {
        return isset($this->request)
            && is_a($this->request, Request::class);
    }

    public function pass(): void
    {
        $this->result = true;
    }

    public function referer(): string
    {
        return Url::home();
    }

    public function request(): Request
    {
        if (!$this->hasRequest()) {
            return new Request();
        }
        return $this->request; // @phpstan-ignore-line
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
        $referer = trailingslashit((string) wp_get_referer());
        $admin_url = trailingslashit(admin_url());
        if (!str_starts_with(esc_url_raw($referer), esc_url_raw($admin_url))) {
            wp_send_json_error($data);
        }
        if (empty($data['notices'])) {
            glsr(Notice::class)->addError(
                sprintf(_x('Something went wrong, check the %s page for errors.', 'link to Console page (admin-text)', 'site-reviews'),
                    glsr_admin_link(['tools', 'console'])
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
