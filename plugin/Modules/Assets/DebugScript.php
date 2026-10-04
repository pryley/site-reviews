<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Database\OptionManager;

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
        $isEnabled = glsr(OptionManager::class)->get('settings.advanced.debug', 'no', 'bool');
        return glsr()->filterBool('debug/assets', $isEnabled, $bundle);
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
