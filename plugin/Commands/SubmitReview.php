<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Helper;

class SubmitReview
{
	public $ajaxRequest;
	public $assignedTo;
	public $author;
	public $blacklisted;
	public $category;
	public $content;
	public $email;
	public $formId;
	public $ipAddress;
	public $rating;
	public $referrer;
	public $request;
	public $terms;
	public $title;

	public function __construct( $input )
	{
		$this->ajaxRequest = isset( $input['ajax_request'] );
		$this->assignedTo = is_numeric( $input['assign_to'] )
			? $input['assign_to']
			: '';
		$this->author = $input['name'];
		$this->blacklisted = false;
		$this->category = $input['category'];
		$this->content = $input['content'];
		$this->email = $input['email'];
		$this->formId = $input['form_id'];
		$this->ipAddress = glsr( Helper::class )->getIpAddress();
		$this->rating = intval( $input['rating'] );
		$this->referrer = $input['_wp_http_referer'];
		$this->request = $input;
		$this->terms = isset( $input['terms'] );
		$this->title = $input['title'];
	}
}
