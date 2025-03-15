<?php

namespace GeminiLabs\SiteReviews\Notices;

use GeminiLabs\SiteReviews\Defaults\GatekeeperNoticeDefaults;
use GeminiLabs\SiteReviews\Gatekeeper;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class GatekeeperNotice extends AbstractNotice
{
    public $errors = [];

    public function render(): void
    {
        if ($errors = $this->errors([
            Gatekeeper::ERROR_NOT_ACTIVATED,
            Gatekeeper::ERROR_NOT_INSTALLED,
            Gatekeeper::ERROR_NOT_SUPPORTED,
        ])) {
            glsr()->render('partials/notices/gatekeeper-external', [
                'actions' => $this->pluginActions($errors),
                'errors' => $errors,
                'links' => $this->pluginLinks($errors),
            ]);
        }
        if ($errors = $this->errors([Gatekeeper::ERROR_NOT_TESTED])) {
            glsr()->render('partials/notices/gatekeeper-internal', [
                'actions' => $this->pluginActions($errors),
                'errors' => $errors,
                'links' => $this->pluginLinks($errors),
            ]);
        }
    }

    protected function canRender(): bool
    {
        $transient = glsr()->prefix.'gatekeeper';
        $errors = get_transient($transient);
        if (!is_array($errors)) {
            $this->errors = [];
            return false;
        }
        $this->errors = $errors;
        delete_transient($transient);
        return parent::canRender();
    }

    protected function errors(array $errorKeys): array
    {
        return array_filter($this->errors,
            fn ($data) => in_array(Arr::get($data, 'error'), $errorKeys)
        );
    }

    protected function hasPermission(): bool
    {
        return true;
    }

    protected function isDismissible(): bool
    {
        return false;
    }

    protected function isNoticeScreen(): bool
    {
        $screenIds = ['dashboard', 'plugins', 'update-core'];
        if (in_array(glsr_current_screen()->id, $screenIds)) {
            return true;
        }
        if (get_current_screen()->in_admin('network')) {
            return true;
        }
        return parent::isNoticeScreen();
    }

    protected function pluginActionNotActivated(array $data): string
    {
        if (!current_user_can('activate_plugins')) {
            return '';
        }
        return $this->pluginAction([
            'action' => 'activate',
            'admin_page' => 'plugins.php',
            'name' => $data['name'],
            'nonce_prefix' => 'activate-plugin_',
            'plugin' => $data['plugin'],
            'text' => _x('Activate %s', 'admin-text', 'site-reviews'),
        ]);
    }

    protected function pluginActionNotInstalled(array $data): string
    {
        if (!current_user_can('install_plugins')) {
            return '';
        }
        return $this->pluginAction([
            'action' => 'install-plugin',
            'admin_page' => 'update.php',
            'name' => $data['name'],
            'nonce_prefix' => 'install-plugin_',
            'plugin' => $data['textdomain'],
            'text' => _x('Install %s', 'admin-text', 'site-reviews'),
        ]);
    }

    protected function pluginActionNotSupported(array $data): string
    {
        if (!current_user_can('update_plugins')) {
            return '';
        }
        return $this->pluginAction([
            'action' => 'upgrade-plugin',
            'admin_page' => 'update.php',
            'name' => $data['name'],
            'nonce_prefix' => 'upgrade-plugin_',
            'plugin' => $data['plugin'],
            'text' => _x('Update %s', 'admin-text', 'site-reviews'),
        ]);
    }

    protected function pluginAction(array $data): string
    {
        $args = [
            'action' => $data['action'],
            'plugin' => $data['plugin'],
            'plugin_status' => filter_input(INPUT_GET, 'plugin_status'),
            'paged' => filter_input(INPUT_GET, 'paged'),
            's' => filter_input(INPUT_GET, 's'),
            'trigger' => 'notice',
        ];
        $url = add_query_arg($args, self_admin_url($data['admin_page']));
        $url = wp_nonce_url($url, $data['nonce_prefix'].$data['plugin']);
        return glsr(Builder::class)->a([
            'class' => 'button button-primary',
            'href' => $url,
            'text' => sprintf($data['text'], $data['name']),
        ]);
    }

    protected function pluginActions(array $errors): string
    {
        $actions = [];
        foreach ($errors as $plugin => $data) {
            $data = glsr(GatekeeperNoticeDefaults::class)->restrict($data);
            if (empty($data['error'])) {
                continue;
            }
            $method = Helper::buildMethodName('pluginAction', $data['error']);
            if (method_exists($this, $method)) {
                $data['plugin'] = $plugin;
                $actions[] = call_user_func([$this, $method], $data);
            }
        }
        return implode(' ', $actions);
    }

    protected function pluginLinks(array $errors): string
    {
        $links = [];
        foreach ($errors as $plugin => $data) {
            $data = glsr(GatekeeperNoticeDefaults::class)->restrict($data);
            $links[] = sprintf('<span class="plugin-%s"><a href="%s">%s</a></span>',
                $data['textdomain'],
                $data['plugin_uri'],
                $data['name']
            );
        }
        return Str::naturalJoin($links);
    }
}
