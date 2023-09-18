<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\SchemaParser as Parser;

class SchemaParser extends Parser
{
    public function generate(): array
    {
        $postId = (int) get_the_ID();
        if (empty($postId)
            || !class_exists('Elementor\Plugin')
            || !\Elementor\Plugin::$instance->documents->get($postId)->is_built_with_elementor()) { // @phpstan-ignore-line
            return [];
        }
        $widgets = Cast::toString(get_post_meta($postId, '_elementor_data', true));
        $widgets = json_decode($widgets, true);
        $widgets = Arr::consolidate($widgets);
        $args = $this->parseElementor($widgets, 'site_reviews');
        if (Arr::getAs('bool', $args, 'schema')) {
            return $this->buildReviewSchema($args);
        }
        $args = $this->parseElementor($widgets, 'site_reviews_summary');
        if (Arr::getAs('bool', $args, 'schema')) {
            return $this->buildSummarySchema($args);
        }
        return [];
    }

    protected function parseElementor(array $widgets, string $name = 'site_reviews', array $result = []): array
    {
        foreach ($widgets as $widget) {
            $children = $widget['elements'];
            if ($name === Arr::get($widget, 'widgetType') && Arr::getAs('bool', $widget, 'settings.schema')) {
                $result = Arr::consolidate($widget['settings']);
            } elseif (!empty($children)) {
                $result = $this->parseElementor($children, $name, $result);
            }
            if (!empty($result)) {
                return $result;
            }
        }
        return [];
    }
}
