<?php

/**
 * glsr_exit() is the plugin's only exit point (helpers.php). The construct it
 * wraps cannot be intercepted, but an unqualified function call resolves to the
 * caller's namespace first — so each plugin namespace that terminates a request
 * gets a shadow here that throws instead of dying with the test worker. Same
 * mechanism, and same production-code-does-not-move rule, as filter-input.php.
 *
 * The thrown GlsrExitException is caught by InteractsWithExits and by the
 * download tests; an uncaught one fails a test legibly instead of ending the
 * process as "Premature end of PHP process".
 */

namespace GeminiLabs\SiteReviews\Tests;

class GlsrExitException extends \Exception
{
    public int $status;

    public function __construct(int $status = 0)
    {
        parent::__construct("Exited with status {$status}");
        $this->status = $status;
    }
}

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Tests\GlsrExitException;

if (!function_exists(__NAMESPACE__.'\glsr_exit')) {
    function glsr_exit(int $status = 0): never
    {
        throw new GlsrExitException($status);
    }
}

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Tests\GlsrExitException;

if (!function_exists(__NAMESPACE__.'\glsr_exit')) {
    function glsr_exit(int $status = 0): never
    {
        throw new GlsrExitException($status);
    }
}

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Tests\GlsrExitException;

if (!function_exists(__NAMESPACE__.'\glsr_exit')) {
    function glsr_exit(int $status = 0): never
    {
        throw new GlsrExitException($status);
    }
}

namespace GeminiLabs\SiteReviews\Integrations\DuplicatePage;

use GeminiLabs\SiteReviews\Tests\GlsrExitException;

if (!function_exists(__NAMESPACE__.'\glsr_exit')) {
    function glsr_exit(int $status = 0): never
    {
        throw new GlsrExitException($status);
    }
}

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Tests\GlsrExitException;

if (!function_exists(__NAMESPACE__.'\glsr_exit')) {
    function glsr_exit(int $status = 0): never
    {
        throw new GlsrExitException($status);
    }
}
