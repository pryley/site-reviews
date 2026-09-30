<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Notices\WelcomeNotice;

/**
 * A notice declared outside the plugin's namespace, as premium and the addons declare theirs.
 */
class AddonNotice extends WelcomeNotice
{
}
