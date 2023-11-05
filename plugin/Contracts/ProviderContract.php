<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Application;

interface ProviderContract
{
    public function register(Application $app): void;
}
