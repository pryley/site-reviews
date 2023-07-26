<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Role;

class RepairPermissions implements Contract
{
    public $resetAll;

    public function __construct(Request $request)
    {
        $this->resetAll = wp_validate_boolean($request->alt);
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->can('edit_users')) {
            glsr(Notice::class)->clear()->addError(
                _x('You do not have permission to repair permissions.', 'admin-text', 'site-reviews')
            );
            return false;
        }
        if ($this->resetAll) {
            glsr(Role::class)->hardResetAll();
        } else {
            glsr(Role::class)->resetAll();
        }
        glsr(Notice::class)->clear()->addSuccess(
            _x('The permissions have been repaired.', 'admin-text', 'site-reviews')
        );
        return true;
    }
}
