<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ValidateSettings;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;

class SettingsController extends AbstractController
{
    /**
     * @action admin_init
     */
    public function registerSettings(): void
    {
        register_setting(glsr()->id, OptionManager::databaseKey(), [
            'default' => glsr()->defaults(),
            'sanitize_callback' => [$this, 'sanitizeSettingsCallback'],
            'type' => 'array',
        ]);
    }

    /**
     * @param mixed $input
     *
     * @see registerSettings
     */
    public function sanitizeSettingsCallback($input): array
    {
        OptionManager::flushSettingsCache(); // remove settings from object cache before updating
        $input = Arr::consolidate($input);
        if (!array_key_exists('settings', $input)) {
            return $input;
        }
        if (OptionManager::isPersisting()) {
            // OptionManager::persist() is writing settings it already sanitized and split
            return $input;
        }
        $options = array_replace_recursive(glsr(OptionManager::class)->all(), [
            'settings' => $input['settings'],
        ]);
        $command = new ValidateSettings($options, $input);
        $this->execute($command);
        $options = glsr()->filterArray('settings/sanitize', $command->options, $input); // fires for every write of the option, not only saves
        if (filter_input(\INPUT_POST, 'option_page') === glsr()->id) {
            glsr(Notice::class)->addSuccess(_x('Settings updated.', 'admin-text', 'site-reviews'));
        }
        glsr(Notice::class)->store(); // store the notices before the page reloads
        glsr()->action('settings/updated', $options, $input);
        return glsr(OptionManager::class)->split($options); // splits addon settings out to their own options
    }
}
