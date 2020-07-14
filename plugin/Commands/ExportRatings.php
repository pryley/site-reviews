<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ExportRatings implements Contract
{
    protected $args;
    protected $exportKey;

    public function __construct($exportKey, $args)
    {
        $this->args = Arr::consolidate($args);
        $this->exportKey = $exportKey;
    }

    /**
     * @return void
     */
    public function handle()
    {
        // @todo save all rating info as export meta on each review
    }

    /**
     * @return bool
     */
    protected function export()
    {
    }
}
