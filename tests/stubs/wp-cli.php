<?php

/**
 * A working WP_CLI, for the suite.
 *
 * Not a signature-only stub: plugin/CLI.php calls into this and the tests read the answers back,
 * so it records rather than prints.
 *
 * WHY IT IS SAFE TO DECLARE THIS FOR THE WHOLE SUITE. Two different things are called "WP_CLI",
 * and only one of them is being faked here:
 *
 *   the CLASS      `class_exists('WP_CLI')` — what plugin/CLI.php gates on, and nothing else in
 *                  the plugin does.
 *   the CONSTANT   `defined('WP_CLI') && WP_CLI` — what Action Scheduler gates on, in four
 *                  places (ActionScheduler::init, ActionScheduler_WPCLI_Command,
 *                  migration/Controller, migration/Runner), to decide whether to register its own
 *                  commands and swap in a CLI progress bar.
 *
 * This file declares the class and NOT the constant, so Action Scheduler — which is bundled in
 * vendors/ and really does run in the suite — cannot tell the difference and behaves exactly as
 * it does on a web request.
 *
 * The real WP_CLI's success() writes "Success: …" to stdout and error() halts the process. Doing
 * either here would put noise in the test output and turn an assertion into an exit, so both are
 * captured.
 */
if (!class_exists('WP_CLI')) {
    class WP_CLI
    {
        /**
         * Every command registered, in registration order. NOT cleared between tests: the plugin
         * registers its command once, at load, from site-reviews.php.
         *
         * @var array[]
         */
        public static array $commands = [];

        /**
         * Everything the plugin has told the person running the command, in order, as
         * ['type' => 'success'|'error'|'warning'|'line'|'log', 'message' => string].
         *
         * @var array[]
         */
        public static array $messages = [];

        public static function add_command($name, $callable, $args = []): void
        {
            static::$commands[] = compact('name', 'callable', 'args');
        }

        public static function error($message, $exit = true): void
        {
            static::$messages[] = ['type' => 'error', 'message' => (string) $message];
            // The real one exits. A test that asserted on an exit would be asserting on nothing.
        }

        public static function line($message = ''): void
        {
            static::$messages[] = ['type' => 'line', 'message' => (string) $message];
        }

        public static function log($message): void
        {
            static::$messages[] = ['type' => 'log', 'message' => (string) $message];
        }

        /**
         * The messages only. The registered commands survive, because the registration they
         * record happened once, before any test ran.
         */
        public static function reset(): void
        {
            static::$messages = [];
        }

        public static function success($message): void
        {
            static::$messages[] = ['type' => 'success', 'message' => (string) $message];
        }

        public static function warning($message): void
        {
            static::$messages[] = ['type' => 'warning', 'message' => (string) $message];
        }

        /**
         * Only the successes, as plain strings — which is what the tests actually want to read.
         *
         * @return string[]
         */
        public static function successes(): array
        {
            return array_column(array_filter(
                static::$messages,
                fn ($message) => 'success' === $message['type']
            ), 'message');
        }
    }
}
