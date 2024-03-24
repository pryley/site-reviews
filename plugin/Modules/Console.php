<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

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
    public const DEBUG = 0;      // Detailed debug information
    public const INFO = 1;       // Interesting events
    public const NOTICE = 2;     // Normal but significant events
    public const WARNING = 4;    // Exceptional occurrences that are not errors
    public const ERROR = 8;      // Runtime errors that do not require immediate action
    public const CRITICAL = 16;  // Critical conditions
    public const ALERT = 32;     // Action must be taken immediately
    public const EMERGENCY = 64; // System is unusable

    public const LOG_LEVEL_KEY = 'glsr_console_level';
    public const LOG_ONCE_KEY = 'glsr_log_once';

    protected $file;
    protected $log;

    public function __construct()
    {
        $this->setLogFile();
        $this->reset();
    }

    public function __call(string $method, $args)
    {
        $constant = strtoupper($method);
        $instance = new \ReflectionClass($this);
        if ($instance->hasConstant($constant)) {
            $args = Arr::prepend($args, $instance->getConstant($constant));
            return call_user_func_array([$this, 'log'], array_slice($args, 0, 3));
        }
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    public function __toString(): string
    {
        return $this->get();
    }

    public function clear(): void
    {
        $this->log = '';
        file_put_contents($this->file, $this->log);
    }

    public function get(): string
    {
        return esc_html(
            Helper::ifEmpty($this->log, _x('Console is empty', 'admin-text', 'site-reviews'))
        );
    }

    public function getRaw(): string
    {
        return htmlspecialchars_decode($this->get(), ENT_QUOTES);
    }

    public function getLevel(): int
    {
        $level = Cast::toInt(get_option(static::LOG_LEVEL_KEY, static::INFO));
        $levels = [
            static::ALERT, static::CRITICAL, static::DEBUG, static::EMERGENCY,
            static::ERROR, static::INFO, static::NOTICE, static::WARNING,
        ];
        if (in_array($level, $levels)) {
            return $level;
        }
        return static::INFO;
    }

    public function getLevels(): array
    {
        $constants = (new \ReflectionClass(__CLASS__))->getConstants();
        return array_map('strtolower', array_flip($constants));
    }

    public function humanLevel(): string
    {
        $level = $this->getLevel();
        return sprintf('%s (%d)', strtoupper(Arr::get($this->getLevels(), $level, 'unknown')), $level);
    }

    public function humanSize(): string
    {
        return Str::replaceLast(' B', ' bytes', Cast::toString(size_format($this->size())));
    }

    /**
     * @param mixed $message
     *
     * @return static
     */
    public function log(int $level, $message, array $context = [], string $backtraceLine = '')
    {
        if (empty($backtraceLine)) {
            $backtraceLine = glsr(Backtrace::class)->line();
        }
        if ($this->canLogEntry($level, $backtraceLine)) {
            $levelName = Arr::get($this->getLevels(), $level);
            $backtraceLine = glsr(Backtrace::class)->normalizeLine($backtraceLine);
            $message = $this->interpolate($message, $context);
            $entry = $this->buildLogEntry($levelName, $message, $backtraceLine);
            file_put_contents($this->file, $entry.PHP_EOL, FILE_APPEND | LOCK_EX);
            apply_filters('console', $message, $levelName, $backtraceLine); // Show in Blackbar plugin if installed
            $this->reset();
        }
        return $this;
    }

    public function logOnce(): void
    {
        $once = glsr()->retrieveAs('array', static::LOG_ONCE_KEY);
        $levels = $this->getLevels();
        foreach ($once as $entry) {
            $levelName = Arr::get($entry, 'level');
            if (in_array($levelName, $levels)) {
                $level = Arr::get(array_flip($levels), $levelName);
                $message = Arr::get($entry, 'message');
                $backtraceLine = Arr::get($entry, 'backtrace');
                $this->log($level, $message, [], $backtraceLine);
            }
        }
        glsr()->store(static::LOG_ONCE_KEY, []);
    }

    /**
     * @param mixed $data
     */
    public function once(string $levelName, string $handle, $data): void
    {
        $once = glsr()->retrieveAs('array', static::LOG_ONCE_KEY);
        $filtered = array_filter($once, function ($entry) use ($levelName, $handle) {
            return Arr::get($entry, 'level') === $levelName
                && Arr::get($entry, 'handle') === $handle;
        });
        if (empty($filtered)) {
            $once[] = [
                'backtrace' => glsr(Backtrace::class)->lineFromData($data),
                'handle' => $handle,
                'level' => $levelName,
                'message' => '[RECURRING] '.$this->getMessageFromData($data),
            ];
            glsr()->store(static::LOG_ONCE_KEY, $once);
        }
    }

    public function size(): int
    {
        return file_exists($this->file)
            ? filesize($this->file)
            : 0;
    }

    protected function buildLogEntry(string $levelName, string $message, string $backtraceLine = ''): string
    {
        return sprintf('[%s] %s [%s] %s',
            current_time('mysql'),
            strtoupper($levelName),
            $backtraceLine,
            esc_html($message)
        );
    }

    protected function canLogEntry(int $level, string $backtraceLine): bool
    {
        $levelExists = array_key_exists($level, $this->getLevels());
        if (!Str::contains($backtraceLine, [glsr()->path(), 'GeminiLabs\SiteReviews'])) {
            return $levelExists; // ignore level restriction if triggered outside of the plugin
        }
        return $levelExists && $level >= $this->getLevel();
    }

    /**
     * @param mixed|\Throwable $data
     */
    protected function getMessageFromData($data): string
    {
        return ($data instanceof \Throwable)
            ? $this->normalizeThrowableMessage($data->getMessage())
            : glsr(Dump::class)->dump($data);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param mixed $message
     */
    protected function interpolate($message, array $context = []): string
    {
        $context = Arr::consolidate($context);
        if (!is_scalar($message) || empty($context)) {
            return glsr(Dump::class)->dump($message);
        }
        $replace = [];
        foreach ($context as $key => $value) {
            $replace['{'.$key.'}'] = $this->normalizeValue($value);
        }
        return strtr($message, $replace);
    }

    protected function normalizeThrowableMessage(string $message): string
    {
        $calledIn = strpos($message, ', called in');
        return false !== $calledIn
            ? substr($message, 0, $calledIn)
            : $message;
    }

    /**
     * @param mixed $value
     */
    protected function normalizeValue($value): string
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (!is_scalar($value)) {
            $value = wp_json_encode($value);
        }
        return Cast::toString($value);
    }

    protected function reset(): void
    {
        if ($this->size() <= wp_convert_hr_to_bytes('512kb')) {
            return;
        }
        $this->clear();
        file_put_contents($this->file,
            $this->buildLogEntry(static::NOTICE,
                _x('Console was automatically cleared (512KB maximum size)', 'admin-text', 'site-reviews')
            )
        );
    }

    protected function setLogFile(): void
    {
        $uploads = wp_upload_dir();
        if (!file_exists($uploads['basedir'])) {
            $uploads = wp_upload_dir(null, true, true); // maybe the site has been moved, so refresh the cached uploads path
        }
        $base = trailingslashit($uploads['basedir'].'/'.glsr()->id);
        $this->file = $base.'logs/'.sanitize_file_name('console-'.Str::hash(glsr()->id).'.log');
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
