<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class EnqueueAssets
{
    public $handle;
    public $path;
    public $url;
    public $version;

    public function __construct( $input )
    {
        $this->handle  = $input['handle'];
        $this->url     = $input['url'];
        $this->path    = $input['path'];
        $this->version = $input['version'];
    }
}
