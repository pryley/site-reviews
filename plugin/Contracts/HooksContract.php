<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Contracts\PluginContract;

interface HooksContract
{
    public function run(): void;
}
