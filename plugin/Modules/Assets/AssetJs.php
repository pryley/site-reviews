<?php

namespace GeminiLabs\SiteReviews\Modules\Assets;

class AssetJs extends AbstractAsset
{
    protected function enqueue(string $url, string $hash): void
    {
        foreach ($this->handles as $handle) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        }
        wp_enqueue_script(glsr()->id, $url, $this->dependencies, $hash, [
            'in_footer' => true,
            'strategy' => 'defer',
        ]);
        if (!empty($this->after)) {
            $script = array_reduce($this->after, fn ($carry, $string) => $carry.$string);
            wp_add_inline_script(glsr()->id, $script);
        }
        if (!empty($this->before)) {
            $script = array_reduce($this->before, fn ($carry, $string) => $carry.$string);
            wp_add_inline_script(glsr()->id, $script, 'before');
        }
    }

    protected function originalUrl(): string
    {
        return glsr()->url(sprintf('assets/scripts/%s.js', glsr()->id));
    }

    protected function registered(): array
    {
        return wp_scripts()->registered;
    }

    protected function type(): string
    {
        return 'js';
    }
}
