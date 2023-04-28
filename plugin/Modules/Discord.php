<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\WebhookContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\DiscordDefaults;
use GeminiLabs\SiteReviews\Review;

class Discord implements WebhookContract
{
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
        $this->review = $review;
        $args = glsr(DiscordDefaults::class)->restrict($args);
        $notification = [
            'content' => esc_html($args['content']),
            'embeds' => [[
                'color' => $args['color'],
                'description' => $this->description(), // rating and content
                'fields' => $this->fields(),
                'title' => $this->title(),
                'url' =>  esc_url($args['edit_url']),
            ]],
        ];
        $this->notification = glsr()->filterArray('discord/compose', $notification, $this);
        return $this;
    }

    /**
     * @return \WP_Error|array
     */
    public function send()
    {
        if (empty($this->webhook)) {
            return new \WP_Error('discord', 'Discord notification was not sent: missing webhook');
        }
        return wp_remote_post($this->webhook, [
            'blocking' => false,
            'body' => wp_json_encode($this->notification),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function description(): string
    {
        return $this->rating().PHP_EOL.PHP_EOL.$this->review->content;
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
            ? '(no title)'
            : $title;
    }
}
