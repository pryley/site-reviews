<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use ArrayObject;
use GeminiLabs\SiteReviews\Review;

class ReviewHtml extends ArrayObject
{
    /**
     * @var Review
     */
    public $review;

    /**
     * @var array
     */
    public $values;

    public function __construct(Review $review, array $values = [])
    {
        $this->review = $review;
        $this->values = $values;
        parent::__construct($values, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string|void
     */
    public function __toString()
    {
        if (empty($this->values)) {
            return;
        }
        return glsr(Template::class)->build('templates/review', [
            'context' => $this->values,
            'review' => $this->review,
        ]);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }
        return property_exists($this, $key)
            ? $this->$key
            : null;
    }
}
