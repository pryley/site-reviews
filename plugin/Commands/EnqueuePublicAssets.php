<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Modules\Assets\AssetCss;
use GeminiLabs\SiteReviews\Modules\Assets\AssetJs;
use GeminiLabs\SiteReviews\Modules\Assets\CompatScript;
use GeminiLabs\SiteReviews\Modules\Assets\InlineScript;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Style;

class EnqueuePublicAssets extends AbstractCommand
{
    public function config(): array
    {
        $style = glsr(Style::class);
        return [
            'captcha' => $this->captcha(),
            'modal' => [
                'wrappedBy' => glsr()->filterArray('modal_wrapped_by', ['block']),
            ],
            'nameprefix' => glsr()->id,
            'pagination' => [
                'fixed' => $this->getFixedSelectorsForPagination(),
                'urlParameter' => glsr(OptionManager::class)->getBool('settings.reviews.pagination.url_parameter')
                    ? glsr()->constant('PAGED_QUERY_VAR')
                    : false,
            ],
            'rating' => [
                'clearable' => false,
                'tooltip' => __('Select a Rating', 'site-reviews'),
            ],
            'request' => [
                'ajax' => [
                    'action' => glsr()->prefix.'public_action',
                    'rest' => glsr()->prefix.'rest_request',
                    'url' => admin_url('admin-ajax.php'),
                ],
                'nonce' => is_user_logged_in() ? wp_create_nonce('wp_rest') : false,
                'url' => esc_url_raw(rest_url(glsr()->id.'/v1/')),
            ],
            'text' => [
                'closeModal' => __('Close Modal', 'site-reviews'),
            ],
            'validation' => [
                'field' => $style->defaultClasses('field'),
                'fieldError' => $style->validation('field_error'),
                'fieldHidden' => $style->validation('field_hidden'),
                'fieldMessage' => $style->validation('field_message'),
                'fieldRequired' => $style->validation('field_required'),
                'fieldValid' => $style->validation('field_valid'),
                'form' => $style->defaultClasses('form'),
                'formError' => $style->validation('form_error'),
                'formMessage' => $style->validation('form_message'),
                'formMessageFailed' => $style->validation('form_message_failed'),
                'formMessageSuccess' => $style->validation('form_message_success'),
                'inputError' => $style->validation('input_error'),
                'inputValid' => $style->validation('input_valid'),
                'strings' => glsr(ValidationStringsDefaults::class)->defaults(),
            ],
        ];
    }

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
        $compat = glsr(CompatScript::class);
        $args = [
            'in_footer' => true,
            'strategy' => 'defer',
        ];
        $last = glsr()->id;
        // the optimized script holds the compat script
        if ($compat->isEnabled('public') && !(glsr(AssetJs::class)->canOptimize() && glsr(AssetJs::class)->isOptimized())) {
            $last = $compat->scriptHandle('public');
            wp_enqueue_script($last, $compat->url('public'), [glsr()->id], glsr()->version, $args);
        }
        // a script added with this filter can read a deprecated key as it is parsed
        wp_add_inline_script($last, glsr()->filterString('enqueue/public/inline-script/after', ''));
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
        return glsr(InlineScript::class)->build('public', $this->config());
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

    protected function captcha(): array
    {
        $config = glsr(Captcha::class)->config();
        foreach (['captcha_type' => 'captchaType', 'token_field' => 'tokenField'] as $key => $name) {
            if (array_key_exists($key, $config)) {
                $config[$name] = $config[$key];
                unset($config[$key]);
            }
        }
        return $config;
    }

    protected function getFixedSelectorsForPagination(): array
    {
        $selectors = ['#wpadminbar', '.site-navigation-fixed'];
        return glsr()->filterArray('enqueue/public/localize/ajax-pagination', $selectors);
    }
}
