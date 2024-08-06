<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Email;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;
use GeminiLabs\SiteReviews\Modules\Notice;
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
        if (!$this->review->isValid()) {
            glsr(Notice::class)->addError(
                _x('The email could not be sent because the review is invalid.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        if (!glsr(OptionManager::class)->getBool('settings.general.request_verification', false)) {
            glsr(Notice::class)->addError(
                _x('The email could not be sent because the Request Verification setting is disabled.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        $recipient = $this->review->email ?: Arr::get($this->review->user(), 'data.user_email');
        if (empty($recipient)) {
            glsr(Notice::class)->addError(
                _x('The email could not be sent because the review does not have a valid email.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        $email = glsr(Email::class)->compose($this->buildEmail($recipient), [
            'review' => $this->review,
        ]);
        if (!$email->send()) {
            glsr(Notice::class)->addError(
                sprintf(_x('The email could not be sent, check the <a href="%s">Site Reviews &rarr; Tools &rarr; Console</a> page for errors.', 'admin-text', 'site-reviews'),
                    glsr_admin_url('tools', 'console')
                )
            );
            $this->fail();
            return;
        }
        glsr(Database::class)->metaSet($this->review->ID, 'verified_requested', 1);
        glsr(Notice::class)->addSuccess(
            _x('The verification request email was sent.', 'admin-text', 'site-reviews')
        );
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
            'text' => esc_html_x('Resend Verification Request', 'admin-text', 'site-reviews'),
        ];
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
