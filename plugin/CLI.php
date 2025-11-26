<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Modules\Migrate;

class CLI
{
    public function __construct()
    {
        if (!class_exists('WP_CLI')) {
            return;
        }
        \WP_CLI::add_command(glsr()->id, static::class, [
            'shortdesc' => 'WP CLI commands for '.glsr()->name,
        ]);
    }

    /**
     * Detect your server IP address.
     *
     * @subcommand ip-address
     */
    public function ipAddress(): void
    {
        \WP_CLI::success(Helper::clientIp());
    }

    /**
     * Migrate the Site Reviews plugin.
     *
     * ## OPTIONS
     *
     * [--force]
     * : This will reset all migrations and then run them in order.
     */
    public function migrate(array $args, array $assoc): void
    {
        if (isset($assoc['force'])) {
            glsr(Tables::class)->dropForeignConstraints();
            glsr(Migrate::class)->runAll();
            \WP_CLI::success('All plugin migrations have been run.');
            return;
        }
        glsr(Migrate::class)->run();
        \WP_CLI::success('The plugin has been migrated.');
    }

    /**
     * Repair user permissions, review relationships, and assigned meta values.
     *
     * ## OPTIONS
     *
     * [--force]
     * : This will reset all Site Reviews user permissions to the defaults.
     */
    public function repair(array $args, array $assoc): void
    {
        glsr(CountManager::class)->recalculate();
        \WP_CLI::success('Assigned meta values have been repaired.');
        glsr(TableRatings::class)->removeInvalidRows();
        \WP_CLI::success('Review relationships have been repaired.');
        if (isset($assoc['force'])) {
            glsr(Role::class)->hardResetAll();
            \WP_CLI::success('User permissions have been reset.');
        } else {
            glsr(Role::class)->resetAll();
            \WP_CLI::success('User permissions have been repaired.');
        }
    }
}
