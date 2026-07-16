<?php

namespace GeminiLabs\SiteReviews\Tests;

use Faker\Factory;
use Faker\Generator;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Request;

/*
 * The state and request helpers a review submission needs, kept on a trait with
 * DECLARED properties (not dynamic ones) so Pest's test-case-bound closures can
 * reach $this->faker, $this->submission, $this->messageSuccess and friends.
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
     * resetPluginState() must have run first — the nonce and the referer depend on it.
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
        // Each call is a separate request from the same visitor, seconds apart. The clock does not
        // run in a test, so the router's five-second parallel-request lock is still down from the
        // last call and would refuse this as a single-packet attack; releasing it stands in for
        // the time passing.
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
     * A copy of the base submission merged with overrides. Deliberately NOT Request::merge(),
     * which goes through wp_parse_args()/array_merge() and RENUMBERS integer-like keys — the
     * honeypot field name is an 8-char hex hash, all digits ~2% of the time, and such a key
     * silently becomes 0 so the honeypot value never reaches the validator. array_replace() keeps it.
     */
    protected function request(array $mergeWith = []): array
    {
        return array_replace($this->submission->toArray(), $mergeWith);
    }
}
