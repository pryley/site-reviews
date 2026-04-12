<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class ColumnValueType implements ColumnValueContract
{
    public function handle(Review $review): string
    {
        $text = $review->type();
        if ('local' === $review->type) {
            return $text;
        }
        $path = "assets/images/platforms/{$review->type}.svg";
        if (!Svg::exists($path)) {
            return $text;
        }
        $logo = Svg::get($path, [
            'alt' => $text,
            'height' => 16,
            'width' => 16,
        ]);
        return glsr(Builder::class)->span([
            'style' => 'display:inline-flex;align-items:center;gap:5px;',
            'text' => "{$logo}<span>{$text}</span>",
        ]);
    }
}
