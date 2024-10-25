<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Validator\CustomValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DuplicateValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ValidateForm;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class CreateReview extends AbstractCommand
{
    public $assigned_posts;
    public $assigned_terms;
    public $assigned_users;
    public $author_id;
    public $avatar;
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

    protected Review $review;
    protected Arguments $validation;

    public function __construct(Request $request)
    {
        $this->request = $this->normalize($request); // IP address is set here
        $this->setProperties(); // do this after setting the request
        $this->review = new Review($this->toArray(), $init = false);
        $this->validation = new Arguments();
        $this->custom = $this->custom();
        $this->type = $this->type();
        $this->avatar = $this->avatar(); // do this last
    }

    public function handle(): void
    {
        if ($this->validate()) {
            $this->create(); // public form submission
        }
    }

    /**
     * This method is used to validate the request instead of the "validate" method
     * when creating a review with the "glsr_create_review" function.
     */
    public function isRequestValid(): bool
    {
        $request = clone $this->request;
        $excluded = array_keys(array_diff_key(
            Arr::consolidate(glsr()->settings('settings.forms.required.options')),
            $this->request->toArray(),
        ));
        $request->merge(compact('excluded'));
        $validators = glsr()->filterArray('validators', [ // order is intentional
            DefaultValidator::class,
            DuplicateValidator::class,
            CustomValidator::class,
        ]);
        $validator = glsr(ValidateForm::class)->validate($request, $validators);
        if ($validator->isValid()) {
            return true;
        }
        glsr_log()->warning($validator->result()->errors);
        return false;
    }

    public function referer(): string
    {
        if ($referer = $this->redirect($this->referer)) {
            return $referer;
        }
        glsr_log()->warning('The form referer ($_SERVER[REQUEST_URI]) was empty.')->debug($this->request);
        return Url::home();
    }

    public function reloadedReviews(): string
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

    public function response(): array
    {
        return [
            'errors' => $this->validation->array('errors'),
            'html' => (string) $this->review,
            'message' => $this->validation->cast('message', 'string'),
            'redirect' => $this->redirect(),
            'review' => $this->review->toArray(['email', 'ip_address']),
            'reviews' => $this->reloadedReviews(),
            'success' => $this->successful(),
        ];
    }

    public function successful(): bool
    {
        return false === $this->validation->failed;
    }

    public function toArray(): array
    {
        $values = get_object_vars($this);
        $values = glsr()->filterArray('create/review-values', $values, $this);
        return glsr(CreateReviewDefaults::class)->merge($values);
    }

    public function validate(): bool
    {
        $validator = glsr(ValidateForm::class)->validate($this->request);
        $this->validation = $validator->result();
        return $validator->isValid();
    }

    protected function avatar(): string
    {
        if (!defined('WP_IMPORTING') && empty($this->avatar)) {
            return glsr(Avatar::class)->generate($this->review);
        }
        return $this->avatar;
    }

    protected function create(): void
    {
        $message = __('Your review could not be submitted and the error has been logged. Please notify the site administrator.', 'site-reviews');
        if ($review = glsr(ReviewManager::class)->create($this)) {
            $this->review = $review; // overwrite the dummy review with the submitted review
            $message = $review->is_approved
                ? __('Your review has been submitted!', 'site-reviews')
                : __('Your review has been submitted and is pending approval.', 'site-reviews');
        }
        $this->validation->set('message', $message);
    }

    protected function custom(): array
    {
        $fields = [];
        foreach ($this->request->toArray() as $key => $value) {
            $key = Str::removePrefix($key, 'custom_');
            $fields[$key] = $value;
        }
        return glsr(CustomFieldsDefaults::class)->filter($fields);
    }

    protected function normalize(Request $request): Request
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

    protected function redirect(string $fallback = ''): string
    {
        $redirect = trim(strval(get_post_meta($this->post_id, 'redirect_to', true)));
        $redirect = glsr()->filterString('review/redirect', $redirect, $this, $this->review);
        if (empty($redirect)) {
            $redirect = $fallback;
        }
        return sanitize_text_field($redirect);
    }

    protected function setProperties(): void
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        $values = glsr(CreateReviewDefaults::class)->restrict($this->request->toArray());
        foreach ($properties as $property) {
            $key = $property->getName();
            if (array_key_exists($key, $values)) {
                $property->setValue($this, $values[$key]);
            }
        }
        if (!empty($this->date) && empty($this->date_gmt)) {
            $this->date_gmt = get_gmt_from_date($this->date); // set the GMT date
        }
    }

    protected function type(): string
    {
        $reviewTypes = glsr()->retrieveAs('array', 'review_types');
        return array_key_exists($this->type, $reviewTypes) ? $this->type : 'local';
    }
}
