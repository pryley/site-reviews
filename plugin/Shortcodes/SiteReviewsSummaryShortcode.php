<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;
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

    public function description(): string
    {
        return esc_html_x('Display a rating summary', 'admin-text', 'site-reviews');
    }

    public function name(): string
    {
        return esc_html_x('Rating Summary', 'admin-text', 'site-reviews');
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

    protected function config(): array
    {
        return [
            'assigned_posts' => [
                'label' => esc_html_x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a Page...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'assigned_terms' => [
                'label' => esc_html_x('Limit Reviews by Categories', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a Category...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'assigned_users' => [
                'label' => esc_html_x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a User...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'terms' => [
                'label' => esc_html_x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews'),
                'options' => $this->options('terms'),
                'placeholder' => esc_html_x('Select Review Terms...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'type' => [
                'label' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
                'options' => $this->options('type'),
                'placeholder' => esc_html_x('Select a Review Type...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'rating' => [
                'default' => (string) Rating::min(),
                'group' => 'display',
                'label' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Rating::max(),
                'min' => Rating::min(),
                'placeholder' => (string) Rating::min(),
                'type' => 'number',
            ],
            'rating_field' => [
                'description' => sprintf(_x('Use the %sReview Forms%s addon to add custom rating fields.', 'admin-text', 'site-reviews'),
                    '<a href="https://niftyplugins.com/plugins/site-reviews-forms/" target="_blank">', '</a>'
                ),
                'label' => esc_html_x('Custom Rating Field Name', 'admin-text', 'site-reviews'),
                'group' => 'display',
                'type' => 'text',
            ],
            'schema' => [
                'description' => esc_html_x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews'),
                'group' => 'schema',
                'label' => esc_html_x('Enable the schema?', 'admin-text', 'site-reviews'),
                'type' => 'checkbox',
            ],
            'hide' => [
                'group' => 'hide',
                'options' => $this->options('hide'),
                'type' => 'checkbox',
            ],
            'id' => [
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'class' => [
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
        ];
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
