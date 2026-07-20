<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Discord;
use GeminiLabs\SiteReviews\Modules\Email;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Slack;
use GeminiLabs\SiteReviews\Review;

class SendNotification extends AbstractCommand
{
    public Review $review;
    public array $types;

    public function __construct(Review $review)
    {
        $this->review = $review;
        $this->types = glsr_get_option('general.notifications', [], 'array');
    }

    public function handle(): void
    {
        if (!$this->review->isValid()) {
            $this->fail();
            return;
        }
        if (empty($this->types)) {
            $this->fail();
            return;
        }
        if (!empty(array_intersect(['admin', 'author', 'custom'], $this->types))) {
            $this->sendToEmail();
        }
        if (in_array('discord', $this->types)) {
            $this->sendToDiscord();
        }
        if (in_array('slack', $this->types)) {
            $this->sendToSlack();
        }
    }

    protected function buildEmail(): array
    {
        $includedTags = Arr::consolidate(glsr()->settings['settings.general.notification_message']['tags'] ?? []);
        $templateTags = glsr(TemplateTags::class)->tags($this->review, [
            'include' => array_keys($includedTags),
        ]);
        return [
            'message' => trim(glsr_get_option('general.notification_message', '', 'string')),
            'to' => $this->recipients(),
            'subject' => $this->subject(true),
            'template-tags' => $templateTags,
        ];
    }

    protected function recipients(): array
    {
        $emails = [];
        if (in_array('admin', $this->types)) {
            $emails[] = glsr(OptionManager::class)->wp('admin_email');
        }
        if (in_array('author', $this->types)) {
            $posts = $this->review->assignedPosts();
            $userIds = wp_list_pluck($posts, 'post_author');
            if (!empty($userIds)) {
                $users = get_users(['fields' => ['user_email'], 'include' => $userIds]);
                $userEmails = wp_list_pluck($users, 'user_email');
                $emails = array_merge($emails, $userEmails);
            }
        }
        if (in_array('custom', $this->types)) {
            $customEmails = glsr_get_option('general.notification_email', '', 'string');
            $customEmails = str_replace([' ', ',', ';'], ',', $customEmails);
            $customEmails = explode(',', $customEmails);
            $emails = array_merge($emails, $customEmails);
        }
        $emails = glsr()->filterArray('notification/emails', $emails, $this->review);
        $emails = array_map([glsr(Sanitizer::class), 'sanitizeEmail'], $emails);
        $emails = Arr::reindex(Arr::unique($emails));
        return $emails;
    }

    protected function sendToDiscord(): void
    {
        $notification = glsr(Discord::class)->compose($this->review, [
            'assigned_links' => glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review, '[%2$s](%1$s)'),
            'header' => $this->subject(),
        ]);
        $notification->send();
    }

    protected function sendToEmail(): void
    {
        $notification = glsr(Email::class)->compose($this->buildEmail(), [
            'review' => $this->review,
        ]);
        $notification->send();
    }

    protected function sendToSlack(): void
    {
        $notification = glsr(Slack::class)->compose($this->review, [
            'assigned_links' => glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review, '<%s|%s>'),
            'header' => $this->subject(),
        ]);
        $notification->send();
    }

    protected function subject(bool $withPostAssignment = false): string
    {
        $siteTitle = wp_specialchars_decode(glsr(OptionManager::class)->wp('blogname'), ENT_QUOTES);
        $title = sprintf(__('New %s-star review', 'site-reviews'), $this->review->rating);
        if ($withPostAssignment) {
            $postAssignments = glsr(TemplateTags::class)->tagReviewAssignedPosts($this->review);
            if (!empty($postAssignments)) {
                $title = sprintf(__('New %s-star review of %s', 'site-reviews'), $this->review->rating, $postAssignments);
            }
        }
        $title = sprintf('[%s] %s', $siteTitle, $title);
        return glsr()->filterString('notification/title', $title, $this->review);
    }
}
