<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewAvatarTag extends ReviewTag
{
    /**
     * @param string $avatarUrl
     * @return string
     */
    public function regenerateAvatar($avatarUrl, $fallback)
    {
        if ($this->canRegenerateAvatar() && $newAvatarUrl = get_avatar_url($this->userField())) {
            return $newAvatarUrl;
        }
        return empty($avatarUrl)
            ? $fallback
            : $avatarUrl;
    }

    /**
     * @param string $avatarUrl
     * @return bool
     */
    protected function canRegenerateAvatar()
    {
        return glsr_get_option('reviews.avatars_regenerate', false, 'bool') && 'local' === $this->review->type;
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden('reviews.avatars')) {
            $size = glsr_get_option('settings.reviews.avatars_size', 40, 'int');
            $fallback = 'https://gravatar.com/avatar/?d=mm&s='.($size * 2);
            return $this->wrap(glsr(Builder::class)->img([
                'height' => $size,
                'loading' => 'lazy',
                'src' => $this->regenerateAvatar($value, $fallback),
                'style' => sprintf('width:%1$spx; height:%1$spx;', $size),
                'width' => $size,
            ]));
        }
    }

    /**
     * @return int|string
     */
    protected function userField()
    {
        if ($this->review->user_id) {
            $authorId = get_the_author_meta('ID', $this->review->user_id);
        }
        return empty($authorId)
            ? $this->review->email
            : $authorId;
    }
}
