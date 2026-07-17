<?php

namespace GeminiLabs\SiteReviews\Tests;

/**
 * The two ways a WordPress request ends before the caller returns: wp_die() and
 * wp_redirect() + exit. A controller ending in either cannot be tested without
 * intercepting it, since exit takes the test process with it. No production code
 * moves:
 *
 *   wp_die()      picks its handler through the "wp_die_handler" filter (the
 *                 non-ajax twin of InteractsWithAjax's "wp_die_ajax_handler"), so
 *                 the handler can throw.
 *   wp_redirect() fires the "wp_redirect" filter as its FIRST statement, before
 *                 any header (wp-includes/pluggable.php); throwing there unwinds
 *                 out of wp_redirect() with no header sent, and the following exit
 *                 is never reached.
 *
 * expectsRedirect() throws at the filter, which is the only safe default: vendored
 * code redirects too (Action Scheduler's list table ends in a BARE exit that no
 * shadow can catch — ActionScheduler_Abstract_ListTable::process_row_actions()).
 * For a redirect the PLUGIN owns, which always ends in glsr_exit(), use
 * expectsRedirectAndExit() instead: it lets wp_redirect() return (the filter
 * answers a falsy location, so wp_redirect sends nothing — pluggable.php returns
 * false before its first header call) and catches the glsr_exit() shadow's throw,
 * so the test asserts the redirect AND that the request then terminated.
 *
 * Firing the controller through its hook works too: HookProxy wraps callbacks in
 * catch(\Throwable) but rethrows when `site-reviews/hook/rethrow` says to, and
 * bootstrap.php does. (Calling the controller directly is clearer, and these
 * tests do that.) Both interceptors are plain hooks, removed by Pest.php's
 * restoreHooks() — nothing to tear down.
 */
class WpDieException extends \Exception
{
}

class WpRedirectException extends \Exception
{
    public string $location;
    public int $status;

    public function __construct(string $location, int $status)
    {
        parent::__construct("Redirected to {$location}");
        $this->location = $location;
        $this->status = $status;
    }
}

trait InteractsWithExits
{
    /**
     * Runs $callback and returns the message wp_die() was given.
     *
     * BOTH exits are always intercepted, not just the asserted one: an uncaught wp_die() or
     * wp_redirect() ends the process, which PHPUnit can only report as "Premature end of PHP
     * process". Catching the other turns that into an ordinary, legible failure.
     *
     * @throws \PHPUnit\Framework\AssertionFailedError if wp_die() was not called
     */
    protected function expectsWpDie(callable $callback): string
    {
        $this->interceptExits();
        try {
            $callback();
        } catch (WpDieException $e) {
            return $e->getMessage();
        } catch (WpRedirectException $e) {
            $this->fail("Expected wp_die(), but it redirected to {$e->location} instead.");
        }
        $this->fail('Expected wp_die() to be called, and it was not.');
    }

    /**
     * Runs $callback and returns the location wp_redirect() was given.
     *
     * @throws \PHPUnit\Framework\AssertionFailedError if wp_redirect() was not called
     */
    protected function expectsRedirect(callable $callback): string
    {
        $this->interceptExits();
        try {
            $callback();
        } catch (WpRedirectException $e) {
            return $e->location;
        } catch (WpDieException $e) {
            $this->fail("Expected a redirect, but wp_die() was called instead: {$e->getMessage()}");
        }
        // A controller that did not redirect usually means a command that failed and put its
        // reason in the session for the form to print. Surface it rather than throwing it away.
        $message = (string) glsr()->sessionGet('form_message');
        $errors = glsr()->sessionGet('form_errors');
        $why = '' === $message && empty($errors)
            ? ' Nothing was put in the session, so it was not a validation failure.'
            : ' The form said: '.trim($message.' '.(string) wp_json_encode($errors));

        $this->fail('Expected wp_redirect() to be called, and it was not.'.$why);
    }

    /**
     * Runs $callback and returns the location wp_redirect() was given, insisting the
     * request then terminated through glsr_exit(). Only for redirects the plugin owns:
     * a vendor path past the captured redirect ends in a bare exit and takes the
     * worker with it — those stay on expectsRedirect().
     *
     * @throws \PHPUnit\Framework\AssertionFailedError if it did not redirect-then-exit
     */
    protected function expectsRedirectAndExit(callable $callback): string
    {
        $captured = null;
        $capture = function ($location, $status) use (&$captured) {
            $captured = ['location' => (string) $location, 'status' => (int) $status];
            return false; // wp_redirect() sends nothing and returns; flow reaches glsr_exit()
        };
        add_filter('wp_redirect', $capture, 10, 2);
        $this->interceptWpDie();
        try {
            $callback();
        } catch (GlsrExitException $e) {
            if (null === $captured) {
                $this->fail('The request terminated without redirecting first.');
            }
            return $captured['location'];
        } catch (WpDieException $e) {
            $this->fail("Expected a redirect, but wp_die() was called instead: {$e->getMessage()}");
        } finally {
            // a test may call this twice; a leftover capture would feed the next
            // one its own false and be read back as a redirect to ""
            remove_filter('wp_redirect', $capture, 10);
        }
        $this->fail(null === $captured
            ? 'Expected wp_redirect() to be called, and it was not.'
            : 'It redirected, but never terminated: glsr_exit() was not reached.');
    }

    protected function interceptExits(): void
    {
        $this->interceptRedirect();
        $this->interceptWpDie();
    }

    protected function interceptRedirect(): void
    {
        add_filter('wp_redirect', function ($location, $status) {
            throw new WpRedirectException((string) $location, (int) $status);
        }, 10, 2);
    }

    protected function interceptWpDie(): void
    {
        add_filter('wp_die_handler', fn () => function ($message, $title = '', $args = []) {
            if (is_wp_error($message)) {
                $message = $message->get_error_message();
            }
            throw new WpDieException(is_scalar($message) ? (string) $message : '');
        });
    }
}
