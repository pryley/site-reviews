<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * The styles records used by the addons as slug => class that reads and writes the record.
 */
class StylesFeaturesDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [];
    }

    /**
     * A slug must be a key and its class must exist.
     */
    protected function finalize(array $values = []): array
    {
        $features = [];
        foreach ($values as $slug => $class) {
            if (!is_string($slug) || $slug !== sanitize_key($slug) || !is_string($class) || !class_exists($class)) {
                continue;
            }
            $features[$slug] = $class;
        }
        return $features;
    }
}
