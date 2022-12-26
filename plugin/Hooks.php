<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    public function run(): void
    {
        $dir = glsr()->path('plugin/Hooks');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isFile()) {
                continue;
            }
            try {
                $hooks = sprintf('\GeminiLabs\SiteReviews\Hooks\%s', $fileinfo->getBasename('.php'));
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks); // make singleton
                    glsr($hooks)->run();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
        add_action('plugins_loaded', [$this, 'runIntegrations'], 100); // run after all addons have loaded
    }

    public function runIntegrations(): void
    {
        $dir = glsr()->path('plugin/Integrations');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isDir() || $fileinfo->isDot()) {
                continue;
            }
            try {
                $hooks = sprintf('\GeminiLabs\SiteReviews\Integrations\%s\Hooks', $fileinfo->getBasename());
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks);
                    glsr($hooks)->run();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }
}
