<?php

namespace GeminiLabs\SiteReviews\Modules\Diagnostics;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * Finds the origin of the code that caused a log entry.
 */
class CodeOrigin
{
    /**
     * @param array<int, array<string, mixed>> $frames debug_backtrace() frames, innermost first
     *
     * @return array{file: string, line: int}|array{}
     */
    public function fromBacktrace(array $frames): array
    {
        foreach ($frames as $frame) {
            $file = Cast::toString(Arr::get($frame, 'file'));
            if ('' === $file || $this->isOurs($file)) {
                continue; // an internal call has no file
            }
            return $this->isUserCode($file)
                ? ['file' => $file, 'line' => Cast::toInt(Arr::get($frame, 'line'))]
                : [];
        }
        return [];
    }

    /**
     * A hook holds callbacks, not calls.
     *
     * @param mixed $callback
     *
     * @return array{file: string, line: int}|array{}
     */
    public function fromCallback($callback): array
    {
        try {
            if (is_string($callback) && str_contains($callback, '::')) {
                $callback = explode('::', $callback, 2);
            }
            $reflection = is_array($callback)
                ? new \ReflectionMethod($callback[0], Cast::toString($callback[1] ?? ''))
                : new \ReflectionFunction($callback);
        } catch (\Throwable $error) {
            return [];
        }
        $file = Cast::toString($reflection->getFileName());
        if ($this->isUserCode($file)) {
            return [
                'file' => $file,
                'line' => Cast::toInt($reflection->getStartLine()),
            ];
        }
        return [];
    }

    /**
     * @return array{file: string, line: int}|array{}
     */
    public function fromThrowable(\Throwable $error): array
    {
        $frames = $error->getTrace();
        array_unshift($frames, [
            'file' => $error->getFile(),
            'line' => $error->getLine(),
        ]);
        return $this->fromBacktrace($frames);
    }

    public function isUserCode(string $file): bool
    {
        if ('' === $file) {
            return false;
        }
        if (str_contains($file, "eval()'d code")) {
            return true; // Code Snippets and WPCode run a snippet with eval()
        }
        $file = wp_normalize_path($file);
        foreach ($this->userDirectories() as $directory) {
            if (str_starts_with($file, trailingslashit(wp_normalize_path($directory)))) {
                return true;
            }
        }
        return false;
    }

    protected function isOurs(string $file): bool
    {
        $file = wp_normalize_path($file);
        if (str_starts_with($file, wp_normalize_path(glsr()->path()))) {
            return true;
        }
        foreach (array_keys(glsr()->retrieveAs('array', 'addons')) as $addonId) {
            $addon = glsr()->addon(Cast::toString($addonId));
            $path = is_object($addon) && method_exists($addon, 'path') ? Cast::toString($addon->path()) : '';
            if ('' !== $path && str_starts_with($file, wp_normalize_path($path))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string[]
     */
    protected function userDirectories(): array
    {
        return array_unique(array_filter([
            get_stylesheet_directory(),
            get_template_directory(),
            \WPMU_PLUGIN_DIR,
            \WP_CONTENT_DIR.'/code-snippets', // Code Snippets, when it stores snippets as files
            defined('FLUENT_SNIPPETS_STORAGE_DIR') && \FLUENT_SNIPPETS_STORAGE_DIR
                ? Cast::toString(\FLUENT_SNIPPETS_STORAGE_DIR)
                : \WP_CONTENT_DIR.'/fluent-snippet-storage',
        ]));
    }
}
