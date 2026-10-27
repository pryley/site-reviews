<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

/**
 * Whether a review is being copied or synced to another site right now.
 * A plugin that acts on review events asks it to tell a synced write from an event on the site.
 */
class SyncState
{
    protected static int $depth = 0; // one state for the request, whichever instance asks

    /**
     * Run the callback as a sync and return what it returns.
     *
     * @return mixed
     */
    public function during(callable $callback)
    {
        ++static::$depth;
        try {
            return $callback();
        } finally {
            --static::$depth;
        }
    }

    public function isActive(): bool
    {
        return static::$depth > 0;
    }
}
