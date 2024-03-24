<?php

namespace GeminiLabs\SiteReviews\Integrations\Flywheel;

use GeminiLabs\SiteReviews\Controllers\AbstractController;

class Controller extends AbstractController
{
    /**
     * Adds a warning notice to the Flywheel Migrations page.
     *
     * @action toplevel_page_flywheel
     *
     * @see https://wordpress.org/plugins/flywheel-migrations/
     */
    public function renderNotice(): void
    {
        global $pagenow;
        if ('admin.php' === $pagenow) {
            glsr()->render('integrations/flywheel/notice');
        }
    }
}
