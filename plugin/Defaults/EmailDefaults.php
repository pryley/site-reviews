<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class EmailDefaults extends Defaults
{
    /**
     * @var array
     */
    public $sanitize = [
        'attachments' => 'array',
        'template-tags' => 'array',
    ];

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
            'from' => '',
            'message' => '',
            'reply-to' => '',
            'subject' => '',
            'template' => '',
            'template-tags' => [],
            'to' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return string
     */
    protected function normalize(array $values = [])
    {
        if (empty($values['from'])) {
            $email = glsr(OptionManager::class)->getWP('admin_email');
            $from = wp_specialchars_decode(glsr(OptionManager::class)->getWP('blogname'), ENT_QUOTES);
            $values['from'] = sprintf('%s <%s>', $from, $email);
        }
        if (empty($values['reply-to'])) {
            $values['reply-to'] = $values['from'];
        }
        return parent::normalize($values);
    }
}
