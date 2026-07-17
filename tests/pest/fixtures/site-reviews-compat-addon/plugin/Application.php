<?php
/*
 * FIXTURE for Addons\Compat: an outdated addon whose main file carries no
 * Update URI header, which is how Compat::register() recognises an addon that
 * predates the plugin's update mechanism and needs compatibility mode.
 */

namespace GeminiLabs\SiteReviews\Tests\Fixtures\CompatAddon;

class Application
{
    public const ID = 'site-reviews-compat-addon';
}
