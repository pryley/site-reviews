<?php

namespace GeminiLabs\SiteReviews\Modules;

use DateTime;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Session;
use ReflectionClass;
use Throwable;

class Console
{
	const DEBUG_0 = 'debug';         // Detailed debug information
	const INFO_1 = 'info';           // Interesting events
	const NOTICE_2 = 'notice';       // Normal but significant events
	const WARNING_3 = 'warning';     // Exceptional occurrences that are not errors
	const ERROR_4 = 'error';         // Runtime errors that do not require immediate action
	const CRITICAL_5 = 'critical';   // Critical conditions
	const ALERT_6 = 'alert';         // Action must be taken immediately
	const EMERGENCY_7 = 'emergency'; // System is unusable

	protected $file;
	protected $log;
	protected $onceSessionKey = 'glsr_log_once';

	public function __construct()
	{
		$this->file = glsr()->path( 'console.log' );
		$this->log = file_exists( $this->file )
			? file_get_contents( $this->file )
			: '';
		$this->reset();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->get();
	}

	/**
	 * Action must be taken immediately
	 * Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function alert( $message, array $context = [] )
	{
		return $this->log( static::ALERT_6, $message, $context );
	}

	/**
	 * @return void
	 */
	public function clear()
	{
		$this->log = '';
		file_put_contents( $this->file, $this->log );
	}

	/**
	 * Critical conditions
	 * Example: Application component unavailable, unexpected exception
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function critical( $message, array $context = [] )
	{
		return $this->log( static::CRITICAL_5, $message, $context );
	}

	/**
	 * Detailed debug information
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function debug( $message, array $context = [] )
	{
		return $this->log( static::DEBUG_0, $message, $context );
	}

	/**
	 * System is unusable
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function emergency( $message, array $context = [] )
	{
		return $this->log( static::EMERGENCY_7, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically be logged and monitored
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function error( $message, array $context = [] )
	{
		return $this->log( static::ERROR_4, $message, $context );
	}

	/**
	 * @return string
	 */
	public function get()
	{
		return empty( $this->log )
			? __( 'Console is empty', 'site-reviews' )
			: $this->log;
	}

	/**
	 * @return int
	 */
	public function getLevel()
	{
		return intval( apply_filters( 'site-reviews/console/level', 2 ));
	}

	/**
	 * @return array
	 */
	public function getLevels()
	{
		return array_values(( new ReflectionClass( __CLASS__ ))->getConstants() );
	}

	/**
	 * @return string
	 */
	public function humanLevel()
	{
		$level = $this->getLevel();
		return sprintf( '%s (%d)', glsr_get( $this->getLevels(), $level, 'unknown' ), $level );
	}

	/**
	 * @param null|string $valueIfEmpty
	 * @return string
	 */
	public function humanSize( $valueIfEmpty = null )
	{
		$bytes = $this->size();
		if( empty( $bytes ) && is_string( $valueIfEmpty )) {
			return $valueIfEmpty;
		}
		$exponent = floor( log( max( $bytes, 1 ), 1024 ));
		return round( $bytes / pow( 1024, $exponent ), 2 ).' '.['bytes','KB','MB','GB'][$exponent];
	}

	/**
	 * Interesting events
	 * Example: User logs in, SQL logs
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function info( $message, array $context = [] )
	{
		return $this->log( static::INFO_1, $message, $context );
	}

	/**
	 * @param mixed $level
	 * @param mixed $message
	 * @param array $context
	 * @param string $backtraceLine
	 * @return static
	 */
	public function log( $level, $message, $context = [], $backtraceLine = '' )
	{
		if( empty( $backtraceLine )) {
			$backtraceLine = $this->getBacktraceLine();
		}
		if( $this->canLogEntry( $level, $backtraceLine )) {
			$context = glsr( Helper::class )->consolidateArray( $context );
			$backtraceLine = $this->normalizeBacktraceLine( $backtraceLine );
			$message = $this->interpolate( $message, $context );
			$entry = $this->buildLogEntry( $level, $message, $backtraceLine );
			file_put_contents( $this->file, $entry.PHP_EOL, FILE_APPEND|LOCK_EX );
			apply_filters( 'console', $message, $level, $backtraceLine ); // Show in Blackbar plugin if installed
			$this->reset();
		}
		return $this;
	}

	/**
	 * @return void
	 */
	public function logOnce()
	{
		$once = glsr( Session::class )->get( $this->onceSessionKey, [], true );
		$once = glsr( Helper::class )->consolidateArray( $once );
		$levels = $this->getLevels();
		foreach( $once as $entry ) {
			if( !in_array( glsr_get( $entry, 'level' ), $levels ))continue;
			$level = glsr_get( $entry, 'level' );
			$message = glsr_get( $entry, 'message' );
			$backtraceLine = glsr_get( $entry, 'backtrace' );
			$this->log( $level, $message, [], $backtraceLine );
		}
	}

