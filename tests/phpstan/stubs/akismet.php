<?php

namespace {
    class Akismet
    {
        public static function get_api_key()
        {
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
        }
    }
}
