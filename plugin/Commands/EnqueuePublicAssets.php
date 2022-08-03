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
        $this->enqueueCaptcha();
    }

    /**
     * @return void
     */
    public function enqueueAssets()
    {
        if (glsr()->filterBool('assets/css', true)) {
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
     * wpforms-recaptcha
     * google-recaptcha
     * nf-google-recaptcha.
     * @return void
     */
    public function enqueueCaptcha()
    {
        if (!glsr(Captcha::class)->isEnabled()) {
            return;
        }
        $integration = glsr_get_option('submissions.captcha.integration');
        $language = glsr()->filterString('captcha/language', get_locale());
        $apiUrl = 'https://www.google.com/recaptcha/api.js';
        $handle = glsr()->id.'/google-recaptcha';
        if ('hcaptcha' === $integration) {
            $apiUrl = 'https://js.hcaptcha.com/1/api.js';
            $handle = glsr()->id.'/hcaptcha';
        }
        if ('friendlycaptcha' === $integration) {
            $moduleUrl = 'https://unpkg.com/friendly-challenge@0.9.4/widget.module.min.js';
            $nomoduleUrl = 'https://unpkg.com/friendly-challenge@0.9.4/widget.min.js';
            wp_enqueue_script(glsr()->id.'/friendlycaptcha-module', $moduleUrl);
            wp_enqueue_script(glsr()->id.'/friendlycaptcha-nomodule', $nomoduleUrl);
        } else {
            wp_enqueue_script($handle, add_query_arg(['hl' => $language, 'render' => 'explicit'], $apiUrl));
        }
    }

    /**
     * @return string
     */
    public function inlineScript()
    {
        $variables = [
            'action' => glsr()->prefix.'action',
            'ajaxpagination' => $this->getFixedSelectorsForPagination(),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'captcha' => glsr(Captcha::class)->config(),
            'nameprefix' => glsr()->id,
            'stars' => [
                'clearable' => false,
                'tooltip' => false,
            ],
            'state' => [
                'popstate' => false,
            ],
            'text' => [
                'closemodal' => __('Close Modal', 'site-reviews'),
            ],
            'urlparameter' => glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter'),
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
        $script = 'window.hasOwnProperty("GLSR")||(window.GLSR={});';
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
