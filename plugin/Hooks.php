<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    /**
     * @return void
     */
    public function run()
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
                $hooks = '\GeminiLabs\SiteReviews\Hooks\\'.$fileinfo->getBasename('.php');
                $reflect = new \ReflectionClass($hooks);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($hooks);
                    glsr($hooks)->run();
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
        add_action('plugins_loaded', [$this, 'runIntegrations'], 100); // run after all add-ons have loaded
    }

    /**
     * @return void
     */
    public function runIntegrations()
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
            $basename = 'GeminiLabs\SiteReviews\Integrations\\'.$fileinfo->getBasename();
            $controller = $basename.'\Controller';
            $hooks = $basename.'\Hooks';
            if (class_exists($controller) && class_exists($hooks)) {
                glsr()->singleton($controller);
                glsr()->singleton($hooks);
                glsr($hooks)->run();
            }
        }
    }
}
