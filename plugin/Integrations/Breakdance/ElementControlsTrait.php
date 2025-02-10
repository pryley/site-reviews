<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Breakdance\Defaults\ControlDefaults;
use GeminiLabs\SiteReviews\Integrations\Breakdance\Defaults\SectionDefaults;

use function Breakdance\Elements\c;

trait ElementControlsTrait
{
    public static function bdSectionGroups(): array
    {
        $config = static::bdShortcode()->settings();
        $groups = [ // order is intentional
            'rating_group' => glsr(SectionDefaults::class)->merge([
                'children' => [
                    static::bdControl([
                        'slug' => 'rating_field_notice',
                        'options' => [
                            'alertBoxOptions' => [
                                'style' => 'info',
                                'content' => $config['rating_field']['description'] ?? '',
                            ],
                            'layout' => 'vertical',
                            'type' => 'alert_box',
                        ],
                    ]),
                ],
                'fields' => ['rating', 'rating_field'],
                'label' => esc_html_x('Rating Options', 'admin-text', 'site-reviews'),
                'options' => [
                    'sectionOptions' => [
                        'type' => 'popout',
                    ],
                    'type' => 'section',
                ],
            ]),
            'pagination_group' => glsr(SectionDefaults::class)->merge([
                'fields' => ['display', 'pagination'],
                'label' => esc_html_x('Pagination Options', 'admin-text', 'site-reviews'),
                'options' => [
                    'sectionOptions' => [
                        'type' => 'popout',
                    ],
                    'type' => 'section',
                ],
            ]),
            'schema_group' => glsr(SectionDefaults::class)->merge([
                'children' => [
                    static::bdControl([
                        'slug' => 'schema_notice',
                        'options' => [
                            'alertBoxOptions' => [
                                'style' => 'warning',
                                'content' => $config['schema']['description'] ?? '',
                            ],
                            'layout' => 'vertical',
                            'type' => 'alert_box',
                        ],
                    ]),
                ],
                'fields' => ['schema'],
                'label' => esc_html_x('Schema Options', 'admin-text', 'site-reviews'),
                'options' => [
                    'sectionOptions' => [
                        'type' => 'popout',
                    ],
                    'type' => 'section',
                ],
            ]),
            'hide_group' => glsr(SectionDefaults::class)->merge([
                'fields' => ['hide'],
                'label' => esc_html_x('Hide Options', 'admin-text', 'site-reviews'),
                'options' => [
                    'sectionOptions' => [
                        'type' => 'popout',
                    ],
                    'type' => 'section',
                ],
            ]),
        ];
        $groups = glsr()->filterArray('breakdance/groups', $groups, static::class);
        return $groups;
    }

    public static function bdSections(): array
    {
        $config = static::bdShortcode()->settings();
        $sections = [ // order is intentional
            'general' => glsr(SectionDefaults::class)->merge([
                'label' => esc_html_x('General', 'admin-text', 'site-reviews'),
                'options' => [
                    'layout' => 'vertical',
                    'type' => 'section',
                ],
            ]),
            'advanced' => glsr(SectionDefaults::class)->merge([
                'children' => [
                    static::bdControl([
                        'slug' => 'reviews_id_notice',
                        'options' => [
                            'alertBoxOptions' => [
                                'style' => 'default',
                                'content' => $config['reviews_id']['description'] ?? '',
                            ],
                            'layout' => 'vertical',
                            'type' => 'alert_box',
                        ],
                    ]),
                ],
                'label' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
                'options' => [
                    'layout' => 'vertical',
                    'type' => 'section',
                ],
            ]),
        ];
        $sections = glsr()->filterArray('breakdance/sections', $sections, static::class);
        return $sections;
    }

    abstract public static function bdShortcode(): ShortcodeContract;

    /**
     * @return array[]
     */
    public static function contentControls()
    {
        $config = static::bdProcessConfig();
        $controls = [];
        foreach ($config as $slug => $section) {
            $children = array_filter($section['children']);
            if (empty($children)) {
                continue;
            }
            $controls[] = static::bdControl([
                'children' => $section['children'],
                'label' => $section['label'],
                'options' => $section['options'],
                'slug' => $slug,
            ]);
        }
        return $controls;
    }

