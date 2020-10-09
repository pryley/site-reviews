<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use ArrayObject;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class ReviewHtml extends ArrayObject
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    public $context;

    /**
     * @var Review
     */
    public $review;

    public function __construct(Review $review, array $args = [])
    {
        $this->args = glsr(SiteReviewsDefaults::class)->merge($args);
        $this->context = $this->buildContext($review);
        $this->review = $review;
        parent::__construct($this->context, ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string|void
     */
    public function __toString()
    {
        if (empty($this->context)) {
            return '';
        }
        return glsr(Template::class)->build('templates/review', [
            'context' => $this->context,
            'review' => $this->review,
        ]);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }
        $key = Helper::ifTrue('values' === $key, 'context', $key); // @deprecated in v5.0
        return Helper::ifTrue(property_exists($this, $key), $this->$key);
    }

    protected function buildContext(Review $review)
    {
        glsr()->action('review/build/before', $review);
        $templateTags = [];
        foreach ($review as $key => $value) {
            $tag = $this->normalizeTemplateTag($key);
            $templateTags[$tag] = $this->buildTemplateTag($review, $tag, $value);
        }
        $templateTags['assigned_to'] = $templateTags['assigned_links']; // @deprecated in v5.0
        return glsr()->filterArray('review/build/after', $templateTags, $review, $this);
    }

    /**
     * @param string $tag
     * @param string $value
     * @return string
     */
    protected function buildTemplateTag(Review $review, $tag, $value)
    {
        $args = $this->args;
        $tagSlug = implode('-', ['review', $tag, 'tag']);
        $className = Helper::buildClassName($tagSlug, 'Modules\Html\Tags');
        $className = glsr()->filterString('review/tag/'.$tag, $className);
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('review', $value, $review)
            : null;
        return glsr()->filterString('review/build/'.$tag, $field, $value, $review, $this);
    }

    /**
     * @param string $tag
     * @return string
     */
    protected function normalizeTemplateTag($tag)
    {
        $mappedTags = [
            'assigned_posts' => 'assigned_links',
            'ID' => 'review_id',
        ];
        return Arr::get($mappedTags, $tag, $tag);
    }
}
