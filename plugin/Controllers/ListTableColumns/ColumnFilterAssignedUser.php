<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class ColumnFilterAssignedUser extends AbstractColumnFilter
{
    public function label(): string
    {
        return _x('Filter by assigned user', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        return [
            '' => _x('Any assigned user', 'admin-text', 'site-reviews'),
            0 => _x('No assigned user', 'admin-text', 'site-reviews'),
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
        if ($user = get_user_by('id', $value)) {
            return glsr(Sanitizer::class)->sanitizeUserName(
                $user->display_name,
                $user->user_nicename
            );
        }
        if (is_numeric($value) && 0 === Cast::toInt($value)) {
            return Arr::get($this->options(), 0);
        }
        return $this->placeholder();
    }

    public function title(): string
    {
        return _x('Assigned User', 'admin-text', 'site-reviews');
    }

    public function value(): string
    {
        return (string) filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
