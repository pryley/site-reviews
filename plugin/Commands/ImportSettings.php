<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Upload;

class ImportSettings extends AbstractCommand
{
    use Upload;

    public function handle(): void
    {
        $this->fail();
        if (!$file = $this->getImportFile('application/json')) {
            return;
        }
        if (!$data = $this->getImportFileData($file)) {
            return;
        }
        if (!$this->import($data)) {
            return;
        }
        $this->pass();
        glsr(Notice::class)->addSuccess(
            _x('Settings imported.', 'admin-text', 'site-reviews')
        );
    }

    protected function import(array $data): bool
    {
        if (empty($data)) {
            return false;
        }
        if (isset($data['version'])) { // don't import version
            $data['version'] = glsr(OptionManager::class)->get('version');
        }
        if (isset($data['version_upgraded_from'])) { // don't import version_upgraded_from
            $data['version_upgraded_from'] = glsr(OptionManager::class)->get('version_upgraded_from');
        }
        $settings = glsr(OptionManager::class)->normalize($data);
        glsr(OptionManager::class)->replace($settings);
        glsr()->action('import/settings/extra', Arr::consolidate($data['extra'] ?? [])); // allow addons to import additional data
        glsr(Migrate::class)->runAll(); // migrate the imported settings
        return true;
    }
}
