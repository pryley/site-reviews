<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Contracts\WebhookContract;
use GeminiLabs\SiteReviews\Defaults\SlackDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class Slack implements WebhookContract
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
        $this->webhook = glsr_get_option('general.notification_slack');
    }

    /**
     * @return static
     */
    public function compose(Review $review, array $args)
    {
        if (empty($this->webhook)) {
            return $this;
        }
        $this->args = glsr(SlackDefaults::class)->restrict($args);
        $this->review = $review;
        $blocks = [
            $this->header(),
            $this->title(),
            $this->assignedLinks(),
            $this->content(),
            $this->fields(),
            $this->moderationLinks(),
        ];
        $blocks = array_values(array_filter($blocks));
        $notification = compact('blocks');
        $this->notification = glsr()->filterArray('slack/notification', $notification, $this);
        return $this;
    }

    public function send(): bool
    {
        if (empty($this->webhook)) {
            $result = new \WP_Error('slack', 'Slack notification was not sent: missing webhook');
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

    protected function assignedLinks(): array
    {
        if (empty($this->args['assigned_links'])) {
            return [];
        }
        return [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => sprintf(__('Review of %s', 'site-reviews'), $this->args['assigned_links']),
            ],
        ];
    }

    protected function content(): array
    {
        if (empty(trim($this->review->content))) {
            return [];
        }
        return [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => trim($this->review->content),
            ],
        ];
    }

    protected function fields(): array
    {
        $fields = [
            'name' => [
                'name' => 'Name',
                'value' => $this->review->name,
            ],
            'email' => [
                'name' => 'Email',
                'value' => $this->review->email,
            ],
            'ip_address' => [
                'name' => 'IP Address',
                'value' => $this->review->ip_address,
            ],
        ];
        $fields = glsr()->filterArray('slack/fields', $fields, $this->review);
        foreach ($fields as $key => $values) {
            $name = Arr::get($values, 'name');
            $value = Arr::get($values, 'value');
            if (empty($name) || empty($value)) {
                continue;
            }
            $fields[$key] = [
                'type' => 'mrkdwn',
                'text' => sprintf('*%s:* %s', $name, $value),
            ];
        }
        $fields = array_values($fields);
        if (empty($fields)) {
            return [];
        }
        return [
            'type' => 'section',
            'fields' => $fields,
        ];
    }

    protected function moderationLinks(): array
    {
        $elements = [];
        if (!$this->review->is_approved) {
            $elements[] = [
                'type' => 'button',
                'text' => [
                    'type' => 'plain_text',
                    'text' => __('Approve Review', 'site-reviews'),
                ],
                'url' => $this->review->approveUrl(),
            ];
        }
        $elements[] = [
            'type' => 'button',
            'text' => [
                'type' => 'plain_text',
                'text' => __('Edit Review', 'site-reviews'),
            ],
            'url' => $this->review->editUrl(),
        ];
        return [
            'type' => 'actions',
            'elements' => $elements,
        ];
    }

    protected function header(): array
    {
        return [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => $this->args['header'],
            ],
        ];
    }

    protected function rating(): string
    {
        $solidStars = str_repeat('★', $this->review->rating);
        $emptyStars = str_repeat('☆', max(0, glsr()->constant('MAX_RATING', Rating::class) - $this->review->rating));
        $stars = $solidStars.$emptyStars;
        return glsr()->filterString('slack/stars', $stars, $this->review->rating, glsr()->constant('MAX_RATING', Rating::class));
    }

    protected function title(): array
    {
        $title = trim($this->review->title);
        if (empty($title)) {
            $title = __('(no title)', 'site-reviews');
        }
        return [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => sprintf("*%s*\n%s", $title, $this->rating()),
            ],
        ];
    }
}
