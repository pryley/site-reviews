<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Honeypot;

class CustomFieldsDefaults extends Defaults
{
    /**
     * @var array
     */
    public $guarded = [
        '_action',
        '_ajax_request',
        '_counter',
        '_nonce',
        '_post_id',
        '_recaptcha',
        '_recaptcha-token',
        '_referer',
        'assigned_posts',
        'assigned_terms',
        'assigned_users',
        'content',
        'date', // special
        'email',
        'excluded',
        'form_id',
        'ip_address',
        'name',
        'rating',
        'terms',
        'title',
        'url', // special
    ];

    /**
     * @return array
     */
    protected function sanitize(array $values = [])
    {
        $this->guarded[] = glsr(Honeypot::class)->hash(Arr::get($values, 'form_id'));
        $sanitize = array_fill_keys(array_keys($this->guard($values)), 'text');
        $this->sanitize = glsr()->filterArray('defaults/custom/sanitize', $sanitize);
        return parent::sanitize($values);
    }
}
