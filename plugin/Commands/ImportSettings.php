<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Exceptions\FileException;
use GeminiLabs\SiteReviews\Exceptions\FileNotFoundException;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Upload;
use GeminiLabs\SiteReviews\UploadedFile;

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
        try {
            $file = $this->file();
        } catch (FileNotFoundException $e) {
            glsr(Notice::class)->addError($e->getMessage());
            $this->fail();
            return;
        }
        if (!$file->isValid()) {
            glsr(Notice::class)->addError($file->getErrorMessage());
            $this->fail();
            return;
        }
        if (!$file->hasMimeType('application/json')) {
            glsr(Notice::class)->addError(
                _x('The import file is not a valid JSON file.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        if (!$this->import($file)) {
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

    protected function import(UploadedFile $file): bool
    {
        try {
            $content = $file->getContent();
            $settings = json_decode($content, true);
        } catch (FileException $e) {
            glsr(Notice::class)->addError($e->getMessage());
            return false;
        }
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
