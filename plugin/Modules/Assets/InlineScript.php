<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Compat\LocalizeFilters;

class InlineScript
{
    /**
     * @param string $bundle "admin" or "public"
     */
    public function build(string $bundle, array $config): string
    {
        $compat = glsr(CompatScript::class)->isEnabled($bundle);
        $debug = ['enabled' => glsr(DebugScript::class)->isEnabled($bundle)];
        // the block editor prints the public script too
        if ('public' === $bundle && !is_admin() && !$debug['enabled'] && glsr(DebugScript::class)->isAllowedOnRequest()) {
            $debug['url'] = add_query_arg('ver', glsr()->version, glsr(DebugScript::class)->url($bundle));
        }
        $deprecated = [];
        if ($compat) {
            [$config, $deprecated] = glsr(LocalizeFilters::class)->apply($bundle, $config);
        }
        $config = glsr()->filterArray('assets/config', $config, $bundle);
        $config = array_merge($config, compact('compat', 'debug'));
        $path = glsr()->path('assets/scripts/inline-script.js');
        if (!file_exists($path)) {
            glsr_log()->error("Inline script is missing: {$path}");
            return '';
        }
        // strtr replaces in one pass
        $script = strtr(trim((string) file_get_contents($path)), [
            'GLSR_CONFIG' => $this->json($config),
            'GLSR_DEPRECATED' => $this->json((object) $deprecated),
            'GLSR_VERSION' => $this->json(glsr()->version),
        ]);
        $pattern = '/\"([a-zA-Z]+)\"(:[{\[\"])/'; // remove unnecessary quotes surrounding object keys
        $optimizedScript = preg_replace($pattern, '$1$2', $script);
        return glsr()->filterString("enqueue/{$bundle}/inline-script", $optimizedScript, $script, $config);
    }

    /**
     * @param mixed $value
     */
    protected function json($value): string
    {
        return (string) wp_json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
    }
}
