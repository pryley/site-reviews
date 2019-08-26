<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Review;

class Rebusify
{
    const API_URL = 'https://www.rebusify.com/api/rbs/';

    /**
     * @return void|array
     */
    public function sendReview(Review $review)
    {
        return $this->send('index.php', [
            'body' => $this->getBodyForReview($review),
            'timeout' => 120,
        ]);
    }

    /**
     * @return void|array
     */
    public function sendReviewResponse(Review $review)
    {
        return $this->send('fetch_customer_reply.php', [
            'body' => $this->getBodyForResponse($review),
        ]);
    }

    /**
     * @return array
     */
    protected function getBodyForResponse(Review $review)
    {
        $rebusifyResponse = [
            'reply' => $review->response, // what is the 300 character limit for?
            'review_id' => '', // @todo
            'review_transaction_id' => '', // @todo
            'type' => 'M',
        ];
        return apply_filters('site-reviews/rebusify/response', $rebusifyResponse, $review);
    }

    /**
     * @return array
     */
    protected function getBodyForReview(Review $review)
    {
        $rebusifyReview = [
            'domain' => get_site_url(),
            'firstname' => $review->name, // what is the 25 character limit for?
            'rate' => $review->rating,
            'review_transaction_id' => '', // @todo
            'reviews' => $review->content, // what is the 280 character limit for?
            'title' => $review->title, // what is the 35 character limit for?
            'transaction' => '', // @todo
        ];
        return apply_filters('site-reviews/rebusify/review', $rebusifyReview, $review);
    }

    /**
     * @return void|array
     */
    protected function send($endpoint, $args)
    {
        $args = wp_parse_args($args, [
            'blocking' => false,
            'body' => [],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'redirection' => 5,
            'sslverify' => false,
            'timeout' => 5,
        ]);
        $response = wp_remote_post(trailingslashit(static::API_URL).$endpoint, $args);
        if (is_wp_error($response)) {
            glsr_log()->error('REBUSIFY: '.$response->get_error_message());
            return;
        }
        if (200 === wp_remote_retrieve_response_code($response)) {
            $responsedata = wp_remote_retrieve_body($response);
            return json_decode($responsedata, true);
        }
    }
}
