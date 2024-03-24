<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Backtrace
{
    public function buildLine(array $backtrace): string
    {
        return sprintf('%s:%s', $this->getClassName($backtrace), $this->getLineNumber($backtrace));
    }

    public function line(int $limit = 10): string
    {
        return $this->buildLine(array_slice($this->trace($limit), 4));
    }

    /**
     * @param mixed            $data
     * @param \Throwable|mixed $data
     */
    public function lineFromData($data): string
    {
        $backtrace = ((interface_exists('Throwable') && $data instanceof \Throwable) || $data instanceof \Exception)
            ? $data->getTrace()
            : debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        return $this->buildLine($backtrace);
    }

    public function normalizeLine(string $line): string
    {
        $search = array_unique([
            'GeminiLabs\\SiteReviews\\',
            glsr()->path('plugin/'),
            glsr()->path('plugin/', false),
            trailingslashit(glsr()->path()),
            trailingslashit(glsr()->path('', false)),
            WP_CONTENT_DIR,
            ABSPATH,
        ]);
        return str_replace('/', '\\', str_replace($search, '', $line));
    }

    public function trace(int $limit = 6): array
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }

    protected function getClassName(array $backtrace): string
    {
        $file = Arr::get($backtrace, '0.file');
        $class = Arr::get($backtrace, '1.class');
        $search = Arr::searchByKey('glsr_log', $backtrace, 'function');
        if (false !== $search) {
            $class = Arr::get($search, 'class', Arr::get($search, 'file'));
        } elseif (str_ends_with($file, 'helpers.php')) {
            $file = Arr::get($backtrace, '1.file');
            $class = Arr::get($backtrace, '2.class');
        } elseif (str_ends_with($file, 'BlackHole.php') && 'WP_Hook' !== Arr::get($backtrace, '2.class')) {
            $class = Arr::get($backtrace, '2.class');
        }
        return Helper::ifEmpty($class, $file);
    }

    protected function getLineNumber(array $backtrace): string
    {
        $search = Arr::searchByKey('glsr_log', $backtrace, 'function');
        if (false !== $search) {
            return Arr::get($search, 'line');
        }
        $file = Arr::get($backtrace, '0.file');
        $line = Arr::get($backtrace, '0.line');
        if (str_ends_with($file, 'helpers.php')) {
            return Arr::get($backtrace, '1.line');
        } elseif (str_ends_with($file, 'BlackHole.php') && 'WP_Hook' !== Arr::get($backtrace, '2.class')) {
            return Arr::get($backtrace, '1.line');
        }
        return $line;
    }
}
