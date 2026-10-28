<?php

namespace GeminiLabs\SiteReviews\Modules\Diagnostics;

use GeminiLabs\SiteReviews\Helpers\Cast;

class Backtrace
{
    /**
     * @param array<int, array<string, mixed>> $backtrace debug_backtrace() frames, innermost first
     */
    public function buildLine(array $backtrace): string
    {
        $caller = $this->caller($backtrace);
        if (empty($caller)) {
            return '';
        }
        return sprintf('%s:%s', $caller['class'] ?: $caller['file'], $caller['line']);
    }

    /**
     * Find the frame of the logged call.
     *
     * @param array<int, array<string, mixed>> $frames debug_backtrace() frames, innermost first
     *
     * @return array{class: string, file: string, line: int}|array{}
     */
    public function caller(array $frames): array
    {
        $frames = array_values($frames);
        foreach ($frames as $index => $frame) {
            $file = Cast::toString($frame['file'] ?? '');
            if ('' === $file || $this->isPassThrough($file)) {
                continue; // a function that call_user_func_array invoked has no file
            }
            // The class of the call site is in the next frame.
            // A frame's file and line say where its function was called from.
            return [
                'class' => Cast::toString($frames[$index + 1]['class'] ?? ''),
                'file' => $file,
                'line' => Cast::toInt($frame['line'] ?? 0),
            ];
        }
        return [];
    }

    public function line(int $limit = 10): string
    {
        return $this->buildLine($this->trace($limit));
    }

    /**
     * @param mixed            $data
     * @param \Throwable|mixed $data
     */
    public function lineFromData($data): string
    {
        $backtrace = ((interface_exists('Throwable') && $data instanceof \Throwable) || $data instanceof \Exception)
            ? $data->getTrace()
            : debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 1);
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
            \WP_CONTENT_DIR,
            \ABSPATH,
        ]);
        return str_replace('/', '\\', str_replace($search, '', $line));
    }

    public function trace(int $limit = 6): array
    {
        $trace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, $limit + 2);
        array_shift($trace); // remove the first entry
        if ('glsr_trace' === ($trace[0]['function'] ?? '')) {
            array_shift($trace); // remove the second entry
        } else {
            array_pop($trace); // remove the last entry
        }
        return $trace;
    }

    /**
     * Tell whether the file only passes a log call on.
     */
    protected function isPassThrough(string $file): bool
    {
        $files = [
            __FILE__,
            dirname(__DIR__).'/Console.php',
            dirname(__DIR__, 2).'/BlackHole.php',
            dirname(__DIR__, 2).'/HookProxy.php',
            dirname(__DIR__, 3).'/helpers.php',
        ];
        return in_array($file, $files, true);
    }
}
