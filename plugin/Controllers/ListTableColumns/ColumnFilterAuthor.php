<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Cast;

class ColumnFilterAuthor extends ColumnFilterAssignedUser
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by author', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        return [
            '' => _x('Any author', 'admin-text', 'site-reviews'),
            0 => _x('No author', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return _x('Any author', 'admin-text', 'site-reviews');
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
            return _x('No author', 'admin-text', 'site-reviews');
        }
        return $this->placeholder();
    }
}
