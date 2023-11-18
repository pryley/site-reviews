<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate
{
    public string $currentVersion;
    public array $migrations;
    public string $migrationsKey;

    public function __construct()
    {
        $this->currentVersion = $this->currentVersion();
        $this->migrations = $this->availableMigrations();
        $this->migrationsKey = glsr()->prefix.'migrations';
    }

    public function isMigrationNeeded(): bool
    {
        if (empty($this->migrations)) {
            return false;
        }
        if (!empty($this->pendingMigrations())) {
            // check if this is a fresh install of the plugin
            return '0.0.0' !== glsr(OptionManager::class)->get('version_upgraded_from');
        }
        return false;
    }

    public function pendingVersions(): string
    {
        $versions = array_map(
            fn ($migration) => str_replace(['Migrate_', '_'], ['', '.'], $migration),
            $this->pendingMigrations()
        );
        return implode(', ', $versions);
    }

    public function reset(): void
    {
        delete_option($this->migrationsKey);
    }

    public function run(): void
    {
        if (glsr(Database::class)->isMigrationNeeded()) {
            $this->runAll();
        } else {
            $this->runMigrations();
        }
    }

    public function runAll(): void
    {
        $this->reset();
        $this->runMigrations();
    }

    protected function availableMigrations(): array
    {
        $migrations = [];
        $dir = glsr()->path('plugin/Migrations');
        if (is_dir($dir)) {
            $iterator = new \DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ('file' === $fileinfo->getType()) {
                    $migrations[] = str_replace('.php', '', $fileinfo->getFilename());
                }
            }
            natsort($migrations);
        }
        return Arr::reindex($migrations);
    }

    protected function createMigrations(): array
    {
        $migrations = [];
        foreach ($this->migrations as $migration) {
            $migrations[$migration] = false;
        }
        return $migrations;
    }

    protected function currentVersion(): string
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

    protected function pendingMigrations(array $migrations = []): array
    {
        if (empty($migrations)) {
            $migrations = $this->storedMigrations();
        }
        return array_keys(array_filter($migrations, fn ($hasRun) => !$hasRun));
    }

    protected function runMigrations(): void
    {
        wp_raise_memory_limit('admin');
        $migrations = $this->storedMigrations();
        glsr()->action('migration/start', $migrations);
        foreach ($this->pendingMigrations($migrations) as $migration) {
            if (class_exists($classname = "\GeminiLabs\SiteReviews\Migrations\\{$migration}")) {
                $instance = glsr($classname);
                if (!$instance instanceof MigrateContract) {
                    glsr_log()->debug("[$migration] was skipped");
                    continue;
                }
                if ($instance->run()) {
                    $migrations[$migration] = true;
                    glsr_log()->debug("[$migration] has run successfully");
                    continue;
                }
                glsr_log()->error("[$migration] was unsuccessful");
            }
        }
        $this->storeMigrations($migrations);
        if ($this->currentVersion !== glsr()->version) {
            $this->updateVersionFrom($this->currentVersion);
        }
        glsr(OptionManager::class)->set('last_migration_run', current_time('timestamp'));
        glsr()->action('migration/end', $migrations);
    }

    protected function storeMigrations(array $migrations): void
    {
        update_option($this->migrationsKey, $migrations);
    }

    protected function storedMigrations(): array
    {
        $migrations = Arr::consolidate(get_option($this->migrationsKey));
        if (!Arr::compare(array_keys($migrations), array_values($this->migrations))) {
            $newMigrations = $this->createMigrations();
            foreach ($newMigrations as $migration => &$hasRun) {
                $hasRun = Arr::get($migrations, $migration, false);
            }
            $migrations = $newMigrations;
            $this->storeMigrations($migrations);
        }
        return array_map('wp_validate_boolean', $migrations);
    }

    protected function updateVersionFrom(string $previousVersion): void
    {
        $storedPreviousVersion = glsr(OptionManager::class)->get('version_upgraded_from');
        glsr(OptionManager::class)->set('version', glsr()->version);
        if ('0.0.0' !== $previousVersion || empty($storedPreviousVersion)) {
            glsr(OptionManager::class)->set('version_upgraded_from', $previousVersion);
        }
    }
}
