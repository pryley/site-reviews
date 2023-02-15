<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class Reviews extends \ArrayObject
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var int
     */
    public $max_num_pages;

    /**
     * @var array
     */
    public $reviews;

    /**
     * @var int
     */
    public $total;

    public function __construct(array $reviews, int $total, array $args)
    {
        $this->args = glsr(SiteReviewsDefaults::class)->unguardedMerge($args);
        $this->max_num_pages = Cast::toInt(ceil($total / $this->args['display']));
        $this->reviews = $reviews;
        $this->total = $total;
        parent::__construct($this->reviews, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    public function __toString()
    {
        return (string) $this->build();
    }

    public function attributes(): array
    {
        return glsr(SiteReviewsShortcode::class)->attributes($this->args);
    }

    public function build(): ReviewsHtml
    {
        return new ReviewsHtml($this);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->reviews)) {
            return $this->reviews[$key];
        }
        return property_exists($this, $key)
            ? $this->$key
            : null;
    }

    public function render(): void
    {
        echo $this->build();
    }
}
