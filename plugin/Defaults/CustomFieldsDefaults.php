<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Modules\Honeypot;

class CustomFieldsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        '_action',
        '_ajax_request',
        '_frcaptcha',
        '_hcaptcha',
        '_nonce',
        '_pagination_atts',
        '_post_id',
        '_procaptcha',
        '_recaptcha',
        '_referer',
        '_reviews_atts',
        '_summary_atts',
        '_turnstile',
        'assigned_posts',
        'assigned_terms',
        'assigned_users',
        'author',
        'author_id',
        'avatar',
        'content',
        'custom',
        'date',
        'date_gmt',
        'email',
        'excluded',
        'form_id',
        'form_signature',
        'ID',
        'ip_address',
        'is_approved',
        'is_editing_review',
        'is_modified',
        'is_pinned',
        'is_verified',
        'name',
        'rating',
        'rating_id',
        'review_id',
        'score',
        'status',
        'terms',
        'terms_exist',
        'title',
        'type',
        'url',
    ];

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        $additionalFieldKeys = array_keys(glsr(AdditionalFieldsDefaults::class)->call('defaults'));
        $guarded = array_merge(
            $this->guarded,
            [glsr(Honeypot::class)->hash($values['form_id'] ?? '')],
            $additionalFieldKeys,
        );
        $this->guarded = array_values(array_filter(array_unique($guarded)));
        $this->sanitize = array_fill_keys(array_keys($this->guard($values)), 'text');
        return $values;
    }
}
