<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

class DebugScript
{
    /**
     * Whether the public script loads the debug script when the URL contains the parameter.
     */
    public function isAllowedOnRequest(): bool
    {
        return glsr()->filterBool('debug/on-request', true);
    }

    /**
     * @param string $bundle "admin" or "public"
     */
    public function isEnabled(string $bundle): bool
    {
        return glsr()->filterBool('debug/assets', false, $bundle);
    }

    /**
     * @param string $bundle "admin" or "public"
     */
    public function scriptHandle(string $bundle): string
    {
        return 'admin' === $bundle ? glsr()->id.'/admin/debug' : glsr()->id.'/debug';
    }

    /**
     * @param string $bundle "admin" or "public"
     */
    public function url(string $bundle): string
    {
        $name = str_replace('/', '-', $this->scriptHandle($bundle));
        return glsr()->url("assets/scripts/{$name}.js");
    }
}
