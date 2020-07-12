<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Notification;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;
use GeminiLabs\SiteReviews\Request;

class CreateReview implements Contract
{
    public $ajax_request;
    public $assigned_posts;
    public $assigned_terms;
    public $assigned_users;
    public $avatar;
    public $blacklisted;
    public $content;
    public $custom;
    public $date;
    public $email;
    public $form_id;
    public $ip_address;
    public $name;
    public $post_id;
    public $rating;
    public $referer;
    public $request;
    public $title;
    public $type;
    public $url;

    protected $errors;
    protected $message;
    protected $recaptcha;
    protected $review;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->sanitize();
    }

    /**
     * @return static
     */
    public function handle()
    {
        if ($this->validate()) {
            $this->create();
        }
        $this->errors = glsr()->sessionGet($this->form_id.'errors', false);
        $this->message = glsr()->sessionGet($this->form_id.'message', '');
        $this->recaptcha = glsr()->sessionGet($this->form_id.'recaptcha', false);
        return $this;
    }

    /**
     * @return string
     */
    public function referer()
    {
        if ($referer = $this->redirect($this->referer)) {
            return $referer;
        }
        glsr_log()->warning('The form referer ($_SERVER[REQUEST_URI]) was empty.')->debug($this);
        return home_url();
    }

    /**
     * @return array
     */
    public function response()
    {
        return [
            'errors' => $this->errors,
            'html' => (string) $this->review,
            'message' => $this->message,
            'recaptcha' => $this->recaptcha,
            'redirect' => $this->redirect(),
            'review' => (array) $this->review,
        ];
    }

    /**
     * @return bool
     */
    public function success()
    {
        if (false === $this->errors) {
            glsr()->sessionClear();
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $values = get_object_vars($this);
        $values = glsr()->filterArray('create/review-values', $values, $this);
        return glsr(CreateReviewDefaults::class)->merge($values);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $validated = glsr(ValidateReview::class)->validate($this->request->toArray());
        return empty($validated->error) && !$validated->recaptchaIsUnset;
    }

    /**
     * @return string
     */
    protected function avatar()
    {
        if (!empty($this->avatar)) {
            return $this->avatar;
        }
        $userField = empty($this->email)
            ? get_current_user_id()
            : $this->email;
        return glsr(Avatar::class)->generate($userField);
    }

    /**
     * @return void
     */
    protected function create()
    {
        if ($this->review = glsr(ReviewManager::class)->create($this)) {
            glsr()->sessionSet($this->form_id.'message', __('Your review has been submitted!', 'site-reviews'));
            glsr(Notification::class)->send($this->review);
            return;
        }
        glsr()->sessionSet($this->form_id.'errors', []);
        glsr()->sessionSet($this->form_id.'message', __('Your review could not be submitted and the error has been logged. Please notify the site admin.', 'site-reviews'));
    }

    /**
     * @return array
     */
    protected function custom()
    {
        return glsr(CustomFieldsDefaults::class)->filter($this->request->toArray());
    }

    /**
     * @return string
     */
    protected function redirect($fallback = '')
    {
        $redirect = trim(strval(get_post_meta($this->post_id, 'redirect_to', true)));
        $redirect = glsr()->filterString('review/redirect', $redirect, $this);
        if (empty($redirect)) {
            $redirect = $fallback;
        }
        return sanitize_text_field($redirect);
    }

    /**
     * @return void
     */
    protected function sanitize()
    {
        $values = glsr(CreateReviewDefaults::class)->restrict($this->request->toArray());
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        $this->avatar = $this->avatar();
        $this->custom = $this->custom();
        $this->type = $this->type();
    }

    /**
     * @return string
     */
    protected function type()
    {
        return array_key_exists($this->type, glsr()->reviewTypes) ? $this->type : 'local';
    }
}
