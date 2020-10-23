<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class SiteReviewsForm implements PartialContract
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
    public function build(array $args = [])
    {
        if (!is_user_logged_in() && glsr_get_option('general.require.login', false, 'bool')) {
            return $this->loginOrRegister();
        }
        $this->args = $args;
        $this->with = $this->with();
        return glsr(Template::class)->build('templates/reviews-form', [
            'args' => $args,
            'context' => [
                'class' => $this->getFormClasses(),
                'fields' => $this->buildTemplateTag('fields'),
                'id' => '', // @deprecated in v5.0
                'response' => $this->buildTemplateTag('response'),
                'submit_button' => $this->buildTemplateTag('submit_button'),
            ],
        ]);
    }

    /**
     * @param string $tag
     * @return false|string
     */
    protected function buildTemplateTag($tag)
    {
        $args = $this->args;
        $classname = implode('-', ['form', $tag, 'tag']);
        $className = Helper::buildClassName($classname, 'Modules\Html\Tags');
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('form', null, $this->with)
            : null;
        return glsr()->filterString('form/build/'.$tag, $field, $this->with, $this);
    }

    /**
     * @return string
     */
    protected function getFormClasses()
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
        $loginLink = glsr(Builder::class)->a([
            'href' => wp_login_url(strval(get_permalink())),
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
            $registerLink = glsr(Builder::class)->a([
                'href' => wp_registration_url(),
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
            'errors' => glsr()->sessionGet($this->args['id'].'errors', []),
            'message' => glsr()->sessionGet($this->args['id'].'message', ''),
            'required' => glsr_get_option('submissions.required', []),
            'values' => glsr()->sessionGet($this->args['id'].'values', []),
        ]);
    }
}
