<?php

namespace GeminiLabs\SiteReviews\Integrations\ProfilePress\MemberDirectory;

use ProfilePress\Core\Themes\DragDrop\MemberDirectory\DefaultTemplate as MemberDirectoryTheme;

class DefaultTemplate extends MemberDirectoryTheme
{
    public function md_standard_sort_fields(): array
    {
        $sortFields = parent::md_standard_sort_fields();
        $sortFields['highest-rated'] = esc_html_x('Highest Rated First', 'admin-text', 'site-reviews');
        $sortFields['lowest-rated'] = esc_html_x('Lowest Rated First', 'admin-text', 'site-reviews');
        return $sortFields;
    }
}
