<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ResponseTag extends ContentTag
{
    /**
     * {@inheritdoc}
     */
    public function handle($value)
    {
        if ($this->isHidden() || empty(trim($value))) {
            return;
        }
        $title = sprintf(__('Response from %s', 'site-reviews'), get_bloginfo('name'));
        $text = $this->normalizeText($value);
        $text = '<p><strong>'.$title.'</strong></p><p>'.$text.'</p>';
        $response = glsr(Builder::class)->div($text, [
            'class' => 'glsr-review-response-inner',
        ]);
        $background = glsr(Builder::class)->div([
            'class' => 'glsr-review-response-background',
        ]);
        return $response.$background;
    }
}
