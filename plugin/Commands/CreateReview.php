<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Encryption;
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
        $request->merge([
            'excluded' => glsr(Encryption::class)->encrypt(implode(',', $excluded)),
        ]);
        $validator = glsr(ValidateForm::class)->validate($request, [ // order is intentional
            DefaultValidator::class,
            DuplicateValidator::class,
            CustomValidator::class,
        ]);
        if (!$validator->isValid()) {
            glsr_log()->warning($validator->errors);
            return false;
        }
        glsr()->sessionClear();
        return true;
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
            'errors' => $this->errors,
            'html' => (string) $this->review,
            'message' => $this->message,
            'redirect' => $this->redirect(),
            'review' => $this->review->toArray(['email', 'ip_address']),
            'reviews' => $this->reloadedReviews(),
        ];
    }

    public function successful(): bool
    {
        if (false === $this->errors) {
            glsr()->sessionClear();
            return true;
        }
        return false;
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
        $this->blacklisted = $validator->blacklisted;
        $this->errors = $validator->errors;
        $this->message = $validator->message;
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

    protected function custom(): array
    {
        return glsr(CustomFieldsDefaults::class)->filter($this->request->toArray());
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

    protected function setProperties(array $properties): void
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

    protected function type(): string
    {
        $reviewTypes = glsr()->retrieveAs('array', 'review_types');
        return array_key_exists($this->type, $reviewTypes) ? $this->type : 'local';
    }
}
