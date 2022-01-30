<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Tags\FormFieldsTag;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class SiteReviewsFormShortcode extends Shortcode
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    protected $with;

    /**
     * {@inheritdoc}
     */
    public function buildTemplate(array $args = [])
    {
        if (!is_user_logged_in() && glsr_get_option('general.require.login', false, 'bool')) {
            return $this->loginOrRegister();
        }
        $this->args = $args;
        $this->with = $this->with();
        $fields = $this->buildTemplateFieldTags();
        return glsr(Template::class)->build('templates/reviews-form', [
            'args' => $args,
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
     * @return string
     * @filter login_url
     */
    public function filterLoginUrl($url, $redirect, $forceReauth)
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
     * @return string
     * @filter register_url
     */
    public function filterRegisterUrl($url)
    {
        if ($registerUrl = glsr_get_option('general.require.register_url')) {
            return $registerUrl;
        }
        return $url;
    }

    /**
     * @return false|string
     */
    protected function buildTemplateFieldTags()
    {
        $parameters = [
            'args' => $this->args,
            'tag' => 'fields',
        ];
        return glsr(FormFieldsTag::class, $parameters)->handleFor('form', null, $this->with);
    }

    /**
     * @param string $tag
     * @return false|string
     */
    protected function buildTemplateTag($tag)
    {
        $args = $this->args;
        $className = Helper::buildClassName(['form', $tag, 'tag'], 'Modules\Html\Tags');
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('form', null, $this->with)
            : null;
        return glsr()->filterString('form/build/'.$tag, $field, $this->with, $this);
    }

    /**
     * @return string
     */
    protected function getClasses()
    {
        $classes = [
            'glsr-review-form',
            glsr(Style::class)->classes('form'),
            $this->args['class'],
        ];
        if (!empty($this->with->errors)) {
            $classes[] = glsr(Style::class)->validation('form_error');
        }
        return trim(implode(' ', array_filter($classes)));
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

    /**
     * @return string
     */
    protected function loginOrRegister()
    {
        return glsr(Template::class)->build('templates/login-register', [
            'context' => [
                'text' => trim($this->loginText().' '.$this->registerText()),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function loginText()
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
     * @return void|string
     */
    protected function registerText()
    {
        if (get_option('users_can_register') && glsr_get_option('general.require.login', false, 'bool')) {
            add_filter('register_url', [$this, 'filterRegisterUrl'], 20, 3);
            $registerUrl = wp_registration_url();
            remove_filter('register_url', [$this, 'filterRegisterUrl'], 20);
            $registerLink = glsr(Builder::class)->a([
                'href' => $registerUrl,
                'text' => __('register', 'site-reviews'),
            ]);
            return sprintf(__('You may also %s for an account.', 'site-reviews'), $registerLink);
        }
    }

    /**
     * @return \GeminiLabs\SiteReviews\Arguments
     */
    protected function with()
    {
        return glsr()->args([
            'errors' => glsr()->sessionPluck($this->args['id'].'errors', []),
            'message' => glsr()->sessionPluck($this->args['id'].'message', ''),
            'required' => glsr_get_option('submissions.required', []),
            'values' => glsr()->sessionPluck($this->args['id'].'values', []),
        ]);
    }
}
