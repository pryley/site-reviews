<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Svg;

class WriteReviewNotice extends AbstractNotice
{
    protected int $priority = 50;
    protected string $type = 'popup';

    protected function canRender(): bool
    {
        $dismissed = Arr::consolidate(get_user_meta(get_current_user_id(), static::USER_META_KEY, true));
        if (empty($dismissed[$this->key])) {
            // Delay the popup for a week if it hasn't been dismissed before.
            // The deferInterval is 2 weeks so we will subtract a week from the timestamp
            // so that it is triggered again in a week.
            $this->dismiss([
                'timestamp' => current_time('timestamp') - WEEK_IN_SECONDS,
                'version' => '',
            ]);
            return false;
        }
        return parent::canRender();
    }

    protected function data(): array
    {
        return [
            'icon' => glsr(Svg::class)->get('assets/images/menu-icon.svg', [
                'fill' => '#fff',
                'width' => 26,
            ]),
        ];
    }

    protected function deferInterval(): int
    {
        return 2 * WEEK_IN_SECONDS;
    }

    protected function deferVersion(): string
    {
        return glsr()->version('major');
    }

    protected function isDismissible(): bool
    {
        return false;
    }

    protected function isMonitored(): bool
    {
        return true;
    }
}
