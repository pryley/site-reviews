<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Migrate_5_19_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateSettings();
        return true;
    }

    protected function migrateSettings(): void
    {
        $optionKeys = [
            'settings.general.require.login_register' => 'settings.general.require.register',
        ];
        foreach ($optionKeys as $oldKey => $newKey) {
            $oldValue = glsr(OptionManager::class)->get($oldKey);
            $newValue = glsr(OptionManager::class)->get($newKey);
            if ($oldValue && empty($newValue)) {
                glsr(OptionManager::class)->set($newKey, $oldValue);
            }
            glsr(OptionManager::class)->delete($oldKey);
        }
    }
}
