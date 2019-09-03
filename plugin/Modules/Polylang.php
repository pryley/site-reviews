<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Polylang implements Contract
{
    const PLUGIN_NAME = 'Polylang';
    const SUPPORTED_VERSION = '2.3';

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
            $polylangPostId = pll_get_post($postId, pll_get_post_language(get_the_ID()));
        }
        if (!empty($polylangPostId)) {
            $postId = $polylangPostId;
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
            $newPostIds = array_merge(
                $newPostIds,
                array_values(pll_get_post_translations($postId))
            );
        }
        return $this->cleanIds($newPostIds);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return function_exists('PLL')
            && function_exists('pll_get_post')
            && function_exists('pll_get_post_language')
            && function_exists('pll_get_post_translations');
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->isActive()
            && 'polylang' == glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        return defined('POLYLANG_VERSION')
            && version_compare(POLYLANG_VERSION, static::SUPPORTED_VERSION, '>=');
    }

    /**
     * @return array
     */
    protected function cleanIds(array $postIds)
    {
        return array_filter(array_unique($postIds));
    }
}
