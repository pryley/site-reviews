<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Compatibility
{
    public function findCallback(string $hook, string $fn, string $className, int $priority = 10): array
    {
        global $wp_filter;
        if (!isset($wp_filter[$hook])) {
            return [];
        }
        if (!isset($wp_filter[$hook]->callbacks[$priority])) {
            return [];
        }
        foreach ($wp_filter[$hook]->callbacks[$priority] as $callback) {
            $callbackFn = [];
            $function = $callback['function'] ?? null;
            if (is_a($function, 'Closure')) {
                $ref = new \ReflectionFunction($function);
                $callbackFn = Arr::getAs('array', $ref->getStaticVariables(), 'callback');
            } elseif (is_array($function)) {
                $callbackFn = $function;
            }
            if (2 !== count($callbackFn)) {
                continue;
            }
            list($object, $method) = $callbackFn;
            if (!is_a($object, $className) || $method !== $fn) {
                continue;
            }
            return $callback;
        }
        return [];
    }

    public function removeHook(string $hook, string $fn, string $className, int $priority = 10): bool
    {
        $callback = $this->findCallback($hook, $fn, $className, $priority);
        if (!empty($callback['function'])) {
            remove_filter($hook, $callback['function'], $priority);
            return true;
        }
        return false;
    }
}
