<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Schema;

class SiteReviewsSummary
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var float
     */
    protected $averageRating;

    /**
     * @var array
     */
    protected $ratingCounts;

    /**
     * @return void|string
     */
    public function build(array $args = [])
    {
        $this->args = $args;
        $this->ratingCounts = glsr(ReviewManager::class)->getRatingCounts($args);
        if (!array_sum($this->ratingCounts) && $this->isHidden('if_empty')) {
            return;
        }
        $this->averageRating = glsr(Rating::class)->getAverage($this->ratingCounts);
        $this->generateSchema();
        return glsr(Template::class)->build('templates/reviews-summary', [
            'context' => [
                'assigned_to' => $this->args['assigned_to'],
                'category' => $this->args['category'],
                'class' => $this->getClass(),
                'id' => $this->args['id'],
                'percentages' => $this->buildPercentage(),
                'rating' => $this->buildRating(),
                'stars' => $this->buildStars(),
                'text' => $this->buildText(),
            ],
        ]);
    }

    /**
     * @return void|string
     */
    protected function buildPercentage()
    {
        if ($this->isHidden('bars')) {
            return;
        }
        $percentages = preg_filter('/$/', '%', glsr(Rating::class)->getPercentages($this->ratingCounts));
        $bars = array_reduce(range(glsr()->constant('MAX_RATING', Rating::class), 1), function ($carry, $level) use ($percentages) {
            $label = $this->buildPercentageLabel($this->args['labels'][$level]);
            $background = $this->buildPercentageBackground($percentages[$level]);
            $count = apply_filters('site-reviews/summary/counts',
                $percentages[$level],
                $this->ratingCounts[$level]
            );
            $percent = $this->buildPercentageCount($count);
            $value = $label.$background.$percent;
            $value = apply_filters('site-reviews/summary/wrap/bar', $value, $this->args, [
                'percent' => wp_strip_all_tags($count, true),
                'rating' => $level,
            ]);
            return $carry.glsr(Builder::class)->div($value, [
                'class' => 'glsr-bar',
            ]);
        });
        return $this->wrap('percentage', $bars);
    }

    /**
     * @param string $percent
     * @return string
     */
    protected function buildPercentageBackground($percent)
    {
        $backgroundPercent = glsr(Builder::class)->span([
            'class' => 'glsr-bar-background-percent',
            'style' => 'width:'.$percent,
        ]);
        return '<span class="glsr-bar-background">'.$backgroundPercent.'</span>';
    }

    /**
     * @param string $count
     * @return string
     */
    protected function buildPercentageCount($count)
    {
        return '<span class="glsr-bar-percent">'.$count.'</span>';
    }

    /**
     * @param string $label
     * @return string
     */
    protected function buildPercentageLabel($label)
    {
        return '<span class="glsr-bar-label">'.$label.'</span>';
    }

    /**
     * @return void|string
     */
    protected function buildRating()
    {
        if ($this->isHidden('rating')) {
            return;
        }
        return $this->wrap('rating', '<span>'.$this->averageRating.'</span>');
    }

    /**
     * @return void|string
     */
    protected function buildStars()
    {
        if ($this->isHidden('stars')) {
            return;
        }
        $stars = glsr_star_rating($this->averageRating);
        return $this->wrap('stars', $stars);
    }

    /**
     * @return void|string
     */
    protected function buildText()
    {
        if ($this->isHidden('summary')) {
            return;
        }
        $count = intval(array_sum($this->ratingCounts));
        if (empty($this->args['text'])) {
            // @todo document this change
            $this->args['text'] = _nx(
                '{rating} out of {max} stars (based on {num} review)',
                '{rating} out of {max} stars (based on {num} reviews)',
                $count,
                'Do not translate {rating}, {max}, and {num}, they are template tags.',
                'site-reviews'
            );
        }
        $summary = str_replace(
            ['{rating}', '{max}', '{num}'],
            [$this->averageRating, glsr()->constant('MAX_RATING', Rating::class), $count],
            $this->args['text']
        );
        return $this->wrap('text', '<span>'.$summary.'</span>');
    }

    /**
     * @return void
     */
    protected function generateSchema()
    {
        if (!wp_validate_boolean($this->args['schema'])) {
            return;
        }
        glsr(Schema::class)->store(
            glsr(Schema::class)->buildSummary($this->args)
        );
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return trim('glsr-summary glsr-default '.$this->args['class']);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isHidden($key)
    {
        return in_array($key, $this->args['hide']);
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function wrap($key, $value)
    {
        $value = apply_filters('site-reviews/summary/wrap/'.$key, $value, $this->args);
        return glsr(Builder::class)->div($value, [
            'class' => 'glsr-summary-'.$key,
        ]);
    }
}
