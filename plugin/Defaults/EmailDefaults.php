<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class EmailDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        $fromName = wp_specialchars_decode(glsr(OptionManager::class)->getWP('blogname'), ENT_QUOTES);
        $fromEmail = glsr(OptionManager::class)->getWP('admin_email');
        return [
            'after' => '',
            'attachments' => [],
            'bcc' => '',
            'before' => '',
            'cc' => '',
            'from' => $fromName.' <'.$fromEmail.'>',
            'message' => '',
            'reply-to' => '',
            'subject' => '',
            'template' => '',
            'template-tags' => [],
            'to' => '',
        ];
    }
}
