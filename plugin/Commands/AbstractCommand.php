<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract;
use GeminiLabs\SiteReviews\Helpers\Url;

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

    public function response(): array
    {
        return [];
    }

    public function successful(): bool
    {
        return $this->result;
    }
}
