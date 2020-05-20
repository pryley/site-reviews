<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Review;

abstract class Tag
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $args;

    /**
     * @var string
     */
    public $key;

    /**
     * @var \GeminiLabs\SiteReviews\Review
     */
    public $review;

    public function __construct($key, Review $review, array $args = [])
    {
        $this->args = glsr()->args($args);
        $this->key = $key;
        $this->review = $review;
    }

    /**
     * @param string $value
     * @return string|null
     */
    abstract public function handle($value);

    /**
     * @param string $path
     * @return bool
     */
    public function isEnabled($path)
    {
        return glsr_get_option($path, true, 'bool');
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isHidden($path = '')
    {
        return in_array($this->key, $this->args->hide) || !$this->isEnabled($path);
    }
}
