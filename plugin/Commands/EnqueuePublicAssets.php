<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Modules\Assets\AssetCss;
use GeminiLabs\SiteReviews\Modules\Assets\AssetJs;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Style;

class EnqueuePublicAssets implements Contract
{
    /**
     * @return void
     */
    public function handle()
    {
        $this->enqueueAssets();
    }

    /**
     * @return void
     */
    public function enqueueAssets()
    {
        if (glsr()->filterBool('assets/css', true)) {
            // ensure block styles are loaded on post types with blocks disabled
            $blocks = \WP_Block_Type_Registry::get_instance();
            if ($blocks->is_registered('core/button')) {
                // $blocks->get_registered('core/button')->style_handles;
                wp_enqueue_style('wp-block-button');
            }
            if ($blocks->is_registered('core/search')) {
                // $blocks->get_registered('core/search')->style_handles;
                wp_enqueue_style('wp-block-search');
            }
            wp_enqueue_style(glsr()->id, glsr(AssetCss::class)->url(), [], glsr(AssetCss::class)->version());
            wp_add_inline_style(glsr()->id, $this->inlineStyles());
            glsr(AssetCss::class)->optimize();
        }
        if (glsr()->filterBool('assets/js', true)) {
            $dependencies = glsr()->filterArray('enqueue/public/dependencies', []);
            wp_enqueue_script(glsr()->id, glsr(AssetJs::class)->url(), $dependencies, glsr(AssetJs::class)->version(), true);
            wp_add_inline_script(glsr()->id, $this->inlineScript(), 'before');
            wp_add_inline_script(glsr()->id, glsr()->filterString('enqueue/public/inline-script/after', ''));
            glsr(AssetJs::class)->optimize();
        }
    }

    /**
     * @return string
     */
    public function inlineScript()
    {
        $urlparameter = glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter')
            ? glsr()->constant('PAGED_QUERY_VAR')
            : false;
        $variables = [
            'action' => glsr()->prefix.'action',
            'addons' => [],
            'ajaxpagination' => $this->getFixedSelectorsForPagination(),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'captcha' => glsr(Captcha::class)->config(),
            'nameprefix' => glsr()->id,
            'starsconfig' => [
                'clearable' => false,
                'tooltip' => false, // 'Select a Rating'
            ],
            'state' => [
                'popstate' => false,
            ],
            'text' => [
                'closemodal' => __('Close Modal', 'site-reviews'),
            ],
            'urlparameter' => $urlparameter,
            'validationconfig' => array_merge(
                [
                    'field' => glsr(Style::class)->defaultClasses('field'),
                    'form' => glsr(Style::class)->defaultClasses('form'),
                ],
                glsr(Style::class)->validation
            ),
            'validationstrings' => glsr(ValidationStringsDefaults::class)->defaults(),
            'version' => glsr()->version,
        ];
        $variables = glsr()->filterArray('enqueue/public/localize', $variables);
        return $this->buildInlineScript($variables);
    }

    /**
     * @return string|void
     */
    public function inlineStyles()
    {
        $inlineStylesheetPath = glsr()->path('assets/styles/inline-styles.css');
        if (!file_exists($inlineStylesheetPath)) {
            glsr_log()->error('Inline stylesheet is missing: '.$inlineStylesheetPath);
            return;
        }
        $inlineStylesheetValues = glsr()->config('inline-styles');
        $stylesheet = str_replace(
            array_keys($inlineStylesheetValues),
            array_values($inlineStylesheetValues),
            file_get_contents($inlineStylesheetPath)
        );
        return glsr()->filterString('enqueue/public/inline-styles', $stylesheet);
    }

    /**
     * @return string
     */
    protected function buildInlineScript(array $variables)
    {
        $script = 'window.hasOwnProperty("GLSR")||(window.GLSR={Event:{on:()=>{}}});';
        foreach ($variables as $key => $value) {
            $script .= sprintf('GLSR.%s=%s;', $key, json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $pattern = '/\"([a-zA-Z]+)\"(:[{\[\"])/'; // remove unnecessary quotes surrounding object keys
        $optimizedScript = preg_replace($pattern, '$1$2', $script);
        return glsr()->filterString('enqueue/public/inline-script', $optimizedScript, $script, $variables);
    }

    /**
     * @return array
     */
    protected function getFixedSelectorsForPagination()
    {
        $selectors = ['#wpadminbar', '.site-navigation-fixed'];
        return glsr()->filterArray('enqueue/public/localize/ajax-pagination', $selectors);
    }
}
