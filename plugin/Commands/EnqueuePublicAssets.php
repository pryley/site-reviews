<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Modules\Assets\CompatScript;
use GeminiLabs\SiteReviews\Modules\Assets\InlineScript;
use GeminiLabs\SiteReviews\Modules\Assets\Stylesheets;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Style;

class EnqueuePublicAssets extends AbstractCommand
{
    public const MODULE_PREFIX = '@site-reviews/';

    /**
     * The behaviour modules and the modules each imports statically; every one imports core.
     *
     * @var array<string, string[]>
     */
    public const MODULES = [
        'captcha' => ['core'],
        'form' => ['core', 'request'],
        'modal' => ['core'],
        'request' => ['core'],
        'reviews' => ['core', 'request'],
    ];

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
                'routes' => glsr()->filterArray('rest-api/routes', [
                    'submit-review' => 'submissions',
                ]),
                'url' => esc_url_raw(rest_url(glsr()->id.'/v1/')),
            ],
            'styles' => glsr(Stylesheets::class)->urls(),
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

    public function enqueueButtonStyles(): void
    {
        if (glsr(Style::class)->usesBlockButton()) {
            wp_enqueue_style('wp-block-button');
        }
    }

    /**
     * Register the script modules the bootstrap names in the import map.
     *
     * @return string[] the module ids
     */
    public function registerModules(): array
    {
        $core = static::MODULE_PREFIX.'core';
        wp_register_script_module($core, glsr()->url('assets/scripts/modules/core.js'), [], glsr()->version);
        foreach (static::MODULES as $name => $imports) {
            $dependencies = array_map(fn ($import) => static::MODULE_PREFIX.$import, $imports);
            wp_register_script_module(static::MODULE_PREFIX.$name, glsr()->url("assets/scripts/modules/{$name}.js"), $dependencies, glsr()->version);
        }
        $ids = array_map(fn ($name) => static::MODULE_PREFIX.$name, array_keys(static::MODULES));
        return glsr()->filterArray('enqueue/public/modules', $ids);
    }

    public function enqueueScripts(): void
    {
        if (!glsr()->filterBool('assets/js', true)) {
            return;
        }
        $dependencies = glsr()->filterArray('enqueue/public/dependencies', []);
        $args = [
            'in_footer' => false, // the module tags print before the footer scripts, and the loader runs first
            'strategy' => 'defer',
        ];
        wp_register_script(glsr()->id, glsr()->url(sprintf('assets/scripts/%s.js', glsr()->id)), $dependencies, glsr()->version, $args);
        wp_enqueue_script(glsr()->id);
        wp_add_inline_script(glsr()->id, $this->inlineScript(), 'before');
        // dynamic: in the import map, fetched when a root names the module
        $modules = array_map(fn ($id) => ['id' => $id, 'import' => 'dynamic'], $this->registerModules());
        wp_scripts()->add_data(glsr()->id, 'module_dependencies', $modules);
        $compat = glsr(CompatScript::class);
        $last = glsr()->id;
        if ($compat->isEnabled('public')) {
            $last = $compat->scriptHandle('public');
            wp_enqueue_script($last, $compat->url('public'), [glsr()->id], glsr()->version, $args);
        }
        // a script added with this filter can read a deprecated key as it is parsed
        wp_add_inline_script($last, glsr()->filterString('enqueue/public/inline-script/after', ''));
    }

    public function handle(): void
    {
        $this->enqueueScripts();
    }

    public function inlineScript(): string
    {
        return glsr(InlineScript::class)->build('public', $this->config());
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
