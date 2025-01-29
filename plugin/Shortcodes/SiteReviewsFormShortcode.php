<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class SiteReviewsFormShortcode extends Shortcode
{
    public function buildTemplate(): string
    {
        if (!is_user_logged_in() && glsr_get_option('general.require.login', false, 'bool')) {
            $this->debug();
            return $this->loginOrRegister();
        }
        $form = new ReviewForm($this->args);
        $this->debug(compact('form'));
        return $form->build();
    }

    public function description(): string
    {
        return esc_html_x('Display a review form', 'admin-text', 'site-reviews');
    }

    /**
     * @param string $url
     * @param string $redirect
     * @param bool   $forceReauth
     *
     * @filter login_url
     */
    public function filterLoginUrl($url, $redirect, $forceReauth): string
    {
        if ($loginUrl = glsr_get_option('general.require.login_url')) {
            if (!empty($redirect)) {
                $loginUrl = add_query_arg('redirect_to', urlencode($redirect), $loginUrl);
            }
            if ($forceReauth) {
                $loginUrl = add_query_arg('reauth', '1', $loginUrl);
            }
            return $loginUrl;
        }
        return $url;
    }

    /**
     * @param string $url
     *
     * @filter register_url
     */
    public function filterRegisterUrl($url): string
    {
        if ($registerUrl = glsr_get_option('general.require.register_url')) {
            return $registerUrl;
        }
        return $url;
    }

    public function loginLink(): string
    {
        return glsr(Builder::class)->a([
            'href' => $this->loginUrl(),
            'text' => __('logged in', 'site-reviews'),
        ]);
    }

    public function loginOrRegister(): string
    {
        $loginText = sprintf(__('You must be %s to submit a review.', 'site-reviews'), $this->loginLink());
        $registerLink = $this->registerLink();
        $registerText = '';
        if (glsr_get_option('general.require.register', false, 'bool') && !empty($registerLink)) {
            $registerText = sprintf(__('You may also %s for an account.', 'site-reviews'), $registerLink);
        }
        return glsr(Template::class)->build('templates/login-register', [
            'context' => [
                'text' => trim("{$loginText} {$registerText}"),
            ],
        ]);
    }

    public function loginUrl(): string
    {
        add_filter('login_url', [$this, 'filterLoginUrl'], 20, 3);
        $url = wp_login_url(strval(get_permalink()));
        remove_filter('login_url', [$this, 'filterLoginUrl'], 20);
        return $url;
    }

    public function name(): string
    {
        return esc_html_x('Review Form', 'admin-text', 'site-reviews');
    }

    public function registerLink(): string
    {
        $registerUrl = $this->registerUrl();
        if (empty($registerUrl)) {
            return '';
        }
        return glsr(Builder::class)->a([
            'href' => $registerUrl,
            'text' => __('register', 'site-reviews'),
        ]);
    }

    public function registerUrl(): string
    {
        if (!get_option('users_can_register')) {
            return '';
        }
        add_filter('register_url', [$this, 'filterRegisterUrl'], 20);
        $url = wp_registration_url();
        remove_filter('register_url', [$this, 'filterRegisterUrl'], 20);
        return $url;
    }

    protected function config(): array
    {
        return [
            // 'form' => [
            //     'label' => esc_html_x('Use a Custom Form', 'admin-text', 'site-reviews-forms'),
            //     'placeholder' => esc_html_x('Select a Form...', 'admin-text', 'site-reviews-forms'),
            //     'type' => 'select',
            // ],
            'assigned_posts' => [
                'label' => esc_html_x('Assign Reviews to Pages', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a Page...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'assigned_terms' => [
                'label' => esc_html_x('Assign Reviews to Categories', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a Category...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'assigned_users' => [
                'label' => esc_html_x('Assign Reviews to Users', 'admin-text', 'site-reviews'),
                'multiple' => true,
                'placeholder' => esc_html_x('Select a User...', 'admin-text', 'site-reviews'),
                'type' => 'select',
            ],
            'hide' => [
                'group' => 'hide',
                'options' => $this->options('hide'),
                'type' => 'checkbox',
            ],
            'reviews_id' => [
                'description' => esc_html_x('Enter the Custom ID of a reviews block, shortcode, or widget where the review should be displayed after submission.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Reviews ID', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'type' => 'text',
            ],
            'id' => [
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'class' => [
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'group' => 'advanced',
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
        ];
    }

    protected function debug(array $data = []): void
    {
        if (!empty($this->args['debug']) && !empty($data['form'])) {
            $form = $data['form'];
            $data = [
                'fields' => [
                    'hidden' => $form->hidden(),
                    'visible' => $form->visible(),
                ],
                'session' => $form->session()->toArray(),
            ];
        }
        parent::debug($data);
    }

    protected function hideOptions(): array
    {
        return [
            'rating' => _x('Hide the rating field', 'admin-text', 'site-reviews'),
            'title' => _x('Hide the title field', 'admin-text', 'site-reviews'),
            'content' => _x('Hide the review field', 'admin-text', 'site-reviews'),
            'name' => _x('Hide the name field', 'admin-text', 'site-reviews'),
            'email' => _x('Hide the email field', 'admin-text', 'site-reviews'),
            'terms' => _x('Hide the terms field', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @param string $value
     */
    protected function normalizeAssignedPosts($value): string
    {
        $postIds = parent::normalizeAssignedPosts($value);
        $postIds = explode(',', $postIds);
        $postIds = array_filter($postIds, 'is_numeric'); // don't use post_types here
        return implode(',', $postIds);
    }
}
