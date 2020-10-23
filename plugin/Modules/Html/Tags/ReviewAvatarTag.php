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
        return $this->canRegenerateAvatar()
            ? glsr(Avatar::class)->generate($this->userField())
            : $avatarUrl;
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
            return $this->wrap(
                glsr(Avatar::class)->img($this->regenerateAvatar($value))
            );
        }
    }

    /**
     * @return int|string
     */
    protected function userField()
    {
        if ($this->review->author_id) {
            $authorId = get_the_author_meta('ID', $this->review->author_id);
        }
        return empty($authorId)
            ? $this->review->email
            : $authorId;
    }
}
