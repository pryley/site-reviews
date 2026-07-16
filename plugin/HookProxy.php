<?php

namespace GeminiLabs\SiteReviews;

trait HookProxy
{
    /**
     * Proxy for WordPress defined filter/action callbacks.
     *
     * As we cannot ensure third-party code will pass correct data types declared
     * by WordPress, this function allows us to maintain parameter types while
     * preventing fatal errors without introducing complexity. If something goes
     * wrong, the error is logged to the Site Reviews console and the unfiltered
     * first argument is returned.
     * 
     * The "site-reviews/hook/rethrow" hook is used by the test suite to catch
     * throwable errors.
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
