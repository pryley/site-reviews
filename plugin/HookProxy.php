<?php

namespace GeminiLabs\SiteReviews;

trait HookProxy
{
    /**
     * Proxy for WordPress defined filter/action callbacks.
     *
     * Since we cannot ensure third-party code will pass the correct data declared by
     * WordPress, this function allows us to maintain parameter type hints and prevents
     * fatal errors without introducing complexity. If something goes wrong, the error is
     * logged to the Site Reviews console.
     *
     * The catch is deliberately as wide as \Throwable, and it stays that way. This plugin
     * runs alongside thirty-odd page builders on sites nobody here will ever see, and the
     * choice being made is between a feature that degrades — renders nothing, logs a line
     * to the console — and a site that goes white and gets Site Reviews blamed for it,
     * whether or not the bad data was ours. Degrading is the right default. Narrowing this
     * to TypeError would not even do what it sounds like: hook callbacks take untyped
     * params by convention and cast inside, so a third party passing junk shows up as an
     * Error from dereferencing it, not as a TypeError at all.
     *
     * The catch is SKIPPED when the rethrow filter says so, and the test suite is what
     * says so (tests/pest/bootstrap.php), because swallowing a throwable is exactly what
     * makes a broken test pass:
     *
     *   - a test whose subject throws inside a proxied hook would go green, with nothing
     *     to show for it but a line in a console that nobody is reading;
     *   - the suite intercepts wp_die() and wp_redirect() by THROWING from a filter
     *     (tests/pest/Support/InteractsWithExits.php), and a catch here swallows those
     *     too, so a redirect fired through a hook could not be asserted at all.
     *
     * It is a filter rather than a defined('PHPUNIT_TESTING') because a constant naming a
     * test framework has no business in shipped code, and because it is genuinely useful
     * to anyone debugging a site: turn it on and the error surfaces instead of hiding in
     * the console. Off by default, so no site's behaviour changes.
     *
     * @see HOOKS.md — site-reviews/hook/rethrow
     */
    public function proxy(string $method): callable
    {
        $reflection = new \ReflectionMethod($this, $method);
        if (!$reflection->isPublic()) {
            throw new \BadMethodCallException("Method [{$method}] is either not public or does not exist.");
        }
        $callback = [$this, $method];
        return static function (...$args) use ($callback, $method) {
            try {
                return call_user_func_array($callback, $args);
            } catch (\Throwable $error) {
                if (glsr()->filterBool('hook/rethrow', false, $error, $method)) {
                    throw $error; // and do not log it: it is about to surface on its own
                }
                glsr_log()->error($error->getMessage())->debug($error);
            }
            if (str_starts_with($method, 'filter')) {
                // A throwable error was caught so just return the unfiltered first argument.
                return array_shift($args);
            }
        };
    }
}
