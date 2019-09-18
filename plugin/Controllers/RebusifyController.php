<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Rebusify;
use GeminiLabs\SiteReviews\Review;

class RebusifyController extends Controller
{
    protected $apiKey = 'settings.general.rebusify_serial';
    protected $emailKey = 'settings.general.rebusify_email';
    protected $enabledKey = 'settings.general.rebusify';

    /**
     * @return array
     * @action site-reviews/settings/callback
     */
    public function filterSettingsCallback(array $settings)
    {
        if ('yes' !== glsr_get($settings, $this->enabledKey)) {
            return $settings;
        }
        $isApiKeyModified = $this->isEmptyOrModified($this->apiKey, $settings);
        $isEmailModified = $this->isEmptyOrModified($this->emailKey, $settings);
        $isAccountVerified = glsr(OptionManager::class)->get('rebusify', false);
        if (!$isAccountVerified || $isApiKeyModified || $isEmailModified) {
            $settings = $this->sanitizeRebusifySettings($settings);
        }
        return $settings;
    }

    /**
     * Triggered when a review is created.
     * @return void
     * @action site-reviews/review/created
     */
    public function onCreated(Review $review)
    {
        glsr_log()->debug('onCreated event triggered')->debug([
            'can-proceed' => $this->canProceed($review),
            'has-publish-status' => 'publish' === $review->status,
        ]);
        if ($this->canProceed($review) && 'publish' === $review->status) {
            glsr_log()->debug('Sending review to Rebusify');
            $rebusify = glsr(Rebusify::class)->sendReview($review);
            if ($rebusify->success) {
                glsr(Database::class)->set($review->ID, 'rebusify', true);
            }
        }
    }

    /**
     * Triggered when a review is reverted to its original title/content/date_timestamp.
     * @return void
     * @action site-reviews/review/reverted
     */
    public function onReverted(Review $review)
    {
        glsr_log()->debug('onReverted event triggered')->debug([
            'can-proceed' => $this->canProceed($review),
            'has-publish-status' => 'publish' === $review->status,
        ]);
        if ($this->canProceed($review) && 'publish' === $review->status) {
            glsr_log()->debug('Sending review to Rebusify');
            $rebusify = glsr(Rebusify::class)->sendReview($review);
            if ($rebusify->success) {
                glsr(Database::class)->set($review->ID, 'rebusify', true);
            }
        }
    }

    /**
     * Triggered when an existing review is updated.
     * @return void
     * @action site-reviews/review/saved
     */
    public function onSaved(Review $review)
    {
        glsr_log()->debug('onSaved event triggered')->debug([
            'can-proceed' => $this->canProceed($review),
            'has-publish-status' => 'publish' === $review->status,
        ]);
        if ($this->canProceed($review) && 'publish' === $review->status) {
            glsr_log()->debug('Sending review to Rebusify');
            $rebusify = glsr(Rebusify::class)->sendReview($review);
            if ($rebusify->success) {
                glsr(Database::class)->set($review->ID, 'rebusify', true);
            }
        }
    }

    /**
     * Triggered when a review's response is added or updated.
     * @param int $metaId
     * @param int $postId
     * @param string $metaKey
     * @return void
     * @action updated_postmeta
     */
    public function onUpdatedMeta($metaId, $postId, $metaKey)
    {
        $review = glsr_get_review($postId);
        glsr_log()->debug('onUpdatedMeta event triggered')->debug([
            'can-proceed' => $this->canProceed($review, 'rebusify_response'),
            'has-publish-status' => 'publish' === $review->status,
            'is-response' => '_response' === $metaKey,
        ]);
        if (!$this->isReviewPostId($review->ID)
            || !$this->canProceed($review, 'rebusify_response')
            || '_response' !== $metaKey) {
            return;
        }
        glsr_log()->debug('Sending merchant response to Rebusify');
        $rebusify = glsr(Rebusify::class)->sendReviewResponse($review);
        if ($rebusify->success) {
            glsr(Database::class)->set($review->ID, 'rebusify_response', true);
        }
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    protected function canProceed(Review $review, $metaKey = 'rebusify')
    {
        return !glsr(Database::class)->get($review->ID, $metaKey)
            && glsr(OptionManager::class)->getBool($this->enabledKey);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isEmptyOrModified($key, array $settings)
    {
        $oldValue = glsr_get_option($key);
        $newValue = glsr_get($settings, $key);
        return empty($newValue) || $newValue !== $oldValue;
    }

    /**
     * @return array
     */
    protected function sanitizeRebusifySettings(array $settings)
    {
        $rebusify = glsr(Rebusify::class)->activateKey(
            glsr_get($settings, $this->apiKey),
            glsr_get($settings, $this->emailKey)
        );
        if ($rebusify->success) {
            glsr(OptionManager::class)->set('rebusify', glsr_get($rebusify->response, 'producttype'));
        } else {
            glsr(OptionManager::class)->delete('rebusify');
            $settings = glsr(Helper::class)->dataSet($settings, $this->enabledKey, 'no');
            glsr(Notice::class)->addError(sprintf(
                __('Your Rebusify account details could not be verified, please try again. %s', 'site-reviews'),
                '('.$rebusify->message.')'
            ));
        }
        return $settings;
    }
}
