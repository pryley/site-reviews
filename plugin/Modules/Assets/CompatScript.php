<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

class CompatScript
{
    /**
     * Scripts that depend on our public/admin scripts might still use deprecated keys,
     * so make them depend on the compat script too.
     */
    public function addToDependents(): void
    {
        foreach (['admin', 'public'] as $bundle) {
            $compat = $this->scriptHandle($bundle);
            if (!wp_script_is($compat, 'registered')) {
                continue;
            }
            $script = 'admin' === $bundle ? glsr()->id.'/admin' : glsr()->id;
            foreach (wp_scripts()->registered as $handle => $dependent) {
                if ($handle !== $compat && in_array($script, $dependent->deps, true) && !in_array($compat, $dependent->deps, true)) {
                    $dependent->deps[] = $compat;
                }
            }
        }
    }

    /**
     * @param string $bundle "admin" or "public"
     */
    public function isEnabled(string $bundle): bool
    {
        return glsr()->filterBool('compat/assets', true, $bundle);
    }

    /**
     * @param string $bundle "admin" or "public"
     */
    public function scriptHandle(string $bundle): string
    {
        return 'admin' === $bundle ? glsr()->id.'/admin/compat' : glsr()->id.'/compat';
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
