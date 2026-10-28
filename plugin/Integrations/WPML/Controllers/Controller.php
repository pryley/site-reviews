<?php

namespace GeminiLabs\SiteReviews\Integrations\WPML\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Controller extends AbstractController
{
    /**
     * Add WPML to the Multilingual choices.
     *
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        if (!isset($settings['settings.general.multilingual'])) {
            return $settings;
        }
        $options = Arr::consolidate($settings['settings.general.multilingual']['options'] ?? []);
        $options['wpml'] = _x('Integrate with WPML', 'admin-text', 'site-reviews');
        $none = array_intersect_key($options, ['' => '']);
        $integrations = array_diff_key($options, $none);
        uasort($integrations, 'strnatcasecmp');
        $settings['settings.general.multilingual']['options'] = $none + $integrations;
        return $settings;
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input): array
    {
        $path = 'settings.general.multilingual';
        $isSelected = 'wpml' === Arr::get($input, $path)
            && 'wpml' !== glsr_get_option('general.multilingual');
        if ($isSelected && !$this->gatekeeper()->allowsEnabling()) { // this renders any error notices
            $settings = Arr::set($settings, $path, ''); // remove the selection
        }
        return $settings;
    }

    protected function gatekeeper(): Gatekeeper
    {
        $plugin = defined('WPML_PLUGIN_BASENAME') ? (string) \WPML_PLUGIN_BASENAME : 'sitepress-multilingual-cms/sitepress.php';
        return new Gatekeeper([
            $plugin => [
                'minimum_version' => '3.3.5',
                'name' => 'WPML Multilingual CMS',
                'plugin_uri' => 'https://wpml.org/',
                'untested_version' => '6.0',
            ],
        ]);
    }
}
