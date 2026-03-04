<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Captcha
{
    public function actions(): array
    {
        return glsr()->filterArray('captcha/actions', [
            'submit-review',
        ]);
    }

    public function config(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }
        $integration = (string) glsr_get_option('forms.captcha.integration');
        $className = Helper::buildClassName([$integration, 'validator'], 'Modules\Validator');
        if (!class_exists($className)) {
            return [];
        }
        return glsr($className)->config();
    }

    public function container(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }
        return glsr(Builder::class)->div([
            'class' => 'glsr-captcha-holder',
        ]);
    }

    public function isEnabled(string $service = ''): bool
    {
        $integration = glsr_get_option('forms.captcha.integration');
        $usage = glsr_get_option('forms.captcha.usage');
        $isEnabled = 'all' === $usage || ('guest' === $usage && !is_user_logged_in());
        if (!empty($service)) {
            return $integration === $service && $isEnabled;
        }
        return !empty($integration) && $isEnabled;
    }

    public function position(): string
    {
        $integration = glsr_get_option('forms.captcha.integration');
        if (str_starts_with($integration, 'recaptcha')) {
            return glsr_get_option('settings.forms.captcha.badge', 'bottomleft', 'string');
        }
        return glsr_get_option('settings.forms.captcha.placement', 'below', 'string');
    }
}
