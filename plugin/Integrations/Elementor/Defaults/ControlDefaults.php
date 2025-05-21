<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Defaults;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ControlDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'description' => '',
            'label' => '',
            'type' => 'text',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $types = [
            'checkbox' => Controls_Manager::SWITCHER,
            'number' => Controls_Manager::NUMBER,
            'radio' => Controls_Manager::CHOOSE,
            'select' => Controls_Manager::SELECT2,
            'text' => Controls_Manager::TEXT,
            'textarea' => Controls_Manager::TEXTAREA,
        ];
        if (array_key_exists($values['type'], $types)) {
            $values['type'] = $types[$values['type']];
        }
        if (Controls_Manager::SWITCHER === $values['type'] && !empty($values['options'])) {
            $values['type'] = 'multi_switcher';
        }
        if (!in_array($values['type'], [
            Controls_Manager::CHOOSE,
            Controls_Manager::COLOR,
            Controls_Manager::NUMBER,
            Controls_Manager::SWITCHER,
        ]) && !isset($values['label_block'])) {
            $values['label_block'] = true;
        }
        if (Controls_Manager::SELECT2 === $values['type'] && !empty($values['placeholder'])) {
            $values['select2options'] ??= [];
            $values['select2options']['placeholder'] = $values['placeholder'];
        }
        if (Controls_Manager::SELECT2 === $values['type'] && !isset($values['options'])) {
            $values['type'] = 'select2_ajax';
        }
        return $values;
    }
}
