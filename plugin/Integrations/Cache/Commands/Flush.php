<?php

namespace GeminiLabs\SiteReviews\Integrations\Cache\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\Cache\Plugins\AbstractPlugin;

class Flush extends AbstractCommand
{
    protected string $message;
    protected ?array $postIds;

    public function __construct(string $message, ?array $postIds = null)
    {
        $this->message = $message;
        $this->postIds = $postIds;
    }

    public function handle(): void
    {
        if ([] === $this->postIds && !glsr()->filterBool('cache/flush_when_empty_assigned_posts', true)) {
            return;
        }
        $postIds = Arr::consolidate($this->postIds);
        foreach ($this->plugins() as $plugin) {
            $plugin->purge($postIds);
        }
        $this->log($postIds);
    }

    protected function log(array $postIds): void
    {
        $message = trim($this->message) ?: 'flushed';
        if (!str_starts_with($message, 'flushed')) {
            $message = Str::prefix($message, 'flushed_after_');
        }
        $flushed = empty($postIds) ? 'all' : implode(', ', $postIds);
        glsr_log()->debug("cache::{$message} [{$flushed}]");
    }

    protected function plugin(string $slug): ?AbstractPlugin
    {
        $className = Helper::buildClassName($slug, 'Integrations\Cache\Plugins');
        if (!class_exists($className)) {
            return null;
        }
        return glsr($className);
    }

    /**
     * @return AbstractPlugin[]
     */
    protected function plugins(): array
    {
        $integrations = glsr()->filterArray('cache/integrations', [
            'cloudflare' => true,
            'endurance' => true,
            'flying_press' => true,
            'hummingbird' => true,
            'litespeed' => true,
            'nitropack' => true,
            'siteground' => true,
            'swift_performance' => true,
            'w3_total_cache' => true,
            'wp_fastest_cache' => true,
            'wp_optimize' => true,
            'wp_rocket' => true,
            'wp_super_cache' => true,
        ]);
        $plugins = [];
        foreach ($integrations as $slug => $enabled) {
            if (!wp_validate_boolean($enabled)) {
                continue;
            }
            $plugin = $this->plugin($slug);
            if (null === $plugin || !$plugin->isActive()) {
                continue;
            }
            $plugins[] = $plugin;
        }
        return $plugins;
    }
}
