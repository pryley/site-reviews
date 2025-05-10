<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress\MemberDirectory;

use ProfilePress\Core\Themes\DragDrop\MemberDirectory\Gerbera as MemberDirectoryTheme;

class Gerbera extends MemberDirectoryTheme
{
    public function md_standard_sort_fields(): array
    {
        $sortFields = parent::md_standard_sort_fields();
        $sortFields['highest_rated'] = esc_html_x('Highest Rated First', 'admin-text', 'site-reviews');
        $sortFields['lowest_rated'] = esc_html_x('Lowest Rated First', 'admin-text', 'site-reviews');
        return $sortFields;
    }
}
