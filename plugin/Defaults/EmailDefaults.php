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
        return [
            'after' => '',
            'attachments' => [],
            'bcc' => '',
            'before' => '',
            'cc' => '',
            'from' => $this->getFromName().' <'.$this->getFromEmail().'>',
            'message' => '',
            'reply-to' => '',
            'subject' => '',
            'template' => '',
            'template-tags' => [],
            'to' => '',
        ];
    }

    /**
     * @return string
     */
    protected function getFromEmail()
    {
        return glsr(OptionManager::class)->getWP('admin_email');
    }

    /**
     * @return string
     */
    protected function getFromName()
    {
        return wp_specialchars_decode(glsr(OptionManager::class)->getWP('blogname'), ENT_QUOTES);
    }
}
