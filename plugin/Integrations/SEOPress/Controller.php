<?php

namespace GeminiLabs\SiteReviews\Integrations\SEOPress;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\RankMath\Defaults\RatingSchemaTypeDefaults;
use GeminiLabs\SiteReviews\Modules\Schema;

class Controller extends AbstractController
{
    /**
     * @param array $data
     *
     * @filter seopress_schemas_auto_course_json
     * @filter seopress_schemas_auto_event_json
     * @filter seopress_schemas_auto_faq_json
     * @filter seopress_schemas_auto_job_json
     * @filter seopress_schemas_auto_lb_json
     * @filter seopress_schemas_auto_product_json
     * @filter seopress_schemas_auto_recipe_json
     * @filter seopress_schemas_auto_service_json
     * @filter seopress_schemas_auto_softwareapp_json
     * @filter seopress_schemas_auto_video_json
     *
     * @see https://www.seopress.org/wordpress-seo-plugins/free/
     */
    public function filterSchema($data): array
    {
        $data = Arr::consolidate($data);
        $schema = $this->filterSchemas([$data]);
        return Arr::getAs('array', $schema, 0);
    }

    /**
     * @param array $data
     *
     * @filter seopress_json_schema_generator_get_jsons
     *
     * @see https://www.seopress.org/wordpress-seo-plugins/pro/
     */
    public function filterSchemas($data): array
    {
        $data = Arr::consolidate($data);
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $data;
        }
        $allowedTypes = glsr(RatingSchemaTypeDefaults::class)->defaults();
        foreach ($data as $key => $values) {
            if (!in_array(Arr::get($values, '@type'), $allowedTypes)) {
                continue;
            }
            $aggregateRatingSchema = Arr::get($schema, 'aggregateRating');
            $reviewSchema = Arr::get($schema, 'review');
            if ($aggregateRatingSchema) {
                $data[$key]['aggregateRating'] = $aggregateRatingSchema;
            }
            if ($reviewSchema) {
                $data[$key]['review'] = $reviewSchema;
            }
        }
        return $data;
    }
}
