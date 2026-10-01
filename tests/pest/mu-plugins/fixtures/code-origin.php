<?php

/*
 * A site owner's own code, outside the plugin, for the tests of the Console's "[CODE SNIPPET]" tag.
 * WordPress does not load a subdirectory of mu-plugins; the test requires this file itself.
 */

defined('ABSPATH') || exit;

return [
    'call' => function (): void {
        (new \GeminiLabs\SiteReviews\Commands\ToggleVerified(new \GeminiLabs\SiteReviews\Request(['post_id' => 0])))->handle();
    },
    'log' => function (string $message): void {
        glsr_log()->error($message);
    },
    'throw' => function (): void {
        throw new \RuntimeException('thrown in a snippet');
    },
];
