<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterElementorPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after', 1],
            ['filterElementorStarRatingDefaults', 'site-reviews/defaults/star-rating/defaults'],
            ['filterGeneratedSchema', 'site-reviews/schema/generate'],
            ['registerElementorCategory', 'elementor/elements/categories_registered'],
            ['registerElementorWidgets', 'elementor/widgets/register'],
            ['registerInlineStyles', 'elementor/editor/after_enqueue_styles'],
        ]);
    }
}
