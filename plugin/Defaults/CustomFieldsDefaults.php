<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Honeypot;

class CustomFieldsDefaults extends Defaults
{
    /**
     * @var string[]
     */
    public $guarded = [
        '_action',
        '_ajax_request',
        '_frcaptcha',
        '_hcaptcha',
        '_nonce',
        '_post_id',
        '_recaptcha',
        '_referer',
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
        'response',
        'review_id',
        'score',
        'terms',
        'terms_exist',
        'title',
        'type',
        'url',
    ];

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $this->guarded[] = glsr(Honeypot::class)->hash(Arr::get($values, 'form_id'));
        $this->sanitize = array_fill_keys(array_keys($this->guard($values)), 'text');
        return $values;
    }
}
