<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Honeypot extends Field
{
    /**
     * @inheritDoc
     */
    public function getArgs()
    {
        return wp_parse_args($this->builder->args, [
            'name' => $this->builder->args['text'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return 'input';
    }

    /**
     * @inheritDoc
     */
    public static function required()
    {
        return [
            'autocomplete' => 'off',
            'is_raw' => true,
            'style' => 'display:none!important',
            'tabindex' => '-1',
            'type' => 'text',
        ];
    }
}
