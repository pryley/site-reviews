<?php

namespace GeminiLabs\SiteReviews\Tests;

use Faker\Factory;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Honeypot;
use GeminiLabs\SiteReviews\Modules\Validator\BlacklistValidator;
use GeminiLabs\SiteReviews\Modules\Validator\CustomValidator;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Modules\Validator\HoneypotValidator;
use GeminiLabs\SiteReviews\Modules\Validator\PermissionValidator;
use GeminiLabs\SiteReviews\Modules\Validator\ReviewLimitsValidator;
use GeminiLabs\SiteReviews\Request;
use WP_Ajax_UnitTestCase;
use WPAjaxDieContinueException;
use WPAjaxDieStopException;

class ValidationTest extends WP_Ajax_UnitTestCase
{
    use Setup;

    protected $faker;
    protected $ipaddress;
    protected $messageFailed;
    protected $messageFailedBlacklist;
    protected $messageFailedCustom;
    protected $messageFailedPermission;
    protected $messageFailedReviewLimits;
    protected $messageFailedValidation;
    protected $messageSuccess;
    protected $request;

    public function set_up()
    {
        parent::set_up();
        $this->faker = Factory::create();
        $this->ipaddress = Helper::getIpAddress();
        $this->request = new Request([
            '_action' => 'submit-review',
            '_nonce' => wp_create_nonce('submit-review'), // wp_ajax_* is used in tests (?)
            '_post_id' => '13',
            '_referer' => $this->referer,
            'excluded' => '[]',
            'form_id' => $this->faker->slug,
            'content' => '',
            'email' => '',
            'name' => '',
            'rating' => '',
            'terms' => '',
            // 'terms_exist' => 1,
            'title' => '',
        ]);
        $this->messageFailed = 'The review submission failed. Please notify the site administrator.';
        $this->messageFailedHoneypot = 'This review has been flagged as possible spam and cannot be submitted.';
        $this->messageFailedBlacklist = 'Your review cannot be submitted at this time.';
        $this->messageFailedCustom = 'Bad review.';
        $this->messageFailedPermission = 'You must be logged in to submit a review.';
        $this->messageFailedReviewLimits = 'You have already submitted a review.';
        $this->messageFailedValidation = 'Please fix the submission errors.';
        $this->messageSuccess = 'Your review has been submitted!';
    }

    public function test_blacklist_validation()
    {
        add_filter('site-reviews/validators', function () {
            return [BlacklistValidator::class];
        });
        $blacklist = "xxx\n \napple";
        $response = $this->assertJsonSuccess($this->request());
        $this->assertTrue($response->data->review->is_approved);
        $this->assertEquals('publish', $response->data->review->status);
        $this->assertEquals($response->data->message, $this->messageSuccess);
        glsr(OptionManager::class)->set('settings.submissions.blacklist.action', 'reject');
        glsr(OptionManager::class)->set('settings.submissions.blacklist.entries', $blacklist);
        glsr(OptionManager::class)->set('settings.submissions.blacklist.integration', '');
        $this->assertJsonError($this->request(['content' => 'Give me a xxx!!']));
        $this->assertJsonError($this->request(['email' => 'john@apple.com']));
        $this->assertJsonSuccess($this->request(['email' => 'john@microsoft.com']));
        $this->assertJsonError($this->request(['name' => 'Johnxxx Doe']));
        $this->assertJsonError($this->request(['title' => 'This is a xxx title']));
        glsr(OptionManager::class)->set('settings.submissions.blacklist.entries', "{$blacklist}\n{$this->ipaddress}");
        $this->assertJsonError($this->request());
        glsr(OptionManager::class)->set('settings.submissions.blacklist.integration', 'comments');
        $this->assertJsonSuccess($this->request());
        update_option('disallowed_keys', $blacklist);
        $this->assertJsonError($this->request(['content' => 'Give me a xxx!!']));
        $this->assertJsonError($this->request(['email' => 'john@apple.com']));
        $this->assertJsonError($this->request(['name' => 'Johnxxx Doe']));
        $this->assertJsonError($this->request(['title' => 'This is a xxx title']));
        update_option('disallowed_keys', "{$blacklist}\n{$this->ipaddress}");
        $response1 = $this->assertJsonError($this->request());
        $this->assertEquals($response1->data->message, $this->messageFailedBlacklist);
        update_option('disallowed_keys', $blacklist);
        glsr(OptionManager::class)->set('settings.submissions.blacklist.action', 'unapprove');
        $response2 = $this->assertJsonSuccess($this->request(['email' => 'john@apple.com']));
        $this->assertFalse($response2->data->review->is_approved);
        $this->assertEquals('pending', $response2->data->review->status);
    }

    public function test_custom_validation()
    {
        add_filter('site-reviews/validators', function () {
            return [CustomValidator::class];
        });
        $this->assertJsonSuccess($this->request());
        add_filter('site-reviews/validate/custom', function () {
            return $this->messageFailedCustom;
        });
        $response1 = $this->assertJsonError($this->request());
        $this->assertEquals($response1->data->message, $this->messageFailedCustom);
        add_filter('site-reviews/validate/custom', '__return_false', 11);
        $response2 = $this->assertJsonError($this->request());
        $this->assertEquals($response2->data->message, $this->messageFailed);
    }

