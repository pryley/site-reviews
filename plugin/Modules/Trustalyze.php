<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Review;

class Trustalyze
{
    const API_URL = 'https://www.trustalyze.com/api/rbs/';
    const WEB_URL = 'https://trustalyze.com/plans?ref=105';

    public $message;
    public $response;
    public $success;

    /**
     * @return mixed
     */
    public function __get($key)
    {
        return property_exists($this, $key)
            ? $this->$key
            : Arr::get($this->response, $key, null);
    }

    /**
     * @return self
     */
    public function activateKey($apiKey = '', $email = '')
    {
        $this->send('api_key_activation.php', [
            'body' => [
                'apikey' => $apiKey ?: 0,
                'domain' => get_home_url(),
                'email' => $email ?: 0,
            ],
        ]);
        return $this;
    }

    /**
     * @return self
     */
    public function reset()
    {
        $this->message = '';
        $this->response = [];
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
        $trustalyzeResponse = [
            'reply' => Str::truncate($review->response, 300),
            'review_id' => glsr(Database::class)->get($review->ID, 'trustalyze'), // this is the trustalyze review ID
            'review_transaction_id' => $review->review_id,
            'type' => 'M',
        ];
        return apply_filters('site-reviews/trustalyze/response', $trustalyzeResponse, $review);
    }

    /**
     * @return array
     */
    protected function getBodyForReview(Review $review)
    {
        $trustalyzeReview = [
            'domain' => get_home_url(),
            'firstname' => Str::truncate(Str::convertName($review->author, 'first'), 25),
            'rate' => $review->rating,
            'review_transaction_id' => $review->review_id,
            'reviews' => Str::truncate($review->content, 280),
            'title' => Str::truncate($review->title, 35),
            'transaction' => Application::ID, // woocommerce field, not needed for Site Reviews
        ];
        return apply_filters('site-reviews/trustalyze/review', $trustalyzeReview, $review);
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
            $responseData = (array) json_decode($responseBody, true);
            $this->response = array_shift($responseData);
            $this->message = Arr::get($this->response, 'msg');
            $this->success = 'success' === Arr::get($this->response, 'result') || 'yes' === Arr::get($this->response, 'success'); // @todo remove this ugly hack!
            if (200 !== $responseCode) {
                glsr_log()->error('Bad response code ['.$responseCode.']');
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
            'body' => null,
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
