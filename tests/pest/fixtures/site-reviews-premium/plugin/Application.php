<?php

namespace GeminiLabs\SiteReviews\Premium\Shell;

use GeminiLabs\SiteReviews\Addons\Addon;

/**
 * The merged premium plugin's shell, as far as License is concerned: it
 * carries the `site-reviews-premium` id and declares itself LICENSED. The
 * Premium\ namespace matters too — the suppression guard admits only that
 * prefix for the ids the premium plugin claims.
 */
class Application extends Addon
{
    public const ID = 'site-reviews-premium';
    public const LICENSED = true;
    public const NAME = 'Site Reviews Premium';
    public const SLUG = 'premium';
}
