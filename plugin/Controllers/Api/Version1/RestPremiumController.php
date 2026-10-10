<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\ConnectPremium;
use GeminiLabs\SiteReviews\Commands\DeactivatePremiumLicense;
use GeminiLabs\SiteReviews\Commands\InstallPremium;
use GeminiLabs\SiteReviews\Commands\VerifyPremiumLicense;
use GeminiLabs\SiteReviews\Request;

class RestPremiumController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkConnectPermission(\WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            return $this->refuse('not_logged_in',
                __('You must be logged in to do this.', 'site-reviews'), 401
            );
        }
        return $this->lock('premium-connect');
    }

    /**
     * The store's call is anonymous: the token authorises it.
     *
     * @return true|\WP_Error
     */
    public function checkInstallPermission(\WP_REST_Request $request)
    {
        return $this->lock('premium-install');
    }

    /**
     * @return true|\WP_Error
     */
    public function checkLicensePermission(\WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            return $this->refuse('not_logged_in',
                __('You must be logged in to do this.', 'site-reviews'), 401
            );
        }
        return $this->lock('premium-license');
    }

    public function connect(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new ConnectPremium();
        $command->handle();
        return $this->respond($command->response(), $command->status);
    }

    public function deactivate(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new DeactivatePremiumLicense(new Request([
            'delete' => $request->get_param('delete'),
        ]));
        $command->handle();
        return $this->respond($command->response(), $command->status);
    }

    public function install(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new InstallPremium(new Request([
            'token' => $request->get_param('token'),
        ]));
        $command->handle();
        return $this->respond($command->response(), $command->status);
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/premium/license', [
            [
                'args' => [
                    'license' => [
                        'description' => 'The premium license key.',
                        'required' => true,
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'verify'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkLicensePermission'],
            ],
            [
                'args' => [
                    'delete' => [
                        'default' => false,
                        'description' => 'Whether to clear the saved key as well as deactivating it.',
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'deactivate'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'checkLicensePermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/premium/connect', [
            [
                'callback' => [$this, 'connect'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkConnectPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/premium/install', [
            [
                'args' => [
                    'token' => [
                        'description' => 'The one-time token premium/connect made for the store.',
                        'required' => true,
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'install'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkInstallPermission'],
            ],
        ]);
    }

    public function verify(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new VerifyPremiumLicense(new Request([
            'license' => $request->get_param('license'),
        ]));
        $command->handle();
        return $this->respond($command->response(), $command->status);
    }
}
