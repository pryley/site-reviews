<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Framework\Route\RESTRoute;

class RESTRegistration implements DependencyInterface
{
    public function load(): void
    {
        $route = new RESTRoute('glsr-divi/v1');
        $route->prefix('/module')->group(function ($router) {
            $router->get('/render', [
                'args' => [RESTController::class, 'renderArgs'],
                'callback' => [RESTController::class, 'render'],
                'permission_callback' => [RESTController::class, 'permission'],
            ]);
        });
        $route->prefix('/module-data')->group(function ($router) {
            $router->get('/options', [
                'args' => [RESTController::class, 'optionsArgs'],
                'callback' => [RESTController::class, 'options'],
                'permission_callback' => [RESTController::class, 'permission'],
            ]);
        });
    }
}
