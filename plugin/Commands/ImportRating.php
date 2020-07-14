<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;

class ImportRating implements Contract
{
    protected $rating;

    public function __construct(array $rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return void
     */
    public function handle()
    {
        // @todo create rating for review and unset export meta key
    }

    /**
     * @return bool
     */
    protected function import()
    {
    }
}
