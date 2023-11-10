<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Colorpicker extends Field
{
    public function args(): Arguments
    {
        // this is needed because merging field defaults filters unique array values
        $value = glsr_get_option($this->builder->args->path, $this->builder->args->default);
        $this->builder->args->set('value', $value);
        return $this->builder->args;
    }

    /**
     * This method is used when building a custom Field type.
     */
    public function build(): string
    {
        if (!is_array($this->args()->default)) {
            return $this->builder->build($this->tag(), $this->args()->toArray());
        }
        $colours = [];
        foreach ($this->args()->default as $index => $default) {
            $colours[] = $this->buildColorField($index);
        }
        return $this->builder->div([
            'class' => 'glsr-color-pickers',
            'text' => implode('', $colours),
        ]);
    }

    public static function required(string $fieldLocation = ''): array
    {
        return [
            'class' => 'glsr-color-picker color-picker-hex',
        ];
    }

    public function tag(): string
    {
        return 'input';
    }

    protected function buildColorField(int $index): string
    {
        $args = Arr::consolidate(Arr::get($this->args()->repeat, (string) $index));
        $args = wp_parse_args($args, $this->args()->toArray());
        $args['default'] = Arr::get($this->args()->default, $index);
        $args['id'] = Str::suffix($this->args()->id, (string) $index);
        $args['name'] = Str::suffix($this->args()->name, '[]');
        $args['value'] = Arr::get($this->args()->value, $index);
        return $this->builder->input($args);
    }
}
