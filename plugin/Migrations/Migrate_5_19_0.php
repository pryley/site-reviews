<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Database\OptionManager;

class Migrate_5_19_0
{
    /**
     * @return bool
     */
    public function run()
    {
        return $this->migrateSettings();
    }

    /**
     * @return bool
     */
    protected function migrateSettings()
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
        return true;
    }
}
