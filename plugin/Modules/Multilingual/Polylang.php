<?php

namespace GeminiLabs\SiteReviews\Modules\Multilingual;

use GeminiLabs\SiteReviews\Contracts\MultilingualContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Polylang implements MultilingualContract
{
    public $pluginName = 'Polylang';
    public $supportedVersion = '2.3';

    public function getPostId(int $postId): int
    {
        if ($this->isEnabled()) {
            $polylangPostId = pll_get_post($postId, pll_get_post_language((int) get_the_ID()));
        }
        if (!empty($polylangPostId)) {
            $postId = $polylangPostId;
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
            $newPostIds = array_merge($newPostIds,
                array_values(pll_get_post_translations($postId))
            );
        }
        return Arr::uniqueInt($newPostIds);
    }

    public function isActive(): bool
    {
        return function_exists('PLL')
            && function_exists('pll_get_post')
            && function_exists('pll_get_post_language')
            && function_exists('pll_get_post_translations');
    }

    public function isEnabled(): bool
    {
        return $this->isActive()
            && 'polylang' === glsr(OptionManager::class)->get('settings.general.multilingual');
    }

    public function isSupported(): bool
    {
        return defined('POLYLANG_VERSION')
            && Helper::isGreaterThanOrEqual(POLYLANG_VERSION, $this->supportedVersion);
    }
}
