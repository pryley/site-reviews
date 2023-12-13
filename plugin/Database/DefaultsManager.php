<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;

class DefaultsManager
{
    public function defaults(): array
    {
        $settings = glsr()->settings();
        $defaults = array_combine(array_keys($settings), wp_list_pluck($settings, 'default'));
        return wp_parse_args($defaults, [
            'version' => '',
            'version_upgraded_from' => '0.0.0',
        ]);
    }

    public function get(): array
    {
        return Arr::unflatten($this->defaults());
    }

    /**
     * @return mixed
     */
    public function pluck(string $path)
    {
        $settings = Arr::unflatten(glsr()->settings());
        return Arr::get($settings, $path);
    }
}
