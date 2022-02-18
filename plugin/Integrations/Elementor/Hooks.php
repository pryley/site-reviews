<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

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
        add_filter('site-reviews/enqueue/public/inline-script/after', [$this->controller, 'filterElementorPublicInlineScript'], 1);
        add_filter('site-reviews/defaults/star-rating/defaults', [$this->controller, 'filterElementorStarRatingDefaults']);
        add_action('elementor/init', [$this->controller, 'registerElementorCategory']);
        add_action('elementor/widgets/widgets_registered', [$this->controller, 'registerElementorWidgets']);
    }
}
