<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class Rebusify
{
    const API_URL = 'https://www.rebusify.com/api/rbs/';

    public $message;
    public $success;

    /**
     * @return self
     */
    public function reset()
    {
        $this->message = '';
        $this->success = false;
        return $this;
    }

    /**
     * @return self
     */
    public function sendReview(Review $review)
    {
        $this->send('index.php', [
            'body' => $this->getBodyForReview($review),
            'timeout' => 120,
        ]);
        return $this;
    }

    /**
     * @return self
     */
    public function sendReviewResponse(Review $review)
    {
        $this->send('fetch_customer_reply.php', [
            'body' => $this->getBodyForResponse($review),
        ]);
        return $this;
    }

    /**
     * @return array
     */
    protected function getBodyForResponse(Review $review)
    {
        $rebusifyResponse = [
            'reply' => glsr(Helper::class)->truncate($review->response, 300),
            'review_id' => '', // @todo
            'review_transaction_id' => $review->review_id,
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
            'firstname' => glsr(Helper::class)->truncate($review->name, 25),
            'rate' => $review->rating,
            'review_transaction_id' => $review->review_id,
            'reviews' => glsr(Helper::class)->truncate($review->content, 280),
            'title' => glsr(Helper::class)->truncate($review->title, 35),
            'transaction' => '', // woocommerce field, not needed for Site Reviews
        ];
        return apply_filters('site-reviews/rebusify/review', $rebusifyReview, $review);
    }

    /**
     * @param \WP_Error|array $response
     * @return void
     */
    protected function handleResponse($response)
    {
        if (is_wp_error($response)) {
            $this->message = $response->get_error_message();
        } else {
            $responseBody = wp_remote_retrieve_body($response);
            $responseCode = wp_remote_retrieve_response_code($response);
            $response = json_decode($responseBody, true);
            $this->message = glsr_get($response, 'msg');
            $this->success = 'success' == glsr_get($response, 'result');
            if (200 !== $responseCode) {
                $this->message = 'Bad response code ['.$responseCode.']';
            }
            if (!$this->success) {
                glsr_log()->error($this->message);
            }
        }
    }

    /**
     * @param string $endpoint
     * @return void
     */
    protected function send($endpoint, array $args = [])
    {
        $args = wp_parse_args($args, [
            'blocking' => false,
            'body' => [],
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'redirection' => 5,
            'sslverify' => false,
            'timeout' => 5,
        ]);
        $this->reset();
        $this->handleResponse(
            wp_remote_post(trailingslashit(static::API_URL).$endpoint, $args)
        );
    }
}
