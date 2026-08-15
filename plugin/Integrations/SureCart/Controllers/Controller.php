<?php

namespace GeminiLabs\SiteReviews\Integrations\SureCart\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/settings
     */
    public function filterSettings(array $settings): array
    {
        return array_merge(glsr()->config('integrations/surecart'), $settings);
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsCallback(array $settings, array $input): array
    {
        $key = 'settings.integrations.surecart';
        $surecart = Arr::get($input, $key);
        $isEnabling = Arr::getAs('bool', $surecart, 'enabled')
            && !glsr_get_option("{$key}.enabled", false, 'bool'); // only a fresh enable attempt is gated; an enabled integration is never disabled here
        if ($isEnabling && !$this->gatekeeper()->allowsEnabling()) { // this renders any error notices
            $settings = Arr::set($settings, "{$key}.enabled", 'no');
        }
        return $settings;
    }

    /**
     * @filter site-reviews/integration/subsubsub
     */
    public function filterSubsubsub(array $subsubsub): array
    {
        $subsubsub['surecart'] = 'SureCart';
        return $subsubsub;
    }

    /**
     * @action admin_init
     */
    public function renderNotice(): void
    {
        if (glsr_get_option('integrations.surecart.enabled', false, 'bool')) {
            $this->gatekeeper()->allows(); // this renders any error notices
        }
    }

    /**
     * @action site-reviews/settings/surecart
     */
    public function renderSettings(string $rows): void
    {
        glsr(Template::class)->render('integrations/surecart/settings', [
            'context' => [
                'rows' => $rows,
            ],
        ]);
    }

    protected function gatekeeper(): Gatekeeper
    {
        return new Gatekeeper([
            'surecart/surecart.php' => [
                'minimum_version' => '3.7',
                'name' => 'SureCart',
                'plugin_uri' => 'https://wordpress.org/plugins/surecart/',
                'untested_version' => '4.0',
            ],
        ]);
    }
}
