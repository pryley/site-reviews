<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterHooks', 'mycred_setup_hooks'],
            ['filterReferences', 'mycred_all_references'],
            ['filterWooreviewHook', 'mycred_setup_hooks', 100], // run after WooCommerce Product Reviews hook
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('myCRED_Hook')
            && class_exists('myCRED_Core')
            && function_exists('mycred_get_post')
            && function_exists('mycred_get_user_meta')
            && function_exists('mycred_update_user_meta')
            && defined('MYCRED_DEFAULT_TYPE_KEY');
    }
}
