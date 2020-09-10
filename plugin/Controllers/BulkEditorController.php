<?php

namespace GeminiLabs\SiteReviews\Controllers;

class BulkEditorController extends Controller
{
    /**
     * @param string $columnName
     * @param string $postType
     * @return void
     * @action bulk_edit_custom_box
     */
    public function renderBulkEditFields($columnName, $postType)
    {
        foreach (['posts', 'users'] as $assignment) {
            if (glsr()->post_type === $postType && 'assigned_'.$assignment === $columnName) {
                glsr()->render('partials/editor/bulk-edit-assigned-'.$assignment);
            }
        }
    }
}
