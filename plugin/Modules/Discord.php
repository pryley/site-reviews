<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\WebhookContract;
use GeminiLabs\SiteReviews\Defaults\DiscordDefaults;
use GeminiLabs\SiteReviews\Review;

class Discord implements WebhookContract
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    public $notification;

    /**
     * @var Review
     */
    public $review;

    /**
     * @var string
     */
    public $webhook;

    public function __construct()
    {
        $this->webhook = glsr_get_option('general.notification_discord');
    }

    /**
     * @return WebhookContract
     */
    public function compose(Review $review, array $args)
    {
        if (empty($this->webhook)) {
            return $this;
        }
        $this->args = glsr(DiscordDefaults::class)->restrict($args);
        $this->review = $review;
        $notification = [
            'content' => $this->args['header'],
            'embeds' => [[
                'color' => $this->args['color'],
                'description' => $this->description(), // rating and content
                'fields' => $this->fields(),
                'title' => $this->title(),
                'url' => esc_url($this->args['edit_url']),
            ]],
        ];
        $this->notification = glsr()->filterArray('discord/notification', $notification, $this);
        return $this;
    }

    public function send(): bool
    {
        if (empty($this->webhook)) {
            $result = new \WP_Error('discord', 'Discord notification was not sent: missing webhook');
        } else {
            $result = wp_remote_post($this->webhook, [
                'blocking' => false,
                'body' => wp_json_encode($this->notification),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
        }
        if (is_wp_error($result)) {
            glsr_log()->error($result->get_error_message())->debug($this->notification);
            return false;
        }
        return true;
    }

    protected function assignedLinks(): string
    {
        if (empty($this->args['assigned_links'])) {
            return '';
        }
        return sprintf(__('Review of %s', 'site-reviews'), $this->args['assigned_links']);
    }

    protected function description(): string
    {
        $parts = [
            $this->rating(),
            $this->assignedLinks(),
            $this->review->content,
        ];
        $parts = array_filter($parts);
        return implode(PHP_EOL.PHP_EOL, $parts);
    }

    protected function fields(): array
    {
        $fields = [
            'name' => [
                'name' => 'Name',
                'value' => $this->review->name,
                'inline' => true,
            ],
            'email' => [
                'name' => 'Email',
                'value' => $this->review->email,
                'inline' => true,
            ],
            'ip_address' => [
                'name' => 'IP Address',
                'value' => $this->review->ip_address,
                'inline' => true,
            ],
        ];
        $fields = glsr()->filterArray('discord/fields', $fields, $this->review);
        return array_values($fields);
    }

    protected function rating(): string
    {
        $solidStars = str_repeat('★', $this->review->rating);
        $emptyStars = str_repeat('☆', max(0, glsr()->constant('MAX_RATING', Rating::class) - $this->review->rating));
        $stars = $solidStars.$emptyStars;
        return glsr()->filterString('discord/stars', $stars, $this->review->rating, glsr()->constant('MAX_RATING', Rating::class));
    }

    protected function title(): string
    {
        $title = trim($this->review->title);
        return empty($title)
            ? __('(no title)', 'site-reviews')
            : $title;
    }
}
