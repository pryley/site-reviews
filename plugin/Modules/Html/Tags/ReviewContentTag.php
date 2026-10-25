<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Text;

class ReviewContentTag extends ReviewTag
{
    protected array $valueAttributes = ['data-expanded' => 'false'];

    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value(), 'div');
    }

    protected function value(): string
    {
        if ('' === $this->value) {
            return ''; // an excerpt of nothing is an empty paragraph
        }
        $useExcerpts = glsr_get_option('reviews.excerpts', false, 'bool');
        if ($this->isRaw() || !$useExcerpts) {
            return Text::text($this->value);
        }
        $limit = glsr_get_option('reviews.excerpts_length', 55, 'int');
        return Text::excerpt($this->value, $limit);
    }
}
