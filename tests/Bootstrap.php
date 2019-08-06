<?php

namespace GeminiLabs\SiteReviews\Tests;

use WP_Roles;

class Bootstrap
{
    protected static $instance = null;

    public $plugin_dir;
    public $reviews_dir;
    public $tests_dir;
    public $wp_tests_dir;

    public function __construct()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
        $this->tests_dir = dirname(__FILE__);
        $this->plugin_dir = dirname($this->tests_dir);
        $this->wp_tests_dir = $this->get_tests_dir($_SERVER['HOME'].'/Sites/wordpress/tests/current/');
        // load test function so tests_add_filter() is available
        require_once $this->wp_tests_dir.'/includes/functions.php';
        tests_add_filter('muplugins_loaded', array($this, 'load_plugin_environment'));
        tests_add_filter('setup_theme', array($this, 'install_plugin_environment'));
        // Finally load the WP testing environment
        require_once $this->wp_tests_dir.'/includes/bootstrap.php';
    }

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_tests_dir($path)
    {
        if (file_exists($path)) {
            putenv('WP_TESTS_DIR='.$path);
        }
        return getenv('WP_TESTS_DIR')
            ? getenv('WP_TESTS_DIR')
            : '/tmp/wordpress-tests-lib';
    }

    public function load_plugin_environment()
    {
        require_once $this->plugin_dir.'/site-reviews.php';
    }

    public function install_plugin_environment()
    {
        define('WP_UNINSTALL_PLUGIN', true);
        // clean existing install first
        include $this->plugin_dir.'/uninstall.php';
        // reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
        $GLOBALS['wp_roles'] = new WP_Roles();
    }
}

Bootstrap::init();
