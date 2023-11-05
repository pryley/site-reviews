<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class RegisterShortcodes extends AbstractCommand
{
    public function handle(): void
    {
        $dir = glsr()->path('plugin/Shortcodes');
        if (!is_dir($dir)) {
            $this->fail();
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            if ('file' !== $fileinfo->getType()) {
                continue;
            }
            $className = str_replace('.php', '', $fileinfo->getFilename());
            $shortcodeClass = Helper::buildClassName($className, 'Shortcodes');
            if (class_exists($shortcodeClass) && !(new \ReflectionClass($shortcodeClass))->isAbstract()) {
                glsr($shortcodeClass)->register();
            }
        }
    }
}
