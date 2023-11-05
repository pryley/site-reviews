<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class RegisterWidgets extends AbstractCommand
{
    public function handle(): void
    {
        $dir = glsr()->path('plugin/Widgets');
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
            $widgetClass = Helper::buildClassName($className, 'Widgets');
            if (class_exists($widgetClass) && !(new \ReflectionClass($widgetClass))->isAbstract()) {
                register_widget($widgetClass);
            }
        }
    }
}
