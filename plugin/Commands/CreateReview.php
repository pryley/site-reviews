<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Validator\CustomValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DuplicateValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateReview;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class CreateReview implements Contract
{
    public $assigned_posts;
    public $assigned_terms;
    public $assigned_users;
    public $author_id;
    public $avatar;
    public $blacklisted;
    public $content;
    public $custom;
    public $date;
    public $date_gmt;
    public $email;
    public $form_id;
    public $ip_address;
    public $is_approved;
    public $is_pinned;
    public $is_verified;
    public $name;
    public $post_id;
    public $rating;
    public $referer;
    public $request;
    public $response;
    public $response_by;
    public $terms;
    public $terms_exist;
    public $title;
    public $type;
    public $url;

    protected $errors;
    protected $message;
    protected $review;

    public function __construct(Request $request)
    {
        $request = $this->normalize($request); // IP address is set here
        $this->setProperties($request->toArray());
        $this->request = $request;
        $this->review = new Review($this->toArray(), $init = false);
        $this->custom = $this->custom();
        $this->type = $this->type();
        $this->avatar = $this->avatar(); // do this last
    }

    /**
     * @return static
     */
    public function handle()
    {
        if ($this->validate()) {
            $this->create(); // public form submission
        }
        return $this;
    }

    /**
     * This only validates the provided values in the Request.
     * @return bool
     */
    public function isValid()
    {
        $options = glsr(DefaultsManager::class)->pluck('settings.forms.required.options');
        $request = clone $this->request;
        $request->merge([
            'excluded' => array_keys(array_diff_key($options, $this->request->toArray())),
        ]);
        $validator = glsr(ValidateReview::class)->validate($request, [ // order is intentional
            DefaultValidator::class,
            DuplicateValidator::class,
            CustomValidator::class,
        ]);
        if (!$validator->isValid()) {
            glsr_log()->warning($validator->errors);
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function referer()
    {
        if ($referer = $this->redirect($this->referer)) {
            return $referer;
        }
        glsr_log()->warning('The form referer ($_SERVER[REQUEST_URI]) was empty.')->debug($this->request);
        return Url::home();
    }

    /**
     * @return string
     */
    public function reloadedReviews()
    {
        $args = $this->request->cast('_reviews_atts', 'array');
        if (!empty($args) && $this->review->is_approved) {
            $paginationArgs = $this->request->cast('_pagination_atts', 'array');
            glsr()->store(glsr()->paged_handle, $paginationArgs);
            return glsr(SiteReviewsShortcode::class)
                ->normalize($args)
                ->buildTemplate();
        }
        return '';
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
            'redirect' => $this->redirect(),
            'review' => Cast::toArray($this->review),
            'reviews' => $this->reloadedReviews(),
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
        $validator = glsr(ValidateReview::class)->validate($this->request);
        $this->blacklisted = $validator->blacklisted;
        $this->errors = $validator->errors;
        $this->message = $validator->message;
        return $validator->isValid();
    }

    /**
     * @return string
     */
    protected function avatar()
    {
        if (!defined('WP_IMPORTING') && empty($this->avatar)) {
            return glsr(Avatar::class)->generate($this->review);
        }
        return $this->avatar;
    }

    /**
     * @return void
     */
    protected function create()
    {
        if ($review = glsr(ReviewManager::class)->create($this)) {
            $this->message = $review->is_approved
                ? __('Your review has been submitted!', 'site-reviews')
                : __('Your review has been submitted and is pending approval.', 'site-reviews');
            $this->review = $review; // overwrite the dummy review with the submitted review
            return;
        }
        $this->errors = [];
        $this->message = __('Your review could not be submitted and the error has been logged. Please notify the site administrator.', 'site-reviews');
    }

    /**
     * @return array
     */
    protected function custom()
    {
        return glsr(CustomFieldsDefaults::class)->filter($this->request->toArray());
    }

    /**
     * @return Request
     */
    protected function normalize(Request $request)
    {
        $isFormSubmission = !defined('WP_IMPORTING') && !glsr()->retrieve('glsr_create_review', false);
        if ($isFormSubmission || empty($request->ip_address)) {
            $request->set('ip_address', Helper::getIpAddress()); // required for Akismet and Blacklist validation
        }
        if ($isFormSubmission) {
            // is_approved is set when the review is created
            $request->set('author_id', get_current_user_id());
            $request->set('is_pinned', false);
            $request->set('is_verified', false);
            $request->set('response', '');
            $request->set('response_by', 0);
        }
        glsr()->action('review/request', $request);
        return $request;
    }

    /**
     * @return string
     */
    protected function redirect($fallback = '')
    {
        $redirect = trim(strval(get_post_meta($this->post_id, 'redirect_to', true)));
        $redirect = glsr()->filterString('review/redirect', $redirect, $this, $this->review);
        if (empty($redirect)) {
            $redirect = $fallback;
        }
        return sanitize_text_field($redirect);
    }

    /**
     * @return void
     */
    protected function setProperties(array $properties)
    {
        $values = glsr(CreateReviewDefaults::class)->restrict($properties);
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        if (!empty($this->date) && empty($this->date_gmt)) {
            $this->date_gmt = get_gmt_from_date($this->date); // set the GMT date
        }
    }

    /**
     * @return string
     */
    protected function type()
    {
        $reviewTypes = glsr()->retrieveAs('array', 'review_types');
        return array_key_exists($this->type, $reviewTypes) ? $this->type : 'local';
    }
}
