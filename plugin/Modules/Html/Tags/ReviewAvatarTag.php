<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Avatar;

class ReviewAvatarTag extends ReviewTag
{
    public function regenerateAvatar(string $avatarUrl): string
    {
        if ($this->canRegenerateAvatar()) {
            return glsr(Avatar::class)->generate($this->review);
        }
        return $avatarUrl;
    }

    protected function canRegenerateAvatar(): bool
    {
        return 'local' === $this->review->type
            && glsr_get_option('reviews.avatars_regenerate', false, 'bool');
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if ($this->isHidden('reviews.avatars')) {
            return '';
        }
        $this->review->set('avatar', $this->regenerateAvatar($value));
        return $this->wrap(
            glsr(Avatar::class)->img($this->review)
        );
    }
}
