<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use Sinergi\BrowserDetector\Browser;

class SystemInfo
{
	/**
	 * @var int
	 */
	const PAD = 40;

	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $sysinfo = [];

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * Downloads the system info as a text file
	 *
	 * @param string $system_info
	 *
	 * @return void
	 */
	public function download( $system_info )
	{
		if( !current_user_can( 'install_plugins' ))return;

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="system-info.txt"' );

		echo wp_strip_all_tags( $system_info );

		exit;
	}

	/**
	 * Get all system info
	 *
	 * @return string
	 */
	public function getAll()
	{
		return trim(
			$this->getPlugin() .
			$this->getBrowser() .
			$this->getWebserver() .
			$this->getPHPConfig() .
			$this->getPHPExtensions() .
			$this->getWordpress() .
			$this->getWordpressMuplugins() .
			$this->getWordpressPlugins() .
			$this->getWordpressMultisitePlugins() .
			$this->getSettings()
		);
	}

	/**
	 * Get browser info
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getBrowser( $title = 'Browser Details' )
	{
		if( !$this->title( $title ))return;

		$browser = new Browser;
		$userAgent = $browser->getUserAgent();

		$name = esc_attr( $browser->getName() );
		$version = esc_attr( $browser->getVersion() );

		$this->sysinfo[ $title ]['Browser Name'] = sprintf( '%s %s', $name, $version );
		$this->sysinfo[ $title ]['Browser UA'] = esc_attr( $userAgent->getUserAgentString() );

		return $this->implode( $title );
	}

	/**
	 * Get plugin info
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getPlugin( $title = 'Plugin Details' )
	{
		if( !$this->title( $title ))return;

		$this->sysinfo[ $title ]['Current version'] = $this->app->version;
		$this->sysinfo[ $title ]['Previous version'] = glsr_resolve( 'Database' )->getOption( 'version_upgraded_from' );

		return $this->implode( $title );
	}

	/**
	 * Get WordPress configuration info
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getWordpress( $title = 'WordPress Configuration' )
	{
		if( !$this->title( $title ))return;

		global $wpdb;

		$theme = wp_get_theme();

		$wp_prefix_status = strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable';
		$language = get_option( 'WPLANG' );

		$this->sysinfo[ $title ]['Active Theme'] = sprintf( '%s v%s', $theme->Name, $theme->Version );
		$this->sysinfo[ $title ]['Home URL'] = home_url();
		$this->sysinfo[ $title ]['Language'] = $language ? $language : 'en_US';
		$this->sysinfo[ $title ]['Memory Limit'] = WP_MEMORY_LIMIT;
		$this->sysinfo[ $title ]['Multisite'] = is_multisite() ? 'Yes' : 'No';
		$this->sysinfo[ $title ]['Page For Posts ID'] = get_option( 'page_for_posts' );
		$this->sysinfo[ $title ]['Page On Front ID'] = get_option( 'page_on_front' );
		$this->sysinfo[ $title ]['Permalink Structure'] = get_option( 'permalink_structure', 'Default' );
		$this->sysinfo[ $title ]['Post Stati'] = implode( ', ', get_post_stati() );
		$this->sysinfo[ $title ]['Remote Post'] = $this->testRemotePost();
		$this->sysinfo[ $title ]['Show On Front'] = get_option( 'show_on_front' );
		$this->sysinfo[ $title ]['Site URL'] = site_url();
		$this->sysinfo[ $title ]['Table Prefix'] = sprintf( '%s (%d)', $wp_prefix_status, strlen( $wpdb->prefix ));
		$this->sysinfo[ $title ]['Timezone'] = get_option( 'timezone_string' );
		$this->sysinfo[ $title ]['Version'] = get_bloginfo( 'version' );
		$this->sysinfo[ $title ]['WP Debug'] = defined( 'WP_DEBUG' ) ? WP_DEBUG : 'Not set';
		$this->sysinfo[ $title ]['WP Max Upload Size'] = size_format( wp_max_upload_size() );
		$this->sysinfo[ $title ]['WP Memory Limit'] = WP_MEMORY_LIMIT;

		return $this->implode( $title );
	}

	/**
	 * Get all active WordPress multisite plugins
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getWordpressMultisitePlugins( $title = 'Network Active Plugins' )
	{
		if( !is_multisite() )return;

		$active = (array) get_site_option( 'active_sitewide_plugins', [] );

		if( !$this->title( $title ) || !count( $active ))return;

		$plugins = wp_get_active_network_plugins();

		foreach( $plugins as $plugin_path ) {

			if( !in_array( plugin_basename( $plugin_path ), $active ))continue;

			$plugin = get_plugin_data( $plugin_path );

			$this->sysinfo[ $title ][] = sprintf( '%s v%s', $plugin['Name'], $plugin['Version'] );
		}

		return $this->implode( $title );
	}

	/**
	 * Get all WordPress mu-plugins
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getWordpressMuplugins( $title = 'Must-Use Plugins' )
	{
		$auto_plugins = get_plugins( '/../' . basename( WPMU_PLUGIN_DIR ));
		$mu_plugins   = get_mu_plugins();

		$plugins = $this->formatPlugins( array_merge( $mu_plugins, $auto_plugins ));

		return $this->wordpressPlugins( $title, $plugins );
	}

	/**
	 * Get all active/inactive WordPress plugins
	 *
	 * @param string $active_title
	 * @param string $inactive_title
	 *
	 * @return null|string
	 */
	public function getWordpressPlugins( $active_title = 'Active Plugins', $inactive_title = 'Inactive Plugins' )
	{
		$plugins = get_plugins();

		if( !count( $plugins ))return;

		$active_plugins = (array) get_option( 'active_plugins', [] );

		$inactive = $this->formatPlugins( array_diff_key( $plugins, array_flip( $active_plugins )));
		$active   = $this->formatPlugins( array_diff_key( $plugins, $inactive ));

		$active_plugins   = $this->wordpressPlugins( $active_title, $active );
		$inactive_plugins = $this->wordpressPlugins( $inactive_title, $inactive );

		return $active_plugins . $inactive_plugins;
	}

