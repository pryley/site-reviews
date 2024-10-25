<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;
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
        '_post_id',
        '_procaptcha',
        '_recaptcha',
        '_referer',
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
        'language',
        'name',
        'rating',
        'rating_id',
        'response',
        'response_by',
        'review_id',
        'score',
        'status',
        'terms',
        'terms_exist',
        'title',
        'type',
        'url',
        'verified',
        'verified_on',
        'verified_requested',
    ];

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        $this->guarded[] = glsr(Honeypot::class)->hash(Arr::get($values, 'form_id'));
        $this->sanitize = array_fill_keys(array_keys($this->guard($values)), 'text');
        return $values;
    }
}
