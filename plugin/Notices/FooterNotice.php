<?php

namespace GeminiLabs\SiteReviews\Notices;

class FooterNotice extends AbstractNotice
{
    protected function isInFooter(): bool
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
