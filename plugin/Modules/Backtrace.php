<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helpers\Arr;
use Throwable;

class Backtrace
{
    /**
     * @param array $backtrace
     * @param int $index
     * @return string
     */
    public function buildLine($backtrace, $index)
    {
        return sprintf('%s:%s',
            Arr::get($backtrace, $index.'.file'), // realpath
            Arr::get($backtrace, $index.'.line')
        );
    }

    /**
     * @return void|string
     */
    public function line($limit = 6)
    {
        $backtrace = $this->trace($limit);
        $search = array_search('glsr_log', wp_list_pluck($backtrace, 'function'));
        if (false !== $search) {
            return $this->buildLine($backtrace, (int) $search);
        }
        $search = array_search('log', wp_list_pluck($backtrace, 'function'));
        if (false !== $search) {
            $index = '{closure}' == Arr::get($backtrace, ($search + 2).'.function')
                ? $search + 4
                : $search + 1;
            return $this->buildLine($backtrace, $index);
        }
        return 'Unknown';
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function lineFromData($data)
    {
        $backtrace = $data instanceof Throwable
            ? $data->getTrace()
            : debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        return $this->buildLine($backtrace, 0);
    }

    /**
     * @param string $line
     * @return string
     */
    public function normalizeLine($line)
    {
        $search = [
            glsr()->path('plugin/'),
            glsr()->path('plugin/', false),
            trailingslashit(glsr()->path()),
            trailingslashit(glsr()->path('', false)),
            WP_CONTENT_DIR,
            ABSPATH,
        ];
        return str_replace(array_unique($search), '', $line);
    }

    /**
     * @return array
     */
    public function trace($limit = 6)
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }
}
