<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;

class Controller extends BaseController
{
    /**
     * @action admin_head
     */
    public function displaySettingNotice()
    {
        if (!$this->isReviewAdminPage()) {
            return;
        }
        $settings = get_option('sd_data');
        if (1 === (int) Arr::get($settings, 'saswp-markup-footer')) {
            return;
        }
        $message = sprintf(_x('Please go to the %sSchema & Structured Data plugin settings%s page and enable the "%s" option.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s" target="_blank">', admin_url('admin.php?page=structured_data_options&tab=tools')),
            '</a>',
            '<strong>Add Schema Markup in footer</strong>'
        );
        glsr(Notice::class)->addError($message, [
            _x('The Schema & Structured Data integration with Site Reviews will only work if the schema markup is added to the footer.', 'admin-text', 'site-reviews'),
        ]);
    }

    /**
     * @filter saswp_modify_schema_output
     */
    public function filterSchema(array $schema): array
    {
        $schemas = glsr()->filterArray('schema/all', glsr()->retrieve('schemas', []));
        if (empty($schemas)) {
            return $schema;
        }
        $types = Arr::consolidate(glsr_get_option('schema.integration.types'));
        foreach ($schema as $key => $values) {
            $type = Arr::get($values, '@type');
            if (!in_array($type, $types)) {
                continue;
            }
            if ($rating = Arr::get($schemas, '0.aggregateRating')) {
                $schema[$key]['aggregateRating'] = $rating;
            }
            if ($review = Arr::get($schemas, '0.review')) {
                $schema[$key]['review'] = $review;
            }
        }
        return $schema;
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsSanitize(array $options, array $input): array
    {
        $key = 'settings.schema.integration.types';
        $options = Arr::set($options, $key, Arr::get($input, $key, []));
        return $options;
    }
}
