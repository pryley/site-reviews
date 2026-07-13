<?php

namespace GeminiLabs\SiteReviews\Tests;

use Faker\Factory;
use Faker\Generator;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Request;

/*
 * The state and the request helpers the phpunit ValidationTest kept on its
 * test case. Pest binds a test closure to the test case, so a trait — with
 * DECLARED properties, not dynamic ones — carries them across unchanged:
 * $this->faker, $this->request, $this->messageSuccess and friends all mean
 * what they meant before.
 */

trait SubmitsReviews
{
    use InteractsWithAjax;

    protected Generator $faker;

    protected string $ipaddress;

    protected string $messageFailed;

    protected string $messageFailedBlacklist;

    protected string $messageFailedCustom;

    protected string $messageFailedDuplicate;

    protected string $messageFailedHoneypot;

    protected string $messageFailedPermission;

    protected string $messageFailedReviewLimits;

    protected string $messageFailedValidation;

    protected string $messageSuccess;

    protected Request $submission;

    /**
     * The port of ValidationTest::set_up(). resetPluginState() (the Setup
     * trait) must have run first — the nonce and the referer depend on it.
     */
    protected function setUpSubmitsReviews(): void
    {
        $this->setUpAjax();
        $this->faker = Factory::create();
        $this->ipaddress = Helper::clientIp();
        $this->submission = new Request([
            '_action' => 'submit-review',
            '_nonce' => wp_create_nonce('submit-review'), // wp_ajax_* is used in tests (?)
            '_post_id' => '13',
            '_referer' => referer(),
            'excluded' => '[]',
            'form_id' => $this->faker->slug(),
            'content' => '',
            'email' => '',
            'name' => '',
            'rating' => '',
            'terms' => '',
            // 'terms_exist' => 1,
            'title' => '',
        ]);
        $this->messageFailed = 'The review submission failed. Please notify the site administrator.';
        $this->messageFailedDuplicate = 'Duplicate review detected. It looks like you already said that!';
        $this->messageFailedHoneypot = 'This review has been flagged as possible spam and cannot be submitted.';
        $this->messageFailedBlacklist = 'Your review cannot be submitted at this time.';
        $this->messageFailedCustom = 'Bad review.';
        $this->messageFailedPermission = 'You must be logged in to submit a review.';
        $this->messageFailedReviewLimits = 'You have already submitted a review.';
        $this->messageFailedValidation = 'Please fix the form errors.';
        $this->messageSuccess = 'Your review has been submitted!';
    }

    /**
     * The payload goes into the failure message: "false is not true" says nothing
     * about WHY the submission was rejected, and the message the validator set is
     * the whole diagnosis.
     */
    protected function assertJsonError(array $request): object
    {
        $response = $this->performAjaxRequest($request);
        expect(get_object_vars($response))->toHaveKey('success');
        $this->assertFalse($response->success, 'Expected the submission to be rejected. Response: '.wp_json_encode($response));
        return $response;
    }

    protected function assertJsonSuccess(array $request): object
    {
        $response = $this->performAjaxRequest($request);
        expect(get_object_vars($response))->toHaveKey('success');
        $this->assertTrue($response->success, 'Expected the submission to be accepted. Response: '.wp_json_encode($response));
        return $response;
    }

    protected function performAjaxRequest(array $request): object
    {
        // Each call here is a separate HTTP request from the same visitor, seconds or
        // minutes apart. The clock does not run in a test, so the router's five-second
        // parallel-request lock is still down from the last one and would refuse this as
        // a single-packet attack. Releasing it is what stands in for the time passing.
        releaseMutexLock();
        $action = glsr()->prefix.'public_action';
        $_POST['_ajax_request'] = true;
        $_POST[glsr()->id] = $request;
        try {
            $this->handleAjax($action);
        } catch (WpAjaxDieContinueException $e) {
        } catch (WpAjaxDieStopException $e) {
            error_log(print_r('WpAjaxDieStopException: '.$e->getMessage(), true));
        }
        $response = json_decode($this->lastResponse);
        // Empty lastResponse so we can call ajax_response more than once in the same method.
        $this->lastResponse = '';
        expect($response)->toBeObject();
        return $response;
    }

    /**
     * A copy of the base submission, merged with the given overrides.
     *
     * Deliberately NOT Request::merge(): that goes through wp_parse_args(), i.e.
     * array_merge(), which RENUMBERS integer-like keys. The honeypot field name
     * is an 8-character hex hash — all digits perhaps 2% of the time, depending
     * on the site's salts — and such a key silently becomes 0, so the honeypot
     * value never reaches the validator. array_replace() keeps the key.
     */
    protected function request(array $mergeWith = []): array
    {
        return array_replace($this->submission->toArray(), $mergeWith);
    }
}
