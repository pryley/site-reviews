<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Toggle extends Field
{
    /**
     * @return string|void
     */
    public function build()
    {
        $this->builder->tag = 'input';
        $this->builder->args->type = 'checkbox';
        return $this->builder->buildOpeningTag();
    }

    /**
     * @param string $fieldLocation
     * @return array
     */
    public static function required($fieldLocation = null)
    {
        return [
            'value' => 1,
        ];
    }
}
