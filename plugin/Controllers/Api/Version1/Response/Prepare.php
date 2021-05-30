<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Response;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Review;

class Prepare
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var \GeminiLabs\SiteReviews\Modules\Html\ReviewHtml
     */
    protected $html;

    /**
     * @var \WP_REST_Request
     */
    protected $request;

    /**
     * @var \GeminiLabs\SiteReviews\Review
     */
    protected $review;

    /**
     * @param string[] $fields
     */
    public function __construct($fields, Review $review, \WP_REST_Request $request)
    {
        $this->data = [];
        $this->html = $review->build();
        $this->fields = $fields;
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
     * @return void
     */
    public function __call($method, array $args = [])
    {
        list($parent) = explode('.', $method);
        $method = Helper::buildMethodName($parent, 'prepare');
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
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
        $this->data['avatar'] = [];
        if (rest_is_field_included('avatar.raw', $this->fields)) {
            $this->data['avatar']['raw'] = $this->review->avatar;
        }
        if (rest_is_field_included('avatar.rendered', $this->fields)) {
            $this->data['avatar']['rendered'] = $this->html->avatar;
        }
    }

    protected function prepareContent()
    {
        $this->data['content'] = [];
        if (rest_is_field_included('content.raw', $this->fields)) {
            $this->data['content']['raw'] = $this->review->content;
        }
        if (rest_is_field_included('content.rendered', $this->fields)) {
            $this->data['content']['rendered'] = $this->html->content;
        }
    }

    protected function prepareCustom()
    {
        $this->data['custom'] = $this->review->custom()->toArray();
    }

    protected function prepareDate()
    {
        $this->data['date'] = [];
        if (rest_is_field_included('date.raw', $this->fields)) {
            $this->data['date']['raw'] = mysql_to_rfc3339($this->review->date);
        }
        if (rest_is_field_included('date.rendered', $this->fields)) {
            $this->data['date']['rendered'] = $this->html->date;
        }
    }

    protected function prepareDateGmt()
    {
        $this->data['date_gmt'] = mysql_to_rfc3339($this->review->date_gmt);
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

    protected function prepareModified()
    {
        $this->data['modified'] = mysql_to_rfc3339($this->review->post()->post_modified);
    }

    protected function prepareModifiedGmt()
    {
        $this->data['modified_gmt'] = mysql_to_rfc3339($this->review->post()->post_modified_gmt);
    }

    protected function prepareName()
    {
        $this->data['name'] = [];
        if (rest_is_field_included('name.raw', $this->fields)) {
            $this->data['name']['raw'] = $this->review->author;
        }
        if (rest_is_field_included('name.rendered', $this->fields)) {
            $this->data['name']['rendered'] = $this->html->author;
        }
    }

    protected function prepareRating()
    {
        $this->data['rating'] = [];
        if (rest_is_field_included('rating.raw', $this->fields)) {
            $this->data['rating']['raw'] = $this->review->rating;
        }
        if (rest_is_field_included('rating.rendered', $this->fields)) {
            $this->data['rating']['rendered'] = $this->html->rating;
        }
    }

    protected function prepareResponse()
    {
        $this->data['response'] = [];
        if (rest_is_field_included('response.raw', $this->fields)) {
            $this->data['response']['raw'] = $this->review->response;
        }
        if (rest_is_field_included('response.rendered', $this->fields)) {
            $this->data['response']['rendered'] = $this->html->response;
        }
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
        $this->data['title'] = [];
        if (rest_is_field_included('title.raw', $this->fields)) {
            $this->data['title']['raw'] = $this->review->title;
        }
        if (rest_is_field_included('title.rendered', $this->fields)) {
            $this->data['title']['rendered'] = $this->html->title;
        }
    }

    protected function prepareType()
    {
        $this->data['type'] = $this->review->type;
    }
}
