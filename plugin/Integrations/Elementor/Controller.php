<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Fix Star Rating control when review form is used inside an Elementor Pro Popup.
     * @param string $script
     * @return string
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterElementorPublicInlineScript($script)
    {
        if (defined('ELEMENTOR_VERSION')) {
            $script .= 'function glsr_init_elementor(){GLSR.Event.trigger("site-reviews/init")}"undefined"!==typeof jQuery&&(';
            if (defined('ELEMENTOR_PRO_VERSION') && 0 > version_compare('2.7.0', ELEMENTOR_PRO_VERSION)) {
                $script .= 'jQuery(document).on("elementor/popup/show",glsr_init_elementor),';
            }
            $script .= 'jQuery(window).on("elementor/frontend/init",function(){elementorFrontend.hooks.addAction("frontend/element_ready/site_reviews.default",glsr_init_elementor);elementorFrontend.hooks.addAction("frontend/element_ready/site_reviews_form.default",glsr_init_elementor)}));';
        }
        return $script;
    }

    /**
     * Fix Star Rating CSS class prefix in the Elementor editor.
     * @return array
     * @filter site-reviews/defaults/star-rating/defaults
     */
    public function filterElementorStarRatingDefaults(array $defaults)
    {
        if ('elementor' === filter_input(INPUT_GET, 'action')) {
            $defaults['prefix'] = 'glsr-';
        }
        return $defaults;
    }

    /**
     * @param $manager \Elementor\Elements_Manager
     * @return void
     * @action elementor/elements/categories_registered
     */
    public function registerElementorCategory($manager)
    {
        $manager->add_category(glsr()->id, [
            'title' => glsr()->name,
            'icon' => 'eicon-star-o', // default icon
        ]);
    }

    /**
     * @param $manager \Elementor\Widgets_Manager
     * @return void
     * @action elementor/widgets/register
     */
    public function registerElementorWidgets($manager)
    {
        $manager->register(new ElementorFormWidget());
        $manager->register(new ElementorReviewsWidget());
        $manager->register(new ElementorSummaryWidget());
    }
}
