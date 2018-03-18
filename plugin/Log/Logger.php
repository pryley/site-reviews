<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Log;

use GeminiLabs\SiteReviews\Log\LoggerInterface;
use GeminiLabs\SiteReviews\Log\LogLevel;

class Logger implements LoggerInterface
{
	protected $file;
	protected $id;
	protected $log;
	protected $prefix;

	/**
	 * Load/Create the log file
	 *
	 * @param string $filename
	 * @param string $prefix
	 *
	 * @return Logger
	 */
	public static function file( $filename, $prefix )
	{
		$logger = new static;

		$logger->file   = $filename;
		$logger->log    = file_exists( $filename ) ? file_get_contents( $filename ) : 'No log';
		$logger->prefix = $prefix;

		return $logger;
	}

	public function __toString()
	{
		return $this->log;
	}

	/**
	 * Download the log file
	 *
	 * @return void
	 */
	public function download()
	{
		if( !current_user_can( 'install_plugins' ))return;

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="log.txt"' );

		echo html_entity_decode( wp_strip_all_tags( $this->log ));

		exit;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function emergency( $message, array $context = [] )
	{
		$this->log( LogLevel::EMERGENCY, $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function alert( $message, array $context = [] )
	{
		$this->log( LogLevel::ALERT, $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function critical( $message, array $context = [] )
	{
		$this->log( LogLevel::CRITICAL, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function error( $message, array $context = [] )
	{
		$this->log( LogLevel::ERROR, $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function warning( $message, array $context = [] )
	{
		$this->log( LogLevel::WARNING, $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function notice( $message, array $context = [] )
	{
		$this->log( LogLevel::NOTICE, $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function info( $message, array $context = [] )
	{
		$this->log( LogLevel::INFO, $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function debug( $message, array $context = [] )
	{
		$this->log( LogLevel::DEBUG, $message, $context );
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function log( $level, $message, array $context = [] )
	{
		// Check if logging is enabled
		if( glsr_resolve( 'Database' )->getOption( 'logging' ) != 1 )return;

		$reflection = new \ReflectionClass( __NAMESPACE__ . '\LogLevel' );

		$levels = $reflection->getConstants();

		$custom_levels = array_intersect( apply_filters( 'site-reviews/logger/levels', $levels ), $levels );
		$custom_levels = array_keys( array_flip( $custom_levels ));

		$levels = empty( $custom_levels ) ? $levels : $custom_levels;

		// Check if log level is allowed.
		if( !in_array( $level, $levels, true ))return;

		$date    = get_date_from_gmt( gmdate('Y-m-d H:i:s') );
		$level   = strtoupper( $level );
		$message = $this->interpolate( $message, $context );
		$entry   = "[{$date}] {$level}: $message" . PHP_EOL;

		if( $this->log == 'Log is empty' ) {
			file_put_contents( $this->file, '' );
		}

		file_put_contents( $this->file, $entry, FILE_APPEND|LOCK_EX );
	}

	/**
	 * Wrapper for print_r()
	 *
	 * @return void
	 */
	public function display()
	{
		call_user_func_array( [ $this, 'print_r' ], func_get_args() );
	}

	/**
	 * Capture print_r() output as variable
	 *
	 * @return string
	 */
	public function capture()
	{
		ob_start();
		call_user_func_array( [ $this, 'display' ], func_get_args() );
		return ob_get_clean();
	}

	/**
	 * @param array $array
	 * @return string
	 */
	public function print_array( $array )
	{
		return htmlspecialchars( print_r( $array, true ), ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Wrapper for print_r()
	 *
	 * @param mixed $value ...
	 *
	 * @return void
	 */
	public function print_r( $value )
	{
		if( func_num_args() == 1 ) {
			printf( '<div class="print__r"><pre>%s</pre></div>', $this->print_array( $value ));
		}
		else {
			echo '<div class="print__r_group">';
			foreach( func_get_args() as $param ) {
				$this->print_r( $param );
			}
			echo '</div>';
		}
	}

	/**
	 * Clear the log file
	 *
	 * @return void
	 */
	public function clear()
	{
		$log = 'Log is empty';

		file_put_contents( $this->file, $log );

		$this->log = $log;
	}

	/**
	 * Interpolates context values into the message placeholders.
	 *
	 * @param string $message
	 * @param array  $context
	 *
	 * @return array|string
	 */
	protected function interpolate( $message, array $context = [] )
	{
		if( is_array( $message )) {
			return $this->print_array( $message );
		}

		// build a replacement array with braces around the context keys
		$replace = [];

		foreach( $context as $key => $val ) {

			if( is_object( $val ) && get_class( $val ) === 'DateTime' ) {
				$val = $val->format( 'Y-m-d H:i:s' );
			}
			else if( is_object( $val ) || is_array( $val )) {
				$val = json_encode( $val );
			}
			else if( is_resource( $val )) {
				$val = (string) $val;
			}

			$replace['{' . $key . '}'] = $val;
		}

		// interpolate replacement values into the message and return
		return strtr( $message, $replace );
	}
}
