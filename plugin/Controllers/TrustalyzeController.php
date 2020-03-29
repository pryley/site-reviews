<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Trustalyze;
use GeminiLabs\SiteReviews\Review;

class TrustalyzeController extends Controller
{
    protected $apiKey = 'settings.general.trustalyze_serial';
    protected $emailKey = 'settings.general.trustalyze_email';
    protected $enabledKey = 'settings.general.trustalyze';
    protected $trustalyzeKey = '_glsr_trustalyze';

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
        $isAccountVerified = glsr(OptionManager::class)->getWP($this->trustalyzeKey, false);
        if (!$isAccountVerified || $isApiKeyModified || $isEmailModified) {
            $settings = $this->sanitizeTrustalyzeSettings($settings);
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
        $isAccountValidated = !empty(glsr(OptionManager::class)->getWP($this->trustalyzeKey));
        $isIntegrationEnabled = glsr(OptionManager::class)->getBool('settings.general.trustalyze');
        if ($isAccountValidated && $isIntegrationEnabled) {
            return $context;
        }
        $context['field'].= $this->buildCreateButton();
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
        $trustalyze = glsr(Trustalyze::class)->sendReview($review);
        if ($trustalyze->success) {
            glsr(Database::class)->set($review->ID, 'trustalyze', $trustalyze->review_id);
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
        $trustalyze = glsr(Trustalyze::class)->sendReview($review);
        if ($trustalyze->success) {
            glsr(Database::class)->set($review->ID, 'trustalyze', $trustalyze->review_id);
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
        $trustalyze = glsr(Trustalyze::class)->sendReview($review);
        if ($trustalyze->success) {
            glsr(Database::class)->set($review->ID, 'trustalyze', $trustalyze->review_id);
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
        $trustalyze = glsr(Trustalyze::class)->sendReviewResponse($review);
        if ($trustalyze->success) {
            glsr(Database::class)->set($review->ID, 'trustalyze_response', true);
        }
    }

    /**
     * @return string
     */
    protected function buildCreateButton()
    {
        return glsr(Builder::class)->a(__('Create Your Trustalyze Account', 'site-reviews'), [
            'class' => 'button',
            'href' => Trustalyze::WEB_URL,
            'target' => '_blank',
        ]);
    }

    /**
     * @return bool
     */
    protected function canPostResponse(Review $review)
    {
        $requiredValues = [
            glsr(Database::class)->get($review->ID, 'trustalyze'),
            $review->response,
            $review->review_id,
        ];
        return $this->canProceed($review, 'trustalyze_response')
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
    protected function canProceed(Review $review, $metaKey = 'trustalyze')
    {
        return glsr(OptionManager::class)->getBool($this->enabledKey)
            && $this->isReviewPostId($review->ID)
            && !$this->hasMetaKey($review, $metaKey);
    }

    /**
     * @param string $metaKey
     * @return bool
     */
    protected function hasMetaKey(Review $review, $metaKey = 'trustalyze')
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
    protected function sanitizeTrustalyzeSettings(array $settings)
    {
        $trustalyze = glsr(Trustalyze::class)->activateKey(
            Arr::get($settings, $this->apiKey),
            Arr::get($settings, $this->emailKey)
        );
        if ($trustalyze->success) {
            update_option($this->trustalyzeKey, Arr::get($trustalyze->response, 'producttype'));
        } else {
            delete_option($this->trustalyzeKey);
            $settings = Arr::set($settings, $this->enabledKey, 'no');
            glsr(Notice::class)->addError(sprintf(
                __('Your Trustalyze account details could not be verified, please try again. %s', 'site-reviews'),
                '('.$trustalyze->message.')'
            ));
        }
        return $settings;
    }
}
