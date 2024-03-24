<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

/**
 * @property string $avatar
 * @property string $content
 * @property string $date
 * @property string $author
 * @property int    $rating
 * @property string $response
 * @property string $title
 */
class ReviewHtml extends \ArrayObject
{
    public array $args;
    public array $context;
    public Review $review;

    protected array $attributes = [];

    public function __construct(Review $review, array $args = [])
    {
        $this->args = glsr(SiteReviewsDefaults::class)->unguardedMerge($args);
        $this->context = $this->buildContext($review);
        $this->review = $review;
        parent::__construct($this->context, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    public function __toString(): string
    {
        if (empty($this->context)) {
            return '';
        }
        return glsr(Template::class)->build('templates/review', [
            'args' => $this->args,
            'context' => $this->context,
            'review' => $this->review,
        ]);
    }

    public function attributes(): array
    {
        if (empty($this->attributes)) {
            $this->attributes = glsr(SiteReviewsShortcode::class)->attributes($this->args);
        }
        return $this->attributes;
    }

    public function buildContext(Review $review): array
    {
        $context = $this->buildTemplateTags($review);
        return glsr()->filterArray('review/build/context', $context, $review, $this);
    }

    /**
     * @param string|array $value
     */
    public function buildTemplateTag(Review $review, string $tag, $value): string
    {
        $args = $this->args;
        $className = Helper::buildClassName(['review', $tag, 'tag'], 'Modules\Html\Tags');
        $className = glsr()->filterString("review/tag/{$tag}", $className, $this);
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('review', $value, $review)
            : Cast::toString($value, false);
        return glsr()->filterString("review/build/tag/{$tag}", $field, $value, $review, $this);
    }

    public function buildTemplateTags(Review $review): array
    {
        glsr()->action('review/build/before', $review, $this);
        $templateTags = [];
        $assignedTag = array_filter([
            'assigned_posts' => $review->assigned_posts,
            'assigned_terms' => $review->assigned_terms,
            'assigned_users' => $review->assigned_users,
        ]);
        $templateTags['assigned'] = wp_json_encode($assignedTag);
        $values = $review->toArray();
        foreach ($values as $key => $value) {
            $tag = $this->normalizeTemplateTag($key);
            $templateTags[$tag] = $this->buildTemplateTag($review, $tag, $value);
        }
        return glsr()->filterArray('review/build/after', $templateTags, $review, $this);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        if ('attributes' === $key) {
            return glsr(Attributes::class)->div($this->attributes())->toString();
        }
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }
        $key = Helper::ifTrue('values' === $key, 'context', $key); // @deprecated in v5.0
        return Helper::ifTrue(property_exists($this, $key), $this->$key);
    }

    protected function normalizeTemplateTag(string $tag): string
    {
        $mappedTags = [
            'ID' => 'review_id',
            'is_verified' => 'verified',
        ];
        return Arr::get($mappedTags, $tag, $tag);
    }
}
