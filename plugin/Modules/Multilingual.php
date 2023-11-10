<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Multilingual
{
    protected $integration;

    /**
     * @return mixed
     */
    public function __call(string $method, array $args = [])
    {
        if ($this->isIntegrated() && method_exists($this->integration, $method)) {
            return call_user_func_array([$this->integration, $method], $args);
        }
        return Arr::get($args, 0, false);
    }

    /**
     * @return \GeminiLabs\SiteReviews\Contracts\MultilingualContract|false
     */
    public function getIntegration(string $integration = '')
    {
        if (empty($integration)) {
            $integration = glsr(OptionManager::class)->get('settings.general.multilingual');
        }
        if (!empty($integration)) {
            $integrationClass = 'GeminiLabs\SiteReviews\Modules\Multilingual\\'.ucfirst($integration);
            if (class_exists($integrationClass)) {
                return glsr($integrationClass);
            }
            glsr_log()->error($integrationClass.' does not exist');
        }
        return false;
    }

    public function isIntegrated(): bool
    {
        if (!empty($this->integration)) {
            return true;
        }
        if ($integration = $this->getIntegration()) {
            $this->integration = $integration;
            return true;
        }
        return false;
    }
}
