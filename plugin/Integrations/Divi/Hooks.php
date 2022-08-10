<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

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
        add_filter('et_dynamic_assets_modules_atf', [$this->controller, 'filterDynamicAssets'], 10, 2);
        add_filter('site-reviews/paginate_links', [$this->controller, 'filterPaginationLinks'], 10, 2);
        add_action('divi_extensions_init', [$this->controller, 'registerDiviModules']);
    }
}
