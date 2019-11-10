<?php

namespace GeminiLabs\SiteReviews\Modules;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Upgrader
{
    /**
     * @return string
     */
    public $currentVersion;

    /**
     * @return void
     */
    public function run()
    {
        $filenames = [];
        $iterator = new DirectoryIterator(dirname(__FILE__).'/Upgrader');
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $filenames[] = $fileinfo->getFilename();
            }
        }
        natsort($filenames);
        $this->currentVersion = $this->currentVersion();
        array_walk($filenames, function ($file) {
            $className = str_replace('.php', '', $file);
            $upgradeFromVersion = str_replace(['Upgrade_', '_'], ['', '.'], $className);
            $suffix = preg_replace('/[\d.]+(.+)?/', '${1}', glsr()->version); // allow alpha/beta versions
            if (version_compare($this->currentVersion, $upgradeFromVersion.$suffix, '>=')) {
                return;
            }
            glsr('Modules\\Upgrader\\'.$className);
            glsr_log()->notice('Completed Upgrade for v'.$upgradeFromVersion.$suffix);
        });
        $this->finish();
    }

    /**
     * @return void
     */
    public function finish()
    {
        if ($this->currentVersion !== glsr()->version) {
            $this->setReviewCounts();
            $this->updateVersionFrom($this->currentVersion);
        } elseif (!glsr(OptionManager::class)->get('last_review_count', false)) {
            $this->setReviewCounts();
        }
    }

    /**
     * @return string
     */
    protected function currentVersion()
    {
        $fallback = '0.0.0';
        $majorVersions = [4, 3, 2];
        foreach ($majorVersions as $majorVersion) {
            $settings = get_option(OptionManager::databaseKey($majorVersion));
            $version = Arr::get($settings, 'version', $fallback);
            if (version_compare($version, $fallback, '>')) {
                return $version;
            }
        }
        return $fallback;
    }

    /**
     * @return void
     */
    protected function setReviewCounts()
    {
        add_action('admin_init', 'glsr_calculate_ratings');
    }

    /**
     * @param string $previousVersion
     * @return void
     */
    protected function updateVersionFrom($previousVersion)
    {
        glsr(OptionManager::class)->set('version', glsr()->version);
        glsr(OptionManager::class)->set('version_upgraded_from', $previousVersion);
    }
}
