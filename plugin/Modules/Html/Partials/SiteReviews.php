<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviews
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var Reviews
     */
    protected $reviews;

    /**
     * @param Reviews|null $reviews
     * @return ReviewsHtml
     */
    public function build(array $args = [], $reviews = null)
    {
        $this->args = glsr(SiteReviewsDefaults::class)->merge($args);
        if (!($reviews instanceof Reviews)) {
            $reviews = glsr(ReviewManager::class)->get($this->args);
        }
        $this->reviews = $reviews;
        $this->generateSchema();
        return $this->buildReviews();
    }

    /**
     * @return ReviewHtml
     */
    public function buildReview(Review $review)
    {
        $review = glsr()->filter('review/build/before', $review);
        $renderedFields = [];
        foreach ($review as $key => $value) {
            $tag = $this->normalizeTemplateTag($key);
            $field = $this->buildTemplateTag($review, $tag, $value);
            if (false !== $field) {
                $renderedFields[$tag] = $field;
            }
        }
        $this->wrap($renderedFields, $review);
        $renderedFields = glsr()->filterArray('review/build/after', $renderedFields, $review, $this);
        return new ReviewHtml($review, $renderedFields);
    }

    /**
     * @return ReviewsHtml
     */
    public function buildReviews()
    {
        $renderedReviews = [];
        foreach ($this->reviews as $index => $review) {
            $renderedReviews[] = $this->buildReview($review);
        }
        return new ReviewsHtml($renderedReviews, $this->reviews->max_num_pages, $this->args);
    }

    /**
     * @return void
     */
    public function generateSchema()
    {
        if (!wp_validate_boolean($this->args['schema'])) {
            return;
        }
        glsr(Schema::class)->store(
            glsr(Schema::class)->build($this->args, $this->reviews)
        );
    }

    /**
     * @param string $tag
     * @param string $value
     * @return string
     */
    protected function buildTemplateTag(Review $review, $tag, $value)
    {
        $args = $this->args;
        $className = Helper::buildClassName($tag.'-tag', 'Modules\Html\Tags');
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'review', 'args'))->handle($value)
            : false;
        return glsr()->filterString('review/build/'.$tag, $field, $value, $review, $this);
    }

    /**
     * @param string $tag
     * @return string
     */
    protected function normalizeTemplateTag($tag)
    {
        $mappedTags = [
            'assigned_post_ids' => 'assigned_to',
        ];
        return array_key_exists($tag, $mappedTags)
            ? $mappedTags[$tag]
            : $tag;
    }

    /**
     * @return void
     */
    protected function wrap(array &$renderedFields, Review $review)
    {
        $renderedFields = glsr()->filterArray('review/wrap', $renderedFields, $review, $this);
        array_walk($renderedFields, function (&$value, $key) use ($review) {
            $value = glsr()->filterString('review/wrap/'.$key, $value, $review);
            if (empty($value)) {
                return;
            }
            $value = glsr(Builder::class)->div('<span>'.$value.'</span>', [
                'class' => 'glsr-review-'.$key,
            ]);
        });
    }
}
