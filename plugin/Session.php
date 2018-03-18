<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 *
 * This class is derived from WP Session Manager (1.2.0)
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use PasswordHash;

class Session
{
	const SESSION_COOKIE = '_glsr_session';

	/**
	 * @var int
	 */
	protected $expiryTimestamp;

	/**
	 * @var int
	 */
	protected $expiryTimestampReset;

	/**
	 * @var array
	 */
	protected $sessionData;

	/**
	 * @var string
	 */
	protected $sessionId;

	public function __construct()
	{
		if( $cookieId = filter_input( INPUT_COOKIE, static::SESSION_COOKIE )) {
			$cookie = explode( '||', stripslashes( $cookieId ));
			$this->sessionId = preg_replace( '/[^A-Za-z0-9_]/', '', $cookie[0] );
			$this->expiryTimestamp = absint( $cookie[1] );
			$this->expiryTimestampReset = absint( $cookie[2] );
			if( time() > $this->expiryTimestampReset ) {
				$this->setCookieExpiration();
			}
		}
		else {
			$this->sessionId = $this->generateSessionId();
			$this->setCookieExpiration();
		}
		$this->getSessionData();
		$this->setCookie();
		add_action( 'site-reviews/schedule/session/purge', [$this, 'deleteExpiredSessions'] );
	}

	/**
	 * @return void
	 */
	public function clear()
	{
		$this->setCookieExpiration();
		$this->regenerateSessionId( 'and delete session!' );
	}

	/**
	 * @return int|false
	 */
	public function deleteAllSessions()
	{
		global $wpdb;
		return $wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '".static::SESSION_COOKIE."_%'"
		);
	}

	/**
	 * @param int $limit
	 * @return void
	 */
	public function deleteExpiredSessions( $limit = 1000 )
	{
		global $wpdb;
		if( $expiredSessions = implode( "','", $this->getExpiredSessions( $limit ))) {
			$wpdb->query(
				"DELETE FROM {$wpdb->options} WHERE option_name IN ('{$expiredSessions}')"
			);
		}
	}

	/**
	 * @param string $key
	 * @param string|array $fallback
	 * @param bool|string $unset
	 * @return string|array
	 */
	public function get( $key, $fallback = '', $unset = false )
	{
		$key = sanitize_key( $key );
		$value = isset( $this->sessionData[$key] )
			? maybe_unserialize( $this->sessionData[$key] )
			: $fallback;
		if( isset( $this->sessionData[$key] ) && $unset ) {
			unset( $this->sessionData[$key] );
			$this->updateSession();
		}
		return $value;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function set( $key, $value )
	{
		$key = sanitize_key( $key );
		$this->sessionData[$key] = maybe_serialize( $value );
		$this->updateSession();
		return $this->sessionData[$key];
	}

	/**
	 * @return void
	 */
	protected function createSession()
	{
		add_option( $this->getSessionId(), $this->sessionData, '', false );
		add_option( $this->getSessionId( 'expires' ), $this->expiryTimestamp, '', false );
	}

	/**
	 * @return void
	 */
	protected function deleteSession()
	{
		delete_option( $this->getSessionId() );
		delete_option( $this->getSessionId( 'expires' ));
	}

	/**
	 * @return string
	 */
	protected function generateSessionId()
	{
		return md5(( new PasswordHash( 8, false ))->get_random_bytes( 32 ));
	}

	/**
	 * @param int $limit
	 * @return array
	 */
	protected function getExpiredSessions( $limit )
	{
		global $wpdb;
		$expiredSessions = [];
		$sessions = $wpdb->get_results(
			"SELECT option_name AS name, option_value AS expiration " .
			"FROM {$wpdb->options} " .
			"WHERE option_name LIKE '".static::SESSION_COOKIE."_expires_%' " .
			"ORDER BY option_value ASC " .
			"LIMIT 0, ".absint( $limit )
		);
		if( !empty( $sessions )) {
			$now = time();
			foreach( $sessions as $session ) {
				if( $now <= $session->expiration )continue;
				$expiredSessions[] = $session->name;
				$expiredSessions[] = str_replace( '_expires_', '_', $session->name );
			}
		}
		return $expiredSessions;
	}

	/**
	 * @param string $separator
	 * @return string
	 */
	protected function getSessionId( $separator = '' )
	{
		return implode( '_', array_filter( [static::SESSION_COOKIE, $separator, $this->sessionId] ));
	}

	/**
	 * @return array
	 */
	protected function getSessionData()
	{
		return $this->sessionData = (array) get_option( $this->getSessionId(), [] );
	}

	/**
	 * @param bool|string $deleteOld
	 * @return void
	 */
	protected function regenerateSessionId( $deleteOld = false )
	{
		if( $deleteOld ) {
			$this->deleteSession();
		}
		$this->sessionId = $this->generateSessionId();
		$this->setCookie();
	}

	/**
	 * @return void
	 */
	protected function setCookie()
	{
		if( headers_sent() )return;
		$cookie = $this->sessionId.'||'.$this->expiryTimestamp.'||'.$this->expiryTimestampReset;
		$cookiePath = preg_replace( '|https?://[^/]+|i', '', trailingslashit( (string) get_option( 'home' )));
		setcookie( static::SESSION_COOKIE, $cookie, $this->expiryTimestamp, $cookiePath );
	}

	/**
	 * @return void
	 */
	protected function setCookieExpiration()
	{
		$this->expiryTimestampReset = time() + (24 * 60); // 24 minutes
		$this->expiryTimestamp = time() + (30 * 60); // 30 minutes
	}

	/**
	 * @return void
	 */
	protected function updateSession()
	{
		if( false === get_option( $this->getSessionId() )) {
			$this->createSession();
		}
		else {
			update_option( $this->getSessionId(), $this->sessionData, false );
		}
	}
}
