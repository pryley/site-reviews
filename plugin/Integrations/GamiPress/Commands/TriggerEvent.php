<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Integrations\GamiPress\Triggers;

class TriggerEvent extends AbstractCommand
{
    public array $event = [];

    public function __construct(array $event)
    {
        $this->event = $event;
    }

    public function handle(): void
    {
        foreach (glsr(Triggers::class)->triggers() as $trigger => $labels) {
            $this->triggerReceivedPost($trigger);
            $this->triggerReceivedUser($trigger);
            $this->triggerReviewed($trigger);
        }
    }

    protected function trigger(string $trigger, int $userId): void
    {
        $event = [
            'event' => $trigger,
            'user_id' => $userId,
        ];
        // Upstream declares gamipress_trigger_event() with no parameters and
        // reads func_get_args() instead, so the stubbed signature takes none.
        gamipress_trigger_event(wp_parse_args($event, $this->event)); // @phpstan-ignore arguments.count
    }

    protected function triggerReceivedPost(string $trigger): void
    {
        if (str_contains($trigger, '/received/post')) {
            foreach ($this->event['assigned_posts_authors'] as $userId) {
                $this->trigger($trigger, (int) $userId);
            }
        }
    }

    protected function triggerReceivedUser(string $trigger): void
    {
        if (str_contains($trigger, '/received/user')) {
            foreach ($this->event['assigned_users'] as $userId) {
                $this->trigger($trigger, (int) $userId);
            }
        }
    }

    protected function triggerReviewed(string $trigger): void
    {
        if (str_contains($trigger, '/reviewed')) {
            $this->trigger($trigger, get_current_user_id());
        }
    }
}
