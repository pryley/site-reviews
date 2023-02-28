<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class Controller extends BaseController
{
    /**
     * @param array $data
     * @return array
     * @filter rank_math/json_ld
     */
    public function filterSchema(array $data)
    {
        $schemas = glsr()->filterArray('schema/all', glsr()->retrieve('schemas', []));
        if (empty($schemas)) {
            return $data;
        }
        $types = Arr::consolidate(glsr_get_option('schema.integration.types'));
        foreach ($data as $key => $values) {
            $type = Arr::get($values, '@type');
            if (!in_array($type, $types)) {
                continue;
            }
            if ($rating = Arr::get($schemas, '0.aggregateRating')) {
                $data[$key]['aggregateRating'] = $rating;
            }
            if ($review = Arr::get($schemas, '0.review')) {
                $data[$key]['review'] = $review;
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @filter rank_math/schema/preview/validate
     */
    public function filterSchemaPreview(array $data)
    {
        global $post;
        if (class_exists('Elementor\Plugin') && \Elementor\Plugin::$instance->documents->get($post->ID)->is_built_with_elementor()) { // @phpstan-ignore-line
            $widgets = Cast::toString(get_post_meta($post->ID, '_elementor_data', true));
            $widgets = json_decode($widgets, true);
            $widgets = Arr::consolidate($widgets);
            if ($args = $this->parseElementorWidgets($widgets, 'site_reviews')) {
                $this->buildReviewSchema($args);
                return $this->filterSchema($data);
            }
            if ($args = $this->parseElementorWidgets($widgets, 'site_reviews_summary')) {
                $this->buildSummarySchema($args);
                return $this->filterSchema($data);
            }
        } else {
            // gutenberg?
            $blocks = parse_blocks($post->post_content);
            if ($args = $this->parseBlocks($blocks, 'site-reviews/reviews')) {
                $this->buildReviewSchema($args);
                return $this->filterSchema($data);
            }
            if ($args = $this->parseShortcodes($post->post_content, 'site_reviews')) {
                $this->buildReviewSchema($args);
                return $this->filterSchema($data);
            }
            if ($args = $this->parseBlocks($blocks, 'site-reviews/summary')) {
                $this->buildSummarySchema($args);
                return $this->filterSchema($data);
            }
            if ($args = $this->parseShortcodes($post->post_content, 'site_reviews_summary')) {
                $this->buildSummarySchema($args);
                return $this->filterSchema($data);
            }
        }
        return $data;
    }

    protected function buildReviewSchema(array $args)
    {
        $shortcode = glsr(SiteReviewsShortcode::class)->normalize($args);
        $reviews = glsr(ReviewManager::class)->reviews($shortcode->args);
        $schema = glsr(Schema::class)->build($shortcode->args, $reviews);
        glsr(Schema::class)->store($schema);
    }

    protected function buildSummarySchema(array $args)
    {
        $shortcode = glsr(SiteReviewsSummaryShortcode::class)->normalize($args);
        $ratings = glsr(RatingManager::class)->ratings($shortcode->args);
        $schema = glsr(Schema::class)->build($shortcode->args, $ratings);
        glsr(Schema::class)->store($schema);
    }

    /**
     * @param string $name
     * @param array $result
     * @return false|array
     */
    protected function parseBlocks(array $blocks, $name = 'site-reviews/reviews', $result = [])
    {
        foreach ($blocks as $block) {
            $children = $block['innerBlocks'];
            if ($name === $block['blockName'] && !empty($block['attrs']['schema'])) {
                $result = $block['attrs'];
            } elseif (!empty($children)) {
                $result = $this->parseBlocks($children, $name, $result);
            }
            if (!empty($result) && is_array($result)) {
                return $result;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @param array $result
     * @return false|array
     */
    protected function parseElementorWidgets(array $widgets, $name = 'site_reviews', $result = [])
    {
        foreach ($widgets as $widget) {
            $children = $widget['elements'];
            if ($name === Arr::get($widget, 'widgetType') && Arr::getAs('bool', $widget, 'settings.schema')) {
                $result = $widget['settings'];
            } elseif (!empty($children)) {
                $result = $this->parseElementorWidgets($children, $name, $result);
            }
            if (!empty($result) && is_array($result)) {
                return $result;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return false|array
     */
    protected function parseShortcodes(string $content, $name = 'site_reviews')
    {
        if (false === strpos($content, '[')) {
            return false;
        }
        preg_match_all('/'.get_shortcode_regex().'/', $content, $matches, PREG_SET_ORDER);
        if (empty($matches)) {
            return false;
        }
        foreach ($matches as $shortcode) {
            if ($name !== $shortcode[2]) {
                continue;
            }
            $attributes = shortcode_parse_atts($shortcode[3]);
            if (!is_array($attributes)) {
                continue;
            }
            return $attributes;
        }
        return false;
    }
}
