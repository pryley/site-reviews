<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * Do not use OptionManager non-static methods here!
 */
class Migrate
{
    public array $migrations;
    public string $migrationsKey;
    public string $migrationsLastRun;

    public function __construct()
    {
        $this->migrations = $this->allMigrations();
        $this->migrationsKey = glsr()->prefix.'migrations';
        $this->migrationsLastRun = glsr()->prefix.'last_migration_run';
    }

    public function isMigrationNeeded(): bool
    {
        if (empty($this->migrations)) {
            return false;
        }
        if (!empty($this->pendingMigrations())) {
            // check if this is a fresh install of the plugin
            return '0.0.0' !== $this->versionUpgradedFrom();
        }
        return false;
    }

    /**
     * Returns a UNIX timestamp
     */
    public function lastRun(): int
    {
        return Cast::toInt(get_option($this->migrationsLastRun));
    }

    public function pendingMigrations(array $migrations = []): array
    {
        if (empty($migrations)) {
            $migrations = $this->migrations();
        }
        return array_keys(array_filter($migrations, fn ($hasRun) => !$hasRun));
    }

    /**
     * Used by Notices\MigrationNotice::class.
     */
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

    protected function allMigrations(): array
    {
        $migrations = [];
        $dir = glsr()->path('plugin/Migrations');
        if (is_dir($dir)) {
            $iterator = new \DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ('file' !== $fileinfo->getType()) {
                    continue;
                }
                if ('php' !== $fileinfo->getExtension()) {
                    continue;
                }
                if (!str_starts_with($fileinfo->getFilename(), 'Migrate_')) {
                    continue;
                }
                $migrations[] = str_replace('.php', '', $fileinfo->getFilename());
            }
            natsort($migrations);
        }
        return Arr::reindex($migrations);
    }

    protected function migrations(): array
    {
        $storedMigrations = Arr::consolidate(get_option($this->migrationsKey));
        if (!Arr::compare(array_keys($storedMigrations), array_values($this->migrations))) {
            $migrations = [];
            foreach ($this->migrations as $migration) {
                $migrations[$migration] = Arr::get($storedMigrations, $migration, false);
            }
            $storedMigrations = $migrations;
        }
        return array_map('wp_validate_boolean', $storedMigrations);
    }

    protected function runMigrations(): void
    {
        wp_raise_memory_limit('admin');
        $migrations = $this->migrations();
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
        $this->updateMigrationStatus($migrations);
        glsr()->action('migration/end', $migrations);
    }

    protected function updateMigrationStatus(array $migrations): void
    {
        update_option($this->migrationsKey, $migrations);
        update_option($this->migrationsLastRun, current_time('timestamp'));
    }

    protected function versionUpgradedFrom(): string
    {
        $settings = get_option(OptionManager::databaseKey());
        $version = Arr::getAs('string', $settings, 'version_upgraded_from');
        return $version ?: '0.0.0';
    }
}
