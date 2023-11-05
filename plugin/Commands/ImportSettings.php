<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Upload;

class ImportSettings extends AbstractCommand
{
    use Upload;

    public function handle(): void
    {
        if (!glsr()->hasPermission('settings')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to import settings.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        if (!$this->validateUpload() || !$this->validateExtension('.json')) {
            glsr(Notice::class)->addWarning(
                _x('The import file is not a valid JSON file.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        if (!$this->import()) {
            glsr(Notice::class)->addWarning(
                _x('There were no settings found to import.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        glsr(Notice::class)->addSuccess(
            _x('Settings imported.', 'admin-text', 'site-reviews')
        );
    }

    protected function import(): bool
    {
        $settings = json_decode(file_get_contents($this->file()->tmp_name), true);
        if (!empty($settings)) {
            if (isset($settings['version'])) { // don't import version
                $settings['version'] = glsr(OptionManager::class)->get('version');
            }
            if (isset($settings['version_upgraded_from'])) { // don't import version_upgraded_from
                $settings['version_upgraded_from'] = glsr(OptionManager::class)->get('version_upgraded_from');
            }
            glsr(OptionManager::class)->replace(
                glsr(OptionManager::class)->normalize($settings)
            );
            glsr(Migrate::class)->runAll(); // migrate the imported settings
            return true;
        }
        return false;
    }
}
