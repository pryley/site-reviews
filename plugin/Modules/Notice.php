<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Notice
{
    /**
     * @var array
     */
    protected $notices;

    public function __construct()
    {
        $this->notices = [];
        $notices = get_transient(glsr()->prefix.'notices');
        if (is_array($notices)) {
            $this->notices = $notices;
            delete_transient(glsr()->prefix.'notices');
        }
    }

    /**
     * @param string $type
     * @param string|array|\WP_Error $message
     * @param string[] $details
     * @return static
     */
    public function add($type, $message, array $details = [])
    {
        if (is_wp_error($message)) {
            $message = $message->get_error_message();
        }
        $this->notices[] = [
            'details' => (array) $details,
            'messages' => (array) $message,
            'type' => Str::restrictTo(['error', 'warning', 'info', 'success'], $type, 'info'),
        ];
        return $this;
    }

    /**
     * @param string|array|\WP_Error $message
     * @param string[] $details
     * @return static
     */
    public function addError($message, array $details = [])
    {
        return $this->add('error', $message, $details);
    }

    /**
     * @param string|array|\WP_Error $message
     * @param string[] $details
     * @return static
     */
    public function addInfo($message, array $details = [])
    {
        return $this->add('info', $message, $details);
    }

    /**
     * @param string|array|\WP_Error $message
     * @param string[] $details
     * @return static
     */
    public function addSuccess($message, array $details = [])
    {
        return $this->add('success', $message, $details);
    }

    /**
     * @param string|array|\WP_Error $message
     * @param string[] $details
     * @return static
     */
    public function addWarning($message, array $details = [])
    {
        return $this->add('warning', $message, $details);
    }

    /**
     * @return static
     */
    public function clear()
    {
        $this->notices = [];
        return $this;
    }

    /**
     * @return string
     */
    public function get()
    {
        $this->sort();
        $notices = glsr()->filterArray('notices', $this->notices);
        return array_reduce($notices, function ($carry, $args) {
            return $carry.glsr(Builder::class)->div($this->normalizeArgs($args));
        });
    }

    /**
     * @return static
     */
    public function sort()
    {
        $notices = array_map('unserialize', array_unique(array_map('serialize', $this->notices)));
        usort($notices, function ($a, $b) {
            $order = ['error', 'warning', 'info', 'success'];
            return array_search($a['type'], $order) - array_search($b['type'], $order);
        });
        $this->notices = $notices;
        return $this;
    }

    /**
     * @return static
     */
    public function store()
    {
        if (!empty($this->notices)) {
            set_transient(glsr()->prefix.'notices', $this->notices, 30);
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function normalizeArgs(array $args)
    {
        $class = sprintf('notice notice-%s inline is-dismissible', $args['type']);
        if (!empty($args['details'])) {
            $class = 'bulk-action-notice '.$class;
            $lastIndex = count($args['messages']) - 1;
            $args['messages'][$lastIndex] .= sprintf(' <button class="button-link bulk-action-errors-collapsed" aria-expanded="false">%s <span class="toggle-indicator" aria-hidden="true"></span></button>',
                _x('Show more details', 'admin-text', 'site-reviews')
            );
            $li = array_reduce($args['details'], function ($carry, $text) {
                return sprintf('%s<li>%s</li>', $carry, $text);
            });
            $args['messages'][] = sprintf('<ul class="bulk-action-errors hidden">%s</ul>', $li);
        }
        $text = array_reduce($args['messages'], function ($carry, $message) {
            return $carry.wpautop($message);
        });
        return compact('class', 'text');
    }
}
