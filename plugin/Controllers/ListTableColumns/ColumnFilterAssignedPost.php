<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Cast;

class ColumnFilterAssignedPost extends ColumnFilter
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by assigned post', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        return [
            '' => _x('Any assigned post', 'admin-text', 'site-reviews'),
            0 => _x('No assigned post', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return _x('Any assigned post', 'admin-text', 'site-reviews');
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->filterDynamic();
    }

    /**
     * @return string
     */
    public function selected()
    {
        $value = Cast::toInt($this->value());
        return empty($value)
            ? $this->placeholder()
            : get_the_title($value);
    }

    /**
     * @return string|int
     */
    public function value()
    {
        return filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
