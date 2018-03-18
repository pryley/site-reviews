<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Form
{
	/**
	 * @param string $path
	 * @return string
	 */
	public function build( $path )
	{
		$settingsKey = rtrim( $path, '.' );
		glsr_debug( $settingsKey );
		// glsr( Form::class )->buildSettingsForm( $settingsKey );

	}
}
