<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Wpml implements Contract
{
    const PLUGIN_NAME = 'WPML';
    const SUPPORTED_VERSION = '3.3.5';

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
        foreach ($this->cleanIds($postIds) as $postId) {
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
        return $this->cleanIds($newPostIds);
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
            && version_compare(ICL_SITEPRESS_VERSION, static::SUPPORTED_VERSION, '>=');
    }

    /**
     * @return array
     */
    protected function cleanIds(array $postIds)
    {
        return array_filter(array_unique($postIds));
    }
}
