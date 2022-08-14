<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Modules\Style;

class AssetCss extends AssetAbstract
{
    /**
     * @param string $url
     * @param string $hash
     */
    protected function enqueue($url, $hash)
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
        wp_enqueue_style(glsr()->id, $url, $this->dependencies, $hash);
        if (!empty($this->after)) {
            $styles = array_reduce($this->after, function ($carry, $string) {
                return $carry.$string;
            });
            wp_add_inline_style(glsr()->id, $styles);
        }
    }

    /**
     * @return string
     */
    protected function originalUrl()
    {
        return glsr(Style::class)->stylesheetUrl();
    }

    /**
     * @return array
     */
    protected function registered()
    {
        return wp_styles()->registered;
    }

    /**
     * @return string
     */
    protected function type()
    {
        return 'css';
    }
}
