<?php

namespace GeminiLabs\SiteReviews\Notices;

class WriteReviewNotice extends AbstractNotice
{
    protected function canRender(): bool
    {
        if (!parent::canRender()) {
            return false;
        }
        if ($this->futureTime() >= time()) {
            return false;
        }
        return true;
    }

    protected function isIsolated(): bool
    {
        return true;
    }

    protected function isMonitored(): bool
    {
        return false;
    }

    protected function version(): string
    {
        return glsr()->version('major');
    }
}