	/**
	 * Get the webhost/webserver info
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getWebserver( $title = 'Server configuration' )
	{
		if( !$this->title( $title ))return;
		global $wpdb;
		$server_addr = filter_input( INPUT_SERVER, 'SERVER_ADDR' );
		if( strstr( $server_addr, ',' )) {
			$ipaddresses = explode( ',', $server_addr);
			$ipaddresses = array_map( 'trim', $ipaddresses );
			$server_addr = array_shift( $ipaddresses );
		}
		$webhost = !empty( $server_addr )
			? gethostbyaddr( $server_addr )
			: '';
		$this->sysinfo[ $title ]['Host Name'] = sprintf( '%s (%s)', $this->webhost(), $webhost );
		$this->sysinfo[ $title ]['MySQL Version'] = $wpdb->db_version();
		$this->sysinfo[ $title ]['PHP Version'] = PHP_VERSION;
		$this->sysinfo[ $title ]['Server Info'] = filter_input( INPUT_SERVER, 'SERVER_SOFTWARE' );
		$this->sysinfo[ $title ]['Server IP Address'] = filter_input( INPUT_SERVER, 'SERVER_ADDR' );
		return $this->implode( $title );
	}

	/**
	 * Get the PHP config
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getPHPConfig( $title = 'PHP Configuration' )
	{
		if( !$this->title( $title ))return;

		$this->sysinfo[ $title ]['Default Charset'] = ini_get( 'default_charset' );
		$this->sysinfo[ $title ]['Display Errors'] = ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A';
		$this->sysinfo[ $title ]['Max Execution Time'] = ini_get( 'max_execution_time' );
		$this->sysinfo[ $title ]['Max Input Nesting Level'] = ini_get( 'max_input_nesting_level' );
		$this->sysinfo[ $title ]['Max Input Vars'] = ini_get( 'max_input_vars' );
		$this->sysinfo[ $title ]['Memory Limit'] = ini_get( 'memory_limit' );
		$this->sysinfo[ $title ]['Post Max Size'] = ini_get( 'post_max_size' );
		$this->sysinfo[ $title ]['Session Cookie Path'] = esc_html( ini_get( 'session.cookie_path' ));
		$this->sysinfo[ $title ]['Session Name'] = esc_html( ini_get( 'session.name' ));
		$this->sysinfo[ $title ]['Session Save Path'] = esc_html( ini_get( 'session.save_path' ));
		$this->sysinfo[ $title ]['Session Use Cookies'] = ini_get( 'session.use_cookies' ) ? 'On' : 'Off';
		$this->sysinfo[ $title ]['Session Use Only Cookies'] = ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off';
		$this->sysinfo[ $title ]['Upload Max Filesize'] = ini_get( 'upload_max_filesize' );

		return $this->implode( $title );
	}

	/**
	 * Get the status of required PHP extensions
	 *
	 * @param string $title
	 *
	 * @return null|string
	 */
	public function getPHPExtensions( $title = 'PHP Extensions' )
	{
		if( !$this->title( $title ))return;

		$this->sysinfo[ $title ]['cURL'] = function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported';
		$this->sysinfo[ $title ]['fsockopen'] = function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported';
		$this->sysinfo[ $title ]['SOAP Client'] = class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed';
		$this->sysinfo[ $title ]['Suhosin'] = extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed';

		return $this->implode( $title );
	}

	/**
	 * Formats an array of plugins to [path] => [name version] and sort by value
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	protected function formatPlugins( array $plugins )
	{
		$plugins = array_map( function( $plugin ) {
			return sprintf( '%s v%s', $plugin['Name'], $plugin['Version'] );
		}, $plugins );

		natcasesort( $plugins );

		return $plugins;
	}

	/**
	 * Generates a string from a system info section array
	 *
	 * @param string $section
	 *
	 * @return null|string
	 */
	protected function implode( $section )
	{
		if( !isset( $this->sysinfo[ $section ] ))return;

		$strings = [];

		$strings[] = sprintf( '[%s]', strtoupper( $section ));

		foreach( $this->sysinfo[ $section ] as $key => $value ) {
			if( is_int( $key )) {
				$strings[] = " - {$value}";
				continue;
			}

			$strings[] = sprintf( '%s : %s', $this->pad( $key ), $value );
		}

		return implode( PHP_EOL, $strings ) . PHP_EOL . PHP_EOL;
	}

