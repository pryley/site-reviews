<?php

namespace GeminiLabs\SiteReviews;

trait HookProxy
{
    /**
     * Proxy for WordPress defined filter/action callbacks.
     *
     * Since we cannot ensure third-party code will pass the correct data declared
     * by WordPress, this function allows us to maintain parameter type hints and
     * prevents fatal errors without introducing complexity. If something goes wrong,
     * the error is logged to the Site Reviews console.
     *
     * The catch is SKIPPED when PHPUNIT_TESTING is defined, and it is worth saying why,
     * because swallowing a throwable is exactly what makes a broken test pass:
     *
     *   - a test whose subject throws inside a proxied hook would go green, with nothing
     *     to show for it but a line in the console that nobody is reading;
     *   - the test suite intercepts wp_die() and wp_redirect() by THROWING from a filter
     *     (tests/pest/Support/InteractsWithExits.php), and a catch here would swallow
     *     those too, so a redirect fired through a hook could not be asserted at all.
     *
     * The price is that hook-fired code behaves differently under test than it does on a
     * live site, where a third party's bad data is logged rather than fatal. That is the
     * smaller of the two evils: a suite that passes when the code is broken is worth
     * nothing.
     */
    public function proxy(string $method): callable
    {
        $reflection = new \ReflectionMethod($this, $method);
        if (!$reflection->isPublic()) {
            throw new \BadMethodCallException("Method [{$method}] is either not public or does not exist.");
        }
        $callback = [$this, $method];
        return static function (...$args) use ($callback, $method) {
            if (defined('PHPUNIT_TESTING')) {
                return call_user_func_array($callback, $args);
            }
            try {
                return call_user_func_array($callback, $args);
            } catch (\Throwable $error) {
                glsr_log()->error($error->getMessage())->debug($error);
            }
            if (str_starts_with($method, 'filter')) {
                // A throwable error was caught so just return the unfiltered first argument.
                return array_shift($args);
            }
        };
    }
}
