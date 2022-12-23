<?php

namespace GeminiLabs\SiteReviews\Integrations\WooRewards;

use GeminiLabs\SiteReviews\Contracts\HooksContract;

class Hooks implements HooksContract
{
    /**
     * @var Controller
     */
    public $controller;

    public function __construct()
    {
        glsr()->singleton(Controller::class);
        $this->controller = glsr(Controller::class);
    }

    public function run(): void
    {
        if (!class_exists('\LWS_WooRewards') || !class_exists('\LWS\WOOREWARDS\Core\Trace')) {
            return;
        }
        add_action('lws_woorewards_abstracts_event_installed', function () {
            $this->removeAction('comment_post', 'trigger');
            $this->removeAction('comment_unapproved_to_approved', 'delayedApproval');
            $this->removeAction('wp_insert_comment', 'review');
            if ($this->controller->event) {
                add_action('site-reviews/review/approved', [$this->controller, 'onApprovedReview'], 20);
                add_action('site-reviews/review/created', [$this->controller, 'onCreatedReview'], 20);
            }
        });
    }

    protected function removeAction(string $hook, string $fn): bool
    {
        global $wp_filter;
        if (!isset($wp_filter[$hook])) {
            return false;
        }
        $callbacks = $wp_filter[$hook]->callbacks;
        if (!isset($callbacks[10])) {
            return false;
        }
        foreach ($callbacks[10] as $callback) {
            if (!isset($callback['function'][0]) || !isset($callback['function'][1])) {
                continue;
            }
            if (!is_a($callback['function'][0], '\LWS\WOOREWARDS\Events\ProductReview')) {
                continue;
            }
            if ($fn !== $callback['function'][1]) {
                continue;
            }
            $this->controller->event = $callback['function'][0];
            remove_action($hook, [$this->controller->event, $fn]);
            return true;
        }
        return false;
    }
}
