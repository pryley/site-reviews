<?php

namespace GeminiLabs\SiteReviews\Integrations\SEOPress;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isEnabled()) {
            return;
        }
        $this->hook(Controller::class, [
            ['filterSchema', 'seopress_schemas_auto_course_json'],
            ['filterSchema', 'seopress_schemas_auto_event_json'],
            ['filterSchema', 'seopress_schemas_auto_faq_json'],
            ['filterSchema', 'seopress_schemas_auto_job_json'],
            ['filterSchema', 'seopress_schemas_auto_lb_json'],
            ['filterSchema', 'seopress_schemas_auto_product_json'],
            ['filterSchema', 'seopress_schemas_auto_recipe_json'],
            ['filterSchema', 'seopress_schemas_auto_service_json'],
            ['filterSchema', 'seopress_schemas_auto_softwareapp_json'],
            ['filterSchema', 'seopress_schemas_auto_video_json'],
            ['filterSchemas', 'seopress_json_schema_generator_get_jsons'],
        ]);
    }

    protected function isEnabled(): bool
    {
        return 'seopress' === $this->option('schema.integration.plugin');
    }
}