    public function test_default_validation()
    {
        add_filter('site-reviews/validators', function () {
            return [DefaultValidator::class];
        });
        glsr(OptionManager::class)->set('settings.submissions.required', ['rating', 'title', 'content', 'name', 'email', 'terms']);
        $response1 = $this->assertJsonError($this->request());
        $response2 = $this->assertJsonSuccess($this->request([
            'content' => $this->faker->text,
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'rating' => 5,
            'terms' => 1,
            'title' => $this->faker->sentence,
        ]));
        $this->assertCount(6, (array) $response1->data->errors);
        $this->assertEquals($response1->data->message, $this->messageFailedValidation);
        $this->assertEquals($response2->data->message, $this->messageSuccess);
    }

    public function test_multiple_validation()
    {
        $this->assertJsonError($this->request());
    }

    public function test_honeypot_validation()
    {
        add_filter('site-reviews/validators', function () {
            return [HoneypotValidator::class];
        });
        $formId = 'glsr-12345678';
        $honeypotHash = glsr(Honeypot::class)->hash($formId);
        $response1 = $this->assertJsonError($this->request());
        $response2 = $this->assertJsonError($this->request(['form_id' => $formId, $honeypotHash => 'x']));
        $response3 = $this->assertJsonSuccess($this->request(['form_id' => $formId, $honeypotHash => '']));
        $this->assertEquals($response1->data->message, $this->messageFailedHoneypot);
        $this->assertEquals($response2->data->message, $this->messageFailedHoneypot);
        $this->assertEquals($response3->data->message, $this->messageSuccess);
    }

    public function test_permission_validation()
    {
        add_filter('site-reviews/validators', function () {
            return [PermissionValidator::class];
        });
        $response1 = $this->assertJsonSuccess($this->request());
        glsr(OptionManager::class)->set('settings.general.require.login', 'yes');
        $response2 = $this->assertJsonError($this->request());
        wp_set_current_user(self::factory()->user->create([
            'role' => 'editor',
        ]));
        $response3 = $this->assertJsonSuccess($this->request([
            '_nonce' => wp_create_nonce('submit-review'),
        ]));
        $this->assertEquals($response1->data->message, $this->messageSuccess);
        $this->assertEquals($response2->data->message, $this->messageFailedPermission);
        $this->assertEquals($response3->data->message, $this->messageSuccess);
    }

    public function test_review_limits_validation()
    {
        add_filter('site-reviews/validators', function () {
            return [ReviewLimitsValidator::class];
        });
        glsr(OptionManager::class)->set('settings.submissions.limit', 'ip_address');
        $this->assertJsonSuccess($this->request());
        $this->assertJsonError($this->request());
        glsr(OptionManager::class)->set('settings.submissions.limit', 'email');
        $this->assertJsonSuccess($this->request(['email' => 'john@apple.com']));
        $this->assertJsonError($this->request(['email' => 'john@apple.com']));
        glsr(OptionManager::class)->set('settings.submissions.limit_whitelist.email', 'john@apple.com');
        $this->assertJsonSuccess($this->request(['email' => 'john@apple.com']));
        glsr(OptionManager::class)->set('settings.submissions.limit', 'username');
        $this->assertJsonSuccess($this->request());
        wp_set_current_user(self::factory()->user->create([
            'role' => 'editor',
            'user_login' => 'test_user',
        ]));
        $nonce = wp_create_nonce('submit-review');
        $response1 = $this->assertJsonSuccess($this->request(['_nonce' => $nonce]));
        $response2 = $this->assertJsonError($this->request(['_nonce' => $nonce]));
    }

    /**
     * @return object
     */
    protected function assertJsonError(array $request)
    {
        $response = $this->performAjaxRequest($request);
        $this->assertObjectHasAttribute('success', $response);
        $this->assertFalse($response->success);
        return $response;
    }

    /**
     * @return object
     */
    protected function assertJsonSuccess(array $request)
    {
        $response = $this->performAjaxRequest($request);
        $this->assertObjectHasAttribute('success', $response);
        $this->assertTrue($response->success);
        return $response;
    }

    /**
     * @return object
     */
    protected function performAjaxRequest($request)
    {
        $action = glsr()->prefix.'action';
        $_POST['_ajax_request'] = true;
        $_POST[glsr()->id] = $request;
        try {
            $this->_handleAjax($action);
        } catch (WPAjaxDieContinueException $e) {
        } catch (WPAjaxDieStopException $e) {
            error_log(print_r('WPAjaxDieStopException: '.$e->getMessage(), true));
        }
        $response = json_decode($this->_last_response);
        // Empty _last_response so we can call ajax_response more than once in the same method.
        $this->_last_response = '';
        $this->assertIsObject($response);
        return $response;
    }

    /**
     * @return array
     */
    protected function request(array $mergeWith = [])
    {
        $request = clone $this->request;
        return $request->merge($mergeWith)->toArray();
    }
}
