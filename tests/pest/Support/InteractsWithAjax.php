<?php

namespace GeminiLabs\SiteReviews\Tests;

/*
 * An admin-ajax harness: handleAjax() fires the wp_ajax_* hooks with output
 * buffered, intercepts wp_die(), and leaves whatever the handler printed in
 * $lastResponse.
 *
 * Two subtleties:
 *   - the die handler branches on the buffered OUTPUT, not the message: a
 *     handler that printed then called wp_die() ended normally (Continue), one
 *     that died having printed nothing was stopped (Stop). wp_send_json_*
 *     prints first, so review submissions take Continue.
 *   - DOING_AJAX is NOT defined; the wp_doing_ajax filter is added and removed
 *     on teardown, so the suite stays order-independent.
 */

class WpAjaxDieContinueException extends \Exception
{
}

class WpAjaxDieStopException extends \Exception
{
}

trait InteractsWithAjax
{
    protected string $lastResponse = '';

    /**
     * The wp_die() handler installed for the duration of an ajax request.
     *
     * @param string|\WP_Error $message
     */
    public function ajaxDieHandler($message = ''): void
    {
        $this->lastResponse .= (string) ob_get_clean();
        if ('' === $this->lastResponse) {
            throw new WpAjaxDieStopException(is_scalar($message) ? (string) $message : '0');
        }
        throw new WpAjaxDieContinueException(is_scalar($message) ? (string) $message : '');
    }

    /**
     * @param  callable  $handler  the default ajax wp_die() handler
     */
    public function getAjaxDieHandler($handler = null): array
    {
        return [$this, 'ajaxDieHandler'];
    }

    protected function setUpAjax(): void
    {
        add_filter('wp_doing_ajax', '__return_true');
        add_filter('wp_die_ajax_handler', [$this, 'getAjaxDieHandler'], 1, 1);
        set_current_screen('ajax');
        // No error masking here. This used to drop E_WARNING for the duration of the request
        // (against "headers already sent"), but that warning cannot fire in this harness — the
        // CLI process buffers all output and never sends a header — and the mask also hid every
        // REAL warning raised by plugin code driven through ajax, defeating phpunit.xml's
        // failOnWarning exactly where it matters most. Verified by execution: a trigger_error()
        // probe inside routePublicAjaxRequest() fails the suite (exit 2) without the mask.
    }

    protected function tearDownAjax(): void
    {
        $_POST = [];
        $_GET = [];
        unset($GLOBALS['post'], $GLOBALS['comment']);
        remove_filter('wp_doing_ajax', '__return_true');
        remove_filter('wp_die_ajax_handler', [$this, 'getAjaxDieHandler'], 1);
        set_current_screen('front');
        $this->lastResponse = '';
    }

    /**
     * Calls $callback and returns the JSON it sent, decoded. For a controller method ending in
     * wp_send_json(), which only calls wp_die() when wp_doing_ajax() is true (else plain `die`,
     * which nothing can intercept) — so this works only inside the ajax harness. The die handler
     * takes the output buffer itself, which is why nothing is read back here.
     */
    protected function jsonSentBy(callable $callback): array
    {
        $this->lastResponse = '';
        $level = ob_get_level();
        ob_start();
        try {
            $callback();
        } catch (WpAjaxDieContinueException|WpAjaxDieStopException $e) {
            // wp_send_json() printed the payload and died, as in a real ajax request; the handler
            // kept what it printed.
        }
        while (ob_get_level() > $level) {
            ob_end_clean(); // it did not die: do not leave the buffer open
        }

        return json_decode($this->lastResponse, true) ?? [];
    }

    /**
     * Fires the wp_ajax_{$action} hooks and captures what they printed.
     *
     * Deliberately does NOT fire admin_init, though admin-ajax.php does. admin-ajax.php also
     * defines WP_ADMIN and loads wp-admin/includes/admin.php first, so in a real ajax request
     * is_admin() is true; here it is false, and a plugin may assume admin_init implies the admin.
     * WooCommerce does: it loads its admin function files behind is_admin() at boot, then hooks
     * admin_init to a callback that uses one (wc_get_page_screen_id, via
     * OrderAttributionController) — firing admin_init here reached that callback with the function
     * undefined, a fatal in WooCommerce caused by us.
     *
     * Nothing is lost: the plugin's ajax routes hang off wp_ajax_{prefix}public_action /
     * admin_action (RouterHooks); admin_init carries only the non-ajax admin routes.
     */
    protected function handleAjax(string $action): void
    {
        ini_set('implicit_flush', '0');
        ob_start();
        $_POST['action'] = $action;
        $_GET['action'] = $action;
        $_REQUEST = array_merge($_POST, $_GET);
        do_action('wp_ajax_'.$_REQUEST['action'], null);
        $buffer = ob_get_clean();
        if (!empty($buffer)) {
            $this->lastResponse = $buffer;
        }
    }
}
