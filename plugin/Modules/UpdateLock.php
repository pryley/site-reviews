<?php

namespace GeminiLabs\SiteReviews\Modules;

class UpdateLock
{
    /**
     * Checks that the plugin folder of $basename holds a .git file or folder.
     */
    public function isLocked(string $basename): bool
    {
        return file_exists(\WP_PLUGIN_DIR.'/'.dirname($basename).'/.git');
    }

    /**
     * Returns the locked plugins as basename => name
     */
    public function locked(): array
    {
        $plugins = [glsr()->basename => glsr()->name];
        foreach (array_keys(glsr()->retrieveAs('array', 'addons', [])) as $addonId) {
            $addon = glsr($addonId);
            $plugins[$addon->basename] = $addon->name;
        }
        return array_filter($plugins, [$this, 'isLocked'], \ARRAY_FILTER_USE_KEY);
    }
}
