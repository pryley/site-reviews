<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Modules\Html\Tags\FormFieldsTag;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Style;

class SiteReviewsFormShortcode extends Shortcode
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    protected $with;

    /**
     * @return string
     * @todo add return type hint and remove $args in v7.0
     */
    public function buildTemplate(array $args = [])
    {
        if (!is_user_logged_in() && glsr_get_option('general.require.login', false, 'bool')) {
            $this->debug();
            return $this->loginOrRegister();
        }
        $this->with = $this->with();
        $fields = $this->buildTemplateFieldTags();
        $this->debug(compact('fields'));
        return glsr(Template::class)->build('templates/reviews-form', [
            'args' => $this->args,
            'context' => [
                'class' => $this->getClasses(),
                'fields' => glsr()->filterString('form/build/fields', $fields, $this->with, $this),
                'id' => '', // @deprecated in v5.0
                'response' => $this->buildTemplateTag('response'),
                'submit_button' => $this->buildTemplateTag('submit_button'),
            ],
            'form' => $fields,
        ]);
    }

    /**
     * @param string $url
     * @param string $redirect
     * @param bool $forceReauth
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
     * @filter register_url
     */
    public function filterRegisterUrl($url): string
    {
        if ($registerUrl = glsr_get_option('general.require.register_url')) {
            return $registerUrl;
        }
        return $url;
    }

    protected function buildTemplateFieldTags(): Form
    {
        $parameters = [
            'args' => $this->args,
            'tag' => 'fields',
        ];
        return glsr(FormFieldsTag::class, $parameters)->handleFor('form', null, $this->with);
    }

    protected function buildTemplateTag(string $tag): string
    {
        $args = $this->args;
        $className = Helper::buildClassName(['form', $tag, 'tag'], 'Modules\Html\Tags');
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('form', null, $this->with)
            : '';
        return glsr()->filterString('form/build/'.$tag, $field, $this->with, $this);
    }

    protected function debug(array $data = []): void
    {
        if (!empty($this->args['debug']) && !empty($data['fields'])) {
            $fields = $data['fields'];
            $data = [
                'fields' => [
                    'hidden' => $fields->hidden(),
                    'visible' => $fields->visible(),
                ],
                'with' => $this->with->toArray(),
            ];
        }
        parent::debug($data);
    }

    protected function getClasses(): string
    {
        $classes = ['glsr-review-form'];
        $classes[] = glsr(Style::class)->classes('form');
        $classes[] = $this->args['class'];
        if (!empty($this->with->errors)) {
            $classes[] = glsr(Style::class)->validation('form_error');
        }
        $classes = implode(' ', $classes);
        return glsr(Sanitizer::class)->sanitizeAttrClass($classes);
    }

    /**
     * @return array
     */
    protected function hideOptions()
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

    protected function loginOrRegister(): string
    {
        return glsr(Template::class)->build('templates/login-register', [
            'context' => [
                'text' => trim($this->loginText().' '.$this->registerText()),
            ],
        ]);
    }

    protected function loginText(): string
    {
        add_filter('login_url', [$this, 'filterLoginUrl'], 20, 3);
        $loginUrl = wp_login_url(strval(get_permalink()));
        remove_filter('login_url', [$this, 'filterLoginUrl'], 20);
        $loginLink = glsr(Builder::class)->a([
            'href' => $loginUrl,
            'text' => __('logged in', 'site-reviews'),
        ]);
        return sprintf(__('You must be %s to submit a review.', 'site-reviews'), $loginLink);
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

    protected function registerText(): string
    {
        if (get_option('users_can_register') && glsr_get_option('general.require.login', false, 'bool')) {
            add_filter('register_url', [$this, 'filterRegisterUrl'], 20);
            $registerUrl = wp_registration_url();
            remove_filter('register_url', [$this, 'filterRegisterUrl'], 20);
            $registerLink = glsr(Builder::class)->a([
                'href' => $registerUrl,
                'text' => __('register', 'site-reviews'),
            ]);
            return sprintf(__('You may also %s for an account.', 'site-reviews'), $registerLink);
        }
        return '';
    }

    protected function with(): Arguments
    {
        return glsr()->args([
            'errors' => glsr()->sessionPluck('form_errors', []),
            'message' => glsr()->sessionPluck('form_message', ''),
            'required' => glsr_get_option('forms.required', []),
            'values' => glsr()->sessionPluck('form_values', []),
        ]);
    }
}
