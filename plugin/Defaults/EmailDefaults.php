<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class EmailDefaults extends Defaults
{
	/**
	 * @return array
	 */
	protected function defaults()
	{
		$fromName  = wp_specialchars_decode( (string)get_option( 'blogname' ), ENT_QUOTES );
		$fromEmail = (string)get_option( 'admin_email' );
		return [
			'after' => '',
			'attachments' => [],
			'bcc' => '',
			'before' => '',
			'cc' => '',
			'from' => $fromName.' <'.$fromEmail.'>',
			'message' => '',
			'reply-to' => '',
			'subject' => '',
			'template' => '',
			'template-tags' => [],
			'to' => '',
		];
	}
}
