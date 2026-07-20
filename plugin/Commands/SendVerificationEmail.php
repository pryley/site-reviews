<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Email;
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
        if (!glsr_get_option('settings.general.request_verification', false, 'bool')) {
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
                sprintf(_x('The email could not be sent, check the %s page for errors.', 'link to Console page (admin-text)', 'site-reviews'),
                    glsr_admin_link('tools.console')
                )
            );
            $this->fail();
            return;
        }
        glsr(PostMeta::class)->set($this->review->ID, 'verified_requested', 1);
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
        $includedTags = Arr::consolidate(glsr()->settings['settings.general.request_verification_message']['tags'] ?? []);
        $templateTags = glsr(TemplateTags::class)->tags($this->review, [
            'include' => array_keys($includedTags),
        ]);
        $templateTags['verify_url'] = $this->verifyUrl; // use the provided verify_url with the redirect path
        return [
            'message' => trim(glsr_get_option('general.request_verification_message', '', 'string')),
            'to' => $recipient,
            'subject' => __('Please verify your review', 'site-reviews'),
            'template-tags' => $templateTags,
        ];
    }
}
