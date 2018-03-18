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

class Email
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $attachments;

	/**
	 * @var array
	 */
	protected $headers;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $to;

	public function __construct( App $app )
	{
		$this->app = $app;
	}

	/**
	 * @return Email
	 */
	public function compose( array $email )
	{
		$email = $this->normalize( $email );
		// glsr_log( 'Email to send: '.(string)wp_json_encode( $email ));

		$this->attachments = $email['attachments'];
		$this->headers     = $this->buildHeaders( $email );
		$this->message     = $this->buildHtmlMessage( $email );
		$this->subject     = $email['subject'];
		$this->to          = $email['to'];

		add_action( 'phpmailer_init', [ $this, 'buildPlainTextMessage'] );

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function read( $plaintext = false )
	{
		if( !!$plaintext ) {
			$message = $this->stripHtmlTags( $this->message );

			return apply_filters( 'site-reviews/email/message', $message, 'text', $this );
		}

		return $this->message;
	}

	/**
	 * @return void|bool
	 */
	public function send()
	{
		if( !$this->message || !$this->subject || !$this->to )return;
		// glsr_log( 'Sending email now...' );
		$sent = wp_mail(
			$this->to,
			$this->subject,
			$this->message,
			$this->headers,
			$this->attachments
		);
		// glsr_log( 'Result of wp_mail(): '.var_export( $sent, true ));
		$this->reset();
		return $sent;
	}

	/**
	 * @return void
	 *
	 * @action phpmailer_init
	 */
	public function buildPlainTextMessage( $phpmailer )
	{
		if( $phpmailer->ContentType === 'text/plain' || !empty( $phpmailer->AltBody ))return;

		$message = $this->stripHtmlTags( $phpmailer->Body );

		$phpmailer->AltBody = apply_filters( 'site-reviews/email/message', $message, 'text', $this );
	}

	/**
	 * @return array
	 */
	protected function buildHeaders( $email )
	{
		$allowed = [
			'bcc',
			'cc',
			'from',
			'reply-to',
		];

		$headers = array_intersect_key( $email, array_flip( $allowed ));
		$headers = array_filter( $headers );

		foreach( $headers as $key => $value ) {
			unset( $headers[ $key ] );
			$headers[] = "{$key}: {$value}";
		}

		$headers[] = 'Content-Type: text/html';

		return apply_filters( 'site-reviews/email/headers', $headers, $this );
	}

	/**
	 * @return string
	 */
	protected function buildHtmlMessage( $email )
	{
		$html = $this->app->make( 'Html' );

		$template = trim( $this->app->make( 'Database' )->getOption( 'settings.general.notification_message' ));

		if( !empty( $template )) {
			$message = $html->renderTemplateString( $template, $email['template-tags'] );
		}
		else if( $email['template'] ) {
			$message = $html->renderTemplate( "email/templates/{$email['template']}", $email['template-tags'] );
		}

		if( !isset( $message )) {
			$message = $email['message'];
		}

		$message = $email['before'] . $message . $email['after'];

		$body = $html->renderTemplate( 'email/index', [] );

		$message = strip_shortcodes( $message );
		$message = wptexturize( $message );
		$message = wpautop( $message );
		$message = str_replace( '&lt;&gt; ', '', $message );
		$message = str_replace( ']]>', ']]&gt;', $message );
		$message = str_replace( '{message}', $message, $body );
		$message = stripslashes( $message );

		return apply_filters( 'site-reviews/email/message', $message, 'html', $this );
	}

	/**
	 * @return array
	 */
	protected function normalize( $email )
	{
		$fromName  = wp_specialchars_decode( (string) get_option( 'blogname' ), ENT_QUOTES );
		$fromEmail = get_option( 'admin_email' );

		$defaults = [
			'after'         => '',
			'attachments'   => [],
			'bcc'           => '',
			'before'        => '',
			'cc'            => '',
			'from'          => "{$fromName} <{$fromEmail}>",
			'message'       => '',
			'reply-to'      => '',
			'subject'       => '',
			'template'      => '',
			'template-tags' => [],
			'to'            => '',
		];

		$email = shortcode_atts( $defaults, $email );

		!empty( $email['reply-to'] ) ?: $email['reply-to'] = $email['from'];

		return apply_filters( 'site-reviews/email/compose', $email, $this );
	}

	/**
	 * @return void
	 */
	protected function reset()
	{
		$this->attachments = [];
		$this->headers = [];
		$this->message = null;
		$this->subject = null;
		$this->to = null;
	}

	/**
	 * @return string
	 */
	protected function stripHtmlTags( $string )
	{
		// remove invisible elements
		$string = preg_replace( '@<(embed|head|noembed|noscript|object|script|style)[^>]*?>.*?</\\1>@siu', '', $string );

		// replace certain elements with a line-break
		$string = preg_replace( '@</(div|h[1-9]|p|pre|tr)@iu', "\r\n\$0", $string );

		// replace other elements with a space
		$string = preg_replace( '@</(td|th)@iu', " \$0", $string );

		// add a placeholder for plain-text bullets to list elements
		$string = preg_replace( '@<(li)[^>]*?>@siu', "\$0-o-^-o-", $string );

		// strip all remaining HTML tags
		$string = wp_strip_all_tags( $string );
		$string = wp_specialchars_decode( $string, ENT_QUOTES );
		$string = preg_replace( '/\v(?:[\v\h]+){2,}/', "\r\n\r\n", $string );
		$string = str_replace( '-o-^-o-', ' - ', $string );

		return html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );
	}
}
