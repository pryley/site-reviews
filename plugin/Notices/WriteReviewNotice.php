<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Svg;

class WriteReviewNotice extends AbstractNotice
{
    protected string $type = 'popup';

    protected function canLoad(): bool
    {
        if (!parent::canLoad()) {
            return false;
        }
        if ('post' === glsr_current_screen()->base) {
            return false;
        }
        if (str_ends_with(glsr_current_screen()->base, '-premium')) {
            return false;
        }
        if (!glsr()->filterBool('flyoutmenu/enabled', true)) {
            return false;
        }
        return true;
    }

    protected function canRender(): bool
    {
        $dismissed = Arr::consolidate(get_user_meta(get_current_user_id(), static::USER_META_KEY, true));
        if (empty($dismissed[$this->key])) {
            // Delay the popup for a week if it hasn't been dismissed before.
            // The deferInterval is 2 weeks so we will subtract 1 week from that for the initial defer.
            $this->dismiss(WEEK_IN_SECONDS);
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
