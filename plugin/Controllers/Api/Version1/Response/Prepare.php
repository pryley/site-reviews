<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Response;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class Prepare
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var array
     */
    public $fields;

    /**
     * @var \GeminiLabs\SiteReviews\Modules\Html\ReviewHtml
     */
    public $html;

    /**
     * @var \WP_REST_Post_Meta_Fields
     */
    public $meta;

    /**
     * @var \WP_REST_Request
     */
    public $request;

    /**
     * @var Review
     */
    public $review;

    /**
     * @param string[] $fields
     */
    public function __construct($fields, Review $review, \WP_REST_Request $request)
    {
        $this->data = [];
        $this->html = $review->build();
        $this->fields = $fields;
        $this->meta = new \WP_REST_Post_Meta_Fields(glsr()->post_type);
        $this->request = $request;
        $this->review = $review;
    }

    /**
     * @return array
     */
    public function item()
    {
        return $this->data;
    }

    /**
     * @param string $method
     *
     * @return void
     */
    public function __call($method, array $args = [])
    {
        [$key] = explode('.', $method);
        if (!rest_is_field_included($key, $this->fields)) {
            return;
        }
        $method = Helper::buildMethodName('prepare', $key);
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            $this->data[$key] = glsr()->filter("rest-api/reviews/prepare/{$key}", '', $this);
        }
    }

    protected function prepareAssignedPosts()
    {
        $this->data['assigned_posts'] = $this->review->assigned_posts;
    }

    protected function prepareAssignedTerms()
    {
        $this->data['assigned_terms'] = $this->review->assigned_terms;
    }

    protected function prepareAssignedUsers()
    {
        $this->data['assigned_users'] = $this->review->assigned_users;
    }

    protected function prepareAuthor()
    {
        $this->data['author'] = $this->review->user_id;
    }

    protected function prepareAvatar()
    {
        $this->data['avatar'] = $this->review->avatar;
    }

    protected function prepareContent()
    {
        $this->data['content'] = $this->review->content;
    }

    protected function prepareCustom()
    {
        $this->data['custom'] = $this->review->custom()->toArray();
    }

    protected function prepareDate()
    {
        $this->data['date'] = mysql_to_rfc3339($this->review->date); // phpcs:ignore
    }

    protected function prepareDateGmt()
    {
        $date = $this->review->date_gmt;
        if ('0000-00-00 00:00:00' === $date) {
            $date = get_gmt_from_date($this->review->date);
        }
        $this->data['date_gmt'] = mysql_to_rfc3339($date); // phpcs:ignore
    }

    protected function prepareEmail()
    {
        $this->data['email'] = $this->review->email;
    }

    protected function prepareId()
    {
        $this->data['id'] = $this->review->ID;
    }

    protected function prepareIpAddress()
    {
        $this->data['ip_address'] = $this->review->ip_address;
    }

    protected function prepareIsApproved()
    {
        $this->data['is_approved'] = $this->review->is_approved;
    }

    protected function prepareIsModified()
    {
        $this->data['is_modified'] = $this->review->is_modified;
    }

    protected function prepareIsPinned()
    {
        $this->data['is_pinned'] = $this->review->is_pinned;
    }

    protected function prepareIsVerified()
    {
        $this->data['is_verified'] = $this->review->is_verified;
    }

    protected function prepareMeta()
    {
        $this->data['meta'] = $this->meta->get_value($this->review->ID, $this->request);
    }

    protected function prepareModified()
    {
        $this->data['modified'] = mysql_to_rfc3339($this->review->post()->post_modified); // phpcs:ignore
    }

    protected function prepareModifiedGmt()
    {
        $this->data['modified_gmt'] = mysql_to_rfc3339($this->review->post()->post_modified_gmt); // phpcs:ignore
    }

    protected function prepareName()
    {
        $this->data['name'] = $this->review->author;
    }

    protected function prepareRating()
    {
        $this->data['rating'] = $this->review->rating;
    }

    protected function prepareResponse()
    {
        $this->data['response'] = $this->review->response;
    }

    protected function prepareScore()
    {
        $this->data['score'] = $this->review->score;
    }

    protected function prepareStatus()
    {
        $this->data['status'] = $this->review->status;
    }

    protected function prepareTerms()
    {
        $this->data['terms'] = $this->review->terms;
    }

    protected function prepareTitle()
    {
        $this->data['title'] = $this->review->title;
    }

    protected function prepareType()
    {
        $this->data['type'] = $this->review->type;
    }
}
