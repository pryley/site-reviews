<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Schema;

class SiteReviewsSummaryShortcode extends Shortcode
{
    /**
     * @var array
     */
    protected $ratings;

    public function buildTemplate(): string
    {
        $this->ratings = glsr(RatingManager::class)->ratings($this->args);
        $this->debug(['ratings' => $this->ratings]);
        if ($this->isEmpty()) {
            return glsr()->filterString('summary/if_empty', '');
        }
        $this->generateSchema();
        return glsr(Template::class)->build('templates/reviews-summary', [
            'args' => $this->args,
            'context' => [
                'class' => $this->getClasses(),
                'id' => '', // @deprecated in v5.0
                'percentages' => $this->buildTemplateTag('percentages'),
                'rating' => $this->buildTemplateTag('rating'),
                'stars' => $this->buildTemplateTag('stars'),
                'text' => $this->buildTemplateTag('text'),
            ],
        ]);
    }

    protected function buildTemplateTag(string $tag): string
    {
        $args = $this->args;
        $className = Helper::buildClassName(['summary', $tag, 'tag'], 'Modules\Html\Tags');
        $className = glsr()->filterString("summary/tag/{$tag}", $className, $this);
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('summary', null, $this->ratings)
            : '';
        return glsr()->filterString("summary/build/{$tag}", $field, $this->ratings, $this);
    }

    protected function generateSchema(): void
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->buildSummary($this->args, $this->ratings)
            );
        }
    }

    protected function getClasses(): string
    {
        $classes = ['glsr-summary'];
        $classes[] = $this->args['class'];
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    protected function hideOptions(): array
    {
        return [
            'rating' => _x('Hide the rating', 'admin-text', 'site-reviews'),
            'stars' => _x('Hide the stars', 'admin-text', 'site-reviews'),
            'summary' => _x('Hide the summary', 'admin-text', 'site-reviews'),
            'bars' => _x('Hide the percentage bars', 'admin-text', 'site-reviews'),
            'if_empty' => _x('Hide if no reviews are found', 'admin-text', 'site-reviews'),
        ];
    }

    protected function isEmpty(): bool
    {
        return !array_sum($this->ratings) && in_array('if_empty', $this->args['hide']);
    }
}
