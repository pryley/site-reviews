<?php

namespace GeminiLabs\SiteReviews\Modules;

use BadMethodCallException;
use DateTime;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;
use Throwable;

/**
 * @method static debug($message, $context = [])
 * @method static info($message, $context = [])
 * @method static notice($message, $context = [])
 * @method static warning($message, $context = [])
 * @method static error($message, $context = [])
 * @method static critical($message, $context = [])
 * @method static alert($message, $context = [])
 * @method static emergency($message, $context = [])
 */
class Console
{
    const DEBUG = 0;      // Detailed debug information
    const INFO = 1;       // Interesting events
    const NOTICE = 2;     // Normal but significant events
    const WARNING = 4;    // Exceptional occurrences that are not errors
    const ERROR = 8;      // Runtime errors that do not require immediate action
    const CRITICAL = 16;  // Critical conditions
    const ALERT = 32;     // Action must be taken immediately
    const EMERGENCY = 64; // System is unusable

    protected $file;
    protected $log;
    protected $logOnceKey = 'glsr_log_once';

    public function __construct()
    {
        $this->setLogFile();
        $this->reset();
    }

    public function __call($method, $args)
    {
        $constant = 'static::'.strtoupper($method);
        if (defined($constant)) {
            $args = Arr::prepend($args, constant($constant));
            return call_user_func_array([$this, 'log'], array_slice($args, 0, 3));
        }
        throw new BadMethodCallException("Method [$method] does not exist.");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->log = '';
        file_put_contents($this->file, $this->log);
    }

    /**
     * @return string
     */
    public function get()
    {
        return empty($this->log)
            ? _x('Console is empty', 'admin-text', 'site-reviews')
            : $this->log;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return glsr()->filterInt('console/level', static::INFO);
    }

    /**
     * @return array
     */
    public function getLevels()
    {
        $constants = (new ReflectionClass(__CLASS__))->getConstants();
        return array_map('strtolower', array_flip($constants));
    }

    /**
     * @return string
     */
    public function humanLevel()
    {
        $level = $this->getLevel();
        return sprintf('%s (%d)', strtoupper(Arr::get($this->getLevels(), $level, 'unknown')), $level);
    }

    /**
     * @param string|null $valueIfEmpty
     * @return string
     */
    public function humanSize($valueIfEmpty = null)
    {
        $bytes = $this->size();
        if (empty($bytes) && is_string($valueIfEmpty)) {
            return $valueIfEmpty;
        }
        $exponent = floor(log(max($bytes, 1), 1024));
        return round($bytes / pow(1024, $exponent), 2).' '.['bytes', 'KB', 'MB', 'GB'][$exponent];
    }

    /**
     * @param int $level
     * @param mixed $message
     * @param array $context
     * @param string $backtraceLine
     * @return static
     */
    public function log($level, $message, $context = [], $backtraceLine = '')
    {
        if (empty($backtraceLine)) {
            $backtraceLine = $this->getBacktraceLine();
        }
        if ($this->canLogEntry($level, $backtraceLine)) {
            $levelName = Arr::get($this->getLevels(), $level);
            $context = Arr::consolidate($context);
            $backtraceLine = $this->normalizeBacktraceLine($backtraceLine);
            $message = $this->interpolate($message, $context);
            $entry = $this->buildLogEntry($levelName, $message, $backtraceLine);
            file_put_contents($this->file, $entry.PHP_EOL, FILE_APPEND | LOCK_EX);
            apply_filters('console', $message, $levelName, $backtraceLine); // Show in Blackbar plugin if installed
            $this->reset();
        }
        return $this;
    }

    /**
     * @return void
     */
    public function logOnce()
    {
        $once = Arr::consolidate(glsr()->{$this->logOnceKey});
        $levels = $this->getLevels();
        foreach ($once as $entry) {
            $levelName = Arr::get($entry, 'level');
            if (!in_array($levelName, $levels)) {
                continue;
            }
            $level = Arr::get(array_flip($levels), $levelName);
            $message = Arr::get($entry, 'message');
            $backtraceLine = Arr::get($entry, 'backtrace');
            $this->log($level, $message, [], $backtraceLine);
        }
        glsr()->{$this->logOnceKey} = [];
    }

    /**
     * @param string $levelName
     * @param string $handle
     * @param mixed $data
     * @return void
     */
    public function once($levelName, $handle, $data)
    {
        $once = Arr::consolidate(glsr()->{$this->logOnceKey});
        $filtered = array_filter($once, function ($entry) use ($levelName, $handle) {
            return Arr::get($entry, 'level') == $levelName
                && Arr::get($entry, 'handle') == $handle;
        });
        if (!empty($filtered)) {
            return;
        }
        $once[] = [
            'backtrace' => $this->getBacktraceLineFromData($data),
            'handle' => $handle,
            'level' => $levelName,
            'message' => '[RECURRING] '.$this->getMessageFromData($data),
        ];
        glsr()->{$this->logOnceKey} = $once;
    }

