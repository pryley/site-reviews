<?php

namespace GeminiLabs\SiteReviews\Modules\Upgrader;

use GeminiLabs\SiteReviews\Database\OptionManager;

class Upgrade_4_0_0
{
    public function __construct()
    {
        glsr(OptionManager::class)->set('settings.submissions.blacklist.integration', '');
    }
}
