<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TaxonomyController;

class TaxonomyHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(TaxonomyController::class, [
            ['filterColumns', "manage_edit-{$this->taxonomy}_columns"],
            ['filterColumnValue', "manage_{$this->taxonomy}_custom_column", 10, 3],
            ['filterDefaultHiddenColumns', 'default_hidden_columns', 10, 2],
            ['filterRowActions', "{$this->taxonomy}_row_actions", 10, 2],
            ['filterTermsClauses', 'terms_clauses', 10, 3],
            ['renderAddFields', "{$this->taxonomy}_add_form_fields"],
            ['renderEditFields', "{$this->taxonomy}_edit_form_fields"],
            ['renderQuickEditFields', 'quick_edit_custom_box', 10, 3],
            ['termPriorityDeleted', 'deleted_term_meta', 10, 3],
            ['termPriorityUpdated', "edit_{$this->taxonomy}", 10, 3],
        ]);
    }
}
