<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewResponseTag extends ReviewContentTag
{
    protected function label(): string
    {
        $responseBy = glsr()->filterString('review/build/tag/response/by',
            get_bloginfo('name'),
            $this->review
        );
        /* translators: %s: the name of the site */
        return wptexturize(sprintf(__('Response from %s', 'site-reviews'), $responseBy));
    }
}
