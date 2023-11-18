<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Helper;

class Partial
{
    public function app(): PluginContract
    {
        return glsr();
    }

    public function build(string $partialPath, array $args = []): string
    {
        $className = Helper::buildClassName($partialPath, 'Modules\Html\Partials');
        $className = $this->app()->filterString('partial/classname', $className, $partialPath, $args);
        if (!class_exists($className)) {
            glsr_log()->error("Partial missing: {$className}");
            return '';
        }
        $args = $this->app()->filterArray("partial/args/{$partialPath}", $args);
        $partial = glsr($className)->build($args);
        $partial = $this->app()->filterString('rendered/partial', $partial, $partialPath, $args);
        $partial = $this->app()->filterString("rendered/partial/{$partialPath}", $partial, $args);
        return $partial;
    }

    public function render(string $partialPath, array $args = []): void
    {
        echo $this->build($partialPath, $args);
    }
}
