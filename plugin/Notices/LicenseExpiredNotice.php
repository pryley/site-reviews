<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class LicenseExpiredNotice extends AbstractNotice
{
    protected string $type = 'banner';

    public function render(): void
    {
        $licensing = glsr(License::class)->status(); // cached daily
        if (!$licensing['licensed']) {
            return; // unlicensed
        }
        if (!$licensing['expired']) {
            return; // no licenses are expired
        }
        if (!$this->canRender()) {
            return;
        }
        echo glsr(Builder::class)->div([
            'class' => $this->classAttr(),
            'data-notice' => get_class($this),
            'text' => glsr()->build($this->path(), $licensing),
        ]);
    }

    protected function isMonitored(): bool
    {
        return true;
    }

    protected function deferVersion(): string
    {
        return glsr()->version('minor');
    }
}
