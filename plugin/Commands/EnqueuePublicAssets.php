<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Modules\Assets\AssetCss;
use GeminiLabs\SiteReviews\Modules\Assets\AssetJs;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Style;

class EnqueuePublicAssets extends AbstractCommand
{
    public function enqueueScripts(): void
    {
        if (!glsr()->filterBool('assets/js', true)) {
            return;
        }
        $dependencies = glsr()->filterArray('enqueue/public/dependencies', []);
        wp_register_script(glsr()->id, glsr(AssetJs::class)->url(), $dependencies, glsr(AssetJs::class)->version(), [
            'in_footer' => true,
            'strategy' => 'defer',
        ]);
        wp_enqueue_script(glsr()->id);
        wp_add_inline_script(glsr()->id, $this->inlineScript(), 'before');
        wp_add_inline_script(glsr()->id, glsr()->filterString('enqueue/public/inline-script/after', ''));
        glsr(AssetJs::class)->optimize();
    }

    public function enqueueStyles(): void
    {
        if (!glsr()->filterBool('assets/css', true)) {
            return;
        }
        wp_register_style(glsr()->id, glsr(AssetCss::class)->url(), [], glsr(AssetCss::class)->version());
        wp_enqueue_style(glsr()->id);
        wp_add_inline_style(glsr()->id, $this->inlineStyles());
        glsr(AssetCss::class)->optimize();
    }

    public function handle(): void
    {
        $this->enqueueStyles();
        $this->enqueueScripts();
    }

    public function inlineScript(): string
    {
        $urlparameter = glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter')
            ? glsr()->constant('PAGED_QUERY_VAR')
            : false;
        $variables = [
            'action' => glsr()->prefix.'public_action',
            'addons' => [],
            'ajax_pagination' => $this->getFixedSelectorsForPagination(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'captcha' => glsr(Captcha::class)->config(),
            'modal_wrapped_by' => glsr()->filterarray('modal_wrapped_by', ['block']),
            'nameprefix' => glsr()->id,
            'stars_config' => [
                'clearable' => false,
                'tooltip' => __('Select a Rating', 'site-reviews'),
            ],
            'state' => [
                'popstate' => false,
            ],
            'text' => [
                'close_modal' => __('Close Modal', 'site-reviews'),
            ],
            'url_parameter' => $urlparameter,
            'validation_config' => array_merge(
                [
                    'field' => glsr(Style::class)->defaultClasses('field'),
                    'form' => glsr(Style::class)->defaultClasses('form'),
                ],
                glsr(Style::class)->validation
            ),
            'validation_strings' => glsr(ValidationStringsDefaults::class)->defaults(),
            'version' => glsr()->version,
        ];
        $variables = glsr()->filterArray('enqueue/public/localize', $variables);
        return $this->buildInlineScript($variables);
    }

    public function inlineStyles(): string
    {
        $inlineStylesheetPath = glsr()->path('assets/styles/inline-styles.css');
        if (!file_exists($inlineStylesheetPath)) {
            glsr_log()->error("Inline stylesheet is missing: {$inlineStylesheetPath}");
            return '';
        }
        $inlineConfig = glsr()->config('inline-styles');
        $inlineCss = str_replace(
            array_keys($inlineConfig),
            array_values($inlineConfig),
            file_get_contents($inlineStylesheetPath)
        );
        return glsr()->filterString('enqueue/public/inline-styles', $inlineCss, $inlineConfig);
    }

    protected function buildInlineScript(array $variables): string
    {
        $script = 'window.hasOwnProperty("GLSR")||(window.GLSR={Event:{on:()=>{}}});';
        foreach ($variables as $key => $value) {
            $script .= sprintf('GLSR.%s=%s;', $key, (string) wp_json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $pattern = '/\"([a-zA-Z]+)\"(:[{\[\"])/'; // remove unnecessary quotes surrounding object keys
        $optimizedScript = preg_replace($pattern, '$1$2', $script);
        return glsr()->filterString('enqueue/public/inline-script', $optimizedScript, $script, $variables);
    }

    protected function getFixedSelectorsForPagination(): array
    {
        $selectors = ['#wpadminbar', '.site-navigation-fixed'];
        return glsr()->filterArray('enqueue/public/localize/ajax-pagination', $selectors);
    }
}
