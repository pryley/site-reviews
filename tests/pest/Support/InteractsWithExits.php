<?php

namespace GeminiLabs\SiteReviews\Tests;

/**
 * The two ways a WordPress request ends before the code that called it returns:
 * wp_die() and wp_redirect() + exit. A controller that ends in either cannot be
 * tested without intercepting it, because exit takes the test process with it.
 *
 * Core is built for this — it is how core's own tests assert a redirect — and no
 * production code has to move:
 *
 *   wp_die()     picks its handler through the "wp_die_handler" filter (the
 *                non-ajax twin of the "wp_die_ajax_handler" that InteractsWithAjax
 *                already uses), so the handler can be one that throws.
 *
 *   wp_redirect() fires the "wp_redirect" filter as its FIRST statement, before
 *                status_header() and before header() (wp-includes/pluggable.php).
 *                Throwing from that filter unwinds out of wp_redirect() with no
 *                header sent, and the exit on the line after it is never reached.
 *
 * One catch: the controller has to be CALLED, not fired through its hook. The
 * plugin registers third-party hooks through HookProxy, which wraps the callback
 * in a try/catch — it would swallow the exception and the test would see nothing.
 *
 * Both interceptors are plain hooks, so Pest.php's restoreHooks() removes them
 * after the test; there is nothing to tear down.
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
     * BOTH exits are always intercepted, never just the one being asserted: an
     * uncaught wp_die() or wp_redirect() ends the PHP process, and PHPUnit can
     * only report that as "Premature end of PHP process" — no message, no stack,
     * no test name beyond the one that was running. Catching the other exit too
     * turns that into an ordinary failure that says what actually happened.
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
        $this->fail('Expected wp_redirect() to be called, and it was not.');
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
