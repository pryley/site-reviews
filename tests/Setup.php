<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;

trait Setup
{
    protected $referer;

    public function setUp()
    {
        parent::setUp();
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_NAME'] = '';
        $this->referer = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';
        glsr(Install::class)->run();
        glsr(OptionManager::class)->set(glsr(DefaultsManager::class)->get());
        // save initial plugin settings here if needed
    }
}
