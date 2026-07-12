<?php

namespace GeminiLabs\SiteReviews\Tests;

/*
 * A port of WP_Ajax_UnitTestCase (tests/phpunit/includes/testcase-ajax.php),
 * verified against WordPress core rather than reconstructed from memory.
 *
 * Same contract: handleAjax() fires the wp_ajax_* hooks with the output
 * buffered, wp_die() is intercepted, and whatever the handler printed is left
 * in $lastResponse.
 *
 * Two details that are easy to get wrong, and that core settles:
 *
 *   - the die handler branches on the buffered OUTPUT, not on the message. A
 *     handler that printed something and then called wp_die() ended normally
 *     (Continue); one that died having printed nothing was stopped short
 *     (Stop). wp_send_json_* always prints first, so the review submissions
 *     take the Continue branch.
 *   - core does NOT define DOING_AJAX either: it adds the `wp_doing_ajax`
 *     filter, which is removed again on teardown, so the suite stays
 *     order-independent.
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

    protected ?int $previousErrorLevel = null;

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
        // Suppress "Cannot modify header information - headers already sent by".
        $this->previousErrorLevel = error_reporting();
        error_reporting($this->previousErrorLevel & ~E_WARNING);
    }

    protected function tearDownAjax(): void
    {
        $_POST = [];
        $_GET = [];
        unset($GLOBALS['post'], $GLOBALS['comment']);
        remove_filter('wp_doing_ajax', '__return_true');
        remove_filter('wp_die_ajax_handler', [$this, 'getAjaxDieHandler'], 1);
        if (null !== $this->previousErrorLevel) {
            error_reporting($this->previousErrorLevel);
        }
        set_current_screen('front');
        $this->lastResponse = '';
    }

    /**
     * Calls $callback and returns the JSON it sent, decoded.
     *
     * For a controller method that ends in wp_send_json(). Note that wp_send_json()
     * only calls wp_die() when wp_doing_ajax() is true — otherwise it calls plain
     * `die`, which nothing can intercept. setUpAjax() is what makes it interceptable,
     * so this only works inside the ajax harness.
     *
     * The ajax die handler takes the output buffer itself (it does ob_get_clean()
     * and keeps the result in lastResponse), which is why nothing is read back here.
     */
    protected function jsonSentBy(callable $callback): array
    {
        $this->lastResponse = '';
        $level = ob_get_level();
        ob_start();
        try {
            $callback();
        } catch (WpAjaxDieContinueException|WpAjaxDieStopException $e) {
            // wp_send_json() printed the payload and then died, as it does in a real
            // ajax request. The handler kept what it printed.
        }
        while (ob_get_level() > $level) {
            ob_end_clean(); // it did not die: do not leave the buffer open
        }

        return json_decode($this->lastResponse, true) ?? [];
    }

    /**
     * Fires the wp_ajax_{$action} hooks and captures what they printed.
     *
     * Core's WP_Ajax_UnitTestCase fires admin_init here first, copying what
     * wp-admin/admin-ajax.php does. This does NOT, and the difference matters:
     * admin-ajax.php also defines WP_ADMIN and loads wp-admin/includes/admin.php
     * before it fires that hook, so in a real ajax request is_admin() is true.
     * Here it is false, and a plugin is entitled to assume that admin_init implies
     * the admin — only wp-admin/admin.php and admin-ajax.php ever fire it, and both
     * define WP_ADMIN.
     *
     * WooCommerce makes exactly that assumption: it loads its admin function files
     * at boot behind is_admin(), then hooks admin_init to a callback that calls one
     * of them (wc_get_page_screen_id, via OrderAttributionController). Firing
     * admin_init here reached that callback in a process where the function was
     * never defined — a fatal, in WooCommerce, caused by us.
     *
     * Nothing is lost by dropping it. The plugin's ajax routes are registered on
     * wp_ajax_{prefix}public_action and wp_ajax_{prefix}admin_action (RouterHooks);
     * admin_init carries only routeAdminGetRequest and routeAdminPostRequest, which
     * are the NON-ajax admin routes and are not what this harness exercises.
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
