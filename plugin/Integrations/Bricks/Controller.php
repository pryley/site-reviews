<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Bricks\Commands\SearchAssignedPosts;
use GeminiLabs\SiteReviews\Integrations\Bricks\Commands\SearchAssignedTerms;
use GeminiLabs\SiteReviews\Integrations\Bricks\Commands\SearchAssignedUsers;
use GeminiLabs\SiteReviews\Integrations\Bricks\Commands\SearchAuthor;
use GeminiLabs\SiteReviews\Integrations\Bricks\Commands\SearchPostId;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Controller extends AbstractController
{
    /**
     * @filter bricks/builder/i18n
     */
    public function filterBuilderI18n(array $i18n): array
    {
        $i18n = Arr::consolidate($i18n);
        $i18n[glsr()->id] = glsr()->name;
        return $i18n;
    }

    /**
     * @filter site-reviews/bricks/element/controls:50
     */
    public function filterControls(array $controls): array
    {
        $sectionLabels = [
            'display' => esc_html_x('Display', 'admin-text', 'site-reviews'),
            'hide' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
            'schema' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
            'text' => esc_html_x('Text', 'admin-text', 'site-reviews'),
        ];
        $result = [];
        $separatorsAdded = [];
        foreach ($controls as $key => $control) {
            $group = $control['group'] ?? 'general';
            if (!isset($sectionLabels[$group])) {
                $result[$key] = $control;
                continue;
            }
            if (!isset($separatorsAdded[$group])) {
                $result["separator_{$group}"] = [
                    'group' => 'general',
                    'label' => $sectionLabels[$group],
                    'tab' => 'content',
                    'type' => 'separator',
                ];
                $separatorsAdded[$group] = true;
            }
            $control['group'] = 'general';
            $result[$key] = $control;
        }
        return $result;
    }

    /**
     * @filter site-reviews/modal_wrapped_by
     */
    public function filterModalWrappedBy(array $builders): array
    {
        $builders[] = 'bricks';
        return $builders;
    }

    /**
     * Add style classes.
     *
     * @filter bricks/element/settings
     */
    public function filterSettingsClass(array $settings, \Bricks\Element $element): array
    {
        if (!$element instanceof BricksElement) {
            return $settings;
        }
        $classes = [];
        $styleAlign = Cast::toString($element->styledSetting('style_align'));
        $styleRatingColor = $element->styledSetting('style_rating_color');
        $styleTextAlign = Cast::toString($element->styledSetting('style_text_align'));
        if ($styleAlign) {
            $align = str_replace('flex-', '', $styleAlign);
            $align = ['start' => 'left', 'end' => 'right'][$align] ?? $align;
            $classes[] = "items-justified-{$align}";
        }
        if ($styleRatingColor) {
            $classes[] = 'has-custom-color';
        }
        if ($styleTextAlign) {
            $classes[] = "has-text-align-{$styleTextAlign}";
        }
        if (!empty($classes)) {
            $classes[] = $settings['class'] ?? '';
            $settings['class'] = glsr(Sanitizer::class)->sanitizeAttrClass(
                implode(' ', $classes)
            );
        }
        return $settings;
    }

    /**
     * Consolidate multi-checkbox values because Bricks does not allow them.
     *
     * @filter bricks/element/settings
     */
    public function filterSettingsMultiCheckbox(array $settings, \Bricks\Element $element): array
    {
        if (!$element instanceof BricksElement) {
            return $settings;
        }
        foreach ($element->elementConfig() as $key => $control) {
            $type = $control['type'] ?? '';
            $options = Arr::getAs('array', $control, 'options');
            if ('checkbox' !== $type || empty($options)) {
                continue;
            }
            $values = array_filter(
                array_keys($settings),
                fn ($k) => !empty($settings[$k]) && str_starts_with($k, "{$key}_")
            );
            $settings[$key] = array_map(fn ($k) => Str::removePrefix($k, "{$key}_"), $values);
        }
        return $settings;
    }

    /**
     * Remove the "id::" prefix used to maintain javascript sorting.
     *
     * @filter bricks/element/settings
     */
    public function filterSettingsPrefixedId(array $settings, \Bricks\Element $element): array
    {
        if (!$element instanceof BricksElement) {
            return $settings;
        }
        foreach ($settings as $key => $value) {
            if (is_string($value) && str_starts_with($value, 'id::')) {
                $settings[$key] = Str::removePrefix($value, 'id::');
                continue;
            }
            if (is_array($value) && wp_is_numeric_array($value)) {
                $settings[$key] = array_map(
                    fn ($val) => is_string($val) ? Str::removePrefix($val, 'id::') : '',
                    $value
                );
            }
        }
        return $settings;
    }

    /**
     * Use this hook in addons to remove the group if unsupported.
     *
     * @filter bricks/theme_styles/control_groups
     */
    public function filterThemeStyleControlGroups(array $groups): array
    {
        if (!class_exists(\Bricks\Elements::class)) {
            return $groups;
        }
        $groups[glsr()->id] = [
            'isParent' => true,
            'title' => glsr()->name,
        ];
        foreach (\Bricks\Elements::$elements as $tag => $element) {
            if (!str_starts_with($tag, 'site_review')) {
                continue;
            }
            $groups[$tag] = [
                'title' => $element['label'] ?? $tag,
                'parent' => glsr()->id,
            ];
        }
        return $groups;
    }

    /**
     * Use this hook in addons to remove the contolrs group if unsupported.
     *
     * @filter bricks/theme_styles/controls
     */
    public function filterThemeStyleControls(array $controls): array
    {
        if (!class_exists(\Bricks\Elements::class)) {
            return $controls;
        }
        foreach (\Bricks\Elements::$elements as $tag => $element) {
            if (!str_starts_with($tag, 'site_review')) {
                continue;
            }
            $instance = new $element['class']();
            $instance->set_controls();
            $instance->controls = apply_filters("bricks/elements/{$tag}/controls", $instance->controls);
            $instance->controls = glsr()->filterArray('bricks/element/controls', $instance->controls, $instance);
            $themeStyleControls = array_filter($instance->controls,
                fn ($control) => !empty($control['themeStyle'])
            );
            array_walk($themeStyleControls, function (&$control) use ($tag) {
                $control['group'] = $tag;
                unset($control['required']);
            });
            if ($themeStyleControls) {
                $controls[$tag] = $themeStyleControls;
            }
        }
        return $controls;
    }

    /**
     * @action admin_enqueue_scripts:20
     * @action wp_enqueue_scripts:20
     */
    public function printInlineStyles(): void
    {
        $isBricksAdmin = 'bricks_page_bricks-elements' === glsr_current_screen()->base;
        $isBricksBuilder = function_exists('bricks_is_builder') && bricks_is_builder();
        if (!$isBricksAdmin && !$isBricksBuilder) {
            return;
        }
        $icons = [
            'site_reviews_form' => Svg::encoded('assets/images/icons/bricks/icon-form.svg'),
            'site_review' => Svg::encoded('assets/images/icons/bricks/icon-review.svg'),
            'site_reviews' => Svg::encoded('assets/images/icons/bricks/icon-reviews.svg'),
            'site_reviews_summary' => Svg::encoded('assets/images/icons/bricks/icon-summary.svg'),
        ];
        $icons = glsr()->filterArray('bricks/icons', $icons);
        $maskRules = '';
        foreach ($icons as $shortcode => $url) {
            $maskRules .= "i.ti-{$shortcode}::before { mask-image: url(\"{$url}\"); }\n";
        }
        $css = <<<CSS
            i[class*="ti-site_review"]::before {
                background-color: currentColor;
                content: '';
                display: inline-block;
                height: 1em;
                width: 1em;
                mask-position: center;
                mask-repeat: no-repeat;
                mask-size: 100%;
            }
            {$maskRules}
            .glsr :is(a,button,input,textarea,select,.dz-clickable) {
                pointer-events: none !important;
            }
        CSS;
        $css = preg_replace('/\s+/', ' ', $css);
        wp_add_inline_style('bricks-admin', $css);
        wp_add_inline_style('bricks-builder', $css);
    }

    /**
     * @action init:11
     */
    public function registerElements(): void
    {
        if (class_exists('Bricks\Element')) {
            Elements\BricksSiteReview::registerElement();
            Elements\BricksSiteReviews::registerElement();
            Elements\BricksSiteReviewsForm::registerElement();
            Elements\BricksSiteReviewsSummary::registerElement();
        }
    }

    /**
     * @action wp_ajax_bricks_glsr_assigned_posts
     */
    public function searchAssignedPosts(): void
    {
        $this->execute(new SearchAssignedPosts())->sendJsonResponse();
    }

    /**
     * @action wp_ajax_bricks_glsr_assigned_terms
     */
    public function searchAssignedTerms(): void
    {
        $this->execute(new SearchAssignedTerms())->sendJsonResponse();
    }

    /**
     * @action wp_ajax_bricks_glsr_assigned_users
     */
    public function searchAssignedUsers(): void
    {
        $this->execute(new SearchAssignedUsers())->sendJsonResponse();
    }

    /**
     * @action wp_ajax_bricks_glsr_author
     */
    public function searchAuthor(): void
    {
        $this->execute(new SearchAuthor())->sendJsonResponse();
    }

    /**
     * @action wp_ajax_bricks_glsr_post_id
     */
    public function searchPostId(): void
    {
        $this->execute(new SearchPostId())->sendJsonResponse();
    }
}
