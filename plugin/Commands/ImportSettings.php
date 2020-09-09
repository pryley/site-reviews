<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Upload;

class ImportSettings extends Upload implements Contract
{
    /**
     * @return void
     */
    public function handle()
    {
        if (!$this->validateUpload() || !$this->validateExtension('.json')) {
            return;
        }
        if ($this->import()) {
            glsr(Notice::class)->addSuccess(
                _x('Settings imported.', 'admin-text', 'site-reviews')
            );
        }
    }

    /**
     * @return bool
     */
    protected function import()
    {
        if ($settings = json_decode(file_get_contents($this->file()->tmp_name), true)) { // @todo catch Exception if an invalid json file
            glsr(OptionManager::class)->set(
                glsr(OptionManager::class)->normalize($settings)
            );
            return true;
        }
        glsr(Notice::class)->addWarning(
            _x('There were no settings found to import.', 'admin-text', 'site-reviews')
        );
        return false;
    }
}
