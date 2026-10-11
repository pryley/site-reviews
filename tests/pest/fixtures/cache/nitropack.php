<?php
/*
 * FIXTURES for the NitroPack purge in CacheTest: the two parts of NitroPack 1.19.0 that
 * Integrations\Cache\Controller::purgeNitropack() calls. Each invalidation is recorded
 * in $GLOBALS['nitropack_invalidated'].
 */

namespace NitroPack\WordPress\Settings {
    class CPTOptimization
    {
        public static function getInstance(): self
        {
            return new self();
        }

        public function nitropack_get_cacheable_object_types(): array
        {
            return ['page', 'post'];
        }
    }
}

namespace {
    function nitropack_invalidate($url = null, $tag = null, $reason = null): void
    {
        $GLOBALS['nitropack_invalidated'][] = [$url, $tag, $reason];
    }
}