    /**
     * This must return false if the element has no default properties
     * otherwise SSR will not trigger when control values are changed.
     *
     * @return array|false
     */
    public static function defaultProperties()
    {
        $config = static::bdShortcode()->settings();
        $paths = array_keys(Arr::flatten(static::bdContentControlPaths()));
        $props = array_combine($paths,
            array_map(fn ($item) => substr($item, strrpos($item, '.') + 1), $paths)
        );
        $defaults = [];
        foreach ($props as $path => $slug) {
            $defaults[$path] = $config[$slug]['default'] ?? null;
        }
        $defaults = array_filter($defaults, fn ($value) => !is_null($value));
        if (empty($defaults)) {
            return false;
        }
        return Arr::unflatten($defaults);
    }

    public static function ssrArgs(array $data): array
    {
        $content = Arr::getAs('array', $data, 'content');
        $sections = array_keys(static::bdSections());
        $groups = array_keys(static::bdSectionGroups());
        $controls = array_merge(...array_map(fn ($key) => $content[$key] ?? [], $sections));
        $ungrouped = array_filter($controls, fn ($val) => !is_array($val) || wp_is_numeric_array($val));
        $grouped = array_map(fn ($groupKey) => $controls[$groupKey] ?? [], $groups);
        $args = array_merge($ungrouped, ...$grouped);
        $hide = Arr::getAs('array', $args, 'hide');
        $args['hide'] = array_keys(array_filter($hide));
        if (is_array($args['assigned_posts'] ?? null)) {
            $replacements = [ // the post_chooser control requires integer keys
                -10 => 'post_id',
                -20 => 'parent_id',
            ];
            $args['assigned_posts'] = array_map(
                fn ($value) => $replacements[$value] ?? $value,
                $args['assigned_posts']
            );
        }
        if (is_array($args['assigned_users'] ?? null)) {
            $replacements = [ // the post_chooser control requires integer keys
                -10 => 'user_id',
                -20 => 'author_id',
                -30 => 'profile_id',
            ];
            $args['assigned_users'] = array_map(
                fn ($value) => $replacements[$value] ?? $value,
                $args['assigned_users']
            );
        }
        $args = glsr()->filterArray('breakdance/ssr', $args, $data);
        return $args;
    }

    /**
     * @return string[]
     */
    public static function bdContentControlPaths(): array
    {
        $config = static::bdShortcode()->settings();
        $props = [];
        foreach (static::contentControls() as $section) {
            $slug = $section['slug'];
            foreach ($section['children'] as $child) {
                if (!empty($child['children'])) {
                    continue; // this is a section group
                }
                $field = $child['slug'];
                if (!array_key_exists($field, $config)) {
                    continue;
                }
                $props['content'][$slug][$field] = $config[$field]['default'] ?? null;
            }
        }
        $groupedFields = wp_list_pluck(static::bdSectionGroups(), 'fields');
        foreach ($groupedFields as $slug => $fields) {
            foreach ($fields as $field) {
                if (!array_key_exists($field, $config)) {
                    continue;
                }
                $props['content']['general'][$slug][$field] = $config[$field]['default'] ?? null;
            }
        }
        return $props;
    }

    protected static function bdControl(array $args): array
    {
        $args = glsr(ControlDefaults::class)->merge($args);
        return c(
            $args['slug'],
            $args['label'],
            $args['children'],
            $args['options'],
            $args['enableMediaQueries'],
            $args['enableHover'],
            $args['keywords']
        );
    }

    protected static function bdControlCheckbox(Arguments $args): array
    {
        $control = [
            'label' => $args->label,
            'options' => [
                'layout' => 'inline',
                'type' => 'toggle',
            ],
            'slug' => $args->slug,
        ];
        if ($items = static::bdTransformDropdownItems($args->array('options'))) {
            $control['children'] = array_map(fn ($item) => static::bdControl([
                'slug' => $item['value'],
                'label' => $item['text'],
                'options' => [
                    'layout' => 'inline',
                    'type' => 'toggle',
                ],
            ]), $items);
            $control['options']['type'] = 'section';
        }
        return static::bdControl($control);
    }

    protected static function bdControlNumber(Arguments $args): array
    {
        return static::bdControl([
            'slug' => $args->slug,
            'label' => $args->label,
            'options' => [
                'layout' => 'inline',
                'rangeOptions' => [
                    'min' => $args->cast('min', 'int', 0),
                    'max' => $args->cast('max', 'int', 1),
                    'step' => $args->cast('step', 'int', 1),
                ],
                'type' => 'number',
            ],
        ]);
    }

