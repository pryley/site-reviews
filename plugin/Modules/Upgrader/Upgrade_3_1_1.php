<?php

namespace GeminiLabs\SiteReviews\Modules\Upgrader;

use GeminiLabs\SiteReviews\Application;

class Upgrade_3_1_1
{
	public function __construct()
	{
		delete_transient( Application::ID.'_cloudflare_ips' );
	}
}
