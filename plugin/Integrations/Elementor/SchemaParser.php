<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\SchemaParser as Parser;

class SchemaParser extends Parser
{
    public function generate(): array
    {
        if (!class_exists('Elementor\Plugin')) {
            return [];
        }
        $document = \Elementor\Plugin::$instance->documents->get((int) get_the_ID());
        if (false === $document || !$document->is_built_with_elementor()) {
            return [];
        }
        $data = Arr::consolidate($document->get_elements_data());
        $data = $this->parseElementorData($data);
        foreach ($data as $shortcode => $args) {
            if ('site_reviews' === $shortcode) {
                return $this->buildReviewSchema($args);
            }
            if ('site_reviews_summary' === $shortcode) {
                return $this->buildSummarySchema($args);
            }
        }
        return [];
    }

    protected function parseElementorData(array $elements, array $result = []): array
    {
        foreach ($elements as $data) {
            $element = \Elementor\Plugin::$instance->elements_manager->create_element_instance($data);
            if (!$element) {
                continue;
            }
            $name = $element->get_name();
            if ('template' === $name) {
                $postId = Cast::toInt($element->get_settings('template_id'));
                if ($document = \Elementor\Plugin::$instance->documents->get($postId)) {
                    $templateElements = Arr::consolidate($document->get_elements_data());
                    $result = $this->parseElementorData($templateElements, $result);
                }
                continue;
            }
            $children = $element->get_data('elements');
            if (!empty($children)) {
                $result = $this->parseElementorData($children, $result);
                continue;
            }
            if (!in_array($name, ['site_reviews', 'site_reviews_summary'])) {
                continue;
            }
            if (!Cast::toBool($element->get_settings('schema'))) {
                continue;
            }
            if (array_key_exists($name, $result)) {
                continue;
            }
            $result[$name] = $element->get_data('settings');
        }
        return $result;
    }
}
