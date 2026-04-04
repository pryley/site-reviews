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
        $path = "assets/images/platforms/{$review->type}.svg";
        if (!Svg::exists($path)) {
            $path = 'assets/images/platforms/generic.svg';
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
