<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * A request lock per visitor and action, shared by the admin-ajax and REST transports.
 */
class Mutex
{
    /** @var array<string, string> the token of each lock of this request keyed by action */
    protected array $tokens = [];

    public function actions(): array
    {
        return glsr()->filterArray('router/mutex/actions', [
            'submit-review',
        ]);
    }

    /**
     * Lock the action of the request.
     */
    public function lock(string $action): bool
    {
        if (!in_array($action, $this->actions())) {
            return true;
        }
        $key = $this->key($action);
        if (get_transient($key)) {
            return false; // is parallel request
        }
        $expiration = glsr()->filterInt('router/mutex/expiration', 5, Helper::clientIp()); // for a request that dies before shutdown
        $token = wp_generate_password(12, false);
        if (!set_transient($key, $token, $expiration)) {
            return false; // parallel requests cannot set transient
        }
        $this->tokens[$action] = $token;
        add_action('shutdown', fn () => $this->release($action));
        return true;
    }

    /**
     * Release the action lock of the request.
     */
    public function release(string $action): void
    {
        $key = $this->key($action);
        $token = $this->tokens[$action] ?? '';
        if ('' !== $token && get_transient($key) === $token) {
            delete_transient($key);
        }
        unset($this->tokens[$action]);
    }

    protected function key(string $action): string
    {
        $ipAddress = Helper::clientIp(); // "unknown" when undetected
        return Str::prefix(Str::hash("{$ipAddress}|{$action}", 13), glsr()->prefix);
    }
}
