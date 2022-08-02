<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewContentTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->textExcerpt($value), 'div');
        }
    }

    protected function textExcerpt($value)
    {
        $useExcerpts = glsr_get_option('reviews.excerpts', false, 'bool');
        if ($this->isRaw() || !$useExcerpts) {
            return Text::text($value);
        }
        $limit = Cast::toInt(glsr_get_option('reviews.excerpts_length', 55));
        return Text::excerpt($value, $limit);
    }

    /**
     * @param string $value
     * @param string $tag
     * @return string
     */
    protected function wrapValue($tag, $value)
    {
        return glsr(Builder::class)->$tag([
            'class' => 'glsr-tag-value',
            'data-expanded' => 'false',
            'text' => $value,
        ]);
    }
}
