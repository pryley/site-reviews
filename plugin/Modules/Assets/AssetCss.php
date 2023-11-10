<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

use GeminiLabs\SiteReviews\Modules\Style;

class AssetCss extends AbstractAsset
{
    protected function enqueue(string $url, string $hash): void
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
        wp_enqueue_style(glsr()->id, $url, $this->dependencies, $hash);
        if (!empty($this->after)) {
            $styles = array_reduce($this->after, fn ($carry, $string) => $carry.$string);
            wp_add_inline_style(glsr()->id, $styles);
        }
    }

    protected function originalUrl(): string
    {
        return glsr(Style::class)->stylesheetUrl();
    }

    protected function registered(): array
    {
        return wp_styles()->registered;
    }

    protected function type(): string
    {
        return 'css';
    }
}
