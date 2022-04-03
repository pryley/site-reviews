<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class ColumnFilterAssignedUser extends ColumnFilter
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by assigned user', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        return [
            '' => _x('Any assigned user', 'admin-text', 'site-reviews'),
            0 => _x('No assigned user', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return Arr::get($this->options(), '');
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
        $value = $this->value();
        if ($user = get_user_by('ID', $value)) {
            return $user->display_name;
        }
        if (is_numeric($value) && 0 === Cast::toInt($value)) {
            return Arr::get($this->options(), 0);
        }
        return $this->placeholder();
    }

    /**
     * @return string
     */
    public function title()
    {
        return _x('Assigned User', 'admin-text', 'site-reviews');
    }

    /**
     * @return string|int
     */
    public function value()
    {
        return filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
