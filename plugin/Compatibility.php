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
            if (!is_array($callback['function'])) {
                continue;
            }
            $object = Arr::get($callback['function'], 0);
            $method = Arr::get($callback['function'], 1);
            if (is_a($object, $className) && $method === $fn) {
                return $callback;
            }
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
