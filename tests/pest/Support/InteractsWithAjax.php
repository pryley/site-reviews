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
     * Fires the wp_ajax_{$action} hooks and captures what they printed.
     */
    protected function handleAjax(string $action): void
    {
        ini_set('implicit_flush', '0');
        ob_start();
        $_POST['action'] = $action;
        $_GET['action'] = $action;
        $_REQUEST = array_merge($_POST, $_GET);
        do_action('admin_init');
        do_action('wp_ajax_'.$_REQUEST['action'], null);
        $buffer = ob_get_clean();
        if (!empty($buffer)) {
            $this->lastResponse = $buffer;
        }
    }
}
