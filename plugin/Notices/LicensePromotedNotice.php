<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class LicensePromotedNotice extends AbstractNotice
{
    protected string $type = 'banner';

    public function render(): void
    {
        $licensing = glsr(License::class)->status(); // cached daily
        if ($licensing['licensed']) {
            return;
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

    protected function canLoad(): bool
    {
        if (str_ends_with(glsr_current_screen()->base, '-premium')) {
            return false;
        }
        return parent::canLoad();
    }

    protected function isMonitored(): bool
    {
        return true;
    }

    protected function deferInterval(): int
    {
        return 3 * WEEK_IN_SECONDS;
    }

    protected function deferVersion(): string
    {
        return glsr()->version('minor');
    }
}
