<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;

class SettingsCopier
{
    use SwitchSiteTrait;

    protected int $sourceSiteId;

    public function __construct(int $sourceSiteId)
    {
        $this->sourceSiteId = $sourceSiteId;
    }

    public function sync(array $sanitizedSettings): void
    {
        $option = OptionManager::databaseKey();
        $sites = get_sites([
            'count' => false, // this ensures we return an array
            'fields' => 'ids',
            'network_id' => get_current_network_id(),
            'site__not_in' => [$this->sourceSiteId],
        ]);
        foreach ($sites as $remoteSiteId) {
            $originalSiteId = $this->maybeSwitchSite($remoteSiteId);
            $settings = $this->mergeWithRemote($sanitizedSettings);
            $serialized = maybe_serialize($settings);
            $result = glsr(Database::class)->update('options',
                ['option_value' => $serialized, 'autoload' => 'on'],
                ['option_name' => $option]
            );
            if ($result) {
                $this->flushCache($option, $serialized);
            }
            $this->maybeRestoreSite($originalSiteId);
        }
    }

    protected function flushCache(string $option, string $value): void
    {
        $notoptions = wp_cache_get('notoptions', 'options');
        if (is_array($notoptions) && isset($notoptions[$option])) {
            unset($notoptions[$option]);
            wp_cache_set('notoptions', $notoptions, 'options');
        }
        wp_cache_delete($option, 'options');
        $alloptions = wp_load_alloptions(true);
        $alloptions[$option] = $value;
        wp_cache_set('alloptions', $alloptions, 'options');
    }

    protected function mergeWithRemote(array $sourceSettings): array
    {
        $excludedKeys = [
            'strings',
        ];
        $settings = glsr(OptionManager::class)->all();
        if (empty($settings['settings'])) {
            return $sourceSettings;
        }
        foreach (($sourceSettings['settings'] ?? []) as $key => $values) {
            if (!in_array($key, $excludedKeys)) {
                $settings['settings'][$key] = $values;
            }
        }
        return $settings;
    }
}
