<?php

namespace GeminiLabs\SiteReviews\Modules\Multilingual;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Wpml implements MultilingualContract
{
    public $pluginName = 'WPML';
    public $supportedVersion = '3.3.5';

    public function getPostId(int $postId): int
    {
        if ($this->isEnabled()) {
            $postId = apply_filters('wpml_object_id', $postId, 'any', true);
        }
        return intval($postId);
    }

    public function getPostIds(array $postIds): array
    {
        if (!$this->isEnabled()) {
            return $postIds;
        }
        $newPostIds = [];
        foreach (Arr::uniqueInt($postIds) as $postId) {
            $postType = get_post_type($postId);
            if (!$postType) {
                continue;
            }
            $elementType = "post_{$postType}";
            $trid = apply_filters('wpml_element_trid', null, $postId, $elementType);
            $translations = apply_filters('wpml_get_element_translations', null, $trid, $elementType);
            if (!is_array($translations)) {
                $translations = [];
            }
            $translatedPostIds = array_values(wp_list_pluck($translations, 'element_id'));
            $newPostIds = array_merge($newPostIds, $translatedPostIds);
        }
        return Arr::uniqueInt($newPostIds);
    }

    public function isActive(): bool
    {
        return defined('ICL_SITEPRESS_VERSION');
    }

    public function isEnabled(): bool
    {
        return $this->isActive()
            && 'wpml' === glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    public function isSupported(): bool
    {
        return $this->isActive()
            && Helper::isGreaterThanOrEqual(ICL_SITEPRESS_VERSION, $this->supportedVersion);
    }
}