	/**
	 * Normal but significant events
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function notice( $message, array $context = [] )
	{
		return $this->log( static::NOTICE_2, $message, $context );
	}

	/**
	 * @param string $level
	 * @param string $handle
	 * @param mixed $data
	 * @return void
	 */
	public function once( $level, $handle, $data )
	{
		$once = glsr( Session::class )->get( $this->onceSessionKey, [] );
		$once = glsr( Helper::class )->consolidateArray( $once );
		$filtered = array_filter( $once, function( $entry ) use( $level, $handle ) {
			return glsr_get( $entry, 'level' ) == $level
				&& glsr_get( $entry, 'handle' ) == $handle;
		});
		if( !empty( $filtered ))return;
		$once[] = [
			'backtrace' => $this->getBacktraceLineFromData( $data ),
			'handle' => $handle,
			'level' => $level,
			'message' => '[RECURRING] '.$this->getMessageFromData( $data ),
		];
		glsr( Session::class )->set( $this->onceSessionKey, $once );
	}

	/**
	 * @return int
	 */
	public function size()
	{
		return file_exists( $this->file )
			? filesize( $this->file )
			: 0;
	}

	/**
	 * Exceptional occurrences that are not errors
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function warning( $message, array $context = [] )
	{
		return $this->log( static::WARNING_3, $message, $context );
	}

	/**
	 * @param array $backtrace
	 * @param int $index
	 * @return string
	 */
	protected function buildBacktraceLine( $backtrace, $index )
	{
		return sprintf( '%s:%s',
			glsr_get( $backtrace, $index.'.file' ), // realpath
			glsr_get( $backtrace, $index.'.line' )
		);
	}

	/**
	 * @param string $level
	 * @param mixed $message
	 * @param string $backtraceLine
	 * @return string
	 */
	protected function buildLogEntry( $level, $message, $backtraceLine = '' )
	{
		if( !empty( $backtraceLine )) {
			$backtraceLine = '|'.$backtraceLine;
		}
		return sprintf( '[%s%s] %s: %s',
			current_time( 'mysql' ),
			$backtraceLine,
			strtoupper( $level ),
			$message
		);
	}

	/**
	 * @param string $level
	 * @return bool
	 */
	protected function canLogEntry( $level, $backtraceLine )
	{
		$levelIndex = array_search( $level, $this->getLevels(), true );
		$result = $levelIndex !== false;
		if( strpos( $backtraceLine, glsr()->path() ) === false ) {
			return $result; // triggered outside of the plugin
		}
		return $result && $levelIndex >= $this->getLevel();
	}

	/**
	 * @return void|string
	 */
	protected function getBacktraceLine()
	{
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 6 );
		$search = array_search( 'log', glsr_array_column( $backtrace, 'function' ));
		if( $search !== false ) {
			$index = glsr_get( $backtrace, ( $search + 2 ).'.function' ) == '{closure}'
				? $search + 4
				: $search + 1;
			return $this->buildBacktraceLine( $backtrace, $index );
		}
		return 'Unknown';
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	protected function getBacktraceLineFromData( $data )
	{
		$backtrace = $data instanceof Throwable
			? $data->getTrace()
			: debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );
		return $this->buildBacktraceLine( $backtrace, 0 );
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	protected function getMessageFromData( $data )
	{
		return $data instanceof Throwable
			? $this->normalizeThrowableMessage( $data->getMessage() )
			: print_r( $data, 1 );
	}

	/**
	 * Interpolates context values into the message placeholders
	 * @param mixed $message
	 * @param array $context
	 * @return string
	 */
	protected function interpolate( $message, $context = [] )
	{
		if( $this->isObjectOrArray( $message ) || !is_array( $context )) {
			return print_r( $message, true );
		}
		$replace = [];
		foreach( $context as $key => $value ) {
			$replace['{'.$key.'}'] = $this->normalizeValue( $value );
		}
		return strtr( $message, $replace );
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	protected function isObjectOrArray( $value )
	{
		return is_object( $value ) || is_array( $value );
	}

	/**
	 * @param string $backtraceLine
	 * @return string
	 */
	protected function normalizeBacktraceLine( $backtraceLine )
	{
		$search = [
			glsr()->path( 'plugin/' ),
			glsr()->path( 'plugin/', false ),
			trailingslashit( glsr()->path() ),
			trailingslashit( glsr()->path( '', false )),
			WP_CONTENT_DIR,
			ABSPATH
		];
		return str_replace( array_unique( $search ), '', $backtraceLine );
	}

	/**
	 * @param string $message
	 * @return string
	 */
	protected function normalizeThrowableMessage( $message )
	{
		$calledIn = strpos( $message, ', called in' );
		return $calledIn !== false
			? substr( $message, 0, $calledIn )
			: $message;
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	protected function normalizeValue( $value )
	{
		if( $value instanceof DateTime ) {
			$value = $value->format( 'Y-m-d H:i:s' );
		}
		else if( $this->isObjectOrArray( $value )) {
			$value = json_encode( $value );
		}
		return (string)$value;
	}

	/**
	 * @return void
	 */
	protected function reset()
	{
		if( $this->size() <= pow( 1024, 2 ) / 8 )return;
		$this->clear();
		file_put_contents(
			$this->file,
			$this->buildLogEntry(
				static::INFO_1,
				__( 'Console was automatically cleared (128 KB maximum size)', 'site-reviews' )
			)
		);
	}
}
