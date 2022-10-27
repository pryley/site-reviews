<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    /**
     * @var Controller
     */
    public $controller;

    public function __construct()
    {
        $this->controller = glsr(Controller::class);
    }

    /**
     * @return void
     */
    public function run()
    {
        add_action('site-reviews/migration/end', [$this->controller, 'purgeAll']);
        add_action('site-reviews/review/created', [$this->controller, 'purgeForPost'], 10, 2);
    }
}
