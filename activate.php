<?php

defined( 'WPINC' ) || die;

/**
 * Checks for minimum system requirments on plugin activation
 * @version 1.0.1
 */
class GL_Plugin_Check_v1
{
	const MIN_PHP_VERSION = '5.6.0';
	const MIN_WORDPRESS_VERSION = '4.7.0';

	/**
	 * @var string
	 */
	protected static $file;

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * @var object
	 */
	protected static $versions;

	/**
	 * @param string $version
	 * @return bool
	 */
	public static function isPhpValid( $version = '' )
	{
		if( !empty( $version )) {
			static::normalize( array( 'php' => $version ));
		}
		return !version_compare( PHP_VERSION, static::$versions->php, '<' );
	}

	/**
	 * @return bool
	 */
	public static function isValid( array $args = array() )
	{
		if( !empty( $args )) {
			static::normalize( $args );
		}
		return static::isPhpValid() && static::isWpValid();
	}

	/**
	 * @param string $version
	 * @return bool
	 */
	public static function isWpValid( $version = '' )
	{
		global $wp_version;
		if( !empty( $version )) {
			static::normalize( array( 'wordpress' => $version ));
		}
		return !version_compare( $wp_version, static::$versions->wordpress, '<' );
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	public static function shouldDeactivate( $file, array $args = array() )
	{
		if( empty( static::$instance )) {
			static::$file = realpath( $file );
			static::$instance = new static;
			static::$versions = static::normalize( $args );
		}
		if( !static::isValid() ) {
			add_action( 'activated_plugin', array( static::$instance, 'deactivate' ));
			add_action( 'admin_notices', array( static::$instance, 'deactivate' ));
			return true;
		}
		return false;
	}

	/**
	 * @param string $plugin
	 * @return void
	 */
	public function deactivate( $plugin )
	{
		if( static::isValid() )return;
		$pluginSlug = plugin_basename( static::$file );
		if( $plugin == $pluginSlug ) {
			$this->redirect(); //exit
		}
		$pluginData = get_file_data( static::$file, array( 'name' => 'Plugin Name' ), 'plugin' );
		deactivate_plugins( $pluginSlug );
		$this->printNotice( $pluginData['name'] );
	}

	/**
	 * @return object
	 */
	protected static function normalize( array $args = array() )
	{
		return (object)wp_parse_args( $args, array(
			'php' => static::MIN_PHP_VERSION,
			'wordpress' => static::MIN_WORDPRESS_VERSION,
		));
	}

	/**
	 * @return void
	 */
	protected function redirect()
	{
		wp_safe_redirect( self_admin_url( sprintf( 'plugins.php?plugin_status=%s&paged=%s&s=%s',
			filter_input( INPUT_GET, 'plugin_status' ),
			filter_input( INPUT_GET, 'paged' ),
			filter_input( INPUT_GET, 's' )
		)));
		exit;
	}

	/**
	 * @param string $pluginName
	 * @return void
	 */
	protected function printNotice( $pluginName )
	{
		$noticeTemplate = '<div id="message" class="notice notice-error error is-dismissible"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>';
		$messages = array(
			__( 'The %s plugin was deactivated.', 'site-reviews' ),
			__( 'Sorry, this plugin requires %s or greater in order to work properly.', 'site-reviews' ),
			__( 'Please contact your hosting provider or server administrator to upgrade the version of PHP on your server (your server is running PHP version %s), or try to find an alternative plugin.', 'site-reviews' ),
			__( 'PHP version', 'site-reviews' ),
			__( 'WordPress version', 'site-reviews' ),
			__( 'Update WordPress', 'site-reviews' ),
		);
		if( !static::isPhpValid() ) {
			printf( $noticeTemplate,
				sprintf( $messages[0], $pluginName ),
				sprintf( $messages[1], $messages[3].' '.static::$versions->php ),
				sprintf( $messages[2], PHP_VERSION )
			);
		}
		else if( !static::isWpValid() ) {
			printf( $noticeTemplate,
				sprintf( $messages[0], $pluginName ),
				sprintf( $messages[1], $messages[4].' '.static::$versions->wordpress ),
				sprintf( '<a href="%s">%s</a>', admin_url( 'update-core.php' ), $messages[5] )
			);
		}
	}
}
