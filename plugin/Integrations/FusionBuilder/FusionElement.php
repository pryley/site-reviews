<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Integrations\IntegrationShortcode;

abstract class FusionElement extends \Fusion_Element
{
    use IntegrationShortcode;

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
        if ($options = glsr(static::shortcodeClass())->options('type')) {
            return [
                'default' => 'local',
                'heading' => esc_attr_x('Limit the Review Type', 'admin-text', 'site-reviews'),
                'param_name' => 'type',
                'placeholder_text' => esc_attr_x('Select or Leave Blank', 'admin-text', 'site-reviews'),
                'type' => 'multiple_select',
                'value' => $options,
            ];
        }
        return [];
    }

    public static function registerElement(): void
    {
        if (!function_exists('fusion_builder_map')) {
            return;
        }
        if (!function_exists('fusion_builder_frontend_data')) {
            return;
        }
        $instance = glsr(static::shortcodeClass());
        $parameters = static::elementParameters();
        $parameters = glsr()->filterArray("fusion-builder/controls/{$instance->tag}", $parameters);
        fusion_builder_map(fusion_builder_frontend_data(static::class, [
            'name' => $instance->name,
            'shortcode' => $instance->tag,
            'icon' => static::shortcodeIcon(),
            'params' => $parameters,
        ]));
    }

    abstract protected static function shortcodeIcon(): string;
}
