<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewResponseTag extends ReviewContentTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden() && !empty(trim($value))) {
            $responseBy = glsr()->filterString('review/build/tag/response/by', get_bloginfo('name'), $this->review);
            $title = sprintf(__('Response from %s', 'site-reviews'), $responseBy);
            $text = $this->normalizeText($value);
            $response = glsr(Builder::class)->div([
                'class' => 'glsr-review-response-inner',
                'text' => sprintf('<p><strong>%s</strong></p><p>%s</p>', $title, $text),
            ]);
            return $this->wrap($response);
        }
    }
}
