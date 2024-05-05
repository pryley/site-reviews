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
            } catch (\TypeError $error) {
                glsr_log()->error($error->getMessage())->debug($error);
                // if (defined('WP_DEBUG') && \WP_DEBUG) {
                //     throw $error;
                // }
            }
            if (str_starts_with($method, 'filter')) {
                return array_shift($args); // return the unmodified first argument
            }
        };
    }
}
