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
                $hooks = "GeminiLabs\SiteReviews\Hooks\\{$fileinfo->getBasename('.php')}";
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks); // make singleton
                    glsr($hooks)->run();
                    glsr($hooks)->runDeferred();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
        $this->runIntegrations();
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
                $hooks = "GeminiLabs\SiteReviews\Integrations\\{$fileinfo->getBasename()}\Hooks";
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks);
                    add_action('plugins_loaded', function () use ($hooks) {
                        glsr($hooks)->run();
                    }, 100); // run integrations late
                    glsr($hooks)->runDeferred();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }
}
