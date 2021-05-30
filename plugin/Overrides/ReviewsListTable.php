<?php

namespace GeminiLabs\SiteReviews\Overrides;

class ReviewsListTable extends \WP_Posts_List_Table
{
    public function inline_edit()
    {
        glsr()->render('partials/screen/inline-edit', [
            'columns' => $this->get_column_count(),
            'screenId' => esc_attr($this->screen->id),
        ]);
    }
}
