<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Contracts\FieldElementContract;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;
use GeminiLabs\SiteReviews\Modules\Html\Field;

class UnknownElement implements FieldElementContract
{
    protected Field $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    public function build(array $overrideArgs = []): string
    {
        if (empty($this->tag())) {
            return '';
        }
        $args = wp_parse_args($overrideArgs, $this->field->toArray());
        return $this->field->builder()->build($this->tag(), $args);
    }

    public function defaults(): array
    {
        return [];
    }

    public function merge(): void
    {
        if (empty($this->tag())) {
            $this->field->is_valid = false;
            return;
        }
        $this->field->exchangeTag($this->tag());
    }

    public function required(): array
    {
        return [];
    }

    public function tag(): string
    {
        if (in_array($this->field->type, Attributes::INPUT_TYPES)) {
            return 'input';
        }
        return '';
    }
}
