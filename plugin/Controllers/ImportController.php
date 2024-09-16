<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Defaults\AdditionalFieldsDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;

class ImportController extends AbstractController
{
    /**
     * @filter site-reviews/review/create/post_data
     */
    public function filterReviewPostData(array $data, CreateReview $command): array
    {
        if (!defined('WP_IMPORTING')) {
            return $data;
        }
        $additional = glsr(AdditionalFieldsDefaults::class)->restrict($command->request->toArray());
        foreach ($additional as $key => $value) {
            if (!empty($value)) {
                $key = Str::prefix($key, '_');
                $data['meta_input'][$key] = $value;
            }
        }
        return $data;
    }
}
