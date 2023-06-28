<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!class_exists('myCRED_Hook')
            || !class_exists('myCRED_Core')
            || !function_exists('mycred_get_post')
            || !function_exists('mycred_get_user_meta')
            || !function_exists('mycred_update_user_meta')
            || !defined('MYCRED_DEFAULT_TYPE_KEY')) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterHooks', 'mycred_setup_hooks'],
            ['filterReferences', 'mycred_all_references'],
            ['filterWooreviewHook', 'mycred_setup_hooks', 100], // run after WooCommerce Product Reviews hook
        ]);
    }
}
