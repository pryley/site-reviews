<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
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
        glsr(Install::class)->run();
        glsr(Migrate::class)->runAll();
        glsr(OptionManager::class)->set(glsr(DefaultsManager::class)->get());
        // save initial plugin settings here if needed
    }
}
