<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Rebusify;
use GeminiLabs\SiteReviews\Review;

class RebusifyController extends Controller
{
    protected $apiKey = 'settings.general.rebusify_serial';
    protected $emailKey = 'settings.general.rebusify_email';
    protected $enabledKey = 'settings.general.rebusify';
    protected $rebusifyKey = '_glsr_rebusify';

    /**
     * @return array
     * @filter site-reviews/settings/callback
     */
    public function filterSettingsCallback(array $settings)
    {
        if ('yes' !== Arr::get($settings, $this->enabledKey)) {
            return $settings;
        }
        $isApiKeyModified = $this->isEmptyOrModified($this->apiKey, $settings);
        $isEmailModified = $this->isEmptyOrModified($this->emailKey, $settings);
        $isAccountVerified = glsr(OptionManager::class)->getWP($this->rebusifyKey, false);
        if (!$isAccountVerified || $isApiKeyModified || $isEmailModified) {
            $settings = $this->sanitizeRebusifySettings($settings);
        }
        return $settings;
    }

    /**
     * @param string $template
     * @return array
     * @filter site-reviews/interpolate/partials/form/table-row-multiple
     */
    public function filterSettingsTableRow(array $context, $template, array $data)
    {
        if ($this->enabledKey !== Arr::get($data, 'field.path')) {
            return $context;
        }
        $rebusifyProductType = glsr(OptionManager::class)->getWP($this->rebusifyKey);
        if ('P' === $rebusifyProductType) {
            return $context;
        }
        if ('F' === $rebusifyProductType && 'yes' === glsr_get_option('general.rebusify')) {
            $button = $this->buildUpgradeButton();
        } else {
            $button = $this->buildCreateButton();
        }
        $context['field'].= $button;
        return $context;
    }

    /**
     * Triggered when a review is created.
     * @return void
     * @action site-reviews/review/created
     */
    public function onCreated(Review $review)
    {
        if (!$this->canPostReview($review)) {
            return;
        }
        $rebusify = glsr(Rebusify::class)->sendReview($review);
        if ($rebusify->success) {
            glsr(Database::class)->set($review->ID, 'rebusify', $rebusify->review_id);
        }
    }

    /**
     * Triggered when a review is reverted to its original title/content/date_timestamp.
     * @return void
     * @action site-reviews/review/reverted
     */
    public function onReverted(Review $review)
    {
        if (!$this->canPostReview($review)) {
            return;
        }
        $rebusify = glsr(Rebusify::class)->sendReview($review);
        if ($rebusify->success) {
            glsr(Database::class)->set($review->ID, 'rebusify', $rebusify->review_id);
        }
    }

    /**
     * Triggered when an existing review is updated.
     * @return void
     * @action site-reviews/review/saved
     */
    public function onSaved(Review $review)
    {
        if (!$this->canPostReview($review)) {
            return;
        }
        $rebusify = glsr(Rebusify::class)->sendReview($review);
        if ($rebusify->success) {
            glsr(Database::class)->set($review->ID, 'rebusify', $rebusify->review_id);
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
        if (!$this->canPostResponse($review) || '_response' !== $metaKey) {
            return;
        }
        $rebusify = glsr(Rebusify::class)->sendReviewResponse($review);
        if ($rebusify->success) {
            glsr(Database::class)->set($review->ID, 'rebusify_response', true);
        }
    }

    /**
     * @return string
     */
    protected function buildCreateButton()
    {
        return glsr(Builder::class)->a(__('Create Your Rebusify Account', 'site-reviews'), [
            'class' => 'button',
            'href' => Rebusify::WEB_URL,
            'target' => '_blank',
        ]);
    }

    /**
     * @return string
     */
    protected function buildUpgradeButton()
    {
        $build = glsr(Builder::class);
        $notice = $build->p(__('Free Rebusify accounts are limited to 500 blockchain transactions per year.', 'site-reviews'));
        $button = $build->a(__('Upgrade Your Rebusify Plan', 'site-reviews'), [
            'class' => 'button',
            'href' => Rebusify::WEB_URL,
            'target' => '_blank',
        ]);
        return $build->div($notice.$button, [
            'class' => 'glsr-notice-inline notice inline notice-info',
        ]);
    }

    /**
     * @return bool
     */
    protected function canPostResponse(Review $review)
    {
        $requiredValues = [
            glsr(Database::class)->get($review->ID, 'rebusify'),
            $review->response,
            $review->review_id,
        ];
        return $this->canProceed($review, 'rebusify_response')
            && 'publish' === $review->status
            && 3 === count(array_filter($requiredValues));
    }

    /**
     * @return bool
     */
    protected function canPostReview(Review $review)
    {
        $requiredValues = [
            $review->author,
            $review->content,
            $review->rating,
            $review->review_id,
            $review->title,
        ];
        return $this->canProceed($review)
            && 'publish' === $review->status
            && 5 === count(array_filter($requiredValues));
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    protected function canProceed(Review $review, $metaKey = 'rebusify')
    {
        return glsr(OptionManager::class)->getBool($this->enabledKey)
            && $this->isReviewPostId($review->ID)
            && !$this->hasMetaKey($review, $metaKey);
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    protected function hasMetaKey(Review $review, $metaKey = 'rebusify')
    {
        return '' !== glsr(Database::class)->get($review->ID, $metaKey);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isEmptyOrModified($key, array $settings)
    {
        $oldValue = glsr_get_option($key);
        $newValue = Arr::get($settings, $key);
        return empty($newValue) || $newValue !== $oldValue;
    }

    /**
     * @return array
     */
    protected function sanitizeRebusifySettings(array $settings)
    {
        $rebusify = glsr(Rebusify::class)->activateKey(
            Arr::get($settings, $this->apiKey),
            Arr::get($settings, $this->emailKey)
        );
        if ($rebusify->success) {
            update_option($this->rebusifyKey, Arr::get($rebusify->response, 'producttype'));
        } else {
            delete_option($this->rebusifyKey);
            $settings = Arr::set($settings, $this->enabledKey, 'no');
            glsr(Notice::class)->addError(sprintf(
                __('Your Rebusify account details could not be verified, please try again. %s', 'site-reviews'),
                '('.$rebusify->message.')'
            ));
        }
        return $settings;
    }
}
