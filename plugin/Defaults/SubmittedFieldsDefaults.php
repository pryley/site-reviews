<?php

namespace GeminiLabs\SiteReviews\Defaults;

class SubmittedFieldsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        '_action',
        '_ajax_request',
        '_captcha',
        '_nonce',
        '_pagination_atts',
        '_post_id',
        '_referer',
        '_reviews_atts',
        '_summary_atts',
        'author_id',
        'custom',
        'excluded',
        'form_id',
        'form_signature',
        'ID',
        'ip_address',
        'is_editing_review',
        'is_modified',
        'is_pinned',
        'is_verified',
        'rating_id',
        'response',
        'response_by',
        'review_id',
        'terms_exist',
    ];
}
