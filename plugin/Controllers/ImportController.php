<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Defaults\AdditionalFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\StatDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Request;

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
        $meta = Arr::consolidate($data['meta_input'] ?? []);
        $meta = $this->insertAdditionalMeta($meta, $command->request);
        $meta = $this->insertGeolocationMeta($meta, $command->request);
        $data['meta_input'] = $meta;
        return $data;
    }

    protected function insertAdditionalMeta(array $meta, Request $request): array
    {
        $additional = glsr(AdditionalFieldsDefaults::class)->restrict($request->toArray());
        foreach ($additional as $key => $value) {
            if (!empty($value)) {
                $key = Str::prefix($key, '_');
                $meta[$key] = $value;
            }
        }
        return $meta;
    }

    protected function insertGeolocationMeta(array $meta, Request $request): array
    {
        $data = [];
        $prefix = 'geolocation_';
        foreach ($request->toArray() as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $data[Str::removePrefix($key, $prefix)] = $value;
            }
        }
        if (!empty($data)) {
            $geolocation = glsr(StatDefaults::class)->restrict($data);
            unset($geolocation['rating_id']);
            $meta['_geolocation'] = $geolocation;
        }
        return $meta;
    }
}
