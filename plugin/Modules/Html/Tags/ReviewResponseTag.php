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
        if ($this->isHidden() || empty(trim((string) $value))) {
            return '';
        }
        $responseBy = glsr()->filterString('review/build/tag/response/by', get_bloginfo('name'), $this->review);
        $text = $this->textExcerpt($value);
        $title = sprintf(__('Response from %s', 'site-reviews'), $responseBy);
        $response = glsr(Builder::class)->div([
            'class' => 'glsr-review-response-inner',
            'text' => sprintf('<p><strong>%s</strong></p>%s', $title, $text),
        ]);
        return $this->wrap($response, 'div');
    }
}
