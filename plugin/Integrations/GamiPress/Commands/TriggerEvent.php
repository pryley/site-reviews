<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\GamiPress\Triggers;

class TriggerEvent implements Contract
{
    /**
     * @var array
     */
    public $event;

    public function __construct(array $event)
    {
        $this->event = $event;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach (glsr(Triggers::class)->triggers() as $trigger => $labels) {
            $this->triggerReceivedPost($trigger);
            $this->triggerReceivedUser($trigger);
            $this->triggerReviewed($trigger);
        }
    }

    /**
     * @param string $trigger
     * @param int $userId
     * @return void
     */
    protected function trigger($trigger, $userId)
    {
        $event = [
            'event' => $trigger,
            'user_id' => $userId,
        ];
        gamipress_trigger_event(wp_parse_args($event, $this->event));
    }

    /**
     * @param string $trigger
     * @return void
     */
    protected function triggerReceivedPost($trigger)
    {
        if (Str::contains($trigger, '/received/post')) {
            foreach ($this->event['assigned_posts_authors'] as $userId) {
                $this->trigger($trigger, (int) $userId);
            }
        }
    }

    /**
     * @param string $trigger
     * @return void
     */
    protected function triggerReceivedUser($trigger)
    {
        if (Str::contains($trigger, '/received/user')) {
            foreach ($this->event['assigned_users'] as $userId) {
                $this->trigger($trigger, (int) $userId);
            }
        }
    }

    /**
     * @param string $trigger
     * @return void
     */
    protected function triggerReviewed($trigger)
    {
        if (Str::contains($trigger, '/reviewed')) {
            $this->trigger($trigger, get_current_user_id());
        }
    }
}
