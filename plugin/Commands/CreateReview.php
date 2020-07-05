<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Notification;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;
use GeminiLabs\SiteReviews\Request;

class CreateReview implements Contract
{
    public $ajax_request;
    public $assigned_post_ids;
    public $assigned_term_ids;
    public $assigned_user_ids;
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
     * @return string
     */
    protected function avatar()
    {
        if (!empty($this->request->avatar)) {
            return esc_url_raw(sanitize_text_field($this->request->avatar));
        }
        $userField = empty($this->request->email)
            ? get_current_user_id()
            : sanitize_email($this->request->email);
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
        $unset = [
            '_action', '_ajax_request', '_counter', '_nonce', '_post_id', '_recaptcha-token',
            '_referer', 'assigned_post_ids', 'assigned_term_ids', 'assigned_user_ids', 'content',
            'date', 'email', 'excluded', 'form_id', 'gotcha', 'ip_address', 'name', 'rating',
            'title', 'url',
        ];
        $unset = glsr()->filterArray('create/unset-keys-from-custom', $unset);
        $custom = $this->request->toArray();
        foreach ($unset as $key) {
            unset($custom[$key]);
        }
        foreach ($custom as $key => $value) {
            if (is_string($value)) {
                $custom[$key] = sanitize_text_field($value);
            }
        }
        return $custom;
    }

    /**
     * @return string
     */
    protected function date()
    {
        $date = strtotime($this->request->date);
        if (false === $date) {
            $date = time();
        }
        return gmdate('Y-m-d H:i:s', $date);
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
        $this->ajax_request = Cast::toBool($this->request->_ajax_request);
        $this->assigned_post_ids = Arr::uniqueInt($this->request->assigned_post_ids);
        $this->assigned_term_ids = Arr::uniqueInt($this->request->assigned_term_ids);
        $this->assigned_user_ids = Arr::uniqueInt($this->request->assigned_user_ids);
        $this->avatar = $this->avatar();
        $this->blacklisted = Cast::toBool($this->request->blacklisted);
        $this->content = sanitize_textarea_field($this->request->content);
        $this->custom = $this->custom();
        $this->date = $this->date();
        $this->email = sanitize_email($this->request->email);
        $this->form_id = sanitize_key($this->request->form_id);
        $this->ip_address = $this->request->ip_address;
        $this->name = sanitize_text_field($this->request->name);
        $this->post_id = Cast::toInt($this->request->_post_id);
        $this->rating = Cast::toInt($this->request->rating);
        $this->referer = sanitize_text_field($this->request->_referer);
        $this->title = sanitize_text_field($this->request->title);
        $this->type = $this->type();
        $this->url = esc_url_raw(sanitize_text_field($this->request->url));
    }

    /**
     * @return string
     */
    protected function type()
    {
        $type = sanitize_text_field($this->request->type);
        return array_key_exists($type, glsr()->reviewTypes) ? $type : 'local';
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        $validated = glsr(ValidateReview::class)->validate($this->request->toArray());
        return empty($validated->error) && !$validated->recaptchaIsUnset;
    }
}
