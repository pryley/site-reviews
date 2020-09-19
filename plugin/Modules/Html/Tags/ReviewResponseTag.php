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
            $title = sprintf(__('Response from %s', 'site-reviews'), get_bloginfo('name'));
            $text = $this->normalizeText($value);
            $response = glsr(Builder::class)->div([
                'class' => 'glsr-review-response-inner',
                'text' => sprintf('<p><strong>%s</strong></p><p>%s</p>', $title, $text),
            ]);
            return $this->wrap($response);
        }
    }
}
