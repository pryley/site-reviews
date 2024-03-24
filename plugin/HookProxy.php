<?php

namespace GeminiLabs\SiteReviews;

/**
 * Proxy for WordPress defined filter/action hook callbacks.
 *
 * Since we cannot ensure third-party code will pass the correct data declared
 * by WordPress DocBlocks, this Trait (when used in Controller classes) allows
 * us to use method parameter type hints in WordPress defined filter/action hook
 * callbacks and prevents fatal errors without introducing complexity.
 */
trait HookProxy
{
    public function __call($method, array $args = [])
    {
        if (str_starts_with($method, 'proxy_')) {
            $method = substr($method, strlen('proxy_'));
        }
        $reflection = new \ReflectionMethod($this, $method);
        if (!$reflection->isPublic()) {
            throw new \BadMethodCallException("Method [{$method}] is either not public or does not exist.");
        }
        try {
            return call_user_func_array([$this, $method], $args);
        } catch (\TypeError $error) {
            glsr_log()->error($error->getMessage())->debug($error);
            // @todo check for debug mode here...
            // if (false) {
            //     throw $error;
            // }
            if (str_starts_with($method, 'filter')) {
                return array_shift($args); // return the unmodified first argument
            }
        }
    }
}
