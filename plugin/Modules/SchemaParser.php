<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SchemaParser
{
    public function buildReviewSchema(array $args): array
    {
        $shortcode = glsr(SiteReviewsShortcode::class)->normalize($args);
        $reviews = glsr(ReviewManager::class)->reviews($shortcode->args);
        return glsr(Schema::class)->build($shortcode->args, $reviews);
    }

    public function buildSummarySchema(array $args): array
    {
        $shortcode = glsr(SiteReviewsSummaryShortcode::class)->normalize($args);
        $ratings = glsr(RatingManager::class)->ratings($shortcode->args);
        return glsr(Schema::class)->build($shortcode->args, $ratings);
    }

    public function generate(): array
    {
        if ($schema = glsr()->filterArray('schema/generate', [], $this)) {
            return $schema;
        } else {
            if ($schema = $this->generateFromBlocks()) {
                return $schema;
            }
            if ($schema = $this->generateFromShortcodes()) {
                return $schema;
            }
        }
        return [];
    }

    public function generateFromBlocks(): array
    {
        $blocks = parse_blocks(get_post()->post_content ?? '');
        $args = $this->parseBlocks($blocks, 'site-reviews/reviews');
        if (Arr::getAs('bool', $args, 'schema')) {
            return $this->buildReviewSchema($args);
        }
        $args = $this->parseBlocks($blocks, 'site-reviews/summary');
        if (Arr::getAs('bool', $args, 'schema')) {
            return $this->buildSummarySchema($args);
        }
        return [];
    }

    public function generateFromShortcodes(): array
    {
        $content = Arr::get(get_post(), 'post_content', '');
        $args = $this->parseShortcodes($content, 'site_reviews');
        if (Arr::getAs('bool', $args, 'schema')) {
            return $this->buildReviewSchema($args);
        }
        $args = $this->parseShortcodes($content, 'site_reviews_summary');
        if (Arr::getAs('bool', $args, 'schema')) {
            return $this->buildSummarySchema($args);
        }
        return [];
    }

    protected function parseBlocks(array $blocks, string $name = 'site-reviews/reviews', array $result = []): array
    {
        foreach ($blocks as $block) {
            $children = $block['innerBlocks'];
            if ($name === $block['blockName'] && !empty($block['attrs']['schema'])) {
                $result = Arr::consolidate($block['attrs']);
            } elseif (!empty($children)) {
                $result = $this->parseBlocks($children, $name, $result);
            }
            if (!empty($result)) {
                return $result;
            }
        }
        return [];
    }

    protected function parseShortcodes(string $content, string $name = 'site_reviews'): array
    {
        if (false === strpos($content, '[')) {
            return [];
        }
        preg_match_all('/'.get_shortcode_regex().'/', $content, $matches, PREG_SET_ORDER);
        if (empty($matches)) {
            return [];
        }
        foreach ($matches as $shortcode) {
            if ($name !== $shortcode[2]) {
                continue;
            }
            $attributes = shortcode_parse_atts($shortcode[3]);
            if (is_array($attributes)) {
                return $attributes;
            }
        }
        return [];
    }
}
