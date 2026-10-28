<?php

namespace GeminiLabs\SiteReviews\Integrations\Polylang\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Controller extends AbstractController
{
    /**
     * Add Polylang to the Multilingual choices.
     *
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        if (!isset($settings['settings.general.multilingual'])) {
            return $settings;
        }
        $options = Arr::consolidate($settings['settings.general.multilingual']['options'] ?? []);
        $options['polylang'] = _x('Integrate with Polylang', 'admin-text', 'site-reviews');
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
        $isSelected = 'polylang' === Arr::get($input, $path)
            && 'polylang' !== glsr_get_option('general.multilingual');
        if ($isSelected && !$this->gatekeeper()->allowsEnabling()) { // this renders any error notices
            $settings = Arr::set($settings, $path, ''); // remove the selection
        }
        return $settings;
    }

    protected function gatekeeper(): Gatekeeper
    {
        $plugin = defined('POLYLANG_BASENAME') ? (string) \POLYLANG_BASENAME : 'polylang/polylang.php';
        return new Gatekeeper([
            $plugin => [
                'minimum_version' => '2.3',
                'name' => 'Polylang',
                'plugin_uri' => 'https://wordpress.org/plugins/polylang/',
                'untested_version' => '4.0',
            ],
        ]);
    }
}
