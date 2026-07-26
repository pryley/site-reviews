<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Field display conditions arrive in two shapes: the structured
 * {criteria, conditions: [{name, operator, value}]} array the form
 * editor stores, or the legacy pipe-joined string
 * "criteria|name:operator:value|..." (see Field::conditions()).
 *
 * @return string|array
 */
class SanitizeConditions extends AbstractSanitizer
{
    public function run()
    {
        if (!is_array($this->value)) {
            return (new SanitizeText($this->value))->run();
        }
        $sanitizeText = fn ($value) => (new SanitizeText($value))->run();
        $conditions = array_filter((array) ($this->value['conditions'] ?? []), 'is_array');
        $conditions = array_map(fn ($condition) => [
            'name' => $sanitizeText($condition['name'] ?? ''),
            'operator' => $sanitizeText($condition['operator'] ?? ''),
            'value' => $sanitizeText($condition['value'] ?? ''),
        ], $conditions);
        return [
            'criteria' => $sanitizeText($this->value['criteria'] ?? 'always'),
            'conditions' => array_values($conditions),
        ];
    }
}
