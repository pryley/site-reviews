<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryPercentagesTag extends SummaryTag
{
    protected function handle(): string
    {
        if ($this->isHidden()) {
            return '';
        }
        return $this->wrap($this->value());
    }

    protected function ratingBar(int $level, array $percentages): string
    {
        $background = glsr(Builder::class)->span([
            'class' => 'glsr-bar-background-percent',
            'style' => "width:{$percentages[$level]}",
        ]);
        return glsr(Builder::class)->span([
            'class' => 'glsr-bar-background',
            'text' => $background,
        ]);
    }

    protected function ratingInfo(int $level, array $percentages): string
    {
        $count = glsr()->filterString('summary/counts', $percentages[$level], $this->ratings[$level]);
        return glsr(Builder::class)->span([
            'class' => 'glsr-bar-percent',
            'text' => $count,
        ]);
    }

    protected function ratingLabel(int $level): string
    {
        $label = $this->args->get("labels.{$level}");
        return glsr(Builder::class)->span([
            'class' => 'glsr-bar-label',
            'text' => $label,
        ]);
    }

    protected function value(): string
    {
        $percentages = preg_filter('/$/', '%', glsr(Rating::class)->percentages($this->ratings));
        $ratingRange = range(glsr()->constant('MAX_RATING', Rating::class), 1);
        return array_reduce($ratingRange, function ($carry, $level) use ($percentages) {
            $label = $this->ratingLabel($level);
            $bar = $this->ratingBar($level, $percentages);
            $info = $this->ratingInfo($level, $percentages);
            $value = $label.$bar.$info;
            $value = glsr()->filterString('summary/wrap/bar', $value, $this->args, [
                'info' => wp_strip_all_tags($info, true),
                'rating' => $level,
            ]);
            return $carry.glsr(Builder::class)->div([
                'class' => 'glsr-bar',
                'data-level' => $level,
                'text' => $value,
            ]);
        }, '');
    }
}
