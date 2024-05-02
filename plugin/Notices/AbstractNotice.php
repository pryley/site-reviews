<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Migrate;

abstract class AbstractNotice
{
    public const USER_META_KEY = '_glsr_notices';

    public $key;

    public function __construct()
    {
        $this->key = Str::dashCase(
            Str::removeSuffix((new \ReflectionClass($this))->getShortName(), 'Notice')
        );
        if (!$this->canRender()) {
            return;
        }
        if ($this->isMonitored()) {
            glsr()->append('notices', $this->key);
        }
        if ($this->isInFooter()) {
            add_action('in_admin_footer', [$this, 'render']);
        } else {
            add_action('in_admin_header', [$this, 'render']);
        }
    }

    public function dismiss(): void
    {
        if ($this->isDismissible()) {
            $userId = get_current_user_id();
            $meta = Arr::consolidate(get_user_meta($userId, static::USER_META_KEY, true));
            $meta = array_filter(wp_parse_args($meta, []));
            $meta[$this->key] = $this->version();
            update_user_meta($userId, static::USER_META_KEY, $meta);
        }
    }

    public function render(): void
    {
        $notices = glsr()->retrieveAs('array', 'notices');
        if (!$this->isIntroverted() || ($this->isIntroverted() && empty($notices))) { // @phpstan-ignore-line
            glsr()->render("partials/notices/{$this->key}", $this->data());
        }
    }

    protected function canRender(): bool
    {
        if (!$this->hasPermission()) {
            return false;
        }
        if (!$this->isNoticeScreen()) {
            return false;
        }
        if ($this->isDismissed()) {
            return false;
        }
        return true;
    }

    protected function data(): array
    {
        return [];
    }

    protected function futureTime(): int
    {
        $time = glsr(Migrate::class)->isMigrationNeeded()
            ? time() // now
            : glsr(Migrate::class)->lastRun();
        return $time + WEEK_IN_SECONDS;
    }

    protected function hasPermission(): bool
    {
        return glsr()->hasPermission('notices', $this->key);
    }

    protected function isDismissed(): bool
    {
        if (!$this->isDismissible()) {
            return false;
        }
        return !Helper::isGreaterThan($this->version(), $this->storedVersion());
    }

    protected function isDismissible(): bool
    {
        return true;
    }

    protected function isInFooter(): bool
    {
        return false;
    }

    protected function isIntroverted(): bool
    {
        return false;
    }

    protected function isMonitored(): bool
    {
        return true;
    }

    protected function isNoticeScreen(): bool
    {
        return str_starts_with(glsr_current_screen()->post_type, glsr()->post_type);
    }

    protected function storedVersion(): string
    {
        $meta = get_user_meta(get_current_user_id(), static::USER_META_KEY, true);
        return Arr::getAs('string', $meta, $this->key, '0');
    }

    protected function version(): string
    {
        return '0';
    }
}
