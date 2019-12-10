<?php

namespace GeminiLabs\SiteReviews\Modules\Multilingual;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Wpml implements Contract
{
    public $pluginName = 'WPML';
    public $supportedVersion = '3.3.5';

    /**
     * {@inheritdoc}
     */
    public function getPost($postId)
    {
        $postId = trim($postId);
        if (!is_numeric($postId)) {
            return;
        }
        if ($this->isEnabled()) {
            $postId = apply_filters('wpml_object_id', $postId, 'any', true);
        }
        return get_post(intval($postId));
    }

    /**
     * {@inheritdoc}
     */
    public function getPostIds(array $postIds)
    {
        if (!$this->isEnabled()) {
            return $postIds;
        }
        $newPostIds = [];
        foreach (Arr::unique($postIds) as $postId) {
            $postType = get_post_type($postId);
            if (!$postType) {
                continue;
            }
            $elementType = 'post_'.$postType;
            $trid = apply_filters('wpml_element_trid', null, $postId, $elementType);
            $translations = apply_filters('wpml_get_element_translations', null, $trid, $elementType);
            if (!is_array($translations)) {
                $translations = [];
            }
            $newPostIds = array_merge(
                $newPostIds,
                array_column($translations, 'element_id')
            );
        }
        return Arr::unique($newPostIds);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return defined('ICL_SITEPRESS_VERSION');
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->isActive()
            && 'wpml' == glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        return $this->isActive()
            && version_compare(ICL_SITEPRESS_VERSION, $this->supportedVersion, '>=');
    }
}
