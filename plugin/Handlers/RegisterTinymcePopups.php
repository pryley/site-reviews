<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Commands\RegisterTinymcePopups as Command;
use GeminiLabs\SiteReviews\Helper;

class RegisterTinymcePopups
{
    /**
     * @return void
     */
    public function handle(Command $command)
    {
        foreach ($command->popups as $slug => $label) {
            $buttonClass = Helper::buildClassName($slug.'-popup', 'Shortcodes');
            if (!class_exists($buttonClass)) {
                glsr_log()->error(sprintf('Class missing (%s)', $buttonClass));
                continue;
            }
            $shortcode = glsr($buttonClass)->register($slug, [
                'label' => $label,
                'title' => $label,
            ]);
            glsr()->mceShortcodes[$slug] = $shortcode->properties;
        }
    }
}
