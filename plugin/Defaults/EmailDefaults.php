<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class EmailDefaults extends DefaultsAbstract
{
    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'to' => 'recipients',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'after' => 'text-post',
        'attachments' => 'array-consolidate',
        'before' => 'text-post',
        'message' => 'text-post',
        'recipients' => 'array-string',
        'subject' => 'text',
        'template-tags' => 'array-consolidate',
    ];

    protected function defaults(): array
    {
        return [
            'after' => '',
            'attachments' => [],
            'bcc' => '',
            'before' => '',
            'cc' => '',
            'from' => '',
            'message' => '',
            'recipients' => [],
            'reply-to' => '',
            'subject' => '',
            'template' => 'default',
            'template-tags' => [],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $values['recipients'] = Arr::removeEmptyValues($values['recipients']);
        return $values;
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
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
