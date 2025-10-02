<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

abstract class AbstractNotice
{
    public const USER_META_KEY = '_glsr_dismissed_notices';

    protected string $key;
    protected string $type = ''; // add a supported type here to override the default type

    public function __construct()
    {
        $this->normalizeType();
        $this->key = Str::dashCase(
            Str::removeSuffix((new \ReflectionClass($this))->getShortName(), 'Notice')
        );
        if (!$this->canLoad()) {
            return;
        }
        glsr()->append('notices', [
            'class' => get_class($this),
            'monitored' => $this->isMonitored(),
            'rendered' => false,
            'type' => str_starts_with('notice', $this->type) ? 'notice' : $this->type,
        ], $this->key);
        add_action('admin_notices', [$this, 'render']);
    }

    public function app(): PluginContract
    {
        return glsr();
    }

    public function dismiss(): void
    {
        if (!$this->isMonitored()) {
            return; // only track notices that are monitored
        }
        $userId = get_current_user_id();
        $dismissed = Arr::consolidate(get_user_meta($userId, static::USER_META_KEY, true));
        $dismissed[$this->key] = [
            'timestamp' => current_time('timestamp'),
            'version' => $this->deferVersion(),
        ];
        update_user_meta($userId, static::USER_META_KEY, $dismissed);
    }

    public function path(): string
    {
        return "partials/notices/{$this->key}";
    }

    public function render(): void
    {
        if (!$this->canRender()) {
            return;
        }
        echo glsr(Builder::class)->div([
            'class' => $this->classAttr(),
            'data-notice' => get_class($this),
            'text' => $this->app()->build($this->path(), $this->data()),
        ]);
    }

    protected function canLoad(): bool
    {
        if (!$this->hasPermission()) {
            return false;
        }
        if (!$this->isNoticeScreen()) {
            return false;
        }
        if ($this->isDeferred()) {
            return false;
        }
        return true;
    }

    protected function canRender(): bool
    {
        $notices = glsr()->retrieveAs('array', 'notices');
        if ($this->isStandalone()) {
            $filtered = array_filter($notices, fn ($notice) => 'notice' === ($notice['type'] ?? ''));
            return 1 === count($filtered);
        }
        foreach (['banner', 'popup'] as $type) {
            if ($type !== $this->type) {
                continue;
            }
            $filtered = array_filter($notices, function ($notice) {
                if ($this->type !== ($notice['type'] ?? '')) {
                    return false;
                }
                return $notice['rendered'] ?? false;
            });
            if (!empty($filtered)) {
                return false;
            }
            $notices[$this->key]['rendered'] = true;
            glsr()->store('notices', $notices);
        }
        return true;
    }

    protected function classAttr(): string
    {
        $classAttr = [
            'glsr-notice',
        ];
        if ($this->isDismissible()) {
            $classAttr[] = 'is-dismissible';
        }
        if ('banner' === $this->type) {
            $classAttr[] = "glsr-notice-banner";
        } elseif ('popup' === $this->type) {
            $classAttr[] = 'notice glsr-notice-popup';
        } else {
            $classAttr[] = "notice {$this->type}";
        }
        $classAttr = implode(' ', $classAttr);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classAttr);
    }

    protected function data(): array
    {
        return [];
    }

    protected function deferInterval(): int
    {
        return 0;
    }

    protected function deferVersion(): string
    {
        return '';
    }

    protected function hasPermission(): bool
    {
        return glsr()->hasPermission('notices', $this->key);
    }

    protected function isDeferred(): bool
    {
        if (!$this->isMonitored()) {
            return false;
        }
        $dismissed = Arr::consolidate(get_user_meta(get_current_user_id(), static::USER_META_KEY, true));
        if (empty($dismissed[$this->key])) {
            return false;
        }
        $version = glsr(Sanitizer::class)->sanitizeVersion($dismissed[$this->key]['version'] ?? '');
        if (Helper::isGreaterThan($this->deferVersion(), $version)) {
            return false;
        }
        if ($deferInterval = $this->deferInterval()) {
            $timestamp = glsr(Sanitizer::class)->sanitizeTimestamp($dismissed[$this->key]['timestamp'] ?? '');
            $deferredTimestamp = (int) $timestamp + $deferInterval;
            if (current_time('timestamp') > $deferredTimestamp) {
                return false;
            }
        }
        return true;
    }

    protected function isDismissible(): bool
    {
        return true;
    }

    protected function isMonitored(): bool
    {
        return false;
    }

    protected function isNoticeScreen(): bool
    {
        return str_starts_with(glsr_current_screen()->post_type, glsr()->post_type);
    }

    protected function isStandalone(): bool
    {
        return false;
    }

    protected function normalizeType(): void
    {
        $types = [
            'banner',
            'notice',
            'notice-error',
            'notice-info',
            'notice-success',
            'notice-warning',
            'popup',
        ];
        if (!in_array($this->type, $types)) {
            $this->type = 'notice';
        }
    }
}
