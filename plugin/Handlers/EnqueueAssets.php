<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Commands\EnqueueAssets as Command;

class EnqueueAssets
{
	/**
	 * @var array
	 */
	protected $dependencies;

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$this->dependencies = glsr_resolve( 'Html' )->getDependencies();
		$ajaxNonce = wp_create_nonce( glsr_app()->id.'-ajax-nonce' );
		$variables = [
			'action'  => glsr_app()->prefix.'_action',
			'ajaxnonce' => $ajaxNonce,
			'ajaxpagination' => ['#wpadminbar','.site-navigation-fixed'],
			'ajaxurl' => add_query_arg( '_nonce', $ajaxNonce, admin_url( 'admin-ajax.php' )),
		];
		if( is_admin() ) {
			$this->enqueueAdmin( $command );
			$this->enqueueTinymce( $command );
			$variables = array_merge( $variables, [
				'shortcodes' => $this->localizeShortcodes(),
				'tinymce' => [
					'glsr_shortcode' => glsr_app()->url.'assets/js/mce-plugin.js',
				],
			]);
		}
		else {
			$this->enqueuePolyfillService( $command );
			$this->enqueuePublic( $command );
		}
		wp_localize_script( $command->handle, 'site_reviews', apply_filters( 'site-reviews/enqueue/localize', $variables ));
	}

	/**
	 * @return void
	 */
	public function enqueueAdmin( Command $command )
	{
		$screen = glsr_current_screen();
		$dependencies = array_merge( $this->dependencies, [
			'jquery',
			'jquery-ui-sortable',
			'underscore',
			'wp-util',
		]);
		wp_enqueue_style(
			$command->handle,
			$command->url.'css/site-reviews-admin.css',
			[],
			$command->version
		);
		if( !( $screen->post_type == App::POST_TYPE
			|| $screen->base == 'post'
			|| $screen->id == 'dashboard'
			|| $screen->id == 'widgets'
		))return;
		wp_enqueue_script(
			$command->handle,
			$command->url.'js/site-reviews-admin.js',
			$dependencies,
			$command->version,
			true
		);
	}

	/**
	 * @return void
	 */
	public function enqueuePublic( Command $command )
	{
		$currentTheme = sanitize_title( (string) wp_get_theme()->get( 'Name' ));
		$stylesheet = file_exists( $command->path.'css/'.$currentTheme.'.css' )
			? $command->url.'css/'.$currentTheme.'.css'
			: $command->url.'css/site-reviews.css';
		if( apply_filters( 'site-reviews/assets/css', true )) {
			wp_enqueue_style( $command->handle, $stylesheet, [], $command->version );
		}
		if( glsr_get_option( 'reviews-form.recaptcha.integration' ) == 'custom' ) {
			$this->enqueueRecaptchaScript( $command );
		}
		if( apply_filters( 'site-reviews/assets/js', true )) {
			$dependencies = apply_filters( 'site-reviews/assets/polyfill', true )
				? [$command->handle.'/polyfill']
				: [];
			$dependencies = apply_filters( 'site-reviews/enqueue/public/dependencies', $dependencies );
			wp_enqueue_script(
				$command->handle,
				$command->url.'js/site-reviews.js',
				$dependencies,
				$command->version,
				true
			);
		}
	}

	/**
	 * @return void
	 */
	public function enqueuePolyfillService( Command $command )
	{
		if( !apply_filters( 'site-reviews/assets/polyfill', true ))return;
		wp_enqueue_script( $command->handle.'/polyfill', add_query_arg([
			'features' => 'CustomEvent,Element.prototype.closest,Element.prototype.dataset,Event',
			'flags' => 'gated',
		], 'https://cdn.polyfill.io/v2/polyfill.min.js' ));
	}

	/**
	 * @return void
	 */
	public function enqueueRecaptchaScript( Command $command )
	{
		wp_enqueue_script( $command->handle.'/google-recaptcha', add_query_arg([
			'hl' => apply_filters( 'site-reviews/recaptcha/language', get_locale() ),
			'onload' => 'glsr_render_recaptcha',
			'render' => 'explicit',
		], 'https://www.google.com/recaptcha/api.js' ));
		$inlineScript = file_get_contents( $command->path.'js/recaptcha.js' );
		wp_add_inline_script( $command->handle.'/google-recaptcha', $inlineScript, 'before' );
	}

	/**
	 * @return void
	 */
	public function enqueueTinymce( Command $command )
	{
		add_filter( 'mce_external_plugins', [$this, 'enqueueTinymcePlugins'], 15 );
		if( user_can_richedit() )return;
		script_concat_settings();
		global $concatenate_scripts, $compress_scripts;
		$suffix = false !== strpos( get_bloginfo( 'version' ), '-src' ) ? '' : '.min';
		$compressed = $compress_scripts
			&& $concatenate_scripts
			&& false !== stripos( filter_input( INPUT_SERVER, 'HTTP_ACCEPT_ENCODING' ), 'gzip' );
		$script = $compressed
			? 'wp-tinymce.php?c=1'
			: 'tinymce'.$suffix.'.js';
		wp_enqueue_script( 'tinymce', includes_url( 'js/tinymce/'.$script ), $command->version );
		add_action( 'admin_print_footer_scripts', function() {
			?>
			<script type="text/javascript">
				tinymce.PluginManager.load( 'glsr_shortcode', '<?= glsr_app()->url; ?>assets/js/mce-plugin.js' );
			</script>
			<?php
		}, 100 );
	}

	/**
	 * @return array
	 */
	public function enqueueTinymcePlugins( array $plugins )
	{
		if( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' )) {
			$plugins['glsr_shortcode'] = glsr_app()->url.'assets/js/mce-plugin.js';
		}
		return $plugins;
	}

	/**
	 * @return array
	 */
	protected function localizeShortcodes()
	{
		$variables = [];
		foreach( glsr_app()->mceShortcodes as $tag => $args ) {
			if( !empty( $args['required'] )) {
				$variables[$tag] = $args['required'];
			}
		}
		return $variables;
	}
}
