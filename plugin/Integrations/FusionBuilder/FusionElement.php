<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;

abstract class FusionElement extends \Fusion_Element
{
    abstract public static function elementParameters(): array;

    public static function optionAssignedTerms(string $heading, string $description = ''): array
    {
        $isFusionEditor = function_exists('is_fusion_editor') && is_fusion_editor();
        if ($isFusionEditor && function_exists('fusion_builder_shortcodes_categories')) {
            $terms = fusion_builder_shortcodes_categories(glsr()->taxonomy, false, '', 26);
        } else {
            $terms = [];
        }
        $option = [
            'default' => '',
            'heading' => $heading,
            'param_name' => 'assigned_terms',
            'placeholder_text' => esc_attr_x('Select or Leave Blank', 'admin-text', 'site-reviews'),
            'type' => 'multiple_select',
            'value' => $terms,
        ];
        if (!empty($description)) {
            $option['description'] = $description;
        }
        if (count($terms) > 25) {
            $option['type'] = 'ajax_select';
            $option['ajax'] = 'fusion_search_query';
            $option['value'] = [];
            $option['ajax_params'] = [
                'taxonomy' => glsr()->taxonomy,
                'use_slugs' => true,
            ];
        }
        return $option;
    }

    public static function optionReviewTypes(): array
    {
        $types = glsr()->retrieveAs('array', 'review_types', []);
        if (2 > count($types)) {
            return [];
        }
        return [
            'default' => 'local',
            'heading' => esc_attr_x('Limit the Review Type', 'admin-text', 'site-reviews'),
            'param_name' => 'type',
            'placeholder_text' => esc_attr_x('Select or Leave Blank', 'admin-text', 'site-reviews'),
            'type' => 'multiple_select',
            'value' => $types,
        ];
    }

    abstract public static function registerElement(): void;
}
