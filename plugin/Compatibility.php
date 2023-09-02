<?php

namespace GeminiLabs\SiteReviews;

class Compatibility
{
    public function removeHook(string $hook, string $fn, string $className, int $priority = 10): bool
    {
        global $wp_filter;
        if (!isset($wp_filter[$hook])) {
            return false;
        }
        if (!isset($wp_filter[$hook]->callbacks[$priority])) {
            return false;
        }
        foreach ($wp_filter[$hook]->callbacks[$priority] as $callback) {
            if (!isset($callback['function'][0]) || !isset($callback['function'][1])) {
                continue;
            }
            if (!is_a($callback['function'][0], $className)) {
                continue;
            }
            if ($fn !== $callback['function'][1]) {
                continue;
            }
            remove_filter($hook, [$callback['function'][0], $fn], $priority);
            return true;
        }
        return false;
    }
}
