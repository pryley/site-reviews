<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Controls;

use Elementor\Control_Select2;

class Select2Ajax extends Control_Select2
{
    public function get_type(): string
    {
        return 'select2_ajax';
    }

    protected function get_default_settings(): array {
        return [
            'include' => '',
            'lockedOptions' => [],
            'multiple' => false,
            'options' => [],
            'select2options' => [],
        ];
    }
}