    /**
     * If multiple select is required in an AJAX-populated dropdown then we have
     * to use the post_chooser control because:
     * a) The dropdown control does not support multiple selection.
     * b) The multiselect control does not support AJAX searching.
     *
     * @see Controller::searchAssignedPosts
     * @see Controller::searchAssignedTerms
     * @see Controller::searchAssignedUsers
     * @see \GeminiLabs\SiteReviews\Controllers\MainController::parseAssignedPostTypesInQuery
     */
    protected static function bdControlSelect(Arguments $args): array
    {
        $control = [
            'label' => $args->label,
            'slug' => $args->slug,
            'options' => [
                'layout' => 'vertical',
                'placeholder' => $args->placeholder ?: esc_html_x('Select...', 'admin-text', 'site-reviews'),
                'type' => 'dropdown',
            ],
        ];
        if (!$args->exists('options')) {
            // Unfortunately we can't use this because Breakdance does not support
            // AJAX searching in dropdown controls.
            // if (true !== $args->multiple) {
            //     $control['options']['dropdownOptions']['populate'] = [
            //         'fetchDataAction' => glsr()->prefix.'breakdance_'.$args->slug,
            //     ];
            //     return static::bdControl($control);
            // }
            $control['options']['type'] = 'post_chooser';
            $control['options']['postChooserOptions'] = [
                'multiple' => $args->multiple,
                'postType' => glsr()->prefix.$args->slug,
                'showThumbnails' => 'assigned_users' === $args->slug,
            ];
            // Unfortunately we can't use this yet because Breakdance does not support
            // AJAX searching in multiselect controls.
            // $control['options']['type'] = 'multiselect';
            // $control['options']['searchable'] = true;
            // $control['options']['multiselectOptions']['populate'] = [
            //     'fetchDataAction' => glsr()->prefix.'breakdance_'.$args->slug,
            //     'fetchContextPath' => 'content.general.'.$args->slug,
            // ];
            return static::bdControl($control);
        }
        $items = static::bdTransformDropdownItems($args->array('options'));
        if (!empty($items)) {
            $control['options']['items'] = $items;
            return static::bdControl($control);
        }
        return [];
    }

    protected static function bdControlText(Arguments $args): array
    {
        return static::bdControl([
            'slug' => $args->slug,
            'label' => $args->label,
            'options' => [
                'layout' => 'vertical',
                'type' => 'text',
            ],
        ]);
    }

    /**
     * Find the group slug for the given slug in grouped fields.
     */
    protected static function bdFindGroupSlug(string $slug, array $groupedFields): string
    {
        return (string) array_search(true, array_map(
            fn ($fields) => in_array($slug, $fields),
            $groupedFields
        ));
    }

    /**
     * Process the configuration settings and group controls.
     */
    protected static function bdProcessConfig(): array
    {
        $config = static::bdShortcode()->settings();
        $groups = static::bdSectionGroups();
        $groupedFields = wp_list_pluck($groups, 'fields');
        $sections = static::bdSections();
        foreach ($config as $slug => $args) {
            $args = wp_parse_args($args, [
                'group' => 'general',
                'type' => 'text',
                'slug' => $slug,
            ]);
            $args['group'] = array_key_exists($args['group'], $sections) ? $args['group'] : 'general';
            $method = Helper::buildMethodName('bd', 'control', $args['type']);
            if (!method_exists(static::class, $method)) {
                continue;
            }
            $control = call_user_func([static::class, $method], glsr()->args($args));
            if (empty($control)) {
                continue;
            }
            $groupSlug = static::bdFindGroupSlug($slug, $groupedFields);
            if (!empty($groupSlug)) {
                $groups[$groupSlug]['children'][] = $control;
                continue;
            }
            $sections[$args['group']]['children'][] = $control;
        }
        return static::bdProcessControlGroups($groups, $sections);
    }

    /**
     * Process groups and add them to sections if they have valid children.
     */
    protected static function bdProcessControlGroups(array $groups, array $sections): array
    {
        foreach ($groups as $slug => $group) {
            $filteredChildren = array_filter($group['children'], function ($child) {
                return 'alert_box' !== ($child['options']['type'] ?? 'alert_box');
            });
            if (!empty($filteredChildren)) {
                $sections['general']['children'][] = static::bdControl([
                    'children' => $group['children'],
                    'label' => $group['label'],
                    'options' => $group['options'],
                    'slug' => $slug,
                ]);
            }
        }
        return $sections;
    }

    protected static function bdTransformDropdownItems(array $options): array
    {
        $callback = fn ($k, $v) => [
            'text' => $v,
            'value' => $k,
        ];
        return array_map($callback, array_keys($options), $options);
    }
}
