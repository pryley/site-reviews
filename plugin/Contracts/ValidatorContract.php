<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Request;

interface ValidatorContract
{
    public function alreadyFailed(): bool;

    public function fail(string $message, ?string $loggedMessage = null): void;

    public function isValid(): bool;

    public function request(): Request;

    public function performValidation(): void;

    /**
     * @return static
     */
    public function validate();
}
