<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * An IP-based request lock, shared by the admin-ajax and REST transports.
 */
class Mutex
{
    public function actions(): array
    {
        return glsr()->filterArray('router/mutex/actions', [
            'submit-review',
        ]);
    }

    public function isValid(string $action): bool
    {
        if (!in_array($action, $this->actions())) {
            return true;
        }
        $ipAddress = Helper::clientIp(); // "unknown" when undetected, so those clients share one lock
        $hash = Str::hash($ipAddress, 13);
        $lock = Str::prefix($hash, glsr()->prefix);
        if (get_transient($lock)) {
            return false; // is parallel request
        }
        $expiration = glsr()->filterInt('router/mutex/expiration', 5, $ipAddress);
        $transient = set_transient($lock, 1, $expiration);
        if (!$transient) {
            return false; // parallel requests cannot set transient
        }
        return true;
    }
}
