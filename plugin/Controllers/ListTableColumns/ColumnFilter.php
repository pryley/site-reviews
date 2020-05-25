<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

interface ColumnFilter
{
    /**
     * @return string|void
     */
    public function handle();
}
