<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Email;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;
use GeminiLabs\SiteReviews\Review;

class SendVerificationEmail extends AbstractCommand
{
    /** @var Review */
    public $review;
    public $verifyUrl;

    public function __construct(Review $review, string $verifyUrl)
    {
        $this->review = $review;
        $this->verifyUrl = $verifyUrl;
    }

    public function handle(): void
    {
        if (!glsr(OptionManager::class)->getBool('settings.general.request_verification', false)) {
            $this->fail();
            return;
        }
        $recipient = $this->review->email ?: Arr::get($this->review->user(), 'data.user_email');
        if (empty($recipient)) {
            $this->fail();
            return;
        }
        $email = glsr(Email::class)->compose($this->buildEmail($recipient), [
            'review' => $this->review,
        ]);
        if (!$email->send()) {
            $this->fail();
        }
    }

    protected function buildEmail(string $recipient): array
    {
        $context = glsr(TemplateTags::class)->tags($this->review, [
            'include' => [
                'review_assigned_links',
                'review_assigned_posts',
                'review_assigned_terms',
                'review_assigned_users',
                'review_author',
                'review_categories',
                'review_content',
                'review_email',
                'review_rating',
                'review_title',
                'site_title',
                'site_url',
            ],
        ]);
        $context['verify_url'] = $this->verifyUrl; // use the provided verify_url with the redirect path
        $template = trim(glsr(OptionManager::class)->get('settings.general.request_verification_message'));
        if (!empty($template)) {
            $templatePathForHook = 'request_verification_message';
            $message = glsr(Template::class)->interpolate($template, $templatePathForHook, compact('context'));
        } else {
            glsr_log()->error('The request_verification_message setting is missing.');
        }
        return [
            'to' => $recipient,
            'subject' => __('Please verify your review', 'site-reviews'),
            'message' => $message ?? '',
        ];
    }
}
