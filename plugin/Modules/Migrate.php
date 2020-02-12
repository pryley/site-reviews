<?php

namespace GeminiLabs\SiteReviews\Modules;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate
{
    /**
     * @var string
     */
    public $currentVersion;

    /**
     * @var string
     */
    public $transientKey;

    public function __construct()
    {
        $this->currentVersion = $this->getCurrentVersion();
        $this->transientKey = Application::PREFIX.'migrations';
    }

    /**
     * @return bool
     */
    public function isMigrationNeeded()
    {
        $transient = get_transient($this->transientKey);
        if (false === $transient || !isset($transient[glsr()->version])) {
            $transient = [
                glsr()->version => !empty($this->getNewMigrationFiles()),
            ];
            set_transient($this->transientKey, $transient);
        }
        return Helper::castToBool($transient[glsr()->version]);
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->runMigrations($this->getNewMigrationFiles());
    }

    /**
     * @return bool
     */
    public function runAll()
    {
        $this->runMigrations($this->getMigrationFiles());
    }

    /**
     * @return string
     */
    protected function getCurrentVersion()
    {
        $fallback = '0.0.0';
        $majorVersions = [4, 3, 2, 1];
        foreach ($majorVersions as $majorVersion) {
            $settings = get_option(OptionManager::databaseKey($majorVersion));
            $version = Arr::get($settings, 'version', $fallback);
            if (Helper::isGreaterThan($version, $fallback)) {
                return $version;
            }
        }
        return $fallback;
    }

    /**
     * @return array
     */
    protected function getMigrationFiles()
    {
        $files = [];
        $dir = glsr()->path('plugin/Modules/Migrations');
        if (is_dir($dir)) {
            $iterator = new DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $files[] = $fileinfo->getFilename();
                }
            }
            natsort($files);
        }
        return $files;
    }

    /**
     * @return array
     */
    protected function getNewMigrationFiles()
    {
        $files = $this->getMigrationFiles();
        foreach ($files as $index => $file) {
            $className = str_replace('.php', '', $file);
            $migrationVersion = str_replace(['Migrate_', '_'], ['', '.'], $className);
            $suffix = preg_replace('/[\d.]+(.+)?/', '${1}', glsr()->version); // allow alpha/beta versions
            if (Helper::isGreaterThanOrEqual($this->currentVersion, $migrationVersion.$suffix)) {
                unset($files[$index]);
            }
        }
        return $files;
    }

    /**
     * @return void
     */
    protected function runMigrations(array $files)
    {
        if (empty($files)) {
            return;
        }
        array_walk($files, function ($file) {
            $className = str_replace('.php', '', $file);
            glsr('Modules\\Migrations\\'.$className)->run();
            $versionMigrated = str_replace(['Migrate_', '_'], ['v','.'], $className);
            glsr_log()->debug('migration completed for '.$versionMigrated);
        });
        if ($this->currentVersion !== glsr()->version) {
            $this->updateVersionFrom($this->currentVersion);
        }
        glsr(OptionManager::class)->set('last_migration_run', current_time('timestamp'));
        delete_transient($this->transientKey);
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
