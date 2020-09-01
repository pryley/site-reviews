<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Schema;

class SiteReviewsSummary implements PartialContract
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    protected $ratings;

    /**
     * {@inheritdoc}
     */
    public function build(array $args = [])
    {
        $this->args = $args;
        $this->ratings = glsr(RatingManager::class)->ratings($args);
        if ($this->isEmpty()) {
            return;
        }
        $this->generateSchema();
        return glsr(Template::class)->build('templates/reviews-summary', [
            'args' => $this->args,
            'context' => [
                'class' => $this->getClass(),
                'id' => '', // @deprecated in v5.0
                'percentages' => $this->buildTemplateTag('percentages'),
                'rating' => $this->buildTemplateTag('rating'),
                'stars' => $this->buildTemplateTag('stars'),
                'text' => $this->buildTemplateTag('text'),
            ],
        ]);
    }

    /**
     * @param string $tag
     * @return string
     */
    protected function buildTemplateTag($tag)
    {
        $args = $this->args;
        $classname = implode('-', ['summary', $tag, 'tag']);
        $className = Helper::buildClassName($classname, 'Modules\Html\Tags');
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('summary', null, $this->ratings)
            : null;
        return glsr()->filterString('summary/build/'.$tag, $field, $this->ratings, $this);
    }

    /**
     * @return void
     */
    protected function generateSchema()
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->buildSummary($this->args, $this->ratings)
            );
        }
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return trim('glsr-summary '.$this->args['class']);
    }

    /**
     * @return bool
     */
    protected function isEmpty()
    {
        return !array_sum($this->ratings) && in_array('if_empty', $this->args['hide']);
    }
}
