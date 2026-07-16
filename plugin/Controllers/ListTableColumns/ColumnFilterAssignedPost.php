<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class ColumnFilterAssignedPost extends AbstractColumnFilter
{
    public function label(): string
    {
        return _x('Filter by assigned post', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        return [
            '' => _x('Any assigned post', 'admin-text', 'site-reviews'),
            0 => _x('No assigned post', 'admin-text', 'site-reviews'),
        ];
    }

    public function placeholder(): string
    {
        return Arr::get($this->options(), '');
    }

    public function render(): string
    {
        return $this->filterDynamic();
    }

    public function selected(): string
    {
        $value = $this->value();
        if (is_numeric($value) && 0 === Cast::toInt($value)) {
            return Arr::get($this->options(), 0);
        }
        if (is_numeric($value) && '' !== ($title = get_the_title((int) $value))) {
            return $title;
        }
        return $this->placeholder(); // a deleted post has no title, and an empty label is a blank box
    }

    public function title(): string
    {
        return _x('Assigned Post', 'admin-text', 'site-reviews');
    }

    public function value(): string
    {
        return (string) filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
