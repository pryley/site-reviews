<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewAvatarTag extends ReviewTag
{
    /**
     * @param string $avatarUrl
     * @return string
     */
    public function regenerateAvatar($avatarUrl)
    {
        if ($this->canRegenerateAvatar()) {
            return glsr(Avatar::class)->generate($this->review);
        }
        return $avatarUrl;
    }

    /**
     * @return bool
     */
    protected function canRegenerateAvatar()
    {
        return 'local' === $this->review->type
            && glsr_get_option('reviews.avatars_regenerate', false, 'bool');
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden('reviews.avatars')) {
            $this->review->set('avatar', $this->regenerateAvatar($value));
            return $this->wrap(
                glsr(Avatar::class)->img($this->review)
            );
        }
    }
}
