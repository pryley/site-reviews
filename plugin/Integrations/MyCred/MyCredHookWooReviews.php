<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

class MyCredHookWooReviews extends \myCRED_Hook_WooCommerce_Reviews
{
    /**
     * @return void
     */
    public function preferences()
    {
        glsr()->render('integrations/mycred/wooprefs');
        ob_start();
        parent::preferences();
        $preferences = ob_get_clean();
        $preferences = str_replace(['<input', '<select'], ['<input disabled', '<select disabled'], $preferences);
        echo $preferences;
    }

    /**
     * @return void
     */
    public function run()
    {
    }
}
