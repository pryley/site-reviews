<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;

class DefaultsManager
{
    /**
     * @return array
     */
    public function defaults()
    {
        $settings = glsr()->settings();
        $defaults = (array) array_combine(array_keys($settings), wp_list_pluck($settings, 'default'));
        return wp_parse_args($defaults, [
            'version' => '',
            'version_upgraded_from' => '0.0.0',
        ]);
    }

    /**
     * @return array
     */
    public function get()
    {
        return Arr::convertFromDotNotation($this->defaults());
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function pluck($path)
    {
        $settings = Arr::convertFromDotNotation(glsr()->settings());
        return Arr::get($settings, $path);
    }

    /**
     * @return array
     */
    public function set()
    {
        $settings = glsr(OptionManager::class)->all();
        $currentSettings = Arr::removeEmptyValues($settings);
        $defaultSettings = array_replace_recursive($this->get(), $currentSettings);
        $updatedSettings = array_replace_recursive($settings, $defaultSettings);
        update_option(OptionManager::databaseKey(), $updatedSettings);
        glsr(OptionManager::class)->reset();
        return $defaultSettings;
    }
}
