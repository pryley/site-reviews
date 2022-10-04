<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

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
        if ('rankmath' !== glsr_get_option('schema.integration.plugin')) {
            return;
        }
        add_filter('rank_math/json_ld', [$this->controller, 'filterSchema'], 99);
        add_filter('rank_math/schema/preview/validate', [$this->controller, 'filterSchemaPreview'], 20);
    }
}
