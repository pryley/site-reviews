<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;

class Partial
{
    /**
     * @param string $partialPath
     * @return string|void
     */
    public function build($partialPath, array $args = [])
    {
        $className = Helper::buildClassName($partialPath, 'Modules\Html\Partials');
        $className = glsr()->filterString('partial/classname', $className, $partialPath);
        if (!class_exists($className)) {
            glsr_log()->error('Partial missing: '.$className);
            return;
        }
        $args = glsr()->filterArray('partial/args/'.$partialPath, $args);
        $partial = glsr($className)->build($args);
        $partial = glsr()->filterString('rendered/partial', $partial, $partialPath, $args);
        $partial = glsr()->filterString('rendered/partial/'.$partialPath, $partial, $args);
        return $partial;
    }

    /**
     * @param string $partialPath
     * @return void
     */
    public function render($partialPath, array $args = [])
    {
        echo $this->build($partialPath, $args);
    }
}
