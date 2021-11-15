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
     * @return static
     */
    public function add($type, $message)
    {
        if (is_wp_error($message)) {
            $message = $message->get_error_message();
        }
        $this->notices[] = [
            'messages' => (array) $message,
            'type' => Str::restrictTo(['error', 'warning', 'info', 'success'], $type, 'info'),
        ];
        return $this;
    }

    /**
     * @param string|array|\WP_Error $message
     * @return static
     */
    public function addError($message)
    {
        return $this->add('error', $message);
    }

    /**
     * @param string|array|\WP_Error $message
     * @return static
     */
    public function addSuccess($message)
    {
        return $this->add('success', $message);
    }

    /**
     * @param string|array|\WP_Error $message
     * @return static
     */
    public function addWarning($message)
    {
        return $this->add('warning', $message);
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
            $text = array_reduce($args['messages'], function ($carry, $message) {
                return $carry.wpautop($message);
            });
            return $carry.glsr(Builder::class)->div([
                'class' => sprintf('notice notice-%s inline is-dismissible', $args['type']),
                'text' => $text,
            ]);
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
}
