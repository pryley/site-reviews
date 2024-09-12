<?php

namespace GeminiLabs\SiteReviews\Contracts;

interface CommandContract
{
    public function fail(): void;

    public function handle(): void;

    public function pass(): void;

    public function referer(): string;

    public function response(): array;

    public function sendJsonResponse(): void;

    public function successful(): bool;
}
