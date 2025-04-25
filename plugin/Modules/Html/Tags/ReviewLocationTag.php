<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Defaults\FlagDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ReviewLocationTag extends ReviewTag
{
    protected function handle(): string
    {
        if ($this->isHidden('reviews.location')) {
            return '';
        }
        return $this->wrap($this->value(), 'span');
    }

    protected function formattedCityRegion(array $location): string
    {
        if ('US' !== $location['country'] || is_numeric($location['region'])) {
            $location['region'] = '';
        }
        $parts = array_filter([
            trim($location['city']),
            trim($location['region']),
        ]);
        return implode(', ', $parts);
    }

    protected function formattedCountry(array $location): string
    {
        return trim($location['country']);
    }

    protected function formattedFlag(array $location): string
    {
        $svgUrl = Svg::url(
            sprintf('assets/images/flags/%s-%s.svg', $location['country'], $location['region'])
        );
        if (empty($svgUrl)) {
            $svgUrl = Svg::url(
                sprintf('assets/images/flags/%s.svg', $location['country'])
            );
        }
        if (empty($svgUrl)) {
            return '';
        }
        $flag = glsr(Builder::class)->img([
            'alt' => $location['country'],
            'src' => $svgUrl,
        ]);
        return glsr(Builder::class)->span(
            array_filter(glsr(FlagDefaults::class)->merge([
                'class' => 'glsr-flag',
                'text' => $flag,
            ]))
        );
    }

    protected function formattedFlagCityRegion(array $location): string
    {
        $parts = array_filter([
            $this->formattedFlag($location),
            $this->formattedCityRegion($location),
        ]);
        return implode('&nbsp;', $parts);
    }

    protected function formattedFlagCountry(array $location): string
    {
        $parts = array_filter([
            $this->formattedFlag($location),
            $this->formattedCountry($location),
        ]);
        return implode('&nbsp;', $parts);
    }

    protected function value(): string
    {
        $format = glsr_get_option('reviews.location_format', 'flag', 'string');
        $method = Helper::buildMethodName('formatted', $format);
        if (!method_exists($this, $method)) {
            return '';
        }
        return call_user_func([$this, $method], $this->review->location());
    }
}
