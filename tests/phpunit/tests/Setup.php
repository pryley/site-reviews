<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Migrate;

trait Setup
{
    protected $referer;

    public function set_up()
    {
        parent::set_up();
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_NAME'] = '';
        $this->referer = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';
        $defaults = Arr::unflatten(glsr()->defaults());
        glsr(Migrate::class)->runAll();
        glsr(OptionManager::class)->replace($defaults);
        // save initial plugin settings here if needed
    }
}
