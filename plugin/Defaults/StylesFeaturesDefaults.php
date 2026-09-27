<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * The site-wide styles records the addons keep, as slug => the class that
 * reads and writes the record.
 *
 * Unused by the plugin itself; the addons read it, so the addons sharing
 * one styles post type see each other's records whichever of them are
 * installed.
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
