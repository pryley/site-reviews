<?php

namespace GeminiLabs\SiteReviews\Addons;

class UpdateNotice
{
    public string $status;

    protected string $pluginUrl;
    protected string $renewalUrl;

    public function __construct(string $status = '', string $renewalUrl = '', string $pluginUrl = '')
    {
        $this->pluginUrl = $pluginUrl ?: Updater::DEFAULT_API_URL;
        $this->renewalUrl = $renewalUrl;
        $this->status = $status;
    }

    /**
     * For the plugin row on the Plugins screen.
     */
    public function html(): string
    {
        $entry = $this->entry();
        $link = sprintf('<a href="%s">%s</a>', esc_url($entry['url']), esc_html($entry['link']));
        return wp_kses_post(sprintf($entry['template'], $link));
    }

    /**
     * For the upgrade notice and the failed-update email.
     */
    public function text(): string
    {
        $entry = $this->entry();
        return sprintf($entry['template'], $entry['link']);
    }

    /**
     * Where to go to fix the license error.
     */
    public function url(): string
    {
        return $this->entry()['url'];
    }

    /**
     * @return array{link: string, template: string, url: string}
     */
    protected function entry(): array
    {
        $map = $this->map();
        return $map[$this->status] ?? $map[''];
    }

    protected function map(): array
    {
        $settingsUrl = glsr_admin_url('settings', 'licenses');
        $map = [
            '' => [
                'link' => _x('license key', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "license key" */
                'template' => _x('A valid %s is required to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => $this->pluginUrl,
            ],
            'disabled' => [
                'link' => _x('contact support', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "contact support" */
                'template' => _x('Your license has been disabled. Please %s to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => glsr_premium_url('support'),
            ],
            'expired' => [
                'link' => _x('Renew it', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "Renew it" */
                'template' => _x('Your license has expired. %s to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => $this->renewalUrl ?: glsr_premium_url('license-keys'),
            ],
            'invalid' => [
                'link' => _x('Check it', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "Check it" */
                'template' => _x('Your license key was not recognized. %s to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => $settingsUrl,
            ],
            'invalid_item_id' => [
                'link' => _x('Check it', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "Check it" */
                'template' => _x('Your license key is for a different plugin. %s to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => $settingsUrl,
            ],
            'missing' => [
                'link' => _x('license key', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "license key" */
                'template' => _x('Enter your %s to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => $settingsUrl,
            ],
            'site_inactive' => [
                'link' => _x('Activate it', 'admin-text', 'site-reviews'),
                /* translators: %s: link with the text "Activate it" */
                'template' => _x('Your license is not activated for this site. %s to update this plugin.', 'admin-text', 'site-reviews'),
                'url' => $settingsUrl,
            ],
        ];
        $map['inactive'] = $map['site_inactive'];
        $map['item_name_mismatch'] = $map['invalid_item_id'];
        return $map;
    }
}
