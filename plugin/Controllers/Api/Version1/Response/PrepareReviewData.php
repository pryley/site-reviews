<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Response;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class PrepareReviewData
{
    public array $data;

    public array $fields;

    public \WP_REST_Post_Meta_Fields $meta;

    public \WP_REST_Request $request;

    public Review $review;

    /**
     * @param string[] $fields
     */
    public function __construct(array $fields, Review $review, \WP_REST_Request $request)
    {
        $this->data = [];
        $this->fields = $fields;
        $this->meta = new \WP_REST_Post_Meta_Fields(glsr()->post_type);
        $this->request = $request;
        $this->review = $review;
    }

    public function data(): array
    {
        foreach ($this->fields as $field) {
            [$key] = explode('.', $field);
            $method = Helper::buildMethodName('prepare', $key);
            if (method_exists($this, $method)) {
                call_user_func([$this, $method]);
            } else {
                $this->data[$key] = glsr()->filter("rest-api/reviews/prepare/{$key}", '', $this);
            }
        }
        return $this->data;
    }

    protected function prepareAssignedPosts(): void
    {
        $this->data['assigned_posts'] = $this->review->assigned_posts;
    }

    protected function prepareAssignedTerms(): void
    {
        $this->data['assigned_terms'] = $this->review->assigned_terms;
    }

    protected function prepareAssignedUsers(): void
    {
        $this->data['assigned_users'] = $this->review->assigned_users;
    }

    protected function prepareAuthor(): void
    {
        $this->data['author'] = $this->review->user_id;
    }

    protected function prepareAvatar(): void
    {
        $this->data['avatar'] = $this->review->avatar;
    }

    protected function prepareContent(): void
    {
        $this->data['content'] = $this->review->content;
    }

    protected function prepareCustom(): void
    {
        $this->data['custom'] = $this->review->custom()->toArray();
    }

    protected function prepareDate(): void
    {
        $this->data['date'] = mysql_to_rfc3339($this->review->date); // phpcs:ignore
    }

    protected function prepareDateGmt(): void
    {
        $date = $this->review->date_gmt;
        if ('0000-00-00 00:00:00' === $date) {
            $date = get_gmt_from_date($this->review->date);
        }
        $this->data['date_gmt'] = mysql_to_rfc3339($date); // phpcs:ignore
    }

    protected function prepareEmail(): void
    {
        $this->data['email'] = $this->review->email;
    }

    protected function prepareId(): void
    {
        $this->data['id'] = $this->review->ID;
    }

    protected function prepareIpAddress(): void
    {
        $this->data['ip_address'] = $this->review->ip_address;
    }

    protected function prepareIsApproved(): void
    {
        $this->data['is_approved'] = $this->review->is_approved;
    }

    protected function prepareIsModified(): void
    {
        $this->data['is_modified'] = $this->review->is_modified;
    }

    protected function prepareIsPinned(): void
    {
        $this->data['is_pinned'] = $this->review->is_pinned;
    }

    protected function prepareIsVerified(): void
    {
        $this->data['is_verified'] = $this->review->is_verified;
    }

    protected function prepareMeta(): void
    {
        $this->data['meta'] = $this->meta->get_value($this->review->ID, $this->request);
    }

    protected function prepareModified(): void
    {
        $this->data['modified'] = mysql_to_rfc3339($this->review->post()->post_modified); // phpcs:ignore
    }

    protected function prepareModifiedGmt(): void
    {
        $this->data['modified_gmt'] = mysql_to_rfc3339($this->review->post()->post_modified_gmt); // phpcs:ignore
    }

    protected function prepareName(): void
    {
        $this->data['name'] = $this->review->author;
    }

    protected function prepareRating(): void
    {
        $this->data['rating'] = $this->review->rating;
    }

    protected function prepareResponse(): void
    {
        $this->data['response'] = $this->review->response;
    }

    protected function prepareScore(): void
    {
        $this->data['score'] = $this->review->score;
    }

    protected function prepareStatus(): void
    {
        $this->data['status'] = $this->review->status;
    }

    protected function prepareTerms(): void
    {
        $this->data['terms'] = $this->review->terms;
    }

    protected function prepareTitle(): void
    {
        $this->data['title'] = $this->review->title;
    }

    protected function prepareType(): void
    {
        $this->data['type'] = $this->review->type;
    }
}
