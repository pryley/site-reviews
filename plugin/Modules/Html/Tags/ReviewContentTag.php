<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewContentTag extends ReviewTag
{
    protected function handle(string $value = ''): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->textExcerpt((string) $value), 'div');
    }

    protected function textExcerpt(string $value): string
    {
        $useExcerpts = glsr_get_option('reviews.excerpts', false, 'bool');
        if ($this->isRaw() || !$useExcerpts) {
            return Text::text($value);
        }
        $limit = glsr_get_option('reviews.excerpts_length', 55, 'int');
        return Text::excerpt($value, $limit);
    }

    protected function wrapValue(string $tag, string $value): string
    {
        return glsr(Builder::class)->$tag([
            'class' => 'glsr-tag-value',
            'data-expanded' => 'false',
            'text' => $value,
        ]);
    }
}
