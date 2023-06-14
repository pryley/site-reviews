<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Request;

class MigratePlugin implements Contract
{
    public $runAll;

    public function __construct(Request $request)
    {
        $this->runAll = wp_validate_boolean($request->alt);
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to migrate the plugin.', 'admin-text', 'site-reviews'),
            );
            return false;
        }
        glsr(Queue::class)->cancelAll('queue/migration');
        if ($this->runAll) {
            glsr(Tables::class)->dropForeignConstraints();
            glsr(Migrate::class)->runAll();
            glsr(Notice::class)->clear()->addSuccess(sprintf(
                _x('All plugin migrations have been run successfully, please %s the page.', 'admin-text', 'site-reviews'),
                sprintf('<a href="javascript:location.reload()">%s</a>', _x('reload', '(admin-text) e.g. please reload the page', 'site-reviews'))
            ));
        } else {
            glsr(Migrate::class)->run();
            glsr(Notice::class)->clear()->addSuccess(sprintf(
                _x('The plugin has been migrated sucessfully, please %s the page.', 'admin-text', 'site-reviews'),
                sprintf('<a href="javascript:location.reload()">%s</a>', _x('reload', '(admin-text) e.g. please reload the page', 'site-reviews'))
            ));
        }
        return true;
    }
}
