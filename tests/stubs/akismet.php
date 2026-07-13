<?php

namespace {
    /**
     * Akismet, as far as Site Reviews is concerned: an API key, and a yes/no on a submission.
     *
     * This is a WORKING fake rather than a signature-only stub, and it has to be. The other
     * stubs exist so that `is_callable()` and `class_exists()` answer correctly and the
     * integration code can be reached; nothing calls into them. AkismetValidator does call in:
     * it assembles a payload from the request and the whole of $_SERVER, hands it over, and
     * reads a verdict out of the reply. A method returning null would make `$response[1]`
     * fatal, and would leave nothing to assert about what was sent.
     *
     * The contract is not invented. Akismet::http_post() returns a two-member array of
     * [headers, body], and for a comment-check the body is the string 'true' for spam and
     * 'false' for ham — which is exactly what AkismetValidator::validateAkismet() reads.
     */
    class Akismet
    {
        /** @var string The key the site has pasted in. Empty means Akismet is not set up. */
        public static $apiKey = '';

        /** @var string The last request body posted, so a test can see what left the site. */
        public static $lastRequest = '';

        /** @var string 'true' = spam, 'false' = ham. */
        public static $verdict = 'false';

        public static function get_api_key()
        {
            return static::$apiKey;
        }

        /**
         * Make a POST request to the Akismet API.
         *
         * @param string $request The body of the request.
         * @param string $path The path for the request.
         * @param string $ip The specific IP address to hit.
         * @return array A two-member array consisting of the headers and the response body, both empty in the case of a failure.
         */
        public static function http_post($request, $path, $ip = \null)
        {
            static::$lastRequest = (string) $request;

            return ['', static::$verdict];
        }

        /**
         * Back to a site that has never heard of Akismet.
         */
        public static function reset(): void
        {
            static::$apiKey = '';
            static::$lastRequest = '';
            static::$verdict = 'false';
        }
    }
}
