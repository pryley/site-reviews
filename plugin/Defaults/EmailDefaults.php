<?php

namespace GeminiLabs\SiteReviews\Defaults;

class EmailDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'attachments' => 'array-consolidate',
        'template-tags' => 'array-consolidate',
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
            'template' => 'default',
            'template-tags' => [],
            'to' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if (empty($values['from'])) {
            $email = sanitize_email(glsr_get_option('general.notification_from', null, 'string'));
            if (empty($email)) {
                $email = get_bloginfo('admin_email');
            }
            $from = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
            $values['from'] = sprintf('%s <%s>', $from, $email);
        }
        if (empty($values['reply-to'])) {
            $values['reply-to'] = $values['from'];
        }
        return parent::normalize($values);
    }
}