	/**
	 * Pad a string with periods to the required length
	 *
	 * @param string   $string
	 * @param int|null $pad
	 * @param string   $char
	 *
	 * @return string
	 */
	protected function pad( $string, $pad = null, $char = '.' )
	{
		$pad = $pad ?: static::PAD;

		if( strlen( $string ) === $pad ) {
			return $string;
		}

		return str_pad( "{$string} ", $pad, $char );
	}

	/**
	 * @return string
	 */
	protected function testRemotePost()
	{
		$response = wp_remote_post( 'https://api.wordpress.org/stats/php/1.0/' );

		return !is_wp_error( $response ) && in_array( $response['response']['code'], range( 200, 299 ))
			? 'Works'
			: 'Does not work';
	}

	/**
	 * Get the info title
	 *
	 * @param string $title
	 *
	 * @return bool
	 */
	protected function title( $title )
	{
		if( !$title ) {
			return false;
		}

		$this->sysinfo[ strtoupper( $title ) ] = [];

		return true;
	}

	/**
	 * Get the webhost name
	 *
	 * @return string
	 */
	protected function webhost()
	{
		$server_name = filter_input( INPUT_SERVER, 'SERVER_NAME' );
		$uname       = php_uname();

		if( filter_input( INPUT_SERVER, 'DH_USER' )) {
			$host = 'DreamHost';
		} elseif( defined( 'WPE_APIKEY' )) {
			$host = 'WP Engine';
		} elseif( defined( 'PAGELYBIN' )) {
			$host = 'Pagely';
		} elseif( DB_HOST == 'localhost:/tmp/mysql5.sock' ) {
			$host = 'ICDSoft';
		} elseif( DB_HOST == 'mysqlv5' ) {
			$host = 'NetworkSolutions';
		} elseif( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
			$host = 'iPage';
		} elseif( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
			$host = 'IPower';
		} elseif( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
			$host = 'MediaTemple Grid';
		} elseif( strpos( DB_HOST, '.pair.com' ) !== false ) {
			$host = 'pair Networks';
		} elseif( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
			$host = 'Rackspace Cloud';
		} elseif( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
			$host = 'SysFix.eu Power Hosting';
		} elseif( strpos( $server_name, 'Flywheel' ) !== false ) {
			$host = 'Flywheel';
		} elseif( strpos( $uname, 'bluehost.com' ) !== false ) {
			$host = 'Bluehost';
		} elseif( strpos( $uname, 'secureserver.net') !== false ) {
			$host = 'GoDaddy';
		} elseif( strpos( $uname, '.inmotionhosting.com') !== false ) {
			$host = 'InMotion Hosting';
		} elseif( strpos( $uname, '.ovh.net') !== false ) {
			$host = 'OVH';
		} elseif( strpos( $uname, '.accountservergroup.com ') !== false ) {
			$host = 'Site5';
		} elseif( strpos( $uname, '.stratoserver.net ') !== false ) {
			$host = 'STRATO';
		} else {
			$host = sprintf( '%s, %s', DB_HOST, $server_name );
		}

		return $host;
	}

	/**
	 * Format an array of passed WordPress plugins
	 *
	 * @param string $title
	 *
	 * @return string|null
	 */
	protected function wordpressPlugins( $title, array $plugins )
	{
		if( !count( $plugins ) || !$this->title( $title ))return;

		$pad = max( array_map( 'strlen', $plugins ));

		foreach( $plugins as $path => $plugin ) {
			$this->sysinfo[ $title ][] = sprintf( '%s : %s', $this->pad( $plugin, $pad ), $path );
		}

		return $this->implode( $title );
	}

	/**
	 * Get the plugin settings
	 *
	 * @return null|string
	 */
	protected function getSettings( $title = 'Plugin Settings' )
	{
		if( !$this->title( $title ))return;
		$settings = glsr_get_options();
		foreach( ['key','secret'] as $key ) {
			if( isset( $settings['reviews-form']['recaptcha'][$key] )) {
				$settings['reviews-form']['recaptcha'][$key] = str_repeat( '*', 10 );
			}
		}
		$helper = glsr_resolve( 'Helper' );
		$settings = $helper->flattenArray( $settings );
		foreach( $settings as $key => $value ) {
			if( $helper->startsWith( 'strings', $key ) && $helper->endsWith( 'id', $key ))continue;
			$value = htmlspecialchars( trim( preg_replace('/\s\s+/', '\\n', $value )), ENT_QUOTES, 'UTF-8' );
			$this->sysinfo[$title][$key] = $value;
		}
		return $this->implode( $title );
	}
}
