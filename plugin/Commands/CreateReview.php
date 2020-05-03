<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notification;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;

class CreateReview implements Contract
{
    public $ajax_request;
    public $assigned_post_ids;
    public $assigned_term_ids;
    public $assigned_user_ids;
    public $author;
    public $avatar;
    public $blacklisted;
    public $content;
    public $custom;
    public $date;
    public $email;
    public $form_id;
    public $ip_address;
    public $post_id;
    public $rating;
    public $referer;
    public $request;
    public $response;
    public $terms;
    public $title;
    public $url;

    public function __construct($input)
    {
        $this->request = $input;
        $this->ajax_request = isset($input['_ajax_request']);
        $this->assigned_post_ids = $this->getAssignedPosts();
        $this->assigned_term_ids = $this->getAssignedTerms();
        $this->assigned_user_ids = $this->getAssignedUsers();
        $this->author = sanitize_text_field($this->getUser('name'));
        $this->avatar = $this->getAvatar();
        $this->blacklisted = isset($input['blacklisted']);
        $this->content = sanitize_textarea_field($this->get('content'));
        $this->custom = $this->getCustom();
        $this->date = $this->getDate('date');
        $this->email = sanitize_email($this->getUser('email'));
        $this->form_id = sanitize_key($this->get('form_id'));
        $this->ip_address = $this->get('ip_address');
        $this->post_id = absint($this->get('_post_id'));
        $this->rating = absint($this->get('rating'));
        $this->referer = sanitize_text_field($this->get('_referer'));
        $this->response = sanitize_textarea_field($this->get('response'));
        $this->terms = !empty($input['terms']);
        $this->title = sanitize_text_field($this->get('title'));
        $this->url = esc_url_raw(sanitize_text_field($this->get('url')));
    }

    /**
     * @return \GeminiLabs\SiteReviews\Review|void
     */
    public function handle()
    {
        if (!$this->validate()) {
            return;
        }
        if ($review = glsr(ReviewManager::class)->create($this)) {
            glsr()->sessionSet($this->form_id.'message', __('Your review has been submitted!', 'site-reviews'));
            glsr(Notification::class)->send($review);
            return $review;
        }
        glsr()->sessionSet($command->form_id.'errors', []);
        glsr()->sessionSet($command->form_id.'message', __('Your review could not be submitted and the error has been logged. Please notify the site admin.', 'site-reviews'));
    }

    /**
     * @return string
     */
    public function redirect($fallback = '')
    {
        $redirect = trim(strval(get_post_meta($this->post_id, 'redirect_to', true)));
        $redirect = apply_filters('site-reviews/review/redirect', $redirect, $this);
        if (empty($redirect)) {
            $redirect = $fallback;
        }
        return sanitize_text_field($redirect);
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
     * @return bool
     */
    public function validate()
    {
        $validated = glsr(ValidateReview::class)->validate($this->request);
        return empty($validated->error) && !$validated->recaptchaIsUnset;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function get($key)
    {
        return (string) Arr::get($this->request, $key);
    }

    /**
     * @return array
     */
    protected function getAssignedPosts()
    {
        $postIds = Arr::convertFromString($this->get('assign_to'));
        return array_map('sanitize_key', $postIds);
    }

    /**
     * @return array
     */
    protected function getAssignedTerms()
    {
        $termIds = Arr::convertFromString($this->get('category'));
        return array_map('sanitize_key', $termIds);
    }

    /**
     * @return array
     */
    protected function getAssignedUsers()
    {
        $userIds = Arr::convertFromString($this->get('user'));
        return array_map('sanitize_key', $userIds);
    }

    /**
     * @return string
     */
    protected function getAvatar()
    {
        $avatar = $this->get('avatar');
        return !filter_var($avatar, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
            ? (string) get_avatar_url($this->get('email'))
            : $avatar;
    }

    /**
     * @return array
     */
    protected function getCustom()
    {
        $unset = [
            '_action', '_ajax_request', '_counter', '_nonce', '_post_id', '_recaptcha-token',
            '_referer', 'assign_to', 'category', 'content', 'date', 'email', 'excluded', 'form_id',
            'gotcha', 'ip_address', 'name', 'rating', 'response', 'terms', 'title', 'url', 'user',
        ];
        $unset = apply_filters('site-reviews/create/unset-keys-from-custom', $unset);
        $custom = $this->request;
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
     * @param string $key
     * @return string
     */
    protected function getDate($key)
    {
        $date = strtotime($this->get($key));
        if (false === $date) {
            $date = time();
        }
        return get_date_from_gmt(gmdate('Y-m-d H:i:s', $date));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getNumeric($key)
    {
        $value = $this->get($key);
        return is_numeric($value)
            ? $value
            : '';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getUser($key)
    {
        $value = $this->get($key);
        if (empty($value)) {
            $user = wp_get_current_user();
            $userValues = [
                'email' => 'user_email',
                'name' => 'display_name',
            ];
            if ($user->exists() && array_key_exists($key, $userValues)) {
                return $user->{$userValues[$key]};
            }
        }
        return $value;
    }
}
