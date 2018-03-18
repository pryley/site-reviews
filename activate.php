<?php

defined( 'WPINC' ) || die;

/**
 * Check for minimum system requirments on plugin activation
 * @version 3.0.0
 */
class GL_Plugin_Check_v3
{
	const MIN_PHP_VERSION = '5.6.0';
	const MIN_WORDPRESS_VERSION = '4.7.0';

	/**
	 * @var string
	 */
	protected $file;

	/**
	 * @var array
	 */
	protected $versions;

	/**
	 * @param string $file
	 */
	public function __construct( $file, array $versions = array() )
	{
		$this->file = realpath( $file );
		$this->versions = wp_parse_args( $versions, array(
			'php' => static::MIN_PHP_VERSION,
			'wordpress' => static::MIN_WORDPRESS_VERSION,
		));
	}

	/**
	 * @return bool
	 */
	public function canProceed()
	{
		if( $this->isValid() ) {
			return true;
		}
		add_action( 'activated_plugin', array( $this, 'deactivate' ));
		add_action( 'admin_notices', array( $this, 'deactivate' ));
		return false;
	}

	/**
	 * @return bool
	 */
	public function isPhpValid()
	{
		return !version_compare( PHP_VERSION, $this->versions['php'], '<' );
	}

	/**
	 * @return bool
	 */
	public function isValid()
	{
		return $this->isPhpValid() && $this->isWpValid();
	}

	/**
	 * @return bool
	 */
	public function isWpValid()
	{
		global $wp_version;
		return !version_compare( $wp_version, $this->versions['wordpress'], '<' );
	}

	/**
	 * @param string $plugin
	 * @return void
	 */
	public function deactivate( $plugin )
	{
		if( $this->isValid() )return;
		$pluginSlug = plugin_basename( $this->file );
		if( $plugin == $pluginSlug ) {
			$this->redirect(); //exit
		}
		$pluginData = get_file_data( $this->file, array( 'name' => 'Plugin Name' ), 'plugin' );
		deactivate_plugins( $pluginSlug );
		$this->printNotice( $pluginData['name'] );
	}

	/**
	 * @return array
	 */
	protected function getMessages()
	{
		return array(
			__( 'The %s plugin was deactivated.', 'site-reviews' ),
			__( 'This plugin requires %s or greater in order to work properly.', 'site-reviews' ),
			__( 'Please contact your hosting provider or server administrator to upgrade the version of PHP on your server (your server is running PHP version %s), or try to find an alternative plugin.', 'site-reviews' ),
			__( 'PHP version', 'site-reviews' ),
			__( 'WordPress version', 'site-reviews' ),
			__( 'Update WordPress', 'site-reviews' ),
			__( 'You can use the %s plugin to restore %s to the previous version.', 'site-reviews' ),
		);
	}

	/**
	 * @param string $pluginName
	 * @return void
	 */
	protected function printNotice( $pluginName )
	{
		$noticeTemplate = '<div id="message" class="notice notice-error error is-dismissible"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>';
		$messages = $this->getMessages();
		$rollbackMessage = sprintf( '<strong>'.$messages[6].'</strong>', '<a href="https://wordpress.org/plugins/wp-rollback/">WP Rollback</a>', $pluginName );
		if( !$this->isPhpValid() ) {
			printf( $noticeTemplate,
				sprintf( $messages[0], $pluginName ),
				sprintf( $messages[1], $messages[3].' '.$this->versions['php'] ),
				sprintf( $messages[2], PHP_VERSION ).'</p><p>'.$rollbackMessage
			);
		}
		else if( !$this->isWpValid() ) {
			printf( $noticeTemplate,
				sprintf( $messages[0], $pluginName ),
				sprintf( $messages[1], $messages[4].' '.$this->versions['wordpress'] ),
				$rollbackMessage.'</p><p>'.sprintf( '<a href="%s">%s</a>', admin_url( 'update-core.php' ), $messages[5] )
			);
		}
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
}
