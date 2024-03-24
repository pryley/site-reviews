<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewResponseTag extends ReviewContentTag
{
    protected function handle(): string
    {
        if ($this->isHidden() || empty($this->value)) {
            return '';
        }
        return $this->wrap($this->value(), 'div');
    }

    protected function value(): string
    {
        $responseBy = glsr()->filterString('review/build/tag/response/by', get_bloginfo('name'), $this->review);
        $title = sprintf(__('Response from %s', 'site-reviews'), $responseBy);
        $value = parent::value();
        return glsr(Builder::class)->div([
            'class' => 'glsr-review-response-inner',
            'text' => sprintf('<p><strong>%s</strong></p>%s', $title, $value),
        ]);
    }
}