    /**
     * @return int
     */
    public function size()
    {
        return file_exists($this->file)
            ? filesize($this->file)
            : 0;
    }

    /**
     * @param array $backtrace
     * @param int $index
     * @return string
     */
    protected function buildBacktraceLine($backtrace, $index)
    {
        return sprintf('%s:%s',
            Arr::get($backtrace, $index.'.file'), // realpath
            Arr::get($backtrace, $index.'.line')
        );
    }

    /**
     * @param string $levelName
     * @param mixed $message
     * @param string $backtraceLine
     * @return string
     */
    protected function buildLogEntry($levelName, $message, $backtraceLine = '')
    {
        return sprintf('[%s] %s [%s] %s',
            current_time('mysql'),
            strtoupper($levelName),
            $backtraceLine,
            $message
        );
    }

    /**
     * @param int $level
     * @return bool
     */
    protected function canLogEntry($level, $backtraceLine)
    {
        $levelExists = array_key_exists($level, $this->getLevels());
        if (!Str::contains($backtraceLine, glsr()->path())) {
            return $levelExists; // ignore level restriction if triggered outside of the plugin
        }
        return $levelExists && $level >= $this->getLevel();
    }

    /**
     * @return void|string
     */
    protected function getBacktraceLine()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);
        $search = array_search('glsr_log', wp_list_pluck($backtrace, 'function'));
        if (false !== $search) {
            return $this->buildBacktraceLine($backtrace, (int) $search);
        }
        $search = array_search('log', wp_list_pluck($backtrace, 'function'));
        if (false !== $search) {
            $index = '{closure}' == Arr::get($backtrace, ($search + 2).'.function')
                ? $search + 4
                : $search + 1;
            return $this->buildBacktraceLine($backtrace, $index);
        }
        return 'Unknown';
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function getBacktraceLineFromData($data)
    {
        $backtrace = $data instanceof Throwable
            ? $data->getTrace()
            : debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        return $this->buildBacktraceLine($backtrace, 0);
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function getMessageFromData($data)
    {
        return $data instanceof Throwable
            ? $this->normalizeThrowableMessage($data->getMessage())
            : print_r($data, 1);
    }

    /**
     * Interpolates context values into the message placeholders.
     * @param mixed $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, $context = [])
    {
        if ($this->isObjectOrArray($message) || !is_array($context)) {
            return print_r($message, true);
        }
        $replace = [];
        foreach ($context as $key => $value) {
            $replace['{'.$key.'}'] = $this->normalizeValue($value);
        }
        return strtr($message, $replace);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isObjectOrArray($value)
    {
        return is_object($value) || is_array($value);
    }

    /**
     * @param string $backtraceLine
     * @return string
     */
    protected function normalizeBacktraceLine($backtraceLine)
    {
        $search = [
            glsr()->path('plugin/'),
            glsr()->path('plugin/', false),
            trailingslashit(glsr()->path()),
            trailingslashit(glsr()->path('', false)),
            WP_CONTENT_DIR,
            ABSPATH,
        ];
        return str_replace(array_unique($search), '', $backtraceLine);
    }

    /**
     * @param string $message
     * @return string
     */
    protected function normalizeThrowableMessage($message)
    {
        $calledIn = strpos($message, ', called in');
        return false !== $calledIn
            ? substr($message, 0, $calledIn)
            : $message;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function normalizeValue($value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif ($this->isObjectOrArray($value)) {
            $value = json_encode($value);
        }
        return (string) $value;
    }

    /**
     * @return void
     */
    protected function reset()
    {
        if ($this->size() <= pow(1024, 2) / 4) {
            return;
        }
        $this->clear();
        file_put_contents(
            $this->file,
            $this->buildLogEntry(
                static::NOTICE,
                _x('Console was automatically cleared (256KB maximum size)', 'admin-text', 'site-reviews')
            )
        );
    }

    /**
     * @return void
     */
    protected function setLogFile()
    {
        if (!function_exists('wp_hash')) {
            require_once ABSPATH.WPINC.'/pluggable.php';
        }
        $uploads = wp_upload_dir();
        $base = trailingslashit($uploads['basedir'].'/'.glsr()->id);
        $this->file = $base.'logs/'.sanitize_file_name('console-'.wp_hash(glsr()->id).'.log');
        $files = [
            $base.'index.php' => '<?php',
            $base.'logs/.htaccess' => 'deny from all',
            $base.'logs/index.php' => '<?php',
            $this->file => '',
        ];
        foreach ($files as $file => $contents) {
            if (wp_mkdir_p(dirname($file)) && !file_exists($file)) {
                file_put_contents($file, $contents);
            }
        }
        $this->log = file_get_contents($this->file);
    }
}
