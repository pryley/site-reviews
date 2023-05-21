<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\TemplateTags;
use GeminiLabs\SiteReviews\Review;

class Notification
{
    /**
     * @var Review
     */
    protected $review;

    /**
     * @var array
     */
    protected $types;

    public function __construct()
    {
        $this->types = glsr_get_option('general.notifications', [], 'array');
    }

    public function send(Review $review): void
    {
        $this->review = $review;
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
        $assignedTerms = glsr(TemplateTags::class)->tagReviewAssignedTerms($this->review);
        return [
            'to' => $this->emailAddresses(),
            'subject' => $this->notificationTitle(true),
            'template' => 'default',
            'template-tags' => [
                'review_assigned_links' => glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review),
                'review_assigned_posts' => glsr(TemplateTags::class)->tagReviewAssignedPosts($this->review),
                'review_assigned_terms' => $assignedTerms,
                'review_assigned_users' => glsr(TemplateTags::class)->tagReviewAssignedUsers($this->review),
                'review_author' => $this->review->author ?: __('Anonymous', 'site-reviews'),
                'review_categories' => $assignedTerms,
                'review_content' => $this->review->content,
                'review_email' => $this->review->email,
                'review_ip' => $this->review->ip_address,
                'review_link' => sprintf('<a href="%1$s">%1$s</a>', $this->permalink()),
                'review_rating' => $this->review->rating,
                'review_title' => $this->review->title,
                'site_title' => get_bloginfo('name'),
                'site_url' => get_bloginfo('url'),
            ],
        ];
    }

    protected function emailAddresses(): array
    {
        $emails = [];
        if (in_array('admin', $this->types)) {
            $emails[] = glsr(OptionManager::class)->getWP('admin_email');
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

    protected function notificationTitle(bool $withPostAssignment = false): string
    {
        $siteTitle = wp_specialchars_decode(glsr(OptionManager::class)->getWP('blogname'), ENT_QUOTES);
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

    protected function permalink(): string
    {
        return admin_url('post.php?post='.$this->review->ID.'&action=edit');
    }

    protected function sendToDiscord(): void
    {
        $args = [
            'assigned_links' => glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review, '[%2$s](%1$s)'),
            'edit_url' => $this->permalink(),
            'header' => $this->notificationTitle(),
        ];
        glsr(Discord::class)->compose($this->review, $args)->send();
    }

    protected function sendToEmail(): void
    {
        $args = [
            'review' => $this->review,
        ];
        glsr(Email::class)->compose($this->buildEmail(), $args)->send();
    }

    protected function sendToSlack(): void
    {
        $args = [
            'assigned_links' => glsr(TemplateTags::class)->tagReviewAssignedLinks($this->review, '<%s|%s>'),
            'edit_url' => $this->permalink(),
            'header' => $this->notificationTitle(),
        ];
        glsr(Slack::class)->compose($this->review, $args)->send();
    }
}
