<?php

namespace GeminiLabs\SiteReviews\Modules;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Database;
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
     * @var string[]
     */
    public $migrations;

    /**
     * @var string
     */
    public $transientKey;

    public function __construct()
    {
        $this->currentVersion = $this->currentVersion();
        $this->migrations = $this->migrations();
        $this->transientKey = glsr()->prefix.'migrations';
    }

    /**
     * @return bool
     */
    public function isMigrationNeeded()
    {
        if (empty($this->migrations)) {
            return false;
        }
        return !empty($this->pendingMigrations());
    }

    /**
     * @return void
     */
    public function reset()
    {
        delete_transient($this->transientKey);
    }

    /**
     * @return void
     */
    public function run()
    {
        if (glsr(Database::class)->isMigrationNeeded()) {
            $this->runAll();
        } else {
            $this->runMigrations();
        }
    }

    /**
     * @return void
     */
    public function runAll()
    {
        $this->reset();
        $this->runMigrations();
    }

    /**
     * @return array
     */
    protected function createTransient()
    {
        $transient = [];
        foreach ($this->migrations as $migration) {
            $transient[$migration] = false;
        }
        return $transient;
    }

    /**
     * @return string
     */
    protected function currentVersion()
    {
        $fallback = '0.0.0';
        $majorVersions = range(glsr()->version('major'), 1);
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
    protected function migrations()
    {
        $migrations = [];
        $dir = glsr()->path('plugin/Modules/Migrations');
        if (is_dir($dir)) {
            $iterator = new DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $migrations[] = str_replace('.php', '', $fileinfo->getFilename());
                }
            }
            natsort($migrations);
        }
        return Arr::reindex($migrations);
    }

    /**
     * @return string[]
     */
    protected function pendingMigrations(array $transient = [])
    {
        if (empty($transient)) {
            $transient = $this->transient();
        }
        return array_keys(array_filter($transient, function ($hasRun) {
            return !$hasRun;
        }));
    }

    /**
     * @return void
     */
    protected function runMigrations()
    {
        wp_raise_memory_limit('admin');
        $transient = $this->transient();
        foreach ($this->pendingMigrations($transient) as $migration) {
            if (class_exists($classname = __NAMESPACE__.'\Migrations\\'.$migration)) {
                glsr($classname)->run();
                $transient[$migration] = true;
                glsr_log()->debug("[$migration] has run successfully.");
            }
        }
        $this->storeTransient($transient);
        if ($this->currentVersion !== glsr()->version) {
            $this->updateVersionFrom($this->currentVersion);
        }
        glsr(OptionManager::class)->set('last_migration_run', current_time('timestamp'));
    }

    /**
     * @return void
     */
    protected function storeTransient(array $transient)
    {
        set_transient($this->transientKey, $transient);
    }

    /**
     * @return array
     */
    protected function transient()
    {
        $transient = Arr::consolidate(get_transient($this->transientKey));
        if (!Arr::compare(array_keys($transient), array_values($this->migrations))) {
            $newTransient = $this->createTransient();
            foreach ($newTransient as $migration => &$hasRun) {
                $hasRun = Arr::get($transient, $migration, false);
            }
            $transient = $newTransient;
            $this->storeTransient($transient);
        }
        return array_map('wp_validate_boolean', $transient);
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
