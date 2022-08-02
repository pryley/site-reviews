<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

class AssetJs extends AssetAbstract
{
    /**
     * @param string $url
     * @param string $hash
     */
    protected function enqueue($url, $hash)
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }
        wp_enqueue_script(glsr()->id, $url, $this->dependencies, $hash, true);
        if (!empty($this->after)) {
            $script = array_reduce($this->after, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_script(glsr()->id, $script);
        }
        if (!empty($this->before)) {
            $script = array_reduce($this->before, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_script(glsr()->id, $script, 'before');
        }
    }

    /**
     * @return string
     */
    protected function originalUrl()
    {
        return glsr()->url(sprintf('assets/scripts/%s.js', glsr()->id));
    }

    /**
     * @return array
     */
    protected function registered()
    {
        return wp_scripts()->registered;
    }

    /**
     * @return string
     */
    protected function type()
    {
        return 'js';
    }
}
